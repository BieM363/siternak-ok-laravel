<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\Calf;
use App\Models\Regency;
use App\Models\User;
use App\Services\XlsxExportService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PregnancyController extends Controller
{
    /**
     * Menu Khusus: Data Kebuntingan Aktif
     * Terpisah dari daftar semua Sapi IB, menampilkan umur kebuntingan (bulan),
     * identitas pejantan, pemilik (dengan filter NIK), lokasi, dan ekspor Excel (.xlsx).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        // Pastikan status otomatis maju bila batas 3 hari terlewati
        $autoCheck = PregnancyRecord::whereNotIn('status', ['Melahirkan (Selesai)', 'Gagal/Keguguran']);
        if ($isPureOperator) {
            $autoCheck->where('operator_id', $user->id);
        } elseif ($isAdminKabupaten) {
            $autoCheck->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        }
        foreach ($autoCheck->get() as $rec) {
            $rec->checkAndAutoAdvance();
        }

        $filters = $this->extractPregnancyFilters($request, $user);
        $query = $this->buildPregnancyListQuery($filters, $user);

        $pregnancies = (clone $query)
            ->orderBy('insemination_date', 'asc')
            ->paginate(15)
            ->withQueryString();

        $allMatching = (clone $query)->get();
        $totalBunting = $allMatching->count();
        $totalTahapPkb90 = $allMatching->where('status', 'Bunting (PKB 90 Hari)')->count();
        $totalTerkonfirmasi = $allMatching->where('status', 'Siap Melahirkan (245 Hari)')->count();
        $totalBulan9 = $allMatching->where('status', 'Fase Melahirkan')->count();

        $breedSummary = $allMatching
            ->groupBy(fn($r) => $r->straw_rumpun ?: ($r->cow->breed ?? 'Lainnya'))
            ->map(fn($group) => $group->count())
            ->sortDesc();

        $regencies = $isAdminKabupaten
            ? Regency::where('id', $user->regency_id)->get()
            : Regency::orderBy('name')->get();

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

        return view('pregnancies.index', array_merge($filters, compact(
            'pregnancies',
            'totalBunting',
            'totalTahapPkb90',
            'totalTerkonfirmasi',
            'totalBulan9',
            'breedSummary',
            'regencies',
            'officers',
            'breeds'
        )));
    }

    /**
     * Ekspor Data Kebuntingan Aktif ke Excel (.xlsx) mengikuti filter aktif
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $filters = $this->extractPregnancyFilters($request, $user);
        $records = $this->buildPregnancyListQuery($filters, $user)
            ->orderBy('insemination_date', 'asc')
            ->get();

        $isPublic = !auth()->check() || auth()->user()->isGuest();

        $rows = [];
        foreach ($records as $idx => $rec) {
            $cow = $rec->cow;
            $farmer = $cow?->farmer;
            $nikDisplay = 'Tanpa NIK';
            if ($farmer && $farmer->nik) {
                $nikDisplay = $isPublic
                    ? substr($farmer->nik, 0, 6) . '******' . substr($farmer->nik, -4)
                    : $farmer->nik;
            }

            $rows[] = [
                $idx + 1,
                $cow?->cow_nik ?? '-',
                $cow?->name ?? '-',
                $cow?->breed ?? '-',
                'IB ke-' . ($rec->ib_ke ?? 1),
                $rec->insemination_date ? $rec->insemination_date->format('d/m/Y') : '-',
                $rec->created_at ? $rec->created_at->format('d/m/Y') : '-',
                $rec->pregnancy_age_label,
                $rec->straw_rumpun ?: '-',
                $rec->straw_kode_bull ?: ($rec->semen_code ?: '-'),
                $rec->straw_kode_batch ?: '-',
                $farmer?->name ?? '-',
                $nikDisplay,
                $cow?->regency?->name ?? '-',
                $cow?->district?->name ?? '-',
                $cow?->village?->name ?? '-',
                $rec->officer_name ?? '-',
                $rec->estimated_birth_date ? $rec->estimated_birth_date->format('d/m/Y') : '-',
                $rec->status_display_label,
            ];
        }

        $filename = 'Data_Kebuntingan_Sapi_' . date('Ymd_His') . '.xlsx';

        return XlsxExportService::download($filename, [
            [
                'title' => 'Data Kebuntingan',
                'subtitle' => 'SiTernak-OK · Rekap Data Kebuntingan Sapi Aktif (Dicetak: ' . now()->format('d/m/Y H:i') . ' WITA)',
                'headers' => [
                    'No',
                    'NIK Sapi',
                    'Nama Sapi',
                    'Jenis/Ras Sapi',
                    'Urutan IB',
                    'Tanggal Inseminasi',
                    'Tanggal Pelaporan',
                    'Umur Kebuntingan',
                    'Rumpun Pejantan',
                    'Kode Bull',
                    'Kode Batch',
                    'Nama Pemilik',
                    'NIK Pemilik',
                    'Kabupaten/Kota',
                    'Kecamatan',
                    'Desa/Kelurahan',
                    'Inseminator',
                    'Estimasi Lahir (Bln 9)',
                    'Status Kebuntingan',
                ],
                'rows' => $rows,
            ],
        ]);
    }

    private function extractPregnancyFilters(Request $request, $user): array
    {
        $regencyId = $request->query('regency_id');
        if ($user && $user->isAdminKabupaten()) {
            $regencyId = $user->regency_id;
        }

        return [
            'search' => $request->query('search'),
            'regencyId' => $regencyId,
            'officerName' => $request->query('officer_name'),
            'breedFilter' => $request->query('breed'),
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'nikStatus' => $request->query('nik_status'),
            'stageFilter' => $request->query('stage'),
        ];
    }

    private function buildPregnancyListQuery(array $filters, $user)
    {
        $isPureOperator = $user && $user->isOperator() && !$user->isAdmin();
        $isAdminKabupaten = $user && $user->isAdminKabupaten();

        $query = PregnancyRecord::with([
            'cow.farmer',
            'cow.regency',
            'cow.district',
            'cow.village',
            'bullStraw',
        ])->whereHas('cow');

        if (!empty($filters['stageFilter'])) {
            $query->where('status', $filters['stageFilter']);
        } else {
            $query->whereIn('status', PregnancyRecord::ACTIVE_PREGNANCY_STATUSES);
        }

        if ($isPureOperator) {
            $query->where('operator_id', $user->id);
        }

        if ($isAdminKabupaten) {
            $query->whereHas('cow', fn($q) => $q->where('regency_id', $user->regency_id));
        } elseif (!empty($filters['regencyId'])) {
            $query->whereHas('cow', fn($q) => $q->where('regency_id', $filters['regencyId']));
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('straw_kode_bull', 'LIKE', "%{$s}%")
                  ->orWhere('straw_kode_batch', 'LIKE', "%{$s}%")
                  ->orWhere('semen_code', 'LIKE', "%{$s}%")
                  ->orWhereHas('cow', function ($cq) use ($s) {
                      $cq->where('cow_nik', 'LIKE', "%{$s}%")
                         ->orWhere('name', 'LIKE', "%{$s}%")
                         ->orWhereHas('farmer', function ($fq) use ($s) {
                             $fq->where('name', 'LIKE', "%{$s}%")
                                ->orWhere('nik', 'LIKE', "%{$s}%");
                         });
                  });
            });
        }

        if (!empty($filters['officerName']) && !$isPureOperator) {
            $query->where('officer_name', $filters['officerName']);
        }

        if (!empty($filters['breedFilter'])) {
            $b = $filters['breedFilter'];
            $query->where(function ($q) use ($b) {
                $q->where('straw_rumpun', $b)
                  ->orWhereHas('cow', fn($cq) => $cq->where('breed', $b));
            });
        }

        if (!empty($filters['startDate'])) {
            $query->whereDate('insemination_date', '>=', $filters['startDate']);
        }

        if (!empty($filters['endDate'])) {
            $query->whereDate('insemination_date', '<=', $filters['endDate']);
        }

        if ($filters['nikStatus'] === 'without_nik') {
            $query->whereHas('cow.farmer', fn($q) => $q->whereNull('nik')->orWhere('nik', ''));
        } elseif ($filters['nikStatus'] === 'with_nik') {
            $query->whereHas('cow.farmer', fn($q) => $q->whereNotNull('nik')->where('nik', '!=', ''));
        }

        return $query;
    }

    /**
     * Input Inseminasi Buatan (IB Lanjutan: IB ke-2, ke-3, dst. pada Sapi yang Sudah Ada)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->canOperate()) {
            return back()->with('error', 'Akses ditolak: Input Inseminasi Buatan (IB) hanya dapat dilakukan oleh Inseminator atau Admin Dinas.');
        }

        $request->validate([
            'cow_id' => 'required|exists:cows,id',
            'insemination_date' => 'required|date|before_or_equal:today',
            'straw_mode' => 'nullable|in:master,custom',
            'bull_straw_id' => 'nullable|exists:bull_straws,id',
            'semen_code' => 'nullable|string|max:100',
            'straw_rumpun' => 'nullable|string|max:80',
            'straw_kode_bull' => 'nullable|string|max:80',
            'straw_kode_batch' => 'nullable|string|max:80',
            'straw_producer' => 'nullable|string|max:100',
            'operator_id' => 'nullable|exists:users,id',
            'officer_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $cow = Cow::findOrFail($request->cow_id);

        if ($user->isAdminKabupaten() && $cow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak: Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        $ibDate = Carbon::parse($request->insemination_date);
        $nextIbKe = $cow->getNextIbNumber();
        $strawMode = $request->input('straw_mode', 'master');
        $strawData = CowController::resolveStrawPayload($request, $strawMode);

        $operator = null;
        if ($request->filled('operator_id')) {
            $operator = User::find($request->operator_id);
        } elseif ($user->isOperator()) {
            $operator = $user;
        }

        $officerName = trim((string) $request->input('officer_name', ''))
            ?: ($operator?->name ?? $user->name);

        $record = PregnancyRecord::create([
            'cow_id' => $cow->id,
            'operator_id' => $operator?->id ?? ($user->isOperator() ? $user->id : null),
            'ib_ke' => $nextIbKe,
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
            'notes' => $request->notes ?: "Input IB Lanjutan (IB ke-{$nextIbKe}).",
        ]);

        $cow->update(['current_status' => 'Pasca IB']);

        // Jalankan penyesuaian otomatis jika tanggal inseminasi backdate
        $record->checkAndAutoAdvance();

        return redirect()
            ->route('cows.show', $cow->id)
            ->with(
                'success',
                "IB Lanjutan (IB ke-{$nextIbKe}) berhasil dicatat! " .
                'Tahap 1 (30 Hari): ' . $ibDate->copy()->addDays(30)->format('d-m-Y') .
                ' · Tahap 2 (PKB 90 Hari): ' . $ibDate->copy()->addDays(90)->format('d-m-Y') .
                ' · Tahap 3 (Bulan ke-9): ' . $ibDate->copy()->addDays(275)->format('d-m-Y')
            );
    }

    /**
     * Konfirmasi 3 Tahap Kebuntingan dan Kelahiran (Hari 30, Hari 90 / PKB, Bulan ke-9)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();
        $record = PregnancyRecord::with('cow')->findOrFail($id);

        if (!$user || !$user->canOperate()) {
            return back()->with('error', 'Anda tidak memiliki hak untuk melakukan tindakan konfirmasi.');
        }

        if ($user->isAdminKabupaten() && $record->cow && $record->cow->regency_id != $user->regency_id) {
            return back()->with('error', 'Akses ditolak. Sapi ini berada di luar wilayah Kabupaten/Kota Anda.');
        }

        if ($user->isOperator() && !$user->isAdmin() && $record->operator_id !== $user->id) {
            return back()->with('error', 'Akses ditolak. Anda hanya dapat mengonfirmasi sapi yang menjadi tanggung jawab Anda sendiri.');
        }

        $request->validate([
            'action' => 'required|in:confirm_yes,confirm_no,birth_yes,birth_no,direct_update',
            'actual_birth_date' => 'nullable|date|before_or_equal:today',
            'calf_name' => 'nullable|string|max:100',
            'calf_gender' => 'required_if:action,birth_yes|in:Jantan,Betina',
            'calf_breed' => 'nullable|string|max:100',
            'calf_nik' => 'nullable|string|max:50',
            'calf_weight' => 'nullable|numeric|min:0',
            'calf_condition' => 'nullable|in:Sehat,Cacat,Mati',
            'notes' => 'nullable|string',
        ]);

        $cow = $record->cow;
        $now = now();

        // 1. KONFIRMASI YA (Tahap 1 Hari 30 -> Tahap 2 PKB 90 -> Bunting Terkonfirmasi Menuju Bulan ke-9)
        if ($request->action === 'confirm_yes') {
            $nextStatus = match ($record->status) {
                'Pasca IB', 'Estimasi 30 Hari' => 'Bunting (PKB 90 Hari)',
                'Bunting (PKB 90 Hari)' => 'Siap Melahirkan (245 Hari)',
                'Siap Melahirkan (245 Hari)' => 'Fase Melahirkan',
                default => null,
            };

            if (!$nextStatus) {
                return back()->with('error', 'Status kebuntingan sapi saat ini tidak dapat dikonfirmasi pada tahap ini.');
            }

            $oldNotes = $record->notes ?? '';
            $newNotes = $request->notes
                ? trim($oldNotes . "\n[Konfirmasi YA oleh {$user->name}]: " . $request->notes)
                : $oldNotes;

            $record->update([
                'status' => $nextStatus,
                'confirmed_at' => $now,
                'is_auto_advanced' => false,
                'notes' => $newNotes,
            ]);

            $cowStatus = match ($nextStatus) {
                'Bunting (PKB 90 Hari)', 'Siap Melahirkan (245 Hari)' => 'Bunting',
                'Fase Melahirkan' => 'Mendekati Melahirkan',
                default => 'Bunting',
            };

            $cow->update(['current_status' => $cowStatus]);

            return back()->with('success', "Konfirmasi berhasil! Status kebuntingan diperbarui menjadi: {$record->fresh()->status_display_label}.");
        }

        // 2. KONFIRMASI TIDAK / GAGAL (Keluar dari hitungan kebuntingan aktif & siap masuk IB ke-2)
        if ($request->action === 'confirm_no' || $request->action === 'birth_no') {
            $oldNotes = $record->notes ?? '';
            $newNotes = $request->notes
                ? trim($oldNotes . "\n[Konfirmasi TIDAK/Gagal oleh {$user->name}]: " . $request->notes)
                : $oldNotes;

            $record->update([
                'status' => 'Gagal/Keguguran',
                'confirmed_at' => $now,
                'is_auto_advanced' => false,
                'notes' => $newNotes,
            ]);

            $cow->update(['current_status' => 'Tidak Bunting']);

            return back()->with('info', 'Konfirmasi Tidak Bunting / Gagal dicatat. Sapi keluar dari daftar kebuntingan aktif dan siap untuk IB Lanjutan (IB ke-' . $cow->getNextIbNumber() . ').');
        }

        // 3. KONFIRMASI MELAHIRKAN (Tahap 3 Bulan ke-9 -> Otomatis masuk Data Pedet & Kelahiran)
        if ($request->action === 'birth_yes') {
            if (!in_array($record->status, ['Fase Melahirkan', 'Siap Melahirkan (245 Hari)'])) {
                return back()->with('error', 'Konfirmasi kelahiran hanya dapat dicatat pada tahap Bunting Terkonfirmasi atau Bulan ke-9.');
            }

            $birthDate = $request->actual_birth_date ?? Carbon::today()->format('Y-m-d');

            return DB::transaction(function () use ($request, $record, $cow, $now, $birthDate, $user) {
                if ($record->calves()->exists()) {
                    return back()->with('error', 'Data pedet untuk rekam kebuntingan ini sudah tercatat.');
                }

                $calfNik = $request->calf_nik
                    ?: ('PEDET-' . $cow->cow_nik . '-' . Carbon::parse($birthDate)->format('Ymd'));

                $calfCount = Calf::where('cow_id', $cow->id)->count() + 1;
                $calfName = $request->filled('calf_name')
                    ? trim($request->calf_name)
                    : 'Pedet ' . ($cow->name ?? $cow->cow_nik) . ' ' . $calfCount;

                $calfBreed = $request->filled('calf_breed')
                    ? trim($request->calf_breed)
                    : ($record->straw_rumpun ?: ($cow->breed ?? 'Limosin'));

                $record->update([
                    'status' => 'Melahirkan (Selesai)',
                    'actual_birth_date' => $birthDate,
                    'confirmed_at' => $now,
                    'is_auto_advanced' => false,
                    'notes' => $request->notes
                        ? trim(($record->notes ?? '') . "\n[Melahirkan dikonfirmasi {$user->name}]: " . $request->notes)
                        : $record->notes,
                ]);

                Calf::create([
                    'pregnancy_record_id' => $record->id,
                    'cow_id' => $cow->id,
                    'calf_nik' => $calfNik,
                    'name' => $calfName,
                    'breed' => $calfBreed,
                    'gender' => $request->calf_gender,
                    'birth_date' => $birthDate,
                    'birth_weight' => $request->calf_weight,
                    'condition' => $request->calf_condition ?? 'Sehat',
                    'notes' => $request->notes,
                ]);

                $cow->update(['current_status' => 'Pasca Melahirkan']);

                return back()->with('success', "Konfirmasi kelahiran berhasil! Data pedet ({$calfName} · NIK {$calfNik}) otomatis tercatat di menu Pedet & Kelahiran.");
            });
        }

        return back()->with('error', 'Aksi konfirmasi tidak valid.');
    }

    public function triggerCron()
    {
        Artisan::call('ternak:operator-session-cron');
        $sessionCronOutput = trim(Artisan::output());

        Artisan::call('ternak:update-pregnancy-status');
        $output = trim(Artisan::output());

        return back()->with(
            'success',
            'Sinkronisasi otomatis 3 tahap kebuntingan & batas 3 hari selesai dijalankan. ' .
            $output . ($sessionCronOutput ? ' | ' . $sessionCronOutput : '')
        );
    }
}
