<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\PregnancyRecord;
use App\Models\User;
use Carbon\Carbon;

/**
 * NotificationController
 * Menyediakan endpoint JSON real-time untuk:
 * 1. Pemantauan sisa waktu login Operator (batas maksimal 8 jam).
 * 2. Pengiriman notifikasi pengingat konfirmasi sapi ke semua peramban (terutama peramban ponsel/HP saat turun lapangan).
 */
class NotificationController extends Controller
{
    /**
     * Endpoint polling status sesi 8 jam & daftar pengingat kebuntingan untuk peramban ponsel/desktop
     */
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

        // Hitung informasi sesi 8 jam khusus Operator
        $now = now();
        $loginAtTimestamp = session('operator_login_at')
            ?? ($user->last_login_at ? $user->last_login_at->timestamp : $now->timestamp);
        $expiresAtTimestamp = session('operator_expires_at')
            ?? ($user->login_expires_at ? $user->login_expires_at->timestamp : ($loginAtTimestamp + (User::OPERATOR_SESSION_HOURS * 3600)));

        $remainingSeconds = $user->isOperator()
            ? max(0, (int) ($expiresAtTimestamp - $now->timestamp))
            : null;

        // Ambil daftar sapi yang masuk masa konfirmasi (batas 3 hari)
        $today = Carbon::today();

        $reminderQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
        ])
            ->whereHas('cow')
            ->whereNotIn('status', [
                'Melahirkan (Selesai)',
                'Gagal/Keguguran',
            ])
            ->where(function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari'])
                        ->whereDate('day30_date', '<=', $today)
                        ->whereDate('day30_date', '>=', $today->copy()->subDays(3));
                })
                ->orWhere(function ($sub) use ($today) {
                    $sub->where('status', 'Bunting (PKB 90 Hari)')
                        ->whereDate('day90_date', '<=', $today)
                        ->whereDate('day90_date', '>=', $today->copy()->subDays(3));
                })
                ->orWhere(function ($sub) use ($today) {
                    $sub->where('status', 'Siap Melahirkan (245 Hari)')
                        ->whereDate('day245_date', '<=', $today)
                        ->whereDate('day245_date', '>=', $today->copy()->subDays(3));
                })
                ->orWhere(function ($sub) use ($today) {
                    $sub->where('status', 'Fase Melahirkan')
                        ->whereDate('estimated_birth_date', '<=', $today)
                        ->whereDate('birth_window_end_date', '>=', $today);
                });
            });

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
            $milestoneLabel = 'Konfirmasi Kebuntingan';
            $targetDate = null;

            if (in_array($rec->status, ['Pasca IB', 'Estimasi 30 Hari'])) {
                $milestoneLabel = 'PKB 30 Hari Pasca IB';
                $targetDate = $rec->day30_date;
            } elseif ($rec->status === 'Bunting (PKB 90 Hari)') {
                $milestoneLabel = 'PKB 90 Hari';
                $targetDate = $rec->day90_date;
            } elseif ($rec->status === 'Siap Melahirkan (245 Hari)') {
                $milestoneLabel = 'Siap Melahirkan (245 Hari)';
                $targetDate = $rec->day245_date;
            } elseif ($rec->status === 'Fase Melahirkan') {
                $milestoneLabel = 'Fase Melahirkan (Konfirmasi Pedet)';
                $targetDate = $rec->estimated_birth_date;
            }

            $villageName = $cow->village->name ?? '-';
            $districtName = $cow->district->name ?? '-';

            return [
                'id' => $rec->id,
                'cow_id' => $cow->id,
                'cow_nik' => $cow->cow_nik,
                'cow_name' => $cow->name ?? ('Sapi ' . $cow->cow_nik),
                'breed' => $cow->breed ?? '-',
                'farmer_name' => $cow->farmer->name ?? '-',
                'location' => "{$villageName}, Kec. {$districtName}",
                'status' => $rec->status,
                'milestone_label' => $milestoneLabel,
                'target_date' => $targetDate ? Carbon::parse($targetDate)->format('d M Y') : '-',
                'hpl_date' => $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d M Y') : '-',
                'action_url' => route('dashboard') . '#reminder-section',
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

    /**
     * Jalankan cronjob sesi 8 jam & pengingat via AJAX dari peramban ponsel/desktop
     */
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

