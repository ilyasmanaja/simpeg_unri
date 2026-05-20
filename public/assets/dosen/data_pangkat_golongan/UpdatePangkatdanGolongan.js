document.addEventListener("DOMContentLoaded", function () {

    const data = JSON.parse(localStorage.getItem("editData"));

    if (data) {
        document.getElementById("pangkat").value = data.pangkat;
        document.getElementById("tmt").value = convertDate(data.tmt);
    }

    document.getElementById("formUpdate").addEventListener("submit", function (e) {
        e.preventDefault();

        let dataList = JSON.parse(localStorage.getItem("dataList")) || [];
        const oldData = JSON.parse(localStorage.getItem("editData"));

        const fileInput = document.getElementById("berkas");

        const newData = {
            pangkat: document.getElementById("pangkat").value,
            tmt: document.getElementById("tmt").value,
            file: fileInput && fileInput.files[0] ? fileInput.files[0].name : oldData.file
        };

        if (!newData.pangkat || !newData.tmt) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Semua field harus diisi!'
            });
            return;
        }

        dataList[oldData.index] = newData;

        localStorage.setItem("dataList", JSON.stringify(dataList));

        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data berhasil diperbarui',
            confirmButtonColor: '#dc3545'
        }).then(() => {
            window.location.href = "ReadPangkatdanGolongan.html";
        });
    });

    document.getElementById("btnBatal").addEventListener("click", function () {
        Swal.fire({
            title: 'Yakin mau batal?',
            text: 'Perubahan tidak akan disimpan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, batal',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "ReadPangkatdanGolongan.html";
            }
        });
    });

    function convertDate(tanggal) {
        if (!tanggal) return "";
        if (tanggal.includes("-")) return tanggal;
        if (tanggal.includes("/")) {
            const parts = tanggal.split("/");
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        return "";
    }

});
document.addEventListener("DOMContentLoaded", function () {

    const data = JSON.parse(localStorage.getItem("editData"));

    if (data) {
        document.getElementById("pangkat").value = data.pangkat;
        document.getElementById("tmt").value = convertDate(data.tmt);
    }

    document.getElementById("formUpdate").addEventListener("submit", function (e) {
    e.preventDefault();

    console.log("Submit jalan ✅");

    let dataList = JSON.parse(localStorage.getItem("dataList")) || [];
    const oldData = JSON.parse(localStorage.getItem("editData")) || {};

    const fileInput = document.getElementById("berkas");

    const newData = {
        pangkat: document.getElementById("pangkat").value,
        tmt: document.getElementById("tmt").value,
        file: fileInput.files[0] ? fileInput.files[0].name : oldData.file
    };

    if (!newData.pangkat || !newData.tmt) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Semua field harus diisi!'
        });
        return;
    }

    dataList[oldData.index] = newData;

    localStorage.setItem("dataList", JSON.stringify(dataList));

    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data berhasil diperbarui',
        confirmButtonColor: '#dc3545'
    }).then(() => {
        window.location.href = "ReadPangkatdanGolongan.html";
    });
});

    document.getElementById("btnBatal").addEventListener("click", function () {
        Swal.fire({
            title: 'Yakin mau batal?',
            text: 'Perubahan tidak akan disimpan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, batal',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "ReadPangkatdanGolongan.html";
            }
        });
    });

    function convertDate(tanggal) {
        if (!tanggal) return "";
        if (tanggal.includes("-")) return tanggal;
        if (tanggal.includes("/")) {
            const parts = tanggal.split("/");
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        return "";
    }

});
