<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

// ---- Statistik ringkas ----
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event"))['jml'];
$akan_datang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event WHERE status = 'Akan Datang'"))['jml'];

// ---- Event terbaru ditambahkan (4) ----
$terbaru = mysqli_query($koneksi, "SELECT * FROM event ORDER BY created_at DESC LIMIT 4");

// ---- Event terdekat waktunya (4) ----
$terdekat = mysqli_query($koneksi, "SELECT * FROM event WHERE status = 'Akan Datang' ORDER BY tanggal_pelaksanaan ASC, waktu_pelaksanaan ASC LIMIT 4");

function badgeClass($kategori) {
    return match ($kategori) {
        'Seminar' => 'badge-seminar',
        'Workshop' => 'badge-workshop',
        'Lomba' => 'badge-lomba',
        'Pelatihan' => 'badge-pelatihan',
        default => '',
    };
}

function renderKartuEvent($ev) {
    ?>
    <div class="event-gallery-card">
        <div class="event-gallery-image-wrap">
            <a href="detail_event.php?id=<?= $ev['id'] ?>">
            <?php if (!empty($ev['gambar'])): ?>
                <img src="assets/uploads/events/<?= htmlspecialchars($ev['gambar']) ?>" alt="<?= htmlspecialchars($ev['judul']) ?>">
            <?php else: ?>
                <div class="no-image">Smart Event Campus</div>
            <?php endif; ?>
            </a>
            <span class="badge <?= badgeClass($ev['kategori']) ?>"><?= htmlspecialchars($ev['kategori']) ?></span>
        </div>
        <div class="event-gallery-body">
            <h4><a href="detail_event.php?id=<?= $ev['id'] ?>"><?= htmlspecialchars($ev['judul']) ?></a></h4>
            <div class="event-gallery-meta">
                <span class="mono"><?= date('d M Y', strtotime($ev['tanggal_pelaksanaan'])) ?> &middot; <?= date('H:i', strtotime($ev['waktu_pelaksanaan'])) ?> WIB</span>
                <span><?= htmlspecialchars($ev['lokasi']) ?></span>
            </div>
            <div class="event-gallery-actions">
                <a href="edit_event.php?id=<?= $ev['id'] ?>" class="edit-link">Edit</a>
                <a href="hapus_event.php?id=<?= $ev['id'] ?>" class="delete-link" onclick="return confirm('Yakin ingin menghapus event ini?');">Hapus</a>
            </div>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Home - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container">

    <div class="hero-photo">
        <img src="assets/images/hero-kampus.jpg" alt="" class="hero-bg-img" onerror="this.style.display='none';">
        <div class="hero-overlay"></div>
        <div class="hero-center-content">
            <span class="eyebrow">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?></span>
            <h1>Smart Event Campus</h1>
            <p>Platform Informasi dan Pengelolaan Event Kampus untuk Mahasiswa</p>
            <a href="event.php" class="btn-hero-cta">Event</a>
        </div>
    </div>

    <div class="section-heading">
        <h3>Event Terdekat</h3>
        <a href="event.php">Lihat semua &rarr;</a>
    </div>
    <?php if (mysqli_num_rows($terdekat) === 0): ?>
        <div class="card"><div class="empty-state">Belum ada event yang akan datang.</div></div>
    <?php else: ?>
    <div class="event-gallery-grid">
        <?php while ($ev = mysqli_fetch_assoc($terdekat)): ?>
            <?php renderKartuEvent($ev); ?>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
