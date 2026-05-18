document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formPangkatGolongan");
    const btnBatal = document.getElementById("btnBatal");

    const berkasInput = document.getElementById("berkas");
    const boxBerkas = document.getElementById("boxBerkas");
    const ruleBerkas = document.getElementById("ruleBerkas");

    // VALIDASI REAL-TIME BERKAS
    berkasInput.addEventListener("change", function () {
        if (berkasInput.files.length > 0) {
            const file = berkasInput.files[0];
            const fileSizeMB = file.size / 1024 / 1024;

            if (fileSizeMB > 10) {
                showError(boxBerkas, ruleBerkas, "Gagal! File " + fileSizeMB.toFixed(2) + "MB (Maks 10MB)");
                berkasInput.value = "";
            } else {
                showSuccess(boxBerkas, ruleBerkas, "Berkas upload: " + file.name);
            }
        } else {
            boxBerkas.classList.remove("active");
        }
    });

    // SUBMIT FORM
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        let isValid = true;

        const pangkat = document.getElementById("pangkat");
        const tmt = document.getElementById("tmt");
        const boxPangkat = document.getElementById("boxPangkat");
        const boxTMT = document.getElementById("boxTMT");
        const rulePangkat = document.getElementById("rulePangkat");
        const ruleTMT = document.getElementById("ruleTMT");

        // Validasi Pangkat
        if (pangkat.value === "") {
            showError(boxPangkat, rulePangkat, "Pilih pangkat dan golongan!");
            isValid = false;
        } else {
            showSuccess(boxPangkat, rulePangkat, "Pangkat terpilih");
        }

        // Validasi TMT
        if (tmt.value === "") {
            showError(boxTMT, ruleTMT, "Wajib diisi!");
            isValid = false;
        } else {
            showSuccess(boxTMT, ruleTMT, "Tanggal valid");
        }

        // Validasi Berkas
        if (berkasInput.files.length === 0) {
            showError(boxBerkas, ruleBerkas, "Harap unggah berkas!");
            isValid = false;
        }

        // Gagal ajukan
        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Semua data wajib diisi!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Berhasil ajukan
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pengajuan berhasil dikirim!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        }).then(() => {
            window.location.href = "ReadPangkatdanGolongan.html";
        });
    });

    // TOMBOL BATAL
    btnBatal.addEventListener("click", function () {
        Swal.fire({
            title: 'Batalkan Pengisian?',
            text: "Data yang sudah diisi akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6e7881',
            confirmButtonText: 'Ya, kembali',
            cancelButtonText: 'Tetap di sini',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "ReadPangkatdanGolongan.html";
            }
        });
    });

    // HELPER
    function showError(container, textElement, message) {
        container.classList.add("active");
        textElement.innerText = message;
        textElement.className = "invalid";
    }

    function showSuccess(container, textElement, message) {
        container.classList.add("active");
        textElement.innerText = message;
        textElement.className = "valid";
    }
});