@extends('layouts.app')
@section('title', 'Tambah Akun Pegawai')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/manajemen_akun/style.css') }}">
@endpush
@section('content')
    <div class="container-fluid p-4">

        {{-- SATU form saja di sini, bukan dua --}}
        <form action="{{ route('operator.manajemen_akun.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="container-box">
                <div class="header-box mb-4">
                    <div class="header-left">
                        <div class="icon-box"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h1 class="page-title">Tambah Akun Pegawai</h1>
                            <p class="page-subtitle">Halaman tambah akun pegawai di sistem</p>
                        </div>
                    </div>
                </div>

                {{-- ===== DATA IDENTITAS ===== --}}
                <div class="section-divider"><i class="bi bi-person-vcard-fill"></i> Data Identitas</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Foto Pegawai</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" id="f_nik" class="form-control" maxlength="16"
                            value="{{ old('nik') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>
                </div>

                {{-- ===== DATA KONTAK ===== --}}
                <div class="section-divider mt-5"><i class="bi bi-telephone-fill"></i> Data Kontak</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No HP Darurat</label>
                        <input type="text" name="nomor_hp_darurat" class="form-control"
                            value="{{ old('nomor_hp_darurat') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>

                {{-- ===== DATA AKADEMIK ===== --}}
                <div class="section-divider mt-5"><i class="bi bi-mortarboard-fill"></i> Data Akademik</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program Studi</label>
                        <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}">
                    </div>
                </div>

                {{-- ===== DATA KEPEGAWAIAN ===== --}}
                <div class="section-divider mt-5"><i class="bi bi-briefcase-fill"></i> Data Kepegawaian</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Status Pegawai <span class="req">*</span></label>
                        <select name="status_pegawai" id="f_status" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="PNS" {{ old('status_pegawai') == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="Non PNS" {{ old('status_pegawai') == 'Non PNS' ? 'selected' : '' }}>Non PNS
                            </option>
                        </select>
                    </div>

                    <div class="col-12" id="wrap_tipe_pns" style="display:none;">
                        <label class="form-label">
                            Tipe <span class="req">*</span>
                        </label>

                        <div class="tipe-pns-row">

                            <div class="tipe-opt">
                                <input type="radio" id="tipe_dosen" name="tipe_pns_radio" value="dosen">

                                <label for="tipe_dosen">
                                    <i class="bi bi-mortarboard-fill"></i>
                                    Dosen
                                </label>
                            </div>

                            <div class="tipe-opt">
                                <input type="radio" id="tipe_tendik" name="tipe_pns_radio" value="tendik">

                                <label for="tipe_tendik">
                                    <i class="bi bi-person-gear"></i>
                                    Tendik (Non-Dosen)
                                </label>
                            </div>

                        </div>

                        <div id="hint_nondosen"
                            style="display:none;margin-top:8px;padding:8px 12px;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">

                            <i class="bi bi-info-circle-fill me-1"></i>

                            Non PNS hanya dapat memiliki tipe
                            <strong>Tendik</strong>.
                            Tipe Dosen tidak tersedia untuk Non PNS.

                        </div>
                    </div>

                    {{-- NIP --}}
                    <div class="col-md-6" id="wrap_nip" style="display:none;">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip') }}">
                    </div>

                    {{-- NIDN --}}
                    <div class="col-md-6" id="wrap_nidn" style="display:none;">
                        <label class="form-label">NIDN</label>
                        <input type="text" name="nidn" class="form-control" value="{{ old('nidn') }}">
                    </div>

                    {{-- JABATAN FUNGSIONAL --}}
                    <div class="col-md-6" id="wrap_jabfung" style="display:none;">
                        <label class="form-label">Jabatan Fungsional</label>
                        <select name="id_jabfung" id="jabfung" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach ($jabfungs as $jabfung)
                                @php $tipe = strtolower(trim($jabfung->jenis_jabfung)); @endphp
                                <option value="{{ $jabfung->id_jabfung }}" data-tipe="{{ $tipe }}"
                                    {{ old('id_jabfung') == $jabfung->id_jabfung ? 'selected' : '' }}>
                                    {{ $jabfung->jenis_jabfung }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PANGKAT --}}
                    <div class="col-md-6" id="wrap_pangkat" style="display:none;">
                        <label class="form-label">Pangkat / Golongan</label>
                        <select name="id_panggol" id="pangkat" class="form-select">
                            <option value="">Pilih Pangkat</option>
                            @foreach ($pangkats as $pangkat)
                                <option value="{{ $pangkat->id_panggol }}" data-gol="{{ $pangkat->jenis_pangkat }}"
                                    {{ old('id_panggol') == $pangkat->id_panggol ? 'selected' : '' }}>
                                    {{ $pangkat->jenis_pangkat }} 
                                </option>
                            @endforeach
                        </select>
                        {{-- Peringatan jika pangkat tidak memenuhi syarat Pimpinan --}}
                        <div id="warn_pangkat_pimpinan" class="mt-2 p-2"
                            style="display:none;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Role <strong>Pimpinan</strong> membutuhkan minimal pangkat <strong>IV/a</strong>.
                        </div>
                    </div>

                    {{-- Peringatan jabfung Pimpinan --}}
                    <div class="col-12" id="warn_jabfung_pimpinan" style="display:none;">
                        <div class="p-2"
                            style="background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Role <strong>Pimpinan</strong> membutuhkan minimal jabatan <strong>Lektor Kepala</strong>.
                        </div>
                    </div>
                </div>

                {{-- ===== HAK AKSES ===== --}}
                <div class="section-divider mt-5"><i class="bi bi-shield-check"></i> Hak Akses</div>
                <p style="font-size:.82rem;color:#6c757d;margin-bottom:12px;">Pilih role / hak akses yang dimiliki pegawai.
                </p>

                <div class="hint-hak-akses mb-3">
                    <strong><i class="bi bi-info-circle me-1"></i>Panduan kombinasi:</strong>
                    <ul class="mb-0">
                        <li><strong>Pimpinan</strong>: bisa sendiri atau + Dosen. Min. pangkat IV/a, min. Lektor Kepala</li>
                        <li><strong>Dosen</strong>: bisa sendiri atau + Pimpinan. Hanya untuk PNS</li>
                        <li><strong>Tendik</strong>: bisa sendiri atau + Operator</li>
                        <li><strong>Operator</strong>: harus dikombinasikan dengan Tendik</li>
                    </ul>
                </div>

                <div class="hak-akses-grid">
                    @foreach ($roles as $role)
                        @php
                            $warna = '#2563eb';
                            $icon = 'bi-person-fill';
                            $desc = 'Hak akses sistem';
                            if (strtolower($role->jenis_role) == 'pimpinan') {
                                $warna = '#7c3aed';
                                $icon = 'bi-building';
                                $desc = 'Akses dashboard pimpinan';
                            }
                            if (strtolower($role->jenis_role) == 'dosen') {
                                $warna = '#2563eb';
                                $icon = 'bi-mortarboard-fill';
                                $desc = 'Akses portal dosen';
                            }
                            if (strtolower($role->jenis_role) == 'tendik') {
                                $warna = '#047857';
                                $icon = 'bi-person-gear';
                                $desc = 'Akses tenaga kependidikan';
                            }
                            if (strtolower($role->jenis_role) == 'operator') {
                                $warna = '#b45309';
                                $icon = 'bi-shield-lock-fill';
                                $desc = 'Akses manajemen sistem';
                            }
                        @endphp
                        <div>
                            <label class="hak-akses-card" style="--ha-color:{{ $warna }};">
                                <input type="checkbox" name="roles[]" value="{{ $role->id_role }}"
                                    id="role{{ $role->id_role }}" data-jenis="{{ strtolower($role->jenis_role) }}">
                                <div class="hak-akses-label">
                                    <div class="ha-icon"><i class="bi {{ $icon }}"></i></div>
                                    <div>
                                        <div class="ha-text">{{ $role->jenis_role }}</div>
                                        <div class="ha-desc">{{ $desc }}</div>
                                    </div>
                                    <div class="ha-check"><i class="bi bi-check2 ha-check-icon"></i></div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div id="infoPwdDefault" class="mt-4 p-3"
                    style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;">
                    <div style="font-size:.83rem;font-weight:600;color:#92400e;margin-bottom:6px;">
                        <i class="bi bi-key-fill me-1"></i>Password Default
                    </div>
                    <div style="font-size:.8rem;color:#78350f;">
                        Password otomatis menggunakan <strong>NIK pegawai</strong>.
                        Jika NIK kosong, password default: <strong>password123</strong>
                    </div>
                    <div class="mt-2"><span class="pwd-chip" id="pwdChipDisplay">Akan mengikuti NIK pegawai</span>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5">
                    <a href="{{ route('operator.manajemen_akun.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Simpan Akun
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
<script src="{{ asset('assets/manajemen_akun/manajemen_akun.js') }}"></script>
    

   <script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}"
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal!',
            html: `{!! implode('<br>', $errors->all()) !!}`
        });
    @endif
</script>
@endpush