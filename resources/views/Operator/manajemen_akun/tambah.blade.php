@extends('layouts.app')
@section('title', 'Tambah Akun Pegawai')
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
          <input type="text" name="nama_lengkap" class="form-control"
            value="{{ old('nama_lengkap') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">NIK</label>
          <input type="text" name="nik" id="f_nik" class="form-control"
            maxlength="16" value="{{ old('nik') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" class="form-control"
            value="{{ old('tanggal_lahir') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
          <select name="jenis_kelamin" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-laki" {{ old('jenis_kelamin')=='Laki-laki'?'selected':'' }}>Laki-laki</option>
            <option value="Perempuan"  {{ old('jenis_kelamin')=='Perempuan'?'selected':'' }}>Perempuan</option>
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
          <input type="text" name="nomor_hp_darurat" class="form-control" value="{{ old('nomor_hp_darurat') }}">
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
            <option value="ASN"     {{ old('status_pegawai')=='ASN'?'selected':'' }}>ASN</option>
            <option value="Non ASN" {{ old('status_pegawai')=='Non ASN'?'selected':'' }}>Non ASN</option>
          </select>
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
                {{ $jabfung->nama_jabfung }}
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
              <option value="{{ $pangkat->id_panggol }}"
                data-gol="{{ $pangkat->golongan }}"
                {{ old('id_panggol') == $pangkat->id_panggol ? 'selected' : '' }}>
                {{ $pangkat->golongan }} - {{ $pangkat->pangkat }}
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
          <div class="p-2" style="background:#fff0f0;border:1px solid #fca5a5;border-radius:8px;font-size:.78rem;color:#b91c1c;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            Role <strong>Pimpinan</strong> membutuhkan minimal jabatan <strong>Lektor Kepala</strong>.
          </div>
        </div>
      </div>

      {{-- ===== HAK AKSES ===== --}}
      <div class="section-divider mt-5"><i class="bi bi-shield-check"></i> Hak Akses</div>
      <p style="font-size:.82rem;color:#6c757d;margin-bottom:12px;">Pilih role / hak akses yang dimiliki pegawai.</p>

      <div class="hint-hak-akses mb-3">
        <strong><i class="bi bi-info-circle me-1"></i>Panduan kombinasi:</strong>
        <ul class="mb-0">
          <li><strong>Pimpinan</strong>: bisa sendiri atau + Dosen. Min. pangkat IV/a, min. Lektor Kepala</li>
          <li><strong>Dosen</strong>: bisa sendiri atau + Pimpinan. Hanya untuk ASN</li>
          <li><strong>Tendik</strong>: bisa sendiri atau + Operator</li>
          <li><strong>Operator</strong>: harus dikombinasikan dengan Tendik</li>
        </ul>
      </div>

      <div class="hak-akses-grid">
        @foreach ($roles as $role)
          @php
            $warna = '#2563eb'; $icon = 'bi-person-fill'; $desc = 'Hak akses sistem';
            if (strtolower($role->jenis_role) == 'pimpinan') { $warna='#7c3aed'; $icon='bi-building'; $desc='Akses dashboard pimpinan'; }
            if (strtolower($role->jenis_role) == 'dosen')    { $warna='#2563eb'; $icon='bi-mortarboard-fill'; $desc='Akses portal dosen'; }
            if (strtolower($role->jenis_role) == 'tendik')   { $warna='#047857'; $icon='bi-person-gear'; $desc='Akses tenaga kependidikan'; }
            if (strtolower($role->jenis_role) == 'operator') { $warna='#b45309'; $icon='bi-shield-lock-fill'; $desc='Akses manajemen sistem'; }
          @endphp
          <div>
            <label class="hak-akses-card" style="--ha-color:{{ $warna }};">
              <input type="checkbox" name="roles[]" value="{{ $role->id_role }}"
                id="role{{ $role->id_role }}"
                data-jenis="{{ strtolower($role->jenis_role) }}">
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
        <div class="mt-2"><span class="pwd-chip" id="pwdChipDisplay">Akan mengikuti NIK pegawai</span></div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {

  const statusEl   = document.getElementById('f_status');
  const nikEl      = document.getElementById('f_nik');
  const pwdChip    = document.getElementById('pwdChipDisplay');
  const pangkatEl  = document.getElementById('pangkat');
  const jabfungEl  = document.getElementById('jabfung');
  const checkboxes = document.querySelectorAll('input[name="roles[]"]');

  // --- Golongan yang dianggap memenuhi syarat Pimpinan (IV/a ke atas) ---
  const golonganPimpinan = ['IV/a','IV/b','IV/c','IV/d','IV/e'];

  // --- Jabatan yang memenuhi syarat Pimpinan ---
  const jabfungPimpinan = ['lektor kepala','profesor','guru besar'];

  function getCheckedRoles() {
    return [...checkboxes].filter(cb => cb.checked).map(cb => cb.dataset.jenis);
  }

  function isPimpinanChecked() {
    return getCheckedRoles().includes('pimpinan');
  }

  function isNonASN() {
    return statusEl.value === 'Non ASN';
  }

  // Tampilkan/sembunyikan field kepegawaian berdasarkan status
  function updateFieldVisibility() {
    const status = statusEl.value;
    const showFields = status !== '';
    document.getElementById('wrap_nip').style.display    = showFields ? '' : 'none';
    document.getElementById('wrap_nidn').style.display   = showFields ? '' : 'none';
    document.getElementById('wrap_jabfung').style.display = showFields ? '' : 'none';
    document.getElementById('wrap_pangkat').style.display = showFields ? '' : 'none';

    // Non ASN: disable dan uncheck role Dosen & Pimpinan
    checkboxes.forEach(cb => {
      if (isNonASN() && (cb.dataset.jenis === 'dosen' || cb.dataset.jenis === 'pimpinan')) {
        cb.checked  = false;
        cb.disabled = true;
        cb.closest('.hak-akses-card').style.opacity = '.45';
        cb.closest('.hak-akses-card').title = 'Non ASN tidak dapat memiliki role ini';
      } else {
        cb.disabled = false;
        cb.closest('.hak-akses-card').style.opacity = '1';
        cb.closest('.hak-akses-card').title = '';
      }
    });

    updateRoleLogic();
    updatePimpinanWarnings();
  }

  // Validasi syarat Pimpinan: pangkat & jabfung
  function updatePimpinanWarnings() {
    if (!isPimpinanChecked()) {
      document.getElementById('warn_pangkat_pimpinan').style.display = 'none';
      document.getElementById('warn_jabfung_pimpinan').style.display = 'none';
      return;
    }

    // Cek pangkat
    const selectedOpt = pangkatEl.options[pangkatEl.selectedIndex];
    const gol = selectedOpt ? (selectedOpt.dataset.gol || '').trim() : '';
    const pangkatOk = golonganPimpinan.includes(gol);
    document.getElementById('warn_pangkat_pimpinan').style.display = pangkatOk ? 'none' : '';

    // Cek jabfung
    const jabfungTeks = jabfungEl.options[jabfungEl.selectedIndex]
      ? jabfungEl.options[jabfungEl.selectedIndex].text.toLowerCase().trim()
      : '';
    const jabfungOk = jabfungPimpinan.some(j => jabfungTeks.includes(j));
    document.getElementById('warn_jabfung_pimpinan').style.display = jabfungOk ? 'none' : '';
  }

  // Logika kombinasi role
  function updateRoleLogic() {
    const roles = getCheckedRoles();
    const tendikCb   = [...checkboxes].find(cb => cb.dataset.jenis === 'tendik');
    const dosenCb    = [...checkboxes].find(cb => cb.dataset.jenis === 'dosen');
    const pimpinanCb = [...checkboxes].find(cb => cb.dataset.jenis === 'pimpinan');
    const operatorCb = [...checkboxes].find(cb => cb.dataset.jenis === 'operator');

    // Reset (kecuali yang diblock karena Non ASN)
    checkboxes.forEach(cb => {
      if (!cb.disabled) {
        cb.closest('.hak-akses-card').style.opacity = '1';
      }
    });

    function disable(cb) {
      if (!cb) return;
      cb.disabled = true;
      cb.closest('.hak-akses-card').style.opacity = '.45';
    }

    if (roles.includes('tendik')) {
      disable(dosenCb); disable(pimpinanCb);
      dosenCb && (dosenCb.checked = false);
      pimpinanCb && (pimpinanCb.checked = false);
    }
    if (roles.includes('dosen')) {
      disable(tendikCb); disable(operatorCb);
      tendikCb && (tendikCb.checked = false);
      operatorCb && (operatorCb.checked = false);
    }
    if (roles.includes('pimpinan')) {
      disable(tendikCb); disable(operatorCb);
      tendikCb && (tendikCb.checked = false);
      operatorCb && (operatorCb.checked = false);
    }
    if (roles.includes('operator')) {
      if (tendikCb) { tendikCb.checked = true; tendikCb.disabled = true; }
      disable(dosenCb); disable(pimpinanCb);
      dosenCb && (dosenCb.checked = false);
      pimpinanCb && (pimpinanCb.checked = false);
    }

    updatePimpinanWarnings();
  }

  // Update preview password
  nikEl.addEventListener('input', function () {
    pwdChip.textContent = this.value ? this.value : 'Akan mengikuti NIK pegawai';
  });

  statusEl.addEventListener('change', updateFieldVisibility);
  checkboxes.forEach(cb => cb.addEventListener('change', updateRoleLogic));
  pangkatEl.addEventListener('change', updatePimpinanWarnings);
  jabfungEl.addEventListener('change', updatePimpinanWarnings);

  // Aktifkan semua checkbox sebelum submit agar value terkirim
  document.querySelector('form').addEventListener('submit', function (e) {
    // Validasi backend-side warning (hanya warning, tidak block submit)
    checkboxes.forEach(cb => { cb.disabled = false; });
  });

  updateFieldVisibility();
});
</script>
@endsection