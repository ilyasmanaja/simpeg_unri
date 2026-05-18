document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('formUpdate');
    if (!form) return;

    const inputs = form.querySelectorAll('input:not([readonly]), textarea, select');
    const btnBatal = document.querySelector('#formUpdate .btn-batal');

    // Deklarasi Elemen
    const jabatanInput = document.getElementById('jabatan');
    const tmtInput = document.getElementById('tmt');
    const berkasSkInput = document.getElementById('berkas_sk');

    // --- FITUR BARU: UBAH WARNA TEKS VALIDASI ---
    function updateValidationColor(inputElement, msgElementId) {
        const msgElement = document.getElementById(msgElementId);
        if(!msgElement) return;

        if (inputElement.value.trim() !== '') {
            msgElement.className = 'validation-text text-success';
        } else {
            msgElement.className = 'validation-text text-error';
        }
    }

    // Menggunakan 'change' karena elemen berupa select dropdown, input date, dan input file
    if(jabatanInput) jabatanInput.addEventListener('change', () => updateValidationColor(jabatanInput, 'msg_jabatan'));
    if(tmtInput) tmtInput.addEventListener('change', () => updateValidationColor(tmtInput, 'msg_tmt'));
    if(berkasSkInput) berkasSkInput.addEventListener('change', () => updateValidationColor(berkasSkInput, 'msg_berkas_sk'));


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
                title: 'Berhasil!',
                text: 'Data pengajuan Anda berhasil disimpan.',
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
                    title: 'Batalkan Pengisian?',
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
        input.addEventListener('change', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
            }
        });
    });

});