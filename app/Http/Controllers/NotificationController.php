<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\Regency;
use App\Models\User;
use Carbon\Carbon;

/**
 * NotificationController
 * Menyediakan:
 * 1. Halaman khusus Menu "Konfirmasi 3 Tahap Kebuntingan" (/confirmations) — terpisah dari Dashboard agar rapi.
 * 2. Endpoint JSON polling notifikasi peramban ponsel & desktop (/notifications/poll).
 */
class NotificationController extends Controller
{
    /**
     * Halaman Khusus: Notifikasi & Konfirmasi 3 Tahap Kebuntingan (Auto-Lanjut 3 Hari)
     * Dapat diakses oleh Admin Provinsi, Admin Kabupaten/Kota (otomatis terfilter per kabupaten),
     * dan Inseminator (sapi tanggung jawabnya).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        // Jalankan auto-advance 3 hari sesuai scope user
        $autoQuery = PregnancyRecord::whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran']);
        if ($isPureOperator) {
            $autoQuery->where('operator_id', $user->id);
        } elseif ($isAdminKabupaten) {
            $autoQuery->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        }
        foreach ($autoQuery->get() as $rec) {
            $rec->checkAndAutoAdvance();
        }

        $search      = $request->query('search');
        $stage       = $request->query('stage'); // '1', '2', '3'
        $regencyId   = $request->query('regency_id');
        $officerName = $request->query('officer_name');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        $baseQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
        ])
            ->whereHas('cow')
            ->dueForReminder();

        if ($isPureOperator) {
            $baseQuery->where('operator_id', $user->id);
        } elseif ($isAdminKabupaten) {
            $baseQuery->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $baseQuery->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }

        if ($officerName && !$isPureOperator) {
            $baseQuery->where('officer_name', $officerName);
        }

        if ($search) {
            $baseQuery->whereHas('cow', function ($q) use ($search) {
                $q->where('cow_nik', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('farmer', function ($fq) use ($search) {
                        $fq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Hitung jumlah per tahap (sebelum filter tab stage)
        $allDue = (clone $baseQuery)->get();
        $totalAll = $allDue->count();
        $countStage1 = $allDue->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari'])->count();
        $countStage2 = $allDue->where('status', 'Bunting (PKB 90 Hari)')->count();
        $countStage3 = $allDue->where('status', 'Fase Melahirkan')->count();

        // Filter tab stage
        $listQuery = clone $baseQuery;
        if ($stage === '1') {
            $listQuery->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari']);
        } elseif ($stage === '2') {
            $listQuery->where('status', 'Bunting (PKB 90 Hari)');
        } elseif ($stage === '3') {
            $listQuery->where('status', 'Fase Melahirkan');
        }

        $confirmationReminders = $listQuery
            ->orderBy('estimated_birth_date')
            ->paginate(12)
            ->withQueryString();

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::orderBy('name')->get();

        $officerQuery = DB::table('pregnancy_records')
            ->whereNotNull('officer_name')
            ->where('officer_name', '!=', '');
        if ($isAdminKabupaten) {
            $officerQuery->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('cows')
                    ->whereColumn('cows.id', 'pregnancy_records.cow_id')
                    ->where('cows.regency_id', $user->regency_id);
            });
        }
        $officers = $officerQuery->distinct()->orderBy('officer_name')->pluck('officer_name');
        $breeds = Cow::BREEDS;

        return view('confirmations.index', compact(
            'confirmationReminders',
            'totalAll',
            'countStage1',
            'countStage2',
            'countStage3',
            'regencies',
            'officers',
            'breeds',
            'search',
            'stage',
            'regencyId',
            'officerName'
        ));
    }

    public function poll(Request $request)
    {
        if (!Auth::check() || Auth::user()->isGuest()) {
            return response()->json([
                'authenticated' => false,
                'expired' => true,
            ]);
        }

        $user = Auth::user();
        $isPureOperator = $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user->isAdminKabupaten();

        $now = now();
        $loginAtTimestamp = session('operator_login_at')
            ?? ($user->last_login_at ? $user->last_login_at->timestamp : $now->timestamp);
        $expiresAtTimestamp = session('operator_expires_at')
            ?? ($user->login_expires_at ? $user->login_expires_at->timestamp : ($loginAtTimestamp + (User::OPERATOR_SESSION_HOURS * 3600)));

        $remainingSeconds = $user->isOperator()
            ? max(0, min(User::OPERATOR_SESSION_HOURS * 3600, (int) ($expiresAtTimestamp - $now->timestamp)))
            : null;

        // Ambil daftar sapi yang masuk jendela notifikasi 3 tahap (Hari 30, Hari 90, Bulan ke-9)
        $reminderQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
        ])
            ->whereHas('cow')
            ->dueForReminder();

        if ($isPureOperator) {
            $reminderQuery->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $reminderQuery->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        $records = $reminderQuery->orderBy('estimated_birth_date')->get();

        $reminders = $records->map(function ($rec) {
            $cow = $rec->cow;
            $stageInfo = $rec->reminder_stage_info;
            $villageName = $cow->village->name ?? '-';
            $districtName = $cow->district->name ?? '-';

            return [
                'id' => $rec->id,
                'cow_id' => $cow->id,
                'cow_nik' => $cow->cow_nik,
                'cow_name' => $cow->name ?? ('Sapi ' . $cow->cow_nik),
                'ib_ke' => $rec->ib_ke ?? 1,
                'breed' => $cow->breed ?? '-',
                'farmer_name' => $cow->farmer->name ?? '-',
                'location' => "{$villageName}, Kec. {$districtName}",
                'status' => $rec->status,
                'stage' => $stageInfo['stage'],
                'milestone_label' => $stageInfo['label'],
                'target_date' => $stageInfo['target_date'] ? Carbon::parse($stageInfo['target_date'])->format('d M Y') : '-',
                'hpl_date' => $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d M Y') : '-',
                'action_url' => route('confirmations.index'),
                'detail_url' => route('cows.show', $cow->id),
            ];
        })->values();

        $justLoggedIn = (bool) session()->pull('just_logged_in_notify', false);

        return response()->json([
            'authenticated' => true,
            'just_logged_in' => $justLoggedIn,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'role_badge' => $user->getRoleBadgeText(),
            ],
            'session' => [
                'is_operator' => $user->isOperator(),
                'max_hours' => User::OPERATOR_SESSION_HOURS,
                'login_at_timestamp' => $loginAtTimestamp,
                'expires_at_timestamp' => $expiresAtTimestamp,
                'login_at_formatted' => Carbon::createFromTimestamp($loginAtTimestamp)->format('H:i'),
                'expires_at_formatted' => Carbon::createFromTimestamp($expiresAtTimestamp)->format('H:i'),
                'remaining_seconds' => $remainingSeconds,
            ],
            'reminders_count' => $reminders->count(),
            'reminders' => $reminders,
        ]);
    }

    public function syncCron(Request $request)
    {
        if (!Auth::check() || !Auth::user()->canOperate()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Artisan::call('ternak:operator-session-cron');
        $cronOutput = trim(Artisan::output());

        $pollResponse = $this->poll($request)->getData(true);
        $pollResponse['cron_message'] = $cronOutput;

        return response()->json($pollResponse);
    }
}
