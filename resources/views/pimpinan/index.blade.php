@extends('layouts.app')

@section('title', 'Data Diri Pimpinan - SIMPEG UNRI')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_diri/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="main">
        <div class="header">
            <button class="btn btn-menu text-white d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMobile">
                ☰
            </button>

            <h4 class="tebal">
                <img src="{{ $pegawai->foto ? asset($pegawai->foto) : asset('assets/dosen/data_diri/pfp.jpg') }}"
                    alt="Profile"
                    style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
                Selamat Datang, {{ $pegawai->nama_lengkap }} di Sistem Informasi Kepegawaian
            </h4>

            <div class="product-card mt-3">
                <div class="container py-5" style="max-width: 950px;">
                    <div class="settings-header">
                        <h1 class="section-title">Data Diri Pimpinan</h1>
                        <p class="section-subtitle">Portal data diri pimpinan {{ $pegawai->prodi ?? 'Fakultas Teknik' }}.</p>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold mb-1" style="color: var(--unri-red);">Identitas Resmi</h5>
                        <p class="text-muted small mb-4">Data utama yang terverifikasi oleh operator kepegawaian.</p>

                        <div class="data-row">
                            <div class="data-label">NIP</div>
                            <div class="data-value">
                                <span class="locked-field"><i class="fa-solid fa-user-lock"></i>
                                    {{ $pegawai->nip ?? 'Belum ada data' }}</span>
                            </div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">NIDN</div>
                            <div class="data-value">
                                <span class="locked-field"><i class="fa-solid fa-id-card-clip"></i>
                                    {{ $pegawai->nidn ?? 'Belum ada data' }}</span>
                            </div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">Homebase</div>
                            <div class="data-value text-uppercase fw-bold">
                                <span class="locked-field"><i class="fa-solid fa-university"></i>
                                    {{ $pegawai->jurusan ?? '-' }} / {{ $pegawai->prodi ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h5 class="fw-bold m-0" style="color: var(--unri-red);">Informasi Personal</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pimpinan.pegawai.edit', $pegawai->id_pegawai) }}"
                                    class="btn btn-outline-danger btn-sm" style="color: var(--unri-red);">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('pimpinan.pegawai.create') }}" class="btn btn-danger btn-sm"
                                    style="background-color: var(--unri-red);">
                                    <i class="bi bi-plus-circle"></i> Tambah
                                </a>
                            </div>
                        </div>
                        <p class="text-muted small mb-4">Kelola nomor kontak untuk kebutuhan administrasi surat tugas.</p>

                        <div class="data-row">
                            <div class="data-label">Tanggal Lahir</div>
                            <div class="data-value">
                                {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') : 'Belum diatur' }}
                            </div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">Nomor HP Aktif</div>
                            <div class="data-value">{{ $pegawai->nomor_hp ?? 'Belum ada data' }}</div>
                        </div>
                        <div class="data-row">
                            <div class="data-label">No. HP Darurat</div>
                            <div class="data-value">
                                {{ $pegawai->nomor_hp_darurat ?? 'Belum ada data' }}
                                @if ($pegawai->nomor_hp_darurat)
                                    <span class="badge bg-secondary ms-2 opacity-75"
                                        style="font-size: 0.7rem;">Darurat</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection