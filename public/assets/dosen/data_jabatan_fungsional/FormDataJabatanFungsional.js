document.addEventListener("DOMContentLoaded", function () {
    const jabatan = document.getElementById("jabatan");
    const tmt = document.getElementById("tmt");
    const berkas = document.getElementById("berkas");

    const boxJabatan = document.getElementById("box-jabatan");
    const boxTmt = document.getElementById("box-tmt");
    const boxBerkas = document.getElementById("box-berkas");

    const ruleJabatan = document.getElementById("rule-jabatan");
    const ruleTmt = document.getElementById("rule-tmt");
    const ruleBerkas = document.getElementById("rule-berkas");

    const form = document.getElementById("formJabatan");
    const btnBatal = document.querySelector(".btn-batal");

    function setValid(input, rule) {
        rule.classList.add("valid");
        rule.classList.remove("invalid");
        input.classList.add("valid");
        input.classList.remove("invalid");
    }

    function setInvalid(input, rule) {
        rule.classList.add("invalid");
        rule.classList.remove("valid");
        input.classList.add("invalid");
        input.classList.remove("valid");
    }

    jabatan.addEventListener("focus", () => {
        boxJabatan.classList.add("active");
        if (!jabatan.value) setInvalid(jabatan, ruleJabatan);
    });

    jabatan.addEventListener("change", () => {
        jabatan.value ? setValid(jabatan, ruleJabatan) : setInvalid(jabatan, ruleJabatan);
    });

    tmt.addEventListener("focus", () => {
        boxTmt.classList.add("active");
        if (!tmt.value) setInvalid(tmt, ruleTmt);
    });

    tmt.addEventListener("input", () => {
        tmt.value ? setValid(tmt, ruleTmt) : setInvalid(tmt, ruleTmt);
    });

    berkas.addEventListener("focus", () => {
        boxBerkas.classList.add("active");
        if (!berkas.value) setInvalid(berkas, ruleBerkas);
    });

    berkas.addEventListener("change", () => {
        const file = berkas.files[0];
        if (!file) return setInvalid(berkas, ruleBerkas);

        const max = 10 * 1024 * 1024; 
        file.size <= max ? setValid(berkas, ruleBerkas) : setInvalid(berkas, ruleBerkas);
    });

    // form validasi
    function isValidForm() {
        let valid = true;

        if (!jabatan.value) {
            setInvalid(jabatan, ruleJabatan);
            boxJabatan.classList.add("active");
            valid = false;
        }

        if (!tmt.value) {
            setInvalid(tmt, ruleTmt);
            boxTmt.classList.add("active");
            valid = false;
        }

        const file = berkas.files[0];
        if (!file || file.size > 10 * 1024 * 1024) {
            setInvalid(berkas, ruleBerkas);
            boxBerkas.classList.add("active");
            valid = false;
        }

        return valid;
    }

    // ajukan
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!isValidForm()) {
            Swal.fire({
                icon: "error",
                title: "Form belum lengkap",
                text: "Isi semua data terlebih dahulu ya!"
            });
            return;
        }

        Swal.fire({
            title: "Yakin mau ajukan?",
            text: "Pastikan data sudah benar ya",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Ajukan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d32f2f"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: "Pengajuan berhasil",
                    timer: 2000,
                    showConfirmButton: false
                });

                form.reset();

                document.querySelectorAll("small").forEach(el => {
                    el.classList.remove("valid", "invalid");
                });

                document.querySelectorAll(".active").forEach(el => {
                    el.classList.remove("active");
                });

            }
        });
    });

    //button batal
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
                window.location.href = "ReadDataJabatanFungsional.html";
            }
        });
    });
});