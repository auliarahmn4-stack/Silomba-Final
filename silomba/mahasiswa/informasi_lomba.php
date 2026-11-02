
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Informasi Lomba';
$user_id = (int)current_user()['id'];
$cari = trim($_GET['cari'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$sql = "SELECT l.*,
        (SELECT COUNT(*) FROM pendaftaran_lomba p
         WHERE p.lomba_id = l.id AND p.mahasiswa_id = :uid) AS sudah_daftar
        FROM lomba l
        WHERE l.status = 'dipublikasikan'
          AND l.deadline_pendaftaran >= CURDATE()";
$params = ['uid' => $user_id];

if ($cari !== '') {
    $sql .= " AND (l.judul LIKE :cari OR l.penyelenggara LIKE :cari2)";
    $params['cari'] = "%{$cari}%";
    $params['cari2'] = "%{$cari}%";
}
if ($kategori !== '') {
    $sql .= " AND l.kategori = :kategori";
    $params['kategori'] = $kategori;
}
$sql .= " ORDER BY l.deadline_pendaftaran ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lomba_list = $stmt->fetchAll();

$stmt = $pdo->query(
    "SELECT DISTINCT kategori FROM lomba
     WHERE status = 'dipublikasikan' AND kategori IS NOT NULL AND kategori <> ''
     ORDER BY kategori"
);
$kategori_list = $stmt->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>

<section class="page-heading">
    <div><h1>Informasi Lomba</h1><p>Jelajahi informasi lomba yang dipublikasikan.</p></div>
</section>

<section class="card">
    <form method="GET" class="filter-form">
        <input class="form-control" type="search" name="cari"
            placeholder="Cari nama lomba atau penyelenggara..."
            value="<?= e($cari) ?>">
        <select class="form-control" name="kategori">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori_list as $item): ?>
                <option value="<?= e($item) ?>" <?= $kategori === $item ? 'selected' : '' ?>>
                    <?= e($item) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary">Cari</button>
        <a class="btn btn-outline" href="<?= e(url('mahasiswa/informasi_lomba.php')) ?>">Reset</a>
    </form>
</section>

<section class="competition-grid" style="display:flex;flex-direction:column;gap:16px;">
    <?php if (!$lomba_list): ?>
        <div class="card empty-state">Tidak ada lomba yang cocok dengan pencarian Anda.</div>
    <?php else: foreach ($lomba_list as $lomba): ?>
<article class="card competition-card" style="display:flex;gap:16px;align-items:flex-start;">
    <?php if (!empty($lomba['poster'])): ?>
        <a href="<?= e(url('mahasiswa/detail_lomba.php?id=' . (int)$lomba['id'])) ?>" style="flex:0 0 160px;">
            <img class="competition-poster" style="width:160px;height:120px;object-fit:cover;" src="<?= e(url('uploads/poster/' . basename($lomba['poster']))) ?>" alt="Poster <?= e($lomba['judul']) ?>">
        </a>
    <?php endif; ?>
    <div class="competition-content" style="flex:1;">
                <span class="badge badge-info"><?= e($lomba['kategori'] ?? 'Lomba') ?></span>
                <h2><?= e($lomba['judul']) ?></h2>
                <p><?= e(mb_strimwidth($lomba['deskripsi'] ?? '', 0, 150, '...')) ?></p>
                <p><strong>Penyelenggara:</strong> <?= e($lomba['penyelenggara']) ?></p>
                <p><strong>Tingkat:</strong> <?= e($lomba['tingkat'] ?? '-') ?></p>
                <p><strong>Deadline:</strong> <?= e($lomba['deadline_pendaftaran']) ?></p>
                <?php if ($lomba['sudah_daftar']): ?>
                    <span class="badge badge-success">Sudah Mendaftar</span>
                <?php else: ?>
                    <a class="btn btn-outline" href="<?= e(url('mahasiswa/detail_lomba.php?id=' . (int)$lomba['id'])) ?>">Detail</a>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
