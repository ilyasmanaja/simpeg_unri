@extends('layouts.app')

@section('title', 'Data Pangkat dan Golongan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    {{-- Kita pinjam CSS Jabatan Fungsional agar struktur layout, panel, dan tabelnya identik --}}
    <link rel="stylesheet" href="{{ asset('css/jabatanfungsional.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="main">

        {{-- ── Header ── --}}
        <div class="page-header">
            <div class="ph-icon"><i class="bi bi-award-fill"></i></div>
            <div>
                <h1>Pangkat dan Golongan</h1>
                <p>Kelola dan pantau riwayat kenaikan pangkat dan golongan Anda</p>
            </div>
        </div>

        <div class="main-inner">

            {{-- ── Pangkat Aktif Bar (Struktur meminjam jabfung-aktif-bar agar CSS sama) ── --}}
            <div class="jabfung-aktif-bar">
                <div class="jab-left">
                    <div class="jab-icon"><i class="bi bi-award-fill"></i></div>
                    <div>
                        <div class="jab-sublabel">Pangkat Saat Ini</div>
                        @if ($pegawai && $pegawai->id_panggol)
                            <div class="jab-nama">
                                {{ $pegawai->pangkatGolongan->jenis_pangkat ?? ($pegawai->jenis_pangkat ?? '—') }}</div>
                        @else
                            <div class="jab-nama" style="color:#9ca3af;">Belum Ada Pangkat</div>
                        @endif
                        <div class="jab-sub">{{ $pegawai->nama_lengkap ?? '—' }}</div>
                    </div>
                </div>
                <a href="{{ route('dosen.pangkat-golongan.create') }}" class="btn-ajukan-top">
                    <i class="bi bi-plus-circle"></i> Ajukan Kenaikan
                </a>
            </div>

            {{-- ── Tabel Riwayat ── --}}
            <div class="table-responsive">
                <table class="table tbl-jabfung align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pangkat Diajukan</th>
                            <th>Tgl Pengajuan</th>
                            <th>Nomor SK</th>
                            <th>Berkas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $no => $row)
                            @php
                                $bermasalahArr = $row->berkas_bermasalah ?? [];
                                $namaTarget = $row->jenis_pangkat ?? '—';
                                $nomorUsulan = $row->keterangan_tambahan ?? '—';
                                $statusRaw = strtolower($row->status_pengajuan);

                                // Mapping manual agar tampilan sama persis dengan Jabfung
                                $mapStatus = [
                                    'menunggu' => [
                                        'class' => 'badge-menunggu',
                                        'icon' => 'bi-hourglass-split',
                                        'label' => 'Menunggu Diproses',
                                    ],
                                    'sedang diverifikasi' => [
                                        'class' => 'badge-verifikasi',
                                        'icon' => 'bi-search',
                                        'label' => 'Sedang Diverifikasi',
                                    ],
                                    'verifikasi' => [
                                        'class' => 'badge-verifikasi',
                                        'icon' => 'bi-search',
                                        'label' => 'Sedang Diverifikasi',
                                    ],
                                    'menunggu persetujuan' => [
                                        'class' => 'badge-persetujuan',
                                        'icon' => 'bi-clock-history',
                                        'label' => 'Menunggu Persetujuan',
                                    ],
                                    'persetujuan' => [
                                        'class' => 'badge-persetujuan',
                                        'icon' => 'bi-clock-history',
                                        'label' => 'Menunggu Persetujuan',
                                    ],
                                    'disetujui' => [
                                        'class' => 'badge-disetujui',
                                        'icon' => 'bi-check-circle-fill',
                                        'label' => 'Disetujui',
                                    ],
                                    'ditolak' => [
                                        'class' => 'badge-ditolak',
                                        'icon' => 'bi-x-circle-fill',
                                        'label' => 'Ditolak',
                                    ],
                                    'tolak_verifikasi' => [
                                        'class' => 'badge-ditolak',
                                        'icon' => 'bi-x-circle-fill',
                                        'label' => 'Ditolak (Verifikasi)',
                                    ],
                                    'tolak_persetujuan' => [
                                        'class' => 'badge-ditolak',
                                        'icon' => 'bi-x-circle-fill',
                                        'label' => 'Ditolak (Pimpinan)',
                                    ],
                                ];

                                // Gunakan fallback jika status tidak dikenali
                                $statusInfo = $mapStatus[$statusRaw] ?? [
                                    'class' => 'bg-secondary text-white',
                                    'icon' => 'bi-info-circle',
                                    'label' => ucwords(str_replace('_', ' ', $statusRaw)),
                                ];

                                $labelBerkas = [
                                    'sk_cpns' => 'SK CPNS',
                                    'sk_pns' => 'SK PNS',
                                    'pak' => 'PAK',
                                    'publikasi' => 'Publikasi',
                                ];
                            @endphp
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>
                                    <strong>{{ $namaTarget }}</strong>
                                </td>
                                <td>
                                    @if ($row->tanggal_pengajuan)
                                        {{ \Carbon\Carbon::parse($row->tanggal_pengajuan)->translatedFormat('d M Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($nomorUsulan !== '—')
                                        <code>{{ $nomorUsulan }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($row->berkas as $b)
                                        <a href="{{ Storage::url($b->file_path) }}" target="_blank"
                                            class="file-chip d-flex {{ in_array($b->jenis_berkas, $bermasalahArr) ? 'file-chip-bermasalah' : '' }}">
                                            <i class="bi bi-file-earmark-pdf-fill flex-shrink-0"></i>
                                            <span>{{ $labelBerkas[$b->jenis_berkas] ?? $b->jenis_berkas }}</span>
                                            @if (in_array($b->jenis_berkas, $bermasalahArr))
                                                <i class="bi bi-exclamation-circle-fill ms-1 text-danger"
                                                    title="Perlu direvisi"></i>
                                            @endif
                                        </a>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusInfo['class'] }}">
                                        <i class="bi {{ $statusInfo['icon'] }}"></i>
                                        {{ $statusInfo['label'] }}
                                    </span>
                                    @if (!empty($bermasalahArr) && $statusRaw === 'tolak_verifikasi')
                                        <div class="catatan-tolak mt-1">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            Ada {{ count($bermasalahArr) }} berkas direvisi
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">

                                        {{-- Menunggu → Perbarui, Hapus, Detail --}}
                                        @if ($statusRaw === 'menunggu')
                                            <a href="{{ route('dosen.pangkat-golongan.edit', $row->id_pengajuan) }}"
                                                class="btn btn-primary btn-aksi">
                                                <i class="bi bi-pencil me-1"></i>Perbarui
                                            </a>

                                            {{-- Form Delete (Lebih aman dari fungsi JS onClick biasa) --}}
                                            <form
                                                action="{{ route('dosen.pangkat-golongan.destroy', $row->id_pengajuan) }}"
                                                method="POST" class="d-inline" id="form-delete-{{ $row->id_pengajuan }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-aksi"
                                                    onclick="confirmDelete({{ $row->id_pengajuan }})">
                                                    <i class="bi bi-trash me-1"></i>Hapus
                                                </button>
                                            </form>

                                            <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                                class="btn btn-secondary btn-aksi">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </button>

                                            {{-- Tolak Verifikasi → Detail + Revisi --}}
                                        @elseif ($statusRaw === 'tolak_verifikasi')
                                            <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                                class="btn btn-secondary btn-aksi">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </button>
                                            <a href="{{ route('dosen.pangkat-golongan.edit', $row->id_pengajuan) }}?mode=revisi"
                                                class="btn btn-warning btn-aksi">
                                                <i class="bi bi-arrow-repeat me-1"></i>Revisi
                                            </a>

                                            {{-- Status Lainnya (Verifikasi, Persetujuan, Disetujui, Tolak Persetujuan) → Detail --}}
                                        @else
                                            <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                                class="btn btn-secondary btn-aksi">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Belum ada pengajuan pangkat dan golongan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Legenda status ── --}}
            <div class="legenda-status">
                <small class="text-muted fw-semibold">Keterangan:</small>
                <small><span class="status-badge badge-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu
                        Diproses</span></small>
                <small><span class="status-badge badge-verifikasi"><i class="bi bi-search"></i> Sedang
                        Diverifikasi</span></small>
                <small><span class="status-badge badge-persetujuan"><i class="bi bi-clock-history"></i> Menunggu
                        Persetujuan</span></small>
                <small><span class="status-badge badge-disetujui"><i class="bi bi-check-circle-fill"></i>
                        Disetujui</span></small>
                <small><span class="status-badge badge-ditolak"><i class="bi bi-x-circle-fill"></i> Ditolak</span></small>
            </div>

            {{-- ── Panel Detail Inline ── --}}
            <div id="panelDetail" class="detail-panel mt-4" style="display:none;">
                <div class="detail-panel-inner">

                    <div class="detail-panel-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dh-icon"><i class="bi bi-award-fill"></i></div>
                            <div>
                                <h5 id="dp-title">Detail Pengajuan Pangkat & Golongan</h5>
                                <p id="dp-subtitle" class="mb-0"></p>
                            </div>
                        </div>
                        <button onclick="tutupDetail()" class="btn-close-detail">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="detail-panel-body">

                        {{-- Status banner --}}
                        <div id="dp-status-banner" class="status-banner mb-3"></div>

                        <div class="row g-4">
                            {{-- Info pengajuan --}}
                            <div class="col-md-6">
                                <div class="section-title-sm"><i class="bi bi-info-circle me-1"></i>Info Pengajuan</div>
                                <div class="info-row">
                                    <div class="info-label">Nama Lengkap</div>
                                    <p class="info-value" id="dp-nama">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label" id="dp-id-label">NIP / NIDN</div>
                                    <p class="info-value" id="dp-id-value">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Pangkat Saat Ini</div>
                                    <p class="info-value" id="dp-pangkat-now">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Pangkat Diajukan</div>
                                    <p class="info-value fw-bold" id="dp-pangkat-target">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nomor SK / Usulan</div>
                                    <p class="info-value" id="dp-nomor">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tanggal Pengajuan</div>
                                    <p class="info-value" id="dp-tanggal">—</p>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Status</div>
                                    <div id="dp-status-badge"></div>
                                </div>
                            </div>

                            {{-- Berkas bermasalah --}}
                            <div class="col-md-6">
                                <div id="dp-bermasalah-wrap" style="display:none;">
                                    <div class="section-title-sm">
                                        <i class="bi bi-exclamation-triangle me-1 text-danger"></i>Berkas Perlu Direvisi
                                    </div>
                                    <div id="dp-bermasalah-list"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Berkas grid --}}
                        <div class="mt-4">
                            <div class="section-title-sm"><i class="bi bi-paperclip me-1"></i>Berkas Pendukung</div>
                            <div id="dp-berkas-grid" class="berkas-grid-modal"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>{{-- /main-inner --}}
    </div>{{-- /col main --}}

    {{-- ── Data JSON untuk JavaScript ── --}}
    <script>
        window.PEGAWAI_NAMA = @json($pegawai->nama_lengkap ?? '—');
        window.PANGKAT_NOW = @json($pegawai->pangkatGolongan->jenis_pangkat ?? ($pegawai->jenis_pangkat ?? 'Belum ada'));
        window.PEGAWAI_NIDN = @json($pegawai->nidn ?? null);
        window.PEGAWAI_NIP = @json($pegawai->nip ?? null);
        window.PEGAWAI_ID_LABEL = window.PEGAWAI_NIDN ? 'NIDN' : 'NIP';
        window.PEGAWAI_ID_VALUE = window.PEGAWAI_NIDN ?? (window.PEGAWAI_NIP ?? '—');

        @php
            $pengajuanJson = $pengajuan->map(function ($row) {
                $stRaw = strtolower($row->status_pengajuan);

                $map = [
                    'menunggu' => ['class' => 'badge-menunggu', 'icon' => 'bi-hourglass-split', 'label' => 'Menunggu Diproses'],
                    'sedang diverifikasi' => ['class' => 'badge-verifikasi', 'icon' => 'bi-search', 'label' => 'Sedang Diverifikasi'],
                    'verifikasi' => ['class' => 'badge-verifikasi', 'icon' => 'bi-search', 'label' => 'Sedang Diverifikasi'],
                    'menunggu persetujuan' => ['class' => 'badge-persetujuan', 'icon' => 'bi-clock-history', 'label' => 'Menunggu Persetujuan'],
                    'persetujuan' => ['class' => 'badge-persetujuan', 'icon' => 'bi-clock-history', 'label' => 'Menunggu Persetujuan'],
                    'disetujui' => ['class' => 'badge-disetujui', 'icon' => 'bi-check-circle-fill', 'label' => 'Disetujui'],
                    'ditolak' => ['class' => 'badge-ditolak', 'icon' => 'bi-x-circle-fill', 'label' => 'Ditolak'],
                    'tolak_verifikasi' => ['class' => 'badge-ditolak', 'icon' => 'bi-x-circle-fill', 'label' => 'Ditolak (Verifikasi)'],
                    'tolak_persetujuan' => ['class' => 'badge-ditolak', 'icon' => 'bi-x-circle-fill', 'label' => 'Ditolak (Pimpinan)'],
                ];

                $stInfo = $map[$stRaw] ?? ['class' => 'bg-secondary text-white', 'icon' => 'bi-info-circle', 'label' => strtoupper($stRaw)];

                return [
                    'id' => $row->id_pengajuan,
                    'pangkat_target' => $row->jenis_pangkat ?? '—',
                    'nomor_usulan' => $row->keterangan_tambahan ?? '—',
                    'tanggal' => $row->tanggal_pengajuan ? \Carbon\Carbon::parse($row->tanggal_pengajuan)->translatedFormat('d F Y') : '—',
                    'status' => $stRaw,
                    'status_label' => $stInfo['label'],
                    'status_class' => $stInfo['class'],
                    'status_icon' => $stInfo['icon'],
                    'berkas_bermasalah' => $row->berkas_bermasalah ?? [],
                    'berkas' => $row->berkas
                        ->map(function ($b) {
                            return [
                                'jenis' => $b->jenis_berkas,
                                'label' => ['sk_cpns' => 'SK CPNS', 'sk_pns' => 'SK PNS', 'pak' => 'PAK', 'publikasi' => 'Publikasi'][$b->jenis_berkas] ?? $b->jenis_berkas,
                                'url' => Storage::url($b->file_path),
                            ];
                        })
                        ->values(),
                ];
            });
        @endphp

        window.PENGAJUAN_DATA = @json($pengajuanJson);
    </script>

@endsection

@push('scripts')
    {{-- Kita asumsikan fungsi panel inline ada di file JS ini atau kamu bisa meng-copy logika JS Jabatan Fungsional ke ReadPangkatdanGolongan.js --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/dosen/data_pangkat_golongan/ReadPangkatdanGolongan.js') }}"></script>

    <script>
        // Logika SweetAlert untuk Hapus Data
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Pengajuan ini akan dihapus permanen dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + id).submit();
                }
            })
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush
