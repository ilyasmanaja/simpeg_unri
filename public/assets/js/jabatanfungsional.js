/**
 * jabatanfungsional.js
 * public/assets/js/jabatanfungsional.js
 */

/* ============================================================
   READ PAGE — Detail inline
   ============================================================ */

function bukaDetail(id) {
    const data = (window.PENGAJUAN_DATA || []).find(p => p.id == id);
    if (!data) return;

    const panel = document.getElementById('panelDetail');
    if (!panel) return;

    // Header
    document.getElementById('dp-title').textContent    = 'Detail Pengajuan Jabatan Fungsional';
    document.getElementById('dp-subtitle').textContent = `ID #${data.id} · ${window.PEGAWAI_NAMA}`;

    // Info rows
    document.getElementById('dp-nama').textContent          = window.PEGAWAI_NAMA;
    document.getElementById('dp-id-label').textContent      = window.PEGAWAI_ID_LABEL;
    document.getElementById('dp-id-value').textContent      = window.PEGAWAI_ID_VALUE;
    document.getElementById('dp-jabfung-now').textContent   = window.JABFUNG_NOW;
    document.getElementById('dp-jabfung-target').textContent = data.jabfung_target;
    document.getElementById('dp-nomor').textContent         = data.nomor_usulan || '—';
    document.getElementById('dp-tanggal').textContent       = data.tanggal || '—';

    // Status badge
    document.getElementById('dp-status-badge').innerHTML =
        `<span class="status-badge ${data.status_class}">
            <i class="bi ${data.status_icon}"></i> ${data.status_label}
        </span>`;

    // Status banner
    const bannerEl = document.getElementById('dp-status-banner');
    let bannerClass = 'proses', bannerIcon = 'bi-hourglass-split';
    let bannerText  = `Sedang Diproses — ${data.status_label}`;

    if (['tolak_verifikasi', 'tolak_persetujuan'].includes(data.status)) {
        bannerClass = 'tolak';
        bannerIcon  = 'bi-x-circle-fill';
        bannerText  = `<strong>${data.status_label}</strong>${data.keterangan ? '<br>Catatan: ' + data.keterangan : ''}`;
    } else if (data.status === 'disetujui') {
        bannerClass = 'setuju';
        bannerIcon  = 'bi-check-circle-fill';
        bannerText  = '<strong>Pengajuan Disetujui</strong>';
    }
    bannerEl.className = `status-banner ${bannerClass} d-flex gap-2`;
    bannerEl.innerHTML = `<i class="bi ${bannerIcon} flex-shrink-0 mt-1"></i><div>${bannerText}</div>`;

    // Berkas bermasalah
    const bermasalahWrap = document.getElementById('dp-bermasalah-wrap');
    const bermasalahList = document.getElementById('dp-bermasalah-list');
    const labelBerkas    = { sk_cpns: 'SK CPNS', sk_pns: 'SK PNS', pak: 'PAK', publikasi: 'Publikasi' };

    if (data.berkas_bermasalah && data.berkas_bermasalah.length > 0) {
        bermasalahWrap.style.display = '';
        bermasalahList.innerHTML = data.berkas_bermasalah
            .map(k => `<span class="ar-chip"><i class="bi bi-file-earmark-pdf-fill me-1"></i>${labelBerkas[k] || k}</span>`)
            .join('');
    } else {
        bermasalahWrap.style.display = 'none';
    }

    // Dosen ladder (hanya jika ada di DOM)
    const ladderWrap = document.getElementById('dp-ladder-wrap');
    const ladderEl   = document.getElementById('dp-ladder');
    if (ladderWrap && ladderEl) {
        if (window.PEGAWAI_NIDN && window.DOSEN_LADDER && window.DOSEN_LADDER.length) {
            ladderWrap.style.display = '';
            ladderEl.innerHTML = window.DOSEN_LADDER.map(l => {
                const u   = parseInt(l.urutan);
                const tu  = parseInt(data.jabfung_urutan);
                const cls = u < tu ? 'done' : (u === tu ? 'target' : '');
                const sub = u < tu
                    ? '<span class="ld-sub"><i class="bi bi-check2"></i> Lalu</span>'
                    : u === tu ? '<span class="ld-sub">Diajukan</span>' : '';
                return `<div class="ld-step ${cls}">${l.nama}${sub}</div>`;
            }).join('');
        } else {
            ladderWrap.style.display = 'none';
        }
    }

    // Grid berkas
    const berkasGrid = document.getElementById('dp-berkas-grid');
    berkasGrid.innerHTML = ['sk_cpns', 'sk_pns', 'pak', 'publikasi'].map(key => {
        const b          = data.berkas.find(x => x.jenis === key);
        const bermasalah = (data.berkas_bermasalah || []).includes(key);
        if (b) {
            return `
                <a href="${b.url}" target="_blank"
                   class="berkas-card-modal ${bermasalah ? 'bermasalah-card' : ''}">
                    <div class="bc-icon">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                    </div>
                    <div class="bc-label">${b.label}</div>
                    <div class="bc-sub ada">
                        ${bermasalah
                            ? '<i class="bi bi-exclamation-circle me-1 text-danger"></i>Perlu Direvisi'
                            : '<i class="bi bi-check-circle me-1"></i>Klik untuk buka'}
                    </div>
                </a>`;
        }
        return `
            <div class="berkas-card-modal missing">
                <div class="bc-icon"><i class="bi bi-file-earmark text-muted"></i></div>
                <div class="bc-label">${labelBerkas[key] || key}</div>
                <div class="bc-sub kosong"><i class="bi bi-dash-circle me-1"></i>Tidak ada</div>
            </div>`;
    }).join('');

    // Tampilkan panel
    panel.style.display = '';
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function tutupDetail() {
    const panel = document.getElementById('panelDetail');
    if (panel) panel.style.display = 'none';
}

/* ============================================================
   READ PAGE — Hapus pengajuan
   ============================================================ */
function confirmDelete(id, csrfToken) {
    Swal.fire({
        title: 'Hapus Pengajuan?',
        text: 'Data pengajuan ini akan dihapus permanen beserta semua berkasnya.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            const form   = document.createElement('form');
            form.method  = 'POST';
            form.action  = `/jabatanfungsional/${id}`;

            const csrf   = document.createElement('input');
            csrf.type    = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;

            const method = document.createElement('input');
            method.type  = 'hidden'; method.name = '_method'; method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

/* ============================================================
   NOTIFIKASI
   ============================================================ */
function showNotif(type, title, text) {
    Swal.fire({
        icon: type,
        title: title,
        text: text,
        confirmButtonColor: '#b91c1c',
        timer: type === 'success' ? 2500 : undefined,
        timerProgressBar: type === 'success',
    });
}

/* ============================================================
   FORM PAGE — Tendik: pilih dari dropdown
   Update hidden input nama jabfung target
   ============================================================ */
function onSelectTendik(selectEl) {
    const val    = selectEl.value;
    const hidden = document.getElementById('input-nama-jabfung-target'); // disesuaikan
    if (hidden) hidden.value = val;
}

/* ============================================================
   FORM PAGE — Validasi file
   ============================================================ */
function validateFile(inputId, errId, maxMB, required = false) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (!input || !err) return true;

    if (!input.files.length) {
        if (required) {
            err.textContent   = 'File ini wajib diupload.';
            err.style.display = 'block';
            return false;
        }
        err.style.display = 'none';
        return true;
    }

    const file = input.files[0];

    if (file.type !== 'application/pdf') {
        err.textContent   = 'File harus berformat PDF.';
        err.style.display = 'block';
        return false;
    }

    if (file.size > maxMB * 1024 * 1024) {
        err.textContent   = `Ukuran file melebihi batas ${maxMB}MB.`;
        err.style.display = 'block';
        return false;
    }

    err.style.display = 'none';
    return true;
}

function initFileValidation(isEditOrRevisi = false, berkasBermasalah = []) {
    const fileRules = [
        { id: 'file-sk_cpns',   errId: 'err-sk_cpns',   max: 5  },
        { id: 'file-sk_pns',    errId: 'err-sk_pns',    max: 5  },
        { id: 'file-pak',       errId: 'err-pak',        max: 5  },
        { id: 'file-publikasi', errId: 'err-publikasi',  max: 10 },
    ];

    fileRules.forEach(rule => {
        const el = document.getElementById(rule.id);
        if (el) {
            el.addEventListener('change', () => validateFile(rule.id, rule.errId, rule.max, false));
        }
    });
}

/* ============================================================
   FORM PAGE — Submit dengan validasi
   ============================================================ */
function submitForm(formId, isEdit, isRevisi, hasSk_cpns, hasSk_pns, hasPak, berkasBermasalah) {
    berkasBermasalah   = berkasBermasalah || [];
    const jenisPegawai = window.JABFUNG_JENIS || 'tendik';

    // Disesuaikan: pakai id input-nama-jabfung-target
    const namaJabfung = document.getElementById('input-nama-jabfung-target')?.value?.trim();
    const nomorUsulan = document.querySelector('[name="nomor_usulan"]')?.value?.trim();

    // 1. Validasi jabfung (skip saat revisi)
    if (!isRevisi && !namaJabfung) {
        Swal.fire({
            icon: 'warning',
            title: 'Jabatan Fungsional Belum Dipilih',
            text: jenisPegawai === 'tendik'
                ? 'Silakan pilih salah satu jabatan fungsional terlebih dahulu.'
                : 'Terjadi kesalahan sistem. Coba muat ulang halaman.',
            confirmButtonColor: '#b91c1c',
        });
        return;
    }

    // 2. Validasi nomor usulan (skip saat revisi)
    if (!isRevisi && !nomorUsulan) {
        Swal.fire({
            icon: 'warning',
            title: 'Nomor SK Kosong',
            text: 'Nomor SK / Usulan wajib diisi.',
            confirmButtonColor: '#b91c1c',
        });
        return;
    }

    // 3. Validasi berkas
    let v1, v2, v3, v4;

    if (isRevisi) {
        v1 = berkasBermasalah.includes('sk_cpns')   ? validateFile('file-sk_cpns',   'err-sk_cpns',   5,  true) : true;
        v2 = berkasBermasalah.includes('sk_pns')    ? validateFile('file-sk_pns',    'err-sk_pns',    5,  true) : true;
        v3 = berkasBermasalah.includes('pak')       ? validateFile('file-pak',       'err-pak',       5,  true) : true;
        v4 = berkasBermasalah.includes('publikasi') ? validateFile('file-publikasi', 'err-publikasi', 10, true) : true;
    } else {
        v1 = validateFile('file-sk_cpns',   'err-sk_cpns',   5,  isEdit ? !hasSk_cpns : true);
        v2 = validateFile('file-sk_pns',    'err-sk_pns',    5,  isEdit ? !hasSk_pns  : true);
        v3 = validateFile('file-pak',       'err-pak',       5,  isEdit ? !hasPak     : true);
        v4 = validateFile('file-publikasi', 'err-publikasi', 10, false);
    }

    if (!v1 || !v2 || !v3 || !v4) {
        Swal.fire({
            icon: 'error',
            title: 'Cek Berkas',
            text: 'Ada berkas yang tidak valid atau belum diupload. Periksa kembali.',
            confirmButtonColor: '#b91c1c',
        });
        return;
    }

    // 4. Konfirmasi final
    const titleSwal = isRevisi ? 'Kirim Revisi?'
                    : (isEdit  ? 'Simpan Perubahan?' : 'Konfirmasi Pengajuan');
    const textSwal  = isRevisi
        ? 'Berkas yang sudah diganti akan dikirim kembali ke operator untuk diverifikasi.'
        : (isEdit
            ? 'Perubahan akan disimpan. Pengajuan tetap menunggu diproses operator.'
            : 'Pengajuan akan dikirim ke operator untuk diverifikasi. Lanjutkan?');
    const btnText   = isRevisi ? 'Ya, Kirim Revisi' : (isEdit ? 'Ya, Simpan' : 'Ya, Ajukan');

    Swal.fire({
        icon: 'question',
        title: titleSwal,
        text: textSwal,
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: btnText,
        cancelButtonText: 'Periksa Lagi',
    }).then(res => {
        if (res.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}