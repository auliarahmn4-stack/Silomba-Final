
<?php
require_once __DIR__ . "/../includes/auth.php";
require_role("akama","pic","mahasiswa");

$page_title = 'Verifikasi Lomba';

require_once __DIR__ . "/../includes/header.php";
require_once __DIR__ . "/../includes/sidebar_akama.php";
?>

<section class="page-heading">
    <div>
        <h1>Verifikasi Lomba</h1>
        <p>Proses verifikasi pengajuan lomba oleh AKAMA.</p>
    </div>
    <a href="javascript:history.back()" class="btn btn-outline">Kembali</a>
</section>

<section class="card">
    <div class="empty-state">
        <div class="empty-icon">✅</div>
        <h3>Halaman verifikasi dalam pengembangan</h3>
        <p>Tampilan verifikasi lomba akan segera tersedia.</p>
    </div>
</section>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
