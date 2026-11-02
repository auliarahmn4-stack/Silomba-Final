
<?php
// SILOMBA - Setup Database & Akun Default (jalankan sekali via browser, lalu hapus)
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$messages = [];

// 1. Tambahkan kolom baru ke tabel lomba (tidak menghapus data lama)
try {
    $pdo->exec(
        "ALTER TABLE `lomba`
         ADD COLUMN `link_pendaftaran` VARCHAR(255) NULL DEFAULT NULL AFTER `deskripsi`,
         ADD COLUMN `link_panduan` VARCHAR(255) NULL DEFAULT NULL AFTER `link_pendaftaran`,
         ADD COLUMN `kontak_person` VARCHAR(255) NULL DEFAULT NULL AFTER `link_panduan`"
    );
    $messages[] = "<span style='color:green'>[OK]</span> Kolom baru ditambahkan ke tabel <b>lomba</b>.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        $messages[] = "<span style='color:orange'>[INFO]</span> Kolom baru sudah ada di tabel <b>lomba</b>.";
    } else {
        $messages[] = "<span style='color:red'>[ERROR]</span> Gagal menambah kolom: " . $e->getMessage();
    }
}

// 2. Buat/update akun default (tidak menghapus data user lain)
$users = [
    ['nama'=>'Administrator AKAMA','nim'=>null,'email'=>'akama@kampus.ac.id','username'=>'akama','password'=>'Silomba@123','role'=>'akama','status'=>'aktif'],
    ['nama'=>'Pengelola PIC','nim'=>null,'email'=>'pic@kampus.ac.id','username'=>'pic','password'=>'Silomba@123','role'=>'pic','status'=>'aktif'],
    ['nama'=>'Mahasiswa Contoh','nim'=>'230001','email'=>'mahasiswa@kampus.ac.id','username'=>'mahasiswa','password'=>'Silomba@123','role'=>'mahasiswa','status'=>'aktif'],
];

try {
    foreach ($users as $u) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (nama,nim,email,username,password,role,status,created_at)
             VALUES (:nama,:nim,:email,:username,:password,:role,:status,NOW())
             ON DUPLICATE KEY UPDATE nama=VALUES(nama),nim=VALUES(nim),email=VALUES(email),
             password=VALUES(password),role=VALUES(role),status=VALUES(status)'
        );
        $stmt->execute(['nama'=>$u['nama'],'nim'=>$u['nim'],'email'=>$u['email'],
            'username'=>$u['username'],'password'=>$u['password'],'role'=>$u['role'],'status'=>$u['status']]);
        $messages[] = "<span style='color:green'>[OK]</span> Akun <b>{$u['username']}</b> ({$u['role']}) disimpan.";
    }
} catch (PDOException $e) {
    $messages[] = "<span style='color:red'>[ERROR]</span> Gagal menyimpan akun: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup SILOMBA</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<div class="app-layout">
    <header class="topbar">
        <div class="topbar-brand">
            <span class="brand-icon">🔧</span>
            <div class="topbar-brand-text">
                <strong>SILOMBA Setup</strong>
                <span>Database &amp; Akun</span>
            </div>
        </div>
    </header>

    <main class="main-content" style="margin-left:0; padding:40px;">
        <h1>Setup Database &amp; Akun Default</h1>
        <p>Semua perubahan telah disimpan ke database. Data lama tidak terhapus.</p>

        <div class="card">
            <div class="card-body">
                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-info" style="margin:8px 0;"><?= $msg ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-20">
            <a href="auth/login.php" class="btn btn-primary">Buka Halaman Login</a>
            <a href="setup_users.php" class="btn btn-outline" style="margin-left:8px;">Lihat Detail Setup</a>
        </div>

        <p class="mt-20 text-muted">
            <b>Penting:</b> Hapus file <code>setup_users.php</code> setelah selesai untuk alasan keamanan.
        </p>
    </main>
</div>
</body>
</html>
