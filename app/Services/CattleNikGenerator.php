<?php

namespace App\Services;

use App\Models\Cow;
use App\Models\Village;
use Carbon\Carbon;

/**
 * Service Generator NIK Sapi
 * Formulasi: [Kode_Kab/Kec].[Kode_Desa].[Dua_Digit_Tahun].[Nomor_Urut_4_Digit]
 * Contoh: 75.02.01.2001.26.0001
 */
class CattleNikGenerator
{
    public static function generate(int $villageId, ?int $year = null): string
    {
        $village = Village::with('district.regency')->find($villageId);

        $districtCode = $village && $village->district ? $village->district->code : '75.02.01';
        $villageCode  = $village ? $village->code : '2001';
        $yearCode     = Carbon::createFromDate($year ?? date('Y'), 1, 1)->format('y');

        $prefix = "{$districtCode}.{$villageCode}.{$yearCode}";

        // Cari nomor urut terakhir dengan prefix yang sama
        $latestCow = Cow::where('cow_nik', 'LIKE', "{$prefix}.%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latestCow) {
            $parts = explode('.', $latestCow->cow_nik);
            $lastSeq = (int) end($parts);
            $nextSeq = str_pad((string)($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextSeq = '0001';
        }

        return "{$prefix}.{$nextSeq}";
    }
}
