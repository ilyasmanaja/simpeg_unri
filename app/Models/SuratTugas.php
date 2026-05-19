<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $table      = 'SURAT_TUGAS';
    protected $primaryKey = 'id_surat_tugas';
    public    $timestamps = false;

    protected $fillable = [
        'waktu_pelaksanaan',
        'lama_pelaksanaan',
        'perihal',
        'status',
        'alasan_penolakan',
        'berkas_bermasalah',
    ];

    /**
     * Satu surat tugas bisa punya banyak berkas.
     */
    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_surat_tugas', 'id_surat_tugas');
    }

    /**
     * Ambil satu berkas aktif/terbaru untuk surat ini.
     */
    public function berkasAktif()
    {
        return $this->hasOne(Berkas::class, 'id_surat_tugas', 'id_surat_tugas')
                    ->latest('id_berkas');
    }

    /**
     * Daftar anggota yang terlibat dalam surat tugas.
     */
    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'id_surat_tugas', 'id_surat_tugas');
    }
}