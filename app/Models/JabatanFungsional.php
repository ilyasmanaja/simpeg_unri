<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanFungsional extends Model
{
    protected $table = 'JABATAN_FUNGSIONAL';
    protected $primaryKey = 'id_jabfung';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
