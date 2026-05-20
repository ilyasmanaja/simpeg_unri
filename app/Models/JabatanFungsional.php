<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanFungsional extends Model
{
    protected $table      = 'JABATAN_FUNGSIONAL';
    protected $primaryKey = 'id_jabfung';
    public    $keyType    = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'id_jabfung',
        'jenis_jabfung',
        'nama_jabfung',
        'urutan',
    ];

    // Relasi: jabfung ini dimiliki banyak pegawai (jabfung aktif)
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_jabfung', 'id_jabfung');
    }

    // Relasi: jabfung ini menjadi target banyak pengajuan
    public function pengajuanKenaikan()
    {
        return $this->hasMany(PengajuanKenaikan::class, 'target_jabfung', 'id_jabfung');
    }

    // Scope: hanya dosen
    public function scopeDosen($query)
    {
        return $query->where('jenis_jabfung', 'dosen')->orderBy('urutan');
    }

    // Scope: hanya tendik
    public function scopeTendik($query)
    {
        return $query->where('jenis_jabfung', 'tendik')->orderBy('nama_jabfung');
    }
}