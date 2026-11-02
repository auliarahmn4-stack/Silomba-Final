
<?php

/**
 * SILOMBA - Fungsi Umum
 * File: includes/functions.php
 */

/**
 * Mengamankan teks sebelum ditampilkan dalam HTML.
 */
function e($value): string
{
    return htmlspecialchars(
        is_array($value) ? implode(', ', $value) : (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Membuat URL menuju halaman aplikasi.
 */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/'
        . ltrim($path, '/');
}

/**
 * Mengalihkan pengguna ke halaman lain.
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Menyimpan pesan sementara untuk ditampilkan.
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Mengambil pesan sementara satu kali.
 */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

/**
 * Membuat token CSRF untuk keamanan formulir.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}

/**
 * Memeriksa token CSRF yang dikirim melalui formulir.
 */
function verify_csrf_token(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals(
            $_SESSION['csrf_token'],
            $token
        );
}
