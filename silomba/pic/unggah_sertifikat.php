
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Unggah Sertifikat';
$user_id = (int) current_user()['id'];
$errors = [];

$stmt = $pdo->prepare(
    "SELECT p.id AS pendaftaran_id, p.mahasiswa_id,
            u.nama AS nama_mahasiswa, u.nim,
            l.id AS lomba_id, l.judul
     FROM pendaftaran_lomba p
     JOIN lomba l ON l.id = p.lomba_id
     JOIN users u ON u.id = p.mahasiswa_id
     LEFT JOIN sertifikat s ON s.pendaftaran_id = p.id
     WHERE l.pic_id = ?
       AND p.status IN ('diterima', 'selesai')
       AND s.id IS NULL
     ORDER BY l.judul, u.nama"
);
$stmt->execute([$user_id]);
$pilihan_peserta = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/unggah_sertifikat.php');
    }

    $pendaftaran_id = filter_input(INPUT_POST, 'pendaftaran_id', FILTER_VALIDATE_INT);
    $nomor = trim($_POST['nomor_sertifikat'] ?? '');

    if (!$pendaftaran_id || $nomor === '') {
        $errors[] = 'Peserta dan nomor sertifikat wajib diisi.';
    }

    $file_name = null;

    if (empty($_FILES['file_sertifikat']['name']) ||
        $_FILES['file_sertifikat']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File sertifikat PDF wajib diunggah.';
    } elseif ($_FILES['file_sertifikat']['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Ukuran file maksimal 5 MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if ($finfo->file($_FILES['file_sertifikat']['tmp_name']) !== 'application/pdf') {
            $errors[] = 'File sertifikat harus berupa PDF.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "SELECT p.id, p.mahasiswa_id, l.id AS lomba_id
             FROM pendaftaran_lomba p
             JOIN lomba l ON l.id = p.lomba_id
             LEFT JOIN sertifikat s ON s.pendaftaran_id = p.id
             WHERE p.id = ? AND l.pic_id = ?
               AND p.status IN ('diterima', 'selesai')
               AND s.id IS NULL"
        );
        $stmt->execute([$pendaftaran_id, $user_id]);
        $target = $stmt->fetch();

        if (!$target) {
            $errors[] = 'Peserta tidak valid, bukan peserta lomba Anda, atau sertifikat sudah tersedia.';
        }
    }

    if (!$errors) {
        $folder = __DIR__ . '/../uploads/sertifikat/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $file_name = bin2hex(random_bytes(16)) . '.pdf';

        if (!move_uploaded_file($_FILES['file_sertifikat']['tmp_name'], $folder . $file_name)) {
            $errors[] = 'File gagal disimpan.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO sertifikat
                    (pendaftaran_id, lomba_id, mahasiswa_id, pic_id,
                     nomor_sertifikat, file_sertifikat, tanggal_unggah, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, CURDATE(), NOW())"
                );
                $stmt->execute([
                    $target['id'],
                    $target['lomba_id'],
                    $target['mahasiswa_id'],
                    $user_id,
                    $nomor,
                    $file_name
                ]);

                set_flash('success', 'Sertifikat berhasil diunggah.');
                redirect('pic/unggah_sertifikat.php');
            } catch (PDOException $e) {
                if (is_file($folder . $file_name)) {
                    unlink($folder . $file_name);
                }
                $errors[] = 'Sertifikat gagal disimpan ke database.';
            }
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT s.*, u.nama AS nama_mahasiswa, l.judul
     FROM sertifikat s
     JOIN users u ON u.id = s.mahasiswa_id
     JOIN lomba l ON l.id = s.lomba_id
     WHERE s.pic_id = ?
     ORDER BY s.created_at DESC"
);
$stmt->execute([$user_id]);
$sertifikat_list = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Unggah Sertifikat</h1>
        <p>Unggah sertifikat PDF bagi peserta lomba yang memenuhi syarat.</p>
    </div>
</section>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="card">
    <div class="card-header"><h2>Form Unggah Sertifikat</h2></div>

    <form method="POST" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="pendaftaran_id">Pilih Peserta</label>
            <select class="form-control" name="pendaftaran_id" id="pendaftaran_id" required>
                <option value="">-- Pilih peserta --</option>
                <?php foreach ($pilihan_peserta as $item): ?>
                    <option value="<?= (int)$item['pendaftaran_id'] ?>">
                        <?= e($item['nama_mahasiswa'] . ' - ' . ($item['nim'] ?? '-') . ' | ' . $item['judul']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nomor_sertifikat">Nomor Sertifikat</label>
            <input class="form-control" id="nomor_sertifikat" name="nomor_sertifikat"
                maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="file_sertifikat">File Sertifikat PDF (maksimal 5 MB)</label>
            <input type="file" class="form-control" id="file_sertifikat"
                name="file_sertifikat" accept="application/pdf,.pdf" required>
        </div>

        <button class="btn btn-primary" type="submit">Unggah Sertifikat</button>
    </form>
</section>

<section class="card">
    <div class="card-header"><h2>Sertifikat yang Telah Diunggah</h2></div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>No.</th><th>Nomor</th><th>Peserta</th><th>Lomba</th><th>Tanggal</th></tr></thead>
            <tbody>
                <?php if (!$sertifikat_list): ?>
                    <tr><td colspan="5" class="empty-state">Belum ada sertifikat yang diunggah.</td></tr>
                <?php else: foreach ($sertifikat_list as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($s['nomor_sertifikat']) ?></td>
                        <td><?= e($s['nama_mahasiswa']) ?></td>
                        <td><?= e($s['judul']) ?></td>
                        <td><?= e($s['tanggal_unggah'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
