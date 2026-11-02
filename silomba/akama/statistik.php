
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Statistik Lomba';

try {
    $total_lomba = (int) $pdo->query(
        "SELECT COUNT(*) FROM lomba"
    )->fetchColumn();

    $total_peserta = (int) $pdo->query(
        "SELECT COUNT(*) FROM pendaftaran_lomba"
    )->fetchColumn();

    $total_sertifikat = (int) $pdo->query(
        "SELECT COUNT(*) FROM sertifikat"
    )->fetchColumn();

    $stmt = $pdo->query(
        "SELECT status, COUNT(*) AS jumlah
         FROM lomba
         GROUP BY status"
    );
    $statistik_status = $stmt->fetchAll();

    $stmt = $pdo->query(
        "SELECT l.judul, COUNT(p.id) AS jumlah_peserta
         FROM lomba l
         LEFT JOIN pendaftaran_lomba p ON p.lomba_id = l.id
         GROUP BY l.id, l.judul
         ORDER BY jumlah_peserta DESC
         LIMIT 5"
    );
    $lomba_populer = $stmt->fetchAll();

    $stmt = $pdo->query(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan,
                COUNT(*) AS jumlah
         FROM lomba
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
         GROUP BY DATE_FORMAT(created_at, '%Y-%m')
         ORDER BY bulan ASC"
    );
    $statistik_bulanan = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_lomba = $total_peserta = $total_sertifikat = 0;
    $statistik_status = $lomba_populer = $statistik_bulanan = [];
    set_flash('error', 'Data statistik belum dapat dimuat.');
}

$jumlah_status = [];
foreach ($statistik_status as $item) {
    $jumlah_status[$item['status']] = (int) $item['jumlah'];
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Statistik Lomba</h1>
        <p>Ringkasan data lomba, pendaftaran mahasiswa, dan sertifikat.</p>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon">🏆</div>
        <div>
            <p>Total Lomba</p>
            <h2><?= number_format($total_lomba) ?></h2>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon">👥</div>
        <div>
            <p>Total Pendaftaran</p>
            <h2><?= number_format($total_peserta) ?></h2>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon">📜</div>
        <div>
            <p>Total Sertifikat</p>
            <h2><?= number_format($total_sertifikat) ?></h2>
        </div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Jumlah Lomba Berdasarkan Status</h2>
            <p>Distribusi status seluruh lomba yang tercatat.</p>
        </div>
    </div>
    <canvas id="statusChart" height="120"></canvas>
    <div class="stats-grid">
        <?php
        $label_status = [
            'menunggu' => 'Menunggu Verifikasi',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dipublikasikan' => 'Dipublikasikan',
            'selesai' => 'Selesai'
        ];
        ?>
        <?php foreach ($label_status as $key => $label): ?>
            <article class="stat-card">
                <div>
                    <p><?= e($label) ?></p>
                    <h2><?= number_format($jumlah_status[$key] ?? 0) ?></h2>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Lomba dengan Peserta Terbanyak</h2>
            <p>Lima lomba dengan jumlah pendaftaran paling tinggi.</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Jumlah Pendaftar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$lomba_populer): ?>
                    <tr>
                        <td colspan="3" class="empty-state">Belum ada data lomba.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lomba_populer as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($item['judul']) ?></td>
                            <td><?= number_format((int) $item['jumlah_peserta']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Tren Pengajuan Lomba</h2>
            <p>Jumlah lomba berdasarkan bulan pencatatan.</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Lomba</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$statistik_bulanan): ?>
                    <tr>
                        <td colspan="2" class="empty-state">
                            Belum ada data bulanan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($statistik_bulanan as $item): ?>
                        <tr>
                            <td><?= e($item['bulan']) ?></td>
                            <td><?= number_format((int) $item['jumlah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusLabels = <?= json_encode(array_keys($jumlah_status)) ?>;
const statusData = <?= json_encode(array_values($jumlah_status)) ?>;
new Chart(document.getElementById('statusChart'), { type: 'doughnut', data: { labels: statusLabels, datasets: [{ data: statusData }] } });

const pesertaLabels = <?= json_encode(array_column($lomba_populer, 'judul')) ?>;
const pesertaData = <?= json_encode(array_map('intval', array_column($lomba_populer, 'jumlah_peserta'))) ?>;
new Chart(document.getElementById('pesertaChart'), { type: 'bar', data: { labels: pesertaLabels, datasets: [{ label: 'Peserta', data: pesertaData, backgroundColor: '#10b981' }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });

const trendLabels = <?= json_encode(array_column($statistik_bulanan, 'bulan')) ?>;
const trendData = <?= json_encode(array_map('intval', array_column($statistik_bulanan, 'jumlah'))) ?>;
new Chart(document.getElementById('trendChart'), { type: 'line', data: { labels: trendLabels, datasets: [{ label: 'Lomba', data: trendData, fill: true, backgroundColor: 'rgba(79,70,229,.2)', borderColor: '#4f46e5' }] }, options: { responsive: true, scales: { y: { beginAtZero: true } } } });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
