
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Sertifikat Saya';
$user_id = (int)current_user()['id'];

$stmt = $pdo->prepare(
    "SELECT s.nomor_sertifikat, s.file_sertifikat, s.tanggal_unggah,
            l.judul AS judul_lomba
     FROM sertifikat s
     JOIN lomba l ON l.id = s.lomba_id
     WHERE s.mahasiswa_id = ?
     ORDER BY s.created_at DESC"
);
$stmt->execute([$user_id]);
$sertifikat = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>

<section class="page-heading">
    <div><h1>Sertifikat Saya</h1><p>Lihat sertifikat dari lomba yang telah Anda ikuti.</p></div>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead><tr><th>No.</th><th>Nama Lomba</th><th>Nomor Sertifikat</th><th>Tanggal Unggah</th><th>File</th></tr></thead>
            <tbody>
                <?php if (!$sertifikat): ?>
                    <tr><td colspan="5" class="empty-state">Belum ada sertifikat yang tersedia.</td></tr>
                <?php else: foreach ($sertifikat as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($item['judul_lomba']) ?></td>
                        <td><?= e($item['nomor_sertifikat']) ?></td>
                        <td><?= e($item['tanggal_unggah'] ?? '-') ?></td>
                        <td>
                            <?php if ($item['file_sertifikat']): ?>
                                <a class="btn btn-sm btn-primary" target="_blank" rel="noopener"
                                   href="<?= e(url('uploads/sertifikat/' . basename($item['file_sertifikat']))) ?>">
                                    Buka Sertifikat
                                </a>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
