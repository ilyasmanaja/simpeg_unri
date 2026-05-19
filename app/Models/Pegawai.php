<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    protected $fillable = [
        'nama_lengkap',
        'foto',
        'nik',
        'nip',
        'nidn',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_hp',
        'nomor_hp_darurat',
        'jurusan',
        'prodi',
        'status_pegawai',
        'id_jabfung',
        'id_panggol',
    ];

    // Relasi ke tabel referensi
    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'id_jabfung', 'id_jabfung');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'id_panggol', 'id_panggol');
    }

    // Relasi ke tabel turunan
    public function user()
    {
        return $this->hasOne(UserManage::class, 'id_pegawai', 'id_pegawai');
    }

    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'id_pegawai', 'id_pegawai');
    }

    public function pengajuanKenaikan()
    {
        return $this->hasMany(PengajuanKenaikan::class, 'id_pegawai', 'id_pegawai');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_pegawai', 'id_pegawai');
    }
}