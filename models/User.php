<?php

class User {

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function login($username, $password)
    {
        $query = "SELECT * FROM `user` WHERE username = ?";
        $stmt  = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // hash (recommended)
            if (!empty($user['password']) && password_verify($password, $user['password'])) {
                return $user;
            }

            // fallback plaintext (sementara)
            if (!empty($user['password']) && hash_equals((string)$user['password'], (string)$password)) {
                return $user;
            }
        }

        return false;
    }

    // ✅ ambil user berdasarkan id_user
    public function findById($id_user)
    {
        $sql = "SELECT * FROM `user` WHERE id_user = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ✅ update password (selalu hash)
    public function updatePassword($id_user, $hashed_password)
    {
        $sql = "UPDATE `user` SET password = ? WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $id_user);
        return $stmt->execute();
    }

    // ✅ cek username dipakai user lain
    public function usernameExistsForOther($username, $id_user)
    {
        $sql  = "SELECT id_user FROM `user` WHERE username = ? AND id_user != ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $username, $id_user);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }

    // ✅ update profil user
    public function updateProfile($id_user, $username, $nama, $alamat, $no_hp)
    {
        $sql  = "UPDATE `user`
                 SET username = ?, nama = ?, alamat = ?, no_hp = ?
                 WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $nama, $alamat, $no_hp, $id_user);
        return $stmt->execute();
    }
}