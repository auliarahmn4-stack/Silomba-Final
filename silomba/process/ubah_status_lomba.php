<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('akama');
set_flash('info', 'Pengubahan status dilakukan melalui halaman Pengajuan Lomba atau Daftar Lomba.');
redirect('akama/pengajuan_lomba.php');
