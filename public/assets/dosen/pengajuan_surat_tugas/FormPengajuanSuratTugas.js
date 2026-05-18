document.addEventListener("DOMContentLoaded", function () {

const form = document.getElementById("formSurat");

const waktu = document.getElementById("waktu");
const lama = document.getElementById("lama");
const perihal = document.getElementById("perihal");
const berkas = document.getElementById("berkas");

const boxWaktu = document.getElementById("box-waktu");
const boxLama = document.getElementById("box-lama");
const boxPerihal = document.getElementById("box-perihal");
const boxBerkas = document.getElementById("box-berkas");

const ruleWaktu = document.getElementById("rule-waktu");
const ruleLama = document.getElementById("rule-lama");
const ruleWajib = document.getElementById("rule-wajib");
const ruleBerkas = document.getElementById("rule-berkas");

function setValid(input, rule) {
    input.classList.add("valid");
    input.classList.remove("invalid");
    rule.classList.add("valid");
    rule.classList.remove("invalid");
}

function setInvalid(input, rule) {
    input.classList.add("invalid");
    input.classList.remove("valid");
    rule.classList.add("invalid");
    rule.classList.remove("valid");
}

function validateWaktu() {
    if (waktu.value !== "") {
        setValid(waktu, ruleWaktu);
        return true;
    } else {
        setInvalid(waktu, ruleWaktu);
        return false;
    }
}

function validateLama() {
    const val = parseInt(lama.value) || 0;
    if (val >= 1 && val <= 30) {
        setValid(lama, ruleLama);
        return true;
    } else {
        setInvalid(lama, ruleLama);
        return false;
    }
}

function validatePerihal() {
    if (perihal.value.trim() !== "") {
        setValid(perihal, ruleWajib);
        return true;
    } else {
        setInvalid(perihal, ruleWajib);
        return false;
    }
}

function validateBerkas() {
    const file = berkas.files[0];
    if (file && file.size <= 10 * 1024 * 1024) {
        setValid(berkas, ruleBerkas);
        return true;
    } else {
        setInvalid(berkas, ruleBerkas);
        return false;
    }
}

waktu.addEventListener("focus", () => boxWaktu.classList.add("active"));
waktu.addEventListener("input", validateWaktu);

lama.addEventListener("focus", () => boxLama.classList.add("active"));
lama.addEventListener("input", validateLama);

perihal.addEventListener("focus", () => boxPerihal.classList.add("active"));
perihal.addEventListener("input", validatePerihal);

berkas.addEventListener("focus", () => boxBerkas.classList.add("active"));
berkas.addEventListener("change", validateBerkas);

function adaIsi() {
    return (
        waktu.value !== "" ||
        lama.value !== "" ||
        perihal.value.trim() !== "" ||
        berkas.files.length > 0
    );
}

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const isValid =
        validateWaktu() &
        validateLama() &
        validatePerihal() &
        validateBerkas();

    if (!isValid) {
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
                text: "Pengajuan berhasil 🚀",
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

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pengajuan berhasil dikirim!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        }).then(() => {
            window.location.href = "ReadPengajuanSuratTugas.html";
        });
    });

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
                window.location.href = "ReadPengajuanSuratTugas.html";
            }
        });
    });
