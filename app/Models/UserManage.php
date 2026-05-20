<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserManage extends Authenticatable
{
    use Notifiable;

    protected $table = 'user_manage';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
        'id_pegawai',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi ke pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    // Relasi Many-to-Many ke role melalui user_role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'id_user', 'id_role');
    }

    /**
     * Ambil jenis_role pertama yang dimiliki user.
     * Contoh penggunaan: Auth::user()->jenis_role
     */
    public function getJenisRoleAttribute(): ?string
    {
        return $this->roles->first()?->jenis_role;
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('jenis_role', $roleName)->exists();
    }
}