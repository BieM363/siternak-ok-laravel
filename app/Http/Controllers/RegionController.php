<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

/**
 * Controller Hirarki Wilayah Dinas Peternakan
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi)
 * Mengelola Kabupaten/Kota, Kecamatan, dan Desa/Kelurahan.
 */
class RegionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->isAdminKabupaten()) {
            $regencies = Regency::where('id', $user->regency_id)->with('districts.villages')->orderBy('code')->get();
            $districts = District::where('regency_id', $user->regency_id)->with('villages', 'regency')->orderBy('code')->get();
        } else {
            $regencies = Regency::with('districts.villages')->orderBy('code')->get();
            $districts = District::with('villages', 'regency')->orderBy('code')->get();
        }

        return view('regions.index', compact('regencies', 'districts'));
    }

    public function storeRegency(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->isAdminKabupaten()) {
            return back()->with('error', 'Hanya Admin Provinsi yang dapat menambahkan Kabupaten / Kota baru.');
        }

        $request->validate([
            'code' => 'required|string|unique:regencies,code',
            'name' => 'required|string|max:100',
        ]);

        Regency::create($request->only(['code', 'name']));
        return back()->with('success', 'Kabupaten / Kota baru berhasil ditambahkan.');
    }

    public function storeDistrict(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'required|string|unique:districts,code',
            'name' => 'required|string|max:100',
        ]);

        if ($user && $user->isAdminKabupaten() && $request->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat menambahkan Kecamatan pada Kabupaten Anda sendiri.');
        }

        District::create($request->only(['regency_id', 'code', 'name']));
        return back()->with('success', 'Kecamatan baru berhasil ditambahkan.');
    }

    public function storeVillage(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'code' => 'required|string',
            'name' => 'required|string|max:100',
        ]);

        $district = District::findOrFail($request->district_id);
        if ($user && $user->isAdminKabupaten() && $district->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat menambahkan Desa pada Kabupaten Anda sendiri.');
        }

        Village::create($request->only(['district_id', 'code', 'name']));
        return back()->with('success', 'Desa / Kelurahan baru berhasil ditambahkan.');
    }

    public function updateRegency(Request $request, $id)
    {
        $user = auth()->user();

        if ($user && !$user->isAdminProvinsi()) {
            return back()->with('error', 'Hanya Admin Provinsi yang dapat mengedit data Kabupaten / Kota.');
        }

        $regency = Regency::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:regencies,code,' . $regency->id,
            'name' => 'required|string|max:100',
        ]);

        $regency->update($request->only(['code', 'name']));
        return back()->with('success', "Data {$regency->name} berhasil diperbarui.");
    }

    public function updateDistrict(Request $request, $id)
    {
        $user = auth()->user();
        $district = District::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $district->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat mengedit Kecamatan pada wilayah Anda sendiri.');
        }

        $request->validate([
            'code' => 'required|string|unique:districts,code,' . $district->id,
            'name' => 'required|string|max:100',
        ]);

        $district->update($request->only(['code', 'name']));
        return back()->with('success', "Data Kecamatan {$district->name} berhasil diperbarui.");
    }

    public function updateVillage(Request $request, $id)
    {
        $user = auth()->user();
        $village = Village::with('district')->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $village->district->regency_id != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat mengedit Desa / Kelurahan pada wilayah Anda sendiri.');
        }

        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string|max:100',
        ]);

        $village->update($request->only(['code', 'name']));
        $unitLabel = ($village->district && $village->district->regency && str_contains(strtolower($village->district->regency->name), 'kota')) ? 'Kelurahan' : 'Desa';
        return back()->with('success', "Data {$unitLabel} {$village->name} berhasil diperbarui.");
    }
}
