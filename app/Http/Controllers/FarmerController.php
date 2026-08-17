<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

/**
 * Controller Manajemen Data Peternak
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi)
 * Mengelola pendaftaran peternak dengan NIK KTP dan Nama Peternak secara terpisah, serta wilayah tempat tinggal.
 */
class FarmerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $query = Farmer::with(['regency', 'district', 'village', 'cows']);

        // Scope isolasi Admin Kabupaten / Kota
        if ($user && $user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $farmers = $query->latest()->paginate(10)->withQueryString();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->get();

        $districts = ($user && $user->isAdminKabupaten())
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        return view('farmers.index', compact('farmers', 'regencies', 'districts', 'search'));
    }

    public function searchJson(Request $request)
    {
        $user = auth()->user();
        $q = $request->query('q');

        $query = Farmer::where(function ($sq) use ($q) {
            $sq->where('name', 'LIKE', "%{$q}%")
               ->orWhere('nik', 'LIKE', "%{$q}%");
        });

        if ($user && $user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        }

        $farmers = $query->limit(15)->get(['id', 'nik', 'name', 'phone']);

        return response()->json($farmers);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nik' => [
                'required',
                'digits:16',
                'unique:farmers,nik',
            ],
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string',
            'village_id' => 'required|exists:villages,id',
            'address' => 'nullable|string',
        ]);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $farmerRegencyId = $village->district->regency_id ?? null;

        if ($user && $user->isAdminKabupaten() && $farmerRegencyId != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat mendaftarkan peternak pada wilayah Kabupaten Anda sendiri.');
        }

        Farmer::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'regency_id' => $farmerRegencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
        ]);

        return back()->with('success', 'Data Peternak berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farmer = Farmer::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $farmer->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus peternak dari wilayah lain.');
        }

        $farmer->delete();
        return back()->with('success', 'Data Peternak berhasil dihapus.');
    }
}
