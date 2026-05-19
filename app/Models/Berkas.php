<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    protected $table      = 'BERKAS';
    protected $primaryKey = 'id_berkas';
    public    $incrementing = true;
    protected $keyType    = 'int';
    public    $timestamps = false;
    protected $guarded    = [];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKenaikan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'id_jabfung', 'id_jabfung');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'id_panggol', 'id_panggol');
    }

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'id_surat_tugas', 'id_surat_tugas');
    }

    public function verifikasi()
    {
        return $this->hasMany(Verifikasi::class, 'id_berkas', 'id_berkas');
    }
}