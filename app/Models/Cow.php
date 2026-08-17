<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Cow (Sapi Induk Betina)
 * Seluruh ternak utama dalam sistem ini adalah sapi betina yang dipantau kehamilannya.
 */
class Cow extends Model
{
    use HasFactory;

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
        return $this->hasMany(PregnancyRecord::class)->orderBy('insemination_date', 'desc');
    }

    public function latestPregnancy(): HasOne
    {
        return $this->hasOne(PregnancyRecord::class)->latestOfMany('insemination_date');
    }

    public function calves(): HasMany
    {
        return $this->hasMany(Calf::class, 'cow_id');
    }

    /**
     * Scope query untuk membatasi akses data Sapi berdasarkan Scope Regional (Admin Kabupaten vs Admin Provinsi vs Operator)
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
            if ($user->regency_id) {
                $query->where('regency_id', $user->regency_id);
            }
        }

        return $query;
    }
}
