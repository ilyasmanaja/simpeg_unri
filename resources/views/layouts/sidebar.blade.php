<div class="sidebar">
    <div>
        <h4>Sistem Informasi Kepegawaian</h4>

        @php
            // Sesuaikan dengan cara ambil role di project kamu
            // Contoh: $role = auth()->user()->role->jenis_role ?? 'dosen';
            $role = auth()->user()->userRole->role->jenis_role ?? 'dosen';
        @endphp

        {{-- ============================================================ --}}
        {{-- MENU OPERATOR --}}
        {{-- ============================================================ --}}
        @if($role === 'operator')

            <a href="{{ url('/operator/dashboard') }}"
               class="{{ request()->is('operator/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>

            {{-- Verifikasi Data + Sub Menu --}}
            <div class="menu-group">
                <div class="menu-group-label
                    {{ request()->is('operator/verifikasi*') ? 'active' : '' }}"
                    onclick="toggleSubMenu(this)">
                    <i class="bi bi-patch-check-fill me-2"></i>
                    Verifikasi Data
                    <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
                </div>
                <div class="sub-menu {{ request()->is('operator/verifikasi*') ? 'open' : '' }}">
                    <a href="{{ url('/operator/verifikasi/surat-tugas') }}"
                       class="{{ request()->is('operator/verifikasi/surat-tugas*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Surat Tugas
                    </a>
                    <a href="{{ url('/operator/verifikasi/jabfung') }}"
                       class="{{ request()->is('operator/verifikasi/jabfung*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase me-2"></i> Jabatan Fungsional
                    </a>
                    <a href="{{ url('/operator/verifikasi/panggol') }}"
                       class="{{ request()->is('operator/verifikasi/panggol*') ? 'active' : '' }}">
                        <i class="bi bi-award me-2"></i> Pangkat Golongan
                    </a>
                </div>
            </div>

        {{-- ============================================================ --}}
        {{-- MENU DOSEN (menu lama kamu) --}}
        {{-- ============================================================ --}}
        @else

            <a href="/dashboard">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>
            <a href="{{ url('/dosen/surat-tugas') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-paper-fill" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6.5 9.5 3 7.5v-6A1.5 1.5 0 0 1 4.5 0h7A1.5 1.5 0 0 1 13 1.5v6l-3.5 2L8 8.75zM1.059 3.635 2 3.133v3.753L0 5.713V5.4a2 2 0 0 1 1.059-1.765M16 5.713l-2 1.173V3.133l.941.502A2 2 0 0 1 16 5.4zm0 1.16-5.693 3.337L16 13.372v-6.5Zm-8 3.199 7.941 4.412A2 2 0 0 1 14 16H2a2 2 0 0 1-1.941-1.516zm-8 3.3 5.693-3.162L0 6.873v6.5Z" />
                </svg> Pengajuan Surat Tugas
            </a>
            <a href="{{ url('/dosen/data-diri') }}" class="{{ request()->is('dosen/data-diri*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                    <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z" />
                </svg> Data Diri
            </a>
            <a href="{{ url('/dosen/pangkat-golongan') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-award" viewBox="0 0 16 16">
                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z" />
                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z" />
                </svg> Data Pangkat Golongan
            </a>
            <a href="{{ url('/dosen/jabatan-fungsional') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase-fill" viewBox="0 0 16 16">
                    <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5" />
                    <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85z" />
                </svg> Data Jabatan Fungsional
            </a>

        @endif
    </div>

    <div class="mt-auto mb-3">
        <a href="#"><img src="{{ asset('assets/dosen/data_diri/pfp.jpg') }}" alt=""> Profil</a>
        <a href="/logout" class="keluar">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg> Keluar
        </a>
    </div>
</div>

{{-- CSS tambahan untuk sub menu --}}
<style>
.menu-group-label {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    cursor: pointer;
    color: inherit;
    font-size: .9rem;
    border-radius: 6px;
    transition: background .15s;
    user-select: none;
}
.menu-group-label:hover,
.menu-group-label.active {
    background: rgba(255,255,255,.12);
}
.menu-group-label .toggle-icon {
    transition: transform .25s;
}
.sub-menu {
    display: none;
    flex-direction: column;
    padding-left: 16px;
}
.sub-menu.open {
    display: flex;
}
.sub-menu a {
    font-size: .85rem;
    padding: 7px 12px;
    border-left: 2px solid rgba(255,255,255,.2);
    margin-bottom: 2px;
}
.sub-menu a.active,
.sub-menu a:hover {
    border-left-color: #fff;
    background: rgba(255,255,255,.1);
}
/* Rotate chevron saat open */
.menu-group-label.open .toggle-icon {
    transform: rotate(180deg);
}
</style>

<script>
function toggleSubMenu(el) {
    const sub = el.nextElementSibling;
    const isOpen = sub.classList.contains('open');
    sub.classList.toggle('open', !isOpen);
    el.classList.toggle('open', !isOpen);
}
</script>