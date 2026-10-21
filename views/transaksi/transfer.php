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

// kalau ternyata metode pembayaran bukan Transfer, arahkan ke halaman yang sesuai
if (strtolower(trim($data['metode_pembayaran'])) === 'qris') {
    header("Location: qris.php?id=" . $id_transaksi);
    exit;
}

$total = (int)($data['total_harga'] ?? 0);
$biaya_jemput = (int)($data['biaya_jemput'] ?? 0);

$tanggal = !empty($data['tgl_masuk'])
    ? date('d M Y', strtotime($data['tgl_masuk']))
    : date('d M Y');

// INFO REKENING (kamu boleh ganti sesuai kebutuhan)
$namaPenerima = "LaundryGo";
$bankTujuan   = "BCA";
$noRek        = "1234567890";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Transfer</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-transaksi">

<?php if (file_exists(__DIR__ . '/../layout/sidebar.php')) include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main">
    <div class="page-header">
        <div>
            <h1>Pembayaran Transfer</h1>
            <p class="subtle">Transfer ke rekening berikut, lalu klik “Sudah Transfer” untuk melihat struk.</p>
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

            <!-- KANAN: info rekening -->
            <div class="checkout-form card">
                <div class="form-head">
                    <div>
                        <div class="form-title">Informasi Rekening</div>
                        <div class="form-sub">Gunakan m-banking / ATM untuk transfer.</div>
                    </div>
                </div>

                <div class="total-card">
                    <div class="total-label">Nama Penerima</div>
                    <div class="total-value" style="font-size:18px;"><?= htmlspecialchars($namaPenerima, ENT_QUOTES, 'UTF-8'); ?></div>

                    <div style="height:10px;"></div>

                    <div class="total-label">Bank Tujuan</div>
                    <div class="total-value" style="font-size:18px;"><?= htmlspecialchars($bankTujuan, ENT_QUOTES, 'UTF-8'); ?></div>

                    <div style="height:10px;"></div>

                    <div class="total-label">No Rekening</div>
                    <div class="total-value" style="font-size:18px; letter-spacing:0.5px;">
                        <?= htmlspecialchars($noRek, ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                    <div class="total-note" style="margin-top:10px;">
                        Setelah selesai transfer, klik tombol di bawah ini.
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <a href="../../controllers/PaymentController.php?action=confirm&id=<?= (int)$id_transaksi; ?>"
                        class="btn-simpan" style="text-decoration:none;">
                        Sudah Transfer
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>
