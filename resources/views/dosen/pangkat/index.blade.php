<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pangkat dan Golongan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/ReadPangkatdanGolongan.css') }}">
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

            {{-- Header --}}
            <div class="header-box">
                <div class="header-left">
                    <i class="bi bi-award" style="font-size:40px;"></i>
                    <div>
                        <h1>Pengajuan Pangkat dan Golongan</h1>
                        <p>Riwayat pengajuan pangkat dan golongan Anda</p>
                    </div>
                </div>
            </div>

            <div class="container-box mb-4 py-4 border-0 shadow-sm">

                {{-- Baris atas: tombol + pangkat card --}}
                <div class="row align-items-center mb-4 g-3">
                    <div class="col-md-5">
                        <a href="{{ route('pangkat-golongan.create') }}" class="btn-ajukan btn">
                            <i class="bi bi-plus-circle me-2"></i>Ajukan Kenaikan
                        </a>
                    </div>
                    <div class="col-md-7">
                        <div class="pangkat-card">
                            <div class="pangkat-icon"><i class="bi bi-award-fill"></i></div>
                            <div>
                                <div class="pangkat-label">Pangkat Saat Ini</div>
                                @if($pegawai && $pegawai->id_panggol)
                                    <div class="pangkat-nama">{{ $pegawai->jenis_pangkat }}</div>
                                    <div class="pangkat-sub">{{ $pegawai->nama_lengkap }}</div>
                                @else
                                    <div class="pangkat-nama">Belum Ada Pangkat</div>
                                    <div class="pangkat-sub">{{ $pegawai->nama_lengkap ?? '—' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table tbl-panggol align-middle">
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
                        @forelse($pengajuan as $no => $row)
                            @php
                                $statusMap = [
                                    'MENUNGGU'          => ['label' => 'Menunggu Diproses',    'class' => 'badge-menunggu',    'icon' => 'bi-hourglass-split'],
                                    'VERIFIKASI'        => ['label' => 'Sedang Diverifikasi',  'class' => 'badge-verifikasi',  'icon' => 'bi-search'],
                                    'PERSETUJUAN'       => ['label' => 'Menunggu Persetujuan', 'class' => 'badge-persetujuan', 'icon' => 'bi-person-check'],
                                    'DISETUJUI'         => ['label' => 'Disetujui',            'class' => 'badge-disetujui',   'icon' => 'bi-check-circle-fill'],
                                    'TOLAK_VERIFIKASI'  => ['label' => 'Ditolak (Verifikasi)', 'class' => 'badge-ditolak',     'icon' => 'bi-x-circle-fill'],
                                    'TOLAK_PERSETUJUAN' => ['label' => 'Ditolak (Pimpinan)',   'class' => 'badge-ditolak',     'icon' => 'bi-x-circle-fill'],
                                ];
                                $statusInfo = $statusMap[$row->status_pengajuan] ?? ['label' => $row->status_pengajuan, 'class' => 'badge-menunggu', 'icon' => 'bi-question-circle'];

                                $warnaBerkas = ['sk_cpns' => 'merah', 'sk_pns' => 'biru', 'pak' => 'hijau', 'publikasi' => 'ungu'];
                                $labelBerkas = ['sk_cpns' => 'SK CPNS', 'sk_pns' => 'SK PNS', 'pak' => 'PAK', 'publikasi' => 'Publikasi'];

                                $berkasJson = json_encode($row->berkas->map(function($b) use ($labelBerkas, $warnaBerkas) {
                                    return [
                                        'label' => $labelBerkas[$b->jenis_berkas] ?? $b->jenis_berkas,
                                        'path'  => $b->file_path,
                                        'nama'  => $b->nama_berkas,
                                        'warna' => $warnaBerkas[$b->jenis_berkas] ?? 'merah',
                                    ];
                                })->values());

                                $namaEsc = addslashes($row->jenis_pangkat);
                            @endphp
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td><strong>{{ $row->jenis_pangkat }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>
                                    @if($row->keterangan_tambahan)
                                        <code>{{ $row->keterangan_tambahan }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->berkas->count())
                                        <button class="btn-lihat-file"
                                            onclick="bukaModalBerkas('{{ $namaEsc }}', {!! htmlspecialchars($berkasJson, ENT_QUOTES) !!})">
                                            <i class="bi bi-folder2-open"></i>
                                            Lihat File
                                            <span class="badge bg-light text-dark ms-1" style="font-size:0.7rem;">
                                                {{ $row->berkas->count() }}
                                            </span>
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusInfo['class'] }}">
                                        <i class="bi {{ $statusInfo['icon'] }} me-1"></i>
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                    @if($row->status_pengajuan === 'MENUNGGU')
                                        <a href="{{ route('pangkat-golongan.edit', $row->id_pengajuan) }}"
                                           class="btn btn-primary btn-aksi">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <button class="btn btn-danger btn-aksi"
                                                onclick="confirmDelete({{ $row->id_pengajuan }})">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                        <button onclick="bukaDetail({{ $row->id_pengajuan }})" class="btn btn-secondary btn-aksi">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>

                                    @elseif($row->status_pengajuan === 'VERIFIKASI' || $row->status_pengajuan === 'PERSETUJUAN')
                                        <button onclick="bukaDetail({{ $row->id_pengajuan }})" class="btn btn-secondary btn-aksi">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>

                                    @elseif($row->status_pengajuan === 'DISETUJUI')
                                        <button onclick="bukaDetail({{ $row->id_pengajuan }})" class="btn btn-secondary btn-aksi">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </button>
                                        <a href="#" class="btn btn-success btn-aksi">
                                            <i class="bi bi-download me-1"></i>Surat
                                        </a>

                                    @elseif($row->status_pengajuan === 'TOLAK_VERIFIKASI')
                                        <a href="{{ route('pangkat-golongan.edit', $row->id_pengajuan) }}?mode=revisi"
                                           class="btn btn-warning btn-aksi">
                                            <i class="bi bi-arrow-repeat me-1"></i>Revisi
                                        </a>
                                        <button onclick="bukaDetail({{ $row->id_pengajuan }})" class="btn btn-secondary btn-aksi">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                    @elseif($row->status_pengajuan === 'TOLAK_PERSETUJUAN')
                                        <button onclick="bukaDetail({{ $row->id_pengajuan }})" class="btn btn-secondary btn-aksi">
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
                                    Belum ada pengajuan pangkat.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Legenda Status --}}
                <div class="d-flex flex-wrap gap-3 mt-3 pt-3 border-top align-items-center">
                    <small class="text-muted fw-semibold">Keterangan:</small>
                    <small><span class="status-badge badge-menunggu">Menunggu Diproses</span></small>
                    <small><span class="status-badge badge-verifikasi">Sedang Diverifikasi</span></small>
                    <small><span class="status-badge badge-persetujuan">Menunggu Persetujuan</span></small>
                    <small><span class="status-badge badge-disetujui">Disetujui</span></small>
                    <small><span class="status-badge badge-ditolak">Ditolak</span></small>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

{{-- Modal Berkas --}}
<div class="modal-berkas-overlay" id="modalBerkas" role="dialog" aria-modal="true">
    <div class="modal-berkas-box">
        <div class="modal-berkas-header">
            <div>
                <div class="mb-judul">
                    <i class="bi bi-folder2-open"></i>
                    Berkas Pengajuan
                </div>
                <div class="mb-sub" id="modalBerkasNama">—</div>
            </div>
            <button class="modal-berkas-close" onclick="tutupModalBerkas()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-berkas-body" id="modalBerkasIsi"></div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="iframeDetail" src="" style="width:100%;height:80vh;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>


{{-- SweetAlert notifikasi --}}
@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#b91c1c' });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/dosen/data_pangkat_golongan/ReadPangkatdanGolongan.js') }}"></script>
</body>
</html>