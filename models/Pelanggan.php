<?php
class Pelanggan
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // LIST default (tanpa join) - tetap disediakan
    public function all()
    {
        return $this->conn->query("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
    }

    // FIND satu data -> return ARRAY (fetch_assoc), biar edit.php gampang
    public function find($id)
    {
        $id = (int) $id;

        $stmt = $this->conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function create($username, $nama, $alamat, $no_hp)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO pelanggan (username, nama, alamat, no_hp)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $username, $nama, $alamat, $no_hp);

        return $stmt->execute();
    }

    public function update($id, $username, $nama, $alamat, $no_hp)
    {
        $id = (int) $id;

        $stmt = $this->conn->prepare("
            UPDATE pelanggan SET
                username = ?,
                nama = ?,
                alamat = ?,
                no_hp = ?
            WHERE id_pelanggan = ?
        ");
        $stmt->bind_param("ssssi", $username, $nama, $alamat, $no_hp, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $id = (int) $id;

        $stmt = $this->conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    // SEARCH default (tanpa join) - tetap disediakan
    public function search($keyword)
    {
        $like = "%" . $keyword . "%";

        $stmt = $this->conn->prepare("
            SELECT * FROM pelanggan
            WHERE 
                nama LIKE ? OR
                username LIKE ? OR
                alamat LIKE ? OR
                no_hp LIKE ?
            ORDER BY id_pelanggan DESC
        ");
        $stmt->bind_param("ssss", $like, $like, $like, $like);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ==========================
       TAMBAHAN (MVC lebih mantap)
       pindahin JOIN dari view
       ========================== */

    // LIST pelanggan + username (JOIN user) -> return mysqli_result untuk tabel
    public function allWithUser()
    {
        return $this->conn->query("
            SELECT 
                p.id_pelanggan,
                COALESCE(NULLIF(p.username,''), u.username, '-') AS username,
                p.nama,
                p.alamat,
                p.no_hp
            FROM pelanggan p
            LEFT JOIN user u ON u.id_user = p.id_user
            ORDER BY p.id_pelanggan ASC
        ");
    }

    // SEARCH pelanggan + username (JOIN user) -> return mysqli_result
    public function searchWithUser($keyword)
    {
        $like = "%" . $keyword . "%";

        $stmt = $this->conn->prepare("
            SELECT 
                p.id_pelanggan,
                COALESCE(NULLIF(p.username,''), u.username, '-') AS username,
                p.nama,
                p.alamat,
                p.no_hp
            FROM pelanggan p
            LEFT JOIN user u ON u.id_user = p.id_user
            WHERE 
                COALESCE(NULLIF(p.username,''), u.username, '') LIKE ?
                OR p.nama LIKE ?
                OR p.alamat LIKE ?
                OR p.no_hp LIKE ?
            ORDER BY p.id_pelanggan ASC
        ");
        $stmt->bind_param("ssss", $like, $like, $like, $like);
        $stmt->execute();

        return $stmt->get_result();
    }
}