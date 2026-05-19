<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Pengajuan Pangkat dan Golongan</title>
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

                @if($adaPending)
                <div class="alert alert-warning alert-pending d-flex gap-2 align-items-start mb-3">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <div><strong>Perhatian:</strong> Kamu masih memiliki pengajuan yang sedang diproses.</div>
                </div>
                @endif

                <div class="form-main-card">
                    <div class="form-header-stripe">
                        <div class="fh-icon"><i class="bi bi-file-earmark-plus"></i></div>
                        <div>
                            <h5>Form Pengajuan Pangkat dan Golongan</h5>
                            <p>Isi data dengan lengkap dan benar sesuai dokumen resmi</p>
                        </div>
                    </div>

                    <div class="p-4">
                        <form action="{{ route('pangkat-golongan.store') }}" method="POST"
                              enctype="multipart/form-data" id="formPanggol">
                            @csrf
                            <input type="hidden" name="id_pegawai" value="{{ $pegawai->id_pegawai }}">
                            <input type="hidden" name="form_action" id="form-action" value="">

                            {{-- Data Pegawai --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <p class="fw-bold text-muted mb-3" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">
                                    Data Pegawai
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Nama Lengkap</label>
                                        <input type="text" class="form-control"
                                               value="{{ $pegawai->nama_lengkap }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">{{ $identitas_label }}</label>
                                        <input type="text" class="form-control"
                                            value="{{ $identitas_value }}" readonly>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="pangkat-info-box">
                                        <i class="bi bi-award me-1"></i>
                                        Pangkat saat ini:
                                        <strong>
                                            @if($pegawai->id_panggol)
                                                {{ $pegawai->jenis_pangkat }}
                                            @else
                                                Belum ada pangkat
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

                                {{-- Alert konflik jabfung --}}
                                <div id="jabfung-alert" class="alert alert-warning d-none mb-3" style="font-size:0.85rem;">
                                    <span id="jabfung-alert-msg"></span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label-custom">
                                            <i class="bi bi-award me-1 text-danger"></i>
                                            Pangkat / Golongan yang Diajukan
                                        </label>
                                        <select name="target_panggol" id="selectPangkat" class="form-select" required>
                                            <option value="">-- Pilih Pangkat dan Golongan --</option>
                                            @foreach($semuaPangkat as $p)
                                                @if($p->lebihRendah)
                                                    <option value="{{ $p->id_panggol }}"
                                                            data-urutan="{{ $p->urutan ?? 0 }}"
                                                            disabled style="color:#9ca3af;">
                                                        — {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} (sudah dimiliki/lebih rendah)
                                                    </option>
                                                @elseif($p->bisa)
                                                    <option value="{{ $p->id_panggol }}"
                                                            data-urutan="{{ $p->urutan ?? 0 }}"
                                                            selected>
                                                        {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} ✓ Dapat diajukan
                                                    </option>
                                                @else
                                                    <option value="{{ $p->id_panggol }}"
                                                            data-urutan="{{ $p->urutan ?? 0 }}"
                                                            disabled style="color:#9ca3af;">
                                                        {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} (belum bisa diajukan)
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label-custom">
                                            <i class="bi bi-hash me-1 text-danger"></i>Nomor SK / Usulan
                                        </label>
                                        <input type="text" name="nomor_usulan" class="form-control"
                                               placeholder="Contoh: 821.22/001/2026">
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Berkas --}}
                            <div class="mb-4">
                                <p class="fw-bold text-muted mb-3" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">
                                    Upload Berkas Pendukung
                                </p>
                                <div class="berkas-section">
                                    <div class="berkas-title">
                                        <i class="bi bi-paperclip me-2 text-danger"></i>Dokumen Wajib & Pendukung
                                    </div>

                                    {{-- SK CPNS --}}
                                    <div class="berkas-item">
                                        <label>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            SK CPNS <span class="berkas-badge">Wajib</span>
                                        </label>
                                        <div class="berkas-desc">Surat Keputusan pengangkatan sebagai Calon Pegawai Negeri Sipil</div>
                                        <input type="file" name="sk_cpns" class="form-control" accept="application/pdf" id="file-sk-cpns">
                                        <small class="text-muted">PDF, maks. 5MB</small>
                                        <div id="err-sk-cpns" class="text-danger mt-1" style="font-size:0.82rem;display:none;"></div>
                                    </div>

                                    {{-- SK PNS --}}
                                    <div class="berkas-item">
                                        <label>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            SK PNS <span class="berkas-badge">Wajib</span>
                                        </label>
                                        <div class="berkas-desc">Surat Keputusan pengangkatan sebagai Pegawai Negeri Sipil penuh</div>
                                        <input type="file" name="sk_pns" class="form-control" accept="application/pdf" id="file-sk-pns">
                                        <small class="text-muted">PDF, maks. 5MB</small>
                                        <div id="err-sk-pns" class="text-danger mt-1" style="font-size:0.82rem;display:none;"></div>
                                    </div>

                                    {{-- PAK --}}
                                    <div class="berkas-item">
                                        <label>
                                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                            PAK (Penetapan Angka Kredit) <span class="berkas-badge">Wajib</span>
                                        </label>
                                        <div class="berkas-desc">Dokumen penetapan angka kredit dari pejabat berwenang</div>
                                        <input type="file" name="pak" class="form-control" accept="application/pdf" id="file-pak">
                                        <small class="text-muted">PDF, maks. 5MB</small>
                                        <div id="err-pak" class="text-danger mt-1" style="font-size:0.82rem;display:none;"></div>
                                    </div>

                                    {{-- Publikasi --}}
                                    <div class="berkas-item">
                                        <label>
                                            <i class="bi bi-file-earmark-pdf me-1" style="color:#0369a1;"></i>
                                            Publikasi / Karya Ilmiah <span class="berkas-badge optional">Opsional</span>
                                        </label>
                                        <div class="berkas-desc">Bukti publikasi jurnal, prosiding, atau karya ilmiah pendukung</div>
                                        <input type="file" name="publikasi" class="form-control" accept="application/pdf" id="file-publikasi">
                                        <small class="text-muted">PDF, maks. 10MB</small>
                                        <div id="err-publikasi" class="text-danger mt-1" style="font-size:0.82rem;display:none;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a href="{{ route('pangkat-golongan.index') }}" class="btn-batal btn">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="button" class="btn-ajukan btn" id="btnAjukan"
                                        onclick="submitForm('ajukan')"
                                        @if($adaPending) disabled title="Ada pengajuan yang masih diproses" @endif>
                                    <i class="bi bi-send me-2"></i>Ajukan
                                    @if($adaPending)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:0.68rem;">Terkunci</span>
                                    @endif
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

{{-- Inject variabel dari PHP ke JS --}}
<script>
    const jabfungSekarang = "{{ $pegawai->jabfung ?? '' }}";
</script>

<script src="{{ asset('assets/dosen/data_pangkat_golongan/FormPangkatdanGolongan.js') }}"></script>
</body>
</html>