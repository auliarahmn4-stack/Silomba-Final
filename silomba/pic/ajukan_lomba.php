
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Ajukan Lomba';
$user_id = (int) current_user()['id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/ajukan_lomba.php');
    }

    $judul = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $penyelenggara = trim($_POST['penyelenggara'] ?? '');
    $tingkat = trim($_POST['tingkat'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $deadline = $_POST['deadline_pendaftaran'] ?? '';
    $poster = null;

    if ($judul === '' || $penyelenggara === '' || $deskripsi === '') {
        $errors[] = 'Nama lomba, penyelenggara, dan deskripsi wajib diisi.';
    }

    foreach ([
        'Tanggal mulai' => $tanggal_mulai,
        'Tanggal selesai' => $tanggal_selesai,
        'Deadline pendaftaran' => $deadline
    ] as $label => $date) {
        $parsed = DateTime::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) {
            $errors[] = $label . ' tidak valid.';
        }
    }

    if (!$errors && ($tanggal_selesai < $tanggal_mulai || $deadline > $tanggal_mulai)) {
        $errors[] = 'Periksa kembali urutan deadline dan tanggal pelaksanaan.';
    }

    if (!empty($_FILES['poster']['name'])) {
        $file = $_FILES['poster'];

        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Poster gagal diunggah atau ukurannya melebihi 3 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($extensions[$mime])) {
                $errors[] = 'Poster harus berformat JPG, PNG, atau WEBP.';
            } else {
                $folder = __DIR__ . '/../uploads/poster/';

                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }

                $poster = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];

                if (!move_uploaded_file($file['tmp_name'], $folder . $poster)) {
                    $poster = null;
                    $errors[] = 'Poster tidak dapat disimpan.';
                }
            }
        }
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO lomba
                (pic_id, judul, kategori, deskripsi, penyelenggara, tingkat,
                 lokasi, tanggal_mulai, tanggal_selesai, deadline_pendaftaran,
                 poster, status, created_at, updated_at)
                VALUES
                (:pic_id, :judul, :kategori, :deskripsi, :penyelenggara, :tingkat,
                 :lokasi, :mulai, :selesai, :deadline,
                 :poster, 'menunggu', NOW(), NOW())"
            );

            $stmt->execute([
                'pic_id' => $user_id,
                'judul' => $judul,
                'kategori' => $kategori ?: null,
                'deskripsi' => $deskripsi,
                'penyelenggara' => $penyelenggara,
                'tingkat' => $tingkat ?: null,
                'lokasi' => $lokasi ?: null,
                'mulai' => $tanggal_mulai,
                'selesai' => $tanggal_selesai,
                'deadline' => $deadline,
                'poster' => $poster
            ]);

            set_flash('success', 'Pengajuan lomba berhasil dikirim ke AKAMA.');
            redirect('pic/kelola_informasi_lomba.php');
        } catch (PDOException $e) {
            if ($poster && is_file(__DIR__ . '/../uploads/poster/' . $poster)) {
                unlink(__DIR__ . '/../uploads/poster/' . $poster);
            }
            $errors[] = 'Pengajuan gagal disimpan. Periksa struktur database.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div>
        <h1>Ajukan Lomba</h1>
        <p>Lengkapi informasi kegiatan untuk diajukan kepada AKAMA.</p>
    </div>
</section>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="card">
    <form method="POST" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="judul">Nama Lomba *</label>
            <input class="form-control" id="judul" name="judul" required
                maxlength="200" value="<?= e($_POST['judul'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="penyelenggara">Penyelenggara *</label>
            <input class="form-control" id="penyelenggara" name="penyelenggara"
                required value="<?= e($_POST['penyelenggara'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <input class="form-control" id="kategori" name="kategori"
                placeholder="Contoh: Akademik, Olahraga, Seni"
                value="<?= e($_POST['kategori'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="tingkat">Tingkat Lomba</label>
            <select class="form-control" id="tingkat" name="tingkat">
                <?php foreach (['Lokal', 'Kampus', 'Kota', 'Provinsi', 'Nasional', 'Internasional'] as $item): ?>
                    <option value="<?= e($item) ?>"
                        <?= ($_POST['tingkat'] ?? '') === $item ? 'selected' : '' ?>>
                        <?= e($item) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Lomba *</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi"
                rows="5" required><?= e($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="lokasi">Lokasi atau Tautan Pelaksanaan</label>
            <input class="form-control" id="lokasi" name="lokasi"
                value="<?= e($_POST['lokasi'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="deadline_pendaftaran">Deadline Pendaftaran *</label>
            <input type="date" class="form-control" id="deadline_pendaftaran"
                name="deadline_pendaftaran" required
                value="<?= e($_POST['deadline_pendaftaran'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai *</label>
            <input type="date" class="form-control" id="tanggal_mulai"
                name="tanggal_mulai" required
                value="<?= e($_POST['tanggal_mulai'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="tanggal_selesai">Tanggal Selesai *</label>
            <input type="date" class="form-control" id="tanggal_selesai"
                name="tanggal_selesai" required
                value="<?= e($_POST['tanggal_selesai'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="poster">Poster (JPG, PNG, WEBP; maksimal 3 MB)</label>
            <input type="file" class="form-control" id="poster" name="poster"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            <a href="<?= e(url('pic/kelola_informasi_lomba.php')) ?>"
               class="btn btn-outline">Batal</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
