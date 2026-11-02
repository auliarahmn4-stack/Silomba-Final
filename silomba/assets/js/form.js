
/* =========================================
   SILOMBA - VALIDASI FORMULIR
   ========================================= */

document.addEventListener("DOMContentLoaded", function () {
    // ---------- VALIDASI FORMULIR ----------
    document.querySelectorAll("form[data-validate]").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            let isValid = true;
            let firstInvalid = null;

            form.querySelectorAll("[required]").forEach(function (input) {
                input.setCustomValidity("");

                if (!input.checkValidity()) {
                    isValid = false;

                    if (!firstInvalid) {
                        firstInvalid = input;
                    }
                }
            });

            // Konfirmasi kata sandi jika field tersedia
            const password = form.querySelector("[name='password']");
            const confirmPassword = form.querySelector(
                "[name='konfirmasi_password']"
            );

            if (
                password &&
                confirmPassword &&
                password.value !== confirmPassword.value
            ) {
                confirmPassword.setCustomValidity(
                    "Konfirmasi kata sandi tidak sama."
                );

                isValid = false;

                if (!firstInvalid) {
                    firstInvalid = confirmPassword;
                }
            }

            if (!isValid) {
                event.preventDefault();

                if (firstInvalid) {
                    firstInvalid.reportValidity();
                    firstInvalid.focus();
                }
            }
        });
    });

    // ---------- KONFIRMASI KATA SANDI ----------
    document.querySelectorAll("[data-password-confirm]").forEach(function (input) {
        input.addEventListener("input", function () {
            const form = input.closest("form");

            if (!form) return;

            const password = form.querySelector("[name='password']");

            if (!password) return;

            if (input.value && input.value !== password.value) {
                input.setCustomValidity(
                    "Konfirmasi kata sandi tidak sama."
                );
            } else {
                input.setCustomValidity("");
            }
        });
    });

    // ---------- TAMPILKAN / SEMBUNYIKAN PASSWORD ----------
    document.querySelectorAll("[data-toggle-password]").forEach(function (button) {
        button.addEventListener("click", function () {
            const selector = button.getAttribute("data-toggle-password");
            const input = document.querySelector(selector);

            if (!input) return;

            const showPassword = input.type === "password";
            input.type = showPassword ? "text" : "password";
            button.textContent = showPassword ? "Sembunyikan" : "Lihat";
            button.setAttribute("aria-pressed", String(showPassword));
        });
    });

    // ---------- BATAS UKURAN FILE ----------
    document.querySelectorAll("[data-max-size]").forEach(function (input) {
        input.addEventListener("change", function () {
            const file = input.files && input.files[0];

            if (!file) return;

            const maxSizeMB = Number(input.getAttribute("data-max-size"));
            const maxSizeBytes = maxSizeMB * 1024 * 1024;

            if (
                Number.isFinite(maxSizeBytes) &&
                maxSizeBytes > 0 &&
                file.size > maxSizeBytes
            ) {
                alert(
                    "Ukuran file maksimal " + maxSizeMB + " MB."
                );

                input.value = "";
            }
        });
    });

    // ---------- BATAS TIPE FILE ----------
    document.querySelectorAll("[data-allowed-types]").forEach(function (input) {
        input.addEventListener("change", function () {
            const file = input.files && input.files[0];

            if (!file) return;

            const allowedTypes = input
                .getAttribute("data-allowed-types")
                .split(",")
                .map(function (type) {
                    return type.trim().toLowerCase();
                });

            const extension = file.name
                .split(".")
                .pop()
                .toLowerCase();

            if (!allowedTypes.includes(extension)) {
                alert(
                    "Format file tidak didukung. Gunakan: " +
                    allowedTypes.join(", ")
                );

                input.value = "";
            }
        });
    });

    // ---------- FORMAT NOMOR ----------
    document.querySelectorAll("[data-numeric-only]").forEach(function (input) {
        input.addEventListener("input", function () {
            input.value = input.value.replace(/[^0-9]/g, "");
        });
    });

    // ---------- HITUNG JUMLAH KARAKTER ----------
    document.querySelectorAll("[data-character-count]").forEach(function (input) {
        const selector = input.getAttribute("data-character-count");
        const counter = document.querySelector(selector);

        if (!counter) return;

        function updateCounter() {
            counter.textContent = input.value.length + " karakter";
        }

        input.addEventListener("input", updateCounter);
        updateCounter();
    });
});
