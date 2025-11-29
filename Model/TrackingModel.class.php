
<?php

class TrackingModel extends Model {
    
    // Get all jenis minuman
    public function getJenisMinuman() {
        try {
            $sql = "SELECT * FROM jenis_minuman ORDER BY id";
            $result = $this->query($sql);
            $data = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            return $data;

        } catch (Exception $e) {
            return [];
        }
    }
    
    // Get user target harian
    public function getTargetHarian($userId) { // parameter $userId tanpa default
        try {
            $userId = $this->escape($userId);
            $sql = "SELECT target_harian FROM user_target WHERE user_id = '$userId'";
            $result = $this->query($sql);
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row['target_harian'];
            }
            
            return 0; // Default target jika data belum ada
        } catch (Exception $e) {
            return 0;
        }
    }
    
    // Get catatan minum by date
    public function getCatatanMinumByDate($tanggal, $userId) { // parameter $userId tanpa default
        try {
            $tanggal = $this->escape($tanggal);
            $userId = $this->escape($userId);
            
            // Query filter berdasarkan user_id
            $sql = "SELECT cm.id, cm.jumlah, cm.waktu, jm.nama as jenis_minuman, jm.warna
                    FROM catatan_minum cm
                    JOIN jenis_minuman jm ON cm.jenis_minuman_id = jm.id
                    WHERE cm.user_id = '$userId' AND cm.tanggal = '$tanggal'
                    ORDER BY cm.waktu DESC";
            
            $result = $this->query($sql);
            $data = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            return $data;

        } catch (Exception $e) {
            return [];
        }
    }
    
    // Add new catatan minum
    public function tambahCatatanMinum($jenisMinumanId, $jumlah, $userId) { // parameter $userId tanpa default
        try {
            $jenisMinumanId = $this->escape($jenisMinumanId);
            $jumlah = $this->escape($jumlah);
            $userId = $this->escape($userId); // Pastikan user ID dinamis digunakan
            $tanggal = date('Y-m-d');
            $waktu = date('H:i:s');

            $sql = "INSERT INTO catatan_minum (user_id, jenis_minuman_id, jumlah, tanggal, waktu) 
                    VALUES ('$userId', '$jenisMinumanId', '$jumlah', '$tanggal', '$waktu')";
            
            return $this->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    
    // Update catatan minum
    public function updateCatatanMinum($id, $jenis, $jumlah, $waktu) {
        try {
            $id = $this->escape($id);
            $jenis = $this->escape($jenis);
            $jumlah = $this->escape($jumlah);
            $waktu = $this->escape($waktu);
            
            $sql = "UPDATE catatan_minum 
                    SET jenis = '$jenis', jumlah = '$jumlah', waktu = '$waktu'
                    WHERE id = '$id'";
            
            return $this->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    
    // Delete catatan minum
    public function hapusCatatanMinum($id, $userId) { // Tambahkan $userId untuk filter keamanan
        try {
            $id = $this->escape($id);
            $userId = $this->escape($userId);
            
            // Hapus berdasarkan ID catatan DAN user_id
            $sql = "DELETE FROM catatan_minum WHERE id = '$id' AND user_id = '$userId'";
            
            return $this->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get statistics for today
    public function getStatistikHariIni($userId) { // parameter $userId tanpa default
        try {
            $userId = $this->escape($userId);
            $today = date('Y-m-d');
            
            $sql = "SELECT 
                        SUM(jumlah) as total_diminum,
                        COUNT(*) as jumlah_catatan
                    FROM catatan_minum 
                    WHERE user_id = '$userId' AND tanggal = '$today'";
            
            // ... rest of the code
            
            return ['total_diminum' => 0, 'jumlah_catatan' => 0];
        } catch (Exception $e) {
            return ['total_diminum' => 0, 'jumlah_catatan' => 0];
        }
    }
}
