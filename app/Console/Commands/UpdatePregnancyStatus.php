<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PregnancyRecord;
use Carbon\Carbon;

class UpdatePregnancyStatus extends Command
{
    protected $signature = 'ternak:update-pregnancy-status';

    protected $description =
        'Memperbarui otomatis fase kebuntingan sapi berdasarkan milestone dan batas konfirmasi 3 hari';

    public function handle(): int
    {
        $today = Carbon::today();

        $records = PregnancyRecord::with('cow')
            ->whereNotIn('status', [
                'Melahirkan (Selesai)',
                'Gagal/Keguguran'
            ])
            ->get();

        $updatedCount = 0;

        foreach ($records as $record) {

            if ($record->checkAndAutoAdvance()) {
                $updatedCount++;
            }
        }

        $this->info(
            "Berhasil memperbarui {$updatedCount} data pada tanggal " .
            $today->format('d-m-Y')
        );

        return Command::SUCCESS;
    }
}