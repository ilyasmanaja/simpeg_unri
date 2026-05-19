{{-- resources/views/jabatanfungsional/formjabatanfungsional.blade.php --}}
{{-- Mode: create (default) | edit ($pengajuan ada, $berkasBermasalah tidak ada) | revisi ($berkasBermasalah ada) --}}
@extends('layouts.app')

@php
    $isRevisi = isset($berkasBermasalah);
    $isEdit   = isset($pengajuan) && !$isRevisi;
    $isCreate = !isset($pengajuan);

    $formTitle = $isRevisi ? 'Revisi Pengajuan Jabatan Fungsional'
               : ($isEdit  ? 'Edit Pengajuan Jabatan Fungsional'
                           : 'Form Pengajuan Jabatan Fungsional');

    $formSub = $isRevisi
        ? 'Unggah ulang berkas yang ditandai oleh operator'
        : ($isEdit
            ? 'Ubah data pengajuan sebelum diproses operator'
            : ($jenis === 'dosen'
                ? 'Pengajuan kenaikan jabatan fungsional dosen'
                : 'Pengajuan jabatan fungsional tenaga kependidikan'));

    $bAda               = $berkasAda ?? collect();
    $berkasBermasalahArr = $berkasBermasalah ?? [];

    // Nama jabfung target untuk ditampilkan (edit/revisi ambil dari keterangan_tambahan)
    $namaJabfungTarget = ($isEdit || $isRevisi)
        ? ($pengajuan->keterangan_tambahan ?? '')
        : ($namaTarget ?? '');
@endphp

@section('title', $formTitle)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jabatanfungsional.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="main">
    <div class="main-inner" style="padding-top: 32px;">
        <div class="container mb-5" style="max-width: 720px;">

            {{-- Breadcrumb --}}
            <nav class="mb-3">
                <a href="{{ route('dosen.jabatanfungsional.index') }}"
                   class="text-muted text-decoration-none" style="font-size:0.85rem;">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </nav>

            {{-- Alert: pengajuan pending (create) --}}
            @if ($isCreate && isset($adaPending) && $adaPending)
            <div class="alert alert-warning alert-pending d-flex gap-2 align-items-start mb-3">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                <div><strong>Perhatian:</strong> Kamu masih memiliki pengajuan yang sedang diproses.</div>
            </div>
            @endif

            {{-- Alert: sudah puncak --}}
            @if ($isCreate && isset($sudahPuncak) && $sudahPuncak)
            <div class="alert d-flex gap-2 align-items-start mb-3"
                 style="border-left:4px solid #b91c1c;border-radius:10px;background:#fef2f2;">
                <i class="bi bi-patch-check-fill flex-shrink-0 mt-1 text-danger"></i>
                <div>
                    <strong>Jabatan Fungsional Tertinggi Tercapai.</strong>
                    Kamu sudah berada di jabatan <strong>Guru Besar</strong>.
                </div>
            </div>
            @endif

            {{-- Alert revisi --}}
            @if ($isRevisi)
            <div class="alert-revisi mb-3">
                <div class="ar-header">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Pengajuan Ditolak — Perlu Revisi</strong>
                </div>
                @php
                    $labelBerkas = ['sk_cpns'=>'SK CPNS','sk_pns'=>'SK PNS','pak'=>'PAK','publikasi'=>'Publikasi'];
                @endphp
                <div class="ar-berkas-list mt-2">
                    <span class="ar-label">Berkas yang harus diganti:</span>
                    @foreach ($berkasBermasalahArr as $kb)
                        <span class="ar-chip"><i class="bi bi-file-earmark-pdf-fill me-1"></i>{{ $labelBerkas[$kb] ?? $kb }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="form-main-card">

                {{-- Header stripe --}}
                <div class="form-header-stripe">
                    <div class="fh-icon">
                        <i class="bi {{ $isRevisi ? 'bi-arrow-repeat' : ($isEdit ? 'bi-pencil-square' : 'bi-briefcase-fill') }}"></i>
                    </div>
                    <div>
                        <h5>{{ $formTitle }}</h5>
                        <p>{{ $formSub }}</p>
                    </div>
                </div>

                <div class="p-4">

                    @if ($isCreate && isset($sudahPuncak) && $sudahPuncak)
                    {{-- Sudah puncak --}}
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-patch-check-fill text-danger" style="font-size:3rem;"></i>
                        <p class="mt-3">Tidak ada pengajuan yang bisa dilakukan.</p>
                        <a href="{{ route('dosen.jabatanfungsional.index') }}"
                           class="btn btn-outline-secondary btn-sm">Kembali</a>
                    </div>

                    @else

                    {{-- ══ FORM ══ --}}
                    @if ($isRevisi)
                        <form method="POST"
                              action="{{ route('dosen.jabatanfungsional.simpanRevisi', $pengajuan->id_pengajuan) }}"
                              enctype="multipart/form-data" id="formJabfung">
                            @method('PUT')
                    @elseif ($isEdit)
                        <form method="POST"
                              action="{{ route('dosen.jabatanfungsional.update', $pengajuan->id_pengajuan) }}"
                              enctype="multipart/form-data" id="formJabfung">
                            @method('PUT')
                    @else
                        <form method="POST"
                              action="{{ route('dosen.jabatanfungsional.store') }}"
                              enctype="multipart/form-data" id="formJabfung">
                    @endif
                    @csrf

                    {{-- Hidden: nama jabfung target yang dikirim ke controller --}}
                    <input type="hidden" name="nama_jabfung_target" id="input-nama-jabfung-target"
                           value="{{ $namaJabfungTarget }}">

                    {{-- ── DATA PEGAWAI ── --}}
                    <div class="mb-3 pb-3 border-bottom">
                        <p class="section-label">Data Pegawai</p>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Nama Lengkap</label>
                                <input type="text" class="form-control"
                                       value="{{ $pegawai->nama_lengkap }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">
                                    {{ $jenis === 'dosen' ? 'NIDN' : 'NIP' }}
                                </label>
                                <input type="text" class="form-control"
                                       value="{{ $jenis === 'dosen' ? ($pegawai->nidn ?? '-') : ($pegawai->nip ?? '-') }}"
                                       readonly>
                            </div>
                        </div>

                        <div class="info-box {{ $namaJabfungNow ? 'green' : 'amber' }}">
                            <i class="bi bi-briefcase flex-shrink-0 mt-1"></i>
                            <div>
                                Jabatan fungsional saat ini:
                                <strong>{{ $namaJabfungNow ?? 'Belum memiliki jabatan fungsional' }}</strong>
                                @if ($jenis === 'dosen' && $namaTarget)
                                    &nbsp;→&nbsp; Dapat mengajukan:
                                    <strong>{{ $namaTarget }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── JABFUNG YANG DIAJUKAN ── --}}
                    @if (!$isRevisi)
                    <div class="mb-3 pb-3 border-bottom">
                        <p class="section-label">Jabatan Fungsional yang Diajukan</p>

                        @if ($jenis === 'dosen')
                        {{-- Dosen: dropdown readonly, hanya bisa naik 1 jenjang --}}
                        <div class="mb-3">
                            <label class="form-label-custom">
                                <i class="bi bi-briefcase me-1 text-danger"></i>Pilih Jabatan Fungsional
                            </label>
                            <select class="form-select" disabled>
                                @foreach ($semuaJabfung as $urutan => $nama)
                                    @php
                                        $isTarget = ($urutan === $urutanTarget);
                                        $isPast   = ($urutan < $urutanTarget);
                                        $isFuture = ($urutan > $urutanTarget);
                                    @endphp
                                    <option value="{{ $nama }}"
                                            {{ $isTarget ? 'selected' : '' }}
                                            {{ !$isTarget ? 'disabled' : '' }}>
                                        {{ $nama }}
                                        @if ($isPast) (Sudah Dilewati)
                                        @elseif ($isFuture) (Belum Bisa Diajukan)
                                        @else (Dapat Diajukan) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="rule-info mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Dosen hanya dapat naik <strong>satu jenjang</strong> setiap pengajuan secara berurutan:
                                Asisten Ahli → Lektor → Lektor Kepala → Guru Besar.
                            </div>
                        </div>

                        {{-- Visual ladder --}}
                        <div class="jabfung-ladder mb-3">
                            @foreach ($semuaJabfung as $urutan => $nama)
                                @php
                                    if ($urutan < $urutanTarget)       $kelas = 'done';
                                    elseif ($urutan === $urutanTarget) $kelas = 'next';
                                    elseif ($urutan === $urutanNow)    $kelas = 'current';
                                    else                               $kelas = '';
                                @endphp
                                <div class="jl-step {{ $kelas }}">
                                    <span class="jl-label">{{ $nama }}</span>
                                    @if ($kelas === 'done')    <span class="jl-badge"><i class="bi bi-check2"></i> Lalu</span>
                                    @elseif ($kelas === 'current') <span class="jl-badge">Sekarang</span>
                                    @elseif ($kelas === 'next')    <span class="jl-badge">{{ $isEdit ? 'Diajukan' : 'Tujuan' }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @else
                        {{-- Tendik: dropdown pilih jabfung, jabfung sekarang di-disable --}}
                        <div class="mb-3">
                            <label class="form-label-custom">
                                <i class="bi bi-briefcase me-1 text-danger"></i>Pilih Jabatan Fungsional
                            </label>
                            <select class="form-select" id="select-jabfung-tendik"
                                    onchange="onSelectTendik(this)">
                                <option value="">-- Pilih Jabatan Fungsional --</option>
                                @foreach ($listTendik as $nama)
                                    @php
                                        $isCurrent  = ($nama === $namaJabfungNow);
                                        $isSelected = ($isEdit && $namaJabfungTarget === $nama);
                                    @endphp
                                    <option value="{{ $nama }}"
                                            {{ $isCurrent ? 'disabled' : '' }}
                                            {{ $isSelected ? 'selected' : '' }}
                                            data-current="{{ $isCurrent ? '1' : '0' }}">
                                        {{ $nama }}
                                        {{ $isCurrent ? '(Jabatan Saat Ini — Tidak Bisa Dipilih)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="rule-info mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Tenaga Kependidikan dapat memilih jabatan fungsional apapun,
                                <strong>kecuali jabatan yang sedang dimiliki saat ini</strong>.
                            </div>
                        </div>
                        @endif

                        {{-- Nomor SK --}}
                        <div class="mt-3">
                            <label class="form-label-custom">
                                <i class="bi bi-hash me-1 text-danger"></i>Nomor SK / Usulan
                            </label>
                            <input type="text" name="nomor_usulan" class="form-control"
                                   value="{{ ($isEdit || $isRevisi) ? old('nomor_usulan', $pengajuan->nomor_usulan) : old('nomor_usulan') }}"
                                   placeholder="Contoh: 821.22/001/2026"
                                   {{ $isRevisi ? 'readonly' : '' }}>
                            @error('nomor_usulan')
                                <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @else
                    {{-- Mode revisi: jabfung readonly --}}
                    <div class="mb-3 pb-3 border-bottom">
                        <p class="section-label">Jabatan Fungsional yang Diajukan</p>
                        <div class="info-box blue">
                            <i class="bi bi-briefcase flex-shrink-0 mt-1"></i>
                            <div>
                                Jabfung yang direvisi: <strong>{{ $namaJabfungTarget ?: '—' }}</strong>.
                                Tidak dapat diubah saat revisi.
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label-custom">Nomor SK / Usulan</label>
                            <input type="text" class="form-control"
                                   value="{{ $pengajuan->nomor_usulan }}" readonly>
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
                                Hanya berkas yang <strong>ditandai bermasalah</strong> yang perlu diunggah ulang.
                                Berkas lainnya tidak perlu diganti.
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
                                ['key'=>'sk_cpns',   'label'=>'SK CPNS',   'desc'=>'Surat Keputusan pengangkatan sebagai Calon Pegawai Negeri Sipil', 'wajib'=>true,  'max'=>5],
                                ['key'=>'sk_pns',    'label'=>'SK PNS',    'desc'=>'Surat Keputusan pengangkatan sebagai Pegawai Negeri Sipil penuh',  'wajib'=>true,  'max'=>5],
                                ['key'=>'pak',       'label'=>'PAK',       'desc'=>'Dokumen penetapan angka kredit dari pejabat berwenang',            'wajib'=>true,  'max'=>5],
                                ['key'=>'publikasi', 'label'=>'Publikasi', 'desc'=>'Bukti publikasi jurnal, prosiding, atau karya ilmiah pendukung',   'wajib'=>false, 'max'=>10],
                            ];
                            @endphp

                            @foreach ($berkasConfig as $bc)
                            @php
                                $fileAda    = $bAda->get($bc['key']);
                                $bermasalah = $isRevisi && in_array($bc['key'], $berkasBermasalahArr);
                                $readonly   = $isRevisi && !$bermasalah;
                            @endphp
                            <div class="berkas-item {{ $bermasalah ? 'bermasalah' : ($readonly ? 'readonly-item' : '') }}">
                                <label for="file-{{ $bc['key'] }}">
                                    <i class="bi bi-file-earmark-pdf {{ $bc['wajib'] ? 'text-danger' : 'text-primary' }} me-1"></i>
                                    {{ $bc['label'] }}
                                    @if ($isRevisi)
                                        @if ($bermasalah)
                                            <span class="berkas-badge bermasalah-badge">
                                                <i class="bi bi-exclamation-circle me-1"></i>Perlu Diganti
                                            </span>
                                        @else
                                            <span class="berkas-badge ok-badge">
                                                <i class="bi bi-check-circle me-1"></i>Tidak Perlu Diganti
                                            </span>
                                        @endif
                                    @else
                                        <span class="berkas-badge {{ $bc['wajib'] ? '' : 'optional' }}">
                                            {{ $bc['wajib'] ? 'Wajib' : 'Opsional' }}
                                        </span>
                                    @endif
                                </label>
                                <div class="berkas-desc">{{ $bc['desc'] }}</div>

                                @if ($fileAda)
                                    <a href="{{ Storage::url($fileAda->file_path) }}"
                                       target="_blank"
                                       class="file-existing {{ $bermasalah ? 'file-existing-bermasalah' : '' }}">
                                        <i class="bi bi-{{ $bermasalah ? 'exclamation-circle-fill text-danger' : 'check-circle-fill' }}"></i>
                                        <span>{{ $fileAda->nama_berkas }}</span>
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                @endif

                                @if (!$readonly)
                                <input type="file"
                                       name="{{ $bc['key'] }}"
                                       class="form-control mt-2"
                                       accept="application/pdf"
                                       id="file-{{ $bc['key'] }}">
                                <small class="text-muted">
                                    PDF, maks. {{ $bc['max'] }}MB
                                    {{ $fileAda ? '— kosongkan jika tidak diganti' : '' }}
                                </small>
                                <div id="err-{{ $bc['key'] }}" class="text-danger mt-1"
                                     style="font-size:0.78rem; display:none;"></div>
                                @else
                                <div class="readonly-note">
                                    <i class="bi bi-lock me-1"></i>
                                    Berkas ini tidak perlu diganti
                                </div>
                                @endif

                                @error($bc['key'])
                                    <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── TOMBOL AKSI ── --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <a href="{{ route('dosen.jabatanfungsional.index') }}" class="btn-batal btn">
                            <i class="bi bi-arrow-left me-1"></i>Batal
                        </a>
                        <button type="button"
                                class="btn-ajukan-form btn"
                                id="btnAjukan"
                                onclick="submitForm(
                                    'formJabfung',
                                    {{ $isEdit ? 'true' : 'false' }},
                                    {{ $isRevisi ? 'true' : 'false' }},
                                    {{ $bAda->has('sk_cpns')  ? 'true' : 'false' }},
                                    {{ $bAda->has('sk_pns')   ? 'true' : 'false' }},
                                    {{ $bAda->has('pak')      ? 'true' : 'false' }},
                                    {{ json_encode($berkasBermasalahArr) }}
                                )"
                                @if($isCreate && isset($adaPending) && $adaPending) disabled title="Ada pengajuan yang masih diproses" @endif>
                            <i class="bi bi-send me-2"></i>
                            {{ $isRevisi ? 'Kirim Revisi' : ($isEdit ? 'Simpan Perubahan' : 'Ajukan') }}
                        </button>
                    </div>

                    </form>
                    @endif

                </div>{{-- /p-4 --}}
            </div>{{-- /form-main-card --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/jabatanfungsional.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        window.JABFUNG_JENIS = '{{ $jenis }}';
        document.addEventListener('DOMContentLoaded', function () {
            initFileValidation(
                {{ ($isEdit || $isRevisi) ? 'true' : 'false' }},
                {{ json_encode($berkasBermasalahArr) }}
            );

            // Tendik: update hidden input saat select berubah
            @if ($jenis === 'tendik' && !$isRevisi)
            const selectTendik = document.getElementById('select-jabfung-tendik');
            if (selectTendik) {
                selectTendik.addEventListener('change', function () {
                    document.getElementById('input-nama-jabfung-target').value = this.value;
                });
            }
            @endif
        });
    </script>
@endpush