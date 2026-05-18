<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $table = 'VERIFIKASI';
    protected $primaryKey = 'id_verifikasi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'id_berkas', 'id_berkas');
    }
}
