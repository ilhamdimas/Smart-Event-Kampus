-- =========================================================
-- Database: smart_event_campus
-- Deskripsi: Website Smart Event Campus
--            (Sistem Informasi Kegiatan Kampus)
-- =========================================================

CREATE DATABASE IF NOT EXISTS smart_event_campus;
USE smart_event_campus;

-- Tabel admin (untuk login)
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel event kegiatan kampus
CREATE TABLE IF NOT EXISTS event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    kategori ENUM('Seminar', 'Workshop', 'Lomba', 'Pelatihan') NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal_pelaksanaan DATE NOT NULL,
    waktu_pelaksanaan TIME NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    penyelenggara VARCHAR(100) NOT NULL,
    kuota INT DEFAULT 0,
    status ENUM('Akan Datang', 'Berlangsung', 'Selesai') DEFAULT 'Akan Datang',
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Akun admin default
-- username: admin
-- password: admin123  (sudah di-hash dengan password_hash PHP - bcrypt)
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2b$10$2MjGh/b.j0NT4oF///ojeenmqyavWIHSF9qtaA5IrXE3b/3j3rHzK', 'Administrator Kampus');
-- Catatan: hash di atas adalah hasil password_hash('admin123', PASSWORD_BCRYPT)

-- Contoh data event (dummy)
INSERT INTO event (judul, kategori, deskripsi, tanggal_pelaksanaan, waktu_pelaksanaan, lokasi, penyelenggara, kuota, status) VALUES
('Seminar Nasional Teknologi AI', 'Seminar', 'Seminar membahas perkembangan kecerdasan buatan dan penerapannya di dunia industri.', '2026-08-15', '09:00:00', 'Aula Gedung Rektorat', 'Fakultas Ilmu Komputer', 200, 'Akan Datang'),
('Workshop Pengembangan Web dengan PHP', 'Workshop', 'Pelatihan praktik langsung membangun aplikasi web menggunakan PHP dan MySQL.', '2026-07-20', '13:00:00', 'Lab Komputer 3', 'Himpunan Mahasiswa Informatika', 40, 'Akan Datang'),
('Lomba Coding Competition 2026', 'Lomba', 'Kompetisi pemrograman antar mahasiswa tingkat universitas.', '2026-09-05', '08:00:00', 'Gedung Serbaguna', 'BEM Fakultas Teknik', 100, 'Akan Datang'),
('Pelatihan Public Speaking', 'Pelatihan', 'Pelatihan pengembangan soft skill berbicara di depan umum bagi mahasiswa.', '2026-07-25', '10:00:00', 'Ruang Seminar Lantai 2', 'Unit Kegiatan Mahasiswa', 60, 'Akan Datang');
