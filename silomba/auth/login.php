
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/functions.php';

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

$error = get_flash('error');
$success = get_flash('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= e(APP_NAME) ?></title>

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
            <h1>Temukan dan Kelola <span>Prestasi</span> Bersama SILOMBA</h1>

            <p>
                Platform informasi dan pengelolaan lomba yang membantu
                mahasiswa menemukan kesempatan berprestasi serta memudahkan
                pengelola dalam mengatur kegiatan perlombaan.
            </p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Informasi lomba terpusat dan mudah diakses</span>
                </div>

                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Pengajuan dan pengelolaan lomba lebih teratur</span>
                </div>

                <div class="auth-feature">
                    <div class="auth-feature-icon">✓</div>
                    <span>Pemantauan peserta dan sertifikat secara digital</span>
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
                <h2>Selamat Datang!</h2>
                <p>
                    Silakan masuk menggunakan akun Anda untuk melanjutkan
                    ke sistem informasi lomba.
                </p>
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
                action="<?= url('auth/process_login.php') ?>"
                method="POST"
                data-validate
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="form-group">
                    <label class="form-label" for="username">
                        Username atau Email
                    </label>

                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">✉</span>
                        <input
                            class="auth-input"
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username atau email"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        Kata Sandi
                    </label>

                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">●</span>
                        <input
                            class="auth-input"
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            data-password-field
                            required
                        >

                        <button
                            class="auth-password-toggle"
                            type="button"
                            data-toggle-password="#password"
                        >
                            Lihat
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                </div>

                <button class="auth-button" type="submit">
                    Masuk ke SILOMBA →
                </button>
            </form>

            <div class="auth-divider">Belum memiliki akun?</div>

            <div class="auth-switch">
                Mahasiswa baru?
                <a href="<?= url('auth/register.php') ?>">
                    Daftar sekarang
                </a>
            </div>

            <div class="auth-footer">
                Sistem Informasi Lomba · UIN Imam Bonjol Padang
            </div>
        </div>
    </section>

</div>

<script src="<?= url('assets/js/form.js') ?>"></script>
</body>
</html>
