<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table      = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public    $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'foto',
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_hp',
        'jurusan',
        'prodi',
        'nomor_hp_darurat',
        'nidn',
        'nip',
        'status_pegawai',
        'id_jabfung',
        'id_panggol',
    ];

    // ----------------------------------------------------------------
    // ACCESSOR
    // ----------------------------------------------------------------

    /**
     * Nomor identitas: NIDN untuk dosen, NIP untuk tendik.
     * Logika: kalau nidn terisi → dosen, kalau nip terisi → tendik.
     */
    public function getNomorIdentitasAttribute(): string
    {
        if (!empty($this->nidn)) {
            return 'NIDN: ' . $this->nidn;
        }

        if (!empty($this->nip)) {
            return 'NIP: ' . $this->nip;
        }

        return '-';
    }

    /**
     * Jenis pegawai berdasarkan nidn/nip.
     */
    public function getJenisPegawaiAttribute(): string
    {
        if (!empty($this->nidn)) return 'Dosen';
        if (!empty($this->nip))  return 'Tendik';
        return 'Tidak Diketahui';
    }

    // ----------------------------------------------------------------
    // RELASI
    // ----------------------------------------------------------------

    public function userManage()
    {
        return $this->hasOne(UserManage::class, 'id_pegawai', 'id_pegawai');
    }

    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'id_jabfung', 'id_jabfung');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'id_panggol', 'id_panggol');
    }

    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'id_pegawai', 'id_pegawai');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_pegawai', 'id_pegawai');
    }
}