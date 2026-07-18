<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Ambil nama file gambar dulu sebelum data dihapus
    $cek = mysqli_prepare($koneksi, "SELECT gambar FROM event WHERE id = ?");
    mysqli_stmt_bind_param($cek, 'i', $id);
    mysqli_stmt_execute($cek);
    $data = mysqli_stmt_get_result($cek)->fetch_assoc();
    mysqli_stmt_close($cek);

    $stmt = mysqli_prepare($koneksi, "DELETE FROM event WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Hapus file gambar dari server kalau ada
    if (!empty($data['gambar'])) {
        $path = 'assets/uploads/events/' . $data['gambar'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

header('Location: event.php?msg=deleted');
exit;
