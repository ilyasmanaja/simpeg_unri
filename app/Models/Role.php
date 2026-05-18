<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'ROLE';
    protected $primaryKey = 'id_role';
    public $incrementing = false; // Set false jika id di-input manual
    public $timestamps = false;
    protected $guarded = [];

    public function users()
    {
        // Relasi Many-to-Many ke USER_MANAGE melalui tabel pivot USER_ROLE
        return $this->belongsToMany(UserManage::class, 'USER_ROLE', 'id_role', 'id_user');
    }
}
