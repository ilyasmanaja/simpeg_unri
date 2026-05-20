<div class="sidebar">
    <div>
        <h4>Sistem Informasi Kepegawaian</h4>

        @php
            // Mengambil role dengan cara yang lebih aman
            $user = auth()->user();
            $role = strtolower(auth()->user()->userRole->role->jenis_role ?? 'dosen');
        @endphp

        {{-- ============================================================ --}}
        {{-- MENU OPERATOR --}}
        {{-- ============================================================ --}}
        @if($role === 'operator')
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">Menu Operator</div>
            <a href="{{ url('/operator/dashboard') }}" class="{{ request()->is('operator/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>

            <div class="menu-group">
                <div class="menu-group-label {{ request()->is('operator/verifikasi*') ? 'active' : '' }}" onclick="toggleSubMenu(this)">
                    <i class="bi bi-patch-check-fill me-2"></i> Verifikasi Data <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </div>
                <div class="sub-menu {{ request()->is('operator/verifikasi*') ? 'open' : '' }}">
                    <a href="{{ url('/operator/verifikasi/surat-tugas') }}" class="{{ request()->is('operator/verifikasi/surat-tugas*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2"></i> Surat Tugas</a>
                    <a href="{{ url('/operator/verifikasi/jabfung') }}" class="{{ request()->is('operator/verifikasi/jabfung*') ? 'active' : '' }}"><i class="bi bi-briefcase me-2"></i> Jabfung</a>
                    <a href="{{ url('/operator/verifikasi/panggol') }}" class="{{ request()->is('operator/verifikasi/panggol*') ? 'active' : '' }}"><i class="bi bi-award me-2"></i> Pangkat</a>
                </div>
            </div>

        {{-- ============================================================ --}}
        {{-- MENU PIMPINAN --}}
        {{-- ============================================================ --}}
        @elseif($role === 'pimpinan')
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">Menu Pimpinan</div>
            <a href="{{ url('/pimpinan/dashboard') }}" class="{{ request()->is('pimpinan/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>
            <a href="{{ url('/pimpinan/persetujuan') }}" class="{{ request()->is('pimpinan/persetujuan*') ? 'active' : '' }}">
                <i class="bi bi-check2-square me-2"></i> Persetujuan Akhir
            </a>
            <a href="{{ url('/pimpinan/laporan') }}" class="{{ request()->is('pimpinan/laporan*') ? 'active' : '' }}">
                <i class="bi bi-graph-up me-2"></i> Laporan Rekap
            </a>

        {{-- ============================================================ --}}
        {{-- MENU DOSEN --}}
        {{-- ============================================================ --}}
        @else
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">Menu Dosen</div>
            <a href="/dashboard"><i class="fa-solid fa-house me-2"></i> Dashboard</a>
            <a href="{{ url('/dosen/surat-tugas') }}"><i class="bi bi-envelope-paper-fill me-2"></i> Pengajuan Surat Tugas</a>
            <a href="{{ url('/dosen/data-diri') }}" class="{{ request()->is('dosen/data-diri*') ? 'active' : '' }}"><i class="bi bi-person-badge me-2"></i> Data Diri</a>
            <a href="{{ url('/dosen/pangkat-golongan') }}" class="{{ request()->is('dosen/pangkat-golongan*') ? 'active' : '' }}"><i class="bi bi-award me-2"></i> Data Pangkat Golongan</a>
            <a href="{{ url('/dosen/jabatanfungsional') }}" class="{{ request()->is('dosen/jabatanfungsional*') ? 'active' : '' }}"><i class="bi bi-briefcase-fill me-2"></i> Data Jabatan Fungsional</a>
        @endif
    </div>

    {{-- Footer Sidebar --}}
    <div class="mt-auto mb-3">
        <a href="#"><img src="{{ asset('assets/dosen/data_diri/pfp.jpg') }}" alt="Profile" style="width:20px; border-radius:50%; margin-right:8px;"> Profil</a>
        <a href="/logout" class="keluar">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
    </div>
</div>

{{-- CSS & JS tetap sama... --}}