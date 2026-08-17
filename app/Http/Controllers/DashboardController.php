<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cow;
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

        /*
        |--------------------------------------------------------------------------
        | AUTO ADVANCE
        |--------------------------------------------------------------------------
        */

        $activeCheckQuery = PregnancyRecord::whereNotIn('status', [
            'Melahirkan (Selesai)',
            'Gagal/Keguguran'
        ]);

        if ($isPureOperator) {
            $activeCheckQuery->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $activeCheckQuery->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        foreach ($activeCheckQuery->get() as $record) {
            $record->checkAndAutoAdvance();
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $search       = $request->query('search');
        $regencyId    = $request->query('regency_id');
        $districtId   = $request->query('district_id');
        $villageId    = $request->query('village_id');
        $statusFilter = $request->query('status');
        $officerName  = $request->query('officer_name');

        // Lock regency_id jika user adalah Admin Kabupaten
        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA TABEL
        |--------------------------------------------------------------------------
        */

        $query = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
            'calf'
        ])
        ->orderBy('estimated_birth_date', 'asc');

        if ($isPureOperator) {
            $query->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $query->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        /*
        | Search
        */
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

        /*
        | Inseminator / Officer Filter
        */
        if ($officerName) {
            $query->where('officer_name', $officerName);
        }

        /*
        | Regency
        */
        if ($regencyId) {
            $query->whereHas('cow', function ($q) use ($regencyId) {
                $q->where('regency_id', $regencyId);
            });
        }

        /*
        | District
        */
        if ($districtId) {
            $query->whereHas('cow', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        /*
        | Village
        */
        if ($villageId) {
            $query->whereHas('cow', function ($q) use ($villageId) {
                $q->where('village_id', $villageId);
            });
        }

        /*
        | Status
        */
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $records = $query->paginate(10)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $cowQuery = Cow::forOperator($user);
        if ($isAdminKabupaten) {
            $cowQuery->where('regency_id', $user->regency_id);
        }
        $totalCows = $cowQuery->count();

        $pregBaseQuery = PregnancyRecord::query();

        if ($isPureOperator) {
            $pregBaseQuery->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $pregBaseQuery->whereHas('cow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        if ($regencyId && !$isAdminKabupaten) {
            $pregBaseQuery->whereHas('cow', function ($q) use ($regencyId) {
                $q->where('regency_id', $regencyId);
            });
        }

        if ($officerName) {
            $pregBaseQuery->where('officer_name', $officerName);
        }

        $totalPascaIb = (clone $pregBaseQuery)
            ->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari'])
            ->count();

        $totalPregnant90 = (clone $pregBaseQuery)
            ->where('status', 'Bunting (PKB 90 Hari)')
            ->count();

        $totalReady245 = (clone $pregBaseQuery)
            ->where('status', 'Siap Melahirkan (245 Hari)')
            ->count();

        $totalBirthPhase = (clone $pregBaseQuery)
            ->where('status', 'Fase Melahirkan')
            ->count();

        $totalDelivered = (clone $pregBaseQuery)
            ->where('status', 'Melahirkan (Selesai)')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CONCEPTION RATE (CR %) DAN CALVING RATE (CvR %)
        |--------------------------------------------------------------------------
        |
        | Conception Rate (CR %) = (Total Confirmed Bunting / Total IB) * 100%
        | Calving Rate (CvR %)   = (Total Melahirkan / Total Bunting Positif) * 100%
        |
        */

        $totalIbCount = (clone $pregBaseQuery)->count();
        $totalConfirmedBunting = (clone $pregBaseQuery)
            ->whereIn('status', [
                'Bunting (PKB 90 Hari)',
                'Siap Melahirkan (245 Hari)',
                'Fase Melahirkan',
                'Melahirkan (Selesai)'
            ])->count();

        $conceptionRate = $totalIbCount > 0
            ? round(($totalConfirmedBunting / $totalIbCount) * 100, 1)
            : 0;

        $calvingRate = $totalConfirmedBunting > 0
            ? round(($totalDelivered / $totalConfirmedBunting) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | TOTAL ANAK SAPI
        |--------------------------------------------------------------------------
        */

        $calfQuery = Calf::query();

        if ($isPureOperator) {
            $calfQuery->whereHas('pregnancyRecord', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        if ($isAdminKabupaten) {
            $calfQuery->whereHas('motherCow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        $totalCalves = $calfQuery->count();

        /*
        |--------------------------------------------------------------------------
        | REMINDER KONFIRMASI
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();

        $reminderQuery = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village'
        ])
        ->whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran'])
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

        $confirmationReminders = $reminderQuery
            ->orderBy('estimated_birth_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | WILAYAH & OFFICERS
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

        $totalActivePregnancies = $totalConfirmedBunting;
        $totalReadyToGiveBirth = $totalReady245 + $totalBirthPhase;
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
            'confirmationReminders',
            'regencies',
            'districts',
            'officers',
            'search',
            'regencyId',
            'districtId',
            'villageId',
            'statusFilter',
            'officerName'
        ));
    }
}