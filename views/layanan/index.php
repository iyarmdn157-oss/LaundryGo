<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/Layanan.php";

$db = new Database();
$layanan = new Layanan($db->conn);

/* LOGIC CARI (TIDAK DIUBAH) */
$keyword = $_GET['keyword'] ?? '';
if ($keyword != '') {
    $list = $layanan->search($keyword);
} else {
    $list = $layanan->all();
}

$active = 'layanan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Layanan</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-layanan">

    <?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

    <div class="main">

        <!-- HEADER ATAS -->
        <div class="page-header">
            <h1>Data Layanan</h1>

            <a href="tambah.php" class="btn-primary no-underline">
                + Tambah Layanan
            </a>
        </div>

        <!-- SEARCH (DI BAWAH JUDUL, KIRI) -->
        <form method="GET" class="form-search">
            <input type="text"
                   name="keyword"
                   placeholder="Cari layanan..."
                   value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn-primary">Cari</button>
        </form>

        <!-- TABEL -->
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Layanan</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($list && $list->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $list->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)($row['id_layanan'] ?? 0) ?>" class="link-edit">Edit</a>
                            <a href="../../controllers/LayananController.php?aksi=hapus&id=<?= (int)($row['id_layanan'] ?? 0) ?>"
                               onclick="return confirm('Hapus layanan ini?')"
                               class="link-hapus">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">Data layanan tidak ditemukan</td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>

</body>
</html>