<?php
// Komponen navbar — sertakan setelah session_start() dan setelah $_SESSION tersedia.
$halaman_aktif = basename($_SERVER['PHP_SELF']);
?>
<div class="navbar">
    <div class="brand">Smart Event Campus</div>
    <div class="nav-links">
        <a href="home.php" class="<?= $halaman_aktif === 'home.php' ? 'active' : '' ?>">Home</a>
        <a href="event.php" class="<?= in_array($halaman_aktif, ['event.php','tambah_event.php','edit_event.php','detail_event.php']) ? 'active' : '' ?>">Event</a>
        <a href="dashboard.php" class="<?= $halaman_aktif === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    </div>
    <div class="nav-right">
        <span>Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']) ?></strong></span>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</div>
