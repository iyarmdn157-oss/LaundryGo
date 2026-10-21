<?php
session_start();

require_once "../config/Database.php";
require_once "../models/User.php";

class ProfileController
{
    public function handle(): void
    {
        if (!isset($_POST['update_profil'])) {
            header("Location: ../views/user/profile.php");
            exit;
        }

        if (!isset($_SESSION['id_user'])) {
            header("Location: ../views/login.php");
            exit;
        }

        $id_user  = (int)$_SESSION['id_user'];
        $username = trim($_POST['username'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $alamat   = trim($_POST['alamat'] ?? '');
        $no_hp    = trim($_POST['no_hp'] ?? '');

        if ($username === '' || $nama === '' || $alamat === '' || $no_hp === '') {
            header("Location: ../views/user/profile.php?pesan=kosong");
            exit;
        }

        $db = new Database();
        $userModel = new User($db->conn);

        // cek username dipakai user lain
        if ($userModel->usernameExistsForOther($username, $id_user)) {
            header("Location: ../views/user/profile.php?pesan=username");
            exit;
        }

        $ok = $userModel->updateProfile($id_user, $username, $nama, $alamat, $no_hp);

        if ($ok) {
            // update session biar langsung berubah di UI
            $_SESSION['nama'] = $nama;
            $_SESSION['username'] = $username;

            header("Location: ../views/user/profile.php?pesan=berhasil");
            exit;
        }

        header("Location: ../views/user/profile.php?pesan=gagal");
        exit;
    }
}

(new ProfileController())->handle();
