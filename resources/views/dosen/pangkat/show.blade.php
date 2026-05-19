<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan #{{ $data->id_pengajuan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/style.css') }}">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #fff; }
        .detail-header { background: linear-gradient(135deg, #b91c1c, #7f1d1d); padding: 24px 28px; color: #fff; display: flex; align-items: center; gap: 16px; }
        .dh-icon { background: rgba(255,255,255,0.15); border-radius: 12px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .detail-header h5 { margin: 0; font-weight: 700; font-size: 1rem; }
        .detail-header p  { margin: 0; font-size: 0.78rem; opacity: 0.8; }
        .section-title { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; font-weight: 700; margin-bottom: 12px; }
        .info-row { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.4px; color: #9ca3af; font-weight: 600; margin-bottom: 2px; }
        .info-value { font-size: 0.9rem; font-weight: 500; color: #111827; margin: 0; }
        .status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; padding: 5px 14px; border-radius: 20px; font-weight: 600; }
        .berkas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
        .berkas-card { border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 14px 10px; text-align: center; text-decoration: none; transition: all 0.2s; display: block; background: #fff; }
        .berkas-card:hover { border-color: #b91c1c; box-shadow: 0 2px 10px rgba(185,28,28,0.1); transform: translateY(-2px); }
        .berkas-card.missing { opacity: 0.4; pointer-events: none; border-style: dashed; }
        .berkas-card .bc-icon { font-size: 1.8rem; margin-bottom: 6px; }
        .berkas-card .bc-label { font-weight: 700; font-size: 0.75rem; color: #374151; }
        .berkas-card .bc-sub { font-size: 0.68rem; margin-top: 3px; }
        .bc-sub.ada    { color: #059669; }
        .bc-sub.kosong { color: #9ca3af; }
        .status-banner { border-radius: 10px; padding: 12px 16px; font-size: 0.85rem; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-start; }
        .status-banner.tolak  { background: #fef2f2; border-left: 4px solid #dc2626; color: #7f1d1d; }
        .status-banner.setuju { background: #f0fdf4; border-left: 4px solid #16a34a; color: #14532d; }
        .status-banner.proses { background: #fffbeb; border-left: 4px solid #f59e0b; color: #78350f; }
    </style>
</head>
<body>

<div class="detail-header">
    <div class="dh-icon"><i class="bi bi-file-earmark-text"></i></div>
    <div>
        <h5>Detail Pengajuan Pangkat &amp; Golongan</h5>
        <p>ID #{{ $data->id_pengajuan }} &nbsp;·&nbsp; {{ $data->nama_lengkap }}</p>
    </div>
</div>

<div class="p-4">

    {{-- Status banner --}}
    @if($data->status_pengajuan === 'REJECTED')
    <div class="status-banner tolak">
        <i class="bi bi-x-circle-fill flex-shrink-0 mt-1"></i>
        <div>
            <strong>Pengajuan Ditolak</strong><br>
            @if(!empty($data->keterangan_tambahan))
                Catatan: {{ $data->keterangan_tambahan }}
            @endif
        </div>
    </div>
    @elseif($data->status_pengajuan === 'APPROVED')
    <div class="status-banner setuju">
        <i class="bi bi-check-circle-fill flex-shrink-0 mt-1"></i>
        <strong>Pengajuan Disetujui</strong>
    </div>
    @elseif($data->status_pengajuan === 'PENDING')
    <div class="status-banner proses">
        <i class="bi bi-hourglass-split flex-shrink-0 mt-1"></i>
        <strong>Sedang Diproses</strong> — Menunggu verifikasi operator
    </div>
    @endif

    <div class="row g-4">

        {{-- Kiri: Info Pengajuan --}}
        <div class="col-md-6">
            <div class="section-title"><i class="bi bi-info-circle me-1"></i>Info Pengajuan</div>

            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <p class="info-value">{{ $data->nama_lengkap }}</p>
            </div>
            <div class="info-row">
                <div class="info-label">NIP / NIDN</div>
                <p class="info-value">{{ $data->nip ?? $data->nidn ?? '-' }}</p>
            </div>
            <div class="info-row">
                <div class="info-label">Pangkat Saat Ini</div>
                <p class="info-value">
                    @if(!empty($data->jenis_pangkat_skrg))
                        {{ $data->jenis_pangkat_skrg }}
                    @else
                        <span class="text-muted">Belum ada</span>
                    @endif
                </p>
            </div>
            <div class="info-row">
                <div class="info-label">Pangkat yang Diajukan</div>
                <p class="info-value fw-bold">{{ $data->jenis_pangkat }}</p>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor SK / Usulan</div>
                <p class="info-value">
                    @if(!empty($data->keterangan_tambahan))
                        <code>{{ $data->keterangan_tambahan }}</code>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pengajuan</div>
                <p class="info-value">{{ \Carbon\Carbon::parse($data->tanggal_pengajuan)->format('d F Y') }}</p>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                @php
                    $statusMap = [
                    'MENUNGGU'          => ['label' => 'Menunggu',            'class' => 'bg-warning text-dark',  'icon' => 'bi-hourglass-split'],
                    'VERIFIKASI'        => ['label' => 'Sedang Diverifikasi', 'class' => 'bg-info text-dark',     'icon' => 'bi-search'],
                    'PERSETUJUAN'       => ['label' => 'Menunggu Persetujuan','class' => 'bg-primary text-white', 'icon' => 'bi-person-check'],
                    'DISETUJUI'         => ['label' => 'Disetujui',           'class' => 'bg-success text-white', 'icon' => 'bi-check-circle'],
                    'TOLAK_VERIFIKASI'  => ['label' => 'Ditolak Verifikasi',  'class' => 'bg-danger text-white',  'icon' => 'bi-x-circle'],
                    'TOLAK_PERSETUJUAN' => ['label' => 'Ditolak Pimpinan',    'class' => 'bg-danger text-white',  'icon' => 'bi-x-circle'],
                ];
                    $st = $statusMap[$data->status_pengajuan] ?? ['label' => $data->status_pengajuan, 'class' => 'bg-secondary text-white', 'icon' => 'bi-circle'];
                @endphp
                <span class="badge status-pill {{ $st['class'] }}">
                    <i class="bi {{ $st['icon'] }}"></i>{{ $st['label'] }}
                </span>
            </div>
        </div>

        {{-- Kanan: Riwayat Status --}}
        <div class="col-md-6">
            <div class="section-title"><i class="bi bi-clock-history me-1"></i>Riwayat Status</div>
            <div class="p-3 rounded" style="background:#f9fafb; border: 1.5px solid #f3f4f6;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @php
                        $iconMap = ['PENDING' => 'bi-hourglass-split text-warning', 'APPROVED' => 'bi-check-circle-fill text-success', 'REJECTED' => 'bi-x-circle-fill text-danger'];
                        $icon = $iconMap[$data->status_pengajuan] ?? 'bi-circle text-secondary';
                    @endphp
                    <i class="bi {{ $icon }}" style="font-size:1.2rem;"></i>
                    <span class="fw-semibold" style="font-size:0.88rem;">{{ $st['label'] }}</span>
                </div>
                <p class="text-muted mb-0" style="font-size:0.78rem;">
                    Diajukan pada {{ \Carbon\Carbon::parse($data->tanggal_pengajuan)->format('d F Y') }}
                </p>
                @if($data->status_pengajuan === 'REJECTED' && !empty($data->keterangan_tambahan))
                <div class="mt-2 pt-2 border-top" style="font-size:0.78rem; color:#7f1d1d;">
                    <i class="bi bi-chat-left-text me-1"></i>{{ $data->keterangan_tambahan }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Berkas --}}
    <div class="mt-4">
        <div class="section-title"><i class="bi bi-paperclip me-1"></i>Berkas Pendukung</div>
        <div class="berkas-grid">
            @php
            $berkasConfig = [
                ['key' => 'sk_cpns',   'label' => 'SK CPNS',   'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                ['key' => 'sk_pns',    'label' => 'SK PNS',    'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                ['key' => 'pak',       'label' => 'PAK',        'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                ['key' => 'publikasi', 'label' => 'Publikasi',  'icon' => 'bi-file-earmark-pdf-fill text-primary'],
            ];
            @endphp

            @foreach($berkasConfig as $bc)
                @php $fileAda = $berkasAda[$bc['key']] ?? null; @endphp
                @if($fileAda)
                    <a href="{{ $fileAda['file_path'] }}" target="_blank" class="berkas-card">
                        <div class="bc-icon"><i class="bi {{ $bc['icon'] }}"></i></div>
                        <div class="bc-label">{{ $bc['label'] }}</div>
                        <div class="bc-sub ada"><i class="bi bi-check-circle me-1"></i>Klik untuk buka</div>
                    </a>
                @else
                    <div class="berkas-card missing">
                        <div class="bc-icon"><i class="bi bi-file-earmark text-muted"></i></div>
                        <div class="bc-label">{{ $bc['label'] }}</div>
                        <div class="bc-sub kosong"><i class="bi bi-dash-circle me-1"></i>Tidak ada</div>
                    </div>
                @endif
            @endforeach
        </div>

        @if($data->status_pengajuan === 'APPROVED')
        <div class="mt-3 pt-3 border-top">
            <a href="#" class="btn btn-success">
                <i class="bi bi-download me-2"></i>Download Surat
            </a>
        </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>