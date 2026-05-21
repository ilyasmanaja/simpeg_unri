@extends('layouts.app')

@section('title', 'Manajemen Akun Pegawai')

@section('content')

    <div class="container-fluid p-4">

        <div class="header-box mb-4">
            <div class="header-left">
                <div class="icon-box">
                    <i class="bi bi-people-fill"></i>
                </div>

                <div>
                    <h1 class="page-title">Manajemen Akun Pegawai</h1>

                    <p class="page-subtitle">
                        Halaman kelola data akun pegawai di sistem
                    </p>
                </div>
            </div>
        </div>

        <div class="container-box">

            {{-- TOOLBAR --}}
            <div class="toolbar-row mb-3">

                <form action="{{ route('operator.manajemen_akun.index') }}" method="GET"
                    class="d-flex gap-2 flex-wrap w-100">

                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        style="max-width:280px;height:46px;" placeholder="Cari nama / NIP / NIK">

                    <select name="status" class="form-select" style="max-width:170px;height:46px;">

                        <option value="">Semua Status</option>

                        <option value="PNS" {{ request('status') == 'PNS' ? 'selected' : '' }}>
                            PNS
                        </option>

                        <option value="Non PNS" {{ request('status') == 'Non PNS' ? 'selected' : '' }}>
                            Non PNS
                        </option>

                    </select>

                    <button class="btn-cari-akun">
                        <i class="bi bi-search"></i>
                        Cari
                    </button>

                    <a href="{{ route('operator.manajemen_akun.create') }}" class="btn-buat-akun text-decoration-none">

                        <i class="bi bi-plus-lg"></i>
                        Buat Akun

                    </a>

                </form>

            </div>

            {{-- INFO --}}
            <div class="info-text mb-2">
                @if (request('per_page') == 'all')
                    Menampilkan semua {{ $pegawais->count() }} akun
                @else
                    Menampilkan {{ $pegawais->firstItem() ?? 0 }} - {{ $pegawais->lastItem() ?? 0 }}
                    dari {{ $pegawais->total() }} akun
                @endif
            </div>

            {{-- TABLE --}}
            <div class="table-responsive custom-table">

                <table class="table data-table mb-0">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>NIP / NIDN</th>
                            <th>Jurusan / Prodi</th>
                            <th>Status</th>
                            <th>Hak Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($pegawais as $pegawai)

                            <tr>

                                <td>
                                    {{ $pegawais->firstItem() + $loop->index }}
                                </td>

                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        {{-- @if ($pegawai->foto)

                                        <img src="{{ asset('storage/' . $pegawai->foto) }}"
                                            width="45"
                                            height="45"
                                            style="object-fit:cover;border-radius:50%;">

                                    @else

                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($pegawai->nama_lengkap) }}"
                                            width="45"
                                            height="45"
                                            style="object-fit:cover;border-radius:50%;">

                                    @endif --}}

                                        <div>

                                            <div style="font-weight:600;">
                                                {{ $pegawai->nama_lengkap }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $pegawai->user->email ?? '-' }}
                                            </small>

                                        </div>

                                    </div>

                                </td>

                                <td>{{ $pegawai->nik ?? '-' }}</td>

                                <td>

                                    <div>
                                        <strong>NIP:</strong>
                                        {{ $pegawai->nip ?? '-' }}
                                    </div>

                                    <div>
                                        <strong>NIDN:</strong>
                                        {{ $pegawai->nidn ?? '-' }}
                                    </div>

                                </td>

                                <td>

                                    <div>
                                        {{ $pegawai->jurusan ?? '-' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $pegawai->prodi ?? '-' }}
                                    </small>

                                </td>

                                <td class="text-center">

                                    @if ($pegawai->status_pegawai == 'PNS')
                                        <span class="badge bg-success">
                                            PNS
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Non PNS
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if ($pegawai->user && $pegawai->user->roles->count())
                                        @foreach ($pegawai->user->roles as $role)
                                            <span class="badge bg-primary mb-1">
                                                {{ $role->jenis_role }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">
                                            Tidak ada role
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex justify-content-center gap-1">

                                        <a href="{{ route('operator.manajemen_akun.show', $pegawai->id_pegawai) }}"
                                            class="btn btn-success btn-sm">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                        <a href="{{ route('operator.manajemen_akun.edit', $pegawai->id_pegawai) }}"
                                            class="btn btn-warning btn-sm">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                        <form action="{{ route('operator.manajemen_akun.destroy', $pegawai->id_pegawai) }}"
                                            method="POST" class="form-hapus">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete(this)">

                                                <i class="bi bi-trash-fill"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">

                                    Belum ada data pegawai

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

                    <div class="d-flex align-items-center gap-2">

                        <label class="text-muted small">
                            Tampilkan:
                        </label>

                        <select
                            onchange="window.location.href='?per_page='+this.value+'&search={{ request('search') }}&status={{ request('status') }}'"
                            class="form-select form-select-sm" style="width:100px;">

                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>
                                10
                            </option>

                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>
                                50
                            </option>

                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>
                                100
                            </option>

                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>
                                Semua
                            </option>

                        </select>

                    </div>

                    @if (request('per_page') != 'all')
                        <div class="pagination-wrapper">

                            {{ $pegawais->links() }}

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

    <script>
        function confirmDelete(button) {

            const form = button.closest('.form-hapus');

            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Data pegawai akan dihapus permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {
                    form.submit();
                }

            });
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error'))
            });
        </script>
    @endif
@endsection
