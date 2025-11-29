
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
            // Ganti 'user_id' menjadi 'id_pengguna' dan hapus default 1
            $userId = $this->getSession('id_pengguna');
            
            // Enforce login: Jika user ID kosong, redirect ke halaman login
            if (empty($userId)) {
                $this->redirect('index.php?c=Auth&m=login');
                return;
            }
            
            // // Inisialisasi target user, target akan 2500 (default) jika belum ada data
            // $this->trackingModel->inisialisasiTargetUser($userId); 

            $today = date('Y-m-d');
            
            // Minta data dengan user ID yang dinamis
            $jenisMinuman = $this->trackingModel->getJenisMinuman();
            $targetHarian = $this->trackingModel->getTargetHarian($userId); // Pass $userId
            $catatanMinum = $this->trackingModel->getCatatanMinumByDate($today, $userId); // Pass $userId
            $statistik = $this->trackingModel->getStatistikHariIni($userId); // Pass $userId
            
            $data = [
                'jenisMinuman' => $jenisMinuman,
                'targetHarian' => $targetHarian,
                'catatanMinum' => $catatanMinum,
                'statistik' => $statistik
            ];
            
            $this->view('tracking', $data);

        // klo error/blm ada datanya, ditampilkan default ini:
        } catch (Exception $e) {
            $this->view('tracking', [
                'jenisMinuman' => [],
                'targetHarian' => 2500,
                'catatanMinum' => [],
                'statistik' => ['total_diminum' => 0, 'jumlah_catatan' => 0],
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
    
    // Method untuk menambah catatan minum
    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Ambil ID pengguna yang sedang login
                $userId = $this->getSession('id_pengguna'); 
                
                if (empty($userId)) {
                     $this->jsonResponse([
                        'success' => false,
                        'message' => 'Anda harus login untuk menambahkan catatan.'
                    ]);
                    return;
                }

                $jenisMinumanId = $_POST['jenis_minuman_id'] ?? '';
                $jumlah = $_POST['jumlah'] ?? '';
                
                if (empty($jenisMinumanId) || empty($jumlah) || !is_numeric($jumlah)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Input tidak valid'
                    ]);
                    return;
                }
                
                // Panggil model dengan user ID yang dinamis
                $result = $this->trackingModel->tambahCatatanMinum($jenisMinumanId, $jumlah, $userId); 
                
                if ($result) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Catatan berhasil ditambahkan'
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
                // Ambil ID pengguna yang sedang login
                $userId = $this->getSession('id_pengguna');
                
                if (empty($userId)) {
                    $this->jsonResponse([
                       'success' => false,
                       'message' => 'Anda harus login untuk menghapus catatan.'
                   ]);
                   return;
               }
                
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'ID tidak valid'
                    ]);
                    return;
                }
                
                // Panggil model dengan ID catatan dan user ID untuk keamanan
                $result = $this->trackingModel->hapusCatatanMinum($id, $userId); 
                
                if ($result) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Catatan berhasil dihapus'
                    ]);
                } else {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Gagal menghapus catatan atau catatan bukan milik Anda'
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
