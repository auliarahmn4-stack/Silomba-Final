
<?php
require_once __DIR__ . '/auth.php';
require_role('akama');

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" data-sidebar>
    <div class="sidebar-brand">
        <span class="brand-icon">
            <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        </span>
        <div class="sidebar-brand-text">
            <strong>SILOMBA</strong>
            <small>AKAMA</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/dashboard.php')) ?>">
            <span class="menu-icon">📊</span> Dashboard
        </a>

        <a class="nav-link <?= $current_page === 'pengajuan_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/pengajuan_lomba.php')) ?>">
            <span class="menu-icon">📥</span> Pengajuan Lomba
        </a>

        <a class="nav-link <?= $current_page === 'daftar_lomba.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/daftar_lomba.php')) ?>">
            <span class="menu-icon">🏆</span> Daftar Lomba
        </a>

        <a class="nav-link <?= $current_page === 'data_peserta.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/data_peserta.php')) ?>">
            <span class="menu-icon">👥</span> Data Peserta
        </a>

        <a class="nav-link <?= $current_page === 'sertifikat.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/sertifikat.php')) ?>">
            <span class="menu-icon">📜</span> Sertifikat
        </a>

        <a class="nav-link <?= $current_page === 'statistik.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/statistik.php')) ?>">
            <span class="menu-icon">📈</span> Statistik
        </a>

        <a class="nav-link <?= $current_page === 'pengaturan.php' ? 'active' : '' ?>"
           href="<?= e(url('akama/pengaturan.php')) ?>">
            <span class="menu-icon">⚙️</span> Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= e(url('auth/logout.php')) ?>">
            <span class="menu-icon">🚪</span> Keluar
        </a>
    </div>
</aside>
