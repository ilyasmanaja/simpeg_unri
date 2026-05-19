<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanKenaikan extends Model
{
    protected $table      = 'PENGAJUAN_KENAIKAN';
    protected $primaryKey = 'id_pengajuan';
    public    $incrementing = false;
    public    $timestamps   = false;
    protected $guarded      = [];

    // ── Relasi ──────────────────────────────────────────────

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function berkas()
    {
        return $this->hasMany(Berkas::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * PENGAJUAN_KENAIKAN → BERKAS → VERIFIKASI
     * hasManyThrough karena VERIFIKASI tidak punya FK langsung ke PENGAJUAN_KENAIKAN
     */
    public function verifikasi()
    {
        return $this->hasManyThrough(
            Verifikasi::class,
            Berkas::class,
            'id_pengajuan', // FK di BERKAS → PENGAJUAN_KENAIKAN
            'id_berkas',    // FK di VERIFIKASI → BERKAS
            'id_pengajuan', // PK di PENGAJUAN_KENAIKAN
            'id_berkas'     // PK di BERKAS
        );
    }

    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'target_jabfung', 'id_jabfung');
    }

    public function pangkatGolongan()
    {
        return $this->belongsTo(PangkatGolongan::class, 'target_panggol', 'id_panggol');
    }

    // ── Accessor: tanggal pengajuan dari verifikasi pertama ──

    public function getTanggalPengajuanAttribute(): ?string
    {
        $v = $this->verifikasi->sortBy('id_verifikasi')->first();
        return $v?->tanggal_pengajuan?->format('Y-m-d');
    }

    // ── Accessor: berkas_bermasalah dari verifikasi terakhir ─

    public function getBerkasBermasalahAttribute(): array
    {
        $v = $this->verifikasi->sortByDesc('id_verifikasi')->first();
        return $v?->berkas_bermasalah ?? [];
    }

    // ── Accessor: status_info untuk badge di blade ───────────

    public function getStatusInfoAttribute(): array
    {
        return match ($this->status_pengajuan) {
            'menunggu'          => ['label' => 'Menunggu Diproses',    'class' => 'badge-menunggu',    'icon' => 'bi-hourglass-split'],
            'verifikasi'        => ['label' => 'Sedang Diverifikasi',  'class' => 'badge-verifikasi',  'icon' => 'bi-search'],
            'persetujuan'       => ['label' => 'Menunggu Persetujuan', 'class' => 'badge-persetujuan', 'icon' => 'bi-clock-history'],
            'disetujui'         => ['label' => 'Disetujui',            'class' => 'badge-disetujui',   'icon' => 'bi-check-circle-fill'],
            'tolak_verifikasi'  => ['label' => 'Ditolak Verifikasi',   'class' => 'badge-ditolak',     'icon' => 'bi-x-circle-fill'],
            'tolak_persetujuan' => ['label' => 'Ditolak Persetujuan',  'class' => 'badge-ditolak',     'icon' => 'bi-x-circle-fill'],
            default             => ['label' => $this->status_pengajuan,'class' => 'badge-menunggu',    'icon' => 'bi-circle'],
        };
    }
}