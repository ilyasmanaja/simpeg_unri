<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $table      = 'VERIFIKASI';
    protected $primaryKey = 'id_verifikasi';
    public    $incrementing = false;
    protected $keyType    = 'string';
    public    $timestamps = false;

    protected $fillable = [
        'id_verifikasi', 'status_verifikasi', 'jenis_verifikasi',
        'tanggal_pengajuan', 'tanggal_proses', 'keterangan', 'id_berkas',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_proses'    => 'date',
    ];

    // Konstanta jenis_verifikasi
    const JENIS_JABFUNG     = 'JABFUNG';
    const JENIS_PANGKAT     = 'PANGKAT';
    const JENIS_SURAT_TUGAS = 'SURAT_TUGAS';

    // Konstanta status_verifikasi
    const STATUS_DITERUSKAN    = 'Diteruskan';
    const STATUS_DITOLAK       = 'Ditolak';
    const STATUS_TERVERIFIKASI = 'Terverifikasi'; 

    // ── Relasi 
    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'id_berkas', 'id_berkas');
    }

    // ── Helper: generate PK unik 
    public static function generateId(string $jenis): string
    {
        $prefix = match ($jenis) {
            self::JENIS_JABFUNG     => 'VRF-JF',
            self::JENIS_PANGKAT     => 'VRF-PG',
            self::JENIS_SURAT_TUGAS => 'VRF-ST',
            default                 => 'VRF',
        };
        return $prefix . '-' . now()->format('YmdHis') . '-' . rand(100, 999);
    }
}