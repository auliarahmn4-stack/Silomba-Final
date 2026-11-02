
<?php

/**
 * SILOMBA - Pembatasan Hak Akses
 * File: includes/role.php
 */

require_once __DIR__ . '/auth.php';

/**
 * Memastikan pengguna mempunyai role yang diizinkan.
 *
 * Contoh:
 * require_role('akama');
 * require_role('pic');
 * require_role('mahasiswa');
 */
function require_role(string ...$allowed_roles): void
{
    require_login();
    refresh_user_status();

    $role = current_role();

    if (
        $role === null
        || !in_array($role, $allowed_roles, true)
    ) {
        http_response_code(403);

        exit(
            '403 Forbidden: Anda tidak memiliki '
            . 'izin untuk mengakses halaman ini.'
        );
    }
}

/**
 * Mengalihkan pengguna ke dashboard sesuai role.
 */
function redirect_by_role(): void
{
    require_login();
    refresh_user_status();

    $role = current_role();

    $destinations = [
        'akama' => 'akama/dashboard.php',
        'pic' => 'pic/dashboard.php',
        'mahasiswa' => 'mahasiswa/dashboard.php',
    ];

    if (!isset($destinations[$role])) {
        http_response_code(403);
        exit('Role akun tidak valid.');
    }

    redirect($destinations[$role]);
}
