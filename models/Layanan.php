<?php

class Layanan {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function all() {
        $sql = "SELECT * FROM layanan ORDER BY id_layanan DESC";
        return $this->conn->query($sql);
    }

    public function find($id) {
        $sql = "SELECT * FROM layanan WHERE id_layanan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function create($nama_layanan) {
        $sql = "INSERT INTO layanan (nama_layanan) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $nama_layanan);
        return $stmt->execute();
    }

    public function update($id, $nama_layanan) {
        $sql = "UPDATE layanan SET nama_layanan = ? WHERE id_layanan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nama_layanan, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM layanan WHERE id_layanan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function search($keyword) {
        $sql = "SELECT * FROM layanan WHERE nama_layanan LIKE ? ORDER BY id_layanan DESC";
        $stmt = $this->conn->prepare($sql);
        $like = "%" . $keyword . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        return $stmt->get_result();
    }
}