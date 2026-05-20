document.addEventListener("DOMContentLoaded", function () {
    // Fitur auto-nomor pada tabel (opsional karena Blade sudah pakai $no+1, tapi tetap aman dibiarkan)
    const tableBody = document.querySelector(".tbl-panggol tbody");
    if (tableBody) {
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach((row, index) => {
            if(row.cells.length > 1) { // Abaikan baris "Data kosong"
                row.cells[0].textContent = index + 1;
            }
        });
    }
});

// ══════════════════════════════════════
// FITUR INLINE DETAIL PANEL
// ══════════════════════════════════════
function bukaDetail(id) {
    // 1. Cari data pengajuan berdasarkan ID dari array JSON yang dilempar oleh Blade
    const data = window.PENGAJUAN_DATA.find(item => item.id === id);
    if (!data) return;

    // 2. Isi teks ke dalam elemen-elemen di Panel Detail
    document.getElementById('dp-subtitle').textContent   = "Pangkat Diajukan: " + data.pangkat_target;
    document.getElementById('dp-nama').textContent       = window.PEGAWAI_NAMA;
    document.getElementById('dp-id-label').textContent   = window.PEGAWAI_ID_LABEL;
    document.getElementById('dp-id-value').textContent   = window.PEGAWAI_ID_VALUE;
    document.getElementById('dp-pangkat-now').textContent= window.PANGKAT_NOW;
    document.getElementById('dp-pangkat-target').textContent = data.pangkat_target;
    document.getElementById('dp-nomor').textContent      = data.nomor_usulan;
    document.getElementById('dp-tanggal').textContent    = data.tanggal;

    // 3. Render Status Badge
    const badgeHtml = `<span class="status-badge ${data.status_class}"><i class="bi ${data.status_icon} me-1"></i> ${data.status_label}</span>`;
    document.getElementById('dp-status-badge').innerHTML = badgeHtml;

    // 4. Render Status Banner (Peringatan/Informasi di atas detail)
    const bannerEl = document.getElementById('dp-status-banner');
    if (data.status === 'tolak_verifikasi' || data.status === 'tolak_persetujuan') {
        bannerEl.className = 'status-banner tolak';
        bannerEl.innerHTML = `<i class="bi bi-x-circle-fill flex-shrink-0 mt-1"></i>
                              <div><strong>Pengajuan Ditolak</strong><br>Silakan cek catatan dan revisi berkas yang bermasalah.</div>`;
        bannerEl.style.display = 'flex';
    } else if (data.status === 'disetujui') {
        bannerEl.className = 'status-banner setuju';
        bannerEl.innerHTML = `<i class="bi bi-check-circle-fill flex-shrink-0 mt-1"></i>
                              <div><strong>Pengajuan Disetujui</strong><br>Selamat, pengajuan pangkat Anda telah diverifikasi dan disetujui.</div>`;
        bannerEl.style.display = 'flex';
    } else if (['menunggu', 'verifikasi', 'persetujuan'].includes(data.status)) {
        bannerEl.className = 'status-banner proses';
        bannerEl.innerHTML = `<i class="bi bi-hourglass-split flex-shrink-0 mt-1"></i>
                              <div><strong>Sedang Diproses</strong> — ${data.status_label}</div>`;
        bannerEl.style.display = 'flex';
    } else {
        bannerEl.style.display = 'none';
    }

    // 5. Render Daftar Berkas Bermasalah (jika ada revisi)
    const masalahWrap = document.getElementById('dp-bermasalah-wrap');
    const masalahList = document.getElementById('dp-bermasalah-list');
    if (data.berkas_bermasalah && data.berkas_bermasalah.length > 0) {
        masalahWrap.style.display = 'block';
        let mHtml = '<ul class="mb-0 ps-3 text-danger" style="font-size:0.85rem;">';
        data.berkas_bermasalah.forEach(b => {
            // Ubah key (sk_cpns) jadi label cantik jika memungkinkan
            let label = data.berkas.find(x => x.jenis === b)?.label || b;
            mHtml += `<li>Berkas <b>${label}</b> bermasalah dan perlu diunggah ulang.</li>`;
        });
        mHtml += '</ul>';
        masalahList.innerHTML = mHtml;
    } else {
        masalahWrap.style.display = 'none';
    }

    // 6. Render Grid Berkas Pendukung
    const gridEl = document.getElementById('dp-berkas-grid');
    let berkasHtml = '';
    if (data.berkas && data.berkas.length > 0) {
        data.berkas.forEach(b => {
            let isBermasalah = data.berkas_bermasalah && data.berkas_bermasalah.includes(b.jenis);
            let missingStyle = isBermasalah ? 'border: 1.5px dashed #dc3545; background: #fff5f5;' : '';
            let iconColor    = isBermasalah ? 'text-danger' : 'text-danger'; // Icon PDF default merah
            let textStatus   = isBermasalah ? '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Revisi</span>' : '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Buka File</span>';

            berkasHtml += `
                <a href="${b.url}" target="_blank" class="berkas-card" style="text-decoration:none; ${missingStyle}">
                    <div class="bc-icon"><i class="bi bi-file-earmark-pdf-fill ${iconColor}"></i></div>
                    <div class="bc-label text-dark">${b.label}</div>
                    <div class="bc-sub mt-1">${textStatus}</div>
                </a>
            `;
        });
    } else {
        berkasHtml = '<div class="text-muted small"><i class="bi bi-info-circle me-1"></i>Belum ada berkas.</div>';
    }
    gridEl.innerHTML = berkasHtml;

    // 7. Tampilkan Panel dan Scroll ke Bawah
    const panel = document.getElementById('panelDetail');
    panel.style.display = 'block';
    
    // Smooth scroll ke panel detail
    setTimeout(() => {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
}

function tutupDetail() {
    document.getElementById('panelDetail').style.display = 'none';
}