-- ============================================
-- DATABASE WELLMATE
-- ============================================

-- Buat Database
CREATE DATABASE IF NOT EXISTS wellmate;
USE wellmate;

-- ============================================
-- TABEL PENGGUNA
-- ============================================
CREATE TABLE pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    id_akun INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    berat_badan DECIMAL(5,2),
    usia INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_id_akun (id_akun)
);

-- ============================================
-- TABEL JENIS MINUMAN
-- ============================================
CREATE TABLE IF NOT EXISTS jenis_minuman (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(50) NOT NULL,
    ikon VARCHAR(10) NOT NULL,
    warna VARCHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABEL CATATAN MINUM HARIAN
-- ============================================
CREATE TABLE IF NOT EXISTS catatan_minum (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT 1,
    jenis VARCHAR(50) NOT NULL,
    jumlah INT NOT NULL,
    waktu VARCHAR(10) NOT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_tanggal (user_id, tanggal)
);

-- ============================================
-- TABEL TARGET HARIAN USER
-- ============================================
CREATE TABLE IF NOT EXISTS user_target (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT 1,
    target_harian INT NOT NULL DEFAULT 2500,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id)
);

-- ============================================
-- TABEL AKTIVITAS FISIK
-- ============================================
CREATE TABLE IF NOT EXISTS aktivitas_fisik (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    ikon VARCHAR(10) NOT NULL,
    cairan_tambahan VARCHAR(20) NOT NULL,
    deskripsi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- INSERT DATA DUMMY - JENIS MINUMAN
-- ============================================
INSERT INTO jenis_minuman (nama, ikon, warna) VALUES
('Air Putih', '💧', '#3478F5'),
('Teh Hijau', '🍵', '#34C759'),
('Jus Buah', '🍊', '#FFB300'),
('Kopi', '☕', '#8B4513'),
('Susu', '🥛', '#F0E68C'),
('Soda', '🥤', '#87CEFA'),
('Jamu', '🌿', '#A0522D'),
('Yogurt', '🥄', '#FFDEAD');

-- Insert data aktivitas fisik
-- ============================================
-- INSERT DATA DUMMY - AKTIVITAS FISIK
-- ============================================
INSERT INTO aktivitas_fisik (nama, ikon, cairan_tambahan, deskripsi) VALUES
('Lari 30-60 menit', '🏃', '±400 ml', 'Aktivitas kardio yang meningkatkan detak jantung dan membuat tubuh cepat kehilangan cairan melalui keringat. Sangat disarankan untuk minum sebelum, selama, dan setelah lari.'),
('Gym 1 jam', '🏋️', '±500 ml', 'Latihan beban dan kardio intens dalam ruangan yang meningkatkan suhu tubuh. Kehilangan keringat tinggi. Pastikan asupan cairan untuk menjaga performa.'),
('Angkat Beban 45-60 menit', '💪', '±300 ml', 'Meskipun tidak seintensif kardio, angkat beban tetap menyebabkan kehilangan cairan. Minum secara teratur antar set untuk menjaga fokus dan energi.'),
('Bersepeda 45 menit', '🚴', '±450 ml', 'Bersepeda, terutama di luar ruangan, membutuhkan asupan cairan yang konsisten. Kebutuhan cairan bisa lebih tinggi jika cuaca panas.'),
('Kerja Fisik', '👷', '±600 ml', 'Bekerja di luar atau di lingkungan panas/lembab memerlukan perhatian ekstra pada hidrasi. Disarankan minum setiap 20-30 menit kerja.'),
('Aerobik atau Zumba 45 menit', '💃', '±350 ml', 'Aktivitas grup yang energik dan bergerak cepat. Kehilangan cairan terjadi secara cepat. Jaga botol air di dekat Anda.'),
('Mendaki 2-4 jam', '⛰️', '±1.5 L', 'Aktivitas yang berlangsung lama dan sering di lingkungan yang menantang (pulau/dataran tinggi). Wajib membawa cairan yang cukup dan elektrolit.'),
('Olahraga 1-2 jam', '⚽', '±750 ml', 'Aktivitas olahraga tim (sepak bola, basket) yang memerlukan gerakan sporadis intens. Konsumsi cairan di waktu istirahat sangat penting.'),
('Lainnya', '✨', '±250 ml', 'Untuk aktivitas ringan atau durasi yang lebih singkat, tambahan 250ml sudah cukup. Sesuaikan dengan rasa haus Anda.');

-- ============================================
-- INSERT DATA DUMMY - DEFAULT USER TARGET
-- ============================================
INSERT INTO user_target (user_id, target_harian) VALUES (1, 2500);

-- =================================================
-- INSERT DATA DUMMY - CATATAN MINUM UNTUK HARI INI
-- =================================================
INSERT INTO catatan_minum (user_id, jenis, jumlah, waktu, tanggal) VALUES
(1, 'Teh Hijau', 500, '10:15', CURDATE()),
(1, 'Air Putih', 600, '12:00', CURDATE()),
(1, 'Air Putih', 250, '14:30', CURDATE()),
(1, 'Jus Buah', 450, '14:55', CURDATE()),
(1, 'Kopi', 250, '16:30', CURDATE());

-- ============================================
-- TABEL NOTIFIKASI
-- ============================================
CREATE TABLE notifikasi (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    pesan TEXT NOT NULL,
    waktu_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_pengguna_status (id_pengguna, status),
    INDEX idx_waktu_kirim (waktu_kirim)
);

-- ============================================
-- TABEL TEMAN
-- ============================================
CREATE TABLE teman (
    id_teman INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    id_user_teman INT NOT NULL,
    status VARCHAR(30) DEFAULT 'pending',
    tanggal DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_user_teman) REFERENCES pengguna(id_pengguna) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_friendship (id_pengguna, id_user_teman),
    INDEX idx_status (status),
    INDEX idx_user_teman (id_user_teman),
    CONSTRAINT chk_different_users CHECK (id_pengguna != id_user_teman),
    CONSTRAINT chk_status CHECK (status IN ('pending', 'accepted', 'declined'))
);

-- ============================================
-- INSERT DATA DUMMY - PENGGUNA
-- ============================================
INSERT INTO pengguna (id_akun, nama, berat_badan, usia) VALUES
(1, 'Siti Nurhaliza', 55.5, 20),
(2, 'Rani Wijaya', 58.0, 21),
(3, 'Dewi Lestari', 52.3, 19),
(4, 'Putri Ayu', 60.0, 22),
(5, 'Maya Sari', 57.5, 20),
(6, 'Linda Kusuma', 54.0, 21),
(7, 'Aisyah Rahman', 56.8, 20),
(8, 'Nurul Fadilah', 59.2, 22);

-- ============================================
-- INSERT DATA DUMMY - NOTIFIKASI
-- ============================================
INSERT INTO notifikasi (id_pengguna, pesan, waktu_kirim, status) VALUES
-- Notifikasi untuk Siti (id_pengguna = 1)
(1, 'Anda sudah lama tidak minum, ayo minum segelas air 💧', '2025-01-15 10:30:00', 'unread'),
(1, 'Selamat! Target hidrasi harian Anda tercapai 🎉', '2025-01-14 20:00:00', 'read'),
(1, 'Putri Ayu menerima permintaan pertemanan Anda', '2025-01-14 15:45:00', 'read'),

-- Notifikasi untuk Rani (id_pengguna = 2)
(2, 'Jangan lupa minum air putih! Sudah 3 jam sejak konsumsi terakhir', '2025-01-15 14:20:00', 'unread'),
(2, 'Maya Sari mengirim permintaan pertemanan', '2025-01-15 09:15:00', 'unread'),
(2, 'Anda terlalu banyak mengonsumsi minuman berkafein hari ini ☕', '2025-01-14 16:30:00', 'read'),

-- Notifikasi untuk Dewi (id_pengguna = 3)
(3, 'Target mingguan Anda: 85% tercapai. Tetap semangat! 💪', '2025-01-15 08:00:00', 'read'),
(3, 'Linda Kusuma menerima permintaan pertemanan Anda', '2025-01-14 11:20:00', 'read'),

-- Notifikasi untuk Putri (id_pengguna = 4)
(4, 'Sudah waktunya minum air! Target harian Anda masih 40%', '2025-01-15 12:00:00', 'unread'),
(4, 'Siti Nurhaliza mengirim permintaan pertemanan', '2025-01-14 15:30:00', 'read'),

-- Notifikasi untuk Maya (id_pengguna = 5)
(5, 'Peringatan: Konsumsi minuman manis berlebihan hari ini 🍹', '2025-01-15 17:45:00', 'unread'),
(5, 'Rani Wijaya menerima permintaan pertemanan Anda', '2025-01-15 09:20:00', 'read'),

-- Notifikasi untuk Linda (id_pengguna = 6)
(6, 'Jangan lupa minum setelah olahraga! Tambahkan 500ml 🏃‍♀️', '2025-01-15 07:30:00', 'read'),
(6, 'Dewi Lestari mengirim permintaan pertemanan', '2025-01-14 11:15:00', 'read'),

-- Notifikasi untuk Aisyah (id_pengguna = 7)
(7, 'Hebat! Anda konsisten minum air selama 7 hari berturut-turut 🌟', '2025-01-15 20:00:00', 'unread'),
(7, 'Target hidrasi harian: 2000ml. Sisa: 600ml', '2025-01-15 18:30:00', 'unread'),

-- Notifikasi untuk Nurul (id_pengguna = 8)
(8, 'Sudah 4 jam tidak ada catatan minum. Yuk minum air putih! 💙', '2025-01-15 16:15:00', 'unread'),
(8, 'Aisyah Rahman mengirim permintaan pertemanan', '2025-01-15 10:00:00', 'unread');

-- ============================================
-- INSERT DATA DUMMY - TEMAN
-- ============================================
INSERT INTO teman (id_pengguna, id_user_teman, status, tanggal) VALUES
-- Pertemanan yang sudah diterima (accepted)
(1, 4, 'accepted', '2025-01-14'),
(1, 2, 'accepted', '2025-01-13'),
(2, 5, 'accepted', '2025-01-15'),
(3, 6, 'accepted', '2025-01-14'),
(4, 1, 'accepted', '2025-01-14'),
(5, 2, 'accepted', '2025-01-15'),
(6, 3, 'accepted', '2025-01-14'),
(7, 8, 'accepted', '2025-01-12'),

-- Permintaan pertemanan pending
(2, 1, 'pending', '2025-01-15'),
(5, 4, 'pending', '2025-01-15'),
(8, 7, 'pending', '2025-01-15'),
(6, 7, 'pending', '2025-01-14'),

-- Permintaan yang ditolak
(3, 5, 'declined', '2025-01-13'),
(4, 6, 'declined', '2025-01-12');
