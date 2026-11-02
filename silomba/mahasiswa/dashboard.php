
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Dashboard Mahasiswa';
$user_id = (int) current_user()['id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran_lomba WHERE mahasiswa_id = ?");
$stmt->execute([$user_id]);
$total_daftar = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM sertifikat WHERE mahasiswa_id = ?"
);
$stmt->execute([$user_id]);
$total_sertifikat = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND dibaca_at IS NULL"
);
$stmt->execute([$user_id]);
$total_notifikasi = (int)$stmt->fetchColumn();

$stmt = $pdo->query(
    "SELECT id, judul, kategori, tingkat, deadline_pendaftaran
     FROM lomba
     WHERE status = 'dipublikasikan'
       AND deadline_pendaftaran >= CURDATE()
     ORDER BY deadline_pendaftaran ASC LIMIT 5"
);
$lomba_tersedia = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>

<section class="page-heading">
    <div>
        <h1>Dashboard Mahasiswa</h1>
        <p>Temukan lomba, pantau pendaftaran, dan akses sertifikat Anda.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(url('mahasiswa/informasi_lomba.php')) ?>">
        Cari Lomba
    </a>
</section>

<section class="stats-grid">
    <article class="stat-card"><div class="stat-icon">📝</div><div><p>Lomba Diikuti</p><h2><?= number_format($total_daftar) ?></h2></div></article>
    <article class="stat-card"><div class="stat-icon">📜</div><div><p>Sertifikat</p><h2><?= number_format($total_sertifikat) ?></h2></div></article>
    <article class="stat-card"><div class="stat-icon">🔔</div><div><p>Notifikasi Belum Dibaca</p><h2><?= number_format($total_notifikasi) ?></h2></div></article>
</section>

<section class="card">
    <div class="card-header">
        <div><h2>Lomba yang Sedang Dibuka</h2><p>Daftar lomba yang dapat Anda ikuti.</p></div>
        <a href="<?= e(url('mahasiswa/informasi_lomba.php')) ?>" class="btn btn-outline">Lihat Semua</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead><tr><th>No.</th><th>Nama Lomba</th><th>Kategori</th><th>Tingkat</th><th>Deadline</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (!$lomba_tersedia): ?>
                    <tr><td colspan="6" class="empty-state">Belum ada lomba yang sedang dibuka.</td></tr>
                <?php else: foreach ($lomba_tersedia as $i => $lomba): ?>
                    <tr>
                        <td><?= $i + 1 ?></td><td><?= e($lomba['judul']) ?></td>
                        <td><?= e($lomba['kategori'] ?? '-') ?></td>
                        <td><?= e($lomba['tingkat'] ?? '-') ?></td>
                        <td><?= e($lomba['deadline_pendaftaran']) ?></td>
                        <td><a class="btn btn-sm btn-primary" href="<?= e(url('mahasiswa/informasi_lomba.php')) ?>">Lihat</a></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
