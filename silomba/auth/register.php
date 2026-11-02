
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/functions.php';

$error = get_flash('error');
$success = get_flash('success');

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';

    $destination = [
        'akama' => 'akama/dashboard.php',
        'pic' => 'pic/dashboard.php',
        'mahasiswa' => 'mahasiswa/dashboard.php',
    ];

    if (isset($destination[$role])) {
        redirect($destination[$role]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - <?= e(APP_NAME) ?></title>

    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/auth.css') ?>">
</head>
<body>
<div class="auth-page">

    <section class="auth-left">
        <div class="auth-brand">
            <span class="auth-logo">
                <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
            </span>
            <div class="auth-brand-text">
                <strong>SILOMBA</strong>
                <span>Sistem Informasi Lomba</span>
            </div>
        </div>

        <div class="auth-hero">
            <h1>Mulai Perjalanan <span>Prestasi</span> Anda</h1>

            <p>
                Buat akun SILOMBA untuk mendapatkan informasi perlombaan,
                melakukan pendaftaran, memantau riwayat keikutsertaan,
                dan mengakses sertifikat lomba.
            </p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Temukan informasi lomba mahasiswa</span>
                </div>

                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Pantau status pendaftaran lomba</span>
                </div>

                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Akses riwayat dan sertifikat lomba</span>
                </div>
            </div>
        </div>

        <div class="auth-left-footer">
            &copy; <?= date('Y') ?> SILOMBA · UIN Imam Bonjol Padang
        </div>
    </section>

    <section class="auth-right">
        <div class="auth-container">

            <div class="auth-mobile-brand">
                <span class="auth-logo">
                    <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
                </span>
                <div class="auth-brand-text">
                    <strong style="color:#14213d">SILOMBA</strong>
                    <span style="color:#6b7280">Sistem Informasi Lomba</span>
                </div>
            </div>

            <div class="auth-heading">
                <h2>Buat Akun</h2>
                <p>Lengkapi data berikut untuk mendaftar sebagai mahasiswa.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <form
                class="auth-form"
                action="<?= url('auth/process_register.php') ?>"
                method="POST"
                data-validate
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="form-group">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input
                        class="auth-input no-icon"
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="nim">NIM</label>
                    <input
                        class="auth-input no-icon"
                        type="text"
                        id="nim"
                        name="nim"
                        placeholder="Masukkan NIM"
                        maxlength="30"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        class="auth-input no-icon"
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        maxlength="150"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input
                        class="auth-input no-icon"
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Buat username"
                        minlength="4"
                        maxlength="50"
                        pattern="[A-Za-z0-9_.-]+"
                        title="Gunakan huruf, angka, titik, garis bawah, atau tanda hubung."
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="auth-input-wrapper">
                        <input
                            class="auth-input no-icon"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                        <button
                            class="auth-password-toggle"
                            type="button"
                            data-toggle-password="#password"
                        >Lihat</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="konfirmasi_password">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="auth-input-wrapper">
                        <input
                            class="auth-input no-icon"
                            type="password"
                            id="konfirmasi_password"
                            name="konfirmasi_password"
                            placeholder="Ulangi kata sandi"
                            minlength="8"
                            autocomplete="new-password"
                            data-password-confirm
                            required
                        >
                    </div>
                </div>

                <label class="auth-terms">
                    <input type="checkbox" name="persetujuan" value="1" required>
                    <span>
                        Saya menyatakan data yang dimasukkan benar dan
                        bersedia menggunakan SILOMBA sesuai ketentuan.
                    </span>
                </label>

                <button class="auth-button" type="submit">
                    Daftar Akun →
                </button>
            </form>

            <div class="auth-divider">Sudah memiliki akun?</div>

            <div class="auth-switch">
                <a href="<?= url('auth/login.php') ?>">Masuk di sini</a>
            </div>

            <div class="auth-footer">
                SILOMBA · UIN Imam Bonjol Padang
            </div>
        </div>
    </section>
</div>

<script src="<?= url('assets/js/form.js') ?>"></script>
</body>
</html>
