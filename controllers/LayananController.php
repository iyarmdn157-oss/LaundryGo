<?php
session_start();

require_once "../config/Database.php";
require_once "../models/Layanan.php";

/**
 * LayananController
 * Controller berbasis OOP (PHP Native) - alur logika tetap sama.
 */
class LayananController
{
    public function handle(): void
    {
        $db = new Database();
        $layanan = new Layanan($db->conn);

        $aksi = $_REQUEST['aksi'] ?? '';
        $aksi = is_string($aksi) ? trim($aksi) : '';

        if ($aksi === 'tambah') {
            $nama = trim($_POST['nama_layanan'] ?? '');
            if ($nama !== '') {
                $layanan->create($nama);
            }

        } elseif ($aksi === 'update') {
            $id   = (int) ($_POST['id'] ?? 0);
            $nama = trim($_POST['nama_layanan'] ?? '');

            if ($id > 0 && $nama !== '') {
                $layanan->update($id, $nama);
            }

        } elseif ($aksi === 'hapus') {
            $id = (int) ($_GET['id'] ?? 0);
            if ($id > 0) {
                $layanan->delete($id);
            }
        }

        header("Location: ../views/layanan/index.php");
        exit;
    }
}

(new LayananController())->handle();
