@extends('layouts.app')

@section('title', 'Persetujuan Pangkat Golongan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/operator/verifikasi.css') }}">
@endpush

@section('content')
    <div class="main">

        <div class="header-box">
            <div class="header-left">
                <div class="icon-box"><i class="bi bi-award-fill"></i></div>
                <div>
                    <h1 class="page-title">Persetujuan Pangkat Golongan</h1>
                    <p class="page-subtitle">Panel pimpinan — periksa dan setujui pengajuan masuk</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="container-box">
            <ul class="nav nav-tabs tab-verifikasi mb-4">
                <li class="nav-item">
                    <button class="nav-link {{ request('tab') !== 'riwayat' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#panel-antrean">
                        Antrean <span class="badge bg-danger ms-1">{{ $antrian->total() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ request('tab') === 'riwayat' ? 'active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#panel-riwayat">Riwayat</button>
                </li>
            </ul>

            <div class="tab-content">

                {{-- TAB ANTREAN --}}
                <div class="tab-pane fade {{ request('tab') !== 'riwayat' ? 'show active' : '' }}" id="panel-antrean">
                    <form method="GET" action="{{ route('pimpinan.persetujuan.panggol') }}" class="row g-2 mb-3">
                        <input type="hidden" name="tab" value="antrean">
                        <div class="col-md-8">
                            <input name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nama pegawai atau pangkat/golongan...">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-pengajuan w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>

                    <div class="info-text mb-3">Menampilkan {{ $antrian->count() }} dari {{ $antrian->total() }} data</div>

                    @if ($antrian->isEmpty())
                        <div class="antrean-kosong">
                            <i class="bi bi-inbox"></i>
                            <p>Tidak ada pengajuan yang menunggu persetujuan</p>
                        </div>
                    @else
                        <div class="antrean-list">
                            @foreach ($antrian as $item)
                                @php
                                    $pegawai = $item->berkas->pegawai ?? null;
                                    $pengajuan = $item->berkas->pengajuan ?? null;

                                    $nama = $pegawai?->nama_lengkap ?? '-';
                                    $nip = $pegawai?->nip ?? '-';
                                    $nidn = $pegawai?->nidn ?? '-';

                                    $namaParts = explode(' ', $nama);
                                    $inisial = strtoupper(
                                        isset($namaParts[0][0]) ? $namaParts[0][0] . ($namaParts[1][0] ?? '') : 'U',
                                    );

                                    $panggolNm = $pengajuan?->pangkatGolongan?->jenis_pangkat ?? '-';

                                    $tgl = $item->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y')
                                        : '-';
                                @endphp

                                <div class="antrean-row">
                                    <div class="antrean-avatar">{{ $inisial }}</div>
                                    <div class="antrean-info">
                                        <div class="antrean-nama">{{ $nama }}</div>
                                        <div class="antrean-meta">
                                            NIP: {{ $nip }}
                                            <span class="chip-jabatan">{{ $panggolNm }}</span>
                                        </div>
                                    </div>
                                    <div class="antrean-tgl"><i class="bi bi-calendar3"></i> {{ $tgl }}</div>

                                    <div class="aksi-cell">
                                        <button class="btn-periksa" onclick="bukaPanel('{{ $item->id_verifikasi }}')">
                                            <i class="bi bi-file-earmark-search"></i> Periksa Pengajuan
                                        </button>
                                    </div>
                                </div>

                                <div class="detail-overlay" id="overlay-{{ $item->id_verifikasi }}">
                                    <div class="detail-modal">
                                        <div class="panel-head">
                                            <i class="bi bi-clipboard2-check-fill"
                                                style="font-size:18px;color:#d32f2f;flex-shrink:0"></i>
                                            <h2 class="panel-title">{{ $nama }}</h2>
                                            <button class="btn-panel-close"
                                                onclick="tutupPanel('{{ $item->id_verifikasi }}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <div class="panel-body">
                                            <div class="panel-section-title">Data Pegawai</div>
                                            <div class="detail-grid">
                                                <div class="detail-field">
                                                    <span class="detail-label">Nama Lengkap</span>
                                                    <span class="detail-val">{{ $nama }}</span>
                                                </div>
                                                <div class="detail-field">
                                                    <span class="detail-label">NIP</span>
                                                    <span class="detail-val">{{ $nip }}</span>
                                                </div>
                                                <div class="detail-field">
                                                    <span class="detail-label">NIDN</span>
                                                    <span class="detail-val">{{ $nidn }}</span>
                                                </div>
                                                <div class="detail-field">
                                                    <span class="detail-label">Status Pegawai</span>
                                                    <span class="detail-val">{{ $pegawai?->status_pegawai ?? '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="panel-section-title">Detail Pengajuan Pangkat / Golongan</div>
                                            <div class="detail-grid">
                                                <div class="detail-field">
                                                    <span class="detail-label">Pangkat / Golongan Diajukan</span>
                                                    <span class="detail-val highlight">{{ $panggolNm }}</span>
                                                </div>
                                                <div class="detail-field">
                                                    <span class="detail-label">Tanggal Pengajuan</span>
                                                    <span class="detail-val">{{ $tgl }}</span>
                                                </div>
                                                @if ($panggolNm?->nomor_sk)
                                                    <div class="detail-field full">
                                                        <span class="detail-label">Nomor SK</span>
                                                        <span class="detail-val">{{ $panggol->nomor_sk }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @php
                                                $semuaBerkas = $item->berkas->pengajuan->berkas ?? collect([$item->berkas]);
                                            @endphp

                                            @if ($semuaBerkas->count() > 0)
                                                <div class="panel-section-title">Berkas yang Diunggah</div>
                                                <div class="berkas-list" style="display:flex; flex-direction:column; gap:10px;">
                                                    @foreach ($semuaBerkas as $file)
                                                        @if ($file && $file->file_path)
                                                            <div class="berkas-item">
                                                                <div class="berkas-icon">
                                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                                </div>
                                                                <div class="berkas-detail">
                                                                    <span class="berkas-nama">
                                                                        {{ $file->nama_berkas ?? 'Berkas Pendukung' }}
                                                                    </span>
                                                                    <span class="berkas-file">{{ basename($file->file_path) }}</span>
                                                                </div>
                                                                <a href="{{ asset('storage/' . $file->file_path) }}"
                                                                    target="_blank" class="btn-lihat-berkas">
                                                                    <i class="bi bi-eye"></i> Lihat
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div id="tolak-{{ $item->id_verifikasi }}" style="display:none">
                                                <div class="tolak-divider">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    Keterangan Penolakan
                                                </div>
                                                <div class="tolak-block">
                                                    <div class="tolak-block-header" style="margin-bottom:10px;margin-top:16px">
                                                        <div class="tolak-block-icon" style="background:#f3f4f6;color:#6b7280">
                                                            <i class="bi bi-chat-left-text-fill"></i>
                                                        </div>
                                                        <div>
                                                            <div class="tolak-block-title">
                                                                Catatan Penolakan <span class="badge-wajib">Wajib</span>
                                                            </div>
                                                            <div class="tolak-block-sub">Jelaskan alasan penolakan</div>
                                                        </div>
                                                    </div>

                                                    <textarea id="catatan-{{ $item->id_verifikasi }}" class="catatan-area"
                                                        placeholder="Contoh: Berkas tidak sesuai dengan ketentuan yang berlaku..."></textarea>

                                                    <p class="catatan-hint" id="hint-{{ $item->id_verifikasi }}" style="display:none">
                                                        <i class="bi bi-exclamation-circle-fill"></i> Catatan wajib diisi.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel-footer">
                                            {{-- SETUJU --}}
                                            <form method="POST" action="{{ route('pimpinan.persetujuan.setuju', $item->id_verifikasi) }}">
                                                @csrf
                                                <button type="submit" id="btn-verif-{{ $item->id_verifikasi }}"
                                                    class="btn-verifikasi-modal">
                                                    <i class="bi bi-check-circle-fill"></i> Setujui
                                                </button>
                                            </form>

                                            {{-- TOLAK --}}
                                            <form id="form-tolak-{{ $item->id_verifikasi }}" method="POST"
                                                action="{{ route('pimpinan.persetujuan.tolak', $item->id_verifikasi) }}"
                                                style="display:contents">
                                                @csrf
                                                <input type="hidden" name="keterangan" id="input-catatan-{{ $item->id_verifikasi }}">
                                                <button type="button" id="btn-tolak-{{ $item->id_verifikasi }}"
                                                    class="btn-tolak-modal" onclick="aksiTolak('{{ $item->id_verifikasi }}')">
                                                    <i class="bi bi-x-circle-fill"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">Halaman {{ $antrian->currentPage() }} dari {{ $antrian->lastPage() }}</small>
                            {{ $antrian->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                {{-- TAB RIWAYAT --}}
                <div class="tab-pane fade {{ request('tab') === 'riwayat' ? 'show active' : '' }}" id="panel-riwayat">
                    <form method="GET" action="{{ route('pimpinan.persetujuan.panggol') }}" class="row g-2 mb-3">
                        <input type="hidden" name="tab" value="riwayat">
                        <div class="col-md-5">
                            <input name="qr" value="{{ request('qr') }}" class="form-control"
                                placeholder="Cari nama pegawai atau pangkat/golongan...">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="Semua" {{ request('status', 'Semua') === 'Semua' ? 'selected' : '' }}>
                                    Semua Status</option>
                                <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>
                                    Disetujui</option>
                                <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>
                                    Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-pengajuan w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>

                    <div class="info-text mb-3">Menampilkan {{ $riwayat->count() }} dari {{ $riwayat->total() }} data</div>

                    <div class="table-responsive custom-table">
                        <table class="table data-table mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Pangkat / Golongan Diajukan</th>
                                    <th>Status</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>Tgl Diproses</th>
                                    <th>Catatan Pimpinan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $i => $item)
                                    @php
                                        $pegawai = $item->berkas->pegawai ?? null;
                                        $pengajuan = $item->berkas->pengajuan ?? null;
                                    @endphp
                                    <tr>
                                        <td>{{ $riwayat->firstItem() + $i }}</td>
                                        <td style="text-align:left">{{ $pegawai?->nama_lengkap ?? '-' }}</td>
                                        <td>{{ $item->berkas->pengajuan?->pangkatGolongan?->jenis_pangkat ?? '-' }}</td>
                                        <td>
                                            <span class="badge-status {{ $item->status_verifikasi === 'Disetujui' ? 'badge-ok' : 'badge-tolak' }}">
                                                @if ($item->status_verifikasi === 'Disetujui')
                                                    <i class="bi bi-check-circle-fill"></i> Disetujui
                                                @else
                                                    <i class="bi bi-x-circle-fill"></i> Ditolak
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y') : '-' }}</td>
                                        <td>{{ $item->tanggal_proses ? \Carbon\Carbon::parse($item->tanggal_proses)->translatedFormat('d M Y') : '-' }}</td>
                                        <td style="text-align:left;max-width:220px;word-break:break-word">{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Halaman {{ $riwayat->currentPage() }} dari {{ $riwayat->lastPage() }}</small>
                        {{ $riwayat->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function bukaPanel(id) {
            document.getElementById('overlay-' + id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function tutupPanel(id) {
            document.getElementById('overlay-' + id).classList.remove('open');
            document.body.style.overflow = '';

            const tolak = document.getElementById('tolak-' + id);
            if (tolak) tolak.style.display = 'none';

            const catatan = document.getElementById('catatan-' + id);
            if (catatan) catatan.value = '';

            const hint = document.getElementById('hint-' + id);
            if (hint) hint.style.display = 'none';

            const btnVerif = document.getElementById('btn-verif-' + id);
            if (btnVerif) {
                btnVerif.disabled = false;
                btnVerif.style.opacity = '';
            }

            const btnTolak = document.getElementById('btn-tolak-' + id);
            if (btnTolak) btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Tolak';
        }

        function aksiTolak(id) {
            const tolakSection = document.getElementById('tolak-' + id);
            const btnTolak = document.getElementById('btn-tolak-' + id);
            const btnVerif = document.getElementById('btn-verif-' + id);

            if (tolakSection.style.display === 'none') {
                tolakSection.style.display = 'block';
                setTimeout(() => tolakSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                }), 80);

                btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Konfirmasi Tolak';

                if (btnVerif) {
                    btnVerif.disabled = true;
                    btnVerif.style.opacity = '0.35';
                }
                return;
            }

            const catatan = document.getElementById('catatan-' + id).value.trim();
            const hint = document.getElementById('hint-' + id);

            if (!catatan) {
                const ta = document.getElementById('catatan-' + id);
                ta.classList.add('catatan-error');
                if (hint) hint.style.display = 'flex';
                ta.focus();
                return;
            }

            document.getElementById('input-catatan-' + id).value = catatan;
            document.getElementById('form-tolak-' + id).submit();
        }

        document.querySelectorAll('.detail-overlay').forEach(el => {
            el.addEventListener('click', function(e) {
                if (e.target === this) tutupPanel(this.id.replace('overlay-', ''));
            });
        });
    </script>
@endpush