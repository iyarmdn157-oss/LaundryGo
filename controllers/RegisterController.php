<?php
session_start();
require_once "../config/Database.php";

/**
 * RegisterController
 * Controller berbasis OOP (PHP Native) - dibungkus tanpa mengubah alur logika yang sudah ada.
 */
class RegisterController
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../views/register.php");
            exit;
        }

        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $alamat   = trim($_POST['alamat'] ?? '');
        $no_hp    = trim($_POST['no_hp'] ?? '');

        // Validasi sederhana
        if ($nama === '' || $username === '' || $password === '' || $alamat === '' || $no_hp === '') {
            header("Location: ../views/register.php?pesan=gagal");
            exit;
        }

        $db = new Database();
        $conn = $db->conn;

        // 1) cek username sudah ada atau belum
        $cek = $conn->prepare("SELECT id_user FROM user WHERE username = ? LIMIT 1");
        $cek->bind_param("s", $username);
        $cek->execute();
        $res = $cek->get_result();

        if ($res && $res->num_rows > 0) {
            header("Location: ../views/register.php?pesan=username_ada");
            exit;
        }

        // 2) simpan user baru
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = "user";

        // Sesuaikan nama tabel/kolom kalau beda di database kamu
        $ins = $conn->prepare("INSERT INTO user (nama, username, password, alamat, no_hp, role) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->bind_param("ssssss", $nama, $username, $hash, $alamat, $no_hp, $role);

        if ($ins->execute()) {
            // sukses -> ke login
            header("Location: ../views/login.php?pesan=daftar_sukses");
            exit;
        } else {
            // gagal -> balik register
            header("Location: ../views/register.php?pesan=gagal");
            exit;
        }
    }
}

(new RegisterController())->handle();

