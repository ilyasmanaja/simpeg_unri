@extends('layouts.app')

@section('title', 'Verifikasi Surat Tugas')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/operator/verifikasi.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('content')
    <div class="main">

        <div class="header-box">
            <div class="header-left">
                <div class="icon-box"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <h1 class="page-title">Verifikasi Surat Tugas</h1>
                    <p class="page-subtitle">Panel operator — periksa dan verifikasi pengajuan masuk</p>
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
                    <form method="GET" action="{{ url('operator/verifikasi/surat-tugas') }}" class="row g-2 mb-3">
                        <input type="hidden" name="tab" value="antrean">
                        <div class="col-md-8">
                            <input name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nama pengusul atau perihal...">
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
                            <p>Tidak ada pengajuan yang menunggu verifikasi</p>
                        </div>
                    @else
                        <div class="antrean-list">
                            @foreach ($antrian as $item)
                                @php
                                    $pegawai = $item->berkas->pegawai ?? null;
                                    $st = $item->berkas->suratTugas ?? null;

                                    // KOREKSI: Menggunakan nama_lengkap
                                    $nama = $pegawai?->nama_lengkap ?? '-';
                                    $nip = $pegawai?->nip ?? '-';
                                    $inisial = strtoupper(
                                        implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $nama), 0, 2))),
                                    );

                                    $perihal = $st?->perihal ?? '-';

                                    // KOREKSI: Menggunakan waktu_pelaksanaan (karena tipenya DATE, kita format)
                                    $tgl = $item->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y')
                                        : '-';
                                    $status = $item->status_verifikasi;
                                @endphp
                                <div class="antrean-row">
                                    <div class="antrean-avatar">{{ $inisial }}</div>
                                    <div class="antrean-info">
                                        <div class="antrean-nama">{{ $nama }}</div>
                                        <div class="antrean-meta">NIP: {{ $nip }} <span
                                                class="chip-jabatan">{{ $perihal }}</span></div>
                                    </div>
                                    <div class="antrean-tgl"><i class="bi bi-calendar3"></i> {{ $tgl }}</div>
                                    <div class="aksi-cell">
                                        @if ($status === 'Menunggu Diproses')
                                            <form method="POST"
                                                action="{{ url('operator/verifikasi/' . $item->id_verifikasi . '/terima') }}">
                                                @csrf
                                                <button type="submit" class="btn-terima"><i
                                                        class="bi bi-check-circle-fill"></i> Terima</button>
                                            </form>
                                        @else
                                            <button class="btn-periksa" onclick="bukaPanelST({{ $item->id_verifikasi }})">
                                                <i class="bi bi-file-earmark-search"></i> Periksa Berkas
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="detail-overlay" id="overlay-st-{{ $item->id_verifikasi }}">
                                    <div class="detail-modal">
                                        <div class="panel-head">
                                            <i class="bi bi-clipboard2-check-fill"
                                                style="font-size:18px;color:#d32f2f;flex-shrink:0"></i>
                                            <h2 class="panel-title">{{ $nama }}</h2>
                                            <button class="btn-panel-close"
                                                onclick="tutupPanelST({{ $item->id_verifikasi }})"><i
                                                    class="bi bi-x-lg"></i></button>
                                        </div>
                                        <div class="panel-body">
                                            <div class="panel-section-title">Data Pengusul</div>
                                            <div class="detail-grid">
                                                <div class="detail-field"><span class="detail-label">Nama
                                                        Pengusul</span><span class="detail-val">{{ $nama }}</span>
                                                </div>
                                                <div class="detail-field"><span class="detail-label">NIP</span><span
                                                        class="detail-val">{{ $nip }}</span></div>
                                                <div class="detail-field"><span class="detail-label">Tanggal
                                                        Pengajuan</span><span class="detail-val">{{ $tgl }}</span>
                                                </div>
                                            </div>
                                            <div class="panel-section-title">Detail Surat Tugas</div>
                                            <div class="detail-grid">
                                                <div class="detail-field full"><span
                                                        class="detail-label">Perihal</span><span
                                                        class="detail-val highlight">{{ $st?->perihal ?? '-' }}</span>
                                                </div>
                                                <div class="detail-field full"><span class="detail-label">Tujuan</span><span
                                                        class="detail-val">{{ $st?->tujuan ?? '-' }}</span></div>
                                                <div class="detail-field"><span class="detail-label">Waktu
                                                        Pelaksanaan</span><span
                                                        class="detail-val">{{ $st?->waktu ?? '-' }}</span></div>
                                                <div class="detail-field"><span class="detail-label">Nomor
                                                        Surat</span><span
                                                        class="detail-val">{{ $st?->nomor_surat ?? '-' }}</span></div>
                                            </div>
                                            @if ($st && $st->anggota && $st->anggota->count())
                                                <div class="panel-section-title">Daftar Anggota</div>
                                                <div style="padding:4px 0 12px;display:flex;flex-wrap:wrap;gap:6px;">
                                                    @foreach ($st->anggota as $anggota)
                                                        <span
                                                            class="chip-jabatan">{{ $anggota->pegawai?->nama_lengkap ?? '-' }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if ($item->berkas && $item->berkas->file_path)
                                                <div class="panel-section-title">Berkas yang Diunggah</div>
                                                <div class="berkas-list">
                                                    <div class="berkas-item">
                                                        <div class="berkas-icon"><i
                                                                class="bi bi-file-earmark-pdf-fill"></i></div>
                                                        <div class="berkas-detail">
                                                            <span class="berkas-nama">Surat Tugas</span>
                                                            <span
                                                                class="berkas-file">{{ basename($item->berkas->file_path) }}</span>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $item->berkas->file_path) }}"
                                                            target="_blank" class="btn-lihat-berkas">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            <div id="tolak-st-{{ $item->id_verifikasi }}" style="display:none">
                                                <div class="tolak-divider"><i class="bi bi-exclamation-triangle-fill"></i>
                                                    Keterangan Penolakan</div>
                                                <div class="tolak-block">
                                                    <div class="tolak-block-header" style="margin-bottom:10px">
                                                        <div class="tolak-block-icon"
                                                            style="background:#f3f4f6;color:#6b7280"><i
                                                                class="bi bi-chat-left-text-fill"></i></div>
                                                        <div>
                                                            <div class="tolak-block-title">Catatan untuk Pegawai <span
                                                                    class="badge-wajib">Wajib</span></div>
                                                            <div class="tolak-block-sub">Jelaskan alasan penolakan</div>
                                                        </div>
                                                    </div>
                                                    <textarea id="catatan-st-{{ $item->id_verifikasi }}" class="catatan-area"
                                                        placeholder="Contoh: Dokumen pendukung tidak lengkap atau tidak sesuai ketentuan..."></textarea>
                                                    <p class="catatan-hint" id="hint-st-{{ $item->id_verifikasi }}"
                                                        style="display:none">
                                                        <i class="bi bi-exclamation-circle-fill"></i> Catatan wajib diisi.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <form method="POST"
                                                action="{{ url('operator/verifikasi/' . $item->id_verifikasi . '/verifikasi') }}">
                                                @csrf
                                                <button type="submit" id="btn-verif-st-{{ $item->id_verifikasi }}"
                                                    class="btn-verifikasi-modal">
                                                    <i class="bi bi-check-circle-fill"></i> Verifikasi
                                                </button>
                                            </form>
                                            <form id="form-tolak-st-{{ $item->id_verifikasi }}" method="POST"
                                                action="{{ url('operator/verifikasi/' . $item->id_verifikasi . '/tolak') }}"
                                                style="display:contents">
                                                @csrf
                                                <input type="hidden" name="keterangan"
                                                    id="input-catatan-st-{{ $item->id_verifikasi }}">
                                                <button type="button" id="btn-tolak-st-{{ $item->id_verifikasi }}"
                                                    class="btn-tolak-modal"
                                                    onclick="aksiTolakST({{ $item->id_verifikasi }})">
                                                    <i class="bi bi-x-circle-fill"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">Halaman {{ $antrian->currentPage() }} dari
                                {{ $antrian->lastPage() }}</small>
                            {{ $antrian->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                {{-- TAB RIWAYAT --}}
                <div class="tab-pane fade {{ request('tab') === 'riwayat' ? 'show active' : '' }}" id="panel-riwayat">
                    <form method="GET" action="{{ url('operator/verifikasi/surat-tugas') }}" class="row g-2 mb-3">
                        <input type="hidden" name="tab" value="riwayat">
                        <div class="col-md-5">
                            <input name="qr" value="{{ request('qr') }}" class="form-control"
                                placeholder="Cari nama pengusul atau perihal...">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="Semua" {{ request('status', 'Semua') === 'Semua' ? 'selected' : '' }}>
                                    Semua Status</option>
                                <option value="Terverifikasi"
                                    {{ request('status') === 'Terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-pengajuan w-100"><i class="bi bi-search"></i>
                                Cari</button>
                        </div>
                    </form>
                    <div class="info-text mb-3">Menampilkan {{ $riwayat->count() }} dari {{ $riwayat->total() }} data
                    </div>
                    <div class="table-responsive custom-table">
                        <table class="table data-table mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pengusul</th>
                                    <th>Perihal</th>
                                    <th>Status</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>Tgl Diproses</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $i => $item)
                                    @php
                                        $pegawai = $item->berkas->pegawai ?? null;
                                        $st = $item->berkas->suratTugas ?? null;
                                    @endphp
                                    <tr>
                                        <td>{{ $riwayat->firstItem() + $i }}</td>
                                        <td style="text-align:left">{{ $pegawai?->nama ?? '-' }}</td>
                                        <td style="text-align:left">{{ $st?->perihal ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge-status {{ $item->status_verifikasi === 'Terverifikasi' ? 'badge-ok' : 'badge-tolak' }}">
                                                @if ($item->status_verifikasi === 'Terverifikasi')
                                                    <i class="bi bi-send-check"></i> Terverifikasi
                                                @else
                                                    <i class="bi bi-x-circle"></i> Ditolak
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td>{{ $item->tanggal_proses ? \Carbon\Carbon::parse($item->tanggal_proses)->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td style="text-align:left;max-width:220px;word-break:break-word">
                                            {{ $item->keterangan ?? '-' }}</td>
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
                        <small class="text-muted">Halaman {{ $riwayat->currentPage() }} dari
                            {{ $riwayat->lastPage() }}</small>
                        {{ $riwayat->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function bukaPanelST(id) {
            document.getElementById('overlay-st-' + id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function tutupPanelST(id) {
            document.getElementById('overlay-st-' + id).classList.remove('open');
            document.body.style.overflow = '';
            const tolak = document.getElementById('tolak-st-' + id);
            if (tolak) tolak.style.display = 'none';
            const catatan = document.getElementById('catatan-st-' + id);
            if (catatan) catatan.value = '';
            const hint = document.getElementById('hint-st-' + id);
            if (hint) hint.style.display = 'none';
            const btnVerif = document.getElementById('btn-verif-st-' + id);
            if (btnVerif) {
                btnVerif.disabled = false;
                btnVerif.style.opacity = '';
            }
            const btnTolak = document.getElementById('btn-tolak-st-' + id);
            if (btnTolak) btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Tolak';
        }

        function aksiTolakST(id) {
            const tolakSection = document.getElementById('tolak-st-' + id);
            const btnTolak = document.getElementById('btn-tolak-st-' + id);
            const btnVerif = document.getElementById('btn-verif-st-' + id);
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
            const catatan = document.getElementById('catatan-st-' + id).value.trim();
            const hint = document.getElementById('hint-st-' + id);
            if (!catatan) {
                const ta = document.getElementById('catatan-st-' + id);
                ta.classList.add('catatan-error');
                if (hint) hint.style.display = 'flex';
                ta.focus();
                return;
            }
            document.getElementById('input-catatan-st-' + id).value = catatan;
            document.getElementById('form-tolak-st-' + id).submit();
        }
        document.querySelectorAll('.detail-overlay').forEach(el => {
            el.addEventListener('click', function(e) {
                if (e.target === this) tutupPanelST(this.id.replace('overlay-st-', ''));
            });
        });
    </script>
@endpush
