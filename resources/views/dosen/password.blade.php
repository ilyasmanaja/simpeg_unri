@extends('template.main')

@section('in', 'active')

@section('konten')
<div class="header">
    <button class="btn btn-menu text-white d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#sidebarMobile">
        ☰
    </button>

    <h4 class="tebal">
        <img src="{{ asset('pfp.jpg') }}" alt=""> Selamat Datang, {{ Auth::user()->nama_lengkap ?? 'User' }} di Sistem Informasi Kepegawaian
    </h4>
</div>

<div class="card-1 mt-3 data_diri_read">
    <form action="{{ route('pegawai.password.update', $pegawai->id_pegawai) }}" method="POST" id="formGantiPassword" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <table class="table table-borderless">
            <tr>
                <td colspan="2">
                    <h4 class="tebal">Pengelolaan Password</h4>
                    <hr class="mb-3" style="border-top: 2px solid #CE2D2D;">
                </td>
            </tr>

            @if(session('error'))
            <tr>
                <td colspan="2">
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                    </div>
                </td>
            </tr>
            @endif

            @if(session('success'))
            <tr>
                <td colspan="2">
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                    </div>
                </td>
            </tr>
            @endif

            <!-- Password Lama -->
            <tr>
                <td width="250">
                    <label for="password_lama" class="form-label">
                        Password Lama <span class="text-danger">*</span>
                    </label>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="password"
                               name="password_lama"
                               id="password_lama"
                               class="form-control @error('password_lama') is-invalid @enderror"
                               placeholder="Masukkan password lama"
                               required
                               style="padding-right: 2.5rem;">
                        <span class="toggle-pw" onclick="togglePassword('password_lama', this)" title="Tampilkan/sembunyikan password">
                            👁
                        </span>
                        @error('password_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Password lama wajib diisi!</div>
                        @enderror
                    </div>
                </td>
            </tr>

            <!-- Password Baru -->
            <tr>
                <td>
                    <label for="password_baru" class="form-label">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="password"
                               name="password_baru"
                               id="password_baru"
                               class="form-control @error('password_baru') is-invalid @enderror"
                               placeholder="Masukkan password baru"
                               required
                               minlength="8"
                               style="padding-right: 2.5rem;">
                        <span class="toggle-pw" onclick="togglePassword('password_baru', this)" title="Tampilkan/sembunyikan password">
                            👁
                        </span>
                        @error('password_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Password baru wajib diisi (minimal 8 karakter)!</div>
                        @enderror
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.78rem;">
                        <i class="bi bi-info-circle"></i> Minimal 8 karakter.
                    </div>
                </td>
            </tr>

            <!-- Ulangi Password Baru -->
            <tr>
                <td>
                    <label for="password_konfirmasi" class="form-label">
                        Ulangi Password Baru <span class="text-danger">*</span>
                    </label>
                </td>
                <td>
                    <div class="position-relative">
                        <input type="password"
                               name="password_konfirmasi"
                               id="password_konfirmasi"
                               class="form-control"
                               placeholder="Ulangi password baru"
                               required
                               style="padding-right: 2.5rem;">
                        <span class="toggle-pw" onclick="togglePassword('password_konfirmasi', this)" title="Tampilkan/sembunyikan password">
                            👁
                        </span>
                        <div class="invalid-feedback" id="konfirmasi-feedback">Konfirmasi password wajib diisi!</div>
                    </div>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('pegawai.show', $pegawai->id_pegawai) }}" class="btn btn-secondary">Kembali</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<style>
    .toggle-pw {
        position: absolute;
        right: 0.6rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        opacity: 0.5;
        user-select: none;
        z-index: 5;
    }
    .toggle-pw:hover {
        opacity: 0.85;
    }
    .toggle-pw.active {
        opacity: 1;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle password visibility
    function togglePassword(fieldId, span) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            span.classList.add('active');
        } else {
            input.type = 'password';
            span.classList.remove('active');
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
            text: "Password akun Anda akan diperbarui.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Ganti!",
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
@endsection