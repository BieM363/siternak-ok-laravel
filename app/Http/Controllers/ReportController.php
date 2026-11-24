<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\Calf;
use App\Models\Regency;
use App\Models\District;
use App\Services\XlsxExportService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Halaman Rekapitulasi Kinerja (Total IB, Total Bunting, Total Kelahiran/Pedet per Ras) + Ekspor Excel (.xlsx) & PDF
     */
    public function index(Request $request)
    {
        $user             = auth()->user();
        $isPureOperator   = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $startDate   = $request->has('start_date') ? $request->query('start_date') : null;
        $endDate     = $request->has('end_date') ? $request->query('end_date') : null;
        $calfGender  = $request->query('calf_gender') ?: $request->query('gender');
        $breedFilter = $request->query('breed');
        $regencyId   = $request->query('regency_id');
        $districtId  = $request->query('district_id');
        $villageId   = $request->query('village_id');
        $status      = $request->query('status');
        $officerName = $request->query('officer_name');
        $nikStatus   = $request->query('nik_status');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        $calvesQuery = $this->buildFilteredCalvesQuery($request, $user, $regencyId);
        $bornCalves = (clone $calvesQuery)->latest('birth_date')->get();
        $maleCalvesCount = $bornCalves->where('gender', 'Jantan')->count();
        $femaleCalvesCount = $bornCalves->where('gender', 'Betina')->count();

        $pregnancyQuery = $this->buildFilteredPregnancyQuery($request, $user, $regencyId);
        $pregnancyRecords = (clone $pregnancyQuery)->latest('insemination_date')->get();

        $totalIb = $pregnancyRecords->count();
        $totalConfirmedBunting = $pregnancyRecords->whereIn('status', [
            'Bunting (PKB 90 Hari)',
            'Siap Melahirkan (245 Hari)',
            'Fase Melahirkan',
            'Melahirkan (Selesai)',
        ])->count();
        $totalActiveBunting = $pregnancyRecords->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count();
        $totalMelahirkan = $pregnancyRecords->where('status', 'Melahirkan (Selesai)')->count();

        $conceptionRate = $totalIb > 0 ? round(($totalConfirmedBunting / $totalIb) * 100, 1) : 0;
        $calvingRate    = $totalConfirmedBunting > 0 ? round(($totalMelahirkan / $totalConfirmedBunting) * 100, 1) : 0;

        // Rekapitulasi Rinci Berdasarkan Ras/Jenis Sapi
        $breedRecap = $pregnancyRecords
            ->groupBy(fn($r) => $r->cow->breed ?? 'Lainnya')
            ->map(function ($group, $breedName) {
                return (object) [
                    'breed' => $breedName,
                    'total_ib' => $group->count(),
                    'total_bunting' => $group->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count(),
                    'total_lahir' => $group->where('status', 'Melahirkan (Selesai)')->count(),
                    'total_gagal' => $group->where('status', 'Gagal/Keguguran')->count(),
                ];
            })
            ->sortByDesc('total_ib')
            ->values();

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->orderBy('name')->get();

        $districts = $regencyId
            ? District::where('regency_id', $regencyId)->with('villages')->get()
            : District::with('villages')->get();

        $officerQuery = DB::table('pregnancy_records')->whereNotNull('officer_name')->where('officer_name', '!=', '');
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

        return view('reports.index', compact(
            'bornCalves',
            'pregnancyRecords',
            'breedRecap',
            'maleCalvesCount',
            'femaleCalvesCount',
            'conceptionRate',
            'calvingRate',
            'totalIb',
            'totalConfirmedBunting',
            'totalActiveBunting',
            'totalMelahirkan',
            'regencies',
            'districts',
            'officers',
            'breeds',
            'startDate',
            'endDate',
            'calfGender',
            'breedFilter',
            'regencyId',
            'districtId',
            'villageId',
            'status',
            'officerName',
            'nikStatus'
        ));
    }

    /**
     * Evaluasi Kinerja & Leaderboard Inseminator (Dengan Filter Wilayah & Periode Tanggal)
     */
    public function inseminators(Request $request)
    {
        $user             = auth()->user();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $regencyId = $request->query('regency_id');
        $search    = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

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
            ->where('officer_name', '!=', '')
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

        if ($startDate) {
            $inseminatorQuery->whereDate('insemination_date', '>=', $startDate);
        }

        if ($endDate) {
            $inseminatorQuery->whereDate('insemination_date', '<=', $endDate);
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
            : Regency::orderBy('name')->get();

        return view('reports.inseminators', compact(
            'inseminatorRecap',
            'totalIbAll',
            'totalBuntingAll',
            'totalMelahirkanAll',
            'avgCr',
            'regencies',
            'regencyId',
            'search',
            'startDate',
            'endDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $user             = auth()->user();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $startDate   = $request->query('start_date');
        $endDate     = $request->query('end_date');
        $calfGender  = $request->query('calf_gender') ?: $request->query('gender');
        $breedFilter = $request->query('breed');
        $regencyId   = $isAdminKabupaten ? $user->regency_id : $request->query('regency_id');
        $officerName = $request->query('officer_name');

        $bornCalves = $this->buildFilteredCalvesQuery($request, $user, $regencyId)
            ->latest('birth_date')
            ->get();

        return view('reports.pdf', compact(
            'bornCalves',
            'startDate',
            'endDate',
            'calfGender',
            'breedFilter',
            'officerName'
        ));
    }

    /**
     * Ekspor Laporan Terpadu ke Excel Asli (.xlsx) — 3 Sheet:
     * 1. Rekapitulasi Per Ras (Total IB, Bunting, Kelahiran/Pedet)
     * 2. Data Pelayanan IB (Lengkap dengan Identitas Pejantan 3 Kolom, IB ke-N, Status NIK)
     * 3. Data Pedet & Kelahiran
     * Seluruh data mengikuti filter yang sedang aktif!
     */
    public function exportExcel(Request $request)
    {
        $user             = auth()->user();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();
        $regencyId        = $isAdminKabupaten ? $user->regency_id : $request->query('regency_id');
        $isPublic         = !auth()->check() || auth()->user()->isGuest();

        $pregnancyRecords = $this->buildFilteredPregnancyQuery($request, $user, $regencyId)
            ->latest('insemination_date')
            ->get();

        $calves = $this->buildFilteredCalvesQuery($request, $user, $regencyId)
            ->latest('birth_date')
            ->get();

        // Sheet 1: Rekapitulasi Per Ras
        $recapRows = [];
        $grouped = $pregnancyRecords->groupBy(fn($r) => $r->cow->breed ?? 'Lainnya');
        $no = 1;
        foreach ($grouped as $breedName => $group) {
            $ib = $group->count();
            $bunting = $group->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count();
            $lahir = $group->where('status', 'Melahirkan (Selesai)')->count();
            $gagal = $group->where('status', 'Gagal/Keguguran')->count();
            $recapRows[] = [$no++, $breedName, $ib, $bunting, $lahir, $gagal];
        }
        $recapRows[] = [
            '',
            'TOTAL KESELURUHAN',
            $pregnancyRecords->count(),
            $pregnancyRecords->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count(),
            $pregnancyRecords->where('status', 'Melahirkan (Selesai)')->count(),
            $pregnancyRecords->where('status', 'Gagal/Keguguran')->count(),
        ];

        // Sheet 2: Data Pelayanan IB & Kebuntingan
        $ibRows = [];
        foreach ($pregnancyRecords as $idx => $rec) {
            $cow = $rec->cow;
            $farmer = $cow?->farmer;
            $nikText = 'Tanpa NIK';
            if ($farmer && $farmer->nik) {
                $nikText = $isPublic
                    ? substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4)
                    : $farmer->nik;
            }

            $ibRows[] = [
                $idx + 1,
                $rec->insemination_date ? $rec->insemination_date->format('d/m/Y') : '-',
                $rec->created_at ? $rec->created_at->format('d/m/Y') : '-',
                'IB ke-' . ($rec->ib_ke ?? 1),
                $cow?->cow_nik ?? '-',
                $cow?->name ?? '-',
                $cow?->breed ?? '-',
                $farmer?->name ?? '-',
                $nikText,
                $cow?->regency?->name ?? '-',
                $cow?->district?->name ?? '-',
                $cow?->village?->name ?? '-',
                $rec->straw_rumpun ?: '-',
                $rec->straw_kode_bull ?: ($rec->semen_code ?: '-'),
                $rec->straw_kode_batch ?: '-',
                $rec->officer_name ?? '-',
                $rec->pregnancy_age_label,
                $rec->status_display_label,
            ];
        }

        // Sheet 3: Data Pedet & Kelahiran
        $calfRows = [];
        foreach ($calves as $idx => $calf) {
            $mother = $calf->motherCow;
            $farmer = $mother?->farmer;
            $nikText = 'Tanpa NIK';
            if ($farmer && $farmer->nik) {
                $nikText = $isPublic
                    ? substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4)
                    : $farmer->nik;
            }

            $calfRows[] = [
                $idx + 1,
                $calf->calf_nik,
                $calf->name,
                $calf->breed ?? '-',
                $calf->gender,
                $calf->birth_date ? Carbon::parse($calf->birth_date)->format('d/m/Y') : '-',
                $calf->condition ?? 'Sehat',
                $mother?->cow_nik ?? '-',
                $mother?->name ?? '-',
                $farmer?->name ?? '-',
                $nikText,
                $mother?->regency?->name ?? '-',
                $mother?->district?->name ?? '-',
                $mother?->village?->name ?? '-',
                $calf->pregnancyRecord?->officer_name ?? '-',
            ];
        }

        $filterNotes = array_filter([
            $request->query('start_date') ? 'Dari ' . $request->query('start_date') : null,
            $request->query('end_date') ? 's/d ' . $request->query('end_date') : null,
            $request->query('officer_name') ? 'Inseminator: ' . $request->query('officer_name') : null,
            $request->query('nik_status') === 'without_nik' ? 'Filter: Tanpa NIK' : ($request->query('nik_status') === 'with_nik' ? 'Filter: Ada NIK' : null),
        ]);
        $subtitleSuffix = !empty($filterNotes) ? ' | ' . implode(' · ', $filterNotes) : '';
        $filename = 'Laporan_SiTernak_OK_' . date('Ymd_His') . '.xlsx';

        return XlsxExportService::download($filename, [
            [
                'title' => 'Rekap Per Ras',
                'subtitle' => 'SiTernak-OK · Rekapitulasi IB, Kebuntingan & Kelahiran Per Ras' . $subtitleSuffix,
                'headers' => ['No', 'Jenis / Ras Sapi', 'Total Sapi di-IB', 'Total Bunting Aktif', 'Total Kelahiran / Pedet', 'Tidak Bunting / Gagal'],
                'rows' => $recapRows,
            ],
            [
                'title' => 'Data Pelayanan IB',
                'subtitle' => 'SiTernak-OK · Rincian Pelayanan Inseminasi Buatan & Kebuntingan' . $subtitleSuffix,
                'headers' => [
                    'No', 'Tanggal Inseminasi', 'Tanggal Pelaporan', 'Urutan IB', 'NIK Sapi', 'Nama Sapi',
                    'Jenis/Ras Sapi', 'Nama Pemilik', 'NIK Pemilik', 'Kabupaten/Kota', 'Kecamatan', 'Desa/Kelurahan',
                    'Rumpun Pejantan', 'Kode Bull', 'Kode Batch', 'Inseminator', 'Umur Kebuntingan', 'Status',
                ],
                'rows' => $ibRows,
            ],
            [
                'title' => 'Pedet & Kelahiran',
                'subtitle' => 'SiTernak-OK · Data Kelahiran Pedet' . $subtitleSuffix,
                'headers' => [
                    'No', 'NIK Pedet', 'Nama Pedet', 'Ras Pedet', 'Jenis Kelamin', 'Tanggal Lahir', 'Kondisi',
                    'NIK Induk', 'Nama Induk', 'Pemilik', 'NIK Pemilik', 'Kabupaten/Kota', 'Kecamatan', 'Desa/Kelurahan', 'Inseminator',
                ],
                'rows' => $calfRows,
            ],
        ]);
    }

    private function buildFilteredCalvesQuery(Request $request, $user, $regencyId)
    {
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $startDate   = $request->query('start_date');
        $endDate     = $request->query('end_date');
        $calfGender  = $request->query('calf_gender') ?: $request->query('gender');
        $breedFilter = $request->query('breed');
        $districtId  = $request->query('district_id');
        $villageId   = $request->query('village_id');
        $officerName = $request->query('officer_name');
        $nikStatus   = $request->query('nik_status');

        $calvesQuery = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord',
        ]);

        if ($startDate) {
            $calvesQuery->whereDate('birth_date', '>=', $startDate);
        }
        if ($endDate) {
            $calvesQuery->whereDate('birth_date', '<=', $endDate);
        }
        if ($isPureOperator) {
            $calvesQuery->whereHas('pregnancyRecord', fn($q) => $q->where('operator_id', $user->id));
        }
        if ($isAdminKabupaten) {
            $calvesQuery->whereHas('motherCow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $calvesQuery->whereHas('motherCow', fn($q) => $q->where('regency_id', $regencyId));
        }
        if ($calfGender) {
            $calvesQuery->where('gender', $calfGender);
        }
        if ($breedFilter) {
            $calvesQuery->where('breed', $breedFilter);
        }
        if ($officerName) {
            $calvesQuery->whereHas('pregnancyRecord', fn($q) => $q->where('officer_name', $officerName));
        }
        if ($districtId) {
            $calvesQuery->whereHas('motherCow', fn($q) => $q->where('district_id', $districtId));
        }
        if ($villageId) {
            $calvesQuery->whereHas('motherCow', fn($q) => $q->where('village_id', $villageId));
        }
        if ($nikStatus === 'without_nik') {
            $calvesQuery->whereHas('motherCow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $calvesQuery->whereHas('motherCow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        return $calvesQuery;
    }

    private function buildFilteredPregnancyQuery(Request $request, $user, $regencyId)
    {
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $startDate   = $request->query('start_date');
        $endDate     = $request->query('end_date');
        $breedFilter = $request->query('breed');
        $districtId  = $request->query('district_id');
        $villageId   = $request->query('village_id');
        $status      = $request->query('status');
        $officerName = $request->query('officer_name');
        $nikStatus   = $request->query('nik_status');

        $pregnancyQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
            'calf',
        ])->whereHas('cow');

        if ($startDate) {
            $pregnancyQuery->whereDate('insemination_date', '>=', $startDate);
        }
        if ($endDate) {
            $pregnancyQuery->whereDate('insemination_date', '<=', $endDate);
        }
        if ($isPureOperator) {
            $pregnancyQuery->where('operator_id', $user->id);
        }
        if ($isAdminKabupaten) {
            $pregnancyQuery->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $pregnancyQuery->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }
        if ($districtId) {
            $pregnancyQuery->whereHas('cow', fn($q) => $q->where('district_id', $districtId));
        }
        if ($villageId) {
            $pregnancyQuery->whereHas('cow', fn($q) => $q->where('village_id', $villageId));
        }
        if ($breedFilter) {
            $pregnancyQuery->whereHas('cow', fn($q) => $q->where('breed', $breedFilter));
        }
        if ($status) {
            $pregnancyQuery->where('status', $status);
        }
        if ($officerName) {
            $pregnancyQuery->where('officer_name', $officerName);
        }
        if ($nikStatus === 'without_nik') {
            $pregnancyQuery->whereHas('cow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $pregnancyQuery->whereHas('cow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        return $pregnancyQuery;
    }
}
