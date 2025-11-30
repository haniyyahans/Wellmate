<?php
require_once 'Controller.class.php'; 
require_once __DIR__ . '/../model/BerandaModel.class.php';

class BerandaController extends Controller { 
    private $model;
    
    public function __construct() {
        $this->model = new BerandaModel();
    }

    public function index() {
        // Ambil id_pengguna dari session (asumsi sudah login)
        session_start();
        $id_pengguna = $_SESSION['id_pengguna'] ?? 1; // Default ke 1 untuk testing
        
        // Ambil data pengguna dari database
        $dataPengguna = $this->model->getBiodataByIdPengguna($id_pengguna);
        
        // Kirim data ke view
        require_once __DIR__ . '/../view/HalamanBeranda.php';
    }

    public function tambahBiodata() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        session_start();
        $id_pengguna = $_SESSION['id_pengguna'] ?? 1;
        
        $data = [
            'id_pengguna' => $id_pengguna,
            'nama' => isset($_POST['nama']) ? $_POST['nama'] : null,
            'berat_badan' => isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null,
            'usia' => isset($_POST['usia']) ? $_POST['usia'] : null
        ];
        
        if (!$data['nama'] || !$data['berat_badan']) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter nama dan berat_badan wajib diisi'
            ]);
            return;
        }
        
        $hasil = $this->model->tambahBiodata($data);
        echo json_encode($hasil);
    }

    public function updateBiodata() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        session_start();
        $id_pengguna = $_SESSION['id_pengguna'] ?? 1;
        
        $data = [
            'nama' => isset($_POST['nama']) ? $_POST['nama'] : null,
            'berat_badan' => isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null,
            'usia' => isset($_POST['usia']) ? $_POST['usia'] : null
        ];
        
        $hasil = $this->model->updateBiodata($id_pengguna, $data);
        echo json_encode($hasil);
    }

    public function hitungKebutuhanCairan() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        $beratBadan = isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
        $aktivitas = isset($_POST['aktivitas']) ? $_POST['aktivitas'] : 'sedang';
        
        if ($beratBadan === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter berat_badan tidak ditemukan'
            ]);
            return;
        }
        
        $hasil = $this->model->hitungKebutuhanCairan($beratBadan, $aktivitas);
        echo json_encode($hasil);
    }
}
