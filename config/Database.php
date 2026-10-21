<?php
class Database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli(
            "localhost",
            "root",
            "",
            "laundrygo",
            3307
        );

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }
}