<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cow;
use App\Models\PregnancyRecord;
use App\Models\Calf;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PregnancyController extends Controller
{
    /**
     * Pendaftaran Suntik Inseminasi Buatan (IB)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'cow_id' => 'required|exists:cows,id',
            'insemination_date' => 'required|date',
            'semen_code' => 'nullable|string|max:50',
            'officer_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $ibDate = Carbon::parse($request->insemination_date);

        $record = PregnancyRecord::create([
            'cow_id' => $request->cow_id,
            'operator_id' => $user->id,
            'insemination_date' => $ibDate->format('Y-m-d'),

            'semen_code' => $request->semen_code,

            'day30_date' => $ibDate->copy()
                ->addDays(30)
                ->format('Y-m-d'),

            'day90_date' => $ibDate->copy()
                ->addDays(90)
                ->format('Y-m-d'),

            'day245_date' => $ibDate->copy()
                ->addDays(245)
                ->format('Y-m-d'),

            'estimated_birth_date' => $ibDate->copy()
                ->addDays(283)
                ->format('Y-m-d'),

            'birth_window_end_date' => $ibDate->copy()
                ->addDays(333)
                ->format('Y-m-d'),

            'status' => 'Pasca IB',

            'officer_name' => $request->officer_name ?? $user->name,

            'notes' => $request->notes,
        ]);

        Cow::where('id', $request->cow_id)
            ->update([
                'current_status' => 'Pasca IB'
            ]);

        return back()->with(
            'success',
            'Pendaftaran Suntik IB berhasil! ' .
            'Estimasi 30 Hari: ' .
            $ibDate->copy()->addDays(30)->format('d-m-Y') .
            ', PKB 90 Hari: ' .
            $ibDate->copy()->addDays(90)->format('d-m-Y') .
            ', HPL: ' .
            $ibDate->copy()->addDays(283)->format('d-m-Y')
        );
    }

    /**
     * Konfirmasi tahapan kebuntingan dan kelahiran.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();

        $record = PregnancyRecord::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Proteksi Operator
        |--------------------------------------------------------------------------
        */

        if (
            $user &&
            $user->isOperator() &&
            !$user->isAdmin() &&
            $record->operator_id !== $user->id
        ) {
            return back()->with(
                'error',
                'Akses ditolak. Anda hanya dapat mengonfirmasi sapi IB milik Anda sendiri.'
            );
        }

        $request->validate([
            'action' => 'required|in:confirm_yes,confirm_no,birth_yes,birth_no,direct_update',
            'actual_birth_date' => 'nullable|date',
            'calf_gender' => 'required_if:action,birth_yes|in:Jantan,Betina',
            'calf_nik' => 'nullable|string|max:50',
            'calf_weight' => 'nullable|numeric|min:0',
            'calf_condition' => 'nullable|in:Sehat,Cacat,Mati',
            'notes' => 'nullable|string',
        ]);

        $cow = $record->cow;
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI YA
        |--------------------------------------------------------------------------
        |
        | Alurnya:
        |
        | Pasca IB
        |     ↓
        | Estimasi 30 Hari
        |     ↓
        | Bunting (PKB 90 Hari)
        |     ↓
        | Siap Melahirkan (245 Hari)
        |     ↓
        | Fase Melahirkan
        |
        */

        if ($request->action === 'confirm_yes') {

            $nextStatus = match ($record->status) {

                'Pasca IB'
                    => 'Bunting (PKB 90 Hari)',

                'Estimasi 30 Hari'
                    => 'Bunting (PKB 90 Hari)',

                'Bunting (PKB 90 Hari)'
                    => 'Siap Melahirkan (245 Hari)',

                'Siap Melahirkan (245 Hari)'
                    => 'Fase Melahirkan',

                default
                    => null,
            };

            if (!$nextStatus) {
                return back()->with(
                    'error',
                    'Status sapi saat ini tidak dapat dikonfirmasi pada tahap ini.'
                );
            }

            $oldNotes = $record->notes ?? '';

            $newNotes = $request->notes
                ? trim(
                    $oldNotes .
                    "\n[Konfirmasi YA]: " .
                    $request->notes
                )
                : $oldNotes;

            $record->update([
                'status' => $nextStatus,
                'confirmed_at' => $now,
                'is_auto_advanced' => false,
                'notes' => $newNotes,
            ]);

            $cowStatus = match ($nextStatus) {

                'Bunting (PKB 90 Hari)'
                    => 'Bunting',

                'Siap Melahirkan (245 Hari)'
                    => 'Siap Melahirkan',

                'Fase Melahirkan'
                    => 'Mendekati Melahirkan',

                default
                    => 'Bunting',
            };

            $cow->update([
                'current_status' => $cowStatus
            ]);

            return back()->with(
                'success',
                "Konfirmasi YA berhasil! Sapi dimajukan ke fase: {$nextStatus}."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI TIDAK
        |--------------------------------------------------------------------------
        */

        if (
            $request->action === 'confirm_no' ||
            $request->action === 'birth_no'
        ) {

            $oldNotes = $record->notes ?? '';

            $newNotes = $request->notes
                ? trim(
                    $oldNotes .
                    "\n[Konfirmasi TIDAK]: " .
                    $request->notes
                )
                : $oldNotes;

            $record->update([
                'status' => 'Gagal/Keguguran',
                'confirmed_at' => $now,
                'is_auto_advanced' => false,
                'notes' => $newNotes,
            ]);

            $cow->update([
                'current_status' => 'Tidak Bunting'
            ]);

            return back()->with(
                'info',
                'Konfirmasi TIDAK dicatat. Status kebuntingan menjadi Gagal/Keguguran.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KONFIRMASI MELAHIRKAN + DATA ANAK
        |--------------------------------------------------------------------------
        */

        if ($request->action === 'birth_yes') {

            if ($record->status !== 'Fase Melahirkan') {
                return back()->with(
                    'error',
                    'Data kelahiran hanya dapat dicatat pada Fase Melahirkan.'
                );
            }

            $birthDate = $request->actual_birth_date
                ?? Carbon::today()->format('Y-m-d');

            return DB::transaction(function () use (
                $request,
                $record,
                $cow,
                $now,
                $birthDate
            ) {

                /*
                |--------------------------------------------------------------------------
                | Cegah pencatatan anak ganda
                |--------------------------------------------------------------------------
                */

                if ($record->calves()->exists()) {
                    return back()->with(
                        'error',
                        'Data anak sapi untuk rekam kebuntingan ini sudah tercatat.'
                    );
                }

                $calfNik = $request->calf_nik
                    ?? (
                        'PEDET-' .
                        $cow->cow_nik .
                        '-' .
                        Carbon::parse($birthDate)->format('Ymd')
                    );

                $record->update([
                    'status' => 'Melahirkan (Selesai)',
                    'actual_birth_date' => $birthDate,
                    'confirmed_at' => $now,
                    'is_auto_advanced' => false,
                    'notes' => $request->notes
                        ? trim(
                            ($record->notes ?? '') .
                            "\n[Melahirkan]: " .
                            $request->notes
                        )
                        : $record->notes,
                ]);

                Calf::create([
                    'pregnancy_record_id' => $record->id,
                    'cow_id' => $cow->id,
                    'calf_nik' => $calfNik,
                    'name' => 'Anak Sapi NIK ' . $cow->cow_nik,
                    'breed' => $cow->breed ?? 'Limosin',
                    'gender' => $request->calf_gender,
                    'birth_date' => $birthDate,
                    'birth_weight' => $request->calf_weight,
                    'condition' => $request->calf_condition ?? 'Sehat',
                    'notes' => $request->notes,
                ]);

                $cow->update([
                    'current_status' => 'Pasca Melahirkan'
                ]);

                return back()->with(
                    'success',
                    "Konfirmasi melahirkan berhasil! Data anak sapi dengan NIK {$calfNik} telah tersimpan."
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE LANGSUNG
        |--------------------------------------------------------------------------
        */

        if ($request->action === 'direct_update') {

            return back()->with(
                'info',
                'Pembaruan langsung belum digunakan dalam alur otomatisasi.'
            );
        }

        return back()->with(
            'error',
            'Aksi konfirmasi tidak valid.'
        );
    }

    /**
     * Jalankan otomatisasi status secara manual.
     *
     * Command yang dijalankan:
     * php artisan ternak:update-pregnancy-status
     */
    public function triggerCron()
    {
        Artisan::call('ternak:update-pregnancy-status');

        $output = Artisan::output();

        return back()->with(
            'success',
            'Otomatisasi status berhasil dijalankan. ' .
            trim($output)
        );
    }
}