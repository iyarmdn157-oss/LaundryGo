<?php
session_start();
require_once "../config/Database.php";
require_once "../models/Pelanggan.php";

/**
 * PelangganController
 * Controller berbasis OOP (PHP Native) - alur logika tetap sama.
 */
class PelangganController
{
    public function handle(): void
    {
        $db = new Database();
        $pelanggan = new Pelanggan($db->conn);

        $aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';
        $aksi = is_string($aksi) ? trim($aksi) : '';

        if ($aksi === 'tambah') {

            $username = trim($_POST['username'] ?? '');
            $nama     = trim($_POST['nama'] ?? '');
            $alamat   = trim($_POST['alamat'] ?? '');
            $no_hp    = trim($_POST['no_hp'] ?? '');

            // Logic tetap: kalau mau tetap selalu create, biarkan ini.
            // Ini cuma cegah input undefined.
            $pelanggan->create($username, $nama, $alamat, $no_hp);

            header("Location: ../views/pelanggan/index.php");
            exit;

        } elseif ($aksi === 'update') {

            $id       = (int)($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $nama     = trim($_POST['nama'] ?? '');
            $alamat   = trim($_POST['alamat'] ?? '');
            $no_hp    = trim($_POST['no_hp'] ?? '');

            $pelanggan->update($id, $username, $nama, $alamat, $no_hp);

            header("Location: ../views/pelanggan/index.php");
            exit;

        } elseif ($aksi === 'hapus') {

            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $pelanggan->delete($id);
            }

            header("Location: ../views/pelanggan/index.php");
            exit;
        }

        // kalau akses controller tanpa aksi
        header("Location: ../views/pelanggan/index.php");
        exit;
    }
}

(new PelangganController())->handle();
