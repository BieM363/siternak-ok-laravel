<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\PregnancyRecord;
use Carbon\Carbon;

/**
 * Artisan Cronjob Command: ternak:operator-session-cron
 * 1. Membatasi waktu login Operator Lapangan maksimal 8 jam (menghapus sesi database > 8 jam).
 * 2. Memperbarui status milestone kebuntingan & menyiapkan antrean notifikasi peramban ponsel
 *    bagi operator yang sedang bertugas di lapangan.
 */
class OperatorSessionCron extends Command
{
    protected $signature = 'ternak:operator-session-cron';

    protected $description = 'Cronjob pembatasan waktu login operator 8 jam & sinkronisasi notifikasi peramban ponsel lapangan';

    public function handle(): int
    {
        $now = Carbon::now();
        $eightHoursAgoTimestamp = $now->copy()->subHours(User::OPERATOR_SESSION_HOURS)->timestamp;

        $expiredUsersCount = 0;
        $deletedSessionsCount = 0;

        // 1. Cari operator yang batas waktu login 8 jam-nya sudah habis
        $expiredOperators = User::where('role', 'operator')
            ->whereNotNull('login_expires_at')
            ->where('login_expires_at', '<=', $now)
            ->get();

        foreach ($expiredOperators as $operator) {
            if (Schema::hasTable('sessions')) {
                $deleted = DB::table('sessions')
                    ->where('user_id', $operator->id)
                    ->delete();
                $deletedSessionsCount += $deleted;
            }

            $operator->update([
                'login_expires_at' => null,
                'remember_token' => null,
            ]);

            $expiredUsersCount++;
        }

        // 2. Bersihkan juga sesi milik role operator di tabel sessions yang tidak aktif > 8 jam
        if (Schema::hasTable('sessions')) {
            $operatorIds = User::where('role', 'operator')->pluck('id');

            if ($operatorIds->isNotEmpty()) {
                $staleDeleted = DB::table('sessions')
                    ->whereIn('user_id', $operatorIds)
                    ->where('last_activity', '<=', $eightHoursAgoTimestamp)
                    ->delete();
                $deletedSessionsCount += $staleDeleted;
            }
        }

        // 3. Jalankan pengecekan milestone kebuntingan & sinkronisasi reminder notifikasi peramban HP
        $today = Carbon::today();
        $activeRecords = PregnancyRecord::with('cow')
            ->whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran'])
            ->get();

        $autoAdvancedCount = 0;
        $reminderReadyCount = 0;

        foreach ($activeRecords as $record) {
            if ($record->checkAndAutoAdvance()) {
                $autoAdvancedCount++;
            }

            // Cek apakah sapi ini berada dalam jendela reminder konfirmasi untuk notifikasi HP operator
            $isDueReminder = false;

            if (
                in_array($record->status, ['Pasca IB', 'Estimasi 30 Hari']) &&
                $record->day30_date &&
                $record->day30_date->lte($today) &&
                $record->day30_date->gte($today->copy()->subDays(3))
            ) {
                $isDueReminder = true;
            } elseif (
                $record->status === 'Bunting (PKB 90 Hari)' &&
                $record->day90_date &&
                $record->day90_date->lte($today) &&
                $record->day90_date->gte($today->copy()->subDays(3))
            ) {
                $isDueReminder = true;
            } elseif (
                $record->status === 'Siap Melahirkan (245 Hari)' &&
                $record->day245_date &&
                $record->day245_date->lte($today) &&
                $record->day245_date->gte($today->copy()->subDays(3))
            ) {
                $isDueReminder = true;
            } elseif (
                $record->status === 'Fase Melahirkan' &&
                $record->estimated_birth_date &&
                $record->estimated_birth_date->lte($today) &&
                (!$record->birth_window_end_date || $record->birth_window_end_date->gte($today))
            ) {
                $isDueReminder = true;
            }

            if ($isDueReminder) {
                $reminderReadyCount++;
                $record->update(['last_reminder_at' => $now]);
            }
        }

        $this->info(
            "[Cron 8 Jam Operator] Sesi berakhir: {$expiredUsersCount} operator ({$deletedSessionsCount} sesi dihapus) | " .
            "Auto-Advance: {$autoAdvancedCount} sapi | Siap Notifikasi Peramban HP: {$reminderReadyCount} sapi."
        );

        return Command::SUCCESS;
    }
}

