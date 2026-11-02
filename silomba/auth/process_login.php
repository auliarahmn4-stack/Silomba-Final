
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('auth/login.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Sesi login tidak valid. Silakan coba kembali.');
    redirect('auth/login.php');
}

$identity = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($identity === '' || $password === '') {
    set_flash('error', 'Username/email dan kata sandi wajib diisi.');
    redirect('auth/login.php');
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, nama, nim, email, username, password, role, status
         FROM users
         WHERE username = :username OR email = :email
         LIMIT 1'
    );

    $stmt->execute([
        'username' => $identity,
        'email' => strtolower($identity),
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $password !== $user['password']) {
        set_flash('error', 'Username/email atau kata sandi salah.');
        redirect('auth/login.php');
    }

    if (($user['status'] ?? '') !== 'aktif') {
        set_flash(
            'error',
            'Akun Anda tidak aktif. Hubungi administrator.'
        );
        redirect('auth/login.php');
    }

    $allowedRoles = ['akama', 'pic', 'mahasiswa'];

    if (!in_array($user['role'], $allowedRoles, true)) {
        set_flash('error', 'Peran akun tidak dikenali.');
        redirect('auth/login.php');
    }

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nama' => $user['nama'],
        'username' => $user['username'],
        'role' => $user['role'],
    ];

    $destination = [
        'akama' => 'akama/dashboard.php',
        'pic' => 'pic/dashboard.php',
        'mahasiswa' => 'mahasiswa/dashboard.php',
    ];

    redirect($destination[$user['role']]);

} catch (PDOException $e) {
    error_log('Login SILOMBA gagal: ' . $e->getMessage());

    set_flash(
        'error',
        'Terjadi kesalahan saat mengakses database. Silakan coba kembali.'
    );

    redirect('auth/login.php');
}
