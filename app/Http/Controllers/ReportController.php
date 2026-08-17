<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PregnancyRecord;
use App\Models\Calf;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menu 1: Rekapan Kelahiran Pedet (Anak Sapi)
     */
    public function index(Request $request)
    {
        $user             = auth()->user();
        $isPureOperator   = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $month       = $request->query('month', date('m'));
        $year        = $request->query('year', date('Y'));
        $startDate   = $request->query('start_date');
        $endDate     = $request->query('end_date');
        $calfGender  = $request->query('calf_gender');
        $breedFilter = $request->query('breed');
        $regencyId   = $request->query('regency_id');
        $districtId  = $request->query('district_id');
        $villageId   = $request->query('village_id');
        $status      = $request->query('status');
        $officerName = $request->query('officer_name');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        /*
        |--------------------------------------------------------------------------
        | REKAPAN ANAK SAPI LAHIR
        |--------------------------------------------------------------------------
        */

        $calvesQuery = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord'
        ]);

        if ($startDate && $endDate) {
            $calvesQuery->whereBetween('birth_date', [$startDate, $endDate]);
        } else {
            $calvesQuery->whereYear('birth_date', $year)
                        ->whereMonth('birth_date', $month);
        }

        if ($isPureOperator) {
            $calvesQuery->whereHas('pregnancyRecord', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        if ($isAdminKabupaten) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        if ($calfGender) {
            $calvesQuery->where('gender', $calfGender);
        }

        if ($breedFilter) {
            $calvesQuery->where('breed', $breedFilter);
        }

        if ($officerName) {
            $calvesQuery->whereHas('pregnancyRecord', function ($q) use ($officerName) {
                $q->where('officer_name', $officerName);
            });
        }

        if ($regencyId) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($regencyId) {
                $q->where('regency_id', $regencyId);
            });
        }

        if ($districtId) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        if ($villageId) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($villageId) {
                $q->where('village_id', $villageId);
            });
        }

        $bornCalves = $calvesQuery->latest('birth_date')->get();

        $maleCalvesCount   = (clone $calvesQuery)->where('gender', 'Jantan')->count();
        $femaleCalvesCount = (clone $calvesQuery)->where('gender', 'Betina')->count();

        /*
        |--------------------------------------------------------------------------
        | GENERAL PREGNANCY RECORDS
        |--------------------------------------------------------------------------
        */

        $pregnancyQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
            'calf'
        ]);

        if ($isPureOperator) {
            $pregnancyQuery->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $pregnancyQuery->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        if ($regencyId && !$isAdminKabupaten) {
            $pregnancyQuery->whereHas('cow', function ($q) use ($regencyId) {
                $q->where('regency_id', $regencyId);
            });
        }

        if ($districtId) {
            $pregnancyQuery->whereHas('cow', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        if ($status) {
            $pregnancyQuery->where('status', $status);
        }

        if ($officerName) {
            $pregnancyQuery->where('officer_name', $officerName);
        }

        $pregnancyRecords = $pregnancyQuery->latest('insemination_date')->get();

        /*
        |--------------------------------------------------------------------------
        | CR (CONCEPTION RATE) & CVR (CALVING RATE) METRICS
        |--------------------------------------------------------------------------
        */

        $totalIb = $pregnancyRecords->count();
        $totalConfirmedBunting = $pregnancyRecords->whereIn('status', [
            'Bunting (PKB 90 Hari)',
            'Siap Melahirkan (245 Hari)',
            'Fase Melahirkan',
            'Melahirkan (Selesai)'
        ])->count();

        $totalMelahirkan = $pregnancyRecords->where('status', 'Melahirkan (Selesai)')->count();

        $conceptionRate = $totalIb > 0 ? round(($totalConfirmedBunting / $totalIb) * 100, 1) : 0;
        $calvingRate    = $totalConfirmedBunting > 0 ? round(($totalMelahirkan / $totalConfirmedBunting) * 100, 1) : 0;

        /*
        |--------------------------------------------------------------------------
        | MASTER DATA FOR FILTERS
        |--------------------------------------------------------------------------
        */

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->get();

        $districts = $regencyId
            ? District::where('regency_id', $regencyId)->with('villages')->get()
            : District::with('villages')->get();

        $officers = DB::table('pregnancy_records')
            ->whereNotNull('officer_name')
            ->distinct()
            ->pluck('officer_name');

        $breeds = ['Limosin', 'Simental', 'Frisian Holstein', 'PO (Peranakan Ongole)', 'Brahman', 'Madura', 'Cross'];

        return view('reports.index', compact(
            'bornCalves',
            'pregnancyRecords',
            'maleCalvesCount',
            'femaleCalvesCount',
            'conceptionRate',
            'calvingRate',
            'totalIb',
            'totalConfirmedBunting',
            'totalMelahirkan',
            'regencies',
            'districts',
            'officers',
            'breeds',
            'month',
            'year',
            'startDate',
            'endDate',
            'calfGender',
            'breedFilter',
            'regencyId',
            'districtId',
            'villageId',
            'status',
            'officerName'
        ));
    }

    /**
     * Menu 2: Halaman Terpisah Leaderboard & Performansi Inseminator
     */
    public function inseminators(Request $request)
    {
        $user             = auth()->user();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $regencyId = $request->query('regency_id');
        $search    = $request->query('search');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        $inseminatorQuery = DB::table('pregnancy_records')
            ->select(
                'officer_name',
                DB::raw('COUNT(*) as total_ib'),
                DB::raw("SUM(CASE WHEN status IN ('Bunting (PKB 90 Hari)', 'Siap Melahirkan (245 Hari)', 'Fase Melahirkan', 'Melahirkan (Selesai)') THEN 1 ELSE 0 END) as total_bunting"),
                DB::raw("SUM(CASE WHEN status = 'Melahirkan (Selesai)' THEN 1 ELSE 0 END) as total_melahirkan")
            )
            ->whereNotNull('officer_name')
            ->groupBy('officer_name')
            ->orderBy('total_ib', 'desc');

        if ($isAdminKabupaten) {
            $inseminatorQuery->whereIn('cow_id', function ($q) use ($user) {
                $q->select('id')->from('cows')->where('regency_id', $user->regency_id);
            });
        } elseif ($regencyId) {
            $inseminatorQuery->whereIn('cow_id', function ($q) use ($regencyId) {
                $q->select('id')->from('cows')->where('regency_id', $regencyId);
            });
        }

        if ($search) {
            $inseminatorQuery->where('officer_name', 'LIKE', "%{$search}%");
        }

        $inseminatorRecap = $inseminatorQuery->get()->map(function ($row) {
            $row->cr = $row->total_ib > 0 ? round(($row->total_bunting / $row->total_ib) * 100, 1) : 0;
            return $row;
        });

        $totalIbAll = $inseminatorRecap->sum('total_ib');
        $totalBuntingAll = $inseminatorRecap->sum('total_bunting');
        $totalMelahirkanAll = $inseminatorRecap->sum('total_melahirkan');
        $avgCr = $totalIbAll > 0 ? round(($totalBuntingAll / $totalIbAll) * 100, 1) : 0;

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::all();

        return view('reports.inseminators', compact(
            'inseminatorRecap',
            'totalIbAll',
            'totalBuntingAll',
            'totalMelahirkanAll',
            'avgCr',
            'regencies',
            'regencyId',
            'search'
        ));
    }

    public function exportPdf(Request $request)
    {
        $user             = auth()->user();
        $isPureOperator   = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $month      = $request->query('month', date('m'));
        $year       = $request->query('year', date('Y'));
        $calfGender = $request->query('calf_gender');

        $calvesQuery = Calf::with(['motherCow.farmer', 'motherCow.regency', 'motherCow.district', 'motherCow.village'])
            ->whereYear('birth_date', $year)
            ->whereMonth('birth_date', $month);

        if ($isPureOperator) {
            $calvesQuery->whereHas('pregnancyRecord', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        if ($isAdminKabupaten) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        if ($calfGender) {
            $calvesQuery->where('gender', $calfGender);
        }

        $bornCalves = $calvesQuery->latest('birth_date')->get();

        return view('reports.pdf', compact('bornCalves', 'month', 'year', 'calfGender'));
    }

    public function exportExcel(Request $request)
    {
        $user             = auth()->user();
        $isPureOperator   = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $month      = $request->query('month', date('m'));
        $year       = $request->query('year', date('Y'));
        $calfGender = $request->query('calf_gender');

        $calvesQuery = Calf::with(['motherCow.farmer', 'motherCow.regency', 'motherCow.district', 'motherCow.village'])
            ->whereYear('birth_date', $year)
            ->whereMonth('birth_date', $month);

        if ($isPureOperator) {
            $calvesQuery->whereHas('pregnancyRecord', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        if ($isAdminKabupaten) {
            $calvesQuery->whereHas('motherCow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        if ($calfGender) {
            $calvesQuery->where('gender', $calfGender);
        }

        $calves = $calvesQuery->latest('birth_date')->get();

        $filename = "Rekapan_Kelahiran_Anak_Sapi_{$year}_{$month}_" . date('His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($calves) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            fputcsv($file, [
                'No',
                'NIK Anak Sapi',
                'Nama Anak',
                'Ras',
                'Jenis Kelamin Anak',
                'Tanggal Lahir Anak',
                'Berat Lahir (kg)',
                'Kondisi Saat Lahir',
                'NIK Induk Sapi',
                'Nama Induk',
                'Nama Peternak',
                'NIK Peternak',
                'Kabupaten',
                'Kecamatan',
                'Desa'
            ]);

            foreach ($calves as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->calf_nik,
                    $row->name ?? '-',
                    $row->breed ?? ($row->motherCow->breed ?? '-'),
                    $row->gender,
                    $row->birth_date ? $row->birth_date->format('d/m/Y') : '-',
                    $row->birth_weight ?? '-',
                    $row->condition,
                    $row->motherCow->cow_nik ?? '-',
                    $row->motherCow->name ?? '-',
                    $row->motherCow->farmer->name ?? '-',
                    $row->motherCow->farmer->nik ?? '-',
                    $row->motherCow->regency->name ?? '-',
                    $row->motherCow->district->name ?? '-',
                    $row->motherCow->village->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
