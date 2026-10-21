<?php
class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function totalPelanggan() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM pelanggan");
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function totalLayanan() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM layanan");
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function totalTransaksi() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM transaksi");
        $data = $result->fetch_assoc();
        return $data['total'];
    }

    public function totalPendapatan() {
        $result = $this->conn->query("SELECT SUM(total_harga) AS total FROM transaksi");
        $data = $result->fetch_assoc();
        return $data['total'] ?? 0;
    }
}