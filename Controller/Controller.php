<?php

// Include model
require_once __DIR__ . '/../model/Model.php';

class TrackingController {
    
    private $model;
    
    public function __construct() {
        $this->model = new TrackingModel();
    }
    
    /**
     * Menangani request untuk menghitung kebutuhan cairan
     * 
     * @return void
     */
    public function hitungKebutuhanCairan() {
        // Set header untuk JSON response
        header('Content-Type: application/json');
        
        // Cek method request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan. Gunakan POST.'
            ]);
            return;
        }
        
        // Ambil data dari POST request
        $beratBadan = isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
        
        // Validasi input
        if ($beratBadan === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter berat_badan tidak ditemukan'
            ]);
            return;
        }
        
        // Panggil model untuk hitung tracking
        $hasil = $this->model->hitungTracking($beratBadan);
        
        // Return response
        echo json_encode($hasil);
    }
    
    /**
     * Menampilkan form input berat badan
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
            $beratBadan = isset($_POST['berat_badan']) ? $_POST['berat_badan'] : null;
        } else {
            $beratBadan = isset($input['berat_badan']) ? $input['berat_badan'] : null;
        }
        
        if ($beratBadan === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Parameter berat_badan tidak ditemukan'
            ]);
            return;
        }
        
        $hasil = $this->model->hitungTracking($beratBadan);
        echo json_encode($hasil);
    }
}