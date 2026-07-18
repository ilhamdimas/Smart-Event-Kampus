<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';

// Ambil data event yang akan diedit
$stmt = mysqli_prepare($koneksi, "SELECT * FROM event WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    header('Location: event.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = $_POST['tanggal_pelaksanaan'] ?? '';
    $waktu = $_POST['waktu_pelaksanaan'] ?? '';
    $lokasi = trim($_POST['lokasi'] ?? '');
    $penyelenggara = trim($_POST['penyelenggara'] ?? '');
    $kuota = (int)($_POST['kuota'] ?? 0);
    $status = $_POST['status'] ?? 'Akan Datang';
    $gambar = $event['gambar']; // pertahankan gambar lama secara default

    if ($judul === '' || $kategori === '' || $deskripsi === '' || $tanggal === '' || $waktu === '' || $lokasi === '' || $penyelenggara === '') {
        $error = 'Semua field wajib diisi.';
        $event = array_merge($event, $_POST); // supaya form tetap terisi
    } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // ---- Ganti gambar (opsional) ----
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $tipe = mime_content_type($_FILES['gambar']['tmp_name']);

        if (!in_array($tipe, $allowed)) {
            $error = 'Format gambar harus JPG, PNG, atau WEBP.';
        } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $error = 'Ukuran gambar maksimal 2MB.';
        } else {
            $folder_upload = 'assets/uploads/events/';
            if (!is_dir($folder_upload)) {
                mkdir($folder_upload, 0755, true);
            }
            // Hapus gambar lama kalau ada
            if (!empty($event['gambar']) && file_exists($folder_upload . $event['gambar'])) {
                unlink($folder_upload . $event['gambar']);
            }
            $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $nama_file = uniqid('event_') . '.' . $ekstensi;
            move_uploaded_file($_FILES['gambar']['tmp_name'], $folder_upload . $nama_file);
            $gambar = $nama_file;
        }
    }

    if ($error === '') {
        $update = mysqli_prepare($koneksi, "UPDATE event SET judul=?, kategori=?, deskripsi=?, tanggal_pelaksanaan=?, waktu_pelaksanaan=?, lokasi=?, penyelenggara=?, kuota=?, status=?, gambar=? WHERE id=?");
        mysqli_stmt_bind_param($update, 'sssssssissi', $judul, $kategori, $deskripsi, $tanggal, $waktu, $lokasi, $penyelenggara, $kuota, $status, $gambar, $id);

        if (mysqli_stmt_execute($update)) {
            header('Location: event.php?msg=updated');
            exit;
        } else {
            $error = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
        }
        mysqli_stmt_close($update);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Event - Smart Event Campus</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'config/navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Edit Event</h2>
        <a href="event.php" class="btn btn-secondary">&larr; Kembali</a>
    </div>

    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_event.php?id=<?= $id ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Event</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($event['judul']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <?php foreach (['Seminar','Workshop','Lomba','Pelatihan'] as $k): ?>
                            <option value="<?= $k ?>" <?= $event['kategori'] === $k ? 'selected' : '' ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <?php foreach (['Akan Datang','Berlangsung','Selesai'] as $s): ?>
                            <option value="<?= $s ?>" <?= $event['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4" required><?= htmlspecialchars($event['deskripsi']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal_pelaksanaan" value="<?= htmlspecialchars($event['tanggal_pelaksanaan']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Waktu Pelaksanaan</label>
                    <input type="time" name="waktu_pelaksanaan" value="<?= htmlspecialchars(substr($event['waktu_pelaksanaan'],0,5)) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" value="<?= htmlspecialchars($event['lokasi']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Penyelenggara</label>
                    <input type="text" name="penyelenggara" value="<?= htmlspecialchars($event['penyelenggara']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kuota Peserta</label>
                <input type="number" name="kuota" min="0" value="<?= htmlspecialchars($event['kuota']) ?>">
            </div>

            <div class="form-group">
                <label>Gambar / Poster Event</label>

                <?php if (!empty($event['gambar'])): ?>
                <div class="current-image-preview">
                    <img src="assets/uploads/events/<?= htmlspecialchars($event['gambar']) ?>" alt="Gambar saat ini">
                    <span>Gambar saat ini.<br>Pilih file baru di bawah untuk menggantinya.</span>
                </div>
                <?php endif; ?>

                <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp">
                <span class="field-hint">Opsional. Kosongkan jika tidak ingin mengganti gambar. Format JPG/PNG/WEBP, maksimal 2MB.</span>
            </div>

            <button type="submit" class="btn btn-warning">Update Event</button>
        </form>
    </div>
</div>
</body>
</html>
