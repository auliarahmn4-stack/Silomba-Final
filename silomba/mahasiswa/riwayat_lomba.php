
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Riwayat Lomba';
$user_id = (int)current_user()['id'];

$stmt = $pdo->prepare(
    "SELECT p.id, p.tanggal_daftar, p.status,
            l.judul, l.penyelenggara, l.tanggal_mulai, l.deadline_pendaftaran
     FROM pendaftaran_lomba p
     JOIN lomba l ON l.id = p.lomba_id
     WHERE p.mahasiswa_id = ?
     ORDER BY p.tanggal_daftar DESC"
);
$stmt->execute([$user_id]);
$riwayat = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>

<section class="page-heading">
    <div><h1>Riwayat Lomba</h1><p>Pantau status pendaftaran lomba yang pernah Anda ikuti.</p></div>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead><tr><th>No.</th><th>Nama Lomba</th><th>Penyelenggara</th><th>Tanggal Daftar</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (!$riwayat): ?>
                    <tr><td colspan="5" class="empty-state">Anda belum mendaftar lomba.</td></tr>
                <?php else: foreach ($riwayat as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($item['judul']) ?></td>
                        <td><?= e($item['penyelenggara']) ?></td>
                        <td><?= e($item['tanggal_daftar']) ?></td>
                        <td><span class="badge badge-info"><?= e(ucfirst($item['status'])) ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
