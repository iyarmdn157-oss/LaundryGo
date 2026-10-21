<?php

class Transaksi
{
    private $conn;
    private $table = "transaksi";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // READ ALL TRANSAKSI
    // =========================
    public function all()
    {
        $sql = "
            SELECT 
                t.id_transaksi,
                t.metode,
                t.berat,
                t.total_harga,
                t.status,
                t.tgl_masuk,
                t.estimasi_selesai,
                p.nama AS nama_pelanggan,
                p.alamat,
                l.nama_layanan,
                h.tipe
            FROM transaksi t
            LEFT JOIN pelanggan p 
                ON t.id_pelanggan = p.id_pelanggan
            LEFT JOIN harga_layanan h
                ON t.id_harga = h.id_harga
            LEFT JOIN layanan l
                ON h.id_layanan = l.id_layanan
            ORDER BY t.id_transaksi DESC
        ";

        return $this->conn->query($sql);
    }

    // =========================
    // SEARCH TRANSAKSI
    // =========================
    public function search($keyword)
    {
        $keyword = "%$keyword%";

        $sql = "
            SELECT 
                t.id_transaksi,
                t.metode,
                t.berat,
                t.total_harga,
                t.status,
                t.tgl_masuk,
                t.estimasi_selesai,
                p.nama AS nama_pelanggan,
                p.alamat,
                l.nama_layanan,
                h.tipe
            FROM transaksi t
            LEFT JOIN pelanggan p 
                ON t.id_pelanggan = p.id_pelanggan
            LEFT JOIN harga_layanan h
                ON t.id_harga = h.id_harga
            LEFT JOIN layanan l
                ON h.id_layanan = l.id_layanan
            WHERE p.nama LIKE ?
            ORDER BY t.id_transaksi DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();
        return $stmt->get_result();
    }

    // =========================
    // UPDATE STATUS TRANSAKSI
    // =========================
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE transaksi SET status=? WHERE id_transaksi=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}
