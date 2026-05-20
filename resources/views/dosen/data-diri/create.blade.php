@extends('layouts.app')

@section('title', 'Tambah Data Diri Dosen - SIMPEG UNRI')

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

                    <div class="settings-header mb-4">
                        <h1 class="section-title">Tambah Data Diri</h1>
                        <p class="section-subtitle">Silahkan lengkapi data personal Anda pada form di bawah ini.</p>
                    </div>

                    <div class="card-1 data_diri_read p-4 border rounded-4 bg-white shadow-sm">
                        <form id="formTambahData" class="needs-validation" novalidate
                            action="{{ route('dosen.pegawai.store') }}" method="POST">
                            @csrf <table class="table table-borderless align-middle mb-0">
                                <tr>
                                    <td style="width: 25%;"><label for="tanggal_lahir" class="form-label fw-bold"
                                            style="color: var(--unri-red);">Tanggal Lahir</label></td>
                                    <td>
                                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir"
                                            required>
                                        <div class="invalid-feedback">
                                            Tanggal Lahir wajib diisi!
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label for="nomor_hp" class="form-label fw-bold" style="color: var(--unri-red);">No
                                            HP</label></td>
                                    <td>
                                        <input type="number" class="form-control" name="nomor_hp" id="nomor_hp" required
                                            placeholder="Masukkan No HP Anda">
                                        <div class="invalid-feedback">
                                            No HP wajib diisi!
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label for="nomor_hp_darurat" class="form-label fw-bold"
                                            style="color: var(--unri-red);">No HP Darurat</label></td>
                                    <td>
                                        <input type="number" class="form-control" name="nomor_hp_darurat"
                                            id="nomor_hp_darurat" required placeholder="Masukkan No HP Darurat">
                                        <div class="invalid-feedback">
                                            No HP Darurat wajib diisi!
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td class="pt-4">
                                        <button type="button" class="btn btn-primary px-4 me-2"
                                            onclick="konfirmasiTambah()">Submit</button>
                                        <a href="{{ url('/dosen/data-diri') }}" class="btn btn-secondary px-4">Kembali</a>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();

        // Logika SweetAlert yang benar untuk Laravel
        function konfirmasiTambah() {
            const form = document.getElementById('formTambahData');

            // Cek apakah ada form yang kosong
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return false;
            }

            Swal.fire({
                title: "Apakah data ingin ditambahkan?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Tambahkan!",
                cancelButtonText: "Batal",
                confirmButtonColor: "#0d6efd"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Baris inilah yang benar-benar mengirim data ke Controller Laravel!
                    form.submit();
                }
            });
        }
    </script>
@endpush
