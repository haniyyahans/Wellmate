<?php

class Tracking extends Controller {
    private $trackingModel;
    
    public function __construct() {
        $this->trackingModel = $this->model('TrackingModel');
        $this->startSession(); // spy bisa baca user id
    }
    
    // Method default untuk menampilkan halaman tracking
    public function index() {
        try {
            $userId = $this->getSession('user_id') ?? 1;
            $today = date('Y-m-d');
            
            // minta data yg diperlukan ke model
            $jenisMinuman = $this->trackingModel->getJenisMinuman();
            $targetHarian = $this->trackingModel->getTargetHarian($userId);
            $catatanMinum = $this->trackingModel->getCatatanMinumByDate($today, $userId);
            $statistik = $this->trackingModel->getStatistikHariIni($userId);
            
            $data = [
                'jenisMinuman' => $jenisMinuman,
                'targetHarian' => $targetHarian,
                'catatanMinum' => $catatanMinum,
                'statistik' => $statistik
            ];
            
            $this->view('tracking.php', $data);

        // klo error/blm ada datanya, ditampilkan default ini:
        } catch (Exception $e) {
            $this->view('tracking.php', [
                'jenisMinuman' => [],
                'targetHarian' => 2500,
                'catatanMinum' => [],
                'statistik' => ['total_diminum' => 0, 'jumlah_catatan' => 0]
            ]);
        }
    }
    
    // Method untuk mendapatkan data via AJAX
    public function getData() {
        try {
            $userId = $this->getSession('user_id') ?? 1;
            $today = date('Y-m-d');
            
            $jenisMinuman = $this->trackingModel->getJenisMinuman();
            $targetHarian = $this->trackingModel->getTargetHarian($userId);
            $catatanMinum = $this->trackingModel->getCatatanMinumByDate($today, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'jenisMinuman' => $jenisMinuman,
                    'targetHarian' => $targetHarian,
                    'catatanMinum' => $catatanMinum
                ]
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data'
            ]);
        }
    }
    
    // Method untuk tambah catatan minum
    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $userId = $this->getSession('user_id') ?? 1;
                $jenis = $_POST['jenis'] ?? '';
                $jumlah = $_POST['jumlah'] ?? 0;
                $waktu = $_POST['waktu'] ?? '';
                $tanggal = date('Y-m-d');
                
                if (empty($jenis) || empty($jumlah) || empty($waktu)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Data tidak lengkap'
                    ]);
                    return;
                }
                
                $result = $this->trackingModel->tambahCatatanMinum($jenis, $jumlah, $waktu, $tanggal, $userId);
                
                if ($result) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Catatan berhasil ditambahkan',
                        'id' => $result
                    ]);
                } else {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Gagal menambahkan catatan'
                    ]);
                }

            } catch (Exception $e) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }
    }
    
    // Method untuk update catatan minum
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? '';
                $jenis = $_POST['jenis'] ?? '';
                $jumlah = $_POST['jumlah'] ?? 0;
                $waktu = $_POST['waktu'] ?? '';
                
                if (empty($id) || empty($jenis) || empty($jumlah) || empty($waktu)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Data tidak lengkap'
                    ]);
                    return;
                }
                
                $result = $this->trackingModel->updateCatatanMinum($id, $jenis, $jumlah, $waktu);
                
                if ($result) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Catatan berhasil diperbarui'
                    ]);
                } else {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Gagal memperbarui catatan'
                    ]);
                }

            } catch (Exception $e) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }
    }
    
    // Method untuk hapus catatan minum
    public function hapus() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'ID tidak valid'
                    ]);
                    return;
                }
                
                $result = $this->trackingModel->hapusCatatanMinum($id);
                
                if ($result) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Catatan berhasil dihapus'
                    ]);
                } else {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Gagal menghapus catatan'
                    ]);
                }

            } catch (Exception $e) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
        }
    }
}