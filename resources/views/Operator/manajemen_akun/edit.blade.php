@extends('layouts.app')

@section('title', 'Edit Akun Pegawai')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/manajemen_akun/style.css') }}">
@endpush

@section('content')

<div class="container-fluid p-4">

    <form action="{{ route('operator.manajemen_akun.update', $pegawai->id_pegawai) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="container-box">

            {{-- ================= HEADER ================= --}}
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

            {{-- ================= IDENTITAS ================= --}}
            <div class="section-divider">
                <i class="bi bi-person-vcard-fill"></i> Data Identitas
            </div>

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Foto Pegawai
                    </label>

                    <input type="file"
                        name="foto"
                        class="form-control"
                        accept="image/*"
                        id="previewFotoInput">

                    <img id="previewFoto"
                        src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}"
                        class="mt-3"
                        style="width:120px;height:120px;object-fit:cover;border-radius:14px;display:block;">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Nama Lengkap
                    </label>

                    <input type="text"
                        name="nama_lengkap"
                        class="form-control"
                        value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        NIK
                    </label>

                    <input type="text"
                        name="nik"
                        id="f_nik"
                        class="form-control"
                        maxlength="16"
                        value="{{ old('nik', $pegawai->nik) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Tanggal Lahir
                    </label>

                    <input type="date"
                        name="tanggal_lahir"
                        class="form-control"
                        value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Jenis Kelamin
                    </label>

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

                    <label class="form-label">
                        Nomor HP
                    </label>

                    <input type="text"
                        name="nomor_hp"
                        class="form-control"
                        value="{{ old('nomor_hp', $pegawai->nomor_hp) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        No HP Darurat
                    </label>

                    <input type="text"
                        name="nomor_hp_darurat"
                        class="form-control"
                        value="{{ old('nomor_hp_darurat', $pegawai->nomor_hp_darurat) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Email
                    </label>

                    <input type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $pegawai->user->email ?? '') }}">

                </div>

            </div>

            {{-- ================= AKADEMIK ================= --}}
            <div class="section-divider mt-5">
                <i class="bi bi-mortarboard-fill"></i> Data Akademik
            </div>

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Jurusan
                    </label>

                    <input type="text"
                        name="jurusan"
                        class="form-control"
                        value="{{ old('jurusan', $pegawai->jurusan) }}">

                </div>

                <div class="col-md-6">

                    <label class="form-label">
                        Program Studi
                    </label>

                    <input type="text"
                        name="prodi"
                        class="form-control"
                        value="{{ old('prodi', $pegawai->prodi) }}">

                </div>

            </div>

            {{-- ================= KEPEGAWAIAN ================= --}}
            <div class="section-divider mt-5">
                <i class="bi bi-briefcase-fill"></i> Data Kepegawaian
            </div>

            <div class="row g-3">

                {{-- STATUS --}}
                <div class="col-md-6">

                    <label class="form-label">
                        Status Pegawai
                    </label>

                    <select name="status_pegawai"
                        id="f_status"
                        class="form-select">

                        <option value="PNS"
                            {{ $pegawai->status_pegawai == 'PNS' ? 'selected' : '' }}>
                            PNS
                        </option>

                        <option value="Non PNS"
                            {{ $pegawai->status_pegawai == 'Non PNS' ? 'selected' : '' }}>
                            Non PNS
                        </option>

                    </select>

                </div>

                {{-- TIPE --}}
                <div class="col-12" id="wrap_tipe_pns">

                    <label class="form-label">
                        Tipe
                    </label>

                    @php
                        $isDosen = false;

                        if ($pegawai->id_jabfung >= 1 && $pegawai->id_jabfung <= 4) {
                            $isDosen = true;
                        }
                    @endphp

                    <div class="tipe-pns-row">

                        <div class="tipe-opt">

                            <input type="radio"
                                id="tipe_dosen"
                                name="tipe_pns_radio"
                                value="dosen"
                                {{ $isDosen ? 'checked' : '' }}>

                            <label for="tipe_dosen">
                                <i class="bi bi-mortarboard-fill"></i>
                                Dosen
                            </label>

                        </div>

                        <div class="tipe-opt">

                            <input type="radio"
                                id="tipe_tendik"
                                name="tipe_pns_radio"
                                value="tendik"
                                {{ !$isDosen ? 'checked' : '' }}>

                            <label for="tipe_tendik">
                                <i class="bi bi-person-gear"></i>
                                Tendik
                            </label>

                        </div>

                    </div>

                    <div id="hint_nondosen"
                        style="display:none;margin-top:8px;padding:8px 12px;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">

                        <i class="bi bi-info-circle-fill me-1"></i>

                        Non PNS hanya dapat memiliki tipe
                        <strong>Tendik</strong>.

                    </div>

                </div>

                {{-- NIP --}}
                <div class="col-md-6" id="wrap_nip">

                    <label class="form-label">
                        NIP
                    </label>

                    <input type="text"
                        name="nip"
                        class="form-control"
                        value="{{ old('nip', $pegawai->nip) }}">

                </div>

                {{-- NIDN --}}
                <div class="col-md-6" id="wrap_nidn">

                    <label class="form-label">
                        NIDN
                    </label>

                    <input type="text"
                        name="nidn"
                        class="form-control"
                        value="{{ old('nidn', $pegawai->nidn) }}">

                </div>

                {{-- JABFUNG --}}
                <div class="col-md-6" id="wrap_jabfung">

                    <label class="form-label">
                        Jabatan Fungsional
                    </label>

                    <select name="id_jabfung"
                        id="jabfung"
                        class="form-select">

                        <option value="">-- Pilih --</option>

                        @foreach ($jabfungs as $jabfung)

                            <option value="{{ $jabfung->id_jabfung }}"
                                {{ $pegawai->id_jabfung == $jabfung->id_jabfung ? 'selected' : '' }}>

                                {{ $jabfung->jenis_jabfung }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- PANGKAT --}}
                <div class="col-md-6" id="wrap_pangkat">

                    <label class="form-label">
                        Pangkat / Golongan
                    </label>

                    <select name="id_panggol"
                        id="pangkat"
                        class="form-select">

                        <option value="">-- Pilih --</option>

                        @foreach ($pangkats as $pangkat)

                            <option value="{{ $pangkat->id_panggol }}"
                                data-gol="{{ $pangkat->jenis_pangkat }}"
                                {{ $pegawai->id_panggol == $pangkat->id_panggol ? 'selected' : '' }}>

                                {{ $pangkat->jenis_pangkat }} 
                            </option>

                        @endforeach

                    </select>

                    <div id="warn_pangkat_pimpinan"
                        class="mt-2 p-2"
                        style="display:none;background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">

                        <i class="bi bi-exclamation-triangle-fill me-1"></i>

                        Role <strong>Pimpinan</strong>
                        membutuhkan minimal pangkat
                        <strong>IV/a</strong>.

                    </div>

                </div>

                <div class="col-12"
                    id="warn_jabfung_pimpinan"
                    style="display:none;">

                    <div class="p-2"
                        style="background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">

                        <i class="bi bi-exclamation-triangle-fill me-1"></i>

                        Role <strong>Pimpinan</strong>
                        membutuhkan minimal jabatan
                        <strong>Lektor Kepala</strong>.

                    </div>

                </div>

            </div>

            {{-- ================= PASSWORD ================= --}}
            <div class="section-divider mt-5">
                <i class="bi bi-key-fill"></i> Akun Login
            </div>

            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Password Baru
                    </label>

                    <input type="password"
                        name="password"
                        class="form-control">

                    <small class="text-muted">
                        Kosongkan jika tidak ingin mengganti password
                    </small>

                </div>

            </div>

            {{-- ================= ROLE ================= --}}
            <div class="section-divider mt-5">
                <i class="bi bi-shield-check"></i> Hak Akses
            </div>

            <p style="font-size:.82rem;color:#6c757d;margin-bottom:12px;">
                Pilih role / hak akses yang dimiliki pegawai.
            </p>

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

                        <label class="hak-akses-card"
                            style="--ha-color:{{ $warna }};">

                            <input type="checkbox"
                                name="roles[]"
                                value="{{ $role->id_role }}"
                                data-jenis="{{ strtolower($role->jenis_role) }}"
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

            {{-- BUTTON --}}
            <div class="d-flex justify-content-end gap-2 mt-5">

                <a href="{{ route('operator.manajemen_akun.index') }}"
                    class="btn btn-secondary">

                    Batal

                </a>

                <button type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>
                    Update

                </button>

            </div>

        </div>

    </form>

</div>
@endsection


@push('scripts')
  <script src="{{ asset('assets/manajemen_akun/manajemen_akun.js') }}"></script>  

<script>

document.addEventListener('DOMContentLoaded', function() {

    // =========================
    // PREVIEW FOTO
    // =========================

    const fotoInput = document.getElementById('previewFotoInput');

    if (fotoInput) {

        fotoInput.addEventListener('change', function(e) {

            const file = e.target.files[0];

            if (file) {

                document.getElementById('previewFoto').src =
                    URL.createObjectURL(file);
            }
        });
    }

    // =========================
    // ELEMENT
    // =========================

    const statusEl = document.getElementById('f_status');

    const tipeWrap = document.getElementById('wrap_tipe_pns');

    const tipeDosen = document.getElementById('tipe_dosen');

    const tipeTendik = document.getElementById('tipe_tendik');

    const hintNonDosen = document.getElementById('hint_nondosen');

    const pangkatEl = document.getElementById('pangkat');

    const jabfungEl = document.getElementById('jabfung');

    const wrapNip = document.getElementById('wrap_nip');

    const wrapNidn = document.getElementById('wrap_nidn');

    const wrapPangkat = document.getElementById('wrap_pangkat');

    const wrapJabfung = document.getElementById('wrap_jabfung');

    const warnPangkat = document.getElementById('warn_pangkat_pimpinan');

    const warnJabfung = document.getElementById('warn_jabfung_pimpinan');

    const checkboxes = document.querySelectorAll('input[name="roles[]"]');

    // =========================
    // SYARAT PIMPINAN
    // =========================

    const golonganPimpinan = [
        'IV/a - Pembina',
        'IV/b - Pembina Tingkat I',
        'IV/c - Pembina Utama Muda',
        'IV/d - Pembina Utama Madya',
        'IV/e - Pembina Utama'
    ];

    const jabfungPimpinan = [
        'lektor kepala',
        'guru besar',
        'profesor'
    ];

    // =========================
    // HELPER
    // =========================

    function getCheckedRoles() {

        return [...checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.jenis);
    }

    function isPimpinanChecked() {

        return getCheckedRoles().includes('pimpinan');
    }

    // =========================
    // FILTER JABFUNG
    // =========================

    function filterJabfung() {

        const isDosen = tipeDosen.checked;

        const isTendik = tipeTendik.checked;

        [...jabfungEl.options].forEach(option => {

            if (!option.value) return;

            const id = parseInt(option.value);

            if (isDosen) {

                if (id >= 1 && id <= 4) {

                    option.style.display = '';

                } else {

                    option.style.display = 'none';

                    if (jabfungEl.value == option.value) {

                        jabfungEl.value = '';
                    }
                }

            } else if (isTendik) {

                if (id >= 5 && id <= 14) {

                    option.style.display = '';

                } else {

                    option.style.display = 'none';

                    if (jabfungEl.value == option.value) {

                        jabfungEl.value = '';
                    }
                }

            } else {

                option.style.display = '';
            }

        });
    }

    // =========================
    // SHOW HIDE FIELD
    // =========================

    function updateFieldVisibility() {

        const status = statusEl.value;

        tipeWrap.style.display = '' ;

        if (status === 'PNS') {

            wrapNip.style.display = '';

            wrapNidn.style.display = '';

            wrapPangkat.style.display = '';

            wrapJabfung.style.display = '';

            tipeDosen.disabled = false;

            tipeTendik.disabled = false;

            hintNonDosen.style.display = 'none';

        }

        else if (status === 'Non PNS') {

            wrapNip.style.display = 'none';

            wrapNidn.style.display = 'none';

            wrapPangkat.style.display = 'none';

            wrapJabfung.style.display = '';

            tipeDosen.checked = false;

            tipeDosen.disabled = true;

            tipeTendik.checked = true;

            tipeTendik.disabled = true;

            hintNonDosen.style.display = '';

            pangkatEl.value = '';
        }

        filterJabfung();

        updateRoleLogic();

        updatePimpinanWarnings();
    }

    // =========================
    // VALIDASI PIMPINAN
    // =========================

    function updatePimpinanWarnings() {

        if (!isPimpinanChecked()) {

            warnPangkat.style.display = 'none';

            warnJabfung.style.display = 'none';

            return;
        }

        if (statusEl.value === 'PNS') {

            const selectedOpt = pangkatEl.options[pangkatEl.selectedIndex];

            const gol = selectedOpt ?
                (selectedOpt.dataset.gol || '').trim() :
                '';

            const pangkatOk = golonganPimpinan.includes(gol);

            warnPangkat.style.display = pangkatOk ?
                'none' :
                '';

        } else {

            warnPangkat.style.display = 'none';
        }

        const jabfungText = jabfungEl.options[jabfungEl.selectedIndex] ?
            jabfungEl.options[jabfungEl.selectedIndex].text
            .toLowerCase()
            .trim() :
            '';

        const jabfungOk = jabfungPimpinan.some(j =>
            jabfungText.includes(j)
        );

        warnJabfung.style.display = jabfungOk ?
            'none' :
            '';
    }

    // =========================
    // ROLE LOGIC
    // =========================

    function updateRoleLogic() {

        const tendikCb = [...checkboxes]
            .find(cb => cb.dataset.jenis === 'tendik');

        const dosenCb = [...checkboxes]
            .find(cb => cb.dataset.jenis === 'dosen');

        const pimpinanCb = [...checkboxes]
            .find(cb => cb.dataset.jenis === 'pimpinan');

        const operatorCb = [...checkboxes]
            .find(cb => cb.dataset.jenis === 'operator');

        checkboxes.forEach(cb => {

            cb.disabled = false;

            cb.closest('.hak-akses-card').style.opacity = '1';
        });

        if (statusEl.value === 'Non PNS') {

            if (dosenCb) {

                dosenCb.checked = false;

                dosenCb.disabled = true;

                dosenCb.closest('.hak-akses-card').style.opacity = '.45';
            }

            if (pimpinanCb) {

                pimpinanCb.checked = false;

                pimpinanCb.disabled = true;

                pimpinanCb.closest('.hak-akses-card').style.opacity = '.45';
            }
        }

        if (tendikCb && tendikCb.checked) {

            if (dosenCb) {

                dosenCb.checked = false;

                dosenCb.disabled = true;

                dosenCb.closest('.hak-akses-card').style.opacity = '.45';
            }

            if (pimpinanCb) {

                pimpinanCb.checked = false;

                pimpinanCb.disabled = true;

                pimpinanCb.closest('.hak-akses-card').style.opacity = '.45';
            }
        }

        if (dosenCb && dosenCb.checked) {

            if (tendikCb) {

                tendikCb.checked = false;

                tendikCb.disabled = true;

                tendikCb.closest('.hak-akses-card').style.opacity = '.45';
            }

            if (operatorCb) {

                operatorCb.checked = false;

                operatorCb.disabled = true;

                operatorCb.closest('.hak-akses-card').style.opacity = '.45';
            }
        }

        if (pimpinanCb && pimpinanCb.checked) {

            if (tendikCb) {

                tendikCb.checked = false;

                tendikCb.disabled = true;

                tendikCb.closest('.hak-akses-card').style.opacity = '.45';
            }

            if (operatorCb) {

                operatorCb.checked = false;

                operatorCb.disabled = true;

                operatorCb.closest('.hak-akses-card').style.opacity = '.45';
            }
        }

        if (operatorCb && operatorCb.checked) {

            if (tendikCb) {

                tendikCb.checked = true;

                tendikCb.disabled = true;

                tendikCb.closest('.hak-akses-card').style.opacity = '1';
            }

            if (dosenCb) {

                dosenCb.checked = false;

                dosenCb.disabled = true;

                dosenCb.closest('.hak-akses-card').style.opacity = '.45';
            }

            if (pimpinanCb) {

                pimpinanCb.checked = false;

                pimpinanCb.disabled = true;

                pimpinanCb.closest('.hak-akses-card').style.opacity = '.45';
            }
        }

        updatePimpinanWarnings();
    }

    // =========================
    // EVENT
    // =========================

    tipeDosen.addEventListener('change', filterJabfung);

    tipeTendik.addEventListener('change', filterJabfung);

    statusEl.addEventListener('change', updateFieldVisibility);

    checkboxes.forEach(cb => {

        cb.addEventListener('change', updateRoleLogic);
    });

    pangkatEl.addEventListener('change', updatePimpinanWarnings);

    jabfungEl.addEventListener('change', updatePimpinanWarnings);

    document.querySelector('form')
        .addEventListener('submit', function() {

            checkboxes.forEach(cb => {

                cb.disabled = false;
            });
        });

    // INIT
    updateFieldVisibility();

});

</script>
@endpush

