<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Pengaturan';
$user_id = (int) current_user()['id'];
$errors = [];

$stmt = $pdo->prepare("SELECT nama, email, username, no_telpon, updated_at FROM users WHERE id = ? AND role = 'akama'");
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('akama/pengaturan.php');
    }
    $action = $_POST['action'] ?? 'profile';

    if ($action === 'photo' && isset($_FILES['foto'])) {
        $file = $_FILES['foto'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) { set_flash('error','Format foto tidak valid.'); redirect('akama/pengaturan.php'); }
        if ($file['size'] > 2*1024*1024) { set_flash('error','Ukuran foto maksimal 2MB.'); redirect('akama/pengaturan.php'); }
        $folder = __DIR__ . '/../uploads/profil/';
        if (!is_dir($folder)) mkdir($folder,0755,true);
        $name = bin2hex(random_bytes(16)).'.'.$allowed[$mime];
        if (move_uploaded_file($file['tmp_name'],$folder.$name)) {
            $stmt = $pdo->prepare("UPDATE users SET foto = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name,$user_id]);
            set_flash('success','Foto profil diperbarui.');
        } else {
            set_flash('error','Gagal menyimpan foto.');
        }
        redirect('akama/pengaturan.php');
    }

    if ($action === 'profile') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $no_telpon = trim($_POST['no_telpon'] ?? '');
        if ($nama === '') $errors[] = 'Nama wajib diisi.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
        if (!$errors) {
            $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, no_telpon = ?, updated_at = NOW() WHERE id = ? AND role = 'akama'");
            $stmt->execute([$nama, $email, $no_telpon ?: null, $user_id]);
            $_SESSION['user']['nama'] = $nama;
            set_flash('success', 'Profil berhasil diperbarui.');
            redirect('akama/pengaturan.php');
        }
        $profil = array_merge($profil, ['nama' => $nama, 'email' => $email, 'no_telpon' => $no_telpon]);
    } elseif ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        if (!password_verify($current, $row['password'] ?? '')) $errors[] = 'Kata sandi saat ini salah.';
        if (strlen($new) < 8) $errors[] = 'Kata sandi baru minimal 8 karakter.';
        if ($new !== $confirm) $errors[] = 'Konfirmasi kata sandi tidak sama.';
        if (!$errors) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hash, $user_id]);
            set_flash('success', 'Kata sandi berhasil diubah.');
            redirect('akama/pengaturan.php');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>
<style>
.settings-layout { display: grid; grid-template-columns: 260px 1fr; gap: 20px; }
.settings-nav { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; }
.settings-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: #1f2937; text-decoration: none; margin-bottom: 4px; }
.settings-nav a.active { background: #dbeafe; color: #1d4ed8; }
.settings-section { display: none; }
.settings-section.active { display: block; }
.profile-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; }
.avatar { width: 72px; height: 72px; border-radius: 50%; background: #1e3a8a; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; }
.toggle { position: relative; display: inline-block; width: 44px; height: 24px; }
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; border-radius: 24px; transition: .2s; }
.toggle .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; }
.toggle input:checked + .slider { background: #2563eb; }
.toggle input:checked + .slider:before { transform: translateX(20px); }
body.dark-mode { background: #0f172a; color: #e2e8f0; }
body.dark-mode .card, body.dark-mode .profile-card, body.dark-mode .settings-nav { background: #1e293b; border-color: #334155; color: #e2e8f0; }
body.dark-mode .form-control { background: #0f172a; color: #e2e8f0; border-color: #334155; }
@media (max-width: 768px) { .settings-layout { grid-template-columns: 1fr; } .form-grid { grid-template-columns: 1fr; } }
</style>

<section class="page-heading">
    <div>
        <h1>Pengaturan</h1>
        <p>Kelola profil, keamanan akun, dan preferensi sistem SILOMBA.</p>
    </div>
</section>

<div class="settings-layout">
    <nav class="settings-nav">
        <a href="#" class="active" data-tab="profil">👤 Profil Admin</a>
        <a href="#" data-tab="akun">🔐 Akun & Keamanan</a>
        <a href="#" data-tab="notif">🔔 Notifikasi</a>
        <a href="#" data-tab="preferensi">⚙️ Preferensi Sistem</a>
        <a href="#" data-tab="privasi">🛡️ Privasi & Akses</a>
    </nav>
    <div>
        <div class="settings-section active" id="profil">
            <div class="profile-card">
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                    <div class="avatar">AK</div>
                    <div>
<button type="button" class="btn btn-outline" onclick="document.getElementById('foto').click()">Ubah Foto</button>
<input type="file" id="foto" name="foto" accept="image/png,image/jpeg" style="display:none">
<form id="photoForm" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="photo">
</form>
                        <p style="font-size:12px;color:#6b7280;margin-top:6px;">JPG atau PNG, maksimal 2 MB.</p>
                    </div>
                </div>
                <?php if ($errors): ?><div class="alert alert-error"><ul><?php foreach($errors as $e):?><li><?= e($e) ?></li><?php endforeach;?></ul></div><?php endif; ?>
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="profile">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input class="form-control" name="nama" required value="<?= e($profil['nama'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Alamat Email</label>
                            <input type="email" class="form-control" name="email" required value="<?= e($profil['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input class="form-control" name="no_telpon" value="<?= e($profil['no_telpon'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input class="form-control" name="jabatan" value="Administrator AKAMA">
                        </div>
                    </div>
                    <div style="margin-top:16px;">
                        <h3>Keamanan Akun</h3>
                        <p style="font-size:13px;color:#6b7280;">Terakhir diperbarui: <?= e($profil['updated_at'] ?? '-') ?></p>
                        <p style="font-size:13px;color:#6b7280;">Kata sandi: ••••••••</p>
                    </div>
                    <div style="margin-top:20px;display:flex;gap:10px;">
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                        <a class="btn btn-outline" href="<?= e(url('akama/dashboard.php')) ?>">Batal</a>
                    </div>
                </form>
            </div>
            <div class="profile-card" style="margin-top:20px;">
                <h3>Preferensi Notifikasi</h3>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
                    <div>
                        <strong>Notifikasi email</strong>
                        <p style="font-size:13px;color:#6b7280;margin:0;">Terima informasi pengajuan lomba dan sertifikat.</p>
                    </div>
                    <label class="toggle"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
                    <div>
                        <strong>Notifikasi sistem</strong>
                        <p style="font-size:13px;color:#6b7280;margin:0;">Terima pengingat aktivitas penting di dashboard.</p>
                    </div>
                    <label class="toggle"><input type="checkbox" checked><span class="slider"></span></label>
                </div>
            </div>
        </div>

        <div class="settings-section" id="akun">
            <div class="profile-card">
                <h3>Ubah Kata Sandi</h3>
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="password">
                    <div class="form-group"><label>Kata Sandi Saat Ini</label><input type="password" class="form-control" name="current_password" required></div>
                    <div class="form-group"><label>Kata Sandi Baru</label><input type="password" class="form-control" name="new_password" required></div>
                    <div class="form-group"><label>Konfirmasi Kata Sandi</label><input type="password" class="form-control" name="confirm_password" required></div>
                    <button class="btn btn-primary" type="submit">Ubah Kata Sandi</button>
                </form>
            </div>
        </div>

<div class="settings-section" id="notif"><div class="profile-card">
<h3>Pengaturan Notifikasi</h3>
<p>Atur channel notifikasi yang ingin diterima.</p>
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;"><div><strong>Email</strong><p style="font-size:13px;color:#6b7280;margin:0;">Notifikasi via email.</p></div><label class="toggle"><input type="checkbox" checked><span class="slider"></span></label></div>
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;"><div><strong>Sistem</strong><p style="font-size:13px;color:#6b7280;margin:0;">Notifikasi di dashboard.</p></div><label class="toggle"><input type="checkbox" checked><span class="slider"></span></label></div>
</div></div>
<div class="settings-section" id="preferensi"><div class="profile-card">
<h3>Preferensi Sistem</h3>
<div class="form-group"><label>Bahasa</label><select class="form-control" id="langSelect"><option value="id">Indonesia</option><option value="en">English</option></select></div>
<div class="form-group"><label>Tema</label><select class="form-control" id="themeSelect"><option value="light">Terang</option><option value="dark">Gelap</option></select></div>
</div></div>
<div class="settings-section" id="privasi"><div class="profile-card">
<h3>Privasi & Akses</h3>
<p>Kelola visibilitas profil dan sesi aktif.</p>
<div class="form-group"><label>Sesi aktif</label><input class="form-control" value="1 sesi" disabled></div>
</div></div>
    </div>
</div>

<script>
document.getElementById('foto').addEventListener('change', function() {
    const f = this.files[0];
    if (!f) return;
    const form = document.getElementById('photoForm');
    const input = document.createElement('input');
    input.type = 'file'; input.name = 'foto'; 
    // can't move file easily; instead use FormData
    const fd = new FormData();
    fd.append('csrf_token', '<?= e(csrf_token()) ?>');
    fd.append('action', 'photo');
    fd.append('foto', f);
    fetch('pengaturan.php', { method: 'POST', body: fd }).then(() => location.reload());
});
document.getElementById('langSelect').addEventListener('change', function() {
    localStorage.setItem('lang', this.value);
    alert('Bahasa diubah ke ' + (this.value === 'en' ? 'English' : 'Indonesia'));
});
document.getElementById('themeSelect').addEventListener('change', function() {
    const dark = this.value === 'dark';
    document.body.classList.toggle('dark-mode', dark);
    localStorage.setItem('theme', this.value);
});
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    document.getElementById('themeSelect').value = 'dark';
}
document.querySelectorAll('.settings-nav a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.settings-nav a').forEach(x => x.classList.remove('active'));
        a.classList.add('active');
        document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
        document.getElementById(a.dataset.tab).classList.add('active');
    });
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>