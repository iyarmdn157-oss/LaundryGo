<?php
session_start();

require_once "../../config/Database.php";
require_once "../../models/Pelanggan.php";

$active = 'pelanggan';

$db = new Database();
$pelanggan = new Pelanggan($db->conn);

// Ambil keyword cari
$keyword = trim($_GET['cari'] ?? '');

// PAKAI MODEL (JOIN pindah ke model) - logic tetap sama
if ($keyword !== '') {
    $dataPelanggan = $pelanggan->searchWithUser($keyword);
} else {
    $dataPelanggan = $pelanggan->allWithUser();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - LaundryGo</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-pelanggan">

<!-- SIDEBAR -->
<?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">
    <div class="page-header">
        <div>
            <h1>Data Pelanggan</h1>
            <div class="subtle">Kelola data pelanggan yang terdaftar</div>
        </div>
        <a href="tambah.php" class="btn-primary no-underline">+ Tambah Pelanggan</a>
    </div>

    <form method="get" class="form-search">
        <div style="flex:1; max-width: 360px;">
            <input type="text"
                   name="cari"
                   placeholder="Cari pelanggan..."
                   value="<?= htmlspecialchars($_GET['cari'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <button type="submit" class="btn-primary">Cari</button>

        <?php if ($keyword !== '') : ?>
            <a href="index.php" class="btn-secondary no-underline">Reset</a>
        <?php endif; ?>
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
            <thead>
                <tr>
                    <th width="70">No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php if ($dataPelanggan && $dataPelanggan->num_rows > 0): ?>
                <?php while ($row = $dataPelanggan->fetch_assoc()) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['no_hp'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="edit.php?id=<?= (int)($row['id_pelanggan'] ?? 0) ?>" class="link-edit">Edit</a>
                        <a href="../../controllers/PelangganController.php?aksi=hapus&id=<?= (int)($row['id_pelanggan'] ?? 0) ?>"
                           onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                           class="link-hapus">
                           Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:#64748b;">Data pelanggan tidak ditemukan</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

</body>
</html>
