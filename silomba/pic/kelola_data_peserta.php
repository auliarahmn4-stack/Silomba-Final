
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Kelola Data Peserta';
$user_id = (int) current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/kelola_data_peserta.php');
    }

    $pendaftaran_id = filter_input(INPUT_POST, 'pendaftaran_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';

    if (!$pendaftaran_id || !in_array($status, ['diterima', 'ditolak'], true)) {
        set_flash('error', 'Data pendaftaran tidak valid.');
        redirect('pic/kelola_data_peserta.php');
    }

    $stmt = $pdo->prepare(
        "UPDATE pendaftaran_lomba p
         JOIN lomba l ON l.id = p.lomba_id
         SET p.status = :status
         WHERE p.id = :id AND l.pic_id = :user_id AND p.status = 'menunggu'"
    );
    $stmt->execute([
        'status' => $status,
        'id' => $pendaftaran_id,
        'user_id' => $user_id
    ]);
    set_flash($stmt->rowCount() ? 'success' : 'error',
        $stmt->rowCount() ? 'Status peserta berhasil diperbarui.' :
        'Pendaftaran tidak ditemukan atau sudah diproses.');
    redirect('pic/kelola_data_peserta.php');
}

$stmt = $pdo->prepare(
    "SELECT p.id, p.tanggal_daftar, p.status,
            u.nama AS nama_mahasiswa, u.nim, u.email,
            l.judul AS judul_lomba
     FROM pendaftaran_lomba p
     JOIN lomba l ON l.id = p.lomba_id
     JOIN users u ON u.id = p.mahasiswa_id
     WHERE l.pic_id = ?
     ORDER BY p.tanggal_daftar DESC"
);
$stmt->execute([$user_id]);
$peserta = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Kelola Data Peserta</h1>
        <p>Periksa pendaftaran mahasiswa pada lomba yang Anda kelola.</p>
    </div>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th><th>Mahasiswa</th><th>NIM</th>
                    <th>Lomba</th><th>Tanggal Daftar</th><th>Status</th><th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$peserta): ?>
                    <tr><td colspan="7" class="empty-state">Belum ada peserta terdaftar.</td></tr>
                <?php else: ?>
                    <?php foreach ($peserta as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($item['nama_mahasiswa']) ?><br>
                                <small><?= e($item['email']) ?></small></td>
                            <td><?= e($item['nim'] ?? '-') ?></td>
                            <td><?= e($item['judul_lomba']) ?></td>
                            <td><?= e($item['tanggal_daftar'] ?? '-') ?></td>
                            <td><span class="badge badge-info"><?= e(ucfirst($item['status'])) ?></span></td>
                            <td>
                                <?php if ($item['status'] === 'menunggu'): ?>
                                    <form method="POST" class="form-actions">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="pendaftaran_id" value="<?= (int)$item['id'] ?>">
                                        <button class="btn btn-sm btn-primary" name="status"
                                            value="diterima">Terima</button>
                                        <button class="btn btn-sm btn-danger" name="status"
                                            value="ditolak">Tolak</button>
                                    </form>
                                <?php else: ?>
                                    <span>-</span>
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
