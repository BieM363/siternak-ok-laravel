<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calf;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * CalfController — Menu Terpadu: Data Pedet & Kelahiran
 * Menggabungkan daftar Pedet dan rekapitulasi Kelahiran dalam satu menu tanpa duplikasi.
 */
class CalfController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $search       = $request->query('search');
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');
        $breedFilter  = $request->query('breed');
        $genderFilter = $request->query('gender');
        $officerName  = $request->query('officer_name');
        $regencyId    = $request->query('regency_id');
        $districtId   = $request->query('district_id');
        $nikStatus    = $request->query('nik_status');

        if ($isAdminKabupaten) {
            $regencyId = $user->regency_id;
        }

        $query = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord',
            'promotedCow',
        ]);

        if ($isPureOperator) {
            $query->whereHas('pregnancyRecord', fn($q) => $q->where('operator_id', $user->id));
        }

        if ($isAdminKabupaten) {
            $query->whereHas('motherCow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $query->whereHas('motherCow', fn($q) => $q->where('regency_id', $regencyId));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('calf_nik', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('breed', 'LIKE', "%{$search}%")
                  ->orWhereHas('motherCow', function ($mq) use ($search) {
                      $mq->where('cow_nik', 'LIKE', "%{$search}%")
                         ->orWhere('name', 'LIKE', "%{$search}%")
                         ->orWhereHas('farmer', fn($fq) => $fq->where('name', 'LIKE', "%{$search}%")->orWhere('nik', 'LIKE', "%{$search}%"));
                  });
            });
        }

        if ($startDate) {
            $query->whereDate('birth_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('birth_date', '<=', $endDate);
        }

        if ($breedFilter) {
            $query->where('breed', $breedFilter);
        }

        if ($genderFilter) {
            $query->where('gender', $genderFilter);
        }

        if ($officerName) {
            $query->whereHas('pregnancyRecord', fn($q) => $q->where('officer_name', $officerName));
        }

        if ($districtId) {
            $query->whereHas('motherCow', fn($q) => $q->where('district_id', $districtId));
        }

        if ($nikStatus === 'without_nik') {
            $query->whereHas('motherCow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $query->whereHas('motherCow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        $totalCalvesCount = (clone $query)->count();
        $maleCalvesCount = (clone $query)->where('gender', 'Jantan')->count();
        $femaleCalvesCount = (clone $query)->where('gender', 'Betina')->count();

        $calves = $query->latest('birth_date')->paginate(15)->withQueryString();

        // Statistik Rekapitulasi Terpadu (Total IB, Bunting, Kelahiran, CR & CVR)
        $pregStats = PregnancyRecord::query()->whereHas('cow');
        if ($isPureOperator) {
            $pregStats->where('operator_id', $user->id);
        }
        if ($isAdminKabupaten) {
            $pregStats->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif ($regencyId) {
            $pregStats->whereHas('cow', fn($q) => $q->where('regency_id', $regencyId));
        }
        if ($officerName) {
            $pregStats->where('officer_name', $officerName);
        }
        if ($nikStatus === 'without_nik') {
            $pregStats->whereHas('cow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($nikStatus === 'with_nik') {
            $pregStats->whereHas('cow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        $allPreg = $pregStats->get();
        $totalIb = $allPreg->count();
        $totalBunting = $allPreg->whereIn('status', [
            'Bunting (PKB 90 Hari)',
            'Siap Melahirkan (245 Hari)',
            'Fase Melahirkan',
            'Melahirkan (Selesai)',
        ])->count();
        $totalMelahirkan = $allPreg->where('status', 'Melahirkan (Selesai)')->count();
        $conceptionRate = $totalIb > 0 ? round(($totalBunting / $totalIb) * 100, 1) : 0;
        $calvingRate = $totalBunting > 0 ? round(($totalMelahirkan / $totalBunting) * 100, 1) : 0;

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::orderBy('name')->get();

        $districts = $regencyId
            ? District::where('regency_id', $regencyId)->get()
            : District::all();

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

        return view('calves.index', compact(
            'calves',
            'totalCalvesCount',
            'maleCalvesCount',
            'femaleCalvesCount',
            'totalIb',
            'totalBunting',
            'totalMelahirkan',
            'conceptionRate',
            'calvingRate',
            'regencies',
            'districts',
            'officers',
            'breeds',
            'search',
            'startDate',
            'endDate',
            'breedFilter',
            'genderFilter',
            'officerName',
            'regencyId',
            'districtId',
            'nikStatus'
        ));
    }

    public function show($id)
    {
        $user = auth()->user();
        $calf = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord',
            'promotedCow',
        ])->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        return view('calves.show', compact('calf'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $breeds = Cow::BREEDS;

        return view('calves.edit', compact('calf', 'breeds'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $request->validate([
            'calf_nik' => 'required|string|max:50|unique:calves,calf_nik,' . $id,
            'name' => 'nullable|string|max:100',
            'breed' => 'nullable|string|max:80',
            'gender' => 'required|in:Jantan,Betina',
            'birth_date' => 'required|date',
            'birth_weight' => 'nullable|numeric|min:0',
            'condition' => 'required|in:Sehat,Cacat,Mati',
            'notes' => 'nullable|string',
        ]);

        $calf->update([
            'calf_nik' => $request->calf_nik,
            'name' => $request->name,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'birth_weight' => $request->birth_weight,
            'condition' => $request->condition,
            'notes' => $request->notes,
        ]);

        return redirect()->route('calves.index')->with('success', "Data pedet NIK {$calf->calf_nik} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $calfNik = $calf->calf_nik;
        $calf->delete();

        return back()->with('success', "Data pedet NIK {$calfNik} berhasil dihapus.");
    }

    /**
     * Promosikan Pedet Betina menjadi Sapi Akseptor Siap IB
     */
    public function promoteToCow($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        if ($calf->gender !== 'Betina') {
            return back()->with('error', 'Hanya pedet Betina yang dapat dipromosikan menjadi sapi akseptor siap IB.');
        }

        if ($calf->is_promoted) {
            return back()->with('info', 'Pedet ini sudah pernah dipromosikan menjadi sapi akseptor siap IB.');
        }

        return DB::transaction(function () use ($calf) {
            $motherCow = $calf->motherCow;

            $newCowNik = 'SPI-' . str_replace('PEDET-', '', $calf->calf_nik);
            if (Cow::where('cow_nik', $newCowNik)->exists()) {
                $newCowNik = 'SPI-' . Carbon::now()->format('Ymd') . '-' . rand(1000, 9999);
            }

            $newCow = Cow::create([
                'cow_nik' => $newCowNik,
                'name' => $calf->name ? ($calf->name . ' (Akseptor Siap IB)') : ('Induk Hasil Pedet ' . $calf->calf_nik),
                'breed' => $calf->breed ?? ($motherCow->breed ?? 'Limosin'),
                'birth_date' => $calf->birth_date,
                'farmer_id' => $motherCow->farmer_id ?? null,
                'regency_id' => $motherCow->regency_id ?? null,
                'district_id' => $motherCow->district_id ?? null,
                'village_id' => $motherCow->village_id ?? null,
                'current_status' => 'Siap IB',
            ]);

            $calf->update([
                'is_promoted' => true,
                'promoted_cow_id' => $newCow->id,
            ]);

            return redirect()->route('cows.show', $newCow->id)->with(
                'success',
                "Selamat! Pedet betina NIK {$calf->calf_nik} berhasil dipromosikan menjadi Sapi Akseptor Siap IB dengan NIK {$newCow->cow_nik}."
            );
        });
    }
}
