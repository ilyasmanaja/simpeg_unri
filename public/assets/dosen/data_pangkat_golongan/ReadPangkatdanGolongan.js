document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.querySelector(".data-table tbody");

    //nomor 
    function updateNomor() {
        const rows = tableBody.querySelectorAll("tr");

        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }

  
    //Delete data 
    tableBody.addEventListener("click", function (e) {

        if (e.target.classList.contains("btn-hapus")) {

            const row = e.target.closest("tr");

            Swal.fire({
                title: 'Hapus data?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    row.remove();
                    updateNomor();

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

    //detail data
    tableBody.addEventListener("click", function (e) {

        if (e.target.classList.contains("btn-outline-secondary")) {

            const row = e.target.closest("tr");
            const data = row.children;

            Swal.fire({
                title: "Detail Pangkat",
                html: `
                    <b>Pangkat:</b> ${data[1].innerText}<br>
                    <b>TMT:</b> ${data[2].innerText}<br>
                    <b>Nomor SK:</b> ${data[3].innerText}
                `,
                icon: "info",
                confirmButtonColor: "#0d6efd"
            });
        }
    });

    updateNomor();

});