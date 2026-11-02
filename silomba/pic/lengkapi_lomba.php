
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Lengkapi Detail Lomba';
$user_id = (int) current_user()['id'];

$lomba_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$lomba_id) {
    set_flash('error', 'ID lomba tidak valid.');
    redirect('pic/kelola_informasi_lomba.php');
}

$stmt = $pdo->prepare(
    "SELECT * FROM lomba
     WHERE id = ? AND pic_id = ? AND status = 'disetujui'
     LIMIT 1"
);
$stmt->execute([$lomba_id, $user_id]);
$lomba = $stmt->fetch();

if (!$lomba) {
    set_flash('error', 'Lomba tidak ditemukan, bukan milik Anda, atau belum disetujui AKAMA.');
    redirect('pic/kelola_informasi_lomba.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/lengkapi_lomba.php?id=' . $lomba_id);
    }

    $link_pendaftaran = trim($_POST['link_pendaftaran'] ?? '');
    $link_panduan = trim($_POST['link_panduan'] ?? '');
    $kontak_person = trim($_POST['kontak_person'] ?? '');

    if ($link_pendaftaran === '' && $link_panduan === '' && $kontak_person === '') {
        $errors[] = 'Isi minimal satu field detail tambahan.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                 "UPDATE lomba
                 SET link_pendaftaran = :link_pendaftaran,
                     link_panduan = :link_panduan,
                     kontak_person = :kontak_person,
                     status = 'siap_publikasi',
                     updated_at = NOW()
                 WHERE id = :lomba_id AND pic_id = :user_id AND status = 'disetujui'"
            );
            $stmt->execute([
                ':link_pendaftaran' => $link_pendaftaran !== '' ? $link_pendaftaran : null,
                ':link_panduan' => $link_panduan !== '' ? $link_panduan : null,
                ':kontak_person' => $kontak_person !== '' ? $kontak_person : null,
                ':lomba_id' => $lomba_id,
                ':user_id' => $user_id
            ]);

            set_flash('success', 'Detail lomba berhasil dilengkapi. Menunggu publikasi final oleh AKAMA.');
            redirect('pic/kelola_informasi_lomba.php');
        } catch (PDOException $e) {
            $errors[] = 'Gagal menyimpan detail lomba. Periksa struktur database.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Lengkapi Detail Lomba</h1>
        <p>Isi informasi tambahan sebelum lomba dipublikasikan oleh AKAMA.</p>
    </div>
    <a href="<?= e(url('pic/kelola_informasi_lomba.php')) ?>" class="btn btn-outline">Kembali</a>
</section>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <div>
            <h2><?= e($lomba['judul']) ?></h2>
            <p>Status: <span class="badge badge-success">Disetujui AKAMA</span></p>
        </div>
    </div>

    <form method="POST" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="link_pendaftaran">Link Pendaftaran Lomba</label>
            <input type="url" class="form-control" id="link_pendaftaran"
                name="link_pendaftaran"
                placeholder="https://pendaftaran.lomba.ac.id"
                value="<?= e($_POST['link_pendaftaran'] ?? $lomba['link_pendaftaran'] ?? '') ?>">
            <div class="form-help">Contoh: https://pendaftaran.lomba.ac.id/daftar</div>
        </div>

        <div class="form-group">
            <label for="link_panduan">Link Panduan Peserta</label>
            <input type="url" class="form-control" id="link_panduan"
                name="link_panduan"
                placeholder="https://panduan.lomba.ac.id"
                value="<?= e($_POST['link_panduan'] ?? $lomba['link_panduan'] ?? '') ?>">
            <div class="form-help">Contoh: https://panduan.lomba.ac.id/panduan-peserta</div>
        </div>

        <div class="form-group">
            <label for="kontak_person">Kontak Person</label>
            <input type="text" class="form-control" id="kontak_person"
                name="kontak_person"
                placeholder="Nama dan kontak person yang dapat dihubungi"
                value="<?= e($_POST['kontak_person'] ?? $lomba['kontak_person'] ?? '') ?>">
            <div class="form-help">Contoh: Budi Santono (08123456789) - budi@kampus.ac.id</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Kirim untuk Publikasi</button>
            <a href="<?= e(url('pic/kelola_informasi_lomba.php')) ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
