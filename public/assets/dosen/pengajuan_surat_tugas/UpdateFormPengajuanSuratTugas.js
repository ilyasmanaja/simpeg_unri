document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.querySelector('form');
    if (!form) return;

    const inputs = form.querySelectorAll('input:not([readonly]), textarea, select');
    const btnBatal = document.querySelector('.btn-batal');

    // Deklarasi Elemen
    const waktuInput = document.getElementById('waktu_pelaksanaan');
    const lamaHariInput = document.getElementById('lama_hari');
    const perihalInput = document.getElementById('perihal');
    const berkasInput = document.getElementById('berkas');

    // --- FITUR: UBAH WARNA TEKS VALIDASI ---
    function updateValidationColor(inputElement, msgElementId) {
        const msgElement = document.getElementById(msgElementId);
        if(!msgElement) return;

        if (inputElement.value.trim() !== '') {
            msgElement.className = 'validation-text text-success';
        } else {
            msgElement.className = 'validation-text text-error';
        }
    }

    if(waktuInput) waktuInput.addEventListener('change', () => updateValidationColor(waktuInput, 'msg_waktu'));
    if(lamaHariInput) lamaHariInput.addEventListener('input', () => updateValidationColor(lamaHariInput, 'msg_lama'));
    if(perihalInput) perihalInput.addEventListener('input', () => updateValidationColor(perihalInput, 'msg_perihal'));
    if(berkasInput) berkasInput.addEventListener('change', () => updateValidationColor(berkasInput, 'msg_berkas'));


    // --- LOGIKA VALIDASI SAAT TOMBOL AJUKAN DIKLIK ---
    form.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        let isValid = true;
        
        inputs.forEach(input => {
            input.classList.remove('is-invalid');
        });

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid'); 
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Lengkap!',
                text: 'Harap isi semua kolom dan unggah berkas yang diminta.',
                confirmButtonColor: '#c0392b' 
            });
        } 
        else {
            Swal.fire({
                icon: 'success',
                title: 'Pengajuan Berhasil!',
                text: 'Surat Tugas Anda berhasil diajukan dan menunggu persetujuan.',
                confirmButtonColor: '#198754' 
            }).then((result) => {
                if (result.isConfirmed) {
                    form.reset();
                    
                    // Reset semua teks validasi kembali merah
                    document.querySelectorAll('.validation-text').forEach(el => {
                        el.className = 'validation-text text-error';
                    });
                }
            });
        }
    });

    // --- LOGIKA SAAT TOMBOL BATAL DIKLIK ---
    if (btnBatal) {
        btnBatal.addEventListener('click', function() {
            let isAnyFilled = Array.from(inputs).some(input => input.value.trim() !== '');

            if (isAnyFilled) {
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Semua data yang sudah Anda ketik akan hilang!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#7f8c8d',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.reset();
                        inputs.forEach(input => input.classList.remove('is-invalid'));
                        
                        document.querySelectorAll('.validation-text').forEach(el => {
                            el.className = 'validation-text text-error';
                        });
                    }
                });
            } else {
                form.reset();
                inputs.forEach(input => input.classList.remove('is-invalid'));
                
                document.querySelectorAll('.validation-text').forEach(el => {
                    el.className = 'validation-text text-error';
                });
            }
        });
    }

    // --- LOGIKA MENGHILANGKAN MERAH (BORDER) SAAT MULAI MENGETIK ---
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });

});