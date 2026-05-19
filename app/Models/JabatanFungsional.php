<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JabatanFungsional extends Model
{
    protected $table = 'jabatan_fungsional';
    protected $primaryKey = 'id_jabfung';
    public $incrementing = false;  // ← tambah ini
protected $keyType = 'string'; // ← tambah ini
    public $timestamps = false;

    protected $fillable = [
        'id_jabfung',
        'nama_jabfung',
        'jenis_jabfung',
        'urutan',
    ];

    // Scope untuk dosen
    public function scopeDosen($query)
    {
        return $query->where('jenis_jabfung', 'dosen')->orderBy('urutan');
    }

    // Scope untuk tendik
    public function scopeTendik($query)
    {
        return $query->where('jenis_jabfung', 'tendik')->orderBy('urutan');
    }

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_jabfung', 'id_jabfung');
    }
}