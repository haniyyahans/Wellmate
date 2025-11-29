<?php

require_once __DIR__ . '/../Model/UserModel.class.php';

class AuthController {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        
        // Start session jika belum
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Tampilkan halaman sign in
     */
    public function showSignIn() {
        require_once __DIR__ . '/../View/signinpage.php';
    }
    
    /**
     * Tampilkan halaman sign up
     */
    public function showSignUp() {
        require_once __DIR__ . '/../View/signuppage.php';
    }
    
    /**
     * Proses sign in
     */
    public function processSignIn() {
        header('Content-Type: application/json');
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']) ? true : false;
        
        if (empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Username dan password harus diisi'
            ]);
            return;
        }
        
        $result = $this->userModel->login($username, $password);
        
        if ($result['success']) {
            $_SESSION['id_pengguna'] = $result['user']['id_pengguna'];
            $_SESSION['id_akun'] = $result['user']['id_akun'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['nama'] = $result['user']['nama'];
            
            if ($remember) {
                setcookie('id_pengguna', $result['user']['id_pengguna'], time() + (86400 * 30), '/');
            }
        }
        
        echo json_encode($result);
    }
    
    /**
     * Proses sign up
     */
   public function processSignUp() {
    header('Content-Type: application/json');
    
    $data = [
        'nama' => isset($_POST['nama']) ? trim($_POST['nama']) : '',
        'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : ''
    ];
    
    if (empty($data['nama']) || empty($data['username']) || empty($data['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Nama, username, dan password harus diisi'
        ]);
        return;
    }
    
    if (empty($data['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Email harus diisi'
        ]);
        return;
    }
    
    $result = $this->userModel->register($data);
    
    // Tambahkan redirect URL jika berhasil
    if ($result['success']) {
        $result['redirect_url'] = 'index.php?c=Auth&m=showSignIn';
    }
    
    echo json_encode($result);
    // ✅ JANGAN pakai require_once atau header redirect di sini!
}
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        setcookie('id_pengguna', '', time() - 3600, '/');
        header('Location: index.php?c=Auth&m=showSignIn');
        exit;
    }
    
    /**
     * Cek apakah user sudah login
     */
    public function isLoggedIn() {
        return isset($_SESSION['id_pengguna']);
    }
}