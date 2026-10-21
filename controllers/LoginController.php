<?php
session_start();

require_once "../config/Database.php";
require_once "../models/User.php";

/**
 * LoginController
 * Controller berbasis OOP (PHP Native) - dibungkus tanpa mengubah alur logika yang sudah ada.
 */
class LoginController
{
    public function handle(): void
    {
        $db   = new Database();
        $user = new User($db->conn);

        if (isset($_POST['login'])) {

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                header("Location: ../views/login.php?pesan=gagal");
                exit;
            }

            $data = $user->login($username, $password);

            if ($data) {

                $_SESSION['login']  = true;
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['user']   = $data['username'];
                $_SESSION['nama']   = $data['nama'];
                $_SESSION['role']   = $data['role'];

                if ($data['role'] === 'admin') {
                    header("Location: ../views/dashboard.php");
                } else {
                    header("Location: ../views/user_dashboard.php");
                }
                exit;

            } else {
                header("Location: ../views/login.php?pesan=gagal");
                exit;
            }
        }
    }
}

(new LoginController())->handle();

