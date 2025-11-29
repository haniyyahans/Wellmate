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
    public function getTargetHarian($userId = 1) {
        try {
            $userId = $this->escape($userId);
            $sql = "SELECT target_harian FROM user_target WHERE user_id = '$userId'";
            $result = $this->query($sql);
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row['target_harian'];
            }
            
            return 2500; // Default target

        } catch (Exception $e) {
            return 2500;
        }
    }
    
    // Get catatan minum by date
    public function getCatatanMinumByDate($tanggal, $userId = 1) {
        try {
            $tanggal = $this->escape($tanggal);
            $userId = $this->escape($userId);
            
            $sql = "SELECT * FROM catatan_minum 
                    WHERE tanggal = '$tanggal' AND user_id = '$userId'
                    ORDER BY waktu ASC";
            
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
    public function tambahCatatanMinum($jenis, $jumlah, $waktu, $tanggal, $userId = 1) {
        try {
            if (!$this->isConnected()) {
                return false;
            }
            
            $jenis = $this->escape($jenis);
            $jumlah = $this->escape($jumlah);
            $waktu = $this->escape($waktu);
            $tanggal = $this->escape($tanggal);
            $userId = $this->escape($userId);
            
            $sql = "INSERT INTO catatan_minum (user_id, jenis, jumlah, waktu, tanggal) 
                    VALUES ('$userId', '$jenis', '$jumlah', '$waktu', '$tanggal')";
            
            $result = $this->query($sql);
            
            return ($result && $this->db !== null) ? $this->db->insert_id : false;

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
    public function hapusCatatanMinum($id) {
        try {
            $id = $this->escape($id);
            $sql = "DELETE FROM catatan_minum WHERE id = '$id'";
            
            return $this->query($sql);

        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get statistics for today
    public function getStatistikHariIni($userId = 1) {
        try {
            $userId = $this->escape($userId);
            $today = date('Y-m-d');
            
            $sql = "SELECT 
                        SUM(jumlah) as total_diminum,
                        COUNT(*) as jumlah_catatan
                    FROM catatan_minum 
                    WHERE user_id = '$userId' AND tanggal = '$today'";
            
            $result = $this->query($sql);
            
            if ($result && $row = $result->fetch_assoc()) {
                return [
                    'total_diminum' => $row['total_diminum'] ?? 0,
                    'jumlah_catatan' => $row['jumlah_catatan'] ?? 0
                ];
            }
            
            return ['total_diminum' => 0, 'jumlah_catatan' => 0];

        } catch (Exception $e) {
            return ['total_diminum' => 0, 'jumlah_catatan' => 0];
        }
    }
}