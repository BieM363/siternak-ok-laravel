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

    public const OPERATOR_SESSION_HOURS = 8;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'admin', 'operator', 'guest'
        'regency_id',
        'phone',
        'last_login_at',
        'login_expires_at',
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
            'last_login_at' => 'datetime',
            'login_expires_at' => 'datetime',
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
     * Memeriksa apakah user adalah Admin Kota
     */
    public function isAdminKota(): bool
    {
        return $this->isAdminKabupaten() && $this->regency && str_contains(strtolower($this->regency->name), 'kota');
    }

    /**
     * Mengembalikan teks badge peran user
     */
    public function getRoleBadgeText(): string
    {
        if ($this->isAdminProvinsi()) {
            return 'Admin Prov';
        }
        if ($this->isAdminKota()) {
            return 'Admin Kota';
        }
        if ($this->isAdminKabupaten()) {
            return 'Admin Kab';
        }
        if ($this->isOperator()) {
            return 'Operator IB';
        }
        return 'Tamu';
    }

    /**
     * Mengembalikan regency_id scope jika user adalah Admin Kabupaten/Kota
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
        return strtolower($this->role) === 'operator';
    }

    public function canOperate(): bool
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

    /**
     * Mengembalikan sisa detik waktu login operator (batas 8 jam)
     */
    public function getRemainingSessionSeconds(): ?int
    {
        if (!$this->isOperator()) {
            return null;
        }

        $expiresAtTimestamp = session('operator_expires_at')
            ?? ($this->login_expires_at ? $this->login_expires_at->timestamp : null);

        if (!$expiresAtTimestamp) {
            return self::OPERATOR_SESSION_HOURS * 3600;
        }

        return max(0, (int) ($expiresAtTimestamp - now()->timestamp));
    }
}
