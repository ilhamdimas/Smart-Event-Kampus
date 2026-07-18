<?php
/**
 * Konfigurasi Koneksi Database
 * Smart Event Campus
 *
 * Sesuaikan HOST, USER, PASS, dan NAME dengan kredensial hosting Anda.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai user database hosting
define('DB_PASS', '');           // ganti sesuai password database hosting
define('DB_NAME', 'smart_event_campus');

try {
    $koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($koneksi, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die(
        '<div style="font-family: sans-serif; max-width:700px; margin:60px auto; padding:20px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; color:#7f1d1d;">'
        . '<h2 style="margin-top:0;">Koneksi Database Gagal</h2>'
        . '<p><b>Pesan error:</b> ' . htmlspecialchars($e->getMessage()) . '</p>'
        . '<p>Cek kembali <code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASS</code>, dan <code>DB_NAME</code> '
        . 'di file <code>config/koneksi.php</code> — pastikan sesuai dengan kredensial database di hosting Anda.</p>'
        . '</div>'
    );
}
