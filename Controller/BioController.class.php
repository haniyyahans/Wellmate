<?php
require_once 'Controller.class.php'; 
// Include model
require_once __DIR__ . '/../Model/BioModel.class.php';

class Bio extends Controller{
    
    private $model;
    
    public function __construct() {
        $this->model = new BioModel();
    }

    public function index() {
        require_once __DIR__ . '/../View/berandapage.php';
    }
    
    /**
     * Menangani request untuk menambah biodata
     * 
     * @return void
     */
    public function tambahBiodata() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        // Ambil data dari POST
        $data = [
            'user_id' => isset($_POST['user_id']) ? $_POST['user_id'] : null,
            'nama' => isset($_POST['nama']) ? $_POST['nama'] : null,
            'berat_badan' => isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null,
            'tinggi_badan' => isset($_POST['tinggi_badan']) ? $_POST['tinggi_badan'] : null,
            'usia' => isset($_POST['usia']) ? $_POST['usia'] : null,
            'jenis_kelamin' => isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null
        ];
        
        // Validasi input wajib
        if (!$data['user_id'] || !$data['nama'] || !$data['berat_badan']) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter user_id, nama, dan berat_badan wajib diisi'
            ]);
            return;
        }
        
        // Panggil model
        $hasil = $this->model->tambahBiodata($data);
        echo json_encode($hasil);
    }
    
    /**
     * Menangani request untuk update biodata
     * 
     * @return void
     */
    public function updateBiodata() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        // Ambil data dari POST
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $data = [
            'nama' => isset($_POST['nama']) ? $_POST['nama'] : null,
            'berat_badan' => isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null,
            'tinggi_badan' => isset($_POST['tinggi_badan']) ? $_POST['tinggi_badan'] : null,
            'usia' => isset($_POST['usia']) ? $_POST['usia'] : null,
            'jenis_kelamin' => isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : null
        ];
        
        // Validasi input
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter id wajib diisi'
            ]);
            return;
        }
        
        // Panggil model
        $hasil = $this->model->updateBiodata($id, $data);
        echo json_encode($hasil);
    }
    
    /**
     * Menangani request untuk menghitung kebutuhan cairan
     * 
     * @return void
     */
    public function hitungKebutuhanCairan() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        // Ambil data dari POST request
        $beratBadan = isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
        $aktivitas = isset($_POST['aktivitas']) ? $_POST['aktivitas'] : 'sedang';
        
        // Validasi input
        if ($beratBadan === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter berat_badan tidak ditemukan'
            ]);
            return;
        }
        
        // Panggil model untuk hitung kebutuhan cairan
        $hasil = $this->model->hitungKebutuhanCairan($beratBadan, $aktivitas);
        
        // Return response
        echo json_encode($hasil);
    }
    
    /**
     * Menampilkan form input biodata
     * 
     * @return void
     */
    public function tampilkanForm() {
        require_once __DIR__ . '/../view/berandapage.php';
    }
    
    /**
     * Handle request dari AJAX
     * 
     * @return void
     */
    public function handleAjaxRequest() {
        header('Content-Type: application/json');
        
        // Ambil raw input untuk JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($input === null) {
            // Fallback ke POST
            $action = isset($_POST['action']) ? $_POST['action'] : null;
            $data = $_POST;
        } else {
            $action = isset($input['action']) ? $input['action'] : null;
            $data = $input;
        }
        
        // Route berdasarkan action
        switch($action) {
            case 'tambah':
                $hasil = $this->model->tambahBiodata($data);
                break;
            case 'update':
                $id = isset($data['id']) ? $data['id'] : null;
                $hasil = $this->model->updateBiodata($id, $data);
                break;
            case 'hitung':
                $beratBadan = isset($data['berat_badan']) ? $data['berat_badan'] : null;
                $aktivitas = isset($data['aktivitas']) ? $data['aktivitas'] : 'sedang';
                $hasil = $this->model->hitungKebutuhanCairan($beratBadan, $aktivitas);
                break;
            default:
                $hasil = [
                    'success' => false,
                    'message' => 'Action tidak valid'
                ];
        }
        
        echo json_encode($hasil);
    }
}