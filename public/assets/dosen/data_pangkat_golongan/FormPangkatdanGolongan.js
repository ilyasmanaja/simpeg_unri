// ══════════════════════════════════════
// JABFUNG TO PANGKAT MAPPING
// ══════════════════════════════════════
const jabfungToPangkat = {
    'Asisten Ahli':  { min: 6,  max: 6,  label: 'III/b' },
    'Lektor':        { min: 7,  max: 8,  label: 'III/c – III/d' },
    'Lektor Kepala': { min: 9,  max: 10, label: 'IV/a – IV/b' },
    'Guru Besar':    { min: 11, max: 13, label: 'IV/c – IV/e' },
};

// ══════════════════════════════════════
// VALIDASI FILE (Form Ajukan)
// ══════════════════════════════════════
function validateFile(inputId, errId, maxMB, required = false) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (!input.files.length) {
        if (required) {
            err.textContent = 'File ini wajib diupload.';
            err.style.display = 'block';
            return false;
        }
        err.style.display = 'none';
        return true;
    }
    const file = input.files[0];
    if (file.type !== 'application/pdf') {
        err.textContent = 'File harus berformat PDF.';
        err.style.display = 'block';
        return false;
    }
    if (file.size > maxMB * 1024 * 1024) {
        err.textContent = `Ukuran file melebihi batas ${maxMB}MB.`;
        err.style.display = 'block';
        return false;
    }
    err.style.display = 'none';
    return true;
}

// ══════════════════════════════════════
// LISTENER REAL-TIME + CEK KONFLIK JABFUNG
// ══════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('file-sk-cpns')) return;

    // Validasi real-time tiap file berubah
    ['sk-cpns', 'sk-pns', 'pak'].forEach(k => {
        document.getElementById('file-' + k).addEventListener('change', () =>
            validateFile('file-' + k, 'err-' + k, 5));
    });
    document.getElementById('file-publikasi').addEventListener('change', () =>
        validateFile('file-publikasi', 'err-publikasi', 10));

    // Cek konflik jabfung saat pilih pangkat
    document.getElementById('selectPangkat').addEventListener('change', function () {
        const alertEl = document.getElementById('jabfung-alert');
        const msgEl   = document.getElementById('jabfung-alert-msg');
        const urutan  = parseInt(this.options[this.selectedIndex].dataset.urutan || '0');

        if (!jabfungSekarang || !urutan) {
            alertEl.classList.add('d-none');
            return;
        }

        const info = jabfungToPangkat[jabfungSekarang];
        if (!info) { alertEl.classList.add('d-none'); return; }

        if (urutan < info.min || urutan > info.max) {
            msgEl.innerHTML = `<strong>Perhatian:</strong> Pangkat yang dipilih tidak sesuai dengan
                jabatan fungsional <strong>${jabfungSekarang}</strong> Anda
                (seharusnya <strong>${info.label}</strong>).<br>
                <span class="text-muted">Pengajuan tetap bisa dikirim, namun berpotensi ditolak operator.</span>`;
            alertEl.classList.remove('d-none');
        } else {
            alertEl.classList.add('d-none');
        }
    });
});

// ══════════════════════════════════════
// SUBMIT FORM AJUKAN
// ══════════════════════════════════════
function submitForm(action) {
    const pangkat     = document.getElementById('selectPangkat').value;
    const nomorUsulan = document.querySelector('[name="nomor_usulan"]').value.trim();

    const v1 = validateFile('file-sk-cpns',   'err-sk-cpns',   5,  action === 'ajukan');
    const v2 = validateFile('file-sk-pns',    'err-sk-pns',    5,  action === 'ajukan');
    const v3 = validateFile('file-pak',       'err-pak',       5,  action === 'ajukan');
    const v4 = validateFile('file-publikasi', 'err-publikasi', 10, false);

    if (!v1 || !v2 || !v3 || !v4) {
        Swal.fire({
            icon: 'error',
            title: 'Cek Berkas',
            text: 'Ada berkas yang tidak valid. Periksa kembali.',
            confirmButtonColor: '#b91c1c'
        });
        return;
    }

    if (action === 'ajukan') {
        if (!pangkat) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Pangkat',
                text: 'Pangkat yang diajukan harus dipilih.',
                confirmButtonColor: '#b91c1c'
            });
            return;
        }
        if (!nomorUsulan) {
            Swal.fire({
                icon: 'warning',
                title: 'Nomor SK Kosong',
                text: 'Nomor SK/Usulan wajib diisi saat mengajukan.',
                confirmButtonColor: '#b91c1c'
            });
            return;
        }
    }

    if (action === 'ajukan' && jabfungSekarang && pangkat) {
        const urutan     = parseInt(document.querySelector('#selectPangkat option:checked').dataset.urutan || '0');
        const info       = jabfungToPangkat[jabfungSekarang];
        const adaKonflik = info && (urutan < info.min || urutan > info.max);

        if (adaKonflik) {
            Swal.fire({
                icon: 'warning',
                title: 'Konflik Jabatan Fungsional',
                html: `Pangkat yang dipilih tidak sesuai dengan jabfung <b>${jabfungSekarang}</b> Anda
                       (seharusnya <b>${info.label}</b>).<br><br>
                       Pengajuan tetap bisa dikirim namun <b>berisiko ditolak</b> operator.<br>Yakin lanjutkan?`,
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tetap Ajukan',
                cancelButtonText: 'Batal'
            }).then(res => { if (res.isConfirmed) doSubmit('ajukan'); });
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Pengajuan',
                text: 'Pengajuan akan dikirim ke operator untuk diverifikasi. Lanjutkan?',
                showCancelButton: true,
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Periksa Lagi'
            }).then(res => { if (res.isConfirmed) doSubmit('ajukan'); });
        }
        return;
    }

    Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Pengajuan',
        text: 'Pengajuan akan dikirim ke operator untuk diverifikasi. Lanjutkan?',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ajukan',
        cancelButtonText: 'Periksa Lagi'
    }).then(res => { if (res.isConfirmed) doSubmit('ajukan'); });
}

function doSubmit(action) {
    document.getElementById('form-action').value = action;
    document.getElementById('formPanggol').submit();
}

// ══════════════════════════════════════
// EDIT / REVISI PENGAJUAN
// ══════════════════════════════════════
function validateFileEdit(inputId, errId, maxMB) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (!input || !input.files.length) { err.style.display = 'none'; return true; }
    const file = input.files[0];
    if (file.type !== 'application/pdf') {
        err.textContent = 'Harus PDF.';
        err.style.display = 'block';
        return false;
    }
    if (file.size > maxMB * 1024 * 1024) {
        err.textContent = `Melebihi ${maxMB}MB.`;
        err.style.display = 'block';
        return false;
    }
    err.style.display = 'none';
    return true;
}

function initEditForm() {
    [['sk_cpns', 5], ['sk_pns', 5], ['pak', 5], ['publikasi', 10]].forEach(([k, m]) => {
        document.getElementById('file-' + k)?.addEventListener('change', () =>
            validateFileEdit('file-' + k, 'err-' + k, m));
    });

    document.getElementById('selectPangkat')?.addEventListener('change', function () {
        const alertEl = document.getElementById('jabfung-alert');
        const msgEl   = document.getElementById('jabfung-alert-msg');
        const urutan  = parseInt(this.options[this.selectedIndex]?.dataset.urutan || '0');
        if (!jabfungSekarang || !urutan) { alertEl.style.display = 'none'; return; }
        const info = jabfungToPangkat[jabfungSekarang];
        if (!info) { alertEl.style.display = 'none'; return; }
        if (urutan < info.min || urutan > info.max) {
            msgEl.innerHTML = `<strong>Perhatian:</strong> Pangkat ini tidak sesuai jabfung
                <strong>${jabfungSekarang}</strong> (seharusnya <strong>${info.label}</strong>).
                Berisiko ditolak operator.`;
            alertEl.style.display = 'flex';
        } else {
            alertEl.style.display = 'none';
        }
    });
}

function submitFormEdit() {
    const v1 = validateFileEdit('file-sk_cpns',   'err-sk_cpns',   5);
    const v2 = validateFileEdit('file-sk_pns',    'err-sk_pns',    5);
    const v3 = validateFileEdit('file-pak',       'err-pak',       5);
    const v4 = validateFileEdit('file-publikasi', 'err-publikasi', 10);

    if (!v1 || !v2 || !v3 || !v4) {
        Swal.fire({
            icon: 'error',
            title: 'Cek Berkas',
            text: 'Ada berkas yang tidak valid.',
            confirmButtonColor: '#b91c1c'
        });
        return;
    }

    const urutan  = parseInt(document.querySelector('#selectPangkat option:checked')?.dataset.urutan || '0');
    const info    = jabfungSekarang ? jabfungToPangkat[jabfungSekarang] : null;
    const konflik = info && (urutan < info.min || urutan > info.max);

    const doKonfirmasi = () => {
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Pengiriman',
            text: 'Pengajuan akan dikirim ke operator. Lanjutkan?',
            showCancelButton: true,
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) document.getElementById('formEdit').submit(); });
    };

    if (konflik) {
        Swal.fire({
            icon: 'warning',
            title: 'Konflik Jabatan Fungsional',
            html: `Pangkat tidak sesuai jabfung <b>${jabfungSekarang}</b>
                   (seharusnya <b>${info.label}</b>).<br>Tetap kirim? Berisiko ditolak.`,
            showCancelButton: true,
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tetap Kirim',
            cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) doKonfirmasi(); });
    } else {
        doKonfirmasi();
    }
}