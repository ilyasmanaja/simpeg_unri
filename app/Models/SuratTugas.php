<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $table = 'SURAT_TUGAS';
    protected $primaryKey = 'id_surat_tugas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'id_surat_tugas', 'id_surat_tugas');
    }
}
