-- ============================================
-- DATABASE WELLMATE - FITUR NOTIFIKASI & TEMAN
-- ============================================

-- Buat Database
CREATE DATABASE IF NOT EXISTS wellmate;
USE wellmate;

-- ============================================
-- TABEL PENGGUNA (untuk referensi FK)
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

-- ============================================
-- QUERY UTILITAS
-- ============================================

-- View: Notifikasi belum dibaca per pengguna
CREATE VIEW v_notifikasi_unread AS
SELECT 
    n.id_notif,
    n.id_pengguna,
    p.nama as nama_pengguna,
    n.pesan,
    n.waktu_kirim,
    n.status
FROM notifikasi n
JOIN pengguna p ON n.id_pengguna = p.id_pengguna
WHERE n.status = 'unread'
ORDER BY n.waktu_kirim DESC;

-- View: Daftar teman aktif
CREATE VIEW v_daftar_teman_aktif AS
SELECT 
    t.id_teman,
    p1.id_pengguna,
    p1.nama as nama_pengguna,
    p2.id_pengguna as id_teman_user,
    p2.nama as nama_teman,
    t.status,
    t.tanggal
FROM teman t
JOIN pengguna p1 ON t.id_pengguna = p1.id_pengguna
JOIN pengguna p2 ON t.id_user_teman = p2.id_pengguna
WHERE t.status = 'accepted'
ORDER BY t.tanggal DESC;

-- View: Permintaan pertemanan pending
CREATE VIEW v_permintaan_pending AS
SELECT 
    t.id_teman,
    p1.id_pengguna as pengirim_id,
    p1.nama as pengirim_nama,
    p2.id_pengguna as penerima_id,
    p2.nama as penerima_nama,
    t.tanggal
FROM teman t
JOIN pengguna p1 ON t.id_pengguna = p1.id_pengguna
JOIN pengguna p2 ON t.id_user_teman = p2.id_pengguna
WHERE t.status = 'pending'
ORDER BY t.tanggal DESC;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure: Kirim notifikasi pengingat minum
DELIMITER $$
CREATE PROCEDURE sp_kirim_notifikasi_pengingat(
    IN p_id_pengguna INT,
    IN p_pesan TEXT
)
BEGIN
    INSERT INTO notifikasi (id_pengguna, pesan, status)
    VALUES (p_id_pengguna, p_pesan, 'unread');
    
    SELECT 'Notifikasi berhasil dikirim' as message;
END$$

-- Procedure: Update status notifikasi menjadi dibaca
CREATE PROCEDURE sp_baca_notifikasi(
    IN p_id_notif INT
)
BEGIN
    UPDATE notifikasi 
    SET status = 'read' 
    WHERE id_notif = p_id_notif;
    
    SELECT 'Notifikasi ditandai sebagai dibaca' as message;
END$$

-- Procedure: Kirim permintaan pertemanan
CREATE PROCEDURE sp_kirim_permintaan_teman(
    IN p_id_pengguna INT,
    IN p_id_user_teman INT
)
BEGIN
    DECLARE pesan_notif TEXT;
    DECLARE nama_pengirim VARCHAR(100);
    
    -- Cek apakah sudah ada permintaan
    IF EXISTS (
        SELECT 1 FROM teman 
        WHERE (id_pengguna = p_id_pengguna AND id_user_teman = p_id_user_teman)
           OR (id_pengguna = p_id_user_teman AND id_user_teman = p_id_pengguna)
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Permintaan pertemanan sudah ada';
    END IF;
    
    -- Insert permintaan teman
    INSERT INTO teman (id_pengguna, id_user_teman, status)
    VALUES (p_id_pengguna, p_id_user_teman, 'pending');
    
    -- Kirim notifikasi ke penerima
    SELECT nama INTO nama_pengirim 
    FROM pengguna 
    WHERE id_pengguna = p_id_pengguna;
    
    SET pesan_notif = CONCAT(nama_pengirim, ' mengirim permintaan pertemanan');
    
    INSERT INTO notifikasi (id_pengguna, pesan, status)
    VALUES (p_id_user_teman, pesan_notif, 'unread');
    
    SELECT 'Permintaan pertemanan berhasil dikirim' as message;
END$$

-- Procedure: Terima/Tolak permintaan pertemanan
CREATE PROCEDURE sp_update_status_teman(
    IN p_id_teman INT,
    IN p_status VARCHAR(30)
)
BEGIN
    DECLARE pesan_notif TEXT;
    DECLARE id_pengirim INT;
    DECLARE nama_penerima VARCHAR(100);
    
    -- Update status pertemanan
    UPDATE teman 
    SET status = p_status, updated_at = CURRENT_TIMESTAMP
    WHERE id_teman = p_id_teman;
    
    -- Kirim notifikasi ke pengirim
    SELECT id_pengguna INTO id_pengirim
    FROM teman
    WHERE id_teman = p_id_teman;
    
    SELECT nama INTO nama_penerima
    FROM pengguna p
    JOIN teman t ON p.id_pengguna = t.id_user_teman
    WHERE t.id_teman = p_id_teman;
    
    IF p_status = 'accepted' THEN
        SET pesan_notif = CONCAT(nama_penerima, ' menerima permintaan pertemanan Anda');
    ELSE
        SET pesan_notif = CONCAT(nama_penerima, ' menolak permintaan pertemanan Anda');
    END IF;
    
    INSERT INTO notifikasi (id_pengguna, pesan, status)
    VALUES (id_pengirim, pesan_notif, 'unread');
    
    SELECT CONCAT('Status pertemanan berhasil diupdate menjadi ', p_status) as message;
END$$

-- Procedure: Hapus teman
CREATE PROCEDURE sp_hapus_teman(
    IN p_id_teman INT
)
BEGIN
    DELETE FROM teman WHERE id_teman = p_id_teman;
    SELECT 'Teman berhasil dihapus' as message;
END$$

DELIMITER ;

-- ============================================
-- CONTOH QUERY PENGGUNAAN
-- ============================================

-- 1. Lihat semua notifikasi belum dibaca
SELECT * FROM v_notifikasi_unread;

-- 2. Lihat daftar teman aktif pengguna tertentu
SELECT * FROM v_daftar_teman_aktif WHERE id_pengguna = 1;

-- 3. Lihat permintaan pertemanan pending
SELECT * FROM v_permintaan_pending WHERE penerima_id = 2;

-- 4. Kirim notifikasi pengingat
CALL sp_kirim_notifikasi_pengingat(1, 'Jangan lupa minum air putih! 💧');

-- 5. Tandai notifikasi sebagai dibaca
CALL sp_baca_notifikasi(1);

-- 6. Kirim permintaan pertemanan
CALL sp_kirim_permintaan_teman(3, 7);

-- 7. Terima permintaan pertemanan
CALL sp_update_status_teman(10, 'accepted');

-- 8. Tolak permintaan pertemanan
CALL sp_update_status_teman(11, 'declined');

-- 9. Hapus teman
CALL sp_hapus_teman(14);

-- ============================================
-- QUERY STATISTIK
-- ============================================

-- Jumlah notifikasi unread per pengguna
SELECT 
    p.id_pengguna,
    p.nama,
    COUNT(n.id_notif) as total_notif_unread
FROM pengguna p
LEFT JOIN notifikasi n ON p.id_pengguna = n.id_pengguna AND n.status = 'unread'
GROUP BY p.id_pengguna, p.nama
ORDER BY total_notif_unread DESC;

-- Jumlah teman aktif per pengguna
SELECT 
    p.id_pengguna,
    p.nama,
    COUNT(t.id_teman) as total_teman
FROM pengguna p
LEFT JOIN teman t ON p.id_pengguna = t.id_pengguna AND t.status = 'accepted'
GROUP BY p.id_pengguna, p.nama
ORDER BY total_teman DESC;

-- Permintaan pertemanan pending per pengguna
SELECT 
    p.id_pengguna,
    p.nama,
    COUNT(t.id_teman) as total_pending
FROM pengguna p
LEFT JOIN teman t ON p.id_pengguna = t.id_user_teman AND t.status = 'pending'
GROUP BY p.id_pengguna, p.nama
HAVING total_pending > 0
ORDER BY total_pending DESC;
