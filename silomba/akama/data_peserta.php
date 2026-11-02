
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Data Peserta';
$keyword = trim($_GET['cari'] ?? '');
$status = $_GET['status'] ?? 'semua';

$sql = "SELECT p.id, p.tanggal_daftar, p.status,
               u.nama AS nama_mahasiswa, u.nim,
               l.judul AS judul_lomba
        FROM pendaftaran_lomba p
        JOIN users u ON u.id = p.mahasiswa_id
        JOIN lomba l ON l.id = p.lomba_id
        WHERE 1 = 1";

$params = [];

if ($keyword !== '') {
    $sql .= " AND (u.nama LIKE :nama OR u.nim LIKE :nim
                   OR l.judul LIKE :judul)";
    $params['nama'] = "%{$keyword}%";
    $params['nim'] = "%{$keyword}%";
    $params['judul'] = "%{$keyword}%";
}

$status_valid = ['semua', 'menunggu', 'diterima', 'ditolak', 'selesai'];

if (in_array($status, $status_valid, true) && $status !== 'semua') {
    $sql .= " AND p.status = :status";
    $params['status'] = $status;
}

$sql .= " ORDER BY p.tanggal_daftar DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $peserta = $stmt->fetchAll();

    $total_peserta = (int) $pdo->query(
        "SELECT COUNT(*) FROM pendaftaran_lomba"
    )->fetchColumn();
} catch (PDOException $e) {
    $peserta = [];
    $total_peserta = 0;
    set_flash('error', 'Data peserta belum dapat dimuat. Periksa tabel database.');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Data Peserta</h1>
        <p>Informasi mahasiswa yang mendaftarkan diri pada lomba.</p>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon">👥</div>
        <div>
            <p>Total Pendaftaran</p>
            <h2><?= number_format($total_peserta) ?></h2>
        </div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Daftar Peserta Lomba</h2>
            <p>Cari berdasarkan nama mahasiswa, NIM, atau nama lomba.</p>
        </div>
    </div>

    <form method="GET" class="filter-form">
        <input type="search" name="cari" class="form-control"
            placeholder="Cari nama, NIM, atau lomba..."
            value="<?= e($keyword) ?>">

        <select name="status" class="form-control">
            <?php foreach ($status_valid as $item): ?>
                <option value="<?= e($item) ?>"
                    <?= $status === $item ? 'selected' : '' ?>>
                    <?= e(ucfirst($item)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="<?= e(url('akama/data_peserta.php')) ?>"
            class="btn btn-outline">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Nama Lomba</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$peserta): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            Data peserta tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($peserta as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($item['nama_mahasiswa']) ?></td>
                            <td><?= e($item['nim'] ?? '-') ?></td>
                            <td><?= e($item['judul_lomba']) ?></td>
                            <td><?= e($item['tanggal_daftar'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $item['status'] === 'diterima'
                                    ? 'badge-success'
                                    : ($item['status'] === 'ditolak'
                                        ? 'badge-danger'
                                        : 'badge-warning') ?>">
                                    <?= e(ucfirst($item['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
