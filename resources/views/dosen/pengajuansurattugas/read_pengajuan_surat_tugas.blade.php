@extends('layouts.app')

@section('title', 'Pengajuan Surat Tugas - SIMPEG UNRI')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pengajuan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="main p-4">
    <div class="header-box">
        <div class="header-left">
            <div class="icon-box">📄</div>
            <div>
                <h1>Pengajuan Surat Tugas</h1>
                <p>Kelola dan pantau semua pengajuan surat tugas</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" style="font-size:13px">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container-box">
        <div class="top-action d-flex justify-content-between align-items-center">
            <a href="{{ route('dosen.surat.create') }}" class="btn-pengajuan">
                + Ajukan Surat Tugas
            </a>
            <input type="text" id="searchInput" class="search-box" placeholder="Cari data...">
        </div>

        <div class="table-responsive custom-table">
            <table class="table align-middle data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pengusul</th>
                        <th>Waktu Pelaksanaan</th>
                        <th>Lama</th>
                        <th>Perihal</th>
                        <th>Berkas Pendukung</th>
                        <th>Status</th>
                        <th>Surat Tugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($surat as $item)
                        @php
                            $st = strtolower(trim($item->status ?? 'menunggu diproses'));
                            $statusClass = [
                                'menunggu diproses' => 'status-menunggu-diproses',
                                'sedang diverifikasi' => 'status-sedang-diverifikasi',
                                'menunggu persetujuan' => 'status-menunggu-persetujuan',
                                'ditolak (verifikasi)' => 'status-ditolak-verifikasi',
                                'disetujui' => 'status-disetujui',
                                'ditolak (persetujuan)' => 'status-ditolak-persetujuan',
                            ][$st] ?? 'status-menunggu-diproses';
                            
                            $berkasItem = $item->berkasAktif;
                            $namaId = $pegawai->nomor_identitas ?? '-';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div style="font-weight:600;font-size:13px">{{ $pegawai->nama_lengkap ?? '-' }}</div>
                                <small class="text-muted" style="font-size:11px">{{ $namaId }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->waktu_pelaksanaan)->format('d M Y') }}</td>
                            <td><span class="badge-hari">{{ $item->lama_pelaksanaan }} Hari</span></td>
                            <td>{{ $item->perihal }}</td>
                            <td>
                                @if ($berkasItem)
                                    <a href="{{ route('dosen.berkas.view', $berkasItem->id_berkas) }}" target="_blank">📄 {{ $berkasItem->nama_berkas }}</a>
                                @else
                                    <span class="text-muted" style="font-size:12px">Tidak ada file</span>
                                @endif
                            </td>
                            <td><span class="badge-status {{ $statusClass }}">{{ ucwords($st) }}</span></td>
                            <td>
                                @if ($st === 'disetujui')
                                    <a href="{{ url('/download-surat-tugas/' . $item->id_surat_tugas) }}" class="btn-download-pdf">📥 Unduh PDF</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    <button class="btn btn-detail btn-show-detail" 
                                        data-pengusul="{{ $pegawai->nama_lengkap }}" 
                                        data-perihal="{{ $item->perihal }}">Detail</button>
                                    
                                    @if ($st === 'menunggu diproses')
                                        <a href="{{ route('dosen.surat.edit', $item->id_surat_tugas) }}" class="btn btn-perbarui">Perbarui</a>
                                        <a href="{{ route('dosen.surat.destroy', $item->id_surat_tugas) }}" class="btn btn-hapus btn-delete">Hapus</a>
                                    @elseif (str_contains($st, 'ditolak'))
                                        <a href="{{ route('dosen.surat.revisi', $item->id_surat_tugas) }}" class="btn btn-ajukan-kembali">Revisi</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4">Belum ada data pengajuan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Summary Box --}}
        <div class="summary-box">
            <div class="summary-item">
                <div class="icon blue">📋</div>
                <div>
                    <small>Total Pengajuan</small>
                    <h4>{{ $surat->count() }} Surat</h4>
                </div>
            </div>

            <div class="divider"></div>

            <div class="summary-item">
                <div class="icon green">✅</div>
                <div>
                    <small>Disetujui</small>
                    <h4>{{ $surat->where('status', 'disetujui')->count() }} Surat</h4>
                </div>
            </div>

            <div class="divider"></div>

            <div class="summary-item">
                <div class="icon purple">🕐</div>
                <div>
                    <small>Total Hari</small>
                    <h4>{{ $surat->sum('lama_pelaksanaan') }} Hari</h4>
                </div>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="info-box">
            ℹ️ Semua surat tugas menunggu verifikasi dari operator dan disetujui oleh pimpinan.
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cari semua tombol dengan class 'btn-show-detail'
            const detailButtons = document.querySelectorAll('.btn-show-detail');

            detailButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Ambil data dari atribut tombol
                    const pengusul = this.getAttribute('data-pengusul');
                    const perihal = this.getAttribute('data-perihal');

                    // Tampilkan Pop-up menggunakan SweetAlert2
                    Swal.fire({
                        title: 'Detail Surat Tugas',
                        html: `
                            <div style="text-align: left;">
                                <p><strong>Pengusul:</strong> <br>${pengusul}</p>
                                <p><strong>Perihal:</strong> <br>${perihal}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#3085d6'
                    });
                });
            });
        });
    </script>
@endpush