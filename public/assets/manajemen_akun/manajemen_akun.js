
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // ELEMENT
    // =========================
    const statusEl       = document.getElementById('f_status');
    const tipeWrap       = document.getElementById('wrap_tipe_pns');

    const tipeDosen      = document.getElementById('tipe_dosen');
    const tipeTendik     = document.getElementById('tipe_tendik');

    const hintNonDosen   = document.getElementById('hint_nondosen');

    const nikEl          = document.getElementById('f_nik');
    const pwdChip        = document.getElementById('pwdChipDisplay');

    const pangkatEl      = document.getElementById('pangkat');
    const jabfungEl      = document.getElementById('jabfung');

    const wrapNip        = document.getElementById('wrap_nip');
    const wrapNidn       = document.getElementById('wrap_nidn');
    const wrapPangkat    = document.getElementById('wrap_pangkat');
    const wrapJabfung    = document.getElementById('wrap_jabfung');

    const warnPangkat    = document.getElementById('warn_pangkat_pimpinan');
    const warnJabfung    = document.getElementById('warn_jabfung_pimpinan');

    const checkboxes     = document.querySelectorAll('input[name="roles[]"]');

    // =========================
    // SYARAT PIMPINAN
    // =========================
    const golonganPimpinan = [
        'IV/a',
        'IV/b',
        'IV/c',
        'IV/d',
        'IV/e'
    ];

    const jabfungPimpinan = [
        'lektor kepala',
        'guru besar',
        'profesor'
    ];

    // =========================
    // RULE KOMBINASI ROLE
    // =========================
    const allowedPartner = {
        pimpinan: ['dosen'],
        dosen: ['pimpinan'],
        tendik: ['operator'],
        operator: ['tendik'],
    };

    // =========================
    // HELPER
    // =========================
    function getCheckedRoles() {
        return [...checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.jenis);
    }

    function isPimpinanChecked() {
        return getCheckedRoles().includes('pimpinan');
    }

    function getCheckbox(jenis) {
        return [...checkboxes]
            .find(cb => cb.dataset.jenis === jenis);
    }

    function disableCheckbox(cb, uncheck = true) {

        if (!cb) return;

        if (uncheck) {
            cb.checked = false;
        }

        cb.disabled = true;

        const card = cb.closest('.hak-akses-card');

        if (card) {
            card.style.opacity = '.45';
            card.style.pointerEvents = 'none';
            card.style.cursor = 'not-allowed';
        }
    }

    function enableCheckbox(cb) {

        if (!cb) return;

        cb.disabled = false;

        const card = cb.closest('.hak-akses-card');

        if (card) {
            card.style.opacity = '1';
            card.style.pointerEvents = '';
            card.style.cursor = '';
        }
    }

    // =========================
    // FILTER JABFUNG
    // =========================
    function filterJabfung() {

        const isDosen  = tipeDosen.checked;
        const isTendik = tipeTendik.checked;

        [...jabfungEl.options].forEach(option => {

            if (!option.value) return;

            const id = parseInt(option.value);

            // DOSEN
            if (isDosen) {

                if (id >= 1 && id <= 4) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';

                    if (jabfungEl.value == option.value) {
                        jabfungEl.value = '';
                    }
                }
            }

            // TENDIK
            else if (isTendik) {

                if (id >= 5 && id <= 14) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';

                    if (jabfungEl.value == option.value) {
                        jabfungEl.value = '';
                    }
                }
            }

            // DEFAULT
            else {
                option.style.display = '';
            }
        });
    }

    // =========================
    // VALIDASI PIMPINAN
    // =========================
    function updatePimpinanWarnings() {

        if (!isPimpinanChecked()) {
            warnPangkat.style.display = 'none';
            warnJabfung.style.display = 'none';
            return;
        }

        // =========================
        // CEK PANGKAT
        // =========================
        if (statusEl.value === 'PNS') {

            const selectedOpt = pangkatEl.options[pangkatEl.selectedIndex];

            const gol = selectedOpt
                ? (selectedOpt.dataset.gol || '').trim()
                : '';

            const pangkatOk = golonganPimpinan.some(g =>
                gol.includes(g)
            );

            warnPangkat.style.display = pangkatOk
                ? 'none'
                : '';

        } else {
            warnPangkat.style.display = 'none';
        }

        // =========================
        // CEK JABFUNG
        // =========================
        const selectedJabfungOpt =
            jabfungEl.options[jabfungEl.selectedIndex];

        const jabfungText = selectedJabfungOpt
            ? selectedJabfungOpt.text.toLowerCase().trim()
            : '';

        const jabfungOk = jabfungPimpinan.some(j =>
            jabfungText.includes(j)
        );

        warnJabfung.style.display = jabfungOk
            ? 'none'
            : '';
    }

    // =========================
    // ROLE LOGIC
    // =========================
    function updateRoleLogic() {

        const checked = getCheckedRoles();

        const pimpinanCb = getCheckbox('pimpinan');
        const dosenCb    = getCheckbox('dosen');
        const tendikCb   = getCheckbox('tendik');
        const operatorCb = getCheckbox('operator');

        // =========================
        // RESET AWAL
        // =========================
        checkboxes.forEach(cb => {
            enableCheckbox(cb);
        });

        // =========================
        // NON PNS
        // =========================
        if (statusEl.value === 'Non PNS') {

            disableCheckbox(dosenCb);
            disableCheckbox(pimpinanCb);
        }

        // =========================
        // BELUM ADA ROLE
        // =========================
        if (checked.length === 0) {
            updatePimpinanWarnings();
            return;
        }

        // =========================
        // 1 ROLE DIPILIH
        // =========================
        if (checked.length === 1) {

            const selected = checked[0];

            const allowed = allowedPartner[selected] || [];

            checkboxes.forEach(cb => {

                const jenis = cb.dataset.jenis;

                if (jenis === selected) return;

                // biarkan partner tetap aktif
                if (allowed.includes(jenis)) return;

                disableCheckbox(cb);
            });
        }

        // =========================
        // 2 ROLE DIPILIH
        // =========================
        if (checked.length >= 2) {

            checkboxes.forEach(cb => {

                if (cb.checked) return;

                disableCheckbox(cb, false);
            });
        }

        // =========================
        // AUTO TENDIK JIKA OPERATOR
        // =========================
        if (operatorCb && operatorCb.checked && tendikCb) {

            tendikCb.checked = true;
            tendikCb.disabled = true;

            const card = tendikCb.closest('.hak-akses-card');

            if (card) {
                card.style.opacity = '1';
                card.style.pointerEvents = '';
                card.style.cursor = '';
            }
        }

        updatePimpinanWarnings();
    }

    // =========================
    // SHOW / HIDE FIELD
    // =========================
    function updateFieldVisibility() {

        const status = statusEl.value;

        const showFields = status !== '';

        tipeWrap.style.display = showFields ? '' : 'none';

        // =========================
        // PNS
        // =========================
        if (status === 'PNS') {

            wrapNip.style.display      = '';
            wrapNidn.style.display     = '';
            wrapPangkat.style.display  = '';
            wrapJabfung.style.display  = '';

            tipeDosen.disabled  = false;
            tipeTendik.disabled = false;

            hintNonDosen.style.display = 'none';
        }

        // =========================
        // NON PNS
        // =========================
        else if (status === 'Non PNS') {

            wrapNip.style.display      = 'none';
            wrapNidn.style.display     = 'none';
            wrapPangkat.style.display  = 'none';
            wrapJabfung.style.display  = '';

            tipeDosen.checked  = false;
            tipeDosen.disabled = true;

            tipeTendik.checked  = true;
            tipeTendik.disabled = true;

            hintNonDosen.style.display = '';

            pangkatEl.value = '';
        }

        // =========================
        // DEFAULT
        // =========================
        else {

            wrapNip.style.display      = 'none';
            wrapNidn.style.display     = 'none';
            wrapPangkat.style.display  = 'none';
            wrapJabfung.style.display  = 'none';
        }

        filterJabfung();
        updateRoleLogic();
        updatePimpinanWarnings();
    }

    // =========================
    // PASSWORD PREVIEW
    // =========================
    nikEl.addEventListener('input', function () {

        pwdChip.textContent = this.value
            ? this.value
            : 'Akan mengikuti NIK pegawai';
    });

    // =========================
    // EVENT
    // =========================
    tipeDosen.addEventListener('change', function () {
        filterJabfung();
        updateRoleLogic();
    });

    tipeTendik.addEventListener('change', function () {
        filterJabfung();
        updateRoleLogic();
    });

    statusEl.addEventListener('change', updateFieldVisibility);

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateRoleLogic);
    });

    pangkatEl.addEventListener('change', updatePimpinanWarnings);

    jabfungEl.addEventListener('change', updatePimpinanWarnings);

    // =========================
    // ENABLE SEMUA SAAT SUBMIT
    // =========================
    document.querySelector('form')
        .addEventListener('submit', function () {

            checkboxes.forEach(cb => {
                cb.disabled = false;
            });
        });

    // =========================
    // INIT
    // =========================
    updateFieldVisibility();
});
