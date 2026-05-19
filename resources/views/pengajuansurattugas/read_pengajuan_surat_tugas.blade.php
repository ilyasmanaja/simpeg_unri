<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Kepegawaian</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container-fluid">
<div class="row min-vh-100">

    {{-- SIDEBAR --}}
    <div class="col-lg-auto sidebar px-0 d-none d-lg-flex flex-column">
        <div>
            <h4>Sistem Informasi Kepegawaian</h4>
            <a href="{{ route('surat.index') }}" class="{{ request()->routeIs('surat.index') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper-fill me-2"></i>Pengajuan Surat Tugas
            </a>
            <a href="{{ url('/data-diri') }}" class="{{ request()->is('data-diri*') ? 'active' : '' }}">
                <i class="bi bi-person-badge me-2"></i>Data Diri
            </a>
            <a href="{{ url('/pangkat-golongan') }}" class="{{ request()->is('pangkat-golongan*') ? 'active' : '' }}">
                <i class="bi bi-award me-2"></i>Data Pangkat Golongan
            </a>
            <a href="{{ url('/jabatan-fungsional') }}" class="{{ request()->is('jabatan-fungsional*') ? 'active' : '' }}">
                <i class="bi bi-briefcase-fill me-2"></i>Data Jabatan Fungsional
            </a>
        </div>
        <div class="mt-auto mb-3">
            <a href="{{ url('/profile') }}" class="{{ request()->is('profile*') ? 'active' : '' }}">
                <img src="{{ $pegawai && $pegawai->foto ? asset('storage/' . $pegawai->foto) : asset('images/pfp.jpg') }}"
                     alt="foto">
                {{ $pegawai->nama_lengkap ?? 'Profile' }}
            </a>
            <a href="{{ url('/login') }}" class="keluar">
                <i class="bi bi-box-arrow-left me-2"></i>Keluar
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="col main col-12 col-lg p-4">

        <div class="header-box">
            <div class="header-left">
                <div class="icon-box">📄</div>
                <div>
                    <h1>Pengajuan Surat Tugas</h1>
                    <p>Kelola dan pantau semua pengajuan surat tugas</p>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" style="font-size:13px">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="container-box">

            <div class="top-action d-flex justify-content-between align-items-center">
                <a href="{{ route('surat.create') }}" class="btn-pengajuan">
                    + Ajukan Surat Tugas
                </a>
                <input type="text" id="searchInput" class="search-box" placeholder="Cari data...">
            </div>

            <div class="table-responsive custom-table">
            <table class="table align-middle data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pengusul</th>
                    <th>Waktu Pelaksanaan</th>
                    <th>Lama</th>
                    <th>Perihal</th>
                    <th>Berkas Pendukung</th>
                    <th>Status</th>
                    <th>Surat Tugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            @forelse ($surat as $item)
            @php
                $st = strtolower(trim($item->status ?? ''));

                if ($st === 'draft' || $st === '') {
                    $st = 'menunggu diproses';
                }
                if ($st === 'ditolak') {
                    $st = 'ditolak (verifikasi)';
                }

                $statusClass = match($st) {
                    'menunggu diproses'     => 'status-menunggu-diproses',
                    'sedang diverifikasi'   => 'status-sedang-diverifikasi',
                    'menunggu persetujuan'  => 'status-menunggu-persetujuan',
                    'ditolak (verifikasi)'  => 'status-ditolak-verifikasi',
                    'disetujui'             => 'status-disetujui',
                    'ditolak (persetujuan)' => 'status-ditolak-persetujuan',
                    default                 => 'status-menunggu-diproses',
                };

                $statusLabel = match($st) {
                    'menunggu diproses'     => 'Menunggu Diproses',
                    'sedang diverifikasi'   => 'Sedang Diverifikasi',
                    'menunggu persetujuan'  => 'Menunggu Persetujuan',
                    'ditolak (verifikasi)'  => 'Ditolak (Verifikasi)',
                    'disetujui'             => 'Disetujui',
                    'ditolak (persetujuan)' => 'Ditolak (Persetujuan)',
                    default                 => 'Menunggu Diproses',
                };

                $namaAnggota = $item->anggota->pluck('nama_anggota')->implode(', ');

                // Berkas aktif dari tabel BERKAS (bukan kolom di surat_tugas)
                $berkasItem  = $item->berkasAktif;

                // Nomor identitas pengusul
                // Karena relasi surat ke pegawai tidak langsung, kita pakai $pegawai (login)
                // Jika ada kebutuhan multi-user, perlu relasi pengusul di tabel surat_tugas
                $nomorId     = $pegawai?->nomor_identitas ?? '-';
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <div style="font-weight:600;font-size:13px">{{ $pegawai->nama_lengkap ?? '-' }}</div>
                    <small class="text-muted" style="font-size:11px">{{ $nomorId }}</small>
                </td>
                <td>{{ \Carbon\Carbon::parse($item->waktu_pelaksanaan)->format('d M Y') }}</td>
                <td><span class="badge-hari">{{ $item->lama_pelaksanaan }} Hari</span></td>
                <td>{{ $item->perihal }}</td>
                <td>
                    @if ($berkasItem)
                        <a href="{{ route('berkas.view', $berkasItem->id_berkas) }}" target="_blank" rel="noopener">
                            📄 {{ $berkasItem->nama_berkas ?? 'Lihat File' }}
                        </a>
                        @if ($item->berkas_bermasalah)
                            <span class="badge bg-danger ms-1" style="font-size:10px">⚠️ Bermasalah</span>
                        @endif
                    @else
                        <span class="text-muted" style="font-size:12px">Tidak ada file</span>
                    @endif
                </td>
                <td>
                    <span class="badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td>
                    @if ($st === 'disetujui')
                        <a href="{{ url('/download-surat-tugas/' . $item->id_surat_tugas) }}"
                           class="btn-download-pdf" target="_blank">
                            📥 Unduh PDF
                        </a>
                    @else
                        <span class="text-muted" style="font-size:12px">—</span>
                    @endif
                </td>
                <td>
                    <div class="action-group">

                        {{-- DETAIL --}}
                        <button class="btn btn-detail btn-show-detail"
                                data-pengusul="{{ e($pegawai->nama_lengkap ?? '-') }}"
                                data-nomor-id="{{ e($nomorId) }}"
                                data-waktu="{{ \Carbon\Carbon::parse($item->waktu_pelaksanaan)->format('d M Y') }}"
                                data-lama="{{ $item->lama_pelaksanaan }}"
                                data-perihal="{{ e($item->perihal) }}"
                                data-berkas="{{ $berkasItem ? route('berkas.view', $berkasItem->id_berkas) : '' }}"
                                data-berkas-nama="{{ $berkasItem->nama_berkas ?? '' }}"
                                data-berkas-bermasalah="{{ $item->berkas_bermasalah ? '1' : '0' }}"
                                data-status="{{ $statusLabel }}"
                                data-status-class="{{ $statusClass }}"
                                data-anggota="{{ $namaAnggota }}"
                                data-alasan="{{ e($item->alasan_penolakan ?? '') }}">
                            Detail
                        </button>

                        {{-- Menunggu Diproses: Perbarui + Hapus --}}
                        @if ($st === 'menunggu diproses')
                            <a href="{{ route('surat.edit', $item->id_surat_tugas) }}"
                               class="btn btn-perbarui">
                                <i class="bi bi-pencil-square me-1"></i>Perbarui
                            </a>
                            <a href="{{ route('surat.destroy', $item->id_surat_tugas) }}"
                               class="btn btn-hapus btn-delete">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </a>

                        {{-- Ditolak: Revisi --}}
                        @elseif ($st === 'ditolak (verifikasi)' || $st === 'ditolak (persetujuan)')
                            <a href="{{ route('surat.revisi', $item->id_surat_tugas) }}"
                               class="btn btn-revisi-kembali">
                                <i class="bi bi-arrow-clockwise me-1"></i>Revisi
                            </a>
                        @endif

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Belum ada data pengajuan</td>
            </tr>
            @endforelse

            </tbody>
            </table>
            </div>

            <div class="summary-box">
                <div class="summary-item">
                    <div class="icon blue">📅</div>
                    <div>
                        <small>Total Pengajuan</small>
                        <h4>{{ $surat->count() }} Surat</h4>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="summary-item">
                    <div class="icon green">✔</div>
                    <div>
                        <small>Disetujui</small>
                        <h4>{{ $surat->where('status', 'disetujui')->count() }} Surat</h4>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="summary-item">
                    <div class="icon purple">⏱</div>
                    <div>
                        <small>Total Hari</small>
                        <h4>{{ $surat->sum('lama_pelaksanaan') }} Hari</h4>
                    </div>
                </div>
            </div>

            <div class="info-box">
                ℹ️ Semua surat tugas menunggu verifikasi dari operator dan disetujui oleh pimpinan.
            </div>

        </div>
    </div>
</div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content modal-detail-content">

            <div class="modal-detail-header">
                <div class="modal-header-left">
                    <div class="modal-icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <h5>Detail Pengajuan Surat Tugas</h5>
                    </div>
                </div>
                <button class="btn-close-modal" data-bs-dismiss="modal">✕</button>
            </div>

            <div class="modal-body p-4">

                <div class="detail-grid-2">
                    <div class="detail-field">
                        <div class="detail-field-label">Pengusul</div>
                        <div class="detail-field-value fw-bold" id="detail-pengusul">—</div>
                        <div class="detail-field-value text-muted" id="detail-nomor-id" style="font-size:12px">—</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Status</div>
                        <div class="detail-field-value" id="detail-status">—</div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="detail-grid-2">
                    <div class="detail-field">
                        <div class="detail-field-label">Waktu Pelaksanaan</div>
                        <div class="detail-field-value fw-bold" id="detail-waktu">—</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Lama Pelaksanaan</div>
                        <div class="detail-field-value fw-bold" id="detail-lama">—</div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="detail-field mb-3">
                    <div class="detail-field-label">Perihal</div>
                    <div class="detail-field-value" id="detail-perihal">—</div>
                </div>

                <div class="detail-grid-2">
                    <div class="detail-field">
                        <div class="detail-field-label">Daftar Anggota</div>
                        <div class="detail-field-value" id="detail-anggota">—</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Berkas Pendukung</div>
                        <div class="detail-field-value" id="detail-berkas">—</div>
                    </div>
                </div>

                {{-- Status boxes --}}
                <div id="box-menunggu-diproses" class="detail-box-menunggu-diproses d-none mt-4">
                    <i class="bi bi-hourglass-split me-2"></i>
                    Pengajuan sedang <strong>menunggu diproses</strong> oleh operator.
                </div>
                <div id="box-sedang-diverifikasi" class="detail-box-sedang-diverifikasi d-none mt-4">
                    <i class="bi bi-search me-2"></i>
                    Pengajuan sedang <strong>diverifikasi</strong> oleh operator.
                </div>
                <div id="box-menunggu-persetujuan" class="detail-box-menunggu-persetujuan d-none mt-4">
                    <i class="bi bi-person-check me-2"></i>
                    Pengajuan telah diverifikasi dan sedang <strong>menunggu persetujuan</strong> pimpinan.
                </div>
                <div id="box-ditolak-verifikasi" class="detail-box-ditolak-verifikasi d-none mt-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-x-circle-fill" style="font-size:18px;color:#c62828;margin-top:2px"></i>
                        <div>
                            <div style="font-weight:700;color:#c62828;font-size:14px;margin-bottom:4px">
                                Ditolak oleh Operator
                            </div>
                            <div id="detail-alasan-verifikasi" style="font-size:13px;color:#555"></div>
                        </div>
                    </div>
                </div>
                <div id="box-disetujui" class="detail-box-disetujui d-none mt-4">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Pengajuan telah <strong>disetujui</strong>. Silakan unduh surat tugas.
                </div>
                <div id="box-ditolak-persetujuan" class="detail-box-ditolak-persetujuan d-none mt-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-x-circle-fill" style="font-size:18px;color:#c62828;margin-top:2px"></i>
                        <div>
                            <div style="font-weight:700;color:#c62828;font-size:14px;margin-bottom:4px">
                                Ditolak oleh Pimpinan
                            </div>
                            <div id="detail-alasan-persetujuan" style="font-size:13px;color:#555"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-detail-footer">
                <button class="btn-tutup" data-bs-dismiss="modal">← Tutup</button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/script.js') }}"></script>

{{-- Patch JS: tambahkan data-nomor-id ke modal --}}
<script>
document.querySelectorAll('.btn-show-detail').forEach(btn => {
    btn.addEventListener('click', function () {
        const nomorId = this.dataset.nomorId ?? '-';
        const el = document.getElementById('detail-nomor-id');
        if (el) el.textContent = nomorId;
    });
});
</script>

</body>
</html>