<?php

class Saran extends Controller {
    private $saranModel;
    
    public function __construct() {
        $this->saranModel = $this->model('SaranModel');
        $this->startSession();
    }
    
    // Method default untuk menampilkan halaman saran aktivitas
    public function index() {
        try {
            $userId = $this->getSession('user_id') ?? 1;
            
            // Get data yang diperlukan
            $aktivitas = $this->saranModel->getAllAktivitas();
            $targetHarian = $this->saranModel->getTargetHarian($userId);
            $statistik = $this->saranModel->getStatistikHariIni($userId);
            
            $data = [
                'aktivitas' => $aktivitas,
                'targetHarian' => $targetHarian,
                'statistik' => $statistik
            ];
            
            $this->view('saran.php', $data);

        } catch (Exception $e) {
            $this->view('saran.php', [
                'aktivitas' => [],
                'targetHarian' => 2500,
                'statistik' => ['total_diminum' => 0, 'jumlah_catatan' => 0]
            ]);
        }
    }
    
    // Method untuk mendapatkan semua aktivitas via AJAX
    public function getAllAktivitas() {
        try {
            $aktivitas = $this->saranModel->getAllAktivitas();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $aktivitas
            ]);

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas'
            ]);
        }
    }
    
    // Method untuk mendapatkan detail aktivitas by ID via AJAX
    public function getAktivitasDetail() {
        try {
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'ID tidak valid'
                ]);
                return;
            }
            
            $aktivitas = $this->saranModel->getAktivitasById($id);
            
            if ($aktivitas) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => $aktivitas
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Aktivitas tidak ditemukan'
                ]);
            }

        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil detail aktivitas'
            ]);
        }
    }
}