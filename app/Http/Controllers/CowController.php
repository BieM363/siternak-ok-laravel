<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cow;
use App\Models\Farmer;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\PregnancyRecord;
use App\Models\User;
use App\Models\BullStraw;
use App\Services\CattleNikGenerator;
use Carbon\Carbon;

class CowController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');

        $query = Cow::forOperator($user)
            ->with([
                'farmer',
                'regency',
                'district',
                'village',
                'latestPregnancy',
            ]);

        if ($search) {
            $query->where(function ($sq) use ($search) {
                $sq->where('cow_nik', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('farmer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                    });
            });
        }

        $cows = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('cows.index', compact('cows', 'search'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $user = auth()->user();

        if (!$user || !$user->isOperator()) {
            return redirect()->route('dashboard')->with('error', 'Akses dibatasi: Pendaftaran Sapi & Inseminasi Buatan (IB) Baru hanya dapat dilakukan oleh Petugas Inseminator / Operator Lapangan.');
        }

        $farmers = ($user && $user->isAdminKabupaten())
            ? Farmer::where('regency_id', $user->regency_id)->orderBy('name')->get()
            : Farmer::orderBy('name')->get();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->get();

        $districts = ($user && $user->isAdminKabupaten())
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        $operators = ($user && $user->isAdminKabupaten())
            ? User::where('role', 'operator')->where('regency_id', $user->regency_id)->orderBy('name')->get()
            : User::where('role', 'operator')->orderBy('name')->get();

        $straws = BullStraw::where('is_active', true)->orderBy('code')->get();

        return view('cows.create', compact(
            'farmers',
            'regencies',
            'districts',
            'operators',
            'straws'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE NIK
    |--------------------------------------------------------------------------
    */

    public function getAutoNik(Request $request)
    {
        $villageId = $request->query('village_id');

        if (!$villageId) {
            return response()->json([
                'error' => 'ID Desa/Kelurahan diperlukan.'
            ], 400);
        }

        $village = Village::find($villageId);

        if (!$village) {
            return response()->json([
                'error' => 'Desa/Kelurahan tidak ditemukan.'
            ], 404);
        }

        $nik = CattleNikGenerator::generate((int) $village->id);

        return response()->json([
            'nik' => $nik
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isOperator()) {
            return redirect()->route('dashboard')->with('error', 'Akses dibatasi: Pendaftaran Sapi & Inseminasi Buatan (IB) Baru hanya dapat dilakukan oleh Petugas Inseminator / Operator Lapangan.');
        }

        $request->validate([
            'farmer_id' => ['required', 'exists:farmers,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'breed' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'insemination_date' => ['nullable', 'date'],
            'semen_code' => ['nullable', 'string', 'max:50'],
            'operator_id' => ['required_with:insemination_date', 'nullable', 'exists:users,id'],
            'officer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $regencyId = $village->district->regency_id ?? null;

        if ($user && $user->isAdminKabupaten() && $regencyId != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat mendaftarkan sapi di wilayah Kabupaten Anda sendiri.');
        }

        $operator = null;
        if ($request->filled('operator_id')) {
            $operator = User::findOrFail($request->operator_id);
            if (!$operator->isOperator()) {
                return back()->withInput()->withErrors([
                    'operator_id' => 'User yang dipilih bukan merupakan Operator.'
                ]);
            }
        } elseif ($user && $user->isOperator()) {
            // Auto default operator_id ke user yang sedang login jika user adalah operator
            $operator = $user;
            $request->merge(['operator_id' => $user->id]);
        }

        $cowNik = CattleNikGenerator::generate($village->id);
        $currentStatus = $request->filled('insemination_date') ? 'Pasca IB' : 'Tidak Bunting';

        $cow = Cow::create([
            'cow_nik' => $cowNik,
            'name' => $request->name,
            'breed' => $request->breed,
            'birth_date' => $request->birth_date,
            'farmer_id' => $request->farmer_id,
            'regency_id' => $regencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
            'current_status' => $currentStatus,
        ]);

        if ($request->filled('insemination_date')) {
            $ibDate = Carbon::parse($request->insemination_date);
            $officerName = $request->filled('officer_name')
                ? $request->officer_name
                : ($operator?->name ?? $user->name);

            PregnancyRecord::create([
                'cow_id' => $cow->id,
                'operator_id' => $request->operator_id ?: ($user->isOperator() ? $user->id : null),
                'insemination_date' => $ibDate->format('Y-m-d'),
                'semen_code' => $request->semen_code,
                'day30_date' => $ibDate->copy()->addDays(30)->format('Y-m-d'),
                'day90_date' => $ibDate->copy()->addDays(90)->format('Y-m-d'),
                'day245_date' => $ibDate->copy()->addDays(245)->format('Y-m-d'),
                'estimated_birth_date' => $ibDate->copy()->addDays(283)->format('Y-m-d'),
                'birth_window_end_date' => $ibDate->copy()->addDays(333)->format('Y-m-d'),
                'status' => 'Pasca IB',
                'officer_name' => $officerName,
                'notes' => $request->notes ?? 'Pendaftaran sapi betina baru beserta rekam Inseminasi Buatan (IB).',
            ]);
        }

        return redirect()
            ->route('cows.show', $cow->id)
            ->with('success', "Sapi betina berhasil didaftarkan dengan NIK Otomatis: {$cowNik}");
    }

    public function show($id)
    {
        $user = auth()->user();
        $cow = Cow::with([
            'farmer',
            'regency',
            'district',
            'village',
            'pregnancyRecords.calf',
            'calves',
        ])->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten Anda.');
        }

        if ($user && $user->isOperator() && !$user->isAdmin()) {
            $hasAccess = $cow->pregnancyRecords()->where('operator_id', $user->id)->exists();
            if (!$hasAccess) {
                return redirect()->route('cows.index')->with('error', 'Akses ditolak. Anda hanya dapat melihat sapi yang terkait dengan rekam IB Anda.');
            }
        }

        $straws = BullStraw::where('is_active', true)->orderBy('code')->get();

        $inseminators = User::where('role', 'operator')->orderBy('name')->get();

        return view('cows.show', compact('cow', 'straws', 'inseminators'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $cow = Cow::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten Anda.');
        }

        $farmers = ($user && $user->isAdminKabupaten())
            ? Farmer::where('regency_id', $user->regency_id)->orderBy('name')->get()
            : Farmer::orderBy('name')->get();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->get();

        $districts = ($user && $user->isAdminKabupaten())
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        $straws = BullStraw::where('is_active', true)->orderBy('code')->get();

        return view('cows.edit', compact('cow', 'farmers', 'regencies', 'districts', 'straws'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $cow = Cow::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten Anda.');
        }

        $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'breed' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'farmer_id' => ['required', 'exists:farmers,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'current_status' => ['required', 'string'],
        ]);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $regencyId = $village->district->regency_id ?? null;

        if ($user && $user->isAdminKabupaten() && $regencyId != $user->regency_id) {
            return back()->with('error', 'Anda hanya dapat menempatkan sapi di wilayah Kabupaten Anda sendiri.');
        }

        $cow->update([
            'name' => $request->name,
            'breed' => $request->breed,
            'birth_date' => $request->birth_date,
            'farmer_id' => $request->farmer_id,
            'regency_id' => $regencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
            'current_status' => $request->current_status,
        ]);

        return redirect()->route('cows.show', $cow->id)->with('success', 'Data sapi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $cow = Cow::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten Anda.');
        }

        $cow->delete();

        return redirect()->route('cows.index')->with('success', 'Data sapi telah dihapus.');
    }
}