
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Data Sertifikat';
$keyword = trim($_GET['cari'] ?? '');

$sql = "SELECT s.*, u.nama AS nama_mahasiswa, u.nim,
               l.judul AS judul_lomba
        FROM sertifikat s
        JOIN users u ON u.id = s.mahasiswa_id
        JOIN lomba l ON l.id = s.lomba_id";

$params = [];

if ($keyword !== '') {
    $sql .= " WHERE u.nama LIKE :nama
              OR u.nim LIKE :nim
              OR l.judul LIKE :judul
              OR s.nomor_sertifikat LIKE :nomor";
    $params = [
        'nama' => "%{$keyword}%",
        'nim' => "%{$keyword}%",
        'judul' => "%{$keyword}%",
        'nomor' => "%{$keyword}%"
    ];
}

$sql .= " ORDER BY s.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $sertifikat_list = $stmt->fetchAll();

    $total_sertifikat = (int) $pdo->query(
        "SELECT COUNT(*) FROM sertifikat"
    )->fetchColumn();
} catch (PDOException $e) {
    $sertifikat_list = [];
    $total_sertifikat = 0;
    set_flash('error', 'Data sertifikat belum dapat dimuat.');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Data Sertifikat</h1>
        <p>Daftar sertifikat lomba yang telah diunggah ke sistem SILOMBA.</p>
    </div>
</section>

<section class="stats-grid">
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
            <h2>Daftar Sertifikat</h2>
            <p>Sertifikat dapat ditinjau berdasarkan peserta atau nama lomba.</p>
        </div>
    </div>

    <form method="GET" class="filter-form">
        <input type="search" name="cari" class="form-control"
            placeholder="Cari nama, NIM, lomba, atau nomor sertifikat..."
            value="<?= e($keyword) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="<?= e(url('akama/sertifikat.php')) ?>"
            class="btn btn-outline">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Sertifikat</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Nama Lomba</th>
                    <th>Tanggal Unggah</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$sertifikat_list): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            Belum ada sertifikat atau data tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sertifikat_list as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($item['nomor_sertifikat'] ?? '-') ?></td>
                            <td><?= e($item['nama_mahasiswa']) ?></td>
                            <td><?= e($item['nim'] ?? '-') ?></td>
                            <td><?= e($item['judul_lomba']) ?></td>
                            <td><?= e($item['tanggal_unggah'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($item['file_sertifikat'])): ?>
                                    <a class="btn btn-sm btn-outline"
                                        target="_blank" rel="noopener"
                                        href="<?= e(url(
                                            'uploads/sertifikat/' .
                                            basename($item['file_sertifikat'])
                                        )) ?>">
                                        Lihat File
                                    </a>
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
