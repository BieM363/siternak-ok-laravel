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

    public const ACTIVE_PREGNANCY_STATUSES = [
        'Bunting (PKB 90 Hari)',
        'Siap Melahirkan (245 Hari)',
        'Fase Melahirkan',
    ];

    protected $fillable = [
        'cow_id',
        'operator_id',
        'ib_ke',
        'insemination_date',
        'semen_code',
        'bull_straw_id',
        'straw_rumpun',
        'straw_kode_bull',
        'straw_kode_batch',
        'straw_producer',
        'is_custom_straw',
        'day30_date',
        'day90_date',
        'day245_date',
        'day270_date',
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
        'ib_ke' => 'integer',
        'is_custom_straw' => 'boolean',
        'insemination_date' => 'date',
        'day30_date' => 'date',
        'day90_date' => 'date',
        'day245_date' => 'date',
        'day270_date' => 'date',
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

    public function bullStraw(): BelongsTo
    {
        return $this->belongsTo(BullStraw::class, 'bull_straw_id');
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
    | HELPER: UMUR KEBUNTINGAN & IDENTITAS PEJANTAN
    |--------------------------------------------------------------------------
    */

    public function getDaysSinceInseminationAttribute(): int
    {
        if (!$this->insemination_date) {
            return 0;
        }

        $refDate = $this->actual_birth_date ?? Carbon::today();

        return max(0, (int) $this->insemination_date->diffInDays($refDate, false));
    }

    public function getPregnancyAgeMonthsAttribute(): float
    {
        return round($this->days_since_insemination / 30, 1);
    }

    public function getPregnancyAgeLabelAttribute(): string
    {
        $days = $this->days_since_insemination;
        $months = intdiv($days, 30);
        $remDays = $days % 30;

        if ($months <= 0) {
            return "{$days} Hari";
        }

        return $remDays > 0 ? "{$months} Bulan {$remDays} Hari" : "{$months} Bulan";
    }

    public function getStrawIdentityLabelAttribute(): string
    {
        $rumpun = $this->straw_rumpun;
        $kodeBull = $this->straw_kode_bull ?: $this->semen_code;
        $kodeBatch = $this->straw_kode_batch;

        $parts = array_filter([
            $rumpun,
            $kodeBull ? "Bull: {$kodeBull}" : null,
            $kodeBatch ? "Batch: {$kodeBatch}" : null,
        ]);

        return !empty($parts) ? implode(' · ', $parts) : '-';
    }

    public function getTargetMonth9DateAttribute(): ?Carbon
    {
        if ($this->day270_date) {
            return $this->day270_date;
        }

        return $this->insemination_date
            ? $this->insemination_date->copy()->addDays(270)
            : $this->estimated_birth_date;
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
                'Gagal/Keguguran',
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
            '270day', '245day' => $this->target_month9_date,
            'birth' => $this->estimated_birth_date,
            default => $this->estimated_birth_date,
        };

        if (!$targetDate) {
            return '#';
        }

        $titlePrefix = match ($milestoneType) {
            '30day' => 'Tahap 1 (Hari ke-30): Evaluasi Awal Pasca IB',
            '90day' => 'Tahap 2 (Hari ke-90): Pemeriksaan Kebuntingan (PKB)',
            '270day', '245day', 'birth' => 'Tahap 3 (Bulan ke-9): Konfirmasi Kelahiran Sapi',
            default => 'Reminder Kebuntingan Sapi',
        };

        $cowNik = $this->cow->cow_nik ?? 'Sapi';
        $cowName = $this->cow->name ?? 'Induk Sapi';
        $farmerName = $this->cow->farmer->name ?? '-';
        $farmerNik = $this->cow->farmer->nik ?? 'Tanpa NIK';

        $isCity = $this->cow->regency && str_contains(strtolower($this->cow->regency->name), 'kota');
        $unit = $isCity ? 'Kel. ' : 'Desa ';

        $location = trim(
            ($this->cow->regency->name ?? '') .
            ', Kec. ' .
            ($this->cow->district->name ?? '') .
            ', ' . $unit .
            ($this->cow->village->name ?? ''),
            ', '
        );

        $title = "[SiTernak-OK] {$titlePrefix} - {$cowName} ({$cowNik})";

        $farmerLine = (auth()->check() && !auth()->user()->isGuest())
            ? "Pemilik: {$farmerName} (NIK: {$farmerNik})\n"
            : "Pemilik: {$farmerName}\n";

        $details =
            "PEMANTAUAN KEBUNTINGAN SAPI - SiTernak-OK\n" .
            "NIK Sapi: {$cowNik} ({$cowName}) - IB ke-" . ($this->ib_ke ?? 1) . "\n" .
            $farmerLine .
            "Tanggal Inseminasi: " .
                ($this->insemination_date
                    ? $this->insemination_date->format('d/m/Y')
                    : '-') . "\n" .
            "Identitas Pejantan: {$this->straw_identity_label}\n" .
            "Inseminator: " . ($this->officer_name ?? '-') . "\n" .
            "Status: {$this->status_display_label}\n" .
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
    | AUTO ADVANCE — LOGIKA 3 TAHAP BERBASIS TANGGAL INSEMINASI + AUTO-LANJUT 3 HARI
    |--------------------------------------------------------------------------
    |
    | 1. Tahap 1 — Hari ke-30 (day30_date + 3 hari tanpa respons Admin Kab/Kota)
    |    -> Lanjut ke 'Bunting (PKB 90 Hari)'
    |
    | 2. Tahap 2 — Hari ke-90 / 3 Bulan (day90_date + 3 hari tanpa respons)
    |    -> Lanjut dianggap Bunting Terkonfirmasi ('Siap Melahirkan (245 Hari)')
    |
    | 3. Tahap 3 — Bulan ke-9 (~Hari ke-270 sampai 280 dari tanggal_inseminasi)
    |    -> Masuk 'Fase Melahirkan' (Menunggu Konfirmasi Kelahiran)
    |
    */

    public function checkAndAutoAdvance(): bool
    {
        if (in_array($this->status, [
            'Melahirkan (Selesai)',
            'Gagal/Keguguran',
        ])) {
            return false;
        }

        $today = Carbon::today();
        $changed = false;

        // Pastikan day270_date tersedia untuk data lama
        $day270 = $this->day270_date
            ?? ($this->insemination_date ? $this->insemination_date->copy()->addDays(270) : null);

        // 1. TAHAP 1 (HARI KE-30): PASCA IB -> BUNTING (MENUNGGU PKB 90 HARI)
        if (
            in_array($this->status, ['Pasca IB', 'Estimasi 30 Hari']) &&
            $this->day30_date
        ) {
            $deadline = $this->day30_date->copy()->addDays(3);

            if ($today->greaterThanOrEqualTo($deadline)) {
                $this->update([
                    'status' => 'Bunting (PKB 90 Hari)',
                    'is_auto_advanced' => true,
                ]);

                $this->cow?->update([
                    'current_status' => 'Bunting',
                ]);

                $changed = true;
            }
        }

        // 2. TAHAP 2 (HARI KE-90 / 3 BULAN): PKB 90 HARI -> BUNTING TERKONFIRMASI MENUJU BULAN KE-9
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
                    'current_status' => 'Bunting',
                ]);

                $changed = true;
            }
        }

        // 3. TAHAP 3 (BULAN KE-9 / HARI KE-270): MASUK FASE KONFIRMASI KELAHIRAN
        if (
            $this->status === 'Siap Melahirkan (245 Hari)' &&
            $day270 &&
            $today->greaterThanOrEqualTo($day270)
        ) {
            $this->update([
                'status' => 'Fase Melahirkan',
                'is_auto_advanced' => true,
            ]);

            $this->cow?->update([
                'current_status' => 'Mendekati Melahirkan',
            ]);

            $changed = true;
        }

        return $changed;
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPE: NOTIFIKASI 3 TAHAP (HARI 30, HARI 90, BULAN KE-9)
    |--------------------------------------------------------------------------
    */

    public function scopeDueForReminder($query, ?Carbon $today = null)
    {
        $today = $today ?? Carbon::today();
        $threeDaysAgo = $today->copy()->subDays(3);

        return $query
            ->whereNotIn('status', [
                'Melahirkan (Selesai)',
                'Gagal/Keguguran',
            ])
            ->where(function ($q) use ($today, $threeDaysAgo) {
                // Tahap 1: Hari ke-30 (jendela 3 hari)
                $q->where(function ($sub) use ($today, $threeDaysAgo) {
                    $sub->whereIn('status', ['Pasca IB', 'Estimasi 30 Hari'])
                        ->whereDate('day30_date', '<=', $today)
                        ->whereDate('day30_date', '>=', $threeDaysAgo);
                })
                // Tahap 2: Hari ke-90 / 3 Bulan (PKB Pasti, jendela 3 hari)
                ->orWhere(function ($sub) use ($today, $threeDaysAgo) {
                    $sub->where('status', 'Bunting (PKB 90 Hari)')
                        ->whereDate('day90_date', '<=', $today)
                        ->whereDate('day90_date', '>=', $threeDaysAgo);
                })
                // Tahap 3: Bulan ke-9 (~Hari ke-270 s/d 280+) Konfirmasi Kelahiran
                ->orWhere(function ($sub) use ($today) {
                    $sub->where('status', 'Fase Melahirkan')
                        ->where(function ($d) use ($today) {
                            $d->whereDate('day270_date', '<=', $today)
                              ->orWhereDate('estimated_birth_date', '<=', $today);
                        })
                        ->where(function ($w) use ($today) {
                            $w->whereNull('birth_window_end_date')
                              ->orWhereDate('birth_window_end_date', '>=', $today);
                        });
                });
            });
    }

    public function getReminderStageInfoAttribute(): array
    {
        if (in_array($this->status, ['Pasca IB', 'Estimasi 30 Hari'])) {
            return [
                'stage' => 1,
                'label' => 'Tahap 1 · Hari ke-30 (Evaluasi Awal)',
                'target_date' => $this->day30_date,
                'question' => 'Apakah kebuntingan berlanjut atau gagal / minta kawin ulang?',
            ];
        }

        if ($this->status === 'Bunting (PKB 90 Hari)') {
            return [
                'stage' => 2,
                'label' => 'Tahap 2 · Hari ke-90 / 3 Bulan (PKB Pasti)',
                'target_date' => $this->day90_date,
                'question' => 'Hasil pemeriksaan kebuntingan (PKB): Bunting atau Tidak Bunting?',
            ];
        }

        return [
            'stage' => 3,
            'label' => 'Tahap 3 · Bulan ke-9 (Konfirmasi Kelahiran)',
            'target_date' => $this->target_month9_date ?? $this->estimated_birth_date,
            'question' => 'Apakah sapi sudah melahirkan? Jika Ya, otomatis masuk Data Pedet & Kelahiran.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS LABEL & BADGE
    |--------------------------------------------------------------------------
    */

    public function getStatusDisplayLabelAttribute(): string
    {
        return match ($this->status) {
            'Pasca IB', 'Estimasi 30 Hari' => 'Pasca IB (Menunggu Cek Hari ke-30)',
            'Bunting (PKB 90 Hari)' => 'Lanjut Tahap 1 (Menunggu PKB 90 Hari)',
            'Siap Melahirkan (245 Hari)' => 'Bunting Terkonfirmasi (PKB 90 Hari)',
            'Fase Melahirkan' => 'Bulan ke-9 (Konfirmasi Kelahiran)',
            'Melahirkan (Selesai)' => 'Melahirkan (Selesai)',
            'Gagal/Keguguran' => 'Tidak Bunting / Gagal (Siap IB Ulang)',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'Melahirkan (Selesai)') {
            return [
                'bg' => 'bg-purple-100 text-purple-800 border-purple-300',
                'label' => 'Melahirkan (Selesai)',
            ];
        }

        if ($this->status === 'Gagal/Keguguran') {
            return [
                'bg' => 'bg-red-100 text-red-700 border-red-300',
                'label' => 'Tidak Bunting / Siap IB Ulang',
            ];
        }

        $today = Carbon::today();
        $days = $this->days_remaining;

        if (in_array($this->status, ['Pasca IB', 'Estimasi 30 Hari'])) {
            $daysTo30 = $this->day30_date ? $today->diffInDays($this->day30_date, false) : 30;

            if ($daysTo30 <= 0 && $daysTo30 >= -3) {
                return [
                    'bg' => 'bg-amber-500 text-white font-bold',
                    'label' => 'Tahap 1 (Hari 30): Perlu Konfirmasi',
                ];
            }

            return [
                'bg' => 'bg-blue-100 text-blue-800 border-blue-300',
                'label' => 'Pasca IB (Cek 30 Hr: ' . max(0, $daysTo30) . ' hr lagi)',
            ];
        }

        if ($this->status === 'Bunting (PKB 90 Hari)') {
            $daysTo90 = $this->day90_date ? $today->diffInDays($this->day90_date, false) : 90;

            if ($daysTo90 <= 0 && $daysTo90 >= -3) {
                return [
                    'bg' => 'bg-emerald-600 text-white font-bold',
                    'label' => 'Tahap 2 (PKB 90 Hr): Perlu Konfirmasi',
                ];
            }

            return [
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'label' => 'Menunggu PKB 90 Hr (' . $this->pregnancy_age_label . ')',
            ];
        }

        if ($this->status === 'Siap Melahirkan (245 Hari)') {
            return [
                'bg' => 'bg-teal-100 text-teal-800 border-teal-300',
                'label' => 'Bunting Terkonfirmasi (' . $this->pregnancy_age_label . ' · HPL ' . max(0, $days) . ' hr)',
            ];
        }

        if ($this->status === 'Fase Melahirkan') {
            return [
                'bg' => 'bg-amber-600 text-white font-bold',
                'label' => 'Tahap 3 (Bulan ke-9): Konfirmasi Kelahiran',
            ];
        }

        return [
            'bg' => 'bg-gray-100 text-gray-800',
            'label' => $this->status,
        ];
    }
}
