<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    protected $table      = 'BERKAS';
    protected $primaryKey = 'id_berkas';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'id_berkas',
        'nama_berkas',
        'jenis_berkas',
        'nomor_berkas',
        'file_path',
        'id_pegawai',
        'id_pengajuan',
        'id_jabfung',
        'id_panggol',
        'id_surat_tugas',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKenaikan::class, 'id_pengajuan', 'id_pengajuan');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}