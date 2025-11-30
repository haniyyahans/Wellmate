<?php
require_once __DIR__ . '/Model.class.php';

class BerandaModel extends Model { 
    
    /**
     * Mengambil biodata berdasarkan id_pengguna
     */
    public function getBiodataByIdPengguna($id_pengguna) {
        try {
            $id_pengguna = intval($id_pengguna);
            $sql = "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna";
            $result = $this->db->query($sql);
            
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Menambah atau update biodata user
     */
    public function tambahBiodata($data) {
        try {
            if (empty($data['id_pengguna']) || empty($data['nama']) || empty($data['berat_badan'])) {
                return [
                    'success' => false,
                    'message' => 'Data id_pengguna, nama, dan berat_badan wajib diisi'
                ];
            }
            
            $id_pengguna = intval($data['id_pengguna']);
            $nama = $this->escape($data['nama']);
            $berat_badan = floatval($data['berat_badan']);
            $usia = isset($data['usia']) ? intval($data['usia']) : null;
            
            if ($berat_badan <= 0 || $berat_badan > 300) {
                return [
                    'success' => false,
                    'message' => 'Berat badan tidak valid (harus antara 0-300 kg)'
                ];
            }
            
            // Cek apakah data sudah ada
            $checkSql = "SELECT id_pengguna FROM pengguna WHERE id_pengguna = $id_pengguna";
            $checkResult = $this->db->query($checkSql);
            
            if ($checkResult->num_rows > 0) {
                // Update jika sudah ada
                return $this->updateBiodata($id_pengguna, $data);
            }
            
            // Insert jika belum ada (ini untuk kasus khusus)
            $sql = "UPDATE pengguna SET 
                    nama = '$nama', 
                    berat_badan = $berat_badan, 
                    usia = " . ($usia ? $usia : "NULL") . ",
                    updated_at = NOW()
                    WHERE id_pengguna = $id_pengguna";
            
            $result = $this->db->query($sql);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Biodata berhasil ditambahkan',
                    'data' => $this->getBiodataByIdPengguna($id_pengguna)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan biodata'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update biodata user yang sudah ada
     */
    public function updateBiodata($id_pengguna, $data) {
        try {
            if (empty($id_pengguna)) {
                return [
                    'success' => false,
                    'message' => 'ID pengguna tidak ditemukan'
                ];
            }
            
            $id_pengguna = intval($id_pengguna);
            
            // Cek apakah pengguna ada
            $checkSql = "SELECT id_pengguna FROM pengguna WHERE id_pengguna = $id_pengguna";
            $checkResult = $this->db->query($checkSql);
            
            if ($checkResult->num_rows == 0) {
                return [
                    'success' => false,
                    'message' => 'Pengguna dengan ID tersebut tidak ditemukan'
                ];
            }
            
            // Build update query dinamis
            $updateFields = [];
            
            if (isset($data['nama']) && !empty($data['nama'])) {
                $nama = $this->escape($data['nama']);
                $updateFields[] = "nama = '$nama'";
            }
            
            if (isset($data['berat_badan']) && !empty($data['berat_badan'])) {
                $berat_badan = floatval($data['berat_badan']);
                if ($berat_badan > 0 && $berat_badan <= 300) {
                    $updateFields[] = "berat_badan = $berat_badan";
                }
            }
            
            if (isset($data['usia'])) {
                $usia = intval($data['usia']);
                $updateFields[] = "usia = " . ($usia > 0 ? $usia : "NULL");
            }
            
            if (empty($updateFields)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data yang diupdate'
                ];
            }
            
            $updateFields[] = "updated_at = NOW()";
            
            $sql = "UPDATE pengguna SET " . implode(', ', $updateFields) . " WHERE id_pengguna = $id_pengguna";
            $result = $this->db->query($sql);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Biodata berhasil diupdate',
                    'data' => $this->getBiodataByIdPengguna($id_pengguna)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate biodata'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Menghitung target kebutuhan cairan harian berdasarkan berat badan
     */
    public function hitungKebutuhanCairan($beratBadan, $aktivitas = 'sedang') {
        try {
            $beratBadan = floatval($beratBadan);
            
            if ($beratBadan <= 0 || $beratBadan > 300) {
                return [
                    'success' => false,
                    'message' => 'Berat badan tidak valid (harus antara 0-300 kg)'
                ];
            }
            
            // Hitung kebutuhan cairan dasar
            if ($beratBadan <= 30) {
                $kebutuhanDasar = $beratBadan * 100;
            } elseif ($beratBadan <= 50) {
                $kebutuhanDasar = 1500 + (($beratBadan - 20) * 50);
            } else {
                $kebutuhanDasar = 2500 + (($beratBadan - 50) * 15);
            }
            
            // Faktor aktivitas
            $faktorAktivitas = 1.0;
            switch (strtolower($aktivitas)) {
                case 'ringan':
                    $faktorAktivitas = 1.0;
                    break;
                case 'sedang':
                    $faktorAktivitas = 1.2;
                    break;
                case 'berat':
                    $faktorAktivitas = 1.5;
                    break;
                default:
                    $faktorAktivitas = 1.2;
            }
            
            $totalKebutuhan = round($kebutuhanDasar * $faktorAktivitas);
            $jumlahGelas = ceil($totalKebutuhan / 250);
            
            return [
                'success' => true,
                'message' => 'Perhitungan berhasil',
                'data' => [
                    'berat_badan' => $beratBadan,
                    'aktivitas' => $aktivitas,
                    'kebutuhan_dasar_ml' => round($kebutuhanDasar),
                    'faktor_aktivitas' => $faktorAktivitas,
                    'total_kebutuhan_ml' => $totalKebutuhan,
                    'jumlah_gelas' => $jumlahGelas,
                    'rekomendasi' => "Disarankan minum $jumlahGelas gelas air per hari ($totalKebutuhan ml)"
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
