
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Daftar Lomba';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('akama/daftar_lomba.php');
    }

    $id = filter_input(INPUT_POST, 'lomba_id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';

    if (!$id || !in_array($aksi, ['publikasikan', 'selesaikan'], true)) {
        set_flash('error', 'Permintaan tidak valid.');
        redirect('akama/daftar_lomba.php');
    }

    $status_asal = $aksi === 'publikasikan' ? 'disetujui' : 'dipublikasikan';
    $status_tujuan = $aksi === 'publikasikan' ? 'dipublikasikan' : 'selesai';

    try {
        $stmt = $pdo->prepare(
            "UPDATE lomba SET status = :tujuan, updated_at = NOW()
             WHERE id = :id AND status = :asal"
        );
        $stmt->execute([
            'tujuan' => $status_tujuan,
            'id' => $id,
            'asal' => $status_asal
        ]);

        set_flash(
            $stmt->rowCount() > 0 ? 'success' : 'error',
            $stmt->rowCount() > 0
                ? 'Status lomba berhasil diperbarui.'
                : 'Status lomba tidak berubah atau data tidak ditemukan.'
        );
    } catch (PDOException $e) {
        set_flash('error', 'Gagal memperbarui status lomba.');
    }

    redirect('akama/daftar_lomba.php');
}

try {
    $stmt = $pdo->query(
        "SELECT l.*, u.nama AS nama_pic
         FROM lomba l
         LEFT JOIN users u ON u.id = l.pic_id
         ORDER BY l.created_at DESC"
    );
    $lomba_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $lomba_list = [];
    set_flash('error', 'Data lomba tidak dapat dimuat.');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Daftar Lomba</h1>
        <p>Pantau status lomba dan publikasikan pengajuan yang telah disetujui.</p>
    </div>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Penyelenggara</th>
                    <th>PIC</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$lomba_list): ?>
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data lomba.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lomba_list as $i => $lomba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($lomba['judul']) ?></td>
                            <td><?= e($lomba['penyelenggara'] ?? '-') ?></td>
                            <td><?= e($lomba['nama_pic'] ?? '-') ?></td>
                            <td><?= e($lomba['deadline_pendaftaran'] ?? '-') ?></td>
                            <td><span class="badge badge-info">
                                <?= e(ucfirst($lomba['status'])) ?>
                            </span></td>
                            <td>
                                <?php if ($lomba['status'] === 'disetujui'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token"
                                            value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="lomba_id"
                                            value="<?= (int)$lomba['id'] ?>">
                                        <button class="btn btn-sm btn-primary"
                                            name="aksi" value="publikasikan"
                                            data-confirm="Publikasikan lomba ini?">
                                            Publikasikan
                                        </button>
                                    </form>
                                <?php elseif ($lomba['status'] === 'dipublikasikan'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token"
                                            value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="lomba_id"
                                            value="<?= (int)$lomba['id'] ?>">
                                        <button class="btn btn-sm btn-outline"
                                            name="aksi" value="selesaikan"
                                            data-confirm="Tandai lomba ini selesai?">
                                            Tandai Selesai
                                        </button>
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
