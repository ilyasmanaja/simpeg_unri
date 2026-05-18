<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKenaikan extends Model
{
    protected $table = 'PENGAJUAN_KENAIKAN';
    protected $primaryKey = 'id_pengajuan';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_pengajuan', 'id_pengajuan');
    }
}
