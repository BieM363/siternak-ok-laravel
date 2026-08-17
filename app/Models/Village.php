<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Village (Desa / Kelurahan)
 */
class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'code',
        'name',
    ];

    /**
     * Relasi ke Kecamatan
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
