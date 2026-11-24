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
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->query('search');
        $nikStatus = $request->query('nik_status');
        $regencyId = $request->query('regency_id');
        $breedFilter = $request->query('breed');

        if ($user && $user->isAdminKabupaten()) {
            $regencyId = $user->regency_id;
        }

        $query = Cow::forOperator($user)
            ->with([
                'farmer',
                'regency',
                'district',
                'village',
                'latestPregnancy',
                'pregnancyRecords',
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

        if ($regencyId && !($user && $user->isAdminKabupaten())) {
            $query->where('regency_id', $regencyId);
        }

        if ($breedFilter) {
            $query->where('breed', $breedFilter);
        }

        if ($nikStatus === 'without_nik') {
            $query->whereHas('farmer', function ($q) {
                $q->whereNull('nik')->orWhere('nik', '');
            });
        } elseif ($nikStatus === 'with_nik') {
            $query->whereHas('farmer', function ($q) {
                $q->whereNotNull('nik')->where('nik', '!=', '');
            });
        }

        $cows = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::orderBy('name')->get();

        $breeds = Cow::BREEDS;

        return view('cows.index', compact(
            'cows',
            'search',
            'nikStatus',
            'regencyId',
            'breedFilter',
            'regencies',
            'breeds'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->canOperate()) {
            return redirect()->route('dashboard')->with('error', 'Akses dibatasi: Input laporan IB hanya dapat dilakukan oleh Inseminator atau Admin Dinas.');
        }

        $isAdminKab = $user->isAdminKabupaten();

        // Inseminator bebas pilih wilayah (lintas kabupaten); Admin Kab/Kota terkunci pada kabupatennya
        $farmers = $isAdminKab
            ? Farmer::where('regency_id', $user->regency_id)->orderBy('name')->get()
            : Farmer::orderBy('name')->get();

        $regencies = $isAdminKab
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->orderBy('name')->get();

        $districts = $isAdminKab
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        $operators = $isAdminKab
            ? User::where('role', 'operator')->where('regency_id', $user->regency_id)->orderBy('name')->get()
            : User::where('role', 'operator')->orderBy('name')->get();

        $straws = BullStraw::where('is_active', true)->orderBy('breed')->orderBy('kode_bull')->get();
        $breeds = Cow::BREEDS;

        // Opsi IB Lanjutan (IB ke-2, dst.) jika datang dari tombol "Input IB Lanjutan"
        $selectedCow = null;
        if ($request->filled('cow_id')) {
            $candidate = Cow::with(['farmer', 'regency', 'district', 'village', 'pregnancyRecords'])->find($request->query('cow_id'));
            if ($candidate) {
                if (!$isAdminKab || $candidate->regency_id == $user->regency_id) {
                    $selectedCow = $candidate;
                }
            }
        }

        return view('cows.create', compact(
            'farmers',
            'regencies',
            'districts',
            'operators',
            'straws',
            'breeds',
            'selectedCow'
        ));
    }

    public function getAutoNik(Request $request)
    {
        $villageId = $request->query('village_id');

        if (!$villageId) {
            return response()->json(['error' => 'ID Desa/Kelurahan diperlukan.'], 400);
        }

        $village = Village::find($villageId);

        if (!$village) {
            return response()->json(['error' => 'Desa/Kelurahan tidak ditemukan.'], 404);
        }

        $nik = CattleNikGenerator::generate((int) $village->id);

        return response()->json(['nik' => $nik]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->canOperate()) {
            return redirect()->route('dashboard')->with('error', 'Akses dibatasi: Input laporan IB hanya dapat dilakukan oleh Inseminator atau Admin Dinas.');
        }

        $farmerMode = $request->input('farmer_mode', 'existing');
        $strawMode = $request->input('straw_mode', 'master');

        $rules = [
            'farmer_mode' => ['nullable', 'in:existing,custom'],
            'village_id' => ['required', 'exists:villages,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'breed' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'insemination_date' => ['nullable', 'date', 'before_or_equal:today'],
            'straw_mode' => ['nullable', 'in:master,custom'],
            'bull_straw_id' => ['nullable', 'exists:bull_straws,id'],
            'semen_code' => ['nullable', 'string', 'max:100'],
            'straw_rumpun' => ['nullable', 'string', 'max:80'],
            'straw_kode_bull' => ['nullable', 'string', 'max:80'],
            'straw_kode_batch' => ['nullable', 'string', 'max:80'],
            'straw_producer' => ['nullable', 'string', 'max:100'],
            'operator_id' => ['nullable', 'exists:users,id'],
            'officer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];

        if ($farmerMode === 'custom') {
            $rules['custom_farmer_name'] = ['required', 'string', 'max:100'];
            $rules['custom_farmer_nik'] = ['nullable', 'digits:16', 'unique:farmers,nik'];
            $rules['custom_farmer_phone'] = ['nullable', 'string', 'max:30'];
        } else {
            $rules['farmer_id'] = ['required', 'exists:farmers,id'];
        }

        $request->validate($rules);

        $village = Village::with('district.regency')->findOrFail($request->village_id);
        $regencyId = $village->district->regency_id ?? null;

        // Admin Kab/Kota tetap terkunci pada kabupatennya; Inseminator bebas lintas kabupaten
        if ($user->isAdminKabupaten() && $regencyId != $user->regency_id) {
            return back()->withInput()->with('error', 'Admin Kabupaten/Kota hanya dapat mendaftarkan data di wilayah Kabupaten/Kota sendiri.');
        }

        // 1. Resolve Pemilik Sapi (Existing atau Custom baru dengan NIK opsional)
        if ($farmerMode === 'custom') {
            $customNik = trim((string) $request->input('custom_farmer_nik', ''));
            $farmer = Farmer::create([
                'nik' => $customNik !== '' ? $customNik : null,
                'name' => trim($request->input('custom_farmer_name')),
                'phone' => trim((string) $request->input('custom_farmer_phone', '')) ?: null,
                'address' => ($village->district->regency && str_contains(strtolower($village->district->regency->name), 'kota') ? 'Kel. ' : 'Desa ') . $village->name,
                'regency_id' => $regencyId,
                'district_id' => $village->district_id,
                'village_id' => $village->id,
            ]);
        } else {
            $farmer = Farmer::findOrFail($request->farmer_id);
        }

        // 2. Resolve Inseminator
        $operator = null;
        if ($request->filled('operator_id')) {
            $operator = User::find($request->operator_id);
        } elseif ($user->isOperator()) {
            $operator = $user;
        }

        // 3. Auto-generate Nama Panggilan Sapi jika dikosongkan -> "[Nama Pemilik] 1", "[Nama Pemilik] 2", dst.
        $cowName = trim((string) $request->input('name', ''));
        if ($cowName === '') {
            $cowName = Cow::generateDefaultName($farmer);
        }

        $cowNik = CattleNikGenerator::generate($village->id);
        $currentStatus = $request->filled('insemination_date') ? 'Pasca IB' : 'Tidak Bunting';

        $cow = Cow::create([
            'cow_nik' => $cowNik,
            'name' => $cowName,
            'breed' => $request->breed,
            'birth_date' => $request->birth_date,
            'farmer_id' => $farmer->id,
            'regency_id' => $regencyId,
            'district_id' => $village->district_id,
            'village_id' => $village->id,
            'current_status' => $currentStatus,
        ]);

        // 4. Buat Rekam Inseminasi Buatan (IB ke-1) jika Tanggal Inseminasi diisi
        if ($request->filled('insemination_date')) {
            $ibDate = Carbon::parse($request->insemination_date);
            $officerName = trim((string) $request->input('officer_name', ''))
                ?: ($operator?->name ?? $user->name);

            $strawData = self::resolveStrawPayload($request, $strawMode);

            $record = PregnancyRecord::create([
                'cow_id' => $cow->id,
                'operator_id' => $operator?->id ?? ($user->isOperator() ? $user->id : null),
                'ib_ke' => 1,
                'insemination_date' => $ibDate->format('Y-m-d'),
                'semen_code' => $strawData['semen_code'],
                'bull_straw_id' => $strawData['bull_straw_id'],
                'straw_rumpun' => $strawData['straw_rumpun'],
                'straw_kode_bull' => $strawData['straw_kode_bull'],
                'straw_kode_batch' => $strawData['straw_kode_batch'],
                'straw_producer' => $strawData['straw_producer'],
                'is_custom_straw' => $strawData['is_custom_straw'],
                'day30_date' => $ibDate->copy()->addDays(30)->format('Y-m-d'),
                'day90_date' => $ibDate->copy()->addDays(90)->format('Y-m-d'),
                'day245_date' => $ibDate->copy()->addDays(245)->format('Y-m-d'),
                'day270_date' => $ibDate->copy()->addDays(270)->format('Y-m-d'),
                'estimated_birth_date' => $ibDate->copy()->addDays(280)->format('Y-m-d'),
                'birth_window_end_date' => $ibDate->copy()->addDays(330)->format('Y-m-d'),
                'status' => 'Pasca IB',
                'officer_name' => $officerName,
                'notes' => $request->notes ?: 'Pendaftaran sapi betina baru beserta laporan Inseminasi Buatan (IB ke-1).',
            ]);

            // Jika input backdate, otomatis sesuaikan tahap berdasarkan tanggal_inseminasi
            $record->checkAndAutoAdvance();
        }

        if ($user->isOperator() && !$user->isAdmin() && $request->input('redirect_to') === 'dashboard') {
            return redirect()
                ->route('dashboard')
                ->with('success', "Laporan IB berhasil tersimpan! Sapi: {$cow->name} ({$cowNik}) · Pemilik: {$farmer->name}.");
        }

        return redirect()
            ->route('cows.show', $cow->id)
            ->with('success', "Sapi betina '{$cow->name}' berhasil didaftarkan dengan NIK Otomatis: {$cowNik}");
    }

    public static function resolveStrawPayload(Request $request, string $strawMode = 'master'): array
    {
        $bullStraw = null;

        if ($strawMode === 'master') {
            if ($request->filled('bull_straw_id')) {
                $bullStraw = BullStraw::find($request->bull_straw_id);
            } elseif ($request->filled('semen_code')) {
                $bullStraw = BullStraw::where('code', $request->semen_code)
                    ->orWhere('kode_bull', $request->semen_code)
                    ->first();
            }
        }

        if ($bullStraw && $strawMode !== 'custom') {
            $kodeBull = $bullStraw->effective_kode_bull;
            $kodeBatch = $bullStraw->kode_batch;
            $semenCode = $bullStraw->code ?: trim($kodeBull . ($kodeBatch ? " / {$kodeBatch}" : ''));

            return [
                'bull_straw_id' => $bullStraw->id,
                'straw_rumpun' => $bullStraw->breed,
                'straw_kode_bull' => $kodeBull,
                'straw_kode_batch' => $kodeBatch,
                'straw_producer' => $bullStraw->producer,
                'semen_code' => $semenCode,
                'is_custom_straw' => false,
            ];
        }

        $rumpun = trim((string) $request->input('straw_rumpun', '')) ?: null;
        $kodeBull = trim((string) $request->input('straw_kode_bull', '')) ?: trim((string) $request->input('semen_code', '')) ?: null;
        $kodeBatch = trim((string) $request->input('straw_kode_batch', '')) ?: null;
        $producer = trim((string) $request->input('straw_producer', '')) ?: null;

        $semenCode = trim((string) $request->input('semen_code', ''));
        if ($semenCode === '') {
            $semenCode = trim(implode(' / ', array_filter([$kodeBull, $kodeBatch]))) ?: null;
        }

        return [
            'bull_straw_id' => null,
            'straw_rumpun' => $rumpun,
            'straw_kode_bull' => $kodeBull,
            'straw_kode_batch' => $kodeBatch,
            'straw_producer' => $producer,
            'semen_code' => $semenCode,
            'is_custom_straw' => $strawMode === 'custom' || (!$bullStraw && ($rumpun || $kodeBull || $kodeBatch)),
        ];
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
            'pregnancyRecords.bullStraw',
            'calves',
        ])->findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        if ($user && $user->isOperator() && !$user->isAdmin()) {
            $hasAccess = $cow->pregnancyRecords()->where('operator_id', $user->id)->exists();
            if (!$hasAccess) {
                return redirect()->route('cows.index')->with('error', 'Akses ditolak. Anda hanya dapat melihat sapi yang terkait dengan laporan IB Anda.');
            }
        }

        $straws = BullStraw::where('is_active', true)->orderBy('breed')->orderBy('kode_bull')->get();
        $inseminators = ($user && $user->isAdminKabupaten())
            ? User::where('role', 'operator')->where('regency_id', $user->regency_id)->orderBy('name')->get()
            : User::where('role', 'operator')->orderBy('name')->get();
        $breeds = Cow::BREEDS;

        return view('cows.show', compact('cow', 'straws', 'inseminators', 'breeds'));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $cow = Cow::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $farmers = ($user && $user->isAdminKabupaten())
            ? Farmer::where('regency_id', $user->regency_id)->orderBy('name')->get()
            : Farmer::orderBy('name')->get();

        $regencies = ($user && $user->isAdminKabupaten())
            ? Regency::where('id', $user->regency_id)->with('districts.villages')->get()
            : Regency::with('districts.villages')->orderBy('name')->get();

        $districts = ($user && $user->isAdminKabupaten())
            ? District::where('regency_id', $user->regency_id)->with('villages')->get()
            : District::with('villages')->get();

        $straws = BullStraw::where('is_active', true)->orderBy('breed')->orderBy('kode_bull')->get();
        $breeds = Cow::BREEDS;

        return view('cows.edit', compact('cow', 'farmers', 'regencies', 'districts', 'straws', 'breeds'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $cow = Cow::findOrFail($id);

        if ($user && $user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
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
            return back()->with('error', 'Anda hanya dapat menempatkan sapi di wilayah Kabupaten/Kota Anda sendiri.');
        }

        $farmer = Farmer::findOrFail($request->farmer_id);
        $cowName = trim((string) $request->input('name', '')) ?: Cow::generateDefaultName($farmer);

        $cow->update([
            'name' => $cowName,
            'breed' => $request->breed,
            'birth_date' => $request->birth_date,
            'farmer_id' => $farmer->id,
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
            return redirect()->route('cows.index')->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $cow->delete();

        return redirect()->route('cows.index')->with('success', 'Data sapi telah dihapus.');
    }
}
