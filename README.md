<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

// ---- Pencarian & Filter ----
$keyword = trim($_GET['q'] ?? '');
$kategori_filter = $_GET['kategori'] ?? '';

$sql = "SELECT * FROM event WHERE 1=1";
$params = [];
$types = '';

if ($keyword !== '') {
    $sql .= " AND (judul LIKE ? OR lokasi LIKE ? OR penyelenggara LIKE ?)";
    $like = '%' . $keyword . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

if ($kategori_filter !== '' && in_array($kategori_filter, ['Seminar','Workshop','Lomba','Pelatihan'])) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori_filter;
    $types .= 's';
}

$sql .= " ORDER BY tanggal_pelaksanaan ASC, waktu_pelaksanaan ASC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jumlah_hasil = mysqli_num_rows($result);

function badgeClass($kategori) {
    return match ($kategori) {
        'Seminar' => 'badge-seminar',
        'Workshop' => 'badge-workshop',
        'Lomba' => 'badge-lomba',
        'Pelatihan' => 'badge-pelatihan',
        default => '',
    };
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Event - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?php
            $msgs = [
                'added' => 'Event berhasil ditambahkan.',
                'updated' => 'Event berhasil diperbarui.',
                'deleted' => 'Event berhasil dihapus.',
                'forbidden' => 'Anda tidak memiliki akses untuk mengelola event. Hanya Admin yang dapat menambah, mengubah, atau menghapus data.',
            ];
            echo htmlspecialchars($msgs[$_GET['msg']] ?? '');
            ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <span class="eyebrow">Kelola Data</span>
            <h2>Data Event Kampus</h2>
        </div>
        <a href="tambah_event.php" class="btn btn-success">+ Tambah Event</a>
    </div>

    <div class="card">
        <form method="GET" action="event.php" style="display:flex; gap:10px; flex-wrap:wrap;">
            <div class="search-box">
                <input type="text" name="q" placeholder="Cari judul, lokasi, penyelenggara..." value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <select name="kategori" class="filter-select">
                <option value="">Semua Kategori</option>
                <?php foreach (['Seminar','Workshop','Lomba','Pelatihan'] as $k): ?>
                    <option value="<?= $k ?>" <?= $kategori_filter === $k ? 'selected' : '' ?>><?= $k ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="event.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <?php if ($jumlah_hasil === 0): ?>
        <div class="card">
            <div class="empty-state">Belum ada data event yang cocok.</div>
        </div>
    <?php else: ?>
    <div class="event-gallery-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="event-gallery-card">
            <div class="event-gallery-image-wrap">
                <a href="detail_event.php?id=<?= $row['id'] ?>">
                <?php if (!empty($row['gambar'])): ?>
                    <img src="assets/uploads/events/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                <?php else: ?>
                    <div class="no-image">Smart Event Campus</div>
                <?php endif; ?>
                </a>
                <span class="badge <?= badgeClass($row['kategori']) ?>"><?= htmlspecialchars($row['kategori']) ?></span>
            </div>
            <div class="event-gallery-body">
                <h4><a href="detail_event.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['judul']) ?></a></h4>
                <div class="event-gallery-meta">
                    <span class="mono"><?= date('d M Y', strtotime($row['tanggal_pelaksanaan'])) ?> &middot; <?= date('H:i', strtotime($row['waktu_pelaksanaan'])) ?> WIB</span>
                    <span><?= htmlspecialchars($row['lokasi']) ?></span>
                    <span><?= htmlspecialchars($row['status']) ?></span>
                </div>
                <div class="event-gallery-actions">
                    <a href="edit_event.php?id=<?= $row['id'] ?>" class="edit-link">Edit</a>
                    <a href="hapus_event.php?id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus event ini?');">Hapus</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
