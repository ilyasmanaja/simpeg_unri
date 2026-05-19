@extends('template.main')

@section('in', 'active')

@section('konten')
<div class="header">
    <h4 class="tebal">
        <img src="{{ asset('pfp.jpg') }}" alt=""> Selamat Datang, {{ Auth::user()->nama ?? 'User' }} di Sistem Informasi Kepegawaian
    </h4>
</div>

<div class="card-1 mt-3">
    <div class="container py-5" style="max-width: 950px;">
        <div class="settings-header">
            <h1 class="section-title">Data Diri</h1>
            <p class="section-subtitle">Portal data diri Teknik Informatika - Fakultas Teknik.</p>
        </div>

        {{-- Profile Header Section --}}
        <div class="profile-header-card mb-5 p-4 rounded-3 d-flex align-items-center gap-4" style="background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%); border: 1px solid #f0d0d0; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; right: 0; width: 200px; height: 100%; background: linear-gradient(135deg, transparent, rgba(206,45,45,0.04)); pointer-events: none;"></div>

            {{-- Avatar --}}
            <div class="profile-avatar-wrapper" style="position: relative; flex-shrink: 0;">
                <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 3px solid #CE2D2D; box-shadow: 0 4px 15px rgba(206,45,45,0.2);">
                    <img src="{{ asset('pfp.jpg') }}" alt="Foto Profil"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap ?? 'User') }}&background=CE2D2D&color=fff&size=90';">
                </div>
                {{-- Online indicator --}}
                <span style="position: absolute; bottom: 4px; right: 4px; width: 16px; height: 16px; background: #28a745; border-radius: 50%; border: 2px solid #fff; display: block;"></span>
            </div>

            {{-- Name & Info --}}
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-bold text-truncate" style="color: #1a1a2e; font-size: 1.35rem; letter-spacing: 0.3px;">
                        {{ strtoupper($pegawai->nama_lengkap ?? Auth::user()->nama ?? 'User') }}
                    </h3>
                    {{-- Verified badge --}}
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Terverifikasi">
                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#CE2D2D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <span class="text-muted small d-flex align-items-center gap-1">
                        <i class="fa-solid fa-id-badge" style="color: #CE2D2D;"></i>
                        {{ $pegawai->nik ?? '-' }}
                    </span>
                    <span class="text-muted small d-flex align-items-center gap-1">
                        <i class="fa-solid fa-building-columns" style="color: #CE2D2D;"></i>
                        Teknik Informatika
                    </span>
                    <span class="text-muted small d-flex align-items-center gap-1">
                        <i class="fa-solid fa-envelope" style="color: #CE2D2D;"></i>
                        {{ Auth::user()->email ?? '-' }}
                    </span>
                </div>
                <div class="mt-2">
                    <span class="badge" style="background: rgba(206,45,45,0.12); color: #CE2D2D; font-size: 0.72rem; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                        {{ Auth::user()->role ?? 'Pegawai' }}
                    </span>
                </div>
            </div>
        </div>
        {{-- End Profile Header --}}

        <div class="mb-5">
            <h5 class="fw-bold mb-1" style="color: var(--unri-red);">Identitas Resmi</h5>
            <p class="text-muted small mb-4">Data utama yang terverifikasi oleh operator kepegawaian.</p>

            <div class="data-row">
                <div class="data-label">NIK</div>
                <div class="data-value">
                    <span class="locked-field"><i class="fa-solid fa-user-lock"></i> {{ $pegawai->nik ?? '-' }}</span>
                </div>
            </div>

            @if($pegawai->status_pegawai == 'PNS')
                <div class="data-row">
                    <div class="data-label">NIP</div>
                    <div class="data-value">
                        <span class="locked-field"><i class="fa-solid fa-user-lock"></i> {{ $pegawai->nip ?? '-' }}</span>
                    </div>
                </div>

            @if(optional(Auth::user())->role == 'Dosen')
                <div class="data-row">
                    <div class="data-label">NIDN</div>
                    <div class="data-value">
                        <span class="locked-field">
                            <i class="fa-solid fa-id-card-clip"></i> {{ $pegawai->nidn ?? '-' }}
                        </span>
                    </div>
                </div>
            @endif

                <div class="data-row">
                    <div class="data-label">Homebase</div>
                    <div class="data-value text-uppercase fw-bold">
                        <span class="locked-field"><i class="fa-solid fa-university"></i> Teknik Informatika</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="fw-bold m-0" style="color: var(--unri-red);">Informasi Personal</h5>

                <div class="d-flex gap-2">
                    {{-- Ganti Password Button --}}
                    <a href="{{ route('pegawai.password.form', $pegawai->id_pegawai) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-lock"></i> Ganti Password
                    </a>

                    @if(empty($pegawai->nomor_hp) || empty($pegawai->nomor_hp_darurat) || empty($pegawai->alamat))
                        <a href="{{ route('pegawai.edit', $pegawai->id_pegawai) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-plus-circle"></i> Lengkapi Biodata
                        </a>
                    @else
                        <a href="{{ route('pegawai.edit', $pegawai->id_pegawai) }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-pencil"></i> Edit Biodata
                        </a>
                    @endif
                </div>
            </div>
            <p class="text-muted small mb-4">Kelola nomor kontak untuk kebutuhan administrasi.</p>

            <div class="data-row">
                <div class="data-label">Tanggal Lahir</div>
                <div class="data-value">{{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Nomor HP Aktif</div>
                <div class="data-value">{{ $pegawai->nomor_hp ?? 'Belum diisi' }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">No. HP Darurat</div>
                <div class="data-value">{{ $pegawai->nomor_hp_darurat ?? 'Belum diisi' }}</div>
            </div>
            <div class="data-row">
                <div class="data-label">Alamat</div>
                <div class="data-value">{{ $pegawai->alamat ?? 'Belum diisi' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection