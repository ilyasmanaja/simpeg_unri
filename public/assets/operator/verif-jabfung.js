// ==========================
// verif-jabfung.js — pakai data dari Blade (@json), aksi via form POST
// ==========================

const PAGE_SIZE_JF = 10;
let antreanAllJF      = [];
let antreanFilteredJF = [];
let antreanPageJF     = 1;
let riwayatAllJF      = [];
let riwayatFilteredJF = [];
let riwayatPageJF     = 1;
let currentIdJF        = null;
let _tolakModeActiveJF = false;

function fmtTglJF(str) {
    if (!str) return '-';
    const d = new Date(str);
    if (isNaN(d)) return str;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function inisialDari(nama) {
    if (!nama) return '??';
    return nama.split(' ').slice(0, 2).map(w => w[0] ?? '').join('').toUpperCase();
}

function initDataJF() {
    antreanAllJF = (window.antreanRaw || []).map(v => ({
        id_verifikasi:     v.id_verifikasi,
        nama_lengkap:      v.berkas?.pegawai?.nama_lengkap ?? '-',
        inisial:           inisialDari(v.berkas?.pegawai?.nama_lengkap),
        nip:               v.berkas?.pegawai?.nip ?? '-',
        nidn:              v.berkas?.pegawai?.nidn ?? '-',
        status_pegawai:    v.berkas?.pegawai?.status_pegawai ?? '-',
        jurusan:           v.berkas?.pegawai?.jurusan ?? '-',
        prodi:             v.berkas?.pegawai?.prodi ?? '-',
        jabatan_diajukan:  v.berkas?.jabatan_fungsional?.jenis_jabfung ?? v.berkas?.jabatanFungsional?.jenis_jabfung ?? '-',
        jabfung_sekarang:  v.berkas?.pegawai?.jabatan_fungsional?.jenis_jabfung ?? v.berkas?.pegawai?.jabatanFungsional?.jenis_jabfung ?? '-',
        tanggal_pengajuan: v.tanggal_pengajuan,
        rowStatus:         v.status_verifikasi === 'Sedang Diverifikasi' ? 'diterima' : 'baru',
        berkas: v.berkas ? [{
            id_berkas:    v.berkas.id_berkas,
            nama_berkas:  v.berkas.nama_berkas ?? 'Berkas Pengajuan',
            jenis_berkas: v.berkas.jenis_berkas ?? '-',
            file_path:    v.berkas.file_path ?? null,
            opsional:     false,
        }] : [],
    }));
    antreanFilteredJF = [...antreanAllJF];

    riwayatAllJF = (window.riwayatRaw || []).map(v => ({
        id_verifikasi:     v.id_verifikasi,
        nama_lengkap:      v.berkas?.pegawai?.nama_lengkap ?? '-',
        jabatan_diajukan:  v.berkas?.jabatan_fungsional?.jenis_jabfung ?? v.berkas?.jabatanFungsional?.jenis_jabfung ?? '-',
        status_verifikasi: v.status_verifikasi,
        tanggal_pengajuan: v.tanggal_pengajuan,
        tanggal_proses:    v.tanggal_proses,
        keterangan:        v.keterangan ?? '-',
    }));
    riwayatFilteredJF = [...riwayatAllJF];
}

function renderAntreanJF() {
    const start = (antreanPageJF - 1) * PAGE_SIZE_JF;
    const slice = antreanFilteredJF.slice(start, start + PAGE_SIZE_JF);
    document.getElementById('totalAntreanPG').textContent   = antreanFilteredJF.length;
    document.getElementById('showingAntreanPG').textContent =
        antreanFilteredJF.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_JF, antreanFilteredJF.length)}`;
    const el = document.getElementById('antreanListPG');
    if (!slice.length) {
        el.innerHTML = `<div class="antrean-kosong"><i class="bi bi-inbox"></i><p>Tidak ada pengajuan ditemukan</p></div>`;
        renderPaginationJF('paginationAntreanPG','pageInfoAntreanPG',antreanFilteredJF.length,antreanPageJF,(p)=>{antreanPageJF=p;renderAntreanJF();});
        return;
    }
    el.innerHTML = slice.map(r => `
        <div class="antrean-row" id="row-${r.id_verifikasi}">
            <div class="antrean-avatar">${r.inisial}</div>
            <div class="antrean-info">
                <div class="antrean-nama">${r.nama_lengkap}</div>
                <div class="antrean-meta">NIP: ${r.nip} <span class="chip-jabatan">${r.jabatan_diajukan}</span></div>
            </div>
            <div class="antrean-tgl"><i class="bi bi-calendar3"></i> ${fmtTglJF(r.tanggal_pengajuan)}</div>
            <div class="aksi-cell">
                ${r.rowStatus === 'diterima'
                    ? `<button class="btn-periksa" onclick="bukaPanelJF('${r.id_verifikasi}')">
                           <i class="bi bi-file-earmark-search"></i> Periksa Berkas
                       </button>`
                    : `<form method="POST" action="/operator/verifikasi/${r.id_verifikasi}/terima" style="display:inline">
                           <input type="hidden" name="_token" value="${window.csrfToken}">
                           <button type="submit" class="btn-terima">
                               <i class="bi bi-check-circle-fill"></i> Terima
                           </button>
                       </form>`
                }
            </div>
        </div>
    `).join('');
    renderPaginationJF('paginationAntreanPG','pageInfoAntreanPG',antreanFilteredJF.length,antreanPageJF,(p)=>{antreanPageJF=p;renderAntreanJF();});
}

function renderRiwayatJF() {
    const start = (riwayatPageJF - 1) * PAGE_SIZE_JF;
    const slice = riwayatFilteredJF.slice(start, start + PAGE_SIZE_JF);
    document.getElementById('totalRiwayatPG').textContent   = riwayatFilteredJF.length;
    document.getElementById('showingRiwayatPG').textContent =
        riwayatFilteredJF.length === 0 ? '0' : `${start + 1}–${Math.min(start + PAGE_SIZE_JF, riwayatFilteredJF.length)}`;
    const body = document.getElementById('bodyRiwayatPG');
    if (!slice.length) {
        body.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat</td></tr>';
        renderPaginationJF('paginationRiwayatPG','pageInfoRiwayatPG',riwayatFilteredJF.length,riwayatPageJF,(p)=>{riwayatPageJF=p;renderRiwayatJF();});
        return;
    }
    body.innerHTML = slice.map((r, i) => `
        <tr>
            <td>${start+i+1}</td>
            <td style="text-align:left">${r.nama_lengkap}</td>
            <td>${r.jabatan_diajukan}</td>
            <td><span class="badge-status ${r.status_verifikasi==='Diteruskan'?'badge-ok':'badge-tolak'}">
                ${r.status_verifikasi==='Diteruskan'
                    ? '<i class="bi bi-send-check"></i> Diteruskan'
                    : '<i class="bi bi-x-circle"></i> Ditolak'}
            </span></td>
            <td>${fmtTglJF(r.tanggal_pengajuan)}</td>
            <td>${fmtTglJF(r.tanggal_proses)}</td>
            <td style="text-align:left;max-width:220px;word-break:break-word">${r.keterangan}</td>
        </tr>`).join('');
    renderPaginationJF('paginationRiwayatPG','pageInfoRiwayatPG',riwayatFilteredJF.length,riwayatPageJF,(p)=>{riwayatPageJF=p;renderRiwayatJF();});
}

function renderPaginationJF(elId, infoId, total, page, onPage) {
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE_JF));
    const info = document.getElementById(infoId);
    if (info) info.textContent = `Halaman ${page} dari ${totalPages}`;
    const el = document.getElementById(elId);
    if (!el) return;
    let html = `<li class="page-item ${page===1?'disabled':''}"><button class="page-link" onclick="(${onPage.toString()})(${page-1})">&lsaquo;</button></li>`;
    for (let p=1;p<=totalPages;p++)
        html += `<li class="page-item ${p===page?'active':''}"><button class="page-link" onclick="(${onPage.toString()})(${p})">${p}</button></li>`;
    html += `<li class="page-item ${page===totalPages?'disabled':''}"><button class="page-link" onclick="(${onPage.toString()})(${page+1})">&rsaquo;</button></li>`;
    el.innerHTML = html;
}

function doFilterAntreanPG() {
    const q = document.getElementById('searchAntreanPG').value.toLowerCase();
    antreanFilteredJF = antreanAllJF.filter(r =>
        !q || r.nama_lengkap.toLowerCase().includes(q) || r.jabatan_diajukan.toLowerCase().includes(q));
    antreanPageJF = 1; renderAntreanJF();
}

function doFilterRiwayatPG() {
    const q  = document.getElementById('searchRiwayatPG').value.toLowerCase();
    const sv = document.getElementById('statusRiwayatPG').value;
    riwayatFilteredJF = riwayatAllJF.filter(r =>
        (!q || r.nama_lengkap.toLowerCase().includes(q) || r.jabatan_diajukan.toLowerCase().includes(q)) &&
        (sv === 'Semua' || r.status_verifikasi === sv));
    riwayatPageJF = 1; renderRiwayatJF();
}

function bukaPanelJF(id_verifikasi) {
    const r = antreanAllJF.find(x => x.id_verifikasi === id_verifikasi);
    if (!r) return;
    currentIdJF = id_verifikasi;
    _tolakModeActiveJF = false;
    document.getElementById('panelTitlePG').textContent = r.nama_lengkap;
    _renderPanelBodyJF(r);
    _syncPanelButtonsJF();
    document.getElementById('detailOverlayPG').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function _renderPanelBodyJF(r) {
    const berkasList = r.berkas.map((b, idx) => `
        <div class="berkas-item" id="berkas-item-${idx}">
            <div class="berkas-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div class="berkas-detail">
                <span class="berkas-nama">${b.nama_berkas}</span>
                <span class="berkas-file">${b.file_path ?? '-'}</span>
            </div>
            ${b.file_path
                ? `<a href="/storage/${b.file_path}" target="_blank" class="btn-lihat-berkas"><i class="bi bi-eye"></i> Lihat</a>`
                : `<span class="btn-lihat-berkas" style="opacity:.4;cursor:default"><i class="bi bi-eye-slash"></i> Tidak ada</span>`
            }
        </div>`).join('');

    const berkasCheckItems = r.berkas.map(b => `
        <label class="tolak-check-item">
            <input type="checkbox" class="berkas-checkbox tolak-checkbox" value="${b.nama_berkas}">
            <span class="tolak-check-label">${b.nama_berkas}</span>
        </label>`).join('');

    document.getElementById('panelBodyPG').innerHTML = `
        <div class="panel-section-title">Data Pegawai</div>
        <div class="detail-grid">
            <div class="detail-field"><span class="detail-label">Nama Lengkap</span><span class="detail-val">${r.nama_lengkap}</span></div>
            <div class="detail-field"><span class="detail-label">NIP</span><span class="detail-val">${r.nip}</span></div>
            <div class="detail-field"><span class="detail-label">NIDN</span><span class="detail-val">${r.nidn}</span></div>
            <div class="detail-field"><span class="detail-label">Status Pegawai</span><span class="detail-val">${r.status_pegawai}</span></div>
            <div class="detail-field"><span class="detail-label">Jurusan</span><span class="detail-val">${r.jurusan}</span></div>
            <div class="detail-field"><span class="detail-label">Program Studi</span><span class="detail-val">${r.prodi}</span></div>
        </div>
        <div class="panel-section-title">Detail Pengajuan Jabatan Fungsional</div>
        <div class="detail-grid">
            <div class="detail-field"><span class="detail-label">Jabatan Saat Ini</span><span class="detail-val">${r.jabfung_sekarang}</span></div>
            <div class="detail-field"><span class="detail-label">Jabatan Diajukan</span><span class="detail-val highlight">${r.jabatan_diajukan}</span></div>
            <div class="detail-field"><span class="detail-label">Tanggal Pengajuan</span><span class="detail-val">${fmtTglJF(r.tanggal_pengajuan)}</span></div>
        </div>
        <div class="panel-section-title">Berkas yang Diunggah</div>
        <div class="berkas-list">${berkasList}</div>
        <div id="tolakSectionJF" style="display:none">
            <div class="tolak-divider"><i class="bi bi-exclamation-triangle-fill"></i> Keterangan Penolakan</div>
            <div class="tolak-block">
                <div class="tolak-block-header">
                    <div class="tolak-block-icon tolak-icon-berkas"><i class="bi bi-file-earmark-x-fill"></i></div>
                    <div style="flex:1"><div class="tolak-block-title">Berkas Bermasalah</div><div class="tolak-block-sub">Opsional</div></div>
                    <button class="btn-select-all" onclick="toggleSemuaBerkasJF(this)" type="button">Pilih Semua</button>
                </div>
                <div class="tolak-checklist">${berkasCheckItems}</div>
            </div>
            <div class="tolak-block" style="margin-top:10px">
                <div class="tolak-block-header" style="margin-bottom:10px">
                    <div class="tolak-block-icon" style="background:#f3f4f6;color:#6b7280"><i class="bi bi-chat-left-text-fill"></i></div>
                    <div>
                        <div class="tolak-block-title">Catatan untuk Pegawai <span class="badge-wajib">Wajib</span></div>
                        <div class="tolak-block-sub">Jelaskan alasan penolakan</div>
                    </div>
                </div>
                <textarea id="catatanOperatorJF" class="catatan-area"
                    placeholder="Contoh: Berkas tidak sesuai ketentuan..."
                    oninput="_clearCatatanErrorJF(this)"></textarea>
                <p class="catatan-hint" id="catatanHintJF" style="display:none">
                    <i class="bi bi-exclamation-circle-fill"></i> Catatan wajib diisi.
                </p>
            </div>
        </div>`;
}

function _clearCatatanErrorJF(el) {
    el.classList.remove('catatan-error');
    const hint = document.getElementById('catatanHintJF');
    if (hint) hint.style.display = 'none';
}

function toggleSemuaBerkasJF(btn) {
    const boxes = document.querySelectorAll('.berkas-checkbox');
    const allChecked = Array.from(boxes).every(c => c.checked);
    boxes.forEach(c => { c.checked = !allChecked; });
    btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih';
}

function _syncPanelButtonsJF() {
    const btnVerif = document.getElementById('btnVerifikasiPG');
    const btnTolak = document.getElementById('btnTolakPanelPG');
    if (!btnVerif || !btnTolak) return;
    if (_tolakModeActiveJF) {
        btnVerif.disabled = true; btnVerif.style.opacity = '0.35'; btnVerif.style.cursor = 'not-allowed';
        btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Konfirmasi Tolak';
    } else {
        btnVerif.disabled = false; btnVerif.style.opacity = ''; btnVerif.style.cursor = '';
        btnTolak.innerHTML = '<i class="bi bi-x-circle-fill"></i> Tolak';
    }
}

function tutupPanelPG() {
    document.getElementById('detailOverlayPG').classList.remove('open');
    document.body.style.overflow = '';
    currentIdJF = null; _tolakModeActiveJF = false;
}

function submitForm(action, extraInputs = '') {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.innerHTML = `<input type="hidden" name="_token" value="${window.csrfToken}">${extraInputs}`;
    document.body.appendChild(form);
    form.submit();
}

function aksiVerifikasiPG() {
    if (!currentIdJF) return;
    const r = antreanAllJF.find(x => x.id_verifikasi === currentIdJF);
    if (!r) return;
    Swal.fire({
        icon: 'question', title: 'Verifikasi Pengajuan?',
        html: `Pengajuan jabatan fungsional <strong>${r.nama_lengkap}</strong> akan diteruskan ke pimpinan.`,
        showCancelButton: true, confirmButtonText: 'Ya, Verifikasi', cancelButtonText: 'Batal',
        confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
    }).then(res => {
        if (!res.isConfirmed) return;
        submitForm(`/operator/verifikasi/${r.id_verifikasi}/verifikasi`);
    });
}

function aksiTolakPanelPG() {
    if (!currentIdJF) return;
    const r = antreanAllJF.find(x => x.id_verifikasi === currentIdJF);
    if (!r) return;

    if (!_tolakModeActiveJF) {
        _tolakModeActiveJF = true;
        const sec = document.getElementById('tolakSectionJF');
        if (sec) { sec.style.display = 'block'; setTimeout(() => sec.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80); }
        _syncPanelButtonsJF();
        return;
    }

    const keterangan = document.getElementById('catatanOperatorJF')?.value?.trim() || '';
    if (!keterangan) {
        const ta = document.getElementById('catatanOperatorJF');
        const hint = document.getElementById('catatanHintJF');
        ta.classList.add('catatan-error');
        if (hint) hint.style.display = 'flex';
        ta.focus(); ta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    const berkasBermasalah = Array.from(document.querySelectorAll('.berkas-checkbox:checked')).map(el => el.value);
    let htmlDetail = '';
    if (berkasBermasalah.length > 0) {
        htmlDetail += `<div style="text-align:left;margin-top:10px"><strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">Berkas bermasalah</strong><ul style="margin:6px 0 0;padding-left:18px;font-size:.84rem;color:#374151">${berkasBermasalah.map(n=>`<li>${n}</li>`).join('')}</ul></div>`;
    }
    htmlDetail += `<div style="text-align:left;margin-top:10px"><strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">Catatan</strong><p style="margin:6px 0 0;font-size:.84rem;color:#374151">${keterangan}</p></div>`;

    Swal.fire({
        icon: 'warning', title: 'Tolak Pengajuan?',
        html: `Pengajuan <strong>${r.nama_lengkap}</strong> akan ditolak.${htmlDetail}`,
        showCancelButton: true, confirmButtonText: 'Ya, Tolak', cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
    }).then(res => {
        if (!res.isConfirmed) return;
        let extraInputs = `<input type="hidden" name="keterangan" value="${keterangan.replace(/"/g,'&quot;')}">`;
        berkasBermasalah.forEach(b => { extraInputs += `<input type="hidden" name="berkas_bermasalah[]" value="${b}">`; });
        submitForm(`/operator/verifikasi/${r.id_verifikasi}/tolak`, extraInputs);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initDataJF();
    renderAntreanJF();
    renderRiwayatJF();
    document.getElementById('filterAntreanPG')?.addEventListener('click', doFilterAntreanPG);
    document.getElementById('searchAntreanPG')?.addEventListener('keyup', e => { if (e.key==='Enter') doFilterAntreanPG(); });
    document.getElementById('filterRiwayatPG')?.addEventListener('click', doFilterRiwayatPG);
    document.getElementById('searchRiwayatPG')?.addEventListener('keyup', e => { if (e.key==='Enter') doFilterRiwayatPG(); });
    document.getElementById('detailOverlayPG')?.addEventListener('click', function(e) { if (e.target===this) tutupPanelPG(); });
});