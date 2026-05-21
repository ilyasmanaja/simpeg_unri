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
    protected $fillable = [
        'jenis_role'
    ];

    public function users()
{
    return $this->belongsToMany(
        UserManage::class,
        'user_role',
        'id_role',
        'id_user'
    );
}


}
