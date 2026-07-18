<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM event WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    header('Location: event.php');
    exit;
}

function badgeClass($kategori) {
    return match ($kategori) {
        'Seminar' => 'badge-seminar',
        'Workshop' => 'badge-workshop',
        'Lomba' => 'badge-lomba',
        'Pelatihan' => 'badge-pelatihan',
        default => '',
    };
}

$link_maps = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($event['lokasi']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($event['judul']) ?> - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container" style="max-width: 780px;">

    <div class="page-header">
        <a href="event.php" class="btn btn-secondary">&larr; Kembali ke Daftar Event</a>
    </div>

    <div class="detail-poster-wrap">
        <?php if (!empty($event['gambar'])): ?>
            <img src="assets/uploads/events/<?= htmlspecialchars($event['gambar']) ?>" alt="<?= htmlspecialchars($event['judul']) ?>">
        <?php else: ?>
            <div class="no-image">Smart Event Campus</div>
        <?php endif; ?>
    </div>

    <div class="detail-header">
        <span class="badge <?= badgeClass($event['kategori']) ?>"><?= htmlspecialchars($event['kategori']) ?></span>
        <h1><?= htmlspecialchars($event['judul']) ?></h1>
    </div>

    <div class="detail-meta-grid">
        <div class="detail-meta-item">
            <div class="meta-icon">&#128197;</div>
            <div class="meta-text">
                <div class="meta-label">Tanggal</div>
                <div class="meta-value"><?= date('d M Y', strtotime($event['tanggal_pelaksanaan'])) ?></div>
            </div>
        </div>
        <div class="detail-meta-item">
            <div class="meta-icon">&#128337;</div>
            <div class="meta-text">
                <div class="meta-label">Waktu</div>
                <div class="meta-value"><?= date('H:i', strtotime($event['waktu_pelaksanaan'])) ?> WIB</div>
            </div>
        </div>
        <div class="detail-meta-item">
            <div class="meta-icon">&#128205;</div>
            <div class="meta-text">
                <div class="meta-label">Lokasi</div>
                <div class="meta-value"><?= htmlspecialchars($event['lokasi']) ?></div>
                <a href="<?= htmlspecialchars($link_maps) ?>" target="_blank" rel="noopener">Lihat di Map &rarr;</a>
            </div>
        </div>
        <div class="detail-meta-item">
            <div class="meta-icon">&#128100;</div>
            <div class="meta-text">
                <div class="meta-label">Penyelenggara</div>
                <div class="meta-value"><?= htmlspecialchars($event['penyelenggara']) ?></div>
            </div>
        </div>
        <div class="detail-meta-item">
            <div class="meta-icon">&#127917;</div>
            <div class="meta-text">
                <div class="meta-label">Kuota Peserta</div>
                <div class="meta-value"><?= (int)$event['kuota'] ?> orang</div>
            </div>
        </div>
        <div class="detail-meta-item">
            <div class="meta-icon">&#9989;</div>
            <div class="meta-text">
                <div class="meta-label">Status</div>
                <div class="meta-value"><?= htmlspecialchars($event['status']) ?></div>
            </div>
        </div>
    </div>

    <div class="card detail-desc">
        <h3>Deskripsi Kegiatan</h3>
        <p><?= nl2br(htmlspecialchars($event['deskripsi'])) ?></p>
    </div>

    <div class="detail-actions">
        <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-warning">Edit Event</a>
        <a href="hapus_event.php?id=<?= $event['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus event ini?');">Hapus Event</a>
    </div>

</div>
</body>
</html>
