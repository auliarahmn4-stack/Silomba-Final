
<?php
require_once __DIR__ . '/auth.php';
require_role('pic');

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" data-sidebar>
    <div class="sidebar-brand">
        <span class="brand-icon">
            <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        </span>
        <div class="sidebar-brand-text">
            <strong>SILOMBA</strong>
            <small>PIC</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/dashboard.php')) ?>">
            <span class="menu-icon">📊</span> Dashboard
        </a>

        <a class="nav-link <?= $current_page === 'ajukan_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/ajukan_lomba.php')) ?>">
            <span class="menu-icon">📝</span> Ajukan Lomba
        </a>

        <a class="nav-link <?= $current_page === 'kelola_informasi_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/kelola_informasi_lomba.php')) ?>">
            <span class="menu-icon">🏆</span> Kelola Informasi Lomba
        </a>

        <a class="nav-link <?= $current_page === 'kelola_data_peserta.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/kelola_data_peserta.php')) ?>">
            <span class="menu-icon">👥</span> Kelola Data Peserta
        </a>

        <a class="nav-link <?= $current_page === 'unggah_sertifikat.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/unggah_sertifikat.php')) ?>">
            <span class="menu-icon">📜</span> Unggah Sertifikat
        </a>

        <a class="nav-link <?= $current_page === 'notifikasi.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/notifikasi.php')) ?>">
            <span class="menu-icon">🔔</span> Notifikasi
        </a>

        <a class="nav-link <?= $current_page === 'profil.php' ? 'active' : '' ?>"
           href="<?= e(url('pic/profil.php')) ?>">
            <span class="menu-icon">👤</span> Profil
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= e(url('auth/logout.php')) ?>">
            <span class="menu-icon">🚪</span> Keluar
        </a>
    </div>
</aside>
