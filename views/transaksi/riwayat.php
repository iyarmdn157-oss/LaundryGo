<?php
session_start();

// cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/Database.php";
$db = new Database();
$conn = $db->conn;

$active = 'riwayat';

// keyword cari
$keyword = trim($_GET['keyword'] ?? '');

// QUERY (AMAN) - transaksi selesai + optional cari nama pelanggan
$sql = "
    SELECT 
        t.*,
        p.nama AS nama_pelanggan,
        l.nama_layanan,
        h.tipe,
        h.satuan
    FROM transaksi t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN harga_layanan h ON t.id_harga = h.id_harga
    JOIN layanan l ON h.id_layanan = l.id_layanan
    WHERE t.status = 'selesai'
";

$params = [];
$types  = "";

if ($keyword !== '') {
    $sql .= " AND p.nama LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types .= "s";
}

$sql .= " ORDER BY t.id_transaksi DESC";

if ($types !== "") {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $query = $stmt->get_result();
} else {
    $query = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-riwayat">

<!-- SIDEBAR -->
<?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main page-riwayat">

    <div class="page-header">
        <div>
            <h1>Riwayat Transaksi Laundry</h1>
            <div class="subtle">Daftar transaksi yang sudah selesai</div>
        </div>

        <form method="GET" class="form-search">
            <div style="flex:1; max-width: 320px;">
                <input type="text"
                       name="keyword"
                       placeholder="Cari nama pelanggan..."
                       value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn-primary">Cari</button>

            <?php if ($keyword !== ''): ?>
                <a href="riwayat.php" class="btn-secondary no-underline">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-wrap">
            <table class="table riwayat-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th width="110">Metode</th>
                        <th width="110">Berat</th>
                        <th width="150">Total</th>
                        <th width="140">Status</th>
                        <th width="140">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                if ($query && $query->num_rows > 0):
                    while ($row = $query->fetch_assoc()):
                ?>
                    <tr>
                        <td class="td-center"><?= $no++ ?></td>

                        <td><?= htmlspecialchars($row['nama_pelanggan'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                        <td>
                            <?= htmlspecialchars($row['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($row['tipe'])): ?>
                                <div class="subtle" style="margin-top:4px;">
                                    (<?= htmlspecialchars($row['tipe'], ENT_QUOTES, 'UTF-8') ?>)
                                </div>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars(ucfirst($row['metode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>

                        <td>
                            <?= number_format((float)($row['berat'] ?? 0), 2) ?>
                            <?= htmlspecialchars($row['satuan'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>Rp <?= number_format((float)($row['total_harga'] ?? 0)) ?></td>

                        <td><span class="status selesai">Selesai</span></td>

                        <td><?= htmlspecialchars($row['estimasi_selesai'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="8" class="empty-state">Belum ada transaksi selesai</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>