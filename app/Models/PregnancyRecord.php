<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class PregnancyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'cow_id',
        'operator_id',
        'insemination_date',
        'semen_code',
        'day30_date',
        'day90_date',
        'day245_date',
        'estimated_birth_date',
        'birth_window_end_date',
        'actual_birth_date',
        'status',
        'officer_name',
        'last_reminder_at',
        'confirmed_at',
        'is_auto_advanced',
        'notes',
    ];

    protected $casts = [
        'insemination_date' => 'date',
        'day30_date' => 'date',
        'day90_date' => 'date',
        'day245_date' => 'date',
        'estimated_birth_date' => 'date',
        'birth_window_end_date' => 'date',
        'actual_birth_date' => 'date',
        'confirmed_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'is_auto_advanced' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function cow(): BelongsTo
    {
        return $this->belongsTo(Cow::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function calves(): HasMany
    {
        return $this->hasMany(Calf::class);
    }

    public function calf(): HasOne
    {
        return $this->hasOne(Calf::class)->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | SISA HARI MENUJU HPL
    |--------------------------------------------------------------------------
    */

    public function getDaysRemainingAttribute(): int
    {
        if (
            $this->actual_birth_date ||
            in_array($this->status, [
                'Melahirkan (Selesai)',
                'Gagal/Keguguran'
            ])
        ) {
            return 0;
        }

        if (!$this->estimated_birth_date) {
            return 0;
        }

        return (int) Carbon::today()
            ->diffInDays($this->estimated_birth_date, false);
    }

    /*
    |--------------------------------------------------------------------------
    | GOOGLE CALENDAR
    |--------------------------------------------------------------------------
    */

    public function getGoogleCalendarUrl(?string $milestoneType = null): string
    {
        $targetDate = match ($milestoneType) {
            '30day' => $this->day30_date,
            '90day' => $this->day90_date,
            '245day' => $this->day245_date,
            'birth' => $this->estimated_birth_date,
            default => $this->estimated_birth_date,
        };

        if (!$targetDate) {
            return '#';
        }

        $titlePrefix = match ($milestoneType) {
            '30day' => 'Konfirmasi 30 Hari Pasca IB',
            '90day' => 'Konfirmasi PKB 90 Hari',
            '245day' => 'Fase 245 Hari Siap Melahirkan',
            'birth' => 'HPL Perkiraan Melahirkan (283 Hari)',
            default => 'Reminder Kebuntingan Sapi',
        };

        $cowNik = $this->cow->cow_nik ?? 'Sapi';
        $cowName = $this->cow->name ?? 'Induk Sapi';
        $farmerName = $this->cow->farmer->name ?? '-';
        $farmerNik = $this->cow->farmer->nik ?? '-';

        $location = trim(
            ($this->cow->regency->name ?? '') .
            ', Kec. ' .
            ($this->cow->district->name ?? '') .
            ', Desa ' .
            ($this->cow->village->name ?? ''),
            ', '
        );

        $title = "[SiTernak-OK] {$titlePrefix} - NIK Sapi: {$cowNik}";

        $details =
            "PEMANTAUAN KEHAMILAN SAPI - SiTernak-OK\n" .
            "NIK Sapi: {$cowNik} ({$cowName})\n" .
            "Peternak: {$farmerName} (NIK: {$farmerNik})\n" .
            "Tanggal Inseminasi (IB): " .
                ($this->insemination_date
                    ? $this->insemination_date->format('d/m/Y')
                    : '-') . "\n" .
            "Kode Straw Pejantan: " . ($this->semen_code ?? '-') . "\n" .
            "Petugas Inseminator: " . ($this->officer_name ?? '-') . "\n" .
            "Status: {$this->status}\n" .
            "Lokasi: {$location}";

        $dateFormatted = Carbon::parse($targetDate)->format('Ymd');

        $baseUrl = 'https://calendar.google.com/calendar/render';

        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => "{$dateFormatted}/{$dateFormatted}",
            'details' => $details,
            'location' => $location,
        ];

        return $baseUrl . '?' . http_build_query($params);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO ADVANCE
    |--------------------------------------------------------------------------
    |
    | Aturan:
    |
    | 1. Pasca IB
    |    Hari 30 + 3 hari tanpa konfirmasi
    |    -> Bunting (PKB 90 Hari)
    |
    | 2. Bunting PKB 90 Hari
    |    Hari 90 + 3 hari tanpa konfirmasi
    |    -> Siap Melahirkan (245 Hari)
    |
    | 3. Siap Melahirkan
    |    Hari 245 + 3 hari tanpa konfirmasi
    |    -> Fase Melahirkan
    |
    */

    public function checkAndAutoAdvance(): bool
    {
        if (in_array($this->status, [
            'Melahirkan (Selesai)',
            'Gagal/Keguguran'
        ])) {
            return false;
        }

        $today = Carbon::today();
        $changed = false;

        /*
        |--------------------------------------------------------------------------
        | 1. PASCA IB -> BUNTING
        |--------------------------------------------------------------------------
        */

        if (
            in_array($this->status, [
                'Pasca IB',
                'Estimasi 30 Hari'
            ]) &&
            $this->day30_date
        ) {
            $deadline = $this->day30_date->copy()->addDays(3);

            if ($today->greaterThanOrEqualTo($deadline)) {

                $this->update([
                    'status' => 'Bunting (PKB 90 Hari)',
                    'is_auto_advanced' => true,
                ]);

                $this->cow?->update([
                    'current_status' => 'Bunting'
                ]);

                $changed = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. BUNTING PKB 90 -> SIAP MELAHIRKAN
        |--------------------------------------------------------------------------
        */

        if (
            $this->status === 'Bunting (PKB 90 Hari)' &&
            $this->day90_date
        ) {
            $deadline = $this->day90_date->copy()->addDays(3);

            if ($today->greaterThanOrEqualTo($deadline)) {

                $this->update([
                    'status' => 'Siap Melahirkan (245 Hari)',
                    'is_auto_advanced' => true,
                ]);

                $this->cow?->update([
                    'current_status' => 'Siap Melahirkan'
                ]);

                $changed = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. SIAP MELAHIRKAN -> FASE MELAHIRKAN
        |--------------------------------------------------------------------------
        */

        if (
            $this->status === 'Siap Melahirkan (245 Hari)' &&
            $this->day245_date
        ) {
            $deadline = $this->day245_date->copy()->addDays(3);

            if ($today->greaterThanOrEqualTo($deadline)) {

                $this->update([
                    'status' => 'Fase Melahirkan',
                    'is_auto_advanced' => true,
                ]);

                $this->cow?->update([
                    'current_status' => 'Mendekati Melahirkan'
                ]);

                $changed = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. JIKA SUDAH MENCAPAI HPL
        |--------------------------------------------------------------------------
        |
        | Tidak langsung dianggap melahirkan.
        | Sistem hanya masuk Fase Melahirkan.
        |
        */

        if (
            $this->status === 'Siap Melahirkan (245 Hari)' &&
            $this->estimated_birth_date &&
            $today->greaterThanOrEqualTo($this->estimated_birth_date)
        ) {
            $this->update([
                'status' => 'Fase Melahirkan',
                'is_auto_advanced' => true,
            ]);

            $this->cow?->update([
                'current_status' => 'Mendekati Melahirkan'
            ]);

            $changed = true;
        }

        return $changed;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS BADGE
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'Melahirkan (Selesai)') {
            return [
                'bg' => 'bg-purple-100 text-purple-800 border-purple-300',
                'label' => 'Melahirkan (Selesai)'
            ];
        }

        if ($this->status === 'Gagal/Keguguran') {
            return [
                'bg' => 'bg-red-100 text-red-700 border-red-300',
                'label' => 'Gagal / IB Ulang'
            ];
        }

        $today = Carbon::today();
        $days = $this->days_remaining;

        /*
        |--------------------------------------------------------------------------
        | PASCA IB
        |--------------------------------------------------------------------------
        */

        if (
            $this->status === 'Pasca IB' ||
            $this->status === 'Estimasi 30 Hari'
        ) {
            $daysTo30 = $today->diffInDays(
                $this->day30_date,
                false
            );

            if ($daysTo30 <= 0 && $daysTo30 >= -3) {
                return [
                    'bg' => 'bg-amber-500 text-white font-bold animate-pulse',
                    'label' => 'Reminder 30 Hari: Konfirmasi'
                ];
            }

            return [
                'bg' => 'bg-blue-100 text-blue-800 border-blue-300',
                'label' => 'Pasca IB (30 Hari: ' .
                    max(0, $daysTo30) .
                    ' hari lagi)'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PKB 90 HARI
        |--------------------------------------------------------------------------
        */

        if ($this->status === 'Bunting (PKB 90 Hari)') {

            $daysTo90 = $today->diffInDays(
                $this->day90_date,
                false
            );

            if ($daysTo90 <= 0 && $daysTo90 >= -3) {
                return [
                    'bg' => 'bg-emerald-600 text-white font-bold animate-pulse',
                    'label' => 'Reminder 90 Hari: Konfirmasi PKB'
                ];
            }

            return [
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'label' => 'Bunting PKB (HPL ' .
                    $days .
                    ' hari lagi)'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 245 HARI
        |--------------------------------------------------------------------------
        */

        if ($this->status === 'Siap Melahirkan (245 Hari)') {
            return [
                'bg' => 'bg-orange-500 text-white font-bold animate-pulse',
                'label' => '245 Hari: Siap Melahirkan'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FASE MELAHIRKAN
        |--------------------------------------------------------------------------
        */

        if ($this->status === 'Fase Melahirkan') {
            return [
                'bg' => 'bg-purple-600 text-white font-bold animate-pulse',
                'label' => 'Fase Melahirkan & Konfirmasi Anak Sapi'
            ];
        }

        return [
            'bg' => 'bg-gray-100 text-gray-800',
            'label' => $this->status
        ];
    }
}