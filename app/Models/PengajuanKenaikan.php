<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKenaikan extends Model
{
    protected $table      = 'pengajuan_kenaikan';
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
        'berkas_bermasalah',   // ← TAMBAHAN: array berkas yang ditandai operator untuk direvisi
    ];

    // Cast berkas_bermasalah otomatis jadi array saat diakses
    protected $casts = [
        'berkas_bermasalah' => 'array',
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

    // ── Helper: cek apakah berkas tertentu bermasalah ────────
    public function isBerkasbermasalah(string $jenisBerkas): bool
    {
        return in_array($jenisBerkas, $this->berkas_bermasalah ?? []);
    }
}