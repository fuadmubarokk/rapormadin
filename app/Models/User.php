<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'no_hp',
        'foto',
        'ttd_wali_kelas',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke tabel Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Cek apakah user memiliki role tertentu
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Cek apakah user adalah guru
     */
    public function isGuru()
    {
        return $this->hasRole('guru');
    }

    /**
     * Cek apakah user adalah wali kelas
     */
    public function isWaliKelas()
    {
        return $this->hasRole('wali');
    }
    
    /**
     * Cek apakah user memiliki peran Guru dan Wali Kelas sekaligus.
     *
     * @return bool
     */
    public function isGuruWali()
    {
        $roles = $this->roles->pluck('name')->toArray();
        return in_array('guru', $roles) && in_array('wali_kelas', $roles);
    }

    /**
     * Relasi ke tabel guru_mapel_kelas
     */
    public function guruMapelKelas()
    {
        return $this->hasMany(GuruMapelKelas::class, 'guru_id');
    }

    /**
     * Relasi ke tabel kelas sebagai wali kelas
     */
    public function kelasWali()
    {
        return $this->hasOne(Kelas::class, 'wali_id');
    }
}