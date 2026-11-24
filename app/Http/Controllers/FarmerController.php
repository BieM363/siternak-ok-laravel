<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

/**
 * Controller Manajemen Data Pemilik Sapi (Peternak)
 * SiTernak-OK — NIK KTP bersifat opsional (nullable) dengan filter Tanpa NIK.
 */
class FarmerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $nikStatus = $request->query('nik_status');
        $regencyId = $request->query('regency_id');

        if ($user && $user->isAdminKabupaten()) {
            $regencyId = $user->regency_id;
        }

        $query = Farmer::with(['regency', 'district', 'village', 'cows']);

        if ($user && $user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        } elseif ($regencyId) {
            $query->where('regency_id', $regencyId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($nikStatus === 'without_nik') {
            $query->where(function ($q) {
                $q->whereNull('nik')->orWhere('nik', '');
            });
        } elseif ($nikStatus === 'with_nik') {
            $query->whereNotNull('nik')->where('nik', '!=', '');
        }

        $baseStatQuery = Farmer::query();
        if ($user && $user->isAdminKabupaten()) {
            $baseStatQuery->where('regency_id', $user->regency_id);
        } elseif ($regencyId) {
            $baseStatQuery->where('regency_id', $regencyId);
        }

        $totalFarmersCount = (clone $baseStatQuery)->count();
        $withoutNikCount = (clone $baseStatQuery)->where(fn($q) => $q->whereNull('nik')->orWhere('nik', ''))->count();
        $withNikCount = $totalFarmersCount - $withoutNikCount;

        $farmers = $query->latest()->paginate(12)->withQueryString();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->orderBy('name')->get();

        $districts = ($user && $user->isAdminKabupaten())
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        return view('farmers.index', compact(
            'farmers',
            'regencies',
            'districts',
            'search',
            'nikStatus',
            'regencyId',
            'totalFarmersCount',
            'withoutNikCount',
            'withNikCount'
        ));
    }

    public function searchJson(Request $request)
    {
        $user = auth()->user();
        $q = $request->query('q');

        $query = Farmer::query();
        if (auth()->check()) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'LIKE', "%{$q}%")
                   ->orWhere('nik', 'LIKE', "%{$q}%");
            });
        } else {
            $query->where('name', 'LIKE', "%{$q}%");
        }

        if ($user && $user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        }

        $farmers = $query->limit(15)->get(['id', 'nik', 'name', 'phone']);

        return response()->json($farmers);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $nik = trim((string) $request->input('nik', ''));
        $request->merge(['nik' => $nik !== '' ? $nik : null]);

        $request->validate([
            'nik' => ['nullable', 'digits:16', 'unique:farmers,nik'],
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'village_id' => 'required|exists:villages,id',
            'address' => 'nullable|string',
        ]);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $farmerRegencyId = $village->district->regency_id ?? null;

        if ($user && $user->isAdminKabupaten() && $farmerRegencyId != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat mendaftarkan peternak pada wilayah Kabupaten/Kota Anda sendiri.');
        }

        Farmer::create([
            'nik' => $request->nik,
            'name' => trim($request->name),
            'phone' => $request->phone,
            'address' => $request->address,
            'regency_id' => $farmerRegencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
        ]);

        return back()->with('success', 'Data Pemilik Sapi (Peternak) berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farmer = Farmer::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $farmer->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah data peternak dari wilayah lain.');
        }

        $nik = trim((string) $request->input('nik', ''));
        $request->merge(['nik' => $nik !== '' ? $nik : null]);

        $request->validate([
            'nik' => ['nullable', 'digits:16', 'unique:farmers,nik,' . $id],
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'village_id' => 'required|exists:villages,id',
            'address' => 'nullable|string',
        ]);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $farmerRegencyId = $village->district->regency_id ?? null;

        if ($user && $user->isAdminKabupaten() && $farmerRegencyId != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat memindahkan peternak pada wilayah Kabupaten/Kota Anda sendiri.');
        }

        $farmer->update([
            'nik' => $request->nik,
            'name' => trim($request->name),
            'phone' => $request->phone,
            'address' => $request->address,
            'regency_id' => $farmerRegencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
        ]);

        return back()->with('success', 'Data Pemilik Sapi (Peternak) berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farmer = Farmer::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $farmer->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus peternak dari wilayah lain.');
        }

        $farmer->delete();

        return back()->with('success', 'Data Pemilik Sapi berhasil dihapus.');
    }
}
