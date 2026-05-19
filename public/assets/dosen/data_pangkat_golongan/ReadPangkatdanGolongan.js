document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.querySelector(".tbl-panggol tbody");
    if (!tableBody) return;

    //nomor 
    function updateNomor() {
        const rows = tableBody.querySelectorAll("tr");
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }

    updateNomor();
});

// ── Detail iframe modal ──
function bukaDetail(id) {
    document.getElementById('iframeDetail').src = '/dosen/pangkat-golongan/' + id;
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}
document.getElementById('modalDetail').addEventListener('hidden.bs.modal', function () {
    document.getElementById('iframeDetail').src = '';
});

// ── Konfirmasi hapus ──
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Pengajuan?',
        text: 'Data pengajuan ini akan dihapus permanen beserta semua berkasnya.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/dosen/pangkat-golongan/' + id;
            const methodInput = document.createElement('input');
            methodInput.type  = 'hidden';
            methodInput.name  = '_method';
            methodInput.value = 'DELETE';
            const tokenInput = document.createElement('input');
            tokenInput.type  = 'hidden';
            tokenInput.name  = '_token';
            tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ══════════════════════════════════════
// MODAL BERKAS
// ══════════════════════════════════════
function bukaModalBerkas(namaAjuan, berkasArr) {
    document.getElementById('modalBerkasNama').textContent = namaAjuan;

    var isi = document.getElementById('modalBerkasIsi');
    isi.innerHTML = '';

    if (!berkasArr || berkasArr.length === 0) {
        isi.innerHTML =
            '<div class="berkas-kosong">' +
                '<i class="bi bi-folder-x"></i>' +
                'Belum ada berkas yang diunggah.' +
            '</div>';
    } else {
        berkasArr.forEach(function (f) {
            var ext  = f.path ? f.path.split('.').pop().toUpperCase() : 'FILE';
            var item = document.createElement('a');
            item.href      = f.path;
            item.target    = '_blank';
            item.rel       = 'noopener noreferrer';
            item.className = 'berkas-item';
            item.innerHTML =
                '<div class="berkas-icon ' + f.warna + '">' +
                    '<i class="bi bi-file-earmark-pdf-fill"></i>' +
                '</div>' +
                '<div class="berkas-info">' +
                    '<div class="berkas-nama">' + f.label + '</div>' +
                    '<div class="berkas-sub">' + ext + ' · Klik untuk buka</div>' +
                '</div>' +
                '<i class="bi bi-box-arrow-up-right berkas-arrow"></i>';
            isi.appendChild(item);
        });
    }

    document.getElementById('modalBerkas').classList.add('aktif');
    document.body.style.overflow = 'hidden';
}

function tutupModalBerkas() {
    document.getElementById('modalBerkas').classList.remove('aktif');
    document.body.style.overflow = '';
}

if (document.getElementById('modalBerkas')) {
    document.getElementById('modalBerkas').addEventListener('click', function (e) {
        if (e.target === this) tutupModalBerkas();
    });
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') tutupModalBerkas();
});