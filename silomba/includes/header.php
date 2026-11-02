
<?php

require_once __DIR__ . '/auth.php';

$user = current_user();

$page_title = $page_title ?? APP_NAME;
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($page_title) ?> | <?= e(APP_NAME) ?></title>

    <link rel="stylesheet"
          href="<?= e(url('assets/css/style.css')) ?>">

    <script>
(function(){ if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark-mode'); })();
</script>
<style>
html.dark-mode body { background: #0f172a; color: #f1f5f9; }
html.dark-mode body, html.dark-mode h1, html.dark-mode h2, html.dark-mode h3, html.dark-mode p, html.dark-mode label, html.dark-mode span, html.dark-mode td, html.dark-mode th { color: #f1f5f9; }
html.dark-mode .card, html.dark-mode .profile-card, html.dark-mode .settings-nav, html.dark-mode .stat-card { background: #1e293b; border-color: #334155; color: #e2e8f0; }
html.dark-mode body { background: #0f172a; color: #f1f5f9; }
html.dark-mode .card, html.dark-mode .profile-card, html.dark-mode .stat-card, html.dark-mode .settings-nav, html.dark-mode table, html.dark-mode th, html.dark-mode td, html.dark-mode .table-wrapper { background: #1e293b; color: #f1f5f9; border-color: #334155; }
html.dark-mode .form-control { background: #0f172a; color: #f1f5f9; border-color: #334155; }
</style>
</head>
<body>

<div class="app-layout">

<header class="topbar">
    <div class="topbar-brand">
        <span class="brand-icon">
            <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        </span>
        <div class="topbar-brand-text">
            <strong>SILOMBA</strong>
            <span>Sistem Informasi Lomba</span>
        </div>
    </div>

    <div class="topbar-center">
        <form class="topbar-search"
              action="<?= e(url('index.php')) ?>"
              method="get">
            <span class="search-icon">🔍</span>
            <input type="search"
                   name="q"
                   placeholder="Cari lomba, peserta, atau informasi..."
                   aria-label="Pencarian">
        </form>
    </div>

    <button type="button" id="themeToggle" style="margin-right:12px;">🌙</button>
<script>
(function(){
  var btn=document.getElementById('themeToggle');
  if(!btn) return;
  if(localStorage.getItem('theme')==='dark'){btn.textContent='☀️';}
  btn.addEventListener('click',function(){
    var dark=!document.documentElement.classList.contains('dark-mode');
    document.documentElement.classList.toggle('dark-mode',dark);
    document.body.classList.toggle('dark-mode',dark);
    localStorage.setItem('theme',dark?'dark':'light');
    btn.textContent=dark?'☀️':'🌙';
  });
})();
</script>
<div class="topbar-user">
        <div class="topbar-user-info">
            <div class="topbar-user-name"><?= e($user['nama'] ?? 'Pengguna') ?></div>
            <div class="topbar-user-role"><?= e(ucfirst($user['role'] ?? '')) ?></div>
        </div>
        <div class="avatar">
            <?= e(strtoupper(substr($user['nama'] ?? 'U', 0, 1))) ?>
        </div>
    </div>

    <button class="menu-toggle"
            type="button"
            aria-label="Buka navigasi"
            data-menu-toggle>
        ☰
    </button>
</header>

<?php $role = $user['role'] ?? ''; $sidebar_file = __DIR__ . '/sidebar_' . $role . '.php'; if (file_exists($sidebar_file)) require_once $sidebar_file; ?>

<main class="main-content">

<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"
         role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
