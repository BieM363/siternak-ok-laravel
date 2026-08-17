<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Calf (Anak Sapi Hasil Kelahiran)
 * Mencatat jenis kelamin anak (Jantan/Betina), NIK anak, dan kondisi saat lahir
 */
class Calf extends Model
{
    use HasFactory;

    protected $fillable = [
        'pregnancy_record_id',
        'cow_id',
        'calf_nik',
        'name',
        'breed',
        'gender',
        'birth_date',
        'birth_weight',
        'condition',
        'notes',
        'is_promoted',
        'promoted_cow_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'birth_weight' => 'float',
        'is_promoted' => 'boolean',
    ];

    public function pregnancyRecord(): BelongsTo
    {
        return $this->belongsTo(PregnancyRecord::class);
    }

    public function motherCow(): BelongsTo
    {
        return $this->belongsTo(Cow::class, 'cow_id');
    }

    public function promotedCow(): BelongsTo
    {
        return $this->belongsTo(Cow::class, 'promoted_cow_id');
    }
}
