<?php // ada 2 fungsi untuk menampilkan semua berita dan menampilkan detail berita
require_once "Controller.class.php";
class BeritaController extends Controller {
    public function index() // menampilkan semua berita
    {
        $beritaModel = $this->model("Berita");        // memanggil model Berita
        $berita = $beritaModel->getAllBerita();       // mengambil semua berita dari database, pake fungsi di model berita
        $this->view("HalamanBerita.php", [            // kirim ke view di HalamanBerita.php
            "berita" => $berita                     
        ]);
    }

    public function detail() // menampilkan detail berita
    {
        if (!isset($_GET['id'])) {                     // kalau list beritanya dipencet kan muncul id, nah ini u/ cek idnya ada apa engga
            echo "ID berita tidak ditemukan!";
            return;
        }
        $id = $_GET['id'];                            // ini untuk ambil id dari url terus disimpan di $id
        $beritaModel = $this->model("Berita");        // panggil model
        $detail = $beritaModel->getBeritaById($id);   // ambil detail berita, pake fungsi model berita
        if (!$detail) {                               // ini output kalau berita tidak ditemukan
            echo "Sistem gagal menampilkan berita dan edukasi silahkan coba lagi!";
            return;
        }
        $berita = $beritaModel->getAllBerita();       // menampilkan daftar berita
        $this->view("HalamanBerita.php", [            // kirim dua data ke view, daftarnya dan detailnya
            "detail" => $detail,
            "berita" => $berita
        ]);
    }
}