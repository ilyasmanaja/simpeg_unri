<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Berkas;

class PengajuanKenaikan extends Model
{
    protected $table      = 'PENGAJUAN_KENAIKAN';
    protected $primaryKey = 'id_pengajuan';
    public    $incrementing = true;
    public    $timestamps   = false;

    protected $fillable = [
        'id_pegawai',
        'jenis_pengajuan',
        'target_panggol',
        'target_jabfung',
        'status_pengajuan',
        'nomor_usulan',
        'keterangan_tambahan',
        'tanggal_pengajuan',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'target_jabfung', 'id_jabfung');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_pengajuan', 'id_pengajuan');
    }

    // ── Helper status ────────────────────────────────────────
    /**
     * Kembalikan label, badge class, dan icon Bootstrap Icons
     * sesuai status_pengajuan.
     */
    public function getStatusInfoAttribute(): array
    {
        $map = [
            'menunggu'           => [
                'label' => 'Menunggu Diproses',
                'class' => 'badge-menunggu',
                'icon'  => 'bi-hourglass-split',
            ],
            'verifikasi'         => [
                'label' => 'Sedang Diverifikasi',
                'class' => 'badge-verifikasi',
                'icon'  => 'bi-search',
            ],
            'persetujuan'        => [
                'label' => 'Menunggu Persetujuan',
                'class' => 'badge-persetujuan',
                'icon'  => 'bi-clock-history',
            ],
            'disetujui'          => [
                'label' => 'Disetujui',
                'class' => 'badge-disetujui',
                'icon'  => 'bi-check-circle-fill',
            ],
            'tolak_verifikasi'   => [
                'label' => 'Ditolak (Verifikasi)',
                'class' => 'badge-ditolak',
                'icon'  => 'bi-x-circle-fill',
            ],
            'tolak_persetujuan'  => [
                'label' => 'Ditolak (Persetujuan)',
                'class' => 'badge-ditolak',
                'icon'  => 'bi-x-circle-fill',
            ],
        ];

        return $map[$this->status_pengajuan]
            ?? ['label' => ucfirst($this->status_pengajuan), 'class' => 'badge-menunggu', 'icon' => 'bi-question-circle'];
    }

    // ── Scope: hanya jabfung ─────────────────────────────────
    public function scopeJabfung($query)
    {
        return $query->where('jenis_pengajuan', 'jabfung');
    }

    public function verifikasi()
    {
        return $this->hasOneThrough(
            Verifikasi::class, // Model Tujuan (Target)
            Berkas::class,     // Model Perantara (Through)
            'id_pengajuan',    // Foreign key di tabel perantara (berkas)
            'id_berkas',       // Foreign key di tabel tujuan (verifikasi)
            'id_pengajuan',    // Primary key di tabel ini (pengajuan_kenaikan)
            'id_berkas'        // Primary key di tabel perantara (berkas)
        );
    }
}