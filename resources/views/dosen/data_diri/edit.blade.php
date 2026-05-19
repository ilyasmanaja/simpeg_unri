@extends('template.main')

@section('in', 'active')

@section('konten')
{{-- Judul luar dihapus agar sesuai dengan image_54b328.png --}}
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
    <form action="{{ route('pegawai.update', $pegawai->id_pegawai) }}" method="POST" id="formEditPegawai" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        
        <table class="table table-borderless">
            <tr>
                <td colspan="2">
                    <h4 class="tebal">Halaman Edit Data Diri</h4>
                    <hr class="mb-3" style="border-top: 2px solid #CE2D2D;">
                </td>
            </tr>

            <!-- Kolom Terkunci (Read-Only) -->
            <tr>
                <td width="250"><label class="form-label">NIK</label></td>
                <td>
                    <span class="form-control-plaintext fw-semibold text-muted">
                        {{ $pegawai->nik ?? '-' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><label class="form-label">NIP</label></td>
                <td>
                    <span class="form-control-plaintext fw-semibold text-muted">
                        {{ $pegawai->nip ?? '-' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><label class="form-label">NIDN</label></td>
                <td>
                    <span class="form-control-plaintext fw-semibold text-muted">
                        {{ $pegawai->nidn ?? '-' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><label class="form-label">Tanggal Lahir</label></td>
                <td>
                    <span class="form-control-plaintext fw-semibold text-muted">
                        {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}
                    </span>
                </td>
            </tr>

            <!-- Kolom yang Bisa Diupdate -->
            <tr>
                <td><label for="nomor_hp" class="form-label">No HP Aktif</label></td>
                <td>
                    <input type="text" name="nomor_hp" id="nomor_hp" class="form-control" value="{{ $pegawai->nomor_hp }}" required placeholder="Masukkan No Hp">
                    <div class="invalid-feedback">No HP wajib diisi!</div>
                </td>
            </tr>
            <tr>
                <td><label for="nomor_hp_darurat" class="form-label">No HP Darurat</label></td>
                <td>
                    <input type="text" name="nomor_hp_darurat" id="nomor_hp_darurat" class="form-control" value="{{ $pegawai->nomor_hp_darurat }}" required placeholder="Masukkan No Hp Darurat">
                    <div class="invalid-feedback">No HP Darurat wajib diisi!</div>
                </td>
            </tr>
            <tr>
                <td><label for="alamat" class="form-label">Alamat</label></td>
                <td>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required placeholder="Masukkan Alamat Lengkap">{{ $pegawai->alamat }}</textarea>
                    <div class="invalid-feedback">Alamat wajib diisi!</div>
                </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('formEditPegawai').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = this;

        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        Swal.fire({
            title: "Simpan perubahan?",
            text: "Data akan diperbarui ke sistem.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Update!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#CE2D2D"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection