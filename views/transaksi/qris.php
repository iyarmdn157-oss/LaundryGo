<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/Database.php";

$db = new Database();
$conn = $db->conn;

$active = 'transaksi';

if (!isset($_GET['id'])) {
    header("Location: tambah.php");
    exit;
}

$id_transaksi = (int)$_GET['id'];
$id_user = (int)$_SESSION['id_user'];

// ambil transaksi berdasarkan id + milik user
$stmt = $conn->prepare("
    SELECT 
        t.id_transaksi, t.metode_pembayaran, t.total_harga, t.tgl_masuk,
        l.nama_layanan,
        h.harga, h.satuan,
        t.berat, t.biaya_jemput
    FROM transaksi t
    JOIN harga_layanan h ON t.id_harga = h.id_harga
    JOIN layanan l ON h.id_layanan = l.id_layanan
    WHERE t.id_transaksi = ? AND t.id_user = ?
    LIMIT 1
");
$stmt->bind_param("ii", $id_transaksi, $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Transaksi tidak ditemukan.");
}

// kalau ternyata metode pembayaran bukan QRIS, arahkan ke halaman yang sesuai
if (strtolower(trim($data['metode_pembayaran'])) === 'transfer') {
    header("Location: transfer.php?id=" . $id_transaksi);
    exit;
}

$total = (int)($data['total_harga'] ?? 0);
$subtotal = (int)($data['harga'] * $data['berat']);
$biaya_jemput = (int)($data['biaya_jemput'] ?? 0);

$tanggal = !empty($data['tgl_masuk'])
    ? date('d M Y', strtotime($data['tgl_masuk']))
    : date('d M Y');

// gambar QRIS pakai yang sudah ada di project kamu
$qrisImg = "../../assets/img/Transaksi/image.png";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran QRIS</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-transaksi">

<?php if (file_exists(__DIR__ . '/../layout/sidebar.php')) include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main">
    <div class="page-header">
        <div>
            <h1>Pembayaran QRIS</h1>
            <p class="subtle">Scan QR berikut, lalu klik “Sudah Bayar” untuk melihat struk.</p>
        </div>
    </div>

    <div class="card content-card">
        <div class="checkout-layout">

            <!-- KIRI: ringkasan transaksi -->
            <div class="checkout-summary card">
                <div class="sum-head">
                    <div>
                        <div class="sum-title">Ringkasan Transaksi</div>
                        <div class="sum-sub">Pastikan datanya sudah benar.</div>
                    </div>
                    <a class="sum-change" href="tambah.php">Buat Baru</a>
                </div>

                <div class="sum-item" style="margin-top:12px;">
                    <div class="sum-info" style="width:100%;">
                        <div class="sum-name"><?= htmlspecialchars($data['nama_layanan'], ENT_QUOTES, 'UTF-8'); ?></div>

                        <div class="sum-price" style="margin-top:8px;">
                            Rp <?= number_format((float)$data['harga']); ?>
                            <span>/ <?= htmlspecialchars($data['satuan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <div style="margin-top:10px;" class="subtle">
                            <div><b>ID Transaksi:</b> #<?= (int)$data['id_transaksi']; ?></div>
                            <div><b>Tanggal:</b> <?= $tanggal; ?></div>
                            <div><b>Berat:</b> <?= number_format((float)$data['berat'], 1); ?> Kg</div>
                            <?php if ($biaya_jemput > 0): ?>
                                <div><b>Biaya Jemput:</b> Rp <?= number_format($biaya_jemput); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="sum-totalbox" style="margin-top:12px;">
                    <div class="note-row">
                        <span>Total Bayar</span>
                        <b>Rp <?= number_format($total); ?></b>
                    </div>
                    <?php if ($biaya_jemput > 0): ?>
                        <div class="note-hint">Termasuk biaya jemput Rp <?= number_format($biaya_jemput); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- KANAN: QRIS -->
            <div class="checkout-form card">
                <div class="form-head">
                    <div>
                        <div class="form-title">Scan QR</div>
                        <div class="form-sub">Gunakan aplikasi e-wallet / m-banking.</div>
                    </div>
                </div>

                <div class="total-card" style="text-align:center;">
                    <img src="<?= $qrisImg; ?>" alt="QRIS" style="max-width:260px; width:100%; height:auto;">
                    <div class="subtle" style="margin-top:10px;">
                        Setelah berhasil scan, klik tombol di bawah ini.
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <a href="../../controllers/PaymentController.php?action=confirm&id=<?= (int)$id_transaksi; ?>"
                        class="btn-simpan" style="text-decoration:none;">
                        Sudah Bayar
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>
