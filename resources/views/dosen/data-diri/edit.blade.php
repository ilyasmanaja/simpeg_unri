@extends('layouts.app')

@section('title', 'Edit Data Diri Pegawai')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datadiri.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="main">
    <div class="main-inner" style="padding-top: 32px;">
        <div class="container mb-5" style="max-width: 720px;">

            {{-- Breadcrumb / Tombol Kembali --}}
            <nav class="mb-3">
                <a href="{{ route('dosen.pegawai.show', $pegawai->id_pegawai) }}"
                   class="text-muted text-decoration-none" style="font-size:0.85rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Data Diri
                </a>
            </nav>

            <div class="form-main-card">
                {{-- Header stripe persis seperti Jabfung --}}
                <div class="form-header-stripe">
                    <div class="fh-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h5>Edit Data Diri</h5>
                        <p>Ubah data kontak dan informasi personal Anda</p>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('dosen.pegawai.update', $pegawai->id_pegawai) }}" method="POST" id="formEditPegawai" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- ── IDENTITAS RESMI (READONLY) ── --}}
                        <div class="mb-3 pb-3 border-bottom">
                            <p class="section-label">Identitas Resmi Terkunci</p>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">NIK</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->nik ?? '-' }}" readonly>
                                </div>
                                @if($pegawai->status_pegawai == 'PNS')
                                <div class="col-md-6">
                                    <label class="form-label-custom">NIP</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->nip ?? '-' }}" readonly>
                                </div>
                                @endif
                            </div>

                            <div class="row g-3 mb-3">
                                @if($pegawai->status_pegawai == 'PNS' && Auth::user()->jenis_role == 'Dosen')
                                <div class="col-md-6">
                                    <label class="form-label-custom">NIDN</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->nidn ?? '-' }}" readonly>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label-custom">Tanggal Lahir</label>
                                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}" readonly>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label-custom">Jenis Kelamin</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : ($pegawai->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Jurusan</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->jurusan ?? '-' }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Prodi</label>
                                    <input type="text" class="form-control" value="{{ $pegawai->prodi ?? '-' }}" readonly>
                                </div>
                            </div>
                            
                            <div class="info-box blue mt-2">
                                <i class="bi bi-info-circle flex-shrink-0 mt-1"></i>
                                <div>
                                    Data identitas resmi di atas bersifat <strong>Read-Only</strong> (terkunci). Jika terdapat kesalahan, silakan hubungi Operator Kepegawaian.
                                </div>
                            </div>
                        </div>

                        {{-- ── KONTAK PERSONAL (EDITABLE) ── --}}
                        <div class="mb-4">
                            <p class="section-label">Kontak Personal</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nomor_hp" class="form-label-custom">
                                        <i class="bi bi-telephone-fill me-1 text-danger"></i>No HP Aktif
                                    </label>
                                    <input type="text" name="nomor_hp" id="nomor_hp"
                                           class="form-control"
                                           value="{{ old('nomor_hp', $pegawai->nomor_hp) }}"
                                           required
                                           placeholder="Contoh: 081234567890">
                                    <div class="invalid-feedback" style="font-size:0.78rem;">No HP aktif wajib diisi!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_hp_darurat" class="form-label-custom">
                                        <i class="bi bi-telephone-plus-fill me-1 text-danger"></i>No HP Darurat
                                    </label>
                                    <input type="text" name="nomor_hp_darurat" id="nomor_hp_darurat"
                                           class="form-control"
                                           value="{{ old('nomor_hp_darurat', $pegawai->nomor_hp_darurat) }}"
                                           required
                                           placeholder="Contoh: 089876543210">
                                    <div class="invalid-feedback" style="font-size:0.78rem;">No HP darurat wajib diisi!</div>
                                </div>
                            </div>
                        </div>

                        {{-- ── TOMBOL AKSI ── --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                            <a href="{{ route('dosen.pegawai.show', $pegawai->id_pegawai) }}" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; padding: 10px 24px;">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn-submission">
                                <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    document.getElementById('formEditPegawai').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = this;

        // Validasi HTML5 bawaan browser
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // SweetAlert Konfirmasi
        Swal.fire({
            title: "Simpan perubahan?",
            text: "Data kontak Anda akan diperbarui ke sistem.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Simpan!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#CE2D2D"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush