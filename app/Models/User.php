<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User (Pengguna Sistem SiTernak-OK)
 * Memiliki 3 Hak Akses (Role):
 * 1. 'admin'    : Hak akses administrasi (mengisi/kelola semua data sapi, peternak, operator, wilayah)
 * 2. 'operator' : Hak akses operator (menekan Ya/Tidak konfirmasi kebuntingan & melahirkan, serta data anak sapi)
 * 3. 'guest'    : Hak akses tamu (hanya melihat/pelihat data tanpa aksi modifikasi)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'operator', 'guest'
        'regency_id',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * Memeriksa apakah user adalah Administrasi
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    /**
     * Memeriksa apakah user adalah Admin Provinsi (Super Admin yang melihat seluruh Kabupaten)
     */
    public function isAdminProvinsi(): bool
    {
        return $this->isAdmin() && is_null($this->regency_id);
    }

    /**
     * Memeriksa apakah user adalah Admin Kabupaten/Kota (Terbatas pada regency_id tertentu)
     */
    public function isAdminKabupaten(): bool
    {
        return $this->isAdmin() && !is_null($this->regency_id);
    }

    /**
     * Mengembalikan regency_idscope jika user adalah Admin Kabupaten
     */
    public function getScopeRegencyId(): ?int
    {
        return $this->isAdminKabupaten() ? (int) $this->regency_id : null;
    }

    /**
     * Memeriksa apakah user memiliki hak Operator (Operator atau Admin)
     */
    public function isOperator(): bool
    {
        return in_array(strtolower($this->role), ['admin', 'operator']);
    }

    /**
     * Memeriksa apakah user hanya Tamu (Pelihat)
     */
    public function isGuest(): bool
    {
        return strtolower($this->role) === 'guest';
    }
}
