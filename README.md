# Wellmate
database sabre (tracking dan saran), aku pake workbench

-- Create Database
CREATE DATABASE IF NOT EXISTS wellmate;
USE wellmate;

-- Table untuk jenis minuman
CREATE TABLE IF NOT EXISTS jenis_minuman (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(50) NOT NULL,
    ikon VARCHAR(10) NOT NULL,
    warna VARCHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table untuk catatan minum harian
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

-- Table untuk target harian user
CREATE TABLE IF NOT EXISTS user_target (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT 1,
    target_harian INT NOT NULL DEFAULT 2500,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id)
);

-- Table untuk aktivitas fisik
CREATE TABLE IF NOT EXISTS aktivitas_fisik (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    ikon VARCHAR(10) NOT NULL,
    cairan_tambahan VARCHAR(20) NOT NULL,
    deskripsi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data jenis minuman
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

-- Insert default user target
INSERT INTO user_target (user_id, target_harian) VALUES (1, 2500);

-- Insert sample data catatan minum untuk hari ini
INSERT INTO catatan_minum (user_id, jenis, jumlah, waktu, tanggal) VALUES
(1, 'Teh Hijau', 500, '10:15', CURDATE()),
(1, 'Air Putih', 600, '12:00', CURDATE()),
(1, 'Air Putih', 250, '14:30', CURDATE()),
(1, 'Jus Buah', 450, '14:55', CURDATE()),
(1, 'Kopi', 250, '16:30', CURDATE());
