<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PregnancyRecord;

/**
 * Artisan Command: pregnancy:auto-advance
 * Menjalankan pengecekan harian untuk memajukan fase kebuntingan sapi secara otomatis
 * apabila suatu tahapan melewati 3 hari tanpa konfirmasi manual dari Operator/Admin.
 */
class AutoAdvancePregnancyPhases extends Command
{
    protected $signature = 'pregnancy:auto-advance';
    protected $description = 'Secara otomatis memajukan fase kebuntingan sapi jika melewati 3 hari tanpa konfirmasi';

    public function handle()
    {
        $this->info('Memulai pengecekan otomatis fase kebuntingan sapi (Reminder 3 Hari Max)...');

        $activeRecords = PregnancyRecord::whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran'])->get();
        $advancedCount = 0;

        foreach ($activeRecords as $record) {
            if ($record->checkAndAutoAdvance()) {
                $advancedCount++;
                $this->line(" - Sapi ID {$record->cow_id} (NIK: {$record->cow->cow_nik}): Status dimajukan ke '{$record->status}'");
            }
        }

        $this->info("Pengecekan selesai. Total {$advancedCount} riwayat kebuntingan dimajukan otomatis.");
        return 0;
    }
}
