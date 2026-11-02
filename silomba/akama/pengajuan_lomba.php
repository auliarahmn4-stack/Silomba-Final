
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Pengajuan Lomba';
$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        set_flash('error', 'Permintaan tidak valid. Silakan coba kembali.');
        redirect('akama/pengajuan_lomba.php');
    }

    $lomba_id = filter_input(INPUT_POST, 'lomba_id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');

    if (!$lomba_id || !in_array($aksi, ['setujui', 'tolak', 'publikasikan', 'hapus'], true)) {
        set_flash('error', 'Data pengajuan tidak valid.');
        redirect('akama/pengajuan_lomba.php');
    }

    try {
        if ($aksi === 'setujui') {
            $stmt = $pdo->prepare(
                "UPDATE lomba
                 SET status = 'disetujui',
                     catatan_verifikasi = :catatan,
                     verified_by = :admin_id,
                     verified_at = NOW(),
                     updated_at = NOW()
                 WHERE id = :id AND status = 'menunggu'"
            );
            $stmt->execute([
                'catatan' => $catatan !== '' ? $catatan : null,
                'admin_id' => current_user()['id'],
                'id' => $lomba_id
            ]);

            if ($stmt->rowCount() > 0) {
                // Notifikasi ke PIC
                $stmt_pic = $pdo->prepare("SELECT pic_id, judul FROM lomba WHERE id = ?");
                $stmt_pic->execute([$lomba_id]);
                $info = $stmt_pic->fetch();
                if ($info) {
                    $stmt_notif = $pdo->prepare(
                        "INSERT INTO notifikasi (user_id, judul, pesan, tautan) VALUES (?, ?, ?, ?)"
                    );
                    $stmt_notif->execute([
                        $info['pic_id'],
                        'Pengajuan Lomba Disetujui',
                        'Pengajuan lomba "' . $info['judul'] . '" telah disetujui. Silakan lengkapi informasi detail lomba.',
                        'pic/lengkapi_lomba.php?id=' . $lomba_id
                    ]);
                }
                set_flash('success', 'Pengajuan lomba berhasil disetujui.');
            } else {
                set_flash('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
            }
        } elseif ($aksi === 'tolak') {
            $stmt = $pdo->prepare(
                "UPDATE lomba
                 SET status = 'ditolak',
                     catatan_verifikasi = :catatan,
                     verified_by = :admin_id,
                     verified_at = NOW(),
                     updated_at = NOW()
                 WHERE id = :id AND status = 'menunggu'"
            );
            $stmt->execute([
                'catatan' => $catatan !== '' ? $catatan : null,
                'admin_id' => current_user()['id'],
                'id' => $lomba_id
            ]);

            if ($stmt->rowCount() > 0) {
                set_flash('success', 'Pengajuan lomba berhasil ditolak.');
            } else {
                set_flash('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
            }
        } elseif ($aksi === 'hapus') {
            $stmt = $pdo->prepare("DELETE FROM lomba WHERE id = ?");
            $stmt->execute([$lomba_id]);
            set_flash('success', 'Lomba berhasil dihapus.');
        } elseif ($aksi === 'publikasikan') {
            $stmt = $pdo->prepare(
                "UPDATE lomba
                 SET status = 'dipublikasikan',
                     updated_at = NOW()
                 WHERE id = :id AND status = 'siap_publikasi'"
            );
            $stmt->execute([
                'id' => $lomba_id
            ]);

            if ($stmt->rowCount() > 0) {
                set_flash('success', 'Lomba berhasil dipublikasikan.');
            } else {
                set_flash('error', 'Lomba tidak ditemukan atau belum siap publikasi.');
            }
        }

    } catch (PDOException $e) {
        set_flash('error', 'Pengajuan gagal diproses. Periksa struktur tabel database.');
    }

    redirect('akama/pengajuan_lomba.php');
}

$filter = $_GET['status'] ?? 'menunggu';
$status_valid = ['semua', 'menunggu', 'disetujui', 'ditolak', 'siap_publikasi', 'dipublikasikan', 'selesai'];

if (!in_array($filter, $status_valid, true)) {
    $filter = 'menunggu';
}

$sql = "SELECT l.*, u.nama AS nama_pic, u.email AS email_pic
        FROM lomba l
        LEFT JOIN users u ON u.id = l.pic_id";

$params = [];

if ($filter !== 'semua') {
    $sql .= " WHERE l.status = :status";
    $params['status'] = $filter;
}

$sql .= " ORDER BY l.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $daftar_lomba = $stmt->fetchAll();
} catch (PDOException $e) {
    $daftar_lomba = [];
    $pesan_error = 'Data belum dapat ditampilkan. Pastikan tabel lomba dan users sudah dibuat.';
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Pengajuan Lomba</h1>
        <p>Periksa, setujui, atau publikasikan pengajuan lomba dari PIC.</p>
    </div>
</section>

<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-error"><?= e($pesan_error) ?></div>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Daftar Pengajuan</h2>
            <p>Gunakan filter untuk melihat status pengajuan.</p>
        </div>

        <form method="GET" class="filter-form">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <?php foreach ($status_valid as $status): ?>
                    <option value="<?= e($status) ?>"
                        <?= $filter === $status ? 'selected' : '' ?>>
                        <?= e(ucfirst(str_replace('_', ' ', $status))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lomba</th>
                    <th>Penyelenggara</th>
                    <th>PIC Pengaju</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftar_lomba)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            Tidak ada pengajuan lomba pada kategori ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftar_lomba as $i => $lomba): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($lomba['judul']) ?></td>
                            <td><?= e($lomba['penyelenggara'] ?? '-') ?></td>
                            <td>
                                <?= e($lomba['nama_pic'] ?? '-') ?><br>
                                <small><?= e($lomba['email_pic'] ?? '-') ?></small>
                            </td>
                            <td>
                                <?= e(
                                    !empty($lomba['created_at'])
                                        ? date('d-m-Y', strtotime($lomba['created_at']))
                                        : '-'
                                ) ?>
                            </td>
                            <td>
                                <span class="badge <?= $lomba['status'] === 'menunggu'
                                    ? 'badge-warning'
                                    : ($lomba['status'] === 'ditolak'
                                        ? 'badge-danger'
                                        : ($lomba['status'] === 'siap_publikasi'
                                            ? 'badge-info'
                                            : 'badge-success')) ?>">
                                    <?= e(ucfirst(str_replace('_', ' ', $lomba['status']))) ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline"
                                    onclick="document.getElementById('detail-<?= (int)$lomba['id'] ?>').hidden = false">
                                    Detail
                                </button>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="lomba_id" value="<?= (int)$lomba['id'] ?>">
                                    <button type="submit" name="aksi" value="hapus" class="btn btn-sm btn-danger" data-confirm="Hapus lomba ini?">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <tr id="detail-<?= (int)$lomba['id'] ?>" hidden>
                            <td colspan="7">
                                <div class="detail-panel">
                                    <h3><?= e($lomba['judul']) ?></h3>
                                    <p><strong>Kategori:</strong>
                                        <?= e($lomba['kategori'] ?? '-') ?></p>
                                    <p><strong>Tingkat:</strong>
                                        <?= e($lomba['tingkat'] ?? '-') ?></p>
                                    <p><strong>Lokasi:</strong>
                                        <?= e($lomba['lokasi'] ?? '-') ?></p>
                                    <p><strong>Deskripsi:</strong><br>
                                        <?= nl2br(e($lomba['deskripsi'] ?? '-')) ?></p>
                                    <p><strong>Periode:</strong>
                                        <?= e($lomba['tanggal_mulai'] ?? '-') ?>
                                        sampai <?= e($lomba['tanggal_selesai'] ?? '-') ?></p>
                                    <p><strong>Batas Pendaftaran:</strong>
                                        <?= e($lomba['deadline_pendaftaran'] ?? '-') ?></p>

                                    <?php if (!empty($lomba['poster'])): ?>
                                        <p>
                                            <a target="_blank" rel="noopener"
                                               href="<?= e(url('uploads/poster/' . basename($lomba['poster']))) ?>">
                                                Lihat Poster
                                            </a>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($lomba['link_pendaftaran']) || !empty($lomba['link_panduan']) || !empty($lomba['kontak_person'])): ?>
                                        <div style="border:1px solid var(--line-soft); border-radius:8px; padding:14px; background:#f8fafc; margin-top:12px;">
                                            <strong style="display:block; margin-bottom:8px; color:var(--brand);">Detail Tambahan dari PIC:</strong>
                                            <?php if (!empty($lomba['link_pendaftaran'])): ?>
                                                <p><strong>Link Pendaftaran:</strong>
                                                    <a href="<?= e($lomba['link_pendaftaran']) ?>" target="_blank" rel="noopener"><?= e($lomba['link_pendaftaran']) ?></a></p>
                                            <?php endif; ?>
                                            <?php if (!empty($lomba['link_panduan'])): ?>
                                                <p><strong>Link Panduan:</strong>
                                                    <a href="<?= e($lomba['link_panduan']) ?>" target="_blank" rel="noopener"><?= e($lomba['link_panduan']) ?></a></p>
                                            <?php endif; ?>
                                            <?php if (!empty($lomba['kontak_person'])): ?>
                                                <p><strong>Kontak Person:</strong> <?= e($lomba['kontak_person']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($lomba['status'] === 'menunggu'): ?>
                                        <form method="POST" data-validate>
                                            <input type="hidden" name="csrf_token"
                                                value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="lomba_id"
                                                value="<?= (int)$lomba['id'] ?>">

                                            <label for="catatan-<?= (int)$lomba['id'] ?>">
                                                Catatan verifikasi
                                            </label>
                                            <textarea
                                                id="catatan-<?= (int)$lomba['id'] ?>"
                                                name="catatan"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Isi catatan jika diperlukan"></textarea>

                                            <div class="form-actions">
                                                <button class="btn btn-primary"
                                                    type="submit" name="aksi" value="setujui"
                                                    data-confirm="Setujui pengajuan lomba ini?">
                                                    Setujui
                                                </button>
                                                <button class="btn btn-danger"
                                                    type="submit" name="aksi" value="tolak"
                                                    data-confirm="Tolak pengajuan lomba ini?">
                                                    Tolak
                                                </button>
                                                <button class="btn btn-outline" type="button"
                                                    onclick="document.getElementById('detail-<?= (int)$lomba['id'] ?>').hidden = true">
                                                    Tutup
                                                </button>
                                            </div>
                                        </form>
                                    <?php elseif ($lomba['status'] === 'siap_publikasi'): ?>
                                        <p><strong>Catatan verifikasi:</strong>
                                            <?= e($lomba['catatan_verifikasi'] ?? '-') ?></p>
                                        <form method="POST" data-validate>
                                            <input type="hidden" name="csrf_token"
                                                value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="lomba_id"
                                                value="<?= (int)$lomba['id'] ?>">

                                            <div class="form-actions">
                                                <button class="btn btn-primary"
                                                    type="submit" name="aksi" value="publikasikan"
                                                    data-confirm="Publikasikan lomba ini secara final?">
                                                    Publikasikan
                                                </button>
                                                <button class="btn btn-outline" type="button"
                                                    onclick="document.getElementById('detail-<?= (int)$lomba['id'] ?>').hidden = true">
                                                    Tutup
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <p><strong>Catatan verifikasi:</strong>
                                            <?= e($lomba['catatan_verifikasi'] ?? '-') ?></p>
                                        <button class="btn btn-outline" type="button"
                                            onclick="document.getElementById('detail-<?= (int)$lomba['id'] ?>').hidden = true">
                                            Tutup
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
