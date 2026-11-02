
/* =========================================
   SILOMBA - JAVASCRIPT UTAMA
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
    // ---------- SIDEBAR MOBILE ----------
    const sidebar = document.querySelector(".sidebar");
    const menuToggle = document.querySelector(".menu-toggle");
    let overlay = document.querySelector(".sidebar-overlay");

    // Membuat overlay otomatis jika belum tersedia
    if (sidebar && !overlay) {
        overlay = document.createElement("div");
        overlay.className = "sidebar-overlay";
        document.body.appendChild(overlay);
    }

    function openSidebar() {
        if (!sidebar || !overlay) return;

        sidebar.classList.add("open");
        overlay.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    function closeSidebar() {
        if (!sidebar || !overlay) return;

        sidebar.classList.remove("open");
        overlay.classList.remove("active");
        document.body.style.overflow = "";
    }

    if (menuToggle) {
        menuToggle.addEventListener("click", function () {
            if (sidebar && sidebar.classList.contains("open")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeSidebar();
        }
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth > 700) {
            closeSidebar();
        }
    });

    // ---------- TUTUP ALERT ----------
    document.querySelectorAll("[data-dismiss-alert]").forEach(function (button) {
        button.addEventListener("click", function () {
            const alert = button.closest(".alert");

            if (alert) {
                alert.remove();
            }
        });
    });

    // ---------- KONFIRMASI AKSI ----------
    document.querySelectorAll("[data-confirm]").forEach(function (element) {
        element.addEventListener("click", function (event) {
            const message =
                element.getAttribute("data-confirm") ||
                "Apakah Anda yakin ingin melanjutkan?";

            if (!window.confirm(message)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });

    // ---------- PREVIEW GAMBAR ----------
    document.querySelectorAll("[data-image-preview]").forEach(function (input) {
        input.addEventListener("change", function () {
            const targetSelector = input.getAttribute("data-image-preview");
            const preview = document.querySelector(targetSelector);
            const file = input.files && input.files[0];

            if (!preview || !file) return;

            if (!file.type.startsWith("image/")) {
                alert("File yang dipilih harus berupa gambar.");
                input.value = "";
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = "block";
            };

            reader.readAsDataURL(file);
        });
    });

    // ---------- PENCARIAN TABEL ----------
    document.querySelectorAll("[data-table-search]").forEach(function (input) {
        input.addEventListener("input", function () {
            const selector = input.getAttribute("data-table-search");
            const table = document.querySelector(selector);

            if (!table) return;

            const keyword = input.value.toLowerCase().trim();
            const rows = table.querySelectorAll("tbody tr");

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? "" : "none";
            });
        });
    });

    // ---------- TOMBOL SALIN TEKS ----------
    document.querySelectorAll("[data-copy-text]").forEach(function (button) {
        button.addEventListener("click", async function () {
            const text = button.getAttribute("data-copy-text");

            if (!text) return;

            try {
                await navigator.clipboard.writeText(text);

                const originalText = button.textContent;
                button.textContent = "Tersalin!";

                window.setTimeout(function () {
                    button.textContent = originalText;
                }, 1500);
            } catch (error) {
                alert("Teks tidak dapat disalin secara otomatis.");
            }
        });
    });

    // ---------- DROPDOWN ----------
    document.querySelectorAll("[data-dropdown-toggle]").forEach(function (button) {
        button.addEventListener("click", function () {
            const selector = button.getAttribute("data-dropdown-toggle");
            const menu = document.querySelector(selector);

            if (!menu) return;

            const isHidden = menu.hasAttribute("hidden");

            menu.toggleAttribute("hidden", !isHidden);
            button.setAttribute("aria-expanded", String(isHidden));
        });
    });

    // ---------- TOOLTIP SEDERHANA ----------
    document.querySelectorAll("[data-tooltip]").forEach(function (element) {
        element.setAttribute(
            "title",
            element.getAttribute("data-tooltip")
        );
    });
});
