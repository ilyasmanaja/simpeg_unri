
document.addEventListener('DOMContentLoaded', function () {

    // ======================================
    // ELEMENT
    // ======================================

    const statusEl   = document.getElementById('f_status');
    const nikEl      = document.getElementById('f_nik');
    const pwdChip    = document.getElementById('pwdChipDisplay');

    const pangkatEl  = document.getElementById('pangkat');
    const jabfungEl  = document.getElementById('jabfung');

    const checkboxes =
        document.querySelectorAll('input[name="roles[]"]');

    // ======================================
    // RULE PIMPINAN
    // ======================================

    // Minimal pangkat IV/a - IV/c
    const golonganPimpinan = [
        'IV/a',
        'IV/b',
        'IV/c'
    ];

    // Minimal jabfung
    const jabfungPimpinan = [
        'lektor kepala',
        'profesor',
        'guru besar'
    ];

    // ======================================
    // HELPER
    // ======================================

    function getCheckedRoles()
    {
        return [...checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.dataset.jenis);
    }

    function isPimpinanChecked()
    {
        return getCheckedRoles()
            .includes('pimpinan');
    }

    function isNonPNS()
    {
        return statusEl.value === 'Non PNS';
    }

    function disable(cb, title = '')
    {
        if (!cb) return;

        cb.disabled = true;

        cb.closest('.hak-akses-card')
            .style.opacity = '.45';

        cb.closest('.hak-akses-card')
            .title = title;
    }

    function enable(cb)
    {
        if (!cb) return;

        cb.disabled = false;

        cb.closest('.hak-akses-card')
            .style.opacity = '1';

        cb.closest('.hak-akses-card')
            .title = '';
    }

    function getRole(jenis)
    {
        return [...checkboxes].find(cb =>
            cb.dataset.jenis === jenis
        );
    }

    // ======================================
    // ROLE
    // ======================================

    const pimpinanCb = getRole('pimpinan');
    const dosenCb    = getRole('dosen');
    const tendikCb   = getRole('tendik');
    const operatorCb = getRole('operator');

    // ======================================
    // WARNING PIMPINAN
    // ======================================

    function updatePimpinanWarnings()
    {
        const warnPangkat =
            document.getElementById(
                'warn_pangkat_pimpinan'
            );

        const warnJabfung =
            document.getElementById(
                'warn_jabfung_pimpinan'
            );

        if (!isPimpinanChecked())
        {
            warnPangkat.style.display = 'none';
            warnJabfung.style.display = 'none';
            return;
        }

        // ======================
        // VALIDASI PANGKAT
        // ======================

        const selectedPangkat =
            pangkatEl.options[
                pangkatEl.selectedIndex
            ];

        const gol =
            selectedPangkat?.dataset.gol
            ?.trim() || '';

        const pangkatOk =
            golonganPimpinan.includes(gol);

        warnPangkat.style.display =
            pangkatOk ? 'none' : 'block';

        // ======================
        // VALIDASI JABFUNG
        // ======================

        const jabfungText =
            jabfungEl.options[
                jabfungEl.selectedIndex
            ]
            ?.text
            ?.toLowerCase()
            ?.trim() || '';

        const jabfungOk =
            jabfungPimpinan.some(j =>
                jabfungText.includes(j)
            );

        warnJabfung.style.display =
            jabfungOk ? 'none' : 'block';
    }

    // ======================================
    // LOGIC ROLE
    // ======================================

    function updateRoleLogic()
    {
        // reset semua
        checkboxes.forEach(cb => {

            enable(cb);

        });

        // ======================================
        // NON PNS
        // ======================================

        if (isNonPNS())
        {
            if (dosenCb)
            {
                dosenCb.checked = false;

                disable(
                    dosenCb,
                    'Non PNS tidak dapat memiliki role Dosen'
                );
            }

            if (pimpinanCb)
            {
                pimpinanCb.checked = false;

                disable(
                    pimpinanCb,
                    'Non PNS tidak dapat memiliki role Pimpinan'
                );
            }
        }

        // ======================================
        // VALIDASI PIMPINAN
        // ======================================

        if (pimpinanCb && !isNonPNS())
        {
            const selectedPangkat =
                pangkatEl.options[
                    pangkatEl.selectedIndex
                ];

            const gol =
                selectedPangkat?.dataset.gol
                ?.trim() || '';

            const pangkatOk =
                golonganPimpinan.includes(gol);

            const jabfungText =
                jabfungEl.options[
                    jabfungEl.selectedIndex
                ]
                ?.text
                ?.toLowerCase()
                ?.trim() || '';

            const jabfungOk =
                jabfungPimpinan.some(j =>
                    jabfungText.includes(j)
                );

            if (!pangkatOk || !jabfungOk)
            {
                pimpinanCb.checked = false;

                disable(
                    pimpinanCb,
                    'Pimpinan membutuhkan pangkat IV/a-IV/c dan minimal Lektor Kepala'
                );
            }
        }

        // ======================================
        // ROLE COMBINATION
        // ======================================

        if (tendikCb?.checked)
        {
            if (!isNonPNS())
            {
                disable(dosenCb);
                disable(pimpinanCb);

                if (dosenCb)
                    dosenCb.checked = false;

                if (pimpinanCb)
                    pimpinanCb.checked = false;
            }
        }

        if (dosenCb?.checked)
        {
            disable(tendikCb);
            disable(operatorCb);

            if (tendikCb)
                tendikCb.checked = false;

            if (operatorCb)
                operatorCb.checked = false;
        }

        if (pimpinanCb?.checked)
        {
            disable(tendikCb);
            disable(operatorCb);

            if (tendikCb)
                tendikCb.checked = false;

            if (operatorCb)
                operatorCb.checked = false;
        }

        if (operatorCb?.checked)
        {
            if (tendikCb)
            {
                tendikCb.checked = true;

                disable(
                    tendikCb,
                    'Operator wajib Tendik'
                );
            }

            if (!isNonPNS())
            {
                disable(dosenCb);
                disable(pimpinanCb);

                if (dosenCb)
                    dosenCb.checked = false;

                if (pimpinanCb)
                    pimpinanCb.checked = false;
            }
        }

        updatePimpinanWarnings();
    }

    // ======================================
    // PASSWORD PREVIEW
    // ======================================

    if (nikEl)
    {
        nikEl.addEventListener('input', function(){

            pwdChip.textContent =
                this.value
                ? this.value
                : 'Akan mengikuti NIK pegawai';

        });
    }

    // ======================================
    // EVENT
    // ======================================

    if (statusEl)
    {
        statusEl.addEventListener(
            'change',
            updateRoleLogic
        );
    }

    if (pangkatEl)
    {
        pangkatEl.addEventListener(
            'change',
            updateRoleLogic
        );
    }

    if (jabfungEl)
    {
        jabfungEl.addEventListener(
            'change',
            updateRoleLogic
        );
    }

    checkboxes.forEach(cb => {

        cb.addEventListener(
            'change',
            updateRoleLogic
        );

    });

    // ======================================
    // SUBMIT
    // ======================================

    document.querySelector('form')
        .addEventListener('submit', function(){

            // aktifkan semua sebelum submit
            checkboxes.forEach(cb => {

                cb.disabled = false;

            });

        });

    // ======================================
    // INIT
    // ======================================

    updateRoleLogic();

});
