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

        // 3. Jalankan pengecekan milestone kebuntingan 3 tahap (Hari 30, Hari 90, Bulan ke-9) & auto-lanjut 3 hari
        $today = Carbon::today();
        $activeRecords = PregnancyRecord::with('cow')
            ->whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran'])
            ->get();

        $autoAdvancedCount = 0;

        foreach ($activeRecords as $record) {
            if ($record->checkAndAutoAdvance()) {
                $autoAdvancedCount++;
            }
        }

        $dueRecords = PregnancyRecord::dueForReminder($today)->get();
        $reminderReadyCount = $dueRecords->count();
        foreach ($dueRecords as $rec) {
            $rec->update(['last_reminder_at' => $now]);
        }

        $this->info(
            "[Cron Sesi Inseminator & 3 Tahap Kebuntingan] Sesi berakhir: {$expiredUsersCount} inseminator ({$deletedSessionsCount} sesi dihapus) | " .
            "Auto-Lanjut 3 Hari: {$autoAdvancedCount} sapi | Notifikasi 3 Tahap Siap: {$reminderReadyCount} sapi."
        );

        return Command::SUCCESS;
    }
}

