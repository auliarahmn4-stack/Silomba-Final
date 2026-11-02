<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_role('pic');
set_flash('info', 'Pengajuan lomba dilakukan melalui halaman Ajukan Lomba.');
redirect('pic/ajukan_lomba.php');
