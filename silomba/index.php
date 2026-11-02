
<?php
// SILOMBA - Landing Page (halaman pertama)
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';
    $destination = [
        'akama' => 'akama/dashboard.php',
        'pic' => 'pic/dashboard.php',
        'mahasiswa' => 'mahasiswa/dashboard.php',
    ];
    if (isset($destination[$role])) {
        redirect($destination[$role]);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILOMBA - Sistem Informasi Lomba</title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/auth.css') ?>">
    <style>
        :root {
            --landing-brand: #0f3d99;
            --landing-brand-dark: #0b2e73;
            --landing-accent: #3b82f6;
            --landing-dark: #0f172a;
            --landing-light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
                "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--landing-dark);
            line-height: 1.6;
        }

        /* ---------- NAVIGASI ---------- */
        .landing-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 24px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .landing-nav .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
            color: var(--landing-brand);
        }

        .landing-nav .nav-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid rgba(15, 61, 153, 0.2);
        }

        .landing-nav .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .landing-nav .nav-links a {
            color: var(--landing-dark);
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .landing-nav .nav-links a:hover {
            color: var(--landing-brand);
        }

        .landing-nav .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ---------- HERO ---------- */
        .landing-hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
            overflow: hidden;
        }

        .landing-hero-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .landing-hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .landing-hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(
                180deg,
                rgba(15, 61, 153, 0.55) 0%,
                rgba(15, 23, 42, 0.75) 60%,
                rgba(15, 23, 42, 0.9) 100%
            );
        }

        .landing-hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            color: #ffffff;
        }

        .landing-hero .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
        }

        .landing-hero h1 {
            font-size: clamp(40px, 6vw, 72px);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -2px;
            background: linear-gradient(135deg, #ffffff 0%, #bae6fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .landing-hero p {
            font-size: clamp(16px, 2vw, 22px);
            color: #e2e8f0;
            margin-bottom: 40px;
            font-weight: 400;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .landing-hero-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ---------- STATS ---------- */
        .landing-stats {
            padding: 80px 20px;
            background: var(--landing-light);
            position: relative;
        }

        .landing-stats::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--landing-brand), transparent);
        }

        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 40px;
            font-weight: 800;
            color: var(--landing-brand);
            display: block;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 15px;
            font-weight: 500;
        }

        /* ---------- FEATURES ---------- */
        .landing-features {
            padding: 100px 20px;
            background: var(--landing-light);
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-header h2 {
            font-size: 36px;
            font-weight: 800;
            color: var(--landing-dark);
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .section-header p {
            color: var(--muted);
            font-size: 17px;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            padding: 32px 24px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--brand-light);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: var(--brand-light);
            color: var(--brand);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(15, 61, 153, 0.15);
        }

        .feature-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--landing-dark);
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        /* ---------- ROLES ---------- */
        .landing-roles {
            padding: 100px 20px;
            background: #ffffff;
        }

        .roles-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .role-card {
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            padding: 40px 24px;
            text-align: center;
            transition: transform 0.3s, border-color 0.3s;
            background: #ffffff;
        }

        .role-card:hover {
            transform: translateY(-6px);
            border-color: var(--brand-light);
            box-shadow: var(--shadow-lg);
        }

        .role-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 24px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .role-card.akama .role-icon { background: #eef3fb; color: var(--brand); }
        .role-card.pic .role-icon { background: var(--success-bg); color: var(--success); }
        .role-card.mahasiswa .role-icon { background: var(--warning-bg); color: var(--warning); }

        .role-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--landing-dark);
            margin-bottom: 12px;
        }

        .role-card p {
            color: var(--muted);
            font-size: 14.5px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-badge.akama { background: var(--brand-light); color: var(--brand); border: 1px solid rgba(15, 61, 153, 0.15); }
        .role-badge.pic { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-line); }
        .role-badge.mahasiswa { background: var(--warning-bg); color: var(--warning); border: 1px solid var(--warning-line); }

        /* ---------- CTA ---------- */
        .landing-cta {
            padding: 100px 20px;
            background: var(--landing-brand);
            text-align: center;
        }

        .landing-cta h2 {
            font-size: 40px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .landing-cta p {
            color: #bae6fd;
            font-size: 18px;
            margin-bottom: 32px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .landing-cta-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ---------- FOOTER ---------- */
        .landing-footer {
            padding: 40px 20px;
            background: var(--landing-dark);
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .landing-footer .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .landing-footer .footer-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .landing-footer p {
            font-size: 13px;
            margin-bottom: 4px;
        }

        /* ---------- BUTTONS ---------- */
        .btn-landing-primary {
            background: #ffffff;
            color: var(--landing-brand);
            border: none;
            padding: 16px 36px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-landing-primary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .btn-landing-secondary {
            background: transparent;
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 16px 36px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, border-color 0.2s, background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-landing-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
            transform: translateY(-2px);
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 900px) {
            .landing-nav {
                padding: 20px 24px;
            }

            .landing-nav .nav-links {
                display: none;
            }

            .roles-grid {
                grid-template-columns: 1fr;
            }

            .landing-hero h1 {
                font-size: 36px;
            }
        }

        @media (max-width: 600px) {
            .landing-nav {
                padding: 16px 20px;
            }

            .landing-nav .nav-actions {
                gap: 8px;
            }

            .landing-nav .btn-landing-primary,
            .landing-nav .btn-landing-secondary {
                padding: 10px 20px;
                font-size: 13px;
            }

            .landing-hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .landing-hero-actions .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .landing-stats {
                padding: 60px 20px;
            }

            .landing-features,
            .landing-roles,
            .landing-cta {
                padding: 70px 20px;
            }

            .landing-cta h2 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>

<nav class="landing-nav">
    <div class="nav-brand">
        <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        <span>SILOMBA</span>
    </div>
    <div class="nav-links">
        <a href="#fitur">Fitur</a>
        <a href="#peran">Peran Pengguna</a>
    </div>
    <div class="nav-actions">
       
    </div>
</nav>

<header class="landing-hero">
    <div class="landing-hero-bg">
        <img src="<?= e(url('picture/landing%20page.png')) ?>" alt="Landing Page SILOMBA">
    </div>
    <div class="landing-hero-overlay"></div>
    <div class="landing-hero-content">
        <span class="badge-pill">🏆 Sistem Informasi Lomba Mahasiswa</span>
        <h2>Temukan, Daftar, dan Raih Prestasi<br>melalui SILOMBA</h2>
        <p>Platform terpadu untuk pengelolaan informasi lomba, pendaftaran peserta,
           dan sertifikat digital bagi mahasiswa, PIC, dan AKAMA.</p>
        <div class="landing-hero-actions">
            <a href="auth/login.php" class="btn-landing-primary">Masuk ke Akun Saya</a>
            <a href="auth/register.php" class="btn-landing-secondary">Buat Akun Baru</a>
        </div>
    </div>
</header>

<section class="landing-stats" id="stats">
    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-number">3+</span>
            <span class="stat-label">Jenis Peran Pengguna</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">100%</span>
            <span class="stat-label">Terintegrasi Digital</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">24/7</span>
            <span class="stat-label">Akses Informasi Lomba</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">∞</span>
            <span class="stat-label">Prestasi Menunggu Anda</span>
        </div>
    </div>
</section>

<section class="landing-features" id="fitur">
    <div class="section-header">
        <h2>Fitur Unggulan</h2>
        <p>Semua yang Anda butuhkan untuk mengelola dan mengikuti lomba dalam satu platform.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📢</div>
            <h3>Informasi Lomba Terpusat</h3>
            <p>Jelajahi berbagai lomba mahasiswa yang dipublikasikan dengan detail lengkap, kategori, dan deadline pendaftaran.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📝</div>
            <h3>Pendaftaran Mudah</h3>
            <p>Daftar lomba secara online dengan proses yang terintegrasi dan notifikasi status pendaftaran secara real-time.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📜</div>
            <h3>Sertifikat Digital</h3>
            <p>Akses dan unduh sertifikat lomba yang telah Anda peroleh secara digital, kapan saja dan di mana saja.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>Kelola Peserta</h3>
            <p>PIC dapat mengelola data peserta, verifikasi pendaftaran, dan mengunggah sertifikat dengan mudah.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">✅</div>
            <h3>Verifikasi AKAMA</h3>
            <p>AKAMA menyetujui pengajuan lomba dan mempublikasikannya secara final untuk diakses semua mahasiswa.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Dashboard &amp; Statistik</h3>
            <p>Pantau perkembangan lomba, jumlah pendaftar, dan statistik keikutsertaan melalui dashboard interaktif.</p>
        </div>
    </div>
</section>

<section class="landing-roles" id="peran">
    <div class="section-header">
        <h2>Siapa yang Menggunakan SILOMBA?</h2>
        <p>Platform ini dirancang untuk semua pihak dalam ekosistem perlombaan kampus.</p>
    </div>
    <div class="roles-grid">
        <div class="role-card akama">
            <div class="role-icon">🏛️</div>
            <h3>AKAMA</h3>
            <p>Bagian yang mengelola verifikasi dan publikasi semua pengajuan lomba secara terpusat.</p>
            <span class="role-badge akama">Administrator</span>
        </div>
        <div class="role-card pic">
            <div class="role-icon">📋</div>
            <h3>PIC Lomba</h3>
            <p>Penanggung jawab lomba yang mengajukan, mengelola peserta, dan mengunggah sertifikat.</p>
            <span class="role-badge pic">Penyelenggara</span>
        </div>
        <div class="role-card mahasiswa">
            <div class="role-icon">🎓</div>
            <h3>Mahasiswa</h3>
            <p>Pengguna akhir yang mencari informasi lomba, mendaftar, dan menerima sertifikat digital.</p>
            <span class="role-badge mahasiswa">Peserta</span>
        </div>
    </div>
</section>

<section class="landing-cta">
    <h2>Siapa lagi yang akan meraih prestasi?</h2>
    <p>Gabung ribuan mahasiswa yang telah menemukan dan mengikuti lomba melalui SILOMBA.</p>
    <div class="landing-cta-actions">
        <a href="auth/login.php" class="btn-landing-primary">Masuk Sekarang</a>
        <a href="auth/register.php" class="btn-landing-secondary">Daftar Gratis</a>
    </div>
</section>

<footer class="landing-footer">
    <div class="footer-brand">
        <img src="<?= e(url('picture/logo%20silomba.png')) ?>" alt="Logo SILOMBA">
        <span>SILOMBA</span>
    </div>
    <p>Sistem Informasi Lomba &bull; UIN Imam Bonjol Padang</p>
    <p>&copy; <?= date('Y') ?> SILOMBA. Hak Cipta Dilindungi.</p>
</footer>

</body>
</html>
