<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'revisi' ? 'Revisi' : 'Edit' }} Pengajuan Pangkat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/FormPangkatdanGolongan.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container-fluid">
<div class="row min-vh-100">

    {{-- Sidebar --}}
    <div class="col-lg-auto sidebar px-0 d-none d-lg-flex flex-column">
        <div>
            <h4>Sistem Informasi Kepegawaian</h4>
            <a href="#"><i class="bi bi-envelope-paper-fill me-2"></i>Pengajuan Surat Tugas</a>
            <a href="#"><i class="bi bi-person-badge me-2"></i>Data Diri</a>
            <a href="{{ route('pangkat-golongan.index') }}" class="active">
                <i class="bi bi-award me-2"></i>Data Pangkat Golongan
            </a>
            <a href="#"><i class="bi bi-briefcase-fill me-2"></i>Data Jabatan Fungsional</a>
        </div>
        <div class="mt-auto mb-3">
            <a href="#"><img src="{{ asset('pfp.jpg') }}" width="30" class="rounded-circle"> Profile</a>
            <a href="#" class="keluar"><i class="bi bi-box-arrow-left me-2"></i>Keluar</a>
        </div>
    </div>

    {{-- Main --}}
    <div class="col main">
        <div class="w-100">
            <div class="container mt-4 mb-5" style="max-width:700px;">

                {{-- Breadcrumb --}}
                <nav class="mb-3">
                    <a href="{{ route('pangkat-golongan.index') }}" class="text-muted text-decoration-none" style="font-size:0.85rem;">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </nav>

                @if($mode === 'revisi')
                <div class="revisi-box mb-3 d-flex gap-2 align-items-start">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>Mode Revisi</strong> — Pengajuan ini ditolak saat verifikasi.<br>
                        @if(!empty($data->keterangan_tambahan))
                            Catatan operator: <em>{{ $data->keterangan_tambahan }}</em><br>
                        @endif
                        Perbaiki berkas atau data yang kurang, lalu kirim ulang.
                    </div>
                </div>
                @endif

                <div class="form-main-card">
                    <div class="form-header-stripe {{ $mode === 'revisi' ? 'revisi' : '' }}">
                        <div class="fh-icon">
                            <i class="bi {{ $mode === 'revisi' ? 'bi-arrow-repeat' : 'bi-pencil-square' }}"></i>
                        </div>
                        <div>
                            <h5>{{ $mode === 'revisi' ? 'Revisi Pengajuan Pangkat' : 'Edit Pengajuan Pangkat' }}</h5>
                            <p>ID #{{ $data->id_pengajuan }} — {{ $mode === 'revisi' ? 'Perbaiki dan kirim ulang ke operator' : 'Edit dan kirim ulang ke operator' }}</p>
                        </div>
                    </div>

                    <div class="p-4">
                        <form method="POST" action="{{ route('pangkat-golongan.update', $data->id_pengajuan) }}"
                              enctype="multipart/form-data" id="formEdit">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="mode" value="{{ $mode }}">

                            {{-- Data Pegawai --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <p class="fw-bold text-muted mb-3" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">
                                    Data Pegawai
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $data->nama_lengkap }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">NIP / NIDN</label>
                                        <input type="text" class="form-control"
                                               value="{{ $data->nip ?? $data->nidn ?? '-' }}" readonly>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="pangkat-info-box">
                                        <i class="bi bi-award me-1"></i>
                                        Pangkat saat ini:
                                        <strong>
                                            @if($data->id_panggol)
                                                {{ $data->jenis_pangkat_skrg }}
                                            @else
                                                Belum ada
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Pengajuan --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <p class="fw-bold text-muted mb-3" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">
                                    Data Pengajuan
                                </p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label-custom">
                                            <i class="bi bi-award me-1 text-danger"></i>Pangkat / Golongan yang Diajukan
                                        </label>
                                        <select name="target_panggol" id="selectPangkat" class="form-select" required>
                                            <option value="">-- Pilih Pangkat --</option>
                                            @foreach($semuaPangkat as $p)
                                                <option value="{{ $p->id_panggol }}"
                                                        @if($p->id_panggol == $data->target_panggol) selected @endif>
                                                    {{ $p->jenis_pangkat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label-custom">
                                            <i class="bi bi-hash me-1 text-danger"></i>Nomor SK / Usulan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nomor_usulan" class="form-control"
                                               value="{{ $data->keterangan_tambahan ?? '' }}"
                                               placeholder="Contoh: 821.22/001/2026" required>
                                        <small class="text-muted">Wajib diisi.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Berkas --}}
                            <div class="mb-4">
                                <p class="fw-bold text-muted mb-3" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">
                                    Berkas Pendukung
                                </p>
                                <div class="berkas-section">
                                    <div class="berkas-title">
                                        <i class="bi bi-paperclip me-2 text-danger"></i>Kosongkan jika tidak ingin mengganti file
                                    </div>

                                    @php
                                    $berkasConfig = [
                                        ['key' => 'sk_cpns',   'label' => 'SK CPNS',   'desc' => 'SK pengangkatan sebagai CPNS',              'wajib' => true,  'max' => 5],
                                        ['key' => 'sk_pns',    'label' => 'SK PNS',    'desc' => 'SK pengangkatan sebagai PNS penuh',         'wajib' => true,  'max' => 5],
                                        ['key' => 'pak',       'label' => 'PAK',       'desc' => 'Penetapan Angka Kredit jabatan fungsional', 'wajib' => true,  'max' => 5],
                                        ['key' => 'publikasi', 'label' => 'Publikasi', 'desc' => 'Karya ilmiah / publikasi pendukung',        'wajib' => false, 'max' => 10],
                                    ];
                                    @endphp

                                    @foreach($berkasConfig as $bc)
                                        @php $fileAda = $berkasAda[$bc['key']] ?? null; @endphp
                                        <div class="berkas-item">
                                            <label>
                                                <i class="bi bi-file-earmark-pdf {{ $bc['wajib'] ? 'text-danger' : '' }} me-1"></i>
                                                {{ $bc['label'] }}
                                                <span class="berkas-badge {{ $bc['wajib'] ? '' : 'optional' }}">
                                                    {{ $bc['wajib'] ? 'Wajib' : 'Opsional' }}
                                                </span>
                                            </label>
                                            <div class="berkas-desc">{{ $bc['desc'] }}</div>
                                            @if($fileAda)
                                                <a href="{{ $fileAda['file_path'] }}" target="_blank" class="file-existing d-inline-flex mb-2">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <span>{{ $fileAda['nama_berkas'] }}</span>
                                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            @endif
                                            <input type="file" name="{{ $bc['key'] }}" class="form-control"
                                                   accept="application/pdf" id="file-{{ $bc['key'] }}">
                                            <small class="text-muted">
                                                PDF, maks. {{ $bc['max'] }}MB{{ $fileAda ? ' — kosongkan jika tidak diganti' : '' }}
                                            </small>
                                            <div id="err-{{ $bc['key'] }}" class="text-danger mt-1" style="font-size:0.78rem;display:none;"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tombol --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a href="{{ route('pangkat-golongan.index') }}" class="btn-batal btn">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="button"
                                        class="{{ $mode === 'revisi' ? 'btn-revisi' : 'btn-kirim' }} btn"
                                        onclick="submitFormEdit()">
                                    <i class="bi {{ $mode === 'revisi' ? 'bi-arrow-repeat' : 'bi-send' }} me-2"></i>
                                    {{ $mode === 'revisi' ? 'Kirim Revisi' : 'Kirim Ulang' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/dosen/data_pangkat_golongan/UpdatePangkatdanGolongan.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', initEditForm);
</script>
</body>
</html>