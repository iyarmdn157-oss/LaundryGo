<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/Database.php";

/**
 * DashboardController
 * Controller berbasis OOP (PHP Native) - dibungkus tanpa mengubah alur logika yang sudah ada.
 */
class DashboardController
{
    public function handle(): void
    {
        $db = new Database();
        $conn = $db->conn;

        $data = [
            'pelanggan'  => $conn->query("SELECT COUNT(*) as total FROM pelanggan")->fetch_assoc()['total'],
            'layanan'    => $conn->query("SELECT COUNT(*) as total FROM layanan")->fetch_assoc()['total'],
            'transaksi'  => $conn->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'],
            'pendapatan' => $conn->query("SELECT SUM(total_harga) as total FROM transaksi")->fetch_assoc()['total'] ?? 0
        ];
    }
}

(new DashboardController())->handle();

