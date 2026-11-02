
<?php

/**
 * SILOMBA - Autentikasi Pengguna
 * File: includes/auth.php
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Memeriksa apakah pengguna sudah login.
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user'])
        && isset($_SESSION['user']['id'])
        && isset($_SESSION['user']['role']);
}

/**
 * Mengambil data pengguna yang sedang login.
 */
function current_user(): ?array
{
    return is_logged_in()
        ? $_SESSION['user']
        : null;
}

/**
 * Memastikan pengguna sudah login.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        set_flash(
            'error',
            'Silakan login terlebih dahulu.'
        );

        redirect('auth/login.php');
    }
}

/**
 * Mengambil role pengguna yang sedang login.
 */
function current_role(): ?string
{
    $user = current_user();

    return $user['role'] ?? null;
}

/**
 * Memastikan pengguna masih memiliki akun aktif.
 */
function refresh_user_status(): void
{
    if (!is_logged_in()) {
        return;
    }

    global $pdo;

    $stmt = $pdo->prepare(
        'SELECT id, username, nama, role, status
         FROM users
         WHERE id = :id
         LIMIT 1'
    );

    $stmt->execute([
        'id' => $_SESSION['user']['id'],
    ]);

    $user = $stmt->fetch();

    if (
        !$user
        || $user['status'] !== 'aktif'
    ) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        session_destroy();

        redirect('auth/login.php');
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'username' => $user['username'],
        'nama' => $user['nama'],
        'role' => $user['role'],
    ];
}
