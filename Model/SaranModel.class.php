<?php

class SaranModel extends Model {
    
    // Get all aktivitas fisik
    public function getAllAktivitas() {
        try {
            $sql = "SELECT * FROM aktivitas_fisik ORDER BY id";
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
    
    // Get aktivitas by ID
    public function getAktivitasById($id) {
        try {
            $id = $this->escape($id);
            $sql = "SELECT * FROM aktivitas_fisik WHERE id = '$id'";
            $result = $this->query($sql);
            
            return $result ? $result->fetch_assoc() : null;

        } catch (Exception $e) {
            return null;
        }
    }
    
    // Get user target harian (untuk ditampilkan di summary)
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
    
    // Get statistik konsumsi hari ini (untuk summary section)
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