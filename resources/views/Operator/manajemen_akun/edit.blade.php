@extends('layouts.app')

@section('title', 'Edit Akun Pegawai')

@section('content')
    <div class="container-fluid p-4">

        <form action="{{ route('operator.manajemen_akun.update', $pegawai->id_pegawai) }}" method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="container-box">

                <div class="header-box mb-4">

                    <div class="header-left">

                        <div class="icon-box">
                            <i class="bi bi-pencil-square"></i>
                        </div>

                        <div>

                            <h1 class="page-title">
                                Edit Akun Pegawai
                            </h1>

                            <p class="page-subtitle">
                                Halaman edit akun pegawai
                            </p>

                        </div>

                    </div>

                </div>

                <form action="{{ route('operator.manajemen_akun.update', $pegawai->id_pegawai) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="container-box">

                        {{-- ================= IDENTITAS ================= --}}
                        <div class="section-divider">
                            <i class="bi bi-person-vcard-fill"></i> Data Identitas
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">Foto Pegawai</label>

                                <input type="file" name="foto" class="form-control" accept="image/*"
                                    id="previewFotoInput">

                                <img id="previewFoto"
                                    src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}"
                                    class="mt-3"
                                    style="width:120px;height:120px;object-fit:cover;border-radius:14px;display:block;">

                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>

                                <input type="text" name="nama_lengkap" class="form-control"
                                    value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIK</label>

                                <input type="text" name="nik" class="form-control"
                                    value="{{ old('nik', $pegawai->nik) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>

                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>

                                <select name="jenis_kelamin" class="form-select">
                                    <option value="Laki-laki"
                                        {{ $pegawai->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>

                                    <option value="Perempuan"
                                        {{ $pegawai->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                        </div>

                        {{-- ================= KONTAK ================= --}}
                        <div class="section-divider mt-5">
                            <i class="bi bi-telephone-fill"></i> Data Kontak
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Nomor HP</label>

                                <input type="text" name="nomor_hp" class="form-control"
                                    value="{{ old('nomor_hp', $pegawai->nomor_hp) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No HP Darurat</label>

                                <input type="text" name="nomor_hp_darurat" class="form-control"
                                    value="{{ old('nomor_hp_darurat', $pegawai->nomor_hp_darurat) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>

                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $pegawai->user->email ?? '') }}">
                            </div>

                        </div>

                        {{-- ================= AKADEMIK ================= --}}
                        <div class="section-divider mt-5">
                            <i class="bi bi-mortarboard-fill"></i> Data Akademik
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Jurusan</label>

                                <input type="text" name="jurusan" class="form-control"
                                    value="{{ old('jurusan', $pegawai->jurusan) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Program Studi</label>

                                <input type="text" name="prodi" class="form-control"
                                    value="{{ old('prodi', $pegawai->prodi) }}">
                            </div>

                        </div>

                        {{-- ================= KEPEGAWAIAN ================= --}}
                        <div class="section-divider mt-5">
                            <i class="bi bi-briefcase-fill"></i> Data Kepegawaian
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Status Pegawai</label>

                                <select name="status_pegawai" class="form-select">
                                    <option value="ASN" {{ $pegawai->status_pegawai == 'ASN' ? 'selected' : '' }}>
                                        ASN
                                    </option>

                                    <option value="Non ASN" {{ $pegawai->status_pegawai == 'Non ASN' ? 'selected' : '' }}>
                                        Non ASN
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIP</label>

                                <input type="text" name="nip" class="form-control"
                                    value="{{ old('nip', $pegawai->nip) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIDN</label>

                                <input type="text" name="nidn" class="form-control"
                                    value="{{ old('nidn', $pegawai->nidn) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jabatan Fungsional</label>

                                <select name="id_jabfung" class="form-select">
                                    <option value="">-- Pilih --</option>

                                    @foreach ($jabfungs as $jabfung)
                                        <option value="{{ $jabfung->id_jabfung }}"
                                            {{ $pegawai->id_jabfung == $jabfung->id_jabfung ? 'selected' : '' }}>
                                            {{ $jabfung->nama_jabfung }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pangkat / Golongan</label>

                                <select name="id_panggol" class="form-select">
                                    <option value="">-- Pilih --</option>

                                    @foreach ($pangkats as $pangkat)
                                        <option value="{{ $pangkat->id_panggol }}"
                                            {{ $pegawai->id_panggol == $pangkat->id_panggol ? 'selected' : '' }}>
                                            {{ $pangkat->pangkat }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="warn_pangkat_pimpinan" class="mt-2 p-2"
                                    style="display:none;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Role <strong>Pimpinan</strong> membutuhkan minimal pangkat <strong>IV/a</strong>.
                                </div>

                                <div id="warn_jabfung_pimpinan" class="col-12" style="display:none;">
                                    <div class="p-2"
                                        style="background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Role <strong>Pimpinan</strong> membutuhkan minimal jabatan <strong>Lektor
                                            Kepala</strong>.
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ================= PASSWORD ================= --}}
                        <div class="section-divider mt-5">
                            <i class="bi bi-key-fill"></i> Akun Login
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Password Baru</label>

                                <input type="password" name="password" class="form-control">

                                <small class="text-muted">
                                    Kosongkan jika tidak ingin mengganti password
                                </small>
                            </div>

                        </div>

                        {{-- ================= ROLE ================= --}}
                        {{-- ================= ROLE ================= --}}
                        <div class="section-divider mt-5">
                            <i class="bi bi-shield-check"></i> Hak Akses
                        </div>

                        <p style="font-size:.82rem;color:#6c757d;margin-bottom:12px;">
                            Pilih role / hak akses yang dimiliki pegawai.
                        </p>

                        {{-- PANDUAN --}}
                        <div class="hint-hak-akses mb-3">

                            <strong>
                                <i class="bi bi-info-circle me-1"></i>
                                Panduan kombinasi:
                            </strong>

                            <ul class="mb-0">
                                <li><strong>Pimpinan</strong>: bisa sendiri atau + Dosen</li>
                                <li><strong>Dosen</strong>: bisa sendiri atau + Pimpinan</li>
                                <li><strong>Tendik</strong>: bisa sendiri atau + Operator</li>
                                <li><strong>Operator</strong>: harus dikombinasikan dengan Tendik</li>
                            </ul>

                        </div>

                        {{-- CARD ROLE --}}
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
                                            id="role{{ $role->id_role }}"
                                            @if ($pegawai->user && $pegawai->user->roles->contains('id_role', $role->id_role)) checked @endif>

                                        <div class="hak-akses-label">

                                            <div class="ha-icon">
                                                <i class="bi {{ $icon }}"></i>
                                            </div>

                                            <div>

                                                <div class="ha-text">
                                                    {{ $role->jenis_role }}
                                                </div>

                                                <div class="ha-desc">
                                                    {{ $desc }}
                                                </div>

                                            </div>

                                            <div class="ha-check">
                                                <i class="bi bi-check2 ha-check-icon"></i>
                                            </div>

                                        </div>

                                    </label>

                                </div>
                            @endforeach

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('operator.manajemen_akun.index') }}" class="btn btn-secondary">
                                Batal
                            </a>

                            <button class="btn btn-primary">
                                <i class="b"></i>Update
                            </button>
                        </div>

                    </div>

                </form>



            </div>

        </form>

    </div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- Preview foto ---
            const fotoInput = document.getElementById('previewFotoInput');
            if (fotoInput) {
                fotoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        document.getElementById('previewFoto').src = URL.createObjectURL(file);
                    }
                });
            }

            // --- Role logic ---
            const checkboxes = document.querySelectorAll('input[name="roles[]"]');
            const statusEl = document.querySelector('select[name="status_pegawai"]');
            const pangkatEl = document.querySelector('select[name="id_panggol"]');
            const jabfungEl = document.querySelector('select[name="id_jabfung"]');

            const golonganPimpinan = ['IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'];
            const jabfungPimpinan = ['lektor kepala', 'profesor', 'guru besar'];

            function getRole(jenis) {
                return [...checkboxes].find(cb =>
                    cb.closest('.hak-akses-card')
                    .querySelector('.ha-text')
                    .textContent.trim().toLowerCase() === jenis
                );
            }

            const dosenCb = getRole('dosen');
            const pimpinanCb = getRole('pimpinan');
            const tendikCb = getRole('tendik');
            const operatorCb = getRole('operator');

            function isPimpinanChecked() {
                return pimpinanCb && pimpinanCb.checked;
            }

            function isNonASN() {
                return statusEl && statusEl.value === 'Non ASN';
            }

            function disable(cb) {
                if (!cb) return;
                cb.disabled = true;
                cb.closest('.hak-akses-card').style.opacity = '.45';
            }

            function enableAll() {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                    cb.closest('.hak-akses-card').style.opacity = '1';
                });
            }

            function updatePimpinanWarnings() {
                const warnPangkat = document.getElementById('warn_pangkat_pimpinan');
                const warnJabfung = document.getElementById('warn_jabfung_pimpinan');
                if (!warnPangkat || !warnJabfung) return;

                if (!isPimpinanChecked()) {
                    warnPangkat.style.display = 'none';
                    warnJabfung.style.display = 'none';
                    return;
                }

                if (pangkatEl) {
                    const gol = (pangkatEl.options[pangkatEl.selectedIndex]?.dataset.gol || '').trim();
                    warnPangkat.style.display = golonganPimpinan.includes(gol) ? 'none' : '';
                }
                if (jabfungEl) {
                    const teks = jabfungEl.options[jabfungEl.selectedIndex]?.text.toLowerCase() || '';
                    warnJabfung.style.display = jabfungPimpinan.some(j => teks.includes(j)) ? 'none' : '';
                }
            }

            function applyNonAsnRestriction() {
                if (isNonASN()) {
                    [dosenCb, pimpinanCb].forEach(cb => {
                        if (!cb) return;
                        cb.checked = false;
                        disable(cb);
                        cb.closest('.hak-akses-card').title = 'Non ASN tidak dapat memiliki role ini';
                    });
                }
            }

            function updateRoleLogic() {
                enableAll();
                applyNonAsnRestriction();

                if (tendikCb?.checked) {
                    disable(dosenCb);
                    disable(pimpinanCb);
                    if (dosenCb) dosenCb.checked = false;
                    if (pimpinanCb) pimpinanCb.checked = false;
                }
                if (dosenCb?.checked) {
                    disable(tendikCb);
                    disable(operatorCb);
                    if (tendikCb) tendikCb.checked = false;
                    if (operatorCb) operatorCb.checked = false;
                }
                if (pimpinanCb?.checked) {
                    disable(tendikCb);
                    disable(operatorCb);
                    if (tendikCb) tendikCb.checked = false;
                    if (operatorCb) operatorCb.checked = false;
                }
                if (operatorCb?.checked) {
                    if (tendikCb) {
                        tendikCb.checked = true;
                        disable(tendikCb);
                    }
                    disable(dosenCb);
                    disable(pimpinanCb);
                    if (dosenCb) dosenCb.checked = false;
                    if (pimpinanCb) pimpinanCb.checked = false;
                }

                updatePimpinanWarnings();
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateRoleLogic));
            if (statusEl) statusEl.addEventListener('change', updateRoleLogic);
            if (pangkatEl) pangkatEl.addEventListener('change', updatePimpinanWarnings);
            if (jabfungEl) jabfungEl.addEventListener('change', updatePimpinanWarnings);

            // Aktifkan semua sebelum submit
            document.querySelector('form').addEventListener('submit', function() {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                });
            });

            updateRoleLogic();
        });
    </script>

@endsection
