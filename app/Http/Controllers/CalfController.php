<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calf;
use App\Models\Cow;
use App\Models\Regency;
use App\Models\District;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalfController extends Controller
{
    /**
     * Tampilkan Daftar Data Anak Sapi (Pedet)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();

        $search       = $request->query('search');
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');
        $breedFilter  = $request->query('breed');
        $genderFilter = $request->query('gender');
        $officerName  = $request->query('officer_name');
        $regencyId    = $request->query('regency_id');
        $districtId   = $request->query('district_id');

        // Otomatis lock regency_id untuk Admin Kabupaten
        if ($user && $user->isAdminKabupaten()) {
            $regencyId = $user->regency_id;
        }

        $query = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord',
            'promotedCow'
        ]);

        // Access scope operator
        if ($isPureOperator) {
            $query->whereHas('pregnancyRecord', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        // Scope regional Admin Kabupaten
        if ($user && $user->isAdminKabupaten()) {
            $query->whereHas('motherCow', function ($q) use ($user) {
                $q->where('regency_id', $user->regency_id);
            });
        }

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('calf_nik', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('breed', 'LIKE', "%{$search}%")
                  ->orWhereHas('motherCow', function ($mq) use ($search) {
                      $mq->where('cow_nik', 'LIKE', "%{$search}%")
                         ->orWhere('name', 'LIKE', "%{$search}%")
                         ->orWhereHas('farmer', function ($fq) use ($search) {
                             $fq->where('name', 'LIKE', "%{$search}%");
                         });
                  });
            });
        }

        // Filter Tanggal Lahir
        if ($startDate) {
            $query->whereDate('birth_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('birth_date', '<=', $endDate);
        }

        // Filter Ras
        if ($breedFilter) {
            $query->where('breed', $breedFilter);
        }

        // Filter Gender
        if ($genderFilter) {
            $query->where('gender', $genderFilter);
        }

        // Filter Inseminator / Petugas
        if ($officerName) {
            $query->whereHas('pregnancyRecord', function ($q) use ($officerName) {
                $q->where('officer_name', $officerName);
            });
        }

        // Filter Wilayah
        if ($regencyId) {
            $query->whereHas('motherCow', function ($q) use ($regencyId) {
                $q->where('regency_id', $regencyId);
            });
        }

        if ($districtId) {
            $query->whereHas('motherCow', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        }

        $calves = $query->latest('birth_date')->paginate(15)->withQueryString();

        // Opsi Filter
        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::all();

        $districts = $regencyId
            ? District::where('regency_id', $regencyId)->get()
            : District::all();

        $officers = DB::table('pregnancy_records')
            ->whereNotNull('officer_name')
            ->distinct()
            ->pluck('officer_name');

        $breeds = ['Limosin', 'Simental', 'Frisian Holstein', 'PO (Peranakan Ongole)', 'Brahman', 'Madura', 'Cross'];

        return view('calves.index', compact(
            'calves',
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
            'districtId'
        ));
    }

    /**
     * Tampilkan Detail Anak Sapi
     */
    public function show($id)
    {
        $user = auth()->user();
        $calf = Calf::with([
            'motherCow.farmer',
            'motherCow.regency',
            'motherCow.district',
            'motherCow.village',
            'pregnancyRecord',
            'promotedCow'
        ])->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten Anda.');
        }

        return view('calves.show', compact('calf'));
    }

    /**
     * Form Edit Data Anak Sapi
     */
    public function edit($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten Anda.');
        }

        $breeds = ['Limosin', 'Simental', 'Frisian Holstein', 'PO (Peranakan Ongole)', 'Brahman', 'Madura', 'Cross'];

        return view('calves.edit', compact('calf', 'breeds'));
    }

    /**
     * Update Data Anak Sapi
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return redirect()->route('calves.index')->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten Anda.');
        }

        $request->validate([
            'calf_nik' => 'required|string|max:50|unique:calves,calf_nik,' . $id,
            'name' => 'nullable|string|max:100',
            'breed' => 'nullable|string|max:50',
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

        return redirect()->route('calves.index')->with('success', "Data anak sapi NIK {$calf->calf_nik} berhasil diperbarui.");
    }

    /**
     * Hapus Data Anak Sapi
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten Anda.');
        }

        $calfNik = $calf->calf_nik;
        $calf->delete();

        return back()->with('success', "Data anak sapi NIK {$calfNik} berhasil dihapus.");
    }

    /**
     * Promosikan Anak Sapi Betina menjadi Sapi Siap IB/Hamil
     */
    public function promoteToCow($id)
    {
        $user = auth()->user();
        $calf = Calf::with('motherCow')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $calf->motherCow && $calf->motherCow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak. Pedet ini berada di luar wilayah Kabupaten Anda.');
        }

        if ($calf->gender !== 'Betina') {
            return back()->with('error', 'Hanya anak sapi Betina yang dapat dipromosikan menjadi sapi siap IB.');
        }

        if ($calf->is_promoted) {
            return back()->with('info', 'Anak sapi ini sudah pernah dipromosikan menjadi sapi siap IB.');
        }

        return DB::transaction(function () use ($calf) {
            $motherCow = $calf->motherCow;

            $newCowNik = 'SPI-' . str_replace('PEDET-', '', $calf->calf_nik);
            if (Cow::where('cow_nik', $newCowNik)->exists()) {
                $newCowNik = 'SPI-' . Carbon::now()->format('Ymd') . '-' . rand(1000, 9999);
            }

            $newCow = Cow::create([
                'cow_nik' => $newCowNik,
                'name' => $calf->name ? ($calf->name . ' (Induk Siap IB)') : ('Induk Hasil Pedet ' . $calf->calf_nik),
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
                "Selamat! Anak sapi betina NIK {$calf->calf_nik} telah berhasil ditarik & dipromosikan menjadi Sapi Siap IB dengan NIK {$newCow->cow_nik}."
            );
        });
    }
}
