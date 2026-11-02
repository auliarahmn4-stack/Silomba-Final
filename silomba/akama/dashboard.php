
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Dashboard AKAMA';

$stats = [
    'pengajuan' => 0,
    'lomba' => 0,
    'peserta' => 0,
    'sertifikat' => 0
];

$status_counts = [];
try {
    $rows = $pdo->query("SELECT status, COUNT(*) AS total FROM lomba GROUP BY status")->fetchAll();
    foreach ($rows as $r) $status_counts[$r['status']] = (int)$r['total'];
} catch (PDOException $e) { }

try {
    $stats['pengajuan'] = (int) $pdo->query(
        "SELECT COUNT(*) FROM lomba WHERE status = 'menunggu'"
    )->fetchColumn();

     $stats['lomba'] = (int) $pdo->query(
         "SELECT COUNT(*) FROM lomba
          WHERE status IN ('disetujui', 'siap_publikasi', 'dipublikasikan', 'selesai')"
     )->fetchColumn();

    $stats['peserta'] = (int) $pdo->query(
        "SELECT COUNT(*) FROM pendaftaran_lomba"
    )->fetchColumn();

    $stats['sertifikat'] = (int) $pdo->query(
        "SELECT COUNT(*) FROM sertifikat"
    )->fetchColumn();

    $stmt = $pdo->query(
        "SELECT l.*, u.nama AS nama_pic
         FROM lomba l
         LEFT JOIN users u ON u.id = l.pic_id
         ORDER BY l.created_at DESC
         LIMIT 5"
    );
    $pengajuan_terbaru = $stmt->fetchAll();
} catch (PDOException $e) {
    $pengajuan_terbaru = [];
    set_flash('error', 'Data dashboard belum tersedia. Periksa database SILOMBA.');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Dashboard AKAMA</h1>
        <p>Ringkasan pengelolaan informasi dan pengajuan lomba.</p>
    </div>
    <a href="<?= e(url('akama/pengajuan_lomba.php')) ?>" class="btn btn-primary">
        Lihat Pengajuan
    </a>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon">📩</div>
        <div>
            <p>Pengajuan Menunggu</p>
            <h2><?= number_format($stats['pengajuan']) ?></h2>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon">🏆</div>
        <div>
            <p>Total Lomba Aktif/Selesai</p>
            <h2><?= number_format($stats['lomba']) ?></h2>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon">👥</div>
        <div>
            <p>Total Pendaftaran</p>
            <h2><?= number_format($stats['peserta']) ?></h2>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon">📜</div>
        <div>
            <p>Sertifikat Diunggah</p>
            <h2><?= number_format($stats['sertifikat']) ?></h2>
        </div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Pengajuan Lomba Terbaru</h2>
            <p>Daftar pengajuan yang terakhir masuk ke sistem.</p>
        </div>
        <a class="btn btn-outline" href="<?= e(url('akama/pengajuan_lomba.php')) ?>">
            Lihat Semua
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Penyelenggara</th>
                    <th>PIC</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengajuan_terbaru)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada data pengajuan lomba.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengajuan_terbaru as $i => $lomba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($lomba['judul']) ?></td>
                            <td><?= e($lomba['penyelenggara'] ?? '-') ?></td>
                            <td><?= e($lomba['nama_pic'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-warning">
                                    <?= e(ucfirst($lomba['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-primary"
                                   href="<?= e(url('akama/pengajuan_lomba.php')) ?>">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>Statistik Lomba per Status</h2>
    <canvas id="lombaChart" height="120"></canvas>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode(array_keys($status_counts)) ?>;
const data = <?= json_encode(array_values($status_counts)) ?>;
new Chart(document.getElementById('lombaChart'), {
  type: 'bar',
  data: { labels, datasets: [{ label: 'Jumlah Lomba', data, backgroundColor: '#4f46e5' }] },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

<section class="card">
    <h2>Statistik Lomba per Status</h2>
    <canvas id="lombaChart" height="120"></canvas>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = <?= json_encode(array_keys($status_counts)) ?>;
const data = <?= json_encode(array_values($status_counts)) ?>;
new Chart(document.getElementById('lombaChart'), {
  type: 'bar',
  data: { labels, datasets: [{ label: 'Jumlah Lomba', data, backgroundColor: '#4f46e5' }] },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>setInterval(function(){ location.reload(); }, 60000);</script>
