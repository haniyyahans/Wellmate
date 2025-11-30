<?php

require_once __DIR__ . '/Model.class.php';

class BioModel extends Model {
    
    /**
     * Menambah biodata user baru
     * 
     * @param array $data Array berisi data biodata (user_id, nama, berat_badan, tinggi_badan, usia, jenis_kelamin)
     * @return array Response dengan status success/fail
     */
    public function tambahBiodata($data) {
        try {
            // Validasi input
            if (empty($data['user_id']) || empty($data['nama']) || empty($data['berat_badan'])) {
                return [
                    'success' => false,
                    'message' => 'Data user_id, nama, dan berat_badan wajib diisi'
                ];
            }
            
            // Escape data untuk keamanan
            $user_id = $this->escape($data['user_id']);
            $nama = $this->escape($data['nama']);
            $berat_badan = floatval($data['berat_badan']);
            $tinggi_badan = isset($data['tinggi_badan']) ? floatval($data['tinggi_badan']) : null;
            $usia = isset($data['usia']) ? intval($data['usia']) : null;
            $jenis_kelamin = isset($data['jenis_kelamin']) ? $this->escape($data['jenis_kelamin']) : null;
            
            // Validasi berat badan
            if ($berat_badan <= 0 || $berat_badan > 300) {
                return [
                    'success' => false,
                    'message' => 'Berat badan tidak valid (harus antara 0-300 kg)'
                ];
            }
            
            // Query insert
            $sql = "INSERT INTO biodata (user_id, nama, berat_badan, tinggi_badan, usia, jenis_kelamin, created_at) 
                    VALUES ('$user_id', '$nama', $berat_badan, " . 
                    ($tinggi_badan ? $tinggi_badan : "NULL") . ", " .
                    ($usia ? $usia : "NULL") . ", " .
                    ($jenis_kelamin ? "'$jenis_kelamin'" : "NULL") . ", NOW())";
            
            $result = $this->query($sql);
            
            if ($result) {
                $insert_id = $this->db->insert_id;
                
                return [
                    'success' => true,
                    'message' => 'Biodata berhasil ditambahkan',
                    'data' => [
                        'id' => $insert_id,
                        'user_id' => $user_id,
                        'nama' => $nama,
                        'berat_badan' => $berat_badan,
                        'tinggi_badan' => $tinggi_badan,
                        'usia' => $usia,
                        'jenis_kelamin' => $jenis_kelamin
                    ]
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
     * 
     * @param int $id ID biodata yang akan diupdate
     * @param array $data Array berisi data yang akan diupdate
     * @return array Response dengan status success/fail
     */
    public function updateBiodata($id, $data) {
        try {
            // Validasi ID
            if (empty($id)) {
                return [
                    'success' => false,
                    'message' => 'ID biodata tidak ditemukan'
                ];
            }
            
            $id = intval($id);
            
            // Cek apakah biodata ada
            $checkSql = "SELECT id FROM biodata WHERE id = $id";
            $checkResult = $this->query($checkSql);
            
            if ($checkResult->num_rows == 0) {
                return [
                    'success' => false,
                    'message' => 'Biodata dengan ID tersebut tidak ditemukan'
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
            
            if (isset($data['tinggi_badan'])) {
                $tinggi_badan = floatval($data['tinggi_badan']);
                $updateFields[] = "tinggi_badan = " . ($tinggi_badan > 0 ? $tinggi_badan : "NULL");
            }
            
            if (isset($data['usia'])) {
                $usia = intval($data['usia']);
                $updateFields[] = "usia = " . ($usia > 0 ? $usia : "NULL");
            }
            
            if (isset($data['jenis_kelamin'])) {
                $jenis_kelamin = $this->escape($data['jenis_kelamin']);
                $updateFields[] = "jenis_kelamin = " . (!empty($jenis_kelamin) ? "'$jenis_kelamin'" : "NULL");
            }
            
            if (empty($updateFields)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data yang diupdate'
                ];
            }
            
            // Tambahkan updated_at
            $updateFields[] = "updated_at = NOW()";
            
            // Query update
            $sql = "UPDATE biodata SET " . implode(', ', $updateFields) . " WHERE id = $id";
            
            $result = $this->query($sql);
            
            if ($result) {
                // Ambil data terbaru
                $selectSql = "SELECT * FROM biodata WHERE id = $id";
                $selectResult = $this->query($selectSql);
                $updatedData = $selectResult->fetch_assoc();
                
                return [
                    'success' => true,
                    'message' => 'Biodata berhasil diupdate',
                    'data' => $updatedData
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
     * Formula: 
     * - Berat < 30kg: 100 ml/kg
     * - Berat 30-50kg: 1500 ml + 50 ml untuk setiap kg di atas 20kg
     * - Berat > 50kg: 2500 ml + 15 ml untuk setiap kg di atas 50kg
     * Dengan penyesuaian berdasarkan tingkat aktivitas
     * 
     * @param float $beratBadan Berat badan dalam kg
     * @param string $aktivitas Tingkat aktivitas (ringan/sedang/berat)
     * @return array Response dengan hasil perhitungan
     */
    public function hitungKebutuhanCairan($beratBadan, $aktivitas = 'sedang') {
        try {
            // Validasi input
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
            
            // Hitung total kebutuhan
            $totalKebutuhan = round($kebutuhanDasar * $faktorAktivitas);
            
            // Konversi ke gelas (1 gelas = 250ml)
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