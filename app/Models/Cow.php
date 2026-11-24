<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Cow (Sapi Betina / Akseptor)
 * Seluruh ternak utama dalam sistem ini adalah sapi betina yang dipantau kebuntingannya.
 */
class Cow extends Model
{
    use HasFactory;

    public const BREEDS = [
        'Limosin',
        'Simental',
        'Bali',
        'PO (Peranakan Ongole)',
        'Friesian Holstein (FH)',
        'Brahman',
        'Angus',
        'Madura',
        'Lokal',
        'Cross / Persilangan',
    ];

    protected $fillable = [
        'cow_nik',
        'name',
        'breed',
        'birth_date',
        'farmer_id',
        'regency_id',
        'district_id',
        'village_id',
        'current_status',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function pregnancyRecords(): HasMany
    {
        return $this->hasMany(PregnancyRecord::class)
            ->orderByDesc('ib_ke')
            ->orderByDesc('insemination_date')
            ->orderByDesc('id');
    }

    public function latestPregnancy(): HasOne
    {
        return $this->hasOne(PregnancyRecord::class)->latestOfMany('id');
    }

    public function calves(): HasMany
    {
        return $this->hasMany(Calf::class, 'cow_id');
    }

    /**
     * Menghasilkan nama panggilan sapi otomatis jika petugas mengosongkannya:
     * Pola: "[Nama Pemilik] 1", "[Nama Pemilik] 2", dst.
     */
    public static function generateDefaultName(Farmer $farmer): string
    {
        $ownerName = trim($farmer->name) ?: 'Sapi';
        $seq = $farmer->cows()->count() + 1;
        $candidate = "{$ownerName} {$seq}";

        while ($farmer->cows()->where('name', $candidate)->exists()) {
            $seq++;
            $candidate = "{$ownerName} {$seq}";
        }

        return $candidate;
    }

    /**
     * Menghitung nomor urutan IB berikutnya untuk sapi ini (IB ke-1, ke-2, dst.)
     */
    public function getNextIbNumber(): int
    {
        $max = (int) $this->pregnancyRecords()->max('ib_ke');
        $count = $this->pregnancyRecords()->count();

        return max($max, $count) + 1;
    }

    /**
     * Apakah sapi ini siap/boleh diinputkan IB Lanjutan (IB ke-2, dst.)
     */
    public function canReceiveNextIb(): bool
    {
        $latest = $this->latestPregnancy;
        if (!$latest) {
            return true;
        }

        return in_array($latest->status, [
            'Gagal/Keguguran',
            'Melahirkan (Selesai)',
        ]);
    }

    /**
     * Scope query untuk membatasi akses data Sapi berdasarkan Role:
     * - Admin Provinsi: seluruh kabupaten/kota
     * - Admin Kabupaten/Kota: terkunci pada regency_id akunnya
     * - Inseminator: seluruh riwayat sapi yang ia tangani (lintas kabupaten diperbolehkan)
     */
    public function scopeForOperator($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->isAdminKabupaten()) {
            $query->where('regency_id', $user->regency_id);
        }

        if ($user->isOperator() && !$user->isAdmin()) {
            $query->whereHas('pregnancyRecords', function ($q) use ($user) {
                $q->where('operator_id', $user->id);
            });
        }

        return $query;
    }
}
