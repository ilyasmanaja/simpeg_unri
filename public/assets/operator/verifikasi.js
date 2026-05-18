const dataAwal = [
  {
    id: 1,
    nama: "Arya Brawijaya",
    jenis: "Jabatan Fungsional",
    jabatan: "Lektor",
    tmt: "01-06-2024",
    file: "lektor.pdf"
  },
  {
    id: 2,
    nama: "Dina Maharani",
    jenis: "Pangkat/Golongan",
    jabatan: "III/c",
    tmt: "15-01-2025",
    file: "iii-c.pdf"
  }
];

let dataTabel = [...dataAwal];
let dataHasilFilter = [...dataTabel];
let currentPage = 1;
const rowsPerPage = 5;

const tableBody = document.getElementById("tableBody");
const searchInput = document.getElementById("searchInput");
const jenisFilter = document.getElementById("jenisFilter");
const filterButton = document.getElementById("filterButton");
const pagination = document.getElementById("pagination");
const pageInfo = document.getElementById("pageInfo");
const showingText = document.getElementById("showingText");
const totalText = document.getElementById("totalText");

function applyFilters() {
  const keyword = searchInput.value.toLowerCase().trim();
  const jenis = jenisFilter.value;

  dataHasilFilter = dataTabel.filter((item) => {
    const cocokKeyword =
      item.nama.toLowerCase().includes(keyword) ||
      item.jenis.toLowerCase().includes(keyword) ||
      item.jabatan.toLowerCase().includes(keyword) ||
      item.file.toLowerCase().includes(keyword);

    const cocokJenis = jenis === "Semua" || item.jenis === jenis;

    return cocokKeyword && cocokJenis;
  });

  currentPage = 1;
  renderTable();
  renderPagination();
}

function renderTable() {
  tableBody.innerHTML = "";

  const totalData = dataHasilFilter.length;
  totalText.textContent = totalData;

  if (totalData === 0) {
    showingText.textContent = "0–0";
    pageInfo.textContent = "Halaman 0 dari 0";
    tableBody.innerHTML = `
      <tr class="empty-row">
        <td colspan="7">Data tidak ditemukan.</td>
      </tr>
    `;
    return;
  }

  const totalPages = Math.ceil(totalData / rowsPerPage);

  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  const startIndex = (currentPage - 1) * rowsPerPage;
  const endIndex = startIndex + rowsPerPage;
  const currentData = dataHasilFilter.slice(startIndex, endIndex);

  showingText.textContent = `${startIndex + 1}–${Math.min(endIndex, totalData)}`;
  pageInfo.textContent = `Halaman ${currentPage} dari ${totalPages}`;

  currentData.forEach((item, index) => {
    const row = `
      <tr>
        <td>${startIndex + index + 1}</td>
        <td>${item.nama}</td>
        <td>${item.jenis}</td>
        <td>${item.jabatan}</td>
        <td>${item.tmt}</td>
        <td>
          <div class="file-box">
            <span class="file-icon"><i class="bi bi-file-earmark-text"></i></span>
            <div>
              <div><a href="#">${item.file}</a></div>
            </div>
          </div>
        </td>
        <td>
          <div class="action-group">
            <button
              class="btn btn-sm btn-verifikasi"
              onclick="openConfirm(${item.id}, 'verifikasi')"
            >
              <i class="bi bi-check-circle me-1"></i> Verifikasi
            </button>
            <button
              class="btn btn-sm btn-delete"
              onclick="openConfirm(${item.id}, 'hapus')"
            >
              <i class="bi bi-trash me-1"></i> Hapus
            </button>
          </div>
        </td>
      </tr>
    `;

    tableBody.innerHTML += row;
  });
}

function renderPagination() {
  pagination.innerHTML = "";

  const totalPages = Math.ceil(dataHasilFilter.length / rowsPerPage);

  const prevLi = document.createElement("li");
  prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
  prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Sebelumnya">‹</a>`;
  prevLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (currentPage > 1) {
      currentPage--;
      renderTable();
      renderPagination();
    }
  });
  pagination.appendChild(prevLi);

  for (let i = 1; i <= totalPages; i++) {
    const li = document.createElement("li");
    li.className = `page-item ${currentPage === i ? "active" : ""}`;
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    li.addEventListener("click", function (e) {
      e.preventDefault();
      currentPage = i;
      renderTable();
      renderPagination();
    });
    pagination.appendChild(li);
  }

  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`;
  nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Berikutnya">›</a>`;
  nextLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (currentPage < totalPages) {
      currentPage++;
      renderTable();
      renderPagination();
    }
  });
  pagination.appendChild(nextLi);
}

function openConfirm(id, action) {
  const isVerifikasi = action === "verifikasi";

  Swal.fire({
    title: isVerifikasi ? "Konfirmasi Verifikasi" : "Konfirmasi Hapus",
    text: isVerifikasi
      ? "Apakah Anda yakin memverifikasi data ini?"
      : "Apakah Anda yakin ingin menghapus data ini?",
    icon: isVerifikasi ? "question" : "warning",
    showCancelButton: true,
    confirmButtonColor: isVerifikasi ? "#2e7d32" : "#424242",
    cancelButtonColor: "#6e7881",
    confirmButtonText: isVerifikasi ? "Ya, Verifikasi" : "Ya, Hapus",
    cancelButtonText: "Batal"
  }).then((result) => {
    if (!result.isConfirmed) return;

    dataTabel = dataTabel.filter((item) => item.id !== id);
    applyFilters();

    Swal.fire({
      icon: "success",
      title: "Berhasil",
      text: isVerifikasi
        ? "Data berhasil diverifikasi dan telah dihapus dari daftar antrian."
        : "Data berhasil dihapus dari daftar antrian.",
      confirmButtonColor: "#b30000"
    });
  });
}

filterButton.addEventListener("click", function () {
  applyFilters();
});

searchInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    applyFilters();
  }
});

window.openConfirm = openConfirm;

applyFilters();