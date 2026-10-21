<?php
session_start();
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/Layanan.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$layanan = new Layanan($db->conn);

/* find() mengembalikan mysqli_result, jadi harus fetch_assoc */
$result = $layanan->find($id);
$data = $result ? $result->fetch_assoc() : null;

if (!$data) {
    header("Location: index.php");
    exit;
}

$active = 'layanan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Layanan</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-layanan">

    <?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

    <div class="main">
        <div class="page-header">
            <div>
                <h1>Edit Layanan</h1>
                <div class="subtle">Perbarui nama layanan yang tersedia</div>
            </div>
            <a href="index.php" class="btn-secondary no-underline">Kembali</a>
        </div>

        <div class="card card-form">
            <h2 style="margin:0 0 12px;">✏️ Edit Layanan</h2>

            <form action="../../controllers/LayananController.php" method="post" class="form-transaksi">
                <input type="hidden" name="aksi" value="update">
                <input type="hidden" name="id" value="<?= (int) $data['id_layanan'] ?>">

                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input
                        type="text"
                        name="nama_layanan"
                        value="<?= htmlspecialchars($data['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-actions" style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="btn-primary">Update</button>
                    <a href="index.php" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
