<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'ANGGOTA';
    protected $primaryKey = 'id_anggota';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'id_surat_tugas', 'id_surat_tugas');
    }
}
