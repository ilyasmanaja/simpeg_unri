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
    <div class="col main col-12 col-lg p-4 d-flex align-items-center justify-content-center">
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
                $statusForm = strtolower(trim(isset($surat) ? ($surat->status ?? '') : ''));

                $isDitolakOperator = $statusForm === 'ditolak (verifikasi)';
                $isDitolakPimpinan = $statusForm === 'ditolak (persetujuan)';
                $isDitolak         = $isDitolakOperator || $isDitolakPimpinan || $statusForm === 'ditolak';

                $labelPenolak = match(true) {
                    $isDitolakOperator => 'Ditolak oleh Operator',
                    $isDitolakPimpinan => 'Ditolak oleh Pimpinan',
                    default            => 'Pengajuan Ditolak',
                };

                $readonlyField = $isDitolakOperator ? 'readonly' : '';
                $disabledField = $isDitolakOperator ? 'disabled' : '';

                // Ambil berkas aktif dari relasi (tabel BERKAS)
                $berkasAktif = isset($surat) ? $surat->berkasAktif : null;

                // Nomor identitas pengusul (NIDN/NIP)
                $nomorIdentitas = $pegawai?->nomor_identitas ?? '-';
            @endphp

            {{-- Alert ditolak --}}
            @if (isset($surat) && $isDitolak)
            <div class="alert-ditolak">
                <div class="alert-ditolak-icon">❌</div>
                <div>
                    <div class="alert-ditolak-title">{{ $labelPenolak }}</div>
                    <div class="alert-ditolak-alasan">
                        {{ $surat->alasan_penolakan ?? 'Tidak ada keterangan alasan.' }}
                    </div>
                    @if ($isDitolakOperator)
                        <div class="alert-ditolak-note">
                            Pengajuan ditolak karena berkas. Silakan upload ulang berkas yang benar, lalu klik "Ajukan Kembali".
                        </div>
                    @else
                        <div class="alert-ditolak-note">
                            Silakan perbaiki data di bawah, lalu klik "Ajukan Kembali".
                        </div>
                    @endif
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

            <form id="formSurat"
                  method="POST"
                  action="{{ isset($surat)
                      ? ($isDitolak
                          ? route('surat.prosesAjukanKembali', $surat->id_surat_tugas)
                          : url('/update/' . $surat->id_surat_tugas))
                      : route('surat.store') }}"
                  enctype="multipart/form-data">
                @csrf

                {{-- Pengusul (dari data pegawai yang login) --}}
                <div class="row mb-3 align-items-center">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-person me-2"></i>Pengusul
                    </label>
                    <div class="col-md-8">
                        <input type="text" class="form-control custom-input"
                               value="{{ $pegawai->nama_lengkap ?? '-' }}" readonly>
                        @if($pegawai)
                        <small class="text-muted" style="font-size:11px">
                            {{ $nomorIdentitas }}
                        </small>
                        @endif
                    </div>
                </div>

                {{-- Daftar Anggota (input manual) --}}
                <div class="row mb-3 align-items-start">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-people me-2"></i>Daftar Anggota
                    </label>
                    <div class="col-md-8">
                        <div id="anggota-container"></div>

                        @if (!$isDitolakOperator)
                        <button type="button" class="btn btn-ajukan btn-sm mt-2"
                                onclick="tambahAnggota()">
                            + Tambah Anggota
                        </button>
                        @endif

                        <div id="error-duplikat" class="d-none mt-2"
                             style="font-size:12px;color:#d32f2f;font-weight:600">
                            ⚠️ Terdapat duplikasi nama anggota!
                        </div>

                        @if ($isDitolakOperator)
                        <small class="text-muted" style="font-size:11px">
                            <i class="bi bi-lock-fill me-1"></i>Daftar anggota tidak dapat diubah
                        </small>
                        @endif
                    </div>
                </div>

                {{-- Waktu Pelaksanaan --}}
                <div class="row mb-3 align-items-center">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-calendar me-2"></i>Waktu Pelaksanaan
                    </label>
                    <div class="col-md-8" id="box-waktu">
                        <input type="date" id="waktu" name="waktu_pelaksanaan"
                               class="form-control custom-input {{ $isDitolakOperator ? 'bg-light' : '' }}"
                               min="{{ date('Y-m-d') }}"
                               value="{{ isset($surat) ? \Carbon\Carbon::parse($surat->waktu_pelaksanaan)->format('Y-m-d') : '' }}"
                               {{ $readonlyField }}>
                        @if ($isDitolakOperator)
                            <small class="text-muted" style="font-size:11px">
                                <i class="bi bi-lock-fill me-1"></i>Tidak dapat diubah
                            </small>
                        @else
                            <small id="rule-waktu">Wajib diisi dan tidak boleh kurang dari hari ini</small>
                        @endif
                    </div>
                </div>

                {{-- Lama Pelaksanaan --}}
                <div class="row mb-3 align-items-center">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-journal-text me-2"></i>Lama Pelaksanaan
                    </label>
                    <div class="col-md-8" id="box-lama">
                        <input type="number" id="lama" name="lama_pelaksanaan"
                               class="form-control custom-input {{ $isDitolakOperator ? 'bg-light' : '' }}"
                               min="1" max="30"
                               value="{{ $surat->lama_pelaksanaan ?? '' }}"
                               {{ $readonlyField }}>
                        @if ($isDitolakOperator)
                            <small class="text-muted" style="font-size:11px">
                                <i class="bi bi-lock-fill me-1"></i>Tidak dapat diubah
                            </small>
                        @else
                            <small id="rule-lama">Minimal 1 hari, maksimal 30 hari</small>
                        @endif
                    </div>
                </div>

                {{-- Perihal --}}
                <div class="row mb-3 align-items-start">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-file-earmark me-2"></i>Perihal
                    </label>
                    <div class="col-md-8">
                        <textarea id="perihal" name="perihal"
                                  class="form-control custom-input {{ $isDitolakOperator ? 'bg-light' : '' }}"
                                  rows="3"
                                  {{ $readonlyField }}>{{ $surat->perihal ?? '' }}</textarea>
                        <div id="box-perihal">
                            @if ($isDitolakOperator)
                                <small class="text-muted" style="font-size:11px">
                                    <i class="bi bi-lock-fill me-1"></i>Tidak dapat diubah
                                </small>
                            @else
                                <small id="rule-wajib">Wajib diisi</small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Berkas Pendukung (dari tabel BERKAS) --}}
                <div class="row mb-4 align-items-center">
                    <label class="col-md-4 form-label">
                        <i class="bi bi-paperclip me-2"></i>Berkas Pendukung
                        @if (!isset($surat))
                            <small class="d-block text-muted" style="font-size:11px;font-weight:400">Wajib diupload</small>
                        @elseif ($isDitolakOperator || ($surat->berkas_bermasalah ?? false))
                            <small class="d-block" style="font-size:11px;font-weight:600;color:#c62828;">
                                ⚠️ Wajib upload ulang
                            </small>
                        @endif
                    </label>
                    <div class="col-md-8" id="box-berkas">

                        {{-- Tampilkan berkas aktif dari tabel BERKAS --}}
                        @if ($berkasAktif)
                        <div class="mb-2" style="font-size:13px;color:#555;background:#f9f9f9;
                                                  padding:8px 12px;border-radius:8px;border:1px solid #eee">
                            📄 File saat ini:
                            <a href="{{ route('berkas.view', $berkasAktif->id_berkas) }}" target="_blank"
                               style="color:#1565c0;font-weight:600">
                                {{ $berkasAktif->nama_berkas ?? 'Lihat File Lama' }}
                            </a>
                            <small class="d-block text-muted" style="font-size:11px;margin-top:3px">
                                @if ($isDitolakOperator || ($surat->berkas_bermasalah ?? false))
                                    Berkas ini bermasalah. Silakan upload berkas baru yang benar.
                                @else
                                    Upload file baru hanya jika ingin mengganti
                                @endif
                            </small>
                        </div>
                        @endif

                        <input type="file" id="berkas" name="berkas"
                               class="form-control custom-input"
                               {{ (!isset($surat) || $isDitolakOperator || ($surat->berkas_bermasalah ?? false)) ? 'required' : '' }}>
                        <small id="rule-berkas">
                            @if (!isset($surat) || $isDitolakOperator || ($surat->berkas_bermasalah ?? false))
                                Wajib diupload, maksimal 10MB
                            @else
                                Ukuran file maksimal 10MB
                            @endif
                        </small>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="submit" class="btn btn-ajukan px-4">
                        @if (isset($surat) && $isDitolak)
                            Ajukan Kembali
                        @elseif (isset($surat))
                            Perbarui
                        @else
                            Ajukan
                        @endif
                    </button>
                    <button type="button" id="btnBatal" class="btn btn-batal ms-2">Batal</button>
                </div>

            </form>
        </div>
    </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const anggotaLama   = @json($anggota ?? []);
    const daftarPegawai = @json($pegawai ?? []);
    const isDitolakOperator = {{ $isDitolakOperator ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>