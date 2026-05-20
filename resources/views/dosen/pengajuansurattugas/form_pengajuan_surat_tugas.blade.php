@extends('layouts.app')

@section('title', 'Form Pengajuan Surat Tugas - SIMPEG UNRI')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengajuan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="main p-4 d-flex align-items-center justify-content-center">
        <div class="w-100 d-flex justify-content-center">
            <div class="form-card p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="form-icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="font-size:16px">Form Pengajuan Surat Tugas</h5>
                        <small class="text-muted d-block" style="font-size:12px">Isi data dengan lengkap dan benar</small>
                    </div>
                </div>

                <hr class="mb-4">

                @php
                    $statusForm = strtolower(trim(isset($surat) ? $surat->status ?? '' : ''));
                    $isDitolakOperator = $statusForm === 'ditolak (verifikasi)';
                    $isDitolakPimpinan = $statusForm === 'ditolak (persetujuan)';
                    $isDitolak = $isDitolakOperator || $isDitolakPimpinan || $statusForm === 'ditolak';

                    $labelPenolak = match (true) {
                        $isDitolakOperator => 'Ditolak oleh Operator',
                        $isDitolakPimpinan => 'Ditolak oleh Pimpinan',
                        default => 'Pengajuan Ditolak',
                    };

                    $readonlyField = $isDitolakOperator ? 'readonly' : '';
                    $berkasAktif = isset($surat) ? $surat->berkasAktif : null;
                    $nomorIdentitas = $pegawai?->nomor_identitas ?? '-';
                @endphp

                @if (isset($surat) && $isDitolak)
                    <div class="alert-ditolak">
                        <div class="alert-ditolak-icon">❌</div>
                        <div>
                            <div class="alert-ditolak-title">{{ $labelPenolak }}</div>
                            <div class="alert-ditolak-alasan">
                                {{ $surat->alasan_penolakan ?? 'Tidak ada keterangan alasan.' }}</div>
                            <div class="alert-ditolak-note">
                                {{ $isDitolakOperator ? 'Silakan upload ulang berkas yang benar, lalu klik "Ajukan Kembali".' : 'Silakan perbaiki data di bawah, lalu klik "Ajukan Kembali".' }}
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="formSurat" method="POST"
                    action="{{ isset($surat) ? ($isDitolak ? route('surat.prosesAjukanKembali', $surat->id_surat_tugas) : url('/update/' . $surat->id_surat_tugas)) : route('dosen.surat.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Pengusul --}}
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-4 form-label"><i class="bi bi-person me-2"></i>Pengusul</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control custom-input"
                                value="{{ $pegawai->nama_lengkap ?? '-' }}" readonly>
                            @if ($pegawai)
                                <small class="text-muted" style="font-size:11px">{{ $nomorIdentitas }}</small>
                            @endif
                        </div>
                    </div>

                    {{-- Daftar Anggota --}}
                    <div class="row mb-3 align-items-start">
                        <label class="col-md-4 form-label"><i class="bi bi-people me-2"></i>Daftar Anggota</label>
                        <div class="col-md-8">
                            <div id="anggota-container"></div>
                            {{-- Template untuk baris anggota baru --}}
                            <template id="template-anggota">
                                <div class="input-group mb-2 anggota-row">
                                    <input type="text" name="anggota[]" class="form-control" placeholder="Nama Anggota"
                                        required>
                                    <button type="button" class="btn btn-danger" onclick="hapusAnggota(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </template>
                            @if (!$isDitolakOperator)
                                <button type="button" class="btn btn-ajukan btn-sm mt-2" onclick="tambahAnggota()">+ Tambah
                                    Anggota</button>
                            @endif
                            <div id="error-duplikat" class="d-none mt-2"
                                style="font-size:12px;color:#d32f2f;font-weight:600">⚠️ Terdapat duplikasi nama anggota!
                            </div>
                        </div>
                    </div>

                    {{-- Waktu Pelaksanaan --}}
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-4 form-label"><i class="bi bi-calendar me-2"></i>Waktu Pelaksanaan</label>
                        <div class="col-md-8">
                            <input type="date" name="waktu_pelaksanaan" class="form-control custom-input"
                                value="{{ isset($surat) ? \Carbon\Carbon::parse($surat->waktu_pelaksanaan)->format('Y-m-d') : '' }}"
                                required>
                        </div>
                    </div>

                    {{-- Lama Pelaksanaan --}}
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-4 form-label"><i class="bi bi-journal-text me-2"></i>Lama Pelaksanaan
                            (Hari)</label>
                        <div class="col-md-8">
                            <input type="number" name="lama_pelaksanaan" class="form-control custom-input" min="1"
                                value="{{ $surat->lama_pelaksanaan ?? '' }}" required>
                        </div>
                    </div>

                    {{-- Perihal --}}
                    <div class="row mb-3 align-items-start">
                        <label class="col-md-4 form-label"><i class="bi bi-file-earmark me-2"></i>Perihal</label>
                        <div class="col-md-8">
                            <textarea name="perihal" class="form-control custom-input" rows="3" required>{{ $surat->perihal ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- Berkas Pendukung --}}
                    <div class="row mb-4 align-items-center">
                        <label class="col-md-4 form-label"><i class="bi bi-paperclip me-2"></i>Berkas Pendukung</label>
                        <div class="col-md-8">
                            @if (isset($surat) && $surat->berkasAktif)
                                <div class="mb-2 p-2 bg-light rounded" style="font-size: 12px;">
                                    📄 File saat ini: <a href="{{ route('dosen.berkas.view', $surat->berkasAktif->id_berkas) }}"
                                        target="_blank">
                                        {{ $surat->berkasAktif->nama_berkas }}
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="berkas" class="form-control custom-input"
                                {{ !isset($surat) ? 'required' : '' }}>
                            <small class="text-muted">Maksimal 10MB</small>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-ajukan px-4">
                            {{ isset($surat) && $isDitolak ? 'Ajukan Kembali' : (isset($surat) ? 'Perbarui' : 'Ajukan') }}
                        </button>
                        <a href="{{ route('dosen.surat.index') }}" class="btn btn-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/surattugas.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const anggotaLama = @json($anggota ?? []);
        const daftarPegawai = @json($pegawai ?? []);
        const isDitolakOperator = {{ $isDitolakOperator ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
@endpush
