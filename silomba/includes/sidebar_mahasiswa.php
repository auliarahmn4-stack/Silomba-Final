
<?php
require_once __DIR__ . '/auth.php';
require_role('mahasiswa');

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" data-sidebar>
    <div class="sidebar-brand">
        <span class="brand-icon">
            <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        </span>
        <div class="sidebar-brand-text">
            <strong>SILOMBA</strong>
            <small>Mahasiswa</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/dashboard.php')) ?>">
            <span class="menu-icon">📊</span> Dashboard
        </a>

        <a class="nav-link <?= $current_page === 'informasi_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/informasi_lomba.php')) ?>">
            <span class="menu-icon">🏆</span> Informasi Lomba
        </a>

        <a class="nav-link <?= $current_page === 'daftar_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/daftar_lomba.php')) ?>">
            <span class="menu-icon">📝</span> Daftar Lomba
        </a>

        <a class="nav-link <?= $current_page === 'riwayat_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/riwayat_lomba.php')) ?>">
            <span class="menu-icon">📋</span> Riwayat Lomba
        </a>

        <a class="nav-link <?= $current_page === 'sertifikat.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/sertifikat.php')) ?>">
            <span class="menu-icon">📜</span> Sertifikat
        </a>

        <a class="nav-link <?= $current_page === 'notifikasi.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/notifikasi.php')) ?>">
            <span class="menu-icon">🔔</span> Notifikasi
        </a>

        <a class="nav-link <?= $current_page === 'profil.php' ? 'active' : '' ?>"
           href="<?= e(url('mahasiswa/profil.php')) ?>">
            <span class="menu-icon">👤</span> Profil
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= e(url('auth/logout.php')) ?>">
            <span class="menu-icon">🚪</span> Keluar
        </a>
    </div>
</aside>
