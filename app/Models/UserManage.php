<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserManage extends Authenticatable
{
    protected $table = 'USER_MANAGE';

    protected $primaryKey = 'id_user';

    public $incrementing = true; 

    protected $keyType = 'int'; 

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['password'];

    protected $fillable = [
        'id_pegawai',
        'email',
        'password',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function roles()
{
    return $this->belongsToMany(
        Role::class,
        'user_role',
        'id_user',
        'id_role'
    );
}

    public function hasRole($roleName)
    {
        return $this->roles->contains('jenis_role', $roleName);
    }
}
