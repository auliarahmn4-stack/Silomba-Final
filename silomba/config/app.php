
<?php

/**
 * SILOMBA - Konfigurasi Aplikasi
 * File: config/app.php
 */

define('APP_NAME', 'SILOMBA');
define('APP_FULL_NAME', 'Sistem Informasi Lomba');
define('APP_VERSION', '1.0.0');

/*
|--------------------------------------------------------------------------
| Alamat dasar aplikasi
|--------------------------------------------------------------------------
| Jika folder project berada di:
| C:\xampp\htdocs\silomba
| maka BASE_URL adalah http://localhost/silomba
*/

define('BASE_URL', 'http://localhost/silomba');

/*
|--------------------------------------------------------------------------
| Zona waktu aplikasi
|--------------------------------------------------------------------------
*/

date_default_timezone_set('Asia/Jakarta');

/*
|--------------------------------------------------------------------------
| Pengaturan sesi
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'httponly' => true,
        'secure'   => !empty($_SERVER['HTTPS'])
                      && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path'     => '/silomba/',
    ]);

    session_start();
}
