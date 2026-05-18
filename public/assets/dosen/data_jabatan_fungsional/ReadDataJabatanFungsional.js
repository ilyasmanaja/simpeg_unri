document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector(".data-table tbody");

    if (!tableBody) return;

    function updateNomor() {
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach((row, index) => {
            const cellNo = row.querySelector("td:first-child");
            if (cellNo) {
                cellNo.textContent = index + 1;
            }
        });
    }

    tableBody.addEventListener("click", (e) => {

        const hapusBtn = e.target.closest(".btn-hapus");
        if (hapusBtn) {
            const row = hapusBtn.closest("tr");

            Swal.fire({
                title: "Hapus data?",
                text: "Data tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                    updateNomor();

                    Swal.fire({
                        icon: "success",
                        title: "Terhapus!",
                        text: "Data berhasil dihapus."
                    });
                }
            });

            return; 
        }

        const detailBtn = e.target.closest(".btn-outline-secondary");
        if (detailBtn) {
            const row = detailBtn.closest("tr");
            const cells = row.querySelectorAll("td");

            const jabatan = cells[1]?.innerText || "-";
            const tmt = cells[2]?.innerText || "-";
            const sk = cells[3]?.innerText || "-";

            Swal.fire({
                title: "Detail Jabatan",
                html: `
                    <div style="text-align:left;">
                        <p><b>Jabatan:</b> ${jabatan}</p>
                        <p><b>TMT:</b> ${tmt}</p>
                        <p><b>Nomor SK:</b> ${sk}</p>
                    </div>
                `,
                icon: "info",
                confirmButtonColor: "#0d6efd"
            });

            return;
        }

    });

    updateNomor();
});