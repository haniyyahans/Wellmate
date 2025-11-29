<?php

require_once __DIR__ . '/../Config/Database.class.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Register user baru
     */
    public function register($data) {
        try {
            // Cek username sudah ada
            $stmt = $this->db->prepare("SELECT id_akun FROM akun WHERE username = ?");
            $stmt->execute([$data['username']]);
            
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Username sudah terdaftar'
                ];
            }
            
            // Mulai transaction
            $this->db->beginTransaction();
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert ke tabel akun
            $stmt = $this->db->prepare("
                INSERT INTO akun (username, password, email) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['email']
            ]);
            
            $idAkun = $this->db->lastInsertId();
            
            // Insert ke tabel pengguna
            $stmt = $this->db->prepare("
                INSERT INTO pengguna (id_akun, nama, berat_badan, usia) 
                VALUES (?, ?, NULL, NULL)
            ");
            $stmt->execute([
                $idAkun,
                $data['nama']
            ]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Registrasi berhasil',
                'id_akun' => $idAkun
            ];
            
        } catch(PDOException $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            // Ambil data akun
            $stmt = $this->db->prepare("SELECT * FROM akun WHERE username = ?");
            $stmt->execute([$username]);
            $akun = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$akun) {
                return [
                    'success' => false,
                    'message' => 'Username tidak ditemukan'
                ];
            }
            
            // Verifikasi password
            if (!password_verify($password, $akun['password'])) {
                return [
                    'success' => false,
                    'message' => 'Password salah'
                ];
            }
            
            // Ambil data pengguna
            $stmt = $this->db->prepare("
                SELECT p.*, a.username, a.email 
                FROM pengguna p 
                JOIN akun a ON p.id_akun = a.id_akun 
                WHERE p.id_akun = ?
            ");
            $stmt->execute([$akun['id_akun']]);
            $pengguna = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'message' => 'Login berhasil',
                'user' => $pengguna
            ];
            
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Login gagal: ' . $e->getMessage()
            ];
        }
    }
}