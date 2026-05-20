@extends('layouts.app')

@section('title', 'Dashboard Pimpinan - SIMPEG UNRI')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 20px;
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="main main-1">
        <div class="header">
            <button class="btn btn-menu text-white d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMobile">
                ☰
            </button>

            <div class="container py-2">
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="tebal">
                            <img src="{{ $pegawai->foto ? asset($pegawai->foto) : asset('assets/dosen/data_diri/pfp.jpg') }}"
                                alt="Profile"
                                style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
                            Selamat Datang, {{ $pegawai->nama_lengkap ?? 'Pimpinan' }} di Sistem Informasi Kepegawaian
                        </h4>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $totalPegawai }}</h3>
                                <small class="text-muted">Total Pegawai</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $totalDosen }}</h3>
                                <small class="text-muted">Total Dosen</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $totalTendik }}</h3>
                                <small class="text-muted">Tenaga Didik</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 d-flex flex-row align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="fa-solid fa-file-circle-plus"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $pengajuanBaru }}</h3>
                                <small class="text-muted">Pengajuan Baru</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card shadow-sm p-4">
                            <h6 class="text-center fw-bold mb-4">Jenis Kelamin</h6>
                            <div class="chart-container">
                                <canvas id="chartGender"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm p-4">
                            <h6 class="text-center fw-bold mb-4">Jabatan Fungsional</h6>
                            <div class="chart-container">
                                <canvas id="chartJabfung"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm p-4">
                            <h6 class="text-center fw-bold mb-4">Pangkat Golongan</h6>
                            <div class="chart-container">
                                <canvas id="chartGolongan"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm p-4">
                            <h6 class="text-center fw-bold mb-4">Distribusi Usia Pegawai</h6>
                            <div class="chart-container">
                                <canvas id="chartUsia"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        Chart.defaults.color = '#000';

        // 1. Chart Gender
        const ctxGender = document.getElementById('chartGender');
        new Chart(ctxGender, {
            type: 'pie',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: @json($dataGender),
                    backgroundColor: ['#7F1D1D', '#F87171'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 2. Chart Jabatan Fungsional (DI SINI TADI DUPLIKAT GENDER)
        const ctxJabfung = document.getElementById('chartJabfung');
        new Chart(ctxJabfung, {
            type: 'pie',
            data: {
                labels: ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'],
                datasets: [{
                    data: @json($dataJabfung),
                    backgroundColor: ['#CE2D2D', '#E53935', '#F87171', '#7F1D1D'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 3. Chart Pangkat Golongan
        const ctxGolongan = document.getElementById('chartGolongan');
        new Chart(ctxGolongan, {
            type: 'bar',
            data: {
                labels: ['Gol. III', 'Gol. IV', 'Gol. II'],
                datasets: [{
                    label: 'Total',
                    data: @json($dataGolongan),
                    backgroundColor: ['#FCA5A5', '#EF4444', '#B91C1C']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 4. Chart Distribusi Usia
        const ctxUsia = document.getElementById('chartUsia');
        new Chart(ctxUsia, {
            type: 'bar',
            data: {
                labels: ['20-30', '31-40', '41-50', '51-60'],
                datasets: [{
                    label: 'Total',
                    data: @json($dataUsia),
                    backgroundColor: ['#FCA5A5', '#F87171', '#EF4444', '#DC2626']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
