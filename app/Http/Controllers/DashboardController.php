<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cow;
use App\Models\Farmer;
use App\Models\BullStraw;
use App\Models\PregnancyRecord;
use App\Models\Regency;
use App\Models\District;
use App\Models\Calf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        // 1. Auto-lanjut 3 hari berbasis tanggal_inseminasi
        $activeCheckQuery = PregnancyRecord::whereNotIn('status', [
            'Melahirkan (Selesai)',
            'Gagal/Keguguran',
        ]);

        if ($isPureOperator) {
            $activeCheckQuery->where('operator_id', $user->id);
        } elseif ($isAdminKabupaten) {
            $activeCheckQuery->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        foreach ($activeCheckQuery->get() as $record) {
            $record->checkAndAutoAdvance();
        }

        // 2. MODE RINGAN KHUSUS INSEMINATOR (Mobile-First: Langsung Form Input IB + Riwayat Pribadi)
        if ($isPureOperator) {
            $search = $request->query('search');

            $myRecordsQuery = PregnancyRecord::with([
                'cow.farmer',
                'cow.regency',
                'cow.district',
                'cow.village',
                'calf',
            ])
                ->whereHas('cow')
                ->where('operator_id', $user->id)
                ->orderByDesc('insemination_date')
                ->orderByDesc('id');

            if ($search) {
                $myRecordsQuery->whereHas('cow', function ($q) use ($search) {
                    $q->where('cow_nik', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%")
                        ->orWhereHas('farmer', fn($fq) => $fq->where('name', 'LIKE', "%{$search}%"));
                });
            }

            $myRecords = $myRecordsQuery->paginate(10)->withQueryString();

            $myAll = PregnancyRecord::where('operator_id', $user->id)->whereHas('cow')->get();
            $myTotalIb = $myAll->count();
            $myTotalBunting = $myAll->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count();
            $myTotalLahir = $myAll->where('status', 'Melahirkan (Selesai)')->count();

            // Wilayah bebas lintas kabupaten untuk Inseminator
            $regencies = Regency::with('districts.villages')->orderBy('name')->get();
            $farmers = Farmer::with(['cows' => fn($q) => $q->select('id', 'farmer_id', 'cow_nik', 'name', 'breed', 'current_status')->latest()])
                ->withCount('cows')
                ->orderBy('name')
                ->get(['id', 'name', 'nik', 'phone', 'regency_id', 'district_id', 'village_id']);
            $straws = BullStraw::where('is_active', true)->orderBy('breed')->orderBy('kode_bull')->get();
            $breeds = Cow::BREEDS;

            // Sapi yang siap di-IB ulang (Gagal / Pasca Melahirkan) milik riwayat inseminator
            $myCowsForNextIb = Cow::with(['farmer', 'regency', 'district', 'village', 'latestPregnancy'])
                ->whereHas('pregnancyRecords', fn($q) => $q->where('operator_id', $user->id))
                ->latest()
                ->get()
                ->filter(fn($c) => $c->canReceiveNextIb())
                ->values();

            $confirmationReminders = PregnancyRecord::with(['cow.farmer', 'cow.regency', 'cow.district', 'cow.village'])
                ->whereHas('cow')
                ->where('operator_id', $user->id)
                ->dueForReminder()
                ->orderBy('estimated_birth_date')
                ->get();

            return view('inseminator.dashboard', compact(
                'myRecords',
                'myTotalIb',
                'myTotalBunting',
                'myTotalLahir',
                'regencies',
                'farmers',
                'straws',
                'breeds',
                'myCowsForNextIb',
                'confirmationReminders',
                'search'
            ));
        }

        // 3. DASHBOARD ADMIN PROVINSI, ADMIN KABUPATEN/KOTA, & PUBLIK
        $search       = $request->query('search');
        $regencyId    = $request->query('regency_id');
        $districtId   = $request->query('district_id');
        $villageId    = $request->query('village_id');
        $statusFilter = $request->query('status');
        $officerName  = $request->query('officer_name');
        $nikStatus    = $request->query('nik_status');
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        $query = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
            'calf',
        ])
            ->whereHas('cow')
            ->orderByDesc('insemination_date')
            ->orderByDesc('id');

        if ($isAdminKabupaten) {
            $query->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        }

        if ($search) {
            $query->whereHas('cow', function ($q) use ($search) {
                $q->where('cow_nik', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('farmer', function ($fq) use ($search) {
                        $fq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($officerName) {
            $query->where('officer_name', $officerName);
        }

        if ($regencyId && !$isAdminKabupaten) {
            $query->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }

        if ($districtId) {
            $query->whereHas('cow', fn($q) => $q->where('district_id', $districtId));
        }

        if ($villageId) {
            $query->whereHas('cow', fn($q) => $q->where('village_id', $villageId));
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($startDate) {
            $query->whereDate('insemination_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('insemination_date', '<=', $endDate);
        }

        if ($nikStatus === 'without_nik') {
            $query->whereHas('cow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $query->whereHas('cow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        $records = $query->paginate(10)->withQueryString();

        // STATISTIK UTAMA
        $cowQuery = Cow::forOperator($user);
        if ($regencyId && !$isAdminKabupaten) {
            $cowQuery->where('regency_id', $regencyId);
        }
        $totalCows = $cowQuery->count();

        $pregBaseQuery = PregnancyRecord::with('cow')->whereHas('cow');

        if ($isAdminKabupaten) {
            $pregBaseQuery->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $pregBaseQuery->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }

        if ($officerName) {
            $pregBaseQuery->where('officer_name', $officerName);
        }

        if ($startDate) {
            $pregBaseQuery->whereDate('insemination_date', '>=', $startDate);
        }

        if ($endDate) {
            $pregBaseQuery->whereDate('insemination_date', '<=', $endDate);
        }

        if ($nikStatus === 'without_nik') {
            $pregBaseQuery->whereHas('cow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $pregBaseQuery->whereHas('cow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        $allPregForStats = (clone $pregBaseQuery)->get();

        $totalIbCount = $allPregForStats->count();
        $totalPascaIb = $allPregForStats->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari'])->count();
        $totalPregnant90 = $allPregForStats->where('status', 'Bunting (PKB 90 Hari)')->count();
        $totalReady245 = $allPregForStats->where('status', 'Siap Melahirkan (245 Hari)')->count();
        $totalBirthPhase = $allPregForStats->where('status', 'Fase Melahirkan')->count();
        $totalDelivered = $allPregForStats->where('status', 'Melahirkan (Selesai)')->count();

        $totalConfirmedBunting = $allPregForStats->whereIn('status', [
            'Bunting (PKB 90 Hari)',
            'Siap Melahirkan (245 Hari)',
            'Fase Melahirkan',
            'Melahirkan (Selesai)',
        ])->count();

        $totalActivePregnancies = $allPregForStats->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count();

        $conceptionRate = $totalIbCount > 0 ? round(($totalConfirmedBunting / $totalIbCount) * 100, 1) : 0;
        $calvingRate = $totalConfirmedBunting > 0 ? round(($totalDelivered / $totalConfirmedBunting) * 100, 1) : 0;

        // Rekapitulasi per Ras/Jenis Sapi (Total IB, Total Bunting, Total Kelahiran/Pedet)
        $breedRecap = $allPregForStats
            ->groupBy(fn($r) => $r->cow->breed ?? 'Lainnya')
            ->map(function ($group, $breedName) {
                return (object) [
                    'breed' => $breedName,
                    'total_ib' => $group->count(),
                    'total_bunting' => $group->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES)->count(),
                    'total_lahir' => $group->where('status', 'Melahirkan (Selesai)')->count(),
                ];
            })
            ->sortByDesc('total_ib')
            ->values();

        // TOTAL PEDET
        $calfQuery = Calf::query();
        if ($isAdminKabupaten) {
            $calfQuery->whereHas('motherCow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $calfQuery->whereHas('motherCow', fn($q) => $q->where('regency_id', $regencyId));
        }
        if ($officerName) {
            $calfQuery->whereHas('pregnancyRecord', fn($q) => $q->where('officer_name', $officerName));
        }
        $totalCalves = $calfQuery->count();

        // NOTIFIKASI 3 TAHAP (Hari 30, Hari 90, Bulan ke-9) — Diarahkan ke Admin Kab/Kota & Admin Provinsi
        $reminderQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
        ])
            ->whereHas('cow')
            ->dueForReminder();

        if ($isAdminKabupaten) {
            $reminderQuery->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $reminderQuery->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }

        $confirmationReminders = $reminderQuery
            ->orderBy('estimated_birth_date')
            ->get();

        // MASTER FILTER
        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->orderBy('name')->get();

        $districts = $regencyId
            ? District::where('regency_id', $regencyId)->with('villages')->get()
            : District::with('villages')->get();

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

        $totalReadyToGiveBirth = $totalBirthPhase;
        $totalCalvesBorn = $totalCalves;

        return view('dashboard', compact(
            'records',
            'totalCows',
            'totalActivePregnancies',
            'totalReadyToGiveBirth',
            'totalCalvesBorn',
            'totalPascaIb',
            'totalPregnant90',
            'totalReady245',
            'totalBirthPhase',
            'totalDelivered',
            'totalCalves',
            'totalIbCount',
            'conceptionRate',
            'calvingRate',
            'breedRecap',
            'confirmationReminders',
            'regencies',
            'districts',
            'officers',
            'breeds',
            'search',
            'regencyId',
            'districtId',
            'villageId',
            'statusFilter',
            'officerName',
            'nikStatus',
            'startDate',
            'endDate'
        ));
    }
}
