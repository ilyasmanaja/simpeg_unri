{{-- resources/views/dosen/pangkat/form.blade.php --}}
@extends('layouts.app')

@php
    // Mapping variabel dari controller
    $pengajuan = $data ?? null;
    $mode = $mode ?? 'create';

    $isRevisi = $mode === 'revisi';
    $isEdit = $mode === 'edit' && $pengajuan;
    $isCreate = !$isEdit && !$isRevisi;

    $formTitle = $isRevisi
        ? 'Revisi Pengajuan Pangkat dan Golongan'
        : ($isEdit
            ? 'Edit Pengajuan Pangkat dan Golongan'
            : 'Form Pengajuan Pangkat dan Golongan');

    $formSub = $isRevisi
        ? 'Unggah ulang berkas yang ditandai bermasalah oleh operator'
        : ($isEdit
            ? 'Ubah data pengajuan pangkat sebelum diproses operator'
            : 'Isi data dengan lengkap dan benar sesuai dokumen resmi');

    $bAda = collect($berkasAda ?? []);
    $berkasBermasalahArr = $berkasBermasalahArr ?? [];

    $pangkatSekarang = $pegawai->pangkatGolongan->jenis_pangkat ?? ($pegawai->jenis_pangkat ?? 'Belum ada pangkat');
    $namaPangkatTarget = $pengajuan ? $pengajuan->pangkatGolonganTarget->jenis_pangkat ?? '' : '';
@endphp

@section('title', $formTitle)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    {{-- Kita pinjam CSS dari jabfung agar tampilan formnya seragam --}}
    <link rel="stylesheet" href="{{ asset('css/jabatanfungsional.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dosen/data_pangkat_golongan/FormPangkatdanGolongan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="main">
        <div class="main-inner" style="padding-top: 32px;">
            <div class="container mb-5" style="max-width: 720px;">

                {{-- Breadcrumb --}}
                <nav class="mb-3">
                    <a href="{{ route('dosen.pangkat-golongan.index') }}" class="text-muted text-decoration-none"
                        style="font-size:0.85rem;">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </nav>

                {{-- Alert: Pengajuan Pending (Create) --}}
                @if ($isCreate && isset($adaPending) && $adaPending)
                    <div class="alert alert-warning alert-pending d-flex gap-2 align-items-start mb-3">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                        <div><strong>Perhatian:</strong> Kamu masih memiliki pengajuan yang sedang diproses.</div>
                    </div>
                @endif

                {{-- Alert: Sudah Puncak (Create) --}}
                @if ($isCreate && isset($sudahPuncak) && $sudahPuncak)
                    <div class="alert d-flex gap-2 align-items-start mb-3"
                        style="border-left:4px solid #b91c1c;border-radius:10px;background:#fef2f2;">
                        <i class="bi bi-patch-check-fill flex-shrink-0 mt-1 text-danger"></i>
                        <div>
                            <strong>Pangkat Tertinggi Tercapai.</strong>
                            Kamu sudah berada di pangkat golongan <strong>IV/e</strong>.
                        </div>
                    </div>
                @endif

                {{-- Alert: Revisi --}}
                @if ($isRevisi)
                    <div class="alert-revisi mb-3">
                        <div class="ar-header">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Pengajuan Ditolak — Perlu Revisi</strong>
                        </div>
                        @php
                            $labelBerkas = [
                                'sk_cpns' => 'SK CPNS',
                                'sk_pns' => 'SK PNS',
                                'pak' => 'PAK',
                                'publikasi' => 'Publikasi',
                            ];
                        @endphp
                        @if (!empty($berkasBermasalahArr))
                            <div class="ar-berkas-list mt-2">
                                <span class="ar-label">Berkas yang harus diganti:</span>
                                @foreach ($berkasBermasalahArr as $kb)
                                    <span class="ar-chip"><i
                                            class="bi bi-file-earmark-pdf-fill me-1"></i>{{ $labelBerkas[$kb] ?? $kb }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($pengajuan->keterangan_tambahan)
                            <div class="mt-2 text-muted" style="font-size: 0.8rem;">
                                <i>Catatan Operator: "{{ $pengajuan->keterangan_tambahan }}"</i>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="form-main-card">
                    {{-- Header Stripe --}}
                    <div class="form-header-stripe">
                        <div class="fh-icon">
                            <i
                                class="bi {{ $isRevisi ? 'bi-arrow-repeat' : ($isEdit ? 'bi-pencil-square' : 'bi-award-fill') }}"></i>
                        </div>
                        <div>
                            <h5>{{ $formTitle }}</h5>
                            <p>{{ $formSub }}</p>
                        </div>
                    </div>

                    <div class="p-4">
                        @if ($isCreate && isset($sudahPuncak) && $sudahPuncak)
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-patch-check-fill text-danger" style="font-size:3rem;"></i>
                                <p class="mt-3">Tidak ada pengajuan yang bisa dilakukan.</p>
                                <a href="{{ route('dosen.pangkat-golongan.index') }}"
                                    class="btn btn-outline-secondary btn-sm">Kembali</a>
                            </div>
                        @else
                            {{-- Form Action --}}
                            @if ($isRevisi || $isEdit)
                                <form method="POST"
                                    action="{{ route('dosen.pangkat-golongan.update', $pengajuan->id_pengajuan) }}"
                                    enctype="multipart/form-data" id="formPanggol">
                                    @method('PUT')
                                    <input type="hidden" name="mode" value="{{ $mode }}">
                                @else
                                    <form method="POST" action="{{ route('dosen.pangkat-golongan.store') }}"
                                        enctype="multipart/form-data" id="formPanggol">
                            @endif
                            @csrf

                            {{-- ── DATA PEGAWAI ── --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <p class="section-label">Data Pegawai</p>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $pegawai->nama_lengkap }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">{{ $identitas_label ?? 'NIP/NIDN' }}</label>
                                        <input type="text" class="form-control" value="{{ $identitas_value ?? '-' }}"
                                            readonly>
                                    </div>
                                </div>

                                <div class="info-box {{ $pangkatSekarang !== 'Belum ada pangkat' ? 'green' : 'amber' }}">
                                    <i class="bi bi-award flex-shrink-0 mt-1"></i>
                                    <div>
                                        Pangkat saat ini:
                                        <strong>{{ $pangkatSekarang }}</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- ── DATA PENGAJUAN ── --}}
                            @if (!$isRevisi)
                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="section-label">Pangkat Golongan yang Diajukan</p>

                                    {{-- Dropdown Target --}}
                                    <div class="col-12 mb-3">
                                        <label class="form-label-custom">
                                            <i class="bi bi-award-fill me-1 text-danger"></i> Pilih Pangkat dan Golongan
                                        </label>
                                        <select name="target_panggol" id="selectPangkat" class="form-select" required>
                                            <option value="">-- Pilih Pangkat dan Golongan --</option>
                                            @foreach ($semuaPangkat as $p)
                                                {{-- TAMBAHKAN PENGECEKAN INI --}}
                                                @php
                                                    $oldSelected = old(
                                                        'target_panggol',
                                                        $pengajuan ? $pengajuan->target_panggol : null,
                                                    );
                                                    $isSelected = $oldSelected == $p->id_panggol;
                                                @endphp

                                                @if ($p->lebihRendah)
                                                    <option value="{{ $p->id_panggol }}" disabled style="color:#9ca3af;">
                                                        — {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} (sudah
                                                        dimiliki/lebih rendah)
                                                    </option>
                                                @elseif($p->bisa || $isSelected)
                                                    <option value="{{ $p->id_panggol }}"
                                                        {{ $isSelected ? 'selected' : '' }}>
                                                        {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} ✓ Dapat diajukan
                                                    </option>
                                                @else
                                                    <option value="{{ $p->id_panggol }}" disabled style="color:#9ca3af;">
                                                        {{ $p->id_panggol }} – {{ $p->jenis_pangkat }} (belum bisa
                                                        diajukan)
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="rule-info mt-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Kenaikan pangkat hanya dapat diajukan secara berurutan sesuai dengan batas
                                            maksimum Jabatan Fungsional Anda saat ini.
                                        </div>
                                    </div>

                                    {{-- Nomor SK / Usulan --}}
                                    <div class="col-12">
                                        <label class="form-label-custom">
                                            <i class="bi bi-hash me-1 text-danger"></i>Nomor SK / Usulan
                                        </label>
                                        <input type="text" name="nomor_usulan" class="form-control"
                                            value="{{ $isEdit || $isRevisi ? old('nomor_usulan', $pengajuan->keterangan_tambahan) : old('nomor_usulan') }}"
                                            placeholder="Contoh: 821.22/001/2026" required>
                                        @error('nomor_usulan')
                                            <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                {{-- Mode Revisi: Field Pengajuan Dikunci --}}
                                <div class="mb-3 pb-3 border-bottom">
                                    <p class="section-label">Pangkat Golongan yang Diajukan</p>
                                    <div class="info-box blue">
                                        <i class="bi bi-award flex-shrink-0 mt-1"></i>
                                        <div>
                                            Pangkat yang direvisi: <strong>{{ $namaPangkatTarget ?: '—' }}</strong>.
                                            Tidak dapat diubah saat revisi.
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label-custom">Nomor SK / Usulan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $pengajuan->keterangan_tambahan }}" readonly>
                                    </div>
                                </div>
                            @endif

                            {{-- ── UPLOAD BERKAS ── --}}
                            <div class="mb-4">
                                <p class="section-label">
                                    {{ $isRevisi ? 'Upload Berkas yang Perlu Direvisi' : 'Upload Berkas Pendukung' }}
                                </p>

                                @if ($isRevisi)
                                    <div class="info-box amber mb-3">
                                        <i class="bi bi-exclamation-triangle flex-shrink-0 mt-1"></i>
                                        <div>
                                            Hanya berkas yang <strong>ditandai bermasalah</strong> yang perlu diunggah
                                            ulang. Berkas lainnya tidak perlu diganti.
                                        </div>
                                    </div>
                                @elseif ($isEdit)
                                    <div class="fw-semibold mb-3" style="font-size:0.82rem;color:#6b7280;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Kosongkan jika tidak ingin mengganti file yang sudah ada
                                    </div>
                                @endif

                                <div class="berkas-section">
                                    @php
                                        $berkasConfig = [
                                            [
                                                'key' => 'sk_cpns',
                                                'label' => 'SK CPNS',
                                                'desc' =>
                                                    'Surat Keputusan pengangkatan sebagai Calon Pegawai Negeri Sipil',
                                                'wajib' => true,
                                                'max' => 5,
                                            ],
                                            [
                                                'key' => 'sk_pns',
                                                'label' => 'SK PNS',
                                                'desc' =>
                                                    'Surat Keputusan pengangkatan sebagai Pegawai Negeri Sipil penuh',
                                                'wajib' => true,
                                                'max' => 5,
                                            ],
                                            [
                                                'key' => 'pak',
                                                'label' => 'PAK',
                                                'desc' => 'Dokumen penetapan angka kredit dari pejabat berwenang',
                                                'wajib' => true,
                                                'max' => 5,
                                            ],
                                            [
                                                'key' => 'publikasi',
                                                'label' => 'Publikasi',
                                                'desc' =>
                                                    'Bukti publikasi jurnal, prosiding, atau karya ilmiah pendukung',
                                                'wajib' => false,
                                                'max' => 10,
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($berkasConfig as $bc)
                                        @php
                                            $fileAda = $bAda->get($bc['key']);
                                            $bermasalah = $isRevisi && in_array($bc['key'], $berkasBermasalahArr);
                                            $readonly = $isRevisi && !$bermasalah;
                                        @endphp
                                        <div
                                            class="berkas-item {{ $bermasalah ? 'bermasalah' : ($readonly ? 'readonly-item' : '') }}">
                                            <label for="file-{{ $bc['key'] }}">
                                                <i
                                                    class="bi bi-file-earmark-pdf {{ $bc['wajib'] ? 'text-danger' : 'text-primary' }} me-1"></i>
                                                {{ $bc['label'] }}
                                                @if ($isRevisi)
                                                    @if ($bermasalah)
                                                        <span class="berkas-badge bermasalah-badge"><i
                                                                class="bi bi-exclamation-circle me-1"></i>Perlu
                                                            Diganti</span>
                                                    @else
                                                        <span class="berkas-badge ok-badge"><i
                                                                class="bi bi-check-circle me-1"></i>Tidak Perlu
                                                            Diganti</span>
                                                    @endif
                                                @else
                                                    <span
                                                        class="berkas-badge {{ $bc['wajib'] ? '' : 'optional' }}">{{ $bc['wajib'] ? 'Wajib' : 'Opsional' }}</span>
                                                @endif
                                            </label>
                                            <div class="berkas-desc">{{ $bc['desc'] }}</div>

                                            @if ($fileAda)
                                                <a href="{{ Storage::url($fileAda['file_path']) }}" target="_blank"
                                                    class="file-existing {{ $bermasalah ? 'file-existing-bermasalah' : '' }}">
                                                    <i
                                                        class="bi bi-{{ $bermasalah ? 'exclamation-circle-fill text-danger' : 'check-circle-fill' }}"></i>
                                                    <span>{{ $fileAda['nama_berkas'] }}</span>
                                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                </a>
                                            @endif

                                            @if (!$readonly)
                                                <input type="file" name="{{ $bc['key'] }}"
                                                    class="form-control mt-2" accept="application/pdf"
                                                    id="file-{{ $bc['key'] }}">
                                                <small class="text-muted">PDF, maks. {{ $bc['max'] }}MB
                                                    {{ $fileAda ? '— kosongkan jika tidak diganti' : '' }}</small>
                                                <div id="err-{{ $bc['key'] }}" class="text-danger mt-1"
                                                    style="font-size:0.78rem; display:none;"></div>
                                            @else
                                                <div class="readonly-note"><i class="bi bi-lock me-1"></i> Berkas ini
                                                    tidak perlu diganti</div>
                                            @endif

                                            @error($bc['key'])
                                                <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- ── TOMBOL AKSI ── --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 border-top">
                                <a href="{{ route('dosen.pangkat-golongan.index') }}" class="btn-batal btn">
                                    <i class="bi bi-arrow-left me-1"></i>Batal
                                </a>
                                <button type="button" class="btn-ajukan-form btn" id="btnAjukan"
                                    onclick="if(typeof submitForm === 'function') { submitForm('formPanggol', {{ $isEdit ? 'true' : 'false' }}, {{ $isRevisi ? 'true' : 'false' }}, {{ $bAda->has('sk_cpns') ? 'true' : 'false' }}, {{ $bAda->has('sk_pns') ? 'true' : 'false' }}, {{ $bAda->has('pak') ? 'true' : 'false' }}, {{ json_encode($berkasBermasalahArr) }}); } else { document.getElementById('formPanggol').submit(); }"
                                    @if ($isCreate && isset($adaPending) && $adaPending) disabled title="Ada pengajuan yang masih diproses" @endif>
                                    <i class="bi bi-send me-2"></i>
                                    {{ $isRevisi ? 'Kirim Revisi' : ($isEdit ? 'Simpan Perubahan' : 'Ajukan') }}
                                </button>
                            </div>

                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    {{-- Pastikan memanggil file JS yang memuat fungsi submitForm() dan validasi --}}
    <script src="{{ asset('assets/dosen/data_pangkat_golongan/FormPangkatdanGolongan.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Jika ada fungsi validasi inisialisasi di JS kamu
            if (typeof initFileValidation === 'function') {
                initFileValidation({{ $isEdit || $isRevisi ? 'true' : 'false' }},
                    {{ json_encode($berkasBermasalahArr) }});
            }
        });
    </script>
@endpush
