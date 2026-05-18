<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserManage extends Authenticatable
{
    protected $table = 'USER_MANAGE';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['password']; // Sembunyikan password saat data dipanggil

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function roles()
    {
        // Relasi Many-to-Many ke ROLE
        return $this->belongsToMany(Role::class, 'USER_ROLE', 'id_user', 'id_role');
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('jenis_role', $roleName);
    }
}
