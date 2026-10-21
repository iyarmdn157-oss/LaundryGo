<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/Database.php";
$db = new Database();
$conn = $db->conn;

$id_user = (int)$_SESSION['id_user'];
$active = 'status';

// Prepared statement
$stmt = $conn->prepare("
    SELECT 
        t.berat,
        t.metode,
        t.status,
        t.estimasi_selesai,
        t.total_harga,
        t.tgl_masuk,
        l.nama_layanan,
        h.tipe,
        h.satuan
    FROM transaksi t
    JOIN harga_layanan h ON t.id_harga = h.id_harga
    JOIN layanan l ON h.id_layanan = l.id_layanan
    WHERE t.id_user = ?
      AND t.status != 'selesai'
    ORDER BY t.id_transaksi DESC
");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$query = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Laundry</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/status.css">
</head>
<body class="page-status">

<?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">
    <div class="page-header">
        <div>
            <h1>Status Laundry</h1>
            <p class="subtle">Pantau proses laundry yang masih berjalan.</p>
        </div>
    </div>

    <div class="card table-card">
        <div class="toolbar">
            <div class="meta">Transaksi belum selesai</div>
        </div>

        <div class="table-wrap status-wrap">
            <table class="table status-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Layanan</th>
                        <th>Metode</th>
                        <th>Berat</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Tanggal Masuk</th>
                        <th>Estimasi Selesai</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;

                if ($query && $query->num_rows > 0):
                    while ($row = $query->fetch_assoc()):
                        $statusRaw = (string)($row['status'] ?? '');
                        $status = strtolower($statusRaw);
                        $statusClass = preg_replace('/[^a-z0-9_-]/', '', $status);

                        $tglMasukRaw = $row['tgl_masuk'] ?? null;
                        $tglMasuk = $tglMasukRaw ? date('d M Y', strtotime($tglMasukRaw)) : '-';

                        $estimasiRaw = $row['estimasi_selesai'] ?? null;
                        $estimasi = $estimasiRaw ? date('d M Y', strtotime($estimasiRaw)) : '-';

                        $totalBayar = (float)($row['total_harga'] ?? 0);
                ?>
                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= htmlspecialchars($row['nama_layanan'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($row['tipe'])): ?>
                                (<?= htmlspecialchars($row['tipe'], ENT_QUOTES, 'UTF-8') ?>)
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars(ucfirst($row['metode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>

                        <td>
                            <?= number_format((float)($row['berat'] ?? 0), 2) ?>
                            <?= htmlspecialchars($row['satuan'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>Rp <?= number_format($totalBayar, 0, ',', '.') ?></td>

                        <td>
                            <span class="status <?= $statusClass ?>">
                                <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($tglMasuk, ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= htmlspecialchars($estimasi, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr><td colspan="8" class="empty-state">Belum ada transaksi</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>