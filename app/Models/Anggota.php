<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table      = 'ANGGOTA';
    protected $primaryKey = 'id_anggota';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = [
        'jenis_anggota',
        'nama_anggota',
        'id_surat_tugas',
        'id_pegawai',
    ];

    /**
     * Anggota terkait dengan surat tugas.
     */
    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'id_surat_tugas', 'id_surat_tugas');
    }

    /**
     * Anggota terkait dengan data pegawai.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}