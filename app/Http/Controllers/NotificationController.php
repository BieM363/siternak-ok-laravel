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
 * 1. Notifikasi 3 Tahap Kebuntingan (Hari ke-30, Hari ke-90/PKB, Bulan ke-9) ke Admin Kab/Kota & petugas.
 * 2. Pemantauan sisa waktu login Inseminator (batas maksimal 8 jam).
 */
class NotificationController extends Controller
{
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
