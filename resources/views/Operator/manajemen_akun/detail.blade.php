@extends('layouts.app')

@section('title', 'Detail Akun Pegawai')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/manajemen_akun/style.css') }}">
@endpush
@section('content')

    <div class="container-fluid p-4">

        <div class="container-box">

            <div class="header-box mb-4">

                <div class="header-left">

                    <div class="icon-box">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>

                    <div>

                        <h1 class="page-title">
                            Detail Akun Pegawai
                        </h1>

                        <p class="page-subtitle">
                            Halaman detail akun pegawai
                        </p>

                    </div>

                </div>

            </div>

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm p-3 text-center">

                        @if ($pegawai->foto)
                            <img src="{{ asset('storage/' . $pegawai->foto) }}"
                                style="width:140px;height:140px;object-fit:cover;border-radius:14px;margin:auto;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap) }}"
                                style="width:140px;height:140px;border-radius:14px;margin:auto;">
                        @endif

                        <h5 class="mt-3 mb-1">
                            {{ $pegawai->nama_lengkap }}
                        </h5>

                        <small class="text-muted">
                            {{ $pegawai->status_pegawai }}
                        </small>

                    </div>

                </div>

                <div class="col-md-8">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Email</div>
                                <div class="col-md-8">
                                    {{ $pegawai->user->email ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">NIK</div>
                                <div class="col-md-8">
                                    {{ $pegawai->nik ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">NIP</div>
                                <div class="col-md-8">
                                    {{ $pegawai->nip ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Nomor Hp</div>
                                <div class="col-md-8">
                                    {{ $pegawai->nomor_hp ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Nomor Hp Darurat</div>
                                <div class="col-md-8">
                                    {{ $pegawai->nomor_hp_darurat ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">NIDN</div>
                                <div class="col-md-8">
                                    {{ $pegawai->nidn ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Jurusan</div>
                                <div class="col-md-8">
                                    {{ $pegawai->jurusan ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Prodi</div>
                                <div class="col-md-8">
                                    {{ $pegawai->prodi ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Jabatan</div>
                                <div class="col-md-8">
                                    {{ $pegawai->jabatanFungsional->jenis_jabfung ?? '-' }}
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Pangkat</div>
                                <div class="col-md-8">
                                    {{ $pegawai->pangkatGolongan->jenis_pangkat ?? '-' }}
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-2 text-primary">
                                Role
                            </h6>

                            @if ($pegawai->user && $pegawai->user->roles->count())

                                @foreach ($pegawai->user->roles as $role)
                                    <span class="badge bg-primary me-1">
                                        {{ $role->jenis_role }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">Tidak ada role</span>

                            @endif
                        </div>

                    </div>

                </div>

            </div>

            <div class="mt-4">

                <a href="{{ route('operator.manajemen_akun.index') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali

                </a>

                <a href="{{ route('operator.manajemen_akun.edit', $pegawai->id_pegawai) }}" class="btn btn-warning">

                    Edit

                </a>

            </div>

        </div>

    </div>

@endsection
