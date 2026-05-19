<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    protected $table      = 'BERKAS';
    protected $primaryKey = 'id_berkas';
    public    $keyType    = 'int';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = [
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

    /**
     * Berkas milik surat tugas tertentu.
     */
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'id_surat_tugas', 'id_surat_tugas');
    }

    /**
     * Berkas milik pegawai tertentu.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Berkas milik pengajuan kenaikan tertentu.
     */
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanKenaikan::class, 'id_pengajuan', 'id_pengajuan');
    }
}