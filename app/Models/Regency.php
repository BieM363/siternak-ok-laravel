<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Regency (Kabupaten / Kota)
 * SiTernak-OK (Sistem Pemantauan Kehamilan Sapi)
 */
class Regency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Relasi ke Kecamatan (Districts)
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Relasi ke Peternak (Farmers)
     */
    public function farmers(): HasMany
    {
        return $this->hasMany(Farmer::class);
    }

    /**
     * Relasi ke Sapi (Cows)
     */
    public function cows(): HasMany
    {
        return $this->hasMany(Cow::class);
    }
}
