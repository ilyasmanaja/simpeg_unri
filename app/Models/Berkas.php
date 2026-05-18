<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    protected $table = 'BERKAS';
    protected $primaryKey = 'id_berkas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKenaikan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function verifikasi()
    {
        return $this->hasMany(Verifikasi::class, 'id_berkas', 'id_berkas');
    }
}
