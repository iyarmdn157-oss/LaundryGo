<?php
session_start();

require_once "../config/Database.php";
require_once "../models/User.php";

/**
 * PasswordController
 * Controller berbasis OOP (PHP Native) - dibungkus tanpa mengubah alur logika yang sudah ada.
 */
class PasswordController
{
    public function handle(): void
    {
        if (!isset($_SESSION['id_user'])) {
            header("Location: ../views/login.php");
            exit;
        }

        $db = new Database();
        $userModel = new User($db->conn);

        if (isset($_POST['ganti_password'])) {

            $id_user = (int)$_SESSION['id_user'];

            $old = trim($_POST['password_lama'] ?? '');
            $new = trim($_POST['password_baru'] ?? '');
            $cfm = trim($_POST['konfirmasi_password'] ?? '');

            // validasi basic
            if ($old === '' || $new === '' || $cfm === '') {
                header("Location: ../views/ganti_password.php?pesan=kosong");
                exit;
            }

            if (strlen($new) < 6) {
                header("Location: ../views/ganti_password.php?pesan=kurang");
                exit;
            }

            if ($new !== $cfm) {
                header("Location: ../views/ganti_password.php?pesan=beda");
                exit;
            }

            $user = $userModel->findById($id_user);
            if (!$user) {
                header("Location: ../views/ganti_password.php?pesan=gagal");
                exit;
            }

            $stored = (string)($user['password'] ?? '');

            // cek password lama: hash dulu, kalau gagal coba plaintext
            $okOld = ($stored !== '' && password_verify($old, $stored));
            if (!$okOld && $stored !== '' && hash_equals($stored, $old)) {
                $okOld = true;
            }

            if (!$okOld) {
                header("Location: ../views/ganti_password.php?pesan=lama_salah");
                exit;
            }

            // simpan password baru (hash)
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $ok = $userModel->updatePassword($id_user, $hashed);

            if ($ok) {
                header("Location: ../views/ganti_password.php?pesan=berhasil");
                exit;
            }

            header("Location: ../views/ganti_password.php?pesan=gagal");
            exit;
        }

        header("Location: ../views/ganti_password.php");
        exit;
    }
}

(new PasswordController())->handle();

