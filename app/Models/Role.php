<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id_role';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function users()
    {
        // Relasi Many-to-Many ke USER_MANAGE melalui tabel pivot user_role
        return $this->belongsToMany(UserManage::class, 'user_role', 'id_role', 'id_user');
    }
}