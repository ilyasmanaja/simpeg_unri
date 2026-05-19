@extends('layouts.app')

@section('title', 'Ganti Password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datadiri.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .toggle-pw {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            opacity: 0.5;
            user-select: none;
            z-index: 5;
            transition: opacity 0.2s, color 0.2s;
        }
        .toggle-pw:hover {
            opacity: 0.85;
        }
        .toggle-pw.active {
            opacity: 1;
            color: var(--unri-red);
        }
    </style>
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

            {{-- Alert Notifikasi --}}
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="form-main-card">
                {{-- Header stripe --}}
                <div class="form-header-stripe">
                    <div class="fh-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <h5>Ganti Password</h5>
                        <p>Kelola keamanan akun Sistem Informasi Kepegawaian Anda</p>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('dosen.pegawai.password.update', $pegawai->id_pegawai) }}" method="POST" id="formGantiPassword" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- ── PASSWORD LAMA ── --}}
                        <div class="mb-4">
                            <label for="password_lama" class="form-label-custom">
                                Password Lama <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password_lama" id="password_lama"
                                       class="form-control @error('password_lama') is-invalid @enderror"
                                       placeholder="Masukkan password lama Anda" required style="padding-right: 2.5rem;">
                                <span class="toggle-pw" onclick="togglePassword('password_lama', this)" title="Tampilkan/sembunyikan password">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                                @error('password_lama')
                                    <div class="invalid-feedback" style="font-size:0.78rem;">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback" style="font-size:0.78rem;">Password lama wajib diisi!</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="mb-4" style="border-top: 1px dashed #e5e7eb; background: none;">

                        {{-- ── PASSWORD BARU ── --}}
                        <div class="mb-3">
                            <label for="password_baru" class="form-label-custom">
                                <i class="bi bi-key-fill me-1 text-danger"></i>Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password_baru" id="password_baru"
                                       class="form-control @error('password_baru') is-invalid @enderror"
                                       placeholder="Buat password baru" required minlength="8" style="padding-right: 2.5rem;">
                                <span class="toggle-pw" onclick="togglePassword('password_baru', this)" title="Tampilkan/sembunyikan password">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                                @error('password_baru')
                                    <div class="invalid-feedback" style="font-size:0.78rem;">{{ $message }}</div>
                                @else
                                    <div class="invalid-feedback" style="font-size:0.78rem;">Password baru wajib diisi (minimal 8 karakter)!</div>
                                @enderror
                            </div>
                            <div class="form-text text-muted mt-2" style="font-size: 0.78rem;">
                                <i class="bi bi-info-circle me-1"></i> Minimal 8 karakter.
                            </div>
                        </div>

                        {{-- ── KONFIRMASI PASSWORD ── --}}
                        <div class="mb-4">
                            <label for="password_konfirmasi" class="form-label-custom">
                                Ulangi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password_konfirmasi" id="password_konfirmasi"
                                       class="form-control" placeholder="Tulis ulang password baru" required style="padding-right: 2.5rem;">
                                <span class="toggle-pw" onclick="togglePassword('password_konfirmasi', this)" title="Tampilkan/sembunyikan password">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                                <div class="invalid-feedback" id="konfirmasi-feedback" style="font-size:0.78rem;">Konfirmasi password wajib diisi!</div>
                            </div>
                        </div>

                        {{-- ── TOMBOL AKSI ── --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top">
                            <a href="{{ route('dosen.pegawai.show', $pegawai->id_pegawai) }}" class="btn btn-secondary" style="border-radius: 10px; font-weight: 600; padding: 10px 24px;">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn-submission">
                                <i class="bi bi-floppy me-1"></i> Simpan Password
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
    // Toggle password visibility & icon switch
    function togglePassword(fieldId, span) {
        const input = document.getElementById(fieldId);
        const icon = span.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            span.classList.add('active');
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            span.classList.remove('active');
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }

    // Form submit with SweetAlert confirmation + password match validation
    document.getElementById('formGantiPassword').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = this;

        const passwordBaru = document.getElementById('password_baru').value;
        const passwordKonfirmasi = document.getElementById('password_konfirmasi').value;
        const konfirmasiField = document.getElementById('password_konfirmasi');
        const konfirmasiFeedback = document.getElementById('konfirmasi-feedback');

        // Reset custom validity
        konfirmasiField.setCustomValidity('');

        // Check password match
        if (passwordBaru !== passwordKonfirmasi) {
            konfirmasiField.setCustomValidity('Tidak cocok');
            konfirmasiFeedback.textContent = 'Password baru dan konfirmasi tidak cocok!';
            form.classList.add('was-validated');
            event.stopPropagation();
            return;
        }

        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        Swal.fire({
            title: "Ganti Password?",
            text: "Sesi Anda mungkin akan berakhir dan Anda harus login kembali.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Ganti Password",
            cancelButtonText: "Batal",
            confirmButtonColor: "#CE2D2D"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Live password match feedback
    document.getElementById('password_konfirmasi').addEventListener('input', function() {
        const passwordBaru = document.getElementById('password_baru').value;
        const konfirmasiFeedback = document.getElementById('konfirmasi-feedback');
        
        if (this.value && this.value !== passwordBaru) {
            this.setCustomValidity('Tidak cocok');
            konfirmasiFeedback.textContent = 'Password baru dan konfirmasi tidak cocok!';
        } else {
            this.setCustomValidity('');
            konfirmasiFeedback.textContent = 'Konfirmasi password wajib diisi!';
        }
    });
</script>
@endpush