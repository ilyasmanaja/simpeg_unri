document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.querySelector(".data-table tbody");
    const searchInput = document.getElementById("searchInput");

    const totalPengajuanEl = document.querySelectorAll(".summary-item h4")[0];
    const menungguEl = document.querySelectorAll(".summary-item h4")[1];
    const totalHariEl = document.querySelectorAll(".summary-item h4")[2];

    function updateNomor() {
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }

    function updateSummary() {
        const rows = tableBody.querySelectorAll("tr");

        let total = rows.length;
        let menunggu = 0;
        let totalHari = 0;

        rows.forEach(row => {

            const status = row.querySelector(".badge-status").textContent.toLowerCase();
            if (status.includes("menunggu")) {
                menunggu++;
            }

            const hariText = row.querySelector(".badge-hari").textContent;
            const angka = parseInt(hariText); 
            totalHari += angka;
        });

        totalPengajuanEl.textContent = total + " Surat";
        menungguEl.textContent = menunggu + " Surat";
        totalHariEl.textContent = totalHari + " Hari";
    }

    searchInput.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll("tr");

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? "" : "none";
        });
    });

    tableBody.addEventListener("click", function (e) {

        if (e.target.classList.contains("btn-hapus")) {

            const row = e.target.closest("tr");

            Swal.fire({
                title: 'Hapus data?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6e7881',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    row.remove();

                    updateNomor();
                    updateSummary();

                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus.',
                        confirmButtonColor: '#dc3545'
                    });
                }

            });
        }
    });

    updateNomor();
    updateSummary();
});