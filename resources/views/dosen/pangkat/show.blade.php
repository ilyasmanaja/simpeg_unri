@extends('layouts.app')

@section('title', 'Detail Pengajuan Pangkat')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/style.css') }}">
    <style>
        .detail-header { background: linear-gradient(135deg, #b91c1c, #7f1d1d); padding: 24px 28px; color: #fff; display: flex; align-items: center; gap: 16px; border-radius: 16px; margin-bottom: 24px; }
        .dh-icon { background: rgba(255,255,255,0.15); border-radius: 12px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
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
        .bc-sub.ada { color: #059669; }
        .bc-sub.kosong { color: #9ca3af; }
        .status-banner { border-radius: 10px; padding: 12px 16px; font-size: 0.85rem; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-start; }
        .status-banner.tolak  { background: #fef2f2; border-left: 4px solid #dc2626; color: #7f1d1d; }
        .status-banner.setuju { background: #f0fdf4; border-left: 4px solid #16a34a; color: #14532d; }
        .status-banner.proses { background: #fffbeb; border-left: 4px solid #f59e0b; color: #78350f; }
    </style>
@endpush

@section('content')
<div class="main">
    <div class="main-inner" style="padding-top: 32px;">
        <div class="container" style="max-width: 900px;">

            {{-- Breadcrumb --}}
            <nav class="mb-3">
                <a href="{{ route('dosen.pangkat-golongan.index') }}" class="text-muted text-decoration-none" style="font-size:0.85rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </nav>

            <div class="detail-header">
                <div class="dh-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <h5>Detail Pengajuan Pangkat &amp; Golongan</h5>
                    <p>ID #{{ $data->id_pengajuan }} &nbsp;·&nbsp; {{ $data->nama_lengkap }}</p>
                </div>
            </div>

            {{-- Status Banner --}}
            @if($data->status_pengajuan === 'REJECTED')
                <div class="status-banner tolak">
                    <i class="bi bi-x-circle-fill flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>Pengajuan Ditolak</strong><br>
                        @if(!empty($data->keterangan_tambahan)) Catatan: {{ $data->keterangan_tambahan }} @endif
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

            <div class="row g-4 mb-4">
                {{-- Info Pengajuan --}}
                <div class="col-md-6">
                    <div class="section-title"><i class="bi bi-info-circle me-1"></i>Info Pengajuan</div>
                    <div class="info-row"><div class="info-label">Nama Lengkap</div><p class="info-value">{{ $data->nama_lengkap }}</p></div>
                    <div class="info-row"><div class="info-label">NIP / NIDN</div><p class="info-value">{{ $data->nip ?? $data->nidn ?? '-' }}</p></div>
                    <div class="info-row"><div class="info-label">Pangkat yang Diajukan</div><p class="info-value fw-bold">{{ $data->jenis_pangkat }}</p></div>
                    <div class="info-row"><div class="info-label">Tanggal Pengajuan</div><p class="info-value">{{ \Carbon\Carbon::parse($data->tanggal_pengajuan)->format('d F Y') }}</p></div>
                </div>

                {{-- Berkas Grid --}}
                <div class="col-md-6">
                    <div class="section-title"><i class="bi bi-paperclip me-1"></i>Berkas Pendukung</div>
                    <div class="berkas-grid">
                        @php
                        $berkasConfig = [
                            ['key' => 'sk_cpns',   'label' => 'SK CPNS',   'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                            ['key' => 'sk_pns',    'label' => 'SK PNS',    'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                            ['key' => 'pak',       'label' => 'PAK',       'icon' => 'bi-file-earmark-pdf-fill text-danger'],
                            ['key' => 'publikasi', 'label' => 'Publikasi', 'icon' => 'bi-file-earmark-pdf-fill text-primary'],
                        ];
                        @endphp
                        @foreach($berkasConfig as $bc)
                            @php $fileAda = $berkasAda[$bc['key']] ?? null; @endphp
                            @if($fileAda)
                                <a href="{{ $fileAda['file_path'] }}" target="_blank" class="berkas-card">
                                    <div class="bc-icon"><i class="bi {{ $bc['icon'] }}"></i></div>
                                    <div class="bc-label">{{ $bc['label'] }}</div>
                                    <div class="bc-sub ada"><i class="bi bi-check-circle me-1"></i>Buka File</div>
                                </a>
                            @else
                                <div class="berkas-card missing">
                                    <div class="bc-icon"><i class="bi bi-file-earmark text-muted"></i></div>
                                    <div class="bc-label">{{ $bc['label'] }}</div>
                                    <div class="bc-sub kosong">Tidak ada</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            @if($data->status_pengajuan === 'APPROVED')
                <a href="#" class="btn btn-success" style="border-radius:10px;"><i class="bi bi-download me-2"></i>Download Surat</a>
            @endif
        </div>
    </div>
</div>
@endsection