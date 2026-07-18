<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

// ---- Statistik umum ----
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event"))['jml'];
$akan_datang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event WHERE status = 'Akan Datang'"))['jml'];
$berlangsung = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event WHERE status = 'Berlangsung'"))['jml'];
$selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM event WHERE status = 'Selesai'"))['jml'];

// ---- Statistik per kategori ----
$kategori_stats = [];
foreach (['Seminar', 'Workshop', 'Lomba', 'Pelatihan'] as $k) {
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jml FROM event WHERE kategori = ?");
    mysqli_stmt_bind_param($stmt, 's', $k);
    mysqli_stmt_execute($stmt);
    $jml = mysqli_stmt_get_result($stmt)->fetch_assoc()['jml'];
    $kategori_stats[$k] = $jml;
    mysqli_stmt_close($stmt);
}
$max_kategori = max(array_merge($kategori_stats, [1]));

// ---- Event yang baru ditambahkan (5 terbaru) ----
$terbaru = mysqli_query($koneksi, "SELECT * FROM event ORDER BY created_at DESC LIMIT 5");

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
<title>Dashboard - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container">

    <div class="page-header">
        <div>
            <span class="eyebrow">Ringkasan</span>
            <h2>Dashboard</h2>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $total ?></div>
            <div class="stat-label">Total Event</div>
        </div>
        <div class="stat-card" style="border-bottom-color:#b3841f;">
            <div class="stat-number"><?= $akan_datang ?></div>
            <div class="stat-label">Akan Datang</div>
        </div>
        <div class="stat-card" style="border-bottom-color:#2f6846;">
            <div class="stat-number"><?= $berlangsung ?></div>
            <div class="stat-label">Berlangsung</div>
        </div>
        <div class="stat-card" style="border-bottom-color:#5b6b82;">
            <div class="stat-number"><?= $selesai ?></div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>

    <div class="card">
        <div class="section-heading">
            <h3>Event per Kategori</h3>
        </div>
        <div style="display:flex; flex-direction:column; gap:16px;">
            <?php foreach ($kategori_stats as $k => $jml): ?>
            <div>
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                    <span class="badge <?= badgeClass($k) ?>"><?= $k ?></span>
                    <span class="mono" style="color:var(--slate);"><?= $jml ?> event</span>
                </div>
                <div style="background:#f0eee3; border-radius:20px; height:8px; overflow:hidden;">
                    <div style="background:var(--gold); height:100%; width:<?= $max_kategori > 0 ? round(($jml / $max_kategori) * 100) : 0 ?>%; border-radius:20px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="section-heading">
            <h3>Event Terbaru Ditambahkan</h3>
            <a href="event.php">Kelola semua &rarr;</a>
        </div>

        <?php if (mysqli_num_rows($terbaru) === 0): ?>
            <div class="empty-state">Belum ada data event.</div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Judul Event</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($terbaru)): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="assets/uploads/events/<?= htmlspecialchars($row['gambar']) ?>" alt="" class="table-thumb">
                        <?php else: ?>
                            <div class="table-thumb-placeholder">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><span class="badge <?= badgeClass($row['kategori']) ?>"><?= htmlspecialchars($row['kategori']) ?></span></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_pelaksanaan'])) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
