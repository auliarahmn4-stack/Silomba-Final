
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Notifikasi';
$user_id = (int) current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/notifikasi.php');
    }

    $id = filter_input(INPUT_POST, 'notifikasi_id', FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $pdo->prepare(
            "UPDATE notifikasi SET dibaca_at = NOW()
             WHERE id = ? AND user_id = ? AND dibaca_at IS NULL"
        );
        $stmt->execute([$id, $user_id]);
        set_flash('success', 'Notifikasi diperbarui.');
    }

    redirect('pic/notifikasi.php');
}

$stmt = $pdo->prepare(
    "SELECT * FROM notifikasi WHERE user_id = ? ORDER BY created_at DESC"
);
$stmt->execute([$user_id]);
$notifikasi = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div><h1>Notifikasi</h1><p>Informasi terbaru mengenai pengajuan dan pengelolaan lomba.</p></div>
</section>

<section class="card">
    <?php if (!$notifikasi): ?>
        <p class="empty-state">Belum ada notifikasi.</p>
    <?php else: ?>
        <?php foreach ($notifikasi as $item): ?>
            <article class="notification-item">
                <div>
                    <h3><?= e($item['judul']) ?></h3>
                    <p><?= nl2br(e($item['pesan'])) ?></p>
                    <small><?= e($item['created_at']) ?></small>
                    <?php if (!empty($item['tautan'])): ?>
                        <p><a href="<?= e(url(ltrim($item['tautan'], '/'))) ?>">Lihat detail</a></p>
                    <?php endif; ?>
                </div>
                <?php if (empty($item['dibaca_at'])): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="notifikasi_id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline" type="submit">Tandai Dibaca</button>
                    </form>
                <?php else: ?>
                    <span class="badge badge-success">Dibaca</span>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
