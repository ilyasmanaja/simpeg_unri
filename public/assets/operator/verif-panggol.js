// ---- State ----
const PAGE_SIZE_PG = 10;
let antreanAllPG      = [];
let antreanFilteredPG = [];
let antreanPagePG     = 1;
let riwayatAllPG      = [];
let riwayatFilteredPG = [];
let riwayatPagePG     = 1;
let currentIdPG        = null;
let _tolakModeActivePG = false;

// ---- Util ----
function fmtTglPG(str) {
    if (!str) return '-';
    const d = new Date(str);
    if (isNaN(d)) return str;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ---- API calls ----
async function fetchAntreanPG(q = '') {
    try {
        const res = await axios.get('/api/verifikasi/pangkat/antrean', { params: { q } });
        antreanAllPG = res.data.data;
        antreanAllPG.forEach(r => { if (!r.rowStatus) r.rowStatus = 'baru'; });
        antreanFilteredPG = [...antreanAllPG];
        renderAntreanPG();
    } catch (e) {
        console.error('Gagal fetch antrean pangkat:', e);
    }
}

async function fetchRiwayatPG(q = '', status = 'Semua') {
    try {
        const res = await axios.get('/api/verifikasi/pangkat/riwayat', { params: { q, status } });
        riwayatAllPG      = res.data.data;
        riwayatFilteredPG = [...riwayatAllPG];
        renderRiwayatPG();
    } catch (e) {
        console.error('Gagal fetch riwayat pangkat:', e);
    }
}

// ---- Render antrean ----
function renderAntreanPG() {
    const start = (antreanPagePG - 1) * PAGE_SIZE_PG;
    const slice = antreanFilteredPG.slice(start, start + PAGE_SIZE_PG);
    document.getElementById('totalAntreanPG').textContent   = antreanFilteredPG.length;
    document.getElementById('showingAntreanPG').textContent =
        antreanFilteredPG.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_PG, antreanFilteredPG.length)}`;
    const el = document.getElementById('antreanListPG');
    if (!slice.length) {
        el.innerHTML = `<div class="antrean-kosong"><i class="bi bi-inbox"></i><p>Tidak ada pengajuan yang ditemukan</p></div>`;
        renderPaginationPG('paginationAntreanPG', 'pageInfoAntreanPG', antreanFilteredPG.length, antreanPagePG, (p) => { antreanPagePG = p; renderAntreanPG(); });
        return;
    }
    el.innerHTML = slice.map(r => `
        <div class="antrean-row" id="rowpg-${r.id_pengajuan}">
            <div class="antrean-avatar">${r.inisial}</div>
            <div class="antrean-info">
                <div class="antrean-nama">${r.nama}</div>
                <div class="antrean-meta">NIP: ${r.nip}<span class="chip-jabatan">${r.pangkat_diajukan}</span></div>
            </div>
            <div class="antrean-tgl"><i class="bi bi-calendar3"></i> ${fmtTglPG(r.tanggal_pengajuan)}</div>
            <div class="aksi-cell">
                ${r.rowStatus === 'diterima'
                    ? `<button class="btn-periksa" onclick="bukaPanelPG(${r.id_pengajuan})"><i class="bi bi-file-earmark-search"></i> Periksa Berkas</button>`
                    : `<button class="btn-terima" onclick="aksiTerimaPG(${r.id_pengajuan})"><i class="bi bi-check-circle-fill"></i> Terima</button>`
                }
            </div>
        </div>
    `).join('');
    renderPaginationPG('paginationAntreanPG', 'pageInfoAntreanPG', antreanFilteredPG.length, antreanPagePG, (p) => { antreanPagePG = p; renderAntreanPG(); });
}

// ---- Render riwayat ----
function renderRiwayatPG() {
    const start = (riwayatPagePG - 1) * PAGE_SIZE_PG;
    const slice = riwayatFilteredPG.slice(start, start + PAGE_SIZE_PG);
    document.getElementById('totalRiwayatPG').textContent   = riwayatFilteredPG.length;
    document.getElementById('showingRiwayatPG').textContent =
        riwayatFilteredPG.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_PG, riwayatFilteredPG.length)}`;
    const body = document.getElementById('bodyRiwayatPG');
    if (!slice.length) {
        body.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat</td></tr>';
        renderPaginationPG('paginationRiwayatPG', 'pageInfoRiwayatPG', riwayatFilteredPG.length, riwayatPagePG, (p) => { riwayatPagePG = p; renderRiwayatPG(); });
        return;
    }
    body.innerHTML = slice.map((r, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td style="text-align:left">${r.nama}</td>
            <td>${r.pangkat_diajukan}</td>
            <td><span class="badge-status ${r.status_verifikasi === 'Diteruskan' ? 'badge-ok' : 'badge-tolak'}">
                ${r.status_verifikasi === 'Diteruskan'
                    ? '<i class="bi bi-send-check"></i> Diteruskan'
                    : '<i class="bi bi-x-circle"></i> Ditolak'}
            </span></td>
            <td>${fmtTglPG(r.tanggal_pengajuan)}</td>
            <td>${fmtTglPG(r.tanggal_proses)}</td>
            <td style="text-align:left;max-width:220px;word-break:break-word">${r.keterangan}</td>
        </tr>
    `).join('');
    renderPaginationPG('paginationRiwayatPG', 'pageInfoRiwayatPG', riwayatFilteredPG.length, riwayatPagePG, (p) => { riwayatPagePG = p; renderRiwayatPG(); });
}

// ---- Pagination ----
function renderPaginationPG(elId, infoId, total, page, onPage) {
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE_PG));
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
function doFilterAntreanPG() {
    const q = document.getElementById('searchAntreanPG').value.toLowerCase();
    antreanFilteredPG = antreanAllPG.filter(r =>
        !q || r.nama.toLowerCase().includes(q) || r.pangkat_diajukan.toLowerCase().includes(q));
    antreanPagePG = 1; renderAntreanPG();
}

function doFilterRiwayatPG() {
    const q  = document.getElementById('searchRiwayatPG').value.toLowerCase();
    const sv = document.getElementById('statusRiwayatPG').value;
    riwayatFilteredPG = riwayatAllPG.filter(r =>
        (!q || r.nama.toLowerCase().includes(q) || r.pangkat_diajukan.toLowerCase().includes(q)) &&
        (sv === 'Semua' || r.status_verifikasi === sv));
    riwayatPagePG = 1; renderRiwayatPG();
}

// ---- Panel ----
function bukaPanelPG(id) {
    const r = antreanAllPG.find(x => x.id_pengajuan === id);
    if (!r) return;
    currentIdPG = id;
    _tolakModeActivePG = false;
    document.getElementById('panelTitlePG').textContent = r.nama;
    _renderPanelBodyPG(r);
    _syncPanelButtonsPG();
    document.getElementById('detailOverlayPG').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function _renderPanelBodyPG(r) {
    const berkasList = r.berkas.map((b, idx) => `
        <div class="berkas-item" id="berkaspg-item-${idx}">
            <div class="berkas-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div class="berkas-detail">
                <span class="berkas-nama">${b.nama}${b.opsional ? '<span class="chip-opsional">Opsional</span>' : ''}</span>
                <span class="berkas-file">${b.file}</span>
            </div>
            <button class="btn-lihat-berkas" onclick="window.open('/storage/${b.file}','_blank')">
                <i class="bi bi-eye"></i> Lihat
            </button>
        </div>
    `).join('');

    const berkasCheckItems = r.berkas.map(b => `
        <label class="tolak-check-item">
            <input type="checkbox" class="berkas-checkbox-pg tolak-checkbox" value="${b.nama}">
            <span class="tolak-check-label">${b.nama}${b.opsional ? '<em class="chip-opsional-sm">opsional</em>' : ''}</span>
        </label>
    `).join('');

    document.getElementById('panelBodyPG').innerHTML = `
        <div class="panel-section-title">Data Pegawai</div>
        <div class="detail-grid">
            <div class="detail-field"><span class="detail-label">Nama Lengkap</span><span class="detail-val">${r.nama}</span></div>
            <div class="detail-field"><span class="detail-label">NIP</span><span class="detail-val">${r.nip}</span></div>
            <div class="detail-field"><span class="detail-label">NIDN</span><span class="detail-val">${r.nidn}</span></div>
            <div class="detail-field"><span class="detail-label">Status Pegawai</span><span class="detail-val">${r.status_pegawai}</span></div>
            <div class="detail-field"><span class="detail-label">Jurusan</span><span class="detail-val">${r.jurusan}</span></div>
            <div class="detail-field"><span class="detail-label">Program Studi</span><span class="detail-val">${r.prodi}</span></div>
        </div>

        <div class="panel-section-title">Detail Pengajuan Pangkat / Golongan</div>
        <div class="detail-grid">
            <div class="detail-field"><span class="detail-label">Pangkat / Golongan Diajukan</span><span class="detail-val highlight">${r.pangkat_diajukan}</span></div>
            <div class="detail-field"><span class="detail-label">Jenis Pengajuan</span><span class="detail-val">${r.jenis_pengajuan}</span></div>
            <div class="detail-field"><span class="detail-label">Tanggal Pengajuan</span><span class="detail-val">${fmtTglPG(r.tanggal_pengajuan)}</span></div>
            <div class="detail-field full"><span class="detail-label">Nomor SK / Usulan</span><span class="detail-val">${r.nomor_sk}</span></div>
        </div>

        <div class="panel-section-title">Berkas yang Diunggah</div>
        <div class="berkas-list">${berkasList}</div>

        <div id="tolakSectionPG" style="display:none">
            <div class="tolak-divider"><i class="bi bi-exclamation-triangle-fill"></i> Keterangan Penolakan</div>
            <div class="tolak-block">
                <div class="tolak-block-header">
                    <div class="tolak-block-icon tolak-icon-berkas"><i class="bi bi-file-earmark-x-fill"></i></div>
                    <div style="flex:1">
                        <div class="tolak-block-title">Berkas Bermasalah</div>
                        <div class="tolak-block-sub">Opsional — centang berkas yang perlu diperbaiki pegawai</div>
                    </div>
                    <button class="btn-select-all" onclick="toggleSemuaBerkasPG(this)" type="button">Pilih Semua</button>
                </div>
                <div class="tolak-checklist">${berkasCheckItems}</div>
            </div>
            <div class="tolak-block" style="margin-top:10px">
                <div class="tolak-block-header" style="margin-bottom:10px">
                    <div class="tolak-block-icon" style="background:#f3f4f6;color:#6b7280"><i class="bi bi-chat-left-text-fill"></i></div>
                    <div>
                        <div class="tolak-block-title">Catatan untuk Pegawai <span class="badge-wajib">Wajib</span></div>
                        <div class="tolak-block-sub">Jelaskan alasan penolakan — akan dikirimkan ke pegawai</div>
                    </div>
                </div>
                <textarea id="catatanOperatorPG" class="catatan-area"
                    placeholder="Contoh: Berkas tidak sesuai dengan ketentuan yang berlaku..."
                    oninput="_clearCatatanErrorPG(this)"></textarea>
                <p class="catatan-hint" id="catatanHintPG" style="display:none">
                    <i class="bi bi-exclamation-circle-fill"></i> Catatan wajib diisi sebelum menolak pengajuan.
                </p>
            </div>
        </div>
    `;
}

function _clearCatatanErrorPG(el) {
    el.classList.remove('catatan-error');
    const hint = document.getElementById('catatanHintPG');
    if (hint) hint.style.display = 'none';
}

function toggleSemuaBerkasPG(btn) {
    const boxes = document.querySelectorAll('.berkas-checkbox-pg');
    const allChecked = Array.from(boxes).every(c => c.checked);
    boxes.forEach(c => { c.checked = !allChecked; });
    btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih';
}

function _syncPanelButtonsPG() {
    const btnVerif = document.getElementById('btnVerifikasiPG');
    const btnTolak = document.getElementById('btnTolakPanelPG');
    if (!btnVerif || !btnTolak) return;
    if (_tolakModeActivePG) {
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

function tutupPanelPG() {
    document.getElementById('detailOverlayPG').classList.remove('open');
    document.body.style.overflow = '';
    currentIdPG = null;
    _tolakModeActivePG = false;
}

// ---- Aksi: Terima ----
function aksiTerimaPG(id) {
    const r = antreanAllPG.find(x => x.id_pengajuan === id);
    if (!r) return;
    r.rowStatus = 'diterima';
    renderAntreanPG();
}

// ---- Aksi: Verifikasi → POST ke API ----
async function aksiVerifikasiPG() {
    if (!currentIdPG) return;
    const r = antreanAllPG.find(x => x.id_pengajuan === currentIdPG);
    if (!r) return;

    const konfirm = await Swal.fire({
        icon: 'question',
        title: 'Verifikasi Pengajuan?',
        html: `Pengajuan pangkat/golongan <strong>${r.nama}</strong> akan diteruskan ke pimpinan untuk persetujuan akhir.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Verifikasi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
    });
    if (!konfirm.isConfirmed) return;

    try {
        await axios.post(`/api/verifikasi/pangkat/${r.id_pengajuan}/verifikasi`);
        hapusDariAntreanPG(currentIdPG);
        tutupPanelPG();
        await fetchRiwayatPG();
        Swal.fire({ icon: 'success', title: 'Terverifikasi!', text: `Pengajuan ${r.nama} berhasil diteruskan ke pimpinan.`, confirmButtonColor: '#198754' });
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Terjadi kesalahan, coba lagi.';
        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
}

// ---- Aksi: Tolak → dua langkah, lalu POST ke API ----
async function aksiTolakPanelPG() {
    if (!currentIdPG) return;
    const r = antreanAllPG.find(x => x.id_pengajuan === currentIdPG);
    if (!r) return;

    // Langkah 1 — tampilkan form + disable Verifikasi (mutex)
    if (!_tolakModeActivePG) {
        _tolakModeActivePG = true;
        const sec = document.getElementById('tolakSectionPG');
        if (sec) {
            sec.style.display = 'block';
            setTimeout(() => sec.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
        }
        _syncPanelButtonsPG();
        return;
    }

    // Langkah 2 — validasi
    const berkas_bermasalah = Array.from(document.querySelectorAll('.berkas-checkbox-pg:checked')).map(el => el.value);
    const keterangan        = document.getElementById('catatanOperatorPG')?.value?.trim() || '';

    if (!keterangan) {
        const ta   = document.getElementById('catatanOperatorPG');
        const hint = document.getElementById('catatanHintPG');
        ta.classList.add('catatan-error');
        if (hint) hint.style.display = 'flex';
        ta.focus();
        ta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    let htmlDetail = '';
    if (berkas_bermasalah.length > 0) {
        htmlDetail += `<div style="text-align:left;margin-top:10px">
            <strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">Berkas bermasalah</strong>
            <ul style="margin:6px 0 0;padding-left:18px;font-size:.84rem;color:#374151">
                ${berkas_bermasalah.map(n => `<li>${n}</li>`).join('')}
            </ul></div>`;
    }
    htmlDetail += `<div style="text-align:left;margin-top:10px">
        <strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">Catatan</strong>
        <p style="margin:6px 0 0;font-size:.84rem;color:#374151">${keterangan}</p></div>`;

    const konfirm = await Swal.fire({
        icon: 'warning',
        title: 'Tolak Pengajuan?',
        html: `Pengajuan <strong>${r.nama}</strong> akan ditolak dan pegawai akan diberi tahu.${htmlDetail}`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
    });
    if (!konfirm.isConfirmed) return;

    try {
        await axios.post(`/api/verifikasi/pangkat/${r.id_pengajuan}/tolak`, {
            keterangan,
            berkas_bermasalah,
        });
        hapusDariAntreanPG(currentIdPG);
        tutupPanelPG();
        await fetchRiwayatPG();
        Swal.fire({ icon: 'info', title: 'Ditolak', text: `Pengajuan ${r.nama} telah ditolak. Pegawai akan menerima notifikasi.`, confirmButtonColor: '#dc3545' });
    } catch (e) {
        const msg = e.response?.data?.message ?? 'Terjadi kesalahan, coba lagi.';
        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
    }
}

function hapusDariAntreanPG(id) {
    antreanAllPG      = antreanAllPG.filter(x => x.id_pengajuan !== id);
    antreanFilteredPG = antreanFilteredPG.filter(x => x.id_pengajuan !== id);
    renderAntreanPG();
}

// ---- Init ----
document.addEventListener('DOMContentLoaded', () => {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document
        .querySelector('meta[name="csrf-token"]')?.content;

    fetchAntreanPG();
    fetchRiwayatPG();

    document.getElementById('filterAntreanPG')?.addEventListener('click', doFilterAntreanPG);
    document.getElementById('searchAntreanPG')?.addEventListener('keyup', e => { if (e.key === 'Enter') doFilterAntreanPG(); });
    document.getElementById('filterRiwayatPG')?.addEventListener('click', doFilterRiwayatPG);
    document.getElementById('searchRiwayatPG')?.addEventListener('keyup', e => { if (e.key === 'Enter') doFilterRiwayatPG(); });
    document.getElementById('detailOverlayPG')?.addEventListener('click', function (e) {
        if (e.target === this) tutupPanelPG();
    });
});