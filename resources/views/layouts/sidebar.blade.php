<div class="sidebar">
    <div>
        <h4>Sistem Informasi Kepegawaian</h4>

        @php
            // Lebih aman menggunakan null-safe operator (?->) untuk mencegah error jika user null
            $user = auth()->user();

            // Memanfaatkan accessor getJenisRoleAttribute() yang sudah kamu buat di model UserManage
            $role = strtolower($user?->jenis_role ?? 'dosen');
        @endphp

        {{-- ============================================================ --}}
        {{-- MENU OPERATOR --}}
        {{-- ============================================================ --}}
        @if ($role === 'operator')
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">Menu
                Operator</div>

            <a href="{{ url('/operator/data-diri') }}" class="{{ request()->is('operator/data-diri*') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Data Diri
            </a>

            <div class="menu-group">
                <div class="menu-group-label {{ request()->is('operator/verifikasi*') ? 'active' : '' }}"
                    onclick="toggleSubMenu(this)">
                    <i class="bi bi-patch-check-fill me-2"></i> Verifikasi Data <i
                        class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </div>
                <div class="sub-menu {{ request()->is('operator/verifikasi*') ? 'open' : '' }}">
                    <a href="{{ url('/operator/verifikasi/surat-tugas') }}"
                        class="{{ request()->is('operator/verifikasi/surat-tugas*') ? 'active' : '' }}"><i
                            class="bi bi-file-earmark-text me-2"></i> Surat Tugas</a>
                    <a href="{{ url('/operator/verifikasi/panggol') }}"
                        class="{{ request()->is('operator/verifikasi/panggol*') ? 'active' : '' }}"><i
                            class="bi bi-award me-2"></i> Pangkat</a>
                    <a href="{{ url('/operator/verifikasi/jabfung') }}"
                        class="{{ request()->is('operator/verifikasi/jabfung*') ? 'active' : '' }}"><i
                            class="bi bi-briefcase me-2"></i> Jabfung</a>

                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- MENU PIMPINAN --}}
            {{-- ============================================================ --}}
        @elseif($role === 'pimpinan')
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">
                Menu Pimpinan
            </div>

            {{-- Dashboard --}}
            <a href="{{ route('pimpinan.dashboard') }}"
                class="{{ request()->routeIs('pimpinan.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>

            {{-- Data Diri --}}
            <a href="{{ route('pimpinan.datadiri.index') }}"
                class="{{ request()->routeIs('pimpinan.datadiri.*', 'pimpinan.pegawai.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge me-2"></i> Data Diri
            </a>

            {{-- Persetujuan (Dropdown) --}}
            <div class="menu-group">
                <div class="menu-group-label {{ request()->routeIs('pimpinan.persetujuan.*') ? 'active' : '' }}"
                    onclick="toggleSubMenu(this)">
                    <i class="bi bi-patch-check-fill me-2"></i> Persetujuan
                    <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </div>
                <div class="sub-menu {{ request()->routeIs('pimpinan.persetujuan.*') ? 'open' : '' }}">
                    <a href="{{ route('pimpinan.persetujuan.surat-tugas') }}"
                        class="{{ request()->routeIs('pimpinan.persetujuan.surat-tugas') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Surat Tugas
                    </a>
                    <a href="{{ route('pimpinan.persetujuan.panggol') }}"
                        class="{{ request()->routeIs('pimpinan.persetujuan.panggol') ? 'active' : '' }}">
                        <i class="bi bi-award me-2"></i> Pangkat
                    </a>
                    <a href="{{ route('pimpinan.persetujuan.jabfung') }}"
                        class="{{ request()->routeIs('pimpinan.persetujuan.jabfung') ? 'active' : '' }}">
                        <i class="bi bi-briefcase me-2"></i> Jabfung
                    </a>
                    
                </div>
            </div>
            {{-- ============================================================ --}}
            {{-- MENU DOSEN --}}
            {{-- ============================================================ --}}
        @else
            <div class="menu-section px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; opacity: 0.6;">Menu
                Dosen</div>
            <a href="{{ url('/dosen/data-diri') }}" class="{{ request()->is('dosen/data-diri*') ? 'active' : '' }}"><i
                    class="bi bi-person-badge me-2"></i> Data Diri</a>
            <a href="{{ route('dosen.surat.index') }}"
                class="{{ request()->routeIs('dosen.surat.index') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper-fill me-2"></i>Pengajuan Surat Tugas
            </a>
            <a href="{{ url('/dosen/pangkat-golongan') }}"
                class="{{ request()->is('dosen/pangkat-golongan*') ? 'active' : '' }}"><i class="bi bi-award me-2"></i>
                Data Pangkat Golongan</a>
            <a href="{{ url('/dosen/jabatanfungsional') }}"
                class="{{ request()->is('dosen/jabatanfungsional*') ? 'active' : '' }}"><i
                    class="bi bi-briefcase-fill me-2"></i> Data Jabatan Fungsional</a>
        @endif
    </div>

    {{-- Footer Sidebar --}}
    <div class="mt-auto mb-3">
        <a href="{{ route('logout') }}" class="keluar">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
    </div>
</div>
