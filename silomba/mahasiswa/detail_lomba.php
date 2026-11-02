<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Detail Lomba';
$lomba_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$lomba_id) { set_flash('error','Lomba tidak valid.'); redirect('mahasiswa/informasi_lomba.php'); }

$stmt = $pdo->prepare("SELECT * FROM lomba WHERE id = ? AND status IN ('dipublikasikan','selesai')");
$stmt->execute([$lomba_id]);
$lomba = $stmt->fetch();
if (!$lomba) { set_flash('error','Lomba tidak ditemukan.'); redirect('mahasiswa/informasi_lomba.php'); }

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>
<section class="page-heading">
    <div><h1><?= e($lomba['judul']) ?></h1><p>Detail lomba.</p></div>
    <a href="javascript:history.back()" class="btn btn-outline">Kembali</a>
</section>
<section class="card">
    <p><strong>Penyelenggara:</strong> <?= e($lomba['penyelenggara'] ?? '-') ?></p>
    <p><strong>Kategori:</strong> <?= e($lomba['kategori'] ?? '-') ?></p>
    <p><strong>Tingkat:</strong> <?= e($lomba['tingkat'] ?? '-') ?></p>
    <p><strong>Lokasi:</strong> <?= e($lomba['lokasi'] ?? '-') ?></p>
    <p><strong>Periode:</strong> <?= e($lomba['tanggal_mulai'] ?? '-') ?> s/d <?= e($lomba['tanggal_selesai'] ?? '-') ?></p>
    <p><strong>Deadline:</strong> <?= e($lomba['deadline_pendaftaran'] ?? '-') ?></p>
    <p><?= nl2br(e($lomba['deskripsi'] ?? '')) ?></p>
    <?php if (!empty($lomba['link_pendaftaran'])): ?><p><strong>Link Pendaftaran:</strong> <a href="<?= e($lomba['link_pendaftaran']) ?>" target="_blank"><?= e($lomba['link_pendaftaran']) ?></a></p><?php endif; ?>
    <?php if (!empty($lomba['link_panduan'])): ?><p><strong>Link Panduan:</strong> <a href="<?= e($lomba['link_panduan']) ?>" target="_blank"><?= e($lomba['link_panduan']) ?></a></p><?php endif; ?>
    <?php if (!empty($lomba['kontak_person'])): ?><p><strong>Kontak Person:</strong> <?= e($lomba['kontak_person']) ?></p><?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>