
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Kelola Informasi Lomba';
$user_id = (int) current_user()['id'];

$stmt = $pdo->prepare(
    "SELECT l.*,
        (SELECT COUNT(*) FROM pendaftaran_lomba p WHERE p.lomba_id = l.id) AS jumlah_peserta
      FROM lomba l WHERE l.pic_id = ?
      ORDER BY l.created_at DESC"
);
$stmt->execute([$user_id]);
$lomba_list = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Kelola Informasi Lomba</h1>
        <p>Pantau pengajuan, status publikasi, dan jumlah pendaftar.</p>
    </div>
    <a href="<?= e(url('pic/ajukan_lomba.php')) ?>" class="btn btn-primary">
        + Ajukan Lomba
    </a>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Deadline</th>
                    <th>Peserta</th>
                    <th>Status</th>
                    <th>Catatan AKAMA</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$lomba_list): ?>
                    <tr><td colspan="7" class="empty-state">Belum ada lomba yang diajukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($lomba_list as $i => $lomba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($lomba['judul']) ?></td>
                            <td><?= e($lomba['deadline_pendaftaran'] ?? '-') ?></td>
                            <td><?= number_format((int) $lomba['jumlah_peserta']) ?></td>
                            <td><span class="badge <?= $lomba['status'] === 'menunggu'
                                ? 'badge-warning'
                                : ($lomba['status'] === 'ditolak'
                                    ? 'badge-danger'
                                    : ($lomba['status'] === 'siap_publikasi'
                                        ? 'badge-info'
                                        : 'badge-success')) ?>">
                                    <?= e(ucfirst(str_replace('_', ' ', $lomba['status']))) ?>
                                </span></td>
                            <td><?= e($lomba['catatan_verifikasi'] ?? '-') ?></td>
                            <td>
                                <?php if ($lomba['status'] === 'disetujui'): ?>
                                    <a href="<?= e(url('pic/lengkapi_lomba.php?id=' . (int)$lomba['id'])) ?>"
                                       class="btn btn-sm btn-primary">Lengkapi Detail</a>
                                <?php elseif ($lomba['status'] === 'siap_publikasi'): ?>
                                    <span class="btn btn-sm btn-outline" style="cursor:default;">Menunggu AKAMA</span>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-outline" style="cursor:default;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
