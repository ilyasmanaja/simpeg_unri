@extends('layouts.app')

@section('in', 'active')

@section('konten')
<div class="header">
    <button class="btn btn-menu text-white d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#sidebarMobile">
        ☰
    </button>

    <h4 class="tebal">
        <img src="{{ asset('pfp.jpg') }}" alt=""> Selamat Datang, {{ Auth::user()->pegawai->nama_lengkap ?? 'User' }} di Sistem Informasi Kepegawaian
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

            {{-- ===== NIK — selalu tampil untuk semua ===== --}}
            <tr>
                <td width="250"><label class="form-label">NIK</label></td>
                <td>
                    <span class="form-control-plaintext fw-semibold text-muted">
                        {{ $pegawai->nik ?? '-' }}
                    </span>
                </td>
            </tr>

            @if($pegawai->status_pegawai == 'PNS')

                {{-- ===== PNS: NIP selalu tampil ===== --}}
                <tr>
                    <td><label class="form-label">NIP</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->nip ?? '-' }}
                        </span>
                    </td>
                </tr>

                {{-- ===== PNS + Dosen: tampil NIDN ===== --}}
                @if(Auth::user()->jenis_role == 'Dosen')
                <tr>
                    <td><label class="form-label">NIDN</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->nidn ?? '-' }}
                        </span>
                    </td>
                </tr>
                @endif

                {{-- ===== PNS (Dosen & Tendik): Tanggal Lahir, Jenis Kelamin, Jurusan, Prodi ===== --}}
                <tr>
                    <td><label class="form-label">Tanggal Lahir</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Jenis Kelamin</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : ($pegawai->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Jurusan</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->jurusan ?? '-' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Prodi</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->prodi ?? '-' }}
                        </span>
                    </td>
                </tr>

                {{-- ===== PNS: field edit No HP ===== --}}
                <tr>
                    <td><label for="nomor_hp" class="form-label">No HP Aktif</label></td>
                    <td>
                        <input type="text" name="nomor_hp" id="nomor_hp"
                               class="form-control"
                               value="{{ $pegawai->nomor_hp }}"
                               required
                               placeholder="Masukkan No HP">
                        <div class="invalid-feedback">No HP wajib diisi!</div>
                    </td>
                </tr>

                <tr>
                    <td><label for="nomor_hp_darurat" class="form-label">No HP Darurat</label></td>
                    <td>
                        <input type="text" name="nomor_hp_darurat" id="nomor_hp_darurat"
                               class="form-control"
                               value="{{ $pegawai->nomor_hp_darurat }}"
                               required
                               placeholder="Masukkan No HP Darurat">
                        <div class="invalid-feedback">No HP Darurat wajib diisi!</div>
                    </td>
                </tr>

            @else

                {{-- ===== Non-PNS (Honorer, dll): tampil data tanpa NIP & NIDN ===== --}}
                <tr>
                    <td><label class="form-label">Tanggal Lahir</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Jenis Kelamin</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : ($pegawai->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Jurusan</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->jurusan ?? '-' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><label class="form-label">Prodi</label></td>
                    <td>
                        <span class="form-control-plaintext fw-semibold text-muted">
                            {{ $pegawai->prodi ?? '-' }}
                        </span>
                    </td>
                </tr>

                {{-- ===== Non-PNS: field edit No HP tetap tersedia ===== --}}
                <tr>
                    <td><label for="nomor_hp" class="form-label">No HP Aktif</label></td>
                    <td>
                        <input type="text" name="nomor_hp" id="nomor_hp"
                               class="form-control"
                               value="{{ $pegawai->nomor_hp }}"
                               required
                               placeholder="Masukkan No HP">
                        <div class="invalid-feedback">No HP wajib diisi!</div>
                    </td>
                </tr>

                <tr>
                    <td><label for="nomor_hp_darurat" class="form-label">No HP Darurat</label></td>
                    <td>
                        <input type="text" name="nomor_hp_darurat" id="nomor_hp_darurat"
                               class="form-control"
                               value="{{ $pegawai->nomor_hp_darurat }}"
                               required
                               placeholder="Masukkan No HP Darurat">
                        <div class="invalid-feedback">No HP Darurat wajib diisi!</div>
                    </td>
                </tr>

            @endif

            <tr>
                <td></td>
                <td class="pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Submit
                    </button>
                    <a href="{{ route('pegawai.show', $pegawai->id_pegawai) }}" class="btn btn-secondary">Kembali</a>
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