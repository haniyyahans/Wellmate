<?php

class PenggunaModel extends Model {
    
    /**
     * Get data pengguna berdasarkan id_akun
     */
    public function getDataPengguna($idAkun) {
        try {
            $idAkun = $this->escape($idAkun);
            $sql = "SELECT p.*, a.username 
                    FROM pengguna p
                    INNER JOIN akun a ON p.id_akun = a.id_akun
                    WHERE p.id_akun = '$idAkun'";
            
            $result = $this->query($sql);
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row;
            }
            
            return null;

        } catch (Exception $e) {
            error_log("Error getDataPengguna: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Hitung target minum harian berdasarkan berat badan dan usia
     * Rumus: Berat Badan (kg) × 35ml (untuk dewasa normal)
     * Penyesuaian berdasarkan usia:
     * - Usia 18-30: × 35ml
     * - Usia 31-55: × 33ml
     * - Usia 56+: × 30ml
     */
    public function hitungTargetMinumHarian($beratBadan, $usia) {
        try {
            if (empty($beratBadan) || empty($usia) || $beratBadan <= 0 || $usia <= 0) {
                return 0; // Default jika data tidak lengkap
            }
            
            // Tentukan faktor pengali berdasarkan usia
            $faktorPengali = 35; // Default untuk usia 18-30
            
            if ($usia >= 31 && $usia <= 55) {
                $faktorPengali = 33;
            } elseif ($usia > 55) {
                $faktorPengali = 30;
            }
            
            // Hitung target (dalam ml)
            $targetMinum = $beratBadan * $faktorPengali;
            
            // Bulatkan ke kelipatan 100ml terdekat
            $targetMinum = round($targetMinum / 100) * 100;
            
            // Pastikan minimal 1500ml dan maksimal 5000ml
            if ($targetMinum < 1500) {
                $targetMinum = 1500;
            } elseif ($targetMinum > 5000) {
                $targetMinum = 5000;
            }
            
            return $targetMinum;

        } catch (Exception $e) {
            error_log("Error hitungTargetMinumHarian: " . $e->getMessage());
            return 0; // Default jika error
        }
    }
    
    /**
     * Simpan atau update target harian user
     */
    public function simpanTargetHarian($idAkun, $targetHarian) {
        try {
            $idAkun = $this->escape($idAkun);
            $targetHarian = $this->escape($targetHarian);
            
            // Cek apakah sudah ada data target untuk user ini
            $sqlCheck = "SELECT id FROM user_target WHERE id_akun = '$idAkun'";
            $resultCheck = $this->query($sqlCheck);
            
            if ($resultCheck && $resultCheck->num_rows > 0) {
                // Update jika sudah ada
                $sql = "UPDATE user_target 
                        SET target_harian = '$targetHarian',
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id_akun = '$idAkun'";
            } else {
                // Insert jika belum ada
                $sql = "INSERT INTO user_target (id_akun, user_id, target_harian) 
                        VALUES ('$idAkun', '$idAkun', '$targetHarian')";
            }
            
            return $this->query($sql);

        } catch (Exception $e) {
            error_log("Error simpanTargetHarian: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update data pengguna (berat badan dan usia)
     */
    public function updateDataPengguna($idAkun, $nama, $beratBadan, $usia) {
        try {
            $idAkun = $this->escape($idAkun);
            $nama = $this->escape($nama);
            $beratBadan = $this->escape($beratBadan);
            $usia = $this->escape($usia);
            
            // Cek apakah data pengguna sudah ada
            $sqlCheck = "SELECT id_pengguna FROM pengguna WHERE id_akun = '$idAkun'";
            $resultCheck = $this->query($sqlCheck);
            
            if ($resultCheck && $resultCheck->num_rows > 0) {
                // Update jika sudah ada
                $sql = "UPDATE pengguna 
                        SET nama = '$nama',
                            berat_badan = '$beratBadan',
                            usia = '$usia',
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id_akun = '$idAkun'";
            } else {
                // Insert jika belum ada
                $sql = "INSERT INTO pengguna (id_akun, nama, berat_badan, usia) 
                        VALUES ('$idAkun', '$nama', '$beratBadan', '$usia')";
            }
            
            $result = $this->query($sql);
            
            // Jika berhasil update data pengguna, hitung dan simpan target baru
            if ($result) {
                $targetBaru = $this->hitungTargetMinumHarian($beratBadan, $usia);
                $this->simpanTargetHarian($idAkun, $targetBaru);
            }
            
            return $result;

        } catch (Exception $e) {
            error_log("Error updateDataPengguna: " . $e->getMessage());
            return false;
        }
    }
}