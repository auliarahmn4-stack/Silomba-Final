
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Dashboard PIC';
$user_id = (int) current_user()['id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM lomba WHERE pic_id = ?");
$stmt->execute([$user_id]);
$total_lomba = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM lomba WHERE pic_id = ? AND status = 'menunggu'"
);
$stmt->execute([$user_id]);
$menunggu = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM pendaftaran_lomba p
     JOIN lomba l ON l.id = p.lomba_id
     WHERE l.pic_id = ?"
);
$stmt->execute([$user_id]);
$total_peserta = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM sertifikat WHERE pic_id = ?"
);
$stmt->execute([$user_id]);
$total_sertifikat = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT id, judul, status, deadline_pendaftaran, created_at
     FROM lomba WHERE pic_id = ?
     ORDER BY created_at DESC LIMIT 5"
);
$stmt->execute([$user_id]);
$lomba_terbaru = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Dashboard PIC</h1>
        <p>Kelola pengajuan lomba, peserta, dan sertifikat kegiatan Anda.</p>
    </div>
    <a href="<?= e(url('pic/ajukan_lomba.php')) ?>" class="btn btn-primary">
        + Ajukan Lomba
    </a>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon">🏆</div>
        <div><p>Total Lomba</p><h2><?= number_format($total_lomba) ?></h2></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">⏳</div>
        <div><p>Menunggu Verifikasi</p><h2><?= number_format($menunggu) ?></h2></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">👥</div>
        <div><p>Total Pendaftar</p><h2><?= number_format($total_peserta) ?></h2></div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">📜</div>
        <div><p>Sertifikat Diunggah</p><h2><?= number_format($total_sertifikat) ?></h2></div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Lomba Saya</h2>
            <p>Status terbaru pengajuan dan informasi lomba.</p>
        </div>
        <a href="<?= e(url('pic/kelola_informasi_lomba.php')) ?>"
           class="btn btn-outline">Kelola Lomba</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$lomba_terbaru): ?>
                    <tr><td colspan="4" class="empty-state">Anda belum mengajukan lomba.</td></tr>
                <?php else: ?>
                    <?php foreach ($lomba_terbaru as $i => $lomba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($lomba['judul']) ?></td>
                            <td><?= e($lomba['deadline_pendaftaran'] ?? '-') ?></td>
                            <td><span class="badge badge-info"><?= e(ucfirst($lomba['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
