{{-- resources/views/jabatanfungsional/readjabatanfungsional.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Jabatan Fungsional')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jabatanfungsional.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="col main">

    {{-- ── Header ── --}}
    <div class="page-header">
        <div class="ph-icon"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <h1>Jabatan Fungsional</h1>
            <p>Kelola dan pantau riwayat kenaikan jabatan fungsional Anda</p>
        </div>
    </div>

    <div class="main-inner">

        {{-- ── Jabfung aktif bar ── --}}
        <div class="jabfung-aktif-bar">
            <div class="jab-left">
                <div class="jab-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div>
                    <div class="jab-sublabel">Jabatan Fungsional Aktif</div>
                    @if ($pegawai->jabatanFungsional)
                        <div class="jab-nama">{{ $pegawai->jabatanFungsional->nama_jabfung }}</div>
                    @else
                        <div class="jab-nama" style="color:#9ca3af;">Belum Ada Jabatan Fungsional</div>
                    @endif
                    <div class="jab-sub">{{ $pegawai->nama_lengkap }}</div>
                </div>
            </div>
            <a href="{{ route('jabatanfungsional.create') }}" class="btn-ajukan-top">
                <i class="bi bi-plus-circle"></i> Ajukan Kenaikan
            </a>
        </div>

        {{-- ── Tabel riwayat ── --}}
        <div class="table-responsive">
            <table class="table tbl-jabfung align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jabfung Diajukan</th>
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
                        $statusInfo      = $row->status_info;
                        $labelBerkas     = [
                            'sk_cpns'   => 'SK CPNS',
                            'sk_pns'    => 'SK PNS',
                            'pak'       => 'PAK',
                            'publikasi' => 'Publikasi',
                        ];
                        $bermasalahArr   = $row->berkas_bermasalah ?? [];
                    @endphp
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>
                            <strong>{{ $row->jabatanFungsional->nama_jabfung ?? '—' }}</strong><br>
                            <small class="text-muted">
                                {{ ($row->jabatanFungsional->jenis_jabfung ?? '') === 'dosen' ? 'Dosen' : 'Tenaga Kependidikan' }}
                            </small>
                        </td>
                        <td>
                            {{ $row->tanggal_pengajuan
                                ? \Carbon\Carbon::parse($row->tanggal_pengajuan)->translatedFormat('d M Y')
                                : '—' }}
                        </td>
                        <td>
                            @if ($row->nomor_usulan)
                                <code>{{ $row->nomor_usulan }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($row->berkas as $b)
                                <a href="{{ Storage::url($b->file_path) }}"
                                   target="_blank"
                                   class="file-chip d-flex {{ in_array($b->jenis_berkas, $bermasalahArr) ? 'file-chip-bermasalah' : '' }}">
                                    <i class="bi bi-file-earmark-pdf-fill flex-shrink-0"></i>
                                    <span>{{ $labelBerkas[$b->jenis_berkas] ?? $b->jenis_berkas }}</span>
                                    @if (in_array($b->jenis_berkas, $bermasalahArr))
                                        <i class="bi bi-exclamation-circle-fill ms-1 text-danger" title="Perlu direvisi"></i>
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
                            @if (!empty($row->keterangan_tambahan) && in_array($row->status_pengajuan, ['tolak_verifikasi','tolak_persetujuan']))
                                <span class="catatan-tolak">
                                    <i class="bi bi-chat-left-text me-1"></i>
                                    {{ $row->keterangan_tambahan }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">

                                {{-- Menunggu → Perbarui, Hapus, Detail --}}
                                @if ($row->status_pengajuan === 'menunggu')
                                    <a href="{{ route('jabatanfungsional.edit', $row->id_pengajuan) }}"
                                       class="btn btn-primary btn-aksi">
                                        <i class="bi bi-pencil me-1"></i>Perbarui
                                    </a>
                                    <button class="btn btn-danger btn-aksi"
                                            onclick="confirmDelete({{ $row->id_pengajuan }}, '{{ csrf_token() }}')">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                    <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                            class="btn btn-secondary btn-aksi">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>

                                {{-- Verifikasi / Persetujuan → Detail --}}
                                @elseif (in_array($row->status_pengajuan, ['verifikasi', 'persetujuan']))
                                    <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                            class="btn btn-secondary btn-aksi">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>

                                {{-- Disetujui → Detail + Download --}}
                                @elseif ($row->status_pengajuan === 'disetujui')
                                    <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                            class="btn btn-secondary btn-aksi">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                    <a href="{{ route('jabatanfungsional.download', $row->id_pengajuan) }}"
                                       class="btn btn-success btn-aksi">
                                        <i class="bi bi-download me-1"></i>Surat
                                    </a>

                                {{-- Tolak Verifikasi → Detail + Revisi --}}
                                @elseif ($row->status_pengajuan === 'tolak_verifikasi')
                                    <button onclick="bukaDetail({{ $row->id_pengajuan }})"
                                            class="btn btn-secondary btn-aksi">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                    <a href="{{ route('jabatanfungsional.revisi', $row->id_pengajuan) }}"
                                       class="btn btn-warning btn-aksi">
                                        <i class="bi bi-arrow-repeat me-1"></i>Revisi
                                    </a>

                                {{-- Tolak Persetujuan → Detail --}}
                                @elseif ($row->status_pengajuan === 'tolak_persetujuan')
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
                            Belum ada pengajuan jabatan fungsional.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Legenda status ── --}}
        <div class="legenda-status">
            <small class="text-muted fw-semibold">Keterangan:</small>
            <small><span class="status-badge badge-menunggu"><i class="bi bi-hourglass-split"></i> Menunggu Diproses</span></small>
            <small><span class="status-badge badge-verifikasi"><i class="bi bi-search"></i> Sedang Diverifikasi</span></small>
            <small><span class="status-badge badge-persetujuan"><i class="bi bi-clock-history"></i> Menunggu Persetujuan</span></small>
            <small><span class="status-badge badge-disetujui"><i class="bi bi-check-circle-fill"></i> Disetujui</span></small>
            <small><span class="status-badge badge-ditolak"><i class="bi bi-x-circle-fill"></i> Ditolak</span></small>
        </div>

        {{-- ── Panel Detail (inline, bukan iframe) ── --}}
        <div id="panelDetail" class="detail-panel mt-4" style="display:none;">
            <div class="detail-panel-inner">

                <div class="detail-panel-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="dh-icon"><i class="bi bi-briefcase-fill"></i></div>
                        <div>
                            <h5 id="dp-title">Detail Pengajuan Jabatan Fungsional</h5>
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
                            <div class="info-row"><div class="info-label">Nama Lengkap</div><p class="info-value" id="dp-nama">—</p></div>
                            <div class="info-row"><div class="info-label" id="dp-id-label">NIP / NIDN</div><p class="info-value" id="dp-id-value">—</p></div>
                            <div class="info-row"><div class="info-label">Jabfung Saat Ini</div><p class="info-value" id="dp-jabfung-now">—</p></div>
                            <div class="info-row"><div class="info-label">Jabfung Diajukan</div><p class="info-value fw-bold" id="dp-jabfung-target">—</p></div>
                            <div class="info-row"><div class="info-label">Jenis Pegawai</div><p class="info-value" id="dp-jenis">—</p></div>
                            <div class="info-row"><div class="info-label">Nomor SK / Usulan</div><p class="info-value" id="dp-nomor">—</p></div>
                            <div class="info-row"><div class="info-label">Tanggal Pengajuan</div><p class="info-value" id="dp-tanggal">—</p></div>
                            <div class="info-row"><div class="info-label">Status</div><div id="dp-status-badge"></div></div>

                            {{-- Ladder dosen --}}
                            <div id="dp-ladder-wrap" class="mt-3" style="display:none;">
                                <div class="section-title-sm"><i class="bi bi-arrow-up-circle me-1"></i>Jenjang Jabfung</div>
                                <div id="dp-ladder" class="ladder-detail"></div>
                            </div>
                        </div>

                        {{-- Catatan operator --}}
                        <div class="col-md-6">
                            <div class="section-title-sm"><i class="bi bi-chat-left-text me-1"></i>Catatan Operator</div>
                            <div id="dp-catatan-wrap"></div>

                            {{-- Berkas bermasalah info --}}
                            <div id="dp-bermasalah-wrap" class="mt-3" style="display:none;">
                                <div class="section-title-sm"><i class="bi bi-exclamation-triangle me-1 text-danger"></i>Berkas yang Perlu Direvisi</div>
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

{{-- Data pengajuan untuk JS (JSON) --}}
<script>
window.PEGAWAI_JENIS = '{{ $pegawai->jenis_jabfung ?? "tendik" }}';
window.PEGAWAI_NAMA  = '{{ $pegawai->nama_lengkap }}';
window.PEGAWAI_ID_VALUE = '{{ ($pegawai->jenis_jabfung ?? "tendik") === "dosen" ? ($pegawai->nidn ?? "-") : ($pegawai->nip ?? "-") }}';
window.PEGAWAI_ID_LABEL = '{{ ($pegawai->jenis_jabfung ?? "tendik") === "dosen" ? "NIDN" : "NIP" }}';
window.JABFUNG_NOW   = '{{ $pegawai->jabatanFungsional->nama_jabfung ?? "Belum ada" }}';

@php
    $pengajuanJson = $pengajuan->map(function($row) {
        return [
            'id'              => $row->id_pengajuan,
            'jabfung_target'  => $row->jabatanFungsional->nama_jabfung ?? '—',
            'jabfung_urutan'  => $row->jabatanFungsional->urutan ?? 0,
            'jenis_jabfung'   => $row->jabatanFungsional->jenis_jabfung ?? 'tendik',
            'nomor_usulan'    => $row->nomor_usulan ?? '—',
            'tanggal'         => $row->tanggal_pengajuan
                                 ? \Carbon\Carbon::parse($row->tanggal_pengajuan)->translatedFormat('d F Y')
                                 : '—',
            'status'          => $row->status_pengajuan,
            'status_label'    => $row->status_info['label'],
            'status_class'    => $row->status_info['class'],
            'status_icon'     => $row->status_info['icon'],
            'keterangan'      => $row->keterangan_tambahan ?? '',
            'berkas_bermasalah' => $row->berkas_bermasalah ?? [],
            'berkas'          => $row->berkas->map(function($b) {
                return [
                    'jenis'  => $b->jenis_berkas,
                    'label'  => ['sk_cpns'=>'SK CPNS','sk_pns'=>'SK PNS','pak'=>'PAK','publikasi'=>'Publikasi'][$b->jenis_berkas] ?? $b->jenis_berkas,
                    'url'    => Storage::url($b->file_path),
                ];
            })->values(),
            'ladder' => [],
        ];
    });

    $dosenLadder = ($pegawai->jenis_jabfung ?? 'tendik') === 'dosen'
        ? \App\Models\JabatanFungsional::dosen()->get()->map(function($l) {
            return ['nama' => $l->nama_jabfung, 'urutan' => $l->urutan];
          })
        : [];
@endphp

window.PENGAJUAN_DATA = @json($pengajuanJson);
window.DOSEN_LADDER = @json($dosenLadder);
</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/jabatanfungsional.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        @if (session('success') === 'sukses')
            showNotif('success', 'Pengajuan Terkirim!', 'Pengajuan jabatan fungsional berhasil dikirim ke operator.');
        @elseif (session('success') === 'perbarui')
            showNotif('success', 'Berhasil Diperbarui!', 'Perubahan pengajuan berhasil disimpan.');
        @elseif (session('success') === 'hapus')
            showNotif('success', 'Dihapus!', 'Pengajuan berhasil dihapus.');
        @elseif (session('success') === 'revisi')
            showNotif('success', 'Revisi Terkirim!', 'Berkas revisi berhasil dikirim. Pengajuan kembali menunggu diproses.');
        @elseif (session('error'))
            showNotif('error', 'Gagal!', '{{ session("error") }}');
        @endif
    </script>
@endpush