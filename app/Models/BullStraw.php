<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullStraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'bull_name',
        'breed',
        'producer',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
