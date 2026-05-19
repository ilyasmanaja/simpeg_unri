document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll(".data-table tbody tr").forEach((row) => {
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? "" : "none";
            });
        });
    }

    document.querySelectorAll(".btn-delete").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const link = this.getAttribute("href");

            Swal.fire({
                title: "Yakin mau hapus?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e53935",
                cancelButtonColor: "#9e9e9e",
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal",
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(link, {
                        method: "GET",
                        headers: { "X-Requested-With": "XMLHttpRequest" },
                    })
                        .then((res) => {
                            if (!res.ok) throw new Error("Gagal menghapus data");
                            return res;
                        })
                        .catch((err) => {
                            Swal.showValidationMessage(`Error: ${err.message}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil dihapus",
                        text: "Data pengajuan berhasil dihapus",
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.href = "/read-pengajuan-surat-tugas";
                    });
                }
            });
        });
    });

    const modalEl = document.getElementById("modalDetail");
    const bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;

    document.querySelectorAll(".btn-show-detail").forEach((btn) => {
        btn.addEventListener("click", function () {
            const {
                pengusul, waktu, lama, perihal,
                berkas, status, statusClass, anggota, alasan,
            } = this.dataset;

            document.getElementById("detail-pengusul").textContent = pengusul || "—";
            document.getElementById("detail-status").innerHTML =
                `<span class="badge-status ${statusClass}">${status}</span>`;
            document.getElementById("detail-waktu").textContent  = waktu || "—";
            document.getElementById("detail-lama").textContent   = lama ? lama + " Hari" : "—";
            document.getElementById("detail-perihal").textContent = perihal || "—";

            const anggotaEl = document.getElementById("detail-anggota");
            if (anggota && anggota.trim() !== "") {
                anggotaEl.innerHTML = anggota
                    .split(",")
                    .map((n) => `<span class="badge-anggota">👤 ${n.trim()}</span>`)
                    .join("");
            } else {
                anggotaEl.innerHTML = `<span style="font-size:12px;color:#aaa">Tidak ada anggota</span>`;
            }

            const berkasEl = document.getElementById("detail-berkas");
            berkasEl.innerHTML = berkas
                ? `<a href="${berkas}" target="_blank" rel="noopener"
                      style="color:#1565c0;font-weight:600;font-size:13px">
                        <i class="bi bi-file-earmark-pdf-fill me-1" style="color:#e53935"></i>
                        Lihat File
                   </a>`
                : `<span style="font-size:12px;color:#aaa">Tidak ada file</span>`;

            [
                "box-menunggu-diproses",
                "box-sedang-diverifikasi",
                "box-menunggu-persetujuan",
                "box-ditolak-verifikasi",
                "box-disetujui",
                "box-ditolak-persetujuan",
            ].forEach((id) => document.getElementById(id)?.classList.add("d-none"));

            const st = (status || "").toLowerCase().trim();

            if (st === "menunggu diproses") {
                document.getElementById("box-menunggu-diproses")?.classList.remove("d-none");
            } else if (st === "sedang diverifikasi") {
                document.getElementById("box-sedang-diverifikasi")?.classList.remove("d-none");
            } else if (st === "menunggu persetujuan") {
                document.getElementById("box-menunggu-persetujuan")?.classList.remove("d-none");
            } else if (st === "ditolak (verifikasi)") {
                document.getElementById("box-ditolak-verifikasi")?.classList.remove("d-none");
                document.getElementById("detail-alasan-verifikasi").textContent =
                    alasan || "Tidak ada keterangan alasan dari operator.";
            } else if (st === "disetujui") {
                document.getElementById("box-disetujui")?.classList.remove("d-none");
            } else if (st === "ditolak (persetujuan)") {
                document.getElementById("box-ditolak-persetujuan")?.classList.remove("d-none");
                document.getElementById("detail-alasan-persetujuan").textContent =
                    alasan || "Tidak ada keterangan alasan dari pimpinan.";
            }

            bsModal.show();
        });
    });

    const form = document.getElementById("formSurat");
    if (!form) return;

    const elWaktu   = document.getElementById("waktu");
    const elLama    = document.getElementById("lama");
    const elPerihal = document.getElementById("perihal");
    const elBerkas  = document.getElementById("berkas");

    const boxWaktu   = document.getElementById("box-waktu");
    const boxLama    = document.getElementById("box-lama");
    const boxPerihal = document.getElementById("box-perihal");
    const boxBerkas  = document.getElementById("box-berkas");

    const ruleWaktu  = document.getElementById("rule-waktu");
    const ruleLama   = document.getElementById("rule-lama");
    const ruleWajib  = document.getElementById("rule-wajib");
    const ruleBerkas = document.getElementById("rule-berkas");

    const btnBatal         = document.getElementById("btnBatal");
    const anggotaContainer = document.getElementById("anggota-container");
    const errorDuplikat    = document.getElementById("error-duplikat");

    const pegawaiList = typeof daftarPegawai !== "undefined" ? daftarPegawai : [];

    window.tambahAnggota = function (namaAwal, idPegawaiAwal) {
        namaAwal      = (typeof namaAwal      === "string") ? namaAwal.trim()      : "";
        idPegawaiAwal = (typeof idPegawaiAwal === "string") ? idPegawaiAwal.trim() : "";

        if (!anggotaContainer) return;

        const uid    = Date.now() + Math.random();
        const safeId = idPegawaiAwal || "";

        const dikunci = typeof isDitolakOperator !== "undefined" && isDitolakOperator;

        const div = document.createElement("div");
        div.classList.add("anggota-row", "mb-2");
        div.innerHTML = dikunci
            ? `
                <input type="hidden" name="jenis_anggota[]" value="dosen">
                <input type="hidden" name="id_pegawai[]" value="${safeId}" class="hidden-id-pegawai">
                <div class="input-group">
                    <input type="text"
                           name="nama_anggota[]"
                           class="form-control custom-input anggota-input bg-light"
                           autocomplete="off"
                           data-uid="${uid}"
                           readonly>
                </div>
              `
            : `
                <input type="hidden" name="jenis_anggota[]" value="dosen">
                <input type="hidden" name="id_pegawai[]" value="${safeId}" class="hidden-id-pegawai">
                <div class="input-group">
                    <input type="text"
                           name="nama_anggota[]"
                           class="form-control custom-input anggota-input"
                           placeholder="Ketik nama anggota..."
                           autocomplete="off"
                           data-uid="${uid}">
                    <button class="btn btn-hapus btn-anggota-hapus" type="button">Hapus</button>
                </div>
                <ul class="autocomplete-list d-none" id="ac-${uid}"></ul>
              `;

        anggotaContainer.appendChild(div);

        const inputEl  = div.querySelector(".anggota-input");
        const hiddenId = div.querySelector(".hidden-id-pegawai");

        if (namaAwal.length > 0) {
            inputEl.value = namaAwal;
            inputEl.classList.add("matched");
        }

        if (dikunci) return;

        const acList = div.querySelector(`#ac-${uid}`);

        inputEl.addEventListener("input", function () {
            const q = this.value.trim().toLowerCase();
            hiddenId.value = "";
            inputEl.classList.remove("matched");

            if (q.length < 1) {
                acList.classList.add("d-none");
                acList.innerHTML = "";
                return;
            }

            const hasil = pegawaiList
                .filter((p) => (p.nama_lengkap || "").toLowerCase().includes(q))
                .slice(0, 8);

            if (hasil.length === 0) {
                acList.classList.add("d-none");
                acList.innerHTML = "";
                return;
            }

            acList.innerHTML = hasil
                .map((p) => `
                    <li class="ac-item" data-id="${p.id_pegawai}" data-nama="${p.nama_lengkap}">
                        ${p.nama_lengkap}
                    </li>
                `)
                .join("");
            acList.classList.remove("d-none");
        });

        acList.addEventListener("click", function (e) {
            const item = e.target.closest(".ac-item");
            if (!item) return;
            inputEl.value  = item.dataset.nama;
            hiddenId.value = item.dataset.id;
            inputEl.classList.add("matched");
            acList.classList.add("d-none");
            acList.innerHTML = "";
            validateAnggota();
        });

        inputEl.addEventListener("blur", () => {
            setTimeout(() => {
                acList.classList.add("d-none");
                validateAnggota();
            }, 150);
        });
    };

    if (typeof anggotaLama !== "undefined" && Array.isArray(anggotaLama)) {
        anggotaLama.forEach(function (item) {
            if (typeof item === "string") {
                tambahAnggota(item, "");
            } else if (item !== null && typeof item === "object") {
              
                let nama = "";
                if (item.nama_lengkap && String(item.nama_lengkap).trim() !== "") {
                    nama = String(item.nama_lengkap).trim();
                } else if (item.nama && String(item.nama).trim() !== "") {
                    nama = String(item.nama).trim();
                } else if (item.name && String(item.name).trim() !== "") {
                    nama = String(item.name).trim();
                } else if (item.nama_anggota && String(item.nama_anggota).trim() !== "") {
                  
                    nama = String(item.nama_anggota).trim();
                }
                const id = String(item.id_pegawai ?? item.id ?? "").trim();
                tambahAnggota(nama, id);
            }
        });
    }

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("btn-anggota-hapus")) {
            e.target.closest(".anggota-row").remove();
            validateAnggota();
        }
    });

    function showHint(box, rule, isValid, pesanError) {
        if (!box || !rule) return;
        box.classList.add("active");
        if (isValid) {
            rule.classList.remove("invalid");
            rule.classList.add("valid");
        } else {
            rule.classList.remove("valid");
            rule.classList.add("invalid");
            if (pesanError) rule.textContent = pesanError;
        }
    }

    function validateWaktu(showError) {
        showError = showError !== false;
        if (!elWaktu) return true;
        const today  = new Date().toISOString().split("T")[0];
        const kosong = elWaktu.value === "";
        const lampau = !kosong && elWaktu.value < today;
        const valid  = !kosong && !lampau;
        if (showError) showHint(boxWaktu, ruleWaktu, valid,
            kosong ? "Wajib diisi" : "Tanggal tidak boleh kurang dari hari ini");
        return valid;
    }

    function validateLama(showError) {
        showError = showError !== false;
        if (!elLama) return true;
        const val   = parseInt(elLama.value) || 0;
        const valid = val >= 1 && val <= 30;
        if (showError) showHint(boxLama, ruleLama, valid,
            val < 1 ? "Minimal 1 hari" : "Maksimal 30 hari");
        return valid;
    }

    function validatePerihal(showError) {
        showError = showError !== false;
        if (!elPerihal) return true;
        const valid = elPerihal.value.trim() !== "";
        if (showError) showHint(boxPerihal, ruleWajib, valid, "Wajib diisi");
        return valid;
    }

    function validateBerkas(showError) {
        showError = showError !== false;
        if (!elBerkas) return true;
        const adaFile = elBerkas.files.length > 0;
        const isEdit  = form.querySelector("button[name='submit']")
                            ?.textContent.trim() !== "Ajukan";
        if (!adaFile && isEdit) return true;
        if (!adaFile) {
            if (showError) showHint(boxBerkas, ruleBerkas, false, "Berkas wajib diupload");
            return false;
        }
        const valid = elBerkas.files[0].size <= 10 * 1024 * 1024;
        if (showError) showHint(boxBerkas, ruleBerkas, valid, "Ukuran file maksimal 10MB");
        return valid;
    }

    function validateAnggota() {
        let valid = true;
        const namaList = [];
        document.querySelectorAll(".anggota-input").forEach((input) => {
            const val = input.value.trim();
            if (val === "") {
                input.classList.add("invalid");
                valid = false;
                return;
            }
            input.classList.remove("invalid");
            namaList.push(val.toLowerCase());
        });
        const adaDuplikat = namaList.length !== new Set(namaList).size;
        if (adaDuplikat) {
            errorDuplikat?.classList.remove("d-none");
            valid = false;
        } else {
            errorDuplikat?.classList.add("d-none");
        }
        return valid;
    }

    if (elWaktu) {
        elWaktu.addEventListener("blur",  () => validateWaktu());
        elWaktu.addEventListener("input", () => { if (boxWaktu?.classList.contains("active")) validateWaktu(); });
    }
    if (elLama) {
        elLama.addEventListener("blur",  () => validateLama());
        elLama.addEventListener("input", () => { if (boxLama?.classList.contains("active")) validateLama(); });
    }
    if (elPerihal) {
        elPerihal.addEventListener("blur",  () => validatePerihal());
        elPerihal.addEventListener("input", () => { if (boxPerihal?.classList.contains("active")) validatePerihal(); });
    }
    if (elBerkas) {
        elBerkas.addEventListener("change", () => validateBerkas());
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const isValid =
            validateWaktu()   &&
            validateLama()    &&
            validatePerihal() &&
            validateBerkas()  &&
            validateAnggota();

        if (!isValid) {
            Swal.fire({
                icon: "error",
                title: "Form belum lengkap!",
                text: "Periksa kembali isian yang ditandai merah ya!",
                confirmButtonColor: "#d32f2f",
            });
            return;
        }

        const submitBtn  = form.querySelector("button[name='submit']");
        const labelBtn   = submitBtn?.textContent.trim() ?? "Ajukan";
        const isKembali  = labelBtn === "Revisi";
        const isPerbarui = labelBtn === "Perbarui";

        Swal.fire({
            title: isKembali  ? "Ajukan Kembali?"
                 : isPerbarui ? "Simpan Perubahan?"
                              : "Yakin mau ajukan?",
            text: "Pastikan data sudah benar ya",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: labelBtn,
            cancelButtonText: "Batal",
            confirmButtonColor: "#d32f2f",
        }).then((result) => {
            if (result.isConfirmed) {
                HTMLFormElement.prototype.submit.call(form);
            }
        });
    });

    if (btnBatal) {
        btnBatal.addEventListener("click", function () {
            Swal.fire({
                title: "Batalkan Pengisian?",
                text: "Data yang sudah diisi akan hilang.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6e7881",
                confirmButtonText: "Ya, kembali",
                cancelButtonText: "Tetap di sini",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/read-pengajuan-surat-tugas";
                }
            });
        });
    }

});