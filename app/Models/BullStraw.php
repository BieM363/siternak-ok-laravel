<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullStraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'kode_bull',
        'kode_batch',
        'bull_name',
        'breed',
        'producer',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getEffectiveKodeBullAttribute(): string
    {
        return $this->kode_bull ?: ($this->code ?? '-');
    }

    public function getDisplayLabelAttribute(): string
    {
        $parts = array_filter([
            $this->breed,
            $this->effective_kode_bull,
            $this->kode_batch ? "Batch {$this->kode_batch}" : null,
            $this->bull_name ? "({$this->bull_name})" : null,
        ]);

        return implode(' • ', $parts);
    }
}
