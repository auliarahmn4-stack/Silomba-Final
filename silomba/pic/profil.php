
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('pic');

$page_title = 'Profil PIC';
$user_id = (int) current_user()['id'];
$errors = [];

$stmt = $pdo->prepare(
    "SELECT id, nama, email, username, no_telpon, foto, updated_at
     FROM users WHERE id = ? AND role = 'pic'"
);
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

if (!$profil) {
    set_flash('error', 'Profil tidak ditemukan.');
    redirect('pic/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('pic/profil.php');
    }
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $mime = mime_content_type($file['tmp_name']);
        if (isset($allowed[$mime]) && $file['size'] <= 2*1024*1024) {
            $folder = __DIR__ . '/../uploads/profil/';
            if (!is_dir($folder)) mkdir($folder,0755,true);
            $name = bin2hex(random_bytes(16)).'.'.$allowed[$mime];
            if (move_uploaded_file($file['tmp_name'],$folder.$name)) {
                $stmt = $pdo->prepare("UPDATE users SET foto = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$name,$user_id]);
                set_flash('success','Foto profil diperbarui.');
            }
        } else {
            set_flash('error','Format/ukuran foto tidak valid.');
        }
        redirect('pic/profil.php');
    }


    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_telpon = trim($_POST['no_telpon'] ?? '');

    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';

    if (!$errors) {
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ? AND id <> ?"
        );
        $stmt->execute([$email, $user_id]);

        if ($stmt->fetch()) {
            $errors[] = 'Email sudah digunakan akun lain.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "UPDATE users SET nama = ?, email = ?, no_telpon = ?, updated_at = NOW()
             WHERE id = ? AND role = 'pic'"
        );
        $stmt->execute([$nama, $email, $no_telpon ?: null, $user_id]);

        $_SESSION['user']['nama'] = $nama;
        set_flash('success', 'Profil berhasil diperbarui.');
        redirect('pic/profil.php');
    }

    $profil['nama'] = $nama;
    $profil['email'] = $email;
    $profil['no_telpon'] = $no_telpon;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_pic.php';
?>

<section class="page-heading">
    <div><h1>Profil PIC</h1><p>Perbarui informasi akun Anda.</p></div>
</section>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="card">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
        <div style="width:72px;height:72px;border-radius:50%;background:#1e3a8a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;">PIC</div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="file" name="foto" accept="image/png,image/jpeg">
            <button class="btn btn-sm btn-outline" type="submit">Ubah Foto</button>
        </form>
    </div>
    <form method="POST" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input class="form-control" id="nama" name="nama" required
                value="<?= e($profil['nama']) ?>">
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" value="<?= e($profil['username']) ?>" disabled>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required
                value="<?= e($profil['email']) ?>">
        </div>

        <div class="form-group">
            <label for="no_telpon">Nomor Telepon</label>
            <input class="form-control" id="no_telpon" name="no_telpon"
                value="<?= e($profil['no_telpon'] ?? '') ?>">
        </div>

        <button class="btn btn-primary" type="submit">Simpan Profil</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
