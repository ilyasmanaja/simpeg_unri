<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
    protected $table = 'PANGKAT_GOLONGAN';
    protected $primaryKey = 'id_panggol';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
