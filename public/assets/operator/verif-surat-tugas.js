// ---- State ----
const PAGE_SIZE_ST = 10;
let antreanAllST      = [];
let antreanFilteredST = [];
let antreanPageST     = 1;
let riwayatAllST      = [];
let riwayatFilteredST = [];
let riwayatPageST     = 1;
let currentIdST        = null;
let _tolakModeActiveST = false;

// ---- Util ----
function fmtTglST(str) {
    if (!str) return '-';
    const d = new Date(str);
    if (isNaN(d)) return str;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ---- API calls ----
async function fetchAntreanST(q = '') {
    try {
        const res = await axios.get('/api/verifikasi/surat-tugas/antrean', { params: { q } });
        antreanAllST = res.data.data;
        antreanAllST.forEach(r => { if (!r.rowStatus) r.rowStatus = 'baru'; });
        antreanFilteredST = [...antreanAllST];
        renderAntreanST();
    } catch (e) {
        console.error('Gagal fetch antrean surat tugas:', e);
    }
}

async function fetchRiwayatST(q = '', status = 'Semua') {
    try {
        const res = await axios.get('/api/verifikasi/surat-tugas/riwayat', { params: { q, status } });
        riwayatAllST      = res.data.data;
        riwayatFilteredST = [...riwayatAllST];
        renderRiwayatST();
    } catch (e) {
        console.error('Gagal fetch riwayat surat tugas:', e);
    }
}

// ---- Render antrean ----
function renderAntreanST() {
    const start = (antreanPageST - 1) * PAGE_SIZE_ST;
    const slice = antreanFilteredST.slice(start, start + PAGE_SIZE_ST);
    document.getElementById('totalAntreanST').textContent   = antreanFilteredST.length;
    document.getElementById('showingAntreanST').textContent =
        antreanFilteredST.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_ST, antreanFilteredST.length)}`;
    const el = document.getElementById('antreanListST');
    if (!slice.length) {
        el.innerHTML = `<div class="antrean-kosong"><i class="bi bi-inbox"></i><p>Tidak ada pengajuan yang ditemukan</p></div>`;
        renderPaginationST('paginationAntreanST', 'pageInfoAntreanST', antreanFilteredST.length, antreanPageST, (p) => { antreanPageST = p; renderAntreanST(); });
        return;
    }
    el.innerHTML = slice.map(r => `
        <div class="antrean-row" id="rowst-${r.id_surat_tugas}">
            <div class="antrean-avatar">${r.inisial}</div>
            <div class="antrean-info">
                <div class="antrean-nama">${r.pengusul}</div>
                <div class="antrean-meta">NIP: ${r.nip}<span class="chip-jabatan">${r.perihal}</span></div>
            </div>
            <div class="antrean-tgl"><i class="bi bi-calendar3"></i> ${fmtTglST(r.tgl_pengajuan)}</div>
            <div class="aksi-cell">
                ${r.rowStatus === 'diterima'
                    ? `<button class="btn-periksa" onclick="bukaPanelST('${r.id_surat_tugas}')"><i class="bi bi-file-earmark-search"></i> Periksa Berkas</button>`
                    : `<button class="btn-terima" onclick="aksiTerimaST('${r.id_surat_tugas}')"><i class="bi bi-check-circle-fill"></i> Terima</button>`
                }
            </div>
        </div>
    `).join('');
    renderPaginationST('paginationAntreanST', 'pageInfoAntreanST', antreanFilteredST.length, antreanPageST, (p) => { antreanPageST = p; renderAntreanST(); });
}

// ---- Render riwayat ----
function renderRiwayatST() {
    const start = (riwayatPageST - 1) * PAGE_SIZE_ST;
    const slice = riwayatFilteredST.slice(start, start + PAGE_SIZE_ST);
    document.getElementById('totalRiwayatST').textContent   = riwayatFilteredST.length;
    document.getElementById('showingRiwayatST').textContent =
        riwayatFilteredST.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_ST, riwayatFilteredST.length)}`;
    const body = document.getElementById('bodyRiwayatST');
    if (!slice.length) {
        body.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat</td></tr>';
        renderPaginationST('paginationRiwayatST', 'pageInfoRiwayatST', riwayatFilteredST.length, riwayatPageST, (p) => { riwayatPageST = p; renderRiwayatST(); });
        return;
    }
    body.innerHTML = slice.map((r, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td style="text-align:left">${r.pengusul}</td>
            <td style="text-align:left">${r.perihal}</td>
            <td>${fmtTglST(r.waktu)}</td>
            <td><span class="badge-status ${r.status_verifikasi === 'Terverifikasi' ? 'badge-ok' : 'badge-tolak'}">
                ${r.status_verifikasi === 'Terverifikasi'
                    ? '<i class="bi bi-send-check"></i> Terverifikasi'
                    : '<i class="bi bi-x-circle"></i> Ditolak'}
            </span></td>
            <td>${fmtTglST(r.tanggal_pengajuan)}</td>
            <td>${fmtTglST(r.tanggal_proses)}</td>
            <td style="text-align:left;max-width:220px;word-break:break-word">${r.keterangan}</td>
        </tr>
    `).join('');
    renderPaginationST('paginationRiwayatST', 'pageInfoRiwayatST', riwayatFilteredST.length, riwayatPageST, (p) => { riwayatPageST = p; renderRiwayatST(); });
}

// ---- Pagination ----
function renderPaginationST(elId, infoId, total, page, onPage) {
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE_ST));
    const info = document.getElementById(infoId);
    if (info) info.textContent = `Halaman ${page} dari ${totalPages}`;
    const el = document.getElementById(elId);
    if (!el) return;
    let html = `<li class="page-item ${page === 1 ? 'disabled' : ''}"><button class="page-link" onclick="(${onPage.toString()})(${page - 1})">&lsaquo;</button></li>`;
    for (let p = 1; p <= totalPages; p++)
        html += `<li class="page-item ${p === page ? 'active' : ''}"><button class="page-link" onclick="(${onPage.toString()})(${p})">${p}</button></li>`;
    html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}"><button class="page-link" onclick="(${onPage.toString()})(${page + 1})">&rsaquo;</button></li>`;
    el.innerHTML = html;
}

// ---- Filter ----
function doFilterAntreanST() {
    const q = document.getElementById('searchAntreanST').value.toLowerCase();
    antreanFilteredST = antreanAllST.filter(r =>
        !q || r.pengusul.toLowerCase().includes(q) || r.perihal.toLowerCase().includes(q));
    antreanPageST = 1; renderAntreanST();
}

function doFilterRiwayatST() {
    const q  = document.getElementById('searchRiwayatST').value.toLowerCase();
    const sv = document.getElementById('statusRiwayatST').value;
    riwayatFilteredST = riwayatAllST.filter(r =>
        (!q || r.pengusul.toLowerCase().includes(q) || r.perihal.toLowerCase().includes(q)) &&
        (sv === 'Semua' || r.status_verifikasi === sv));
    riwayatPageST = 1; renderRiwayatST();
}

// ---- Panel ----
function bukaPanelST(id) {
    const r = antreanAllST.find(x => x.id_surat_tugas === id);
    if (!r) return;
    currentIdST = id;
    _tolakModeActiveST = false;
    document.getElementById('panelTitleST').textContent = r.pengusul.split(',')[0];
    _renderPanelBodyST(r);
    _syncPanelButtonsST();
    document.getElementById('detailOverlayST').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function _renderPanelBodyST(r) {
    const anggotaHtml = r.anggota && r.anggota.length
        ? r.anggota.map(a => `<span class="chip-jabatan">${a}</span>`).join(' ')
        : '<span style="color:#9ca3af;font-size:.85rem">Tidak ada anggota</span>';

    const berkasHtml = r.berkas && r.berkas.length
        ? r.berkas.map(b => `
            <div class="berkas-item">
                <div class="berkas-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                <div class="berkas-detail">
                    <span class="berkas-nama">${b.nama ?? 'Surat Tugas'}</span>
                    <span class="berkas-file">${b.file}</span>
                </div>
                <button class="btn-lihat-berkas" onclick="window.open('/storage/${b.file}','_blank')">
                    <i class="bi bi-eye"></i> Lihat
                </button>
            </div>`).join('')
        : `<p style="color:#9ca3af;font-size:.85rem">Tidak ada berkas diunggah.</p>`;

    document.getElementById('panelBodyST').innerHTML = `
        <div class="panel-section-title">Data Pengusul</div>
        <div class="detail-grid">
            <div class="detail-field"><span class="detail-label">Nama Pengusul</span><span class="detail-val">${r.pengusul}</span></div>
            <div class="detail-field"><span class="detail-label">NIP</span><span class="detail-val">${r.nip}</span></div>
            <div class="detail-field"><span class="detail-label">Jabatan</span><span class="detail-val">${r.jabatan}</span></div>
            <div class="detail-field"><span class="detail-label">Tanggal Pengajuan</span><span class="detail-val">${fmtTglST(r.tgl_pengajuan)}</span></div>
        </div>

        <div class="panel-section-title">Detail Surat Tugas</div>
        <div class="detail-grid">
            <div class="detail-field full"><span class="detail-label">Perihal</span><span class="detail-val highlight">${r.perihal}</span></div>
            <div class="detail-field full"><span class="detail-label">Tujuan</span><span class="detail-val">${r.tujuan}</span></div>
            <div class="detail-field"><span class="detail-label">Waktu Pelaksanaan</span><span class="detail-val">${fmtTglST(r.waktu)}</span></div>
            <div class="detail-field"><span class="detail-label">Lama Pelaksanaan</span><span class="detail-val">${fmtTglST(r.lama)}</span></div>
            <div class="detail-field full"><span class="detail-label">Nomor Surat</span><span class="detail-val">${r.nomor_surat}</span></div>
        </div>

        <div class="panel-section-title">Daftar Anggota</div>
        <div style="padding:4px 0 12px">${anggotaHtml}</div>

        <div class="panel-section-title">Berkas yang Diunggah</div>
        <div class="berkas-list">${berkasHtml}</div>

        <div id="tolakSectionST" style="display:none">
            <div class="tolak-divider"><i class="bi bi-exclamation-triangle-fill"></i> Keterangan Penolakan</div>
            <div class="tolak-block">
                <div class="tolak-block-header" style="margin-bottom:10px">
                    <div class="tolak-block-icon" style="background:#f3f4f6;color:#6b7280">
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>
                    <div>
                        <div class="tolak-block-title">Catatan untuk Pegawai <span class="badge-wajib">Wajib</span></div>
                        <div class="tolak-block-sub">Jelaskan alasan penolakan — akan dikirimkan ke pegawai</div>
                    </div>
                </div>
                <textarea id="catatanOperatorST" class="catatan-area"
                    placeholder="Contoh: Dokumen pendukung tidak lengkap atau tidak sesuai ketentuan..."
                    oninput="_clearCatatanErrorST(this)"></textarea>
                <p class="catatan-hint" id="catatanHintST" style="display:none">
                    <i class="bi bi-exclamation-circle-fill"></i> Catatan wajib diisi sebelum menolak pengajuan.
                </p>
            </div>
        </div>
    `;
}

function _clearCatatanErrorST(el) {
    el.classList.remove('catatan-error');
    const hint = document.getElementById('catatanHintST');
    if (hint) hint.style.display = 'none';
}

function _syncPanelButtonsST() {
    const btnVerif = document.querySelector('#detailPanelST .btn-verifikasi-modal');
    const btnTolak = document.querySelector('#detailPanelST .btn-tolak-modal');
    if (!btnVerif || !btnTolak) return;
    if (_tolakModeActiveST) {
        btnVerif.disabled = true;
        btnVerif.style.opacity = '0.35';
        btnVerif.style.cursor  = 'not-allowed';
        btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Konfirmasi Tolak';
    } else {
        btnVerif.disabled = false;
        btnVerif.style.opacity = '';
        btnVerif.style.cursor  = '';
        btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Tolak';
    }
}

function tutupPanelST() {
    document.getElementById('detailOverlayST').classList.remove('open');
    document.body.style.overflow = '';
    currentIdST = null;
    _tolakModeActiveST = false;
}

// ---- Aksi: Terima ----
function aksiTerimaST(id) {
    const r = antreanAllST.find(x => x.id_surat_tugas === id);
    if (!r) return;
    r.rowStatus = 'diterima';
    renderAntreanST();
}

// ---- Aksi: Verifikasi → POST ke API ----
async function aksiVerifikasiST() {
    if (!currentIdST) return;
    const r = antreanAllST.find(x => x.id_surat_tugas === currentIdST);
    if (!r) return;

    const konfirm = await Swal.fire({
        icon: 'question',
        title: 'Verifikasi Pengajuan?',
        html: `Surat tugas dari <strong>${r.pengusul}</strong> akan diverifikasi dan diteruskan ke pimpinan untuk persetujuan akhir.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
    });
    if (!konfirm.isConfirmed) return;

    try {
        await axios.post(`/api/verifikasi/surat-tugas/${r.id_surat_tugas}/verifikasi`);
        hapusDariAntreanST(currentIdST);
        tutupPanelST();
        await fetchRiwayatST();
        Swal.fire({ icon: 'success', title: 'Terverifikasi!', text: `Surat tugas ${r.pengusul.split(',')[0]} berhasil diverifikasi dan diteruskan ke pimpinan.`, confirmButtonColor: '#198754' });
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Terjadi kesalahan, coba lagi.';
        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
}

// ---- Aksi: Tolak → dua langkah, lalu POST ke API ----
async function aksiTolakPanelST() {
    if (!currentIdST) return;
    const r = antreanAllST.find(x => x.id_surat_tugas === currentIdST);
    if (!r) return;

    // Langkah 1 — tampilkan form + disable Verifikasi (mutex)
    if (!_tolakModeActiveST) {
        _tolakModeActiveST = true;
        const sec = document.getElementById('tolakSectionST');
        if (sec) {
            sec.style.display = 'block';
            setTimeout(() => sec.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
        }
        _syncPanelButtonsST();
        return;
    }

    // Langkah 2 — validasi
    const keterangan = document.getElementById('catatanOperatorST')?.value?.trim() || '';

    if (!keterangan) {
        const ta   = document.getElementById('catatanOperatorST');
        const hint = document.getElementById('catatanHintST');
        ta.classList.add('catatan-error');
        if (hint) hint.style.display = 'flex';
        ta.focus();
        ta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    const konfirm = await Swal.fire({
        icon: 'warning',
        title: 'Tolak Pengajuan?',
        html: `Surat tugas dari <strong>${r.pengusul}</strong> akan ditolak dan pegawai akan diberi tahu.
            <div style="text-align:left;margin-top:10px">
                <strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">Catatan</strong>
                <p style="margin:6px 0 0;font-size:.84rem;color:#374151">${keterangan}</p>
            </div>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
    });
    if (!konfirm.isConfirmed) return;

    try {
        await axios.post(`/api/verifikasi/surat-tugas/${r.id_surat_tugas}/tolak`, { keterangan });
        hapusDariAntreanST(currentIdST);
        tutupPanelST();
        await fetchRiwayatST();
        Swal.fire({ icon: 'info', title: 'Ditolak', text: `Surat tugas ${r.pengusul.split(',')[0]} telah ditolak. Pegawai akan menerima notifikasi.`, confirmButtonColor: '#dc3545' });
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Terjadi kesalahan, coba lagi.';
        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
}

function hapusDariAntreanST(id) {
    antreanAllST      = antreanAllST.filter(x => x.id_surat_tugas !== id);
    antreanFilteredST = antreanFilteredST.filter(x => x.id_surat_tugas !== id);
    renderAntreanST();
}

// ---- Init ----
document.addEventListener('DOMContentLoaded', () => {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document
        .querySelector('meta[name="csrf-token"]')?.content;

    fetchAntreanST();
    fetchRiwayatST();

    document.getElementById('filterAntreanST')?.addEventListener('click', doFilterAntreanST);
    document.getElementById('searchAntreanST')?.addEventListener('keyup', e => { if (e.key === 'Enter') doFilterAntreanST(); });
    document.getElementById('filterRiwayatST')?.addEventListener('click', doFilterRiwayatST);
    document.getElementById('searchRiwayatST')?.addEventListener('keyup', e => { if (e.key === 'Enter') doFilterRiwayatST(); });
    document.getElementById('detailOverlayST')?.addEventListener('click', function (e) {
        if (e.target === this) tutupPanelST();
    });
});