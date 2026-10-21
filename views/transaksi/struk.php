<?php
session_start();
require_once "../../config/Database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->conn;

$id_user = (int)$_SESSION['id_user'];

// ambil transaksi TERAKHIR milik user (logic TETAP)
$stmt = $conn->prepare("
    SELECT 
        t.*,
        l.nama_layanan,
        h.harga,
        u.nama,
        u.no_hp
    FROM transaksi t
    JOIN harga_layanan h ON t.id_harga = h.id_harga
    JOIN layanan l ON h.id_layanan = l.id_layanan
    JOIN user u ON t.id_user = u.id_user
    WHERE t.id_user = ?
    ORDER BY t.id_transaksi DESC
    LIMIT 1
");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Struk tidak ditemukan");
}

function status_class($s) {
    $s = strtolower(trim($s));
    if (in_array($s, ['selesai'])) return 'selesai';
    if (in_array($s, ['dicuci'])) return 'dicuci';
    if (in_array($s, ['disetrika'])) return 'disetrika';
    if (in_array($s, ['diantar','dikirim'])) return 'diantar';
    if (in_array($s, ['diproses','proses','menunggu','pending','antri'])) return 'proses';
    if (in_array($s, ['batal','dibatalkan','cancel'])) return 'batal';
    return 'proses';
}

$subtotal = (int)($data['harga'] * $data['berat']);
$biaya_jemput = (int)($data['biaya_jemput'] ?? 0);
$total = (int)($data['total_harga'] ?? ($subtotal + $biaya_jemput));

$statusText = ucfirst($data['status']);
$statusCls  = status_class($data['status']);

$tanggal = !empty($data['tgl_masuk'])
    ? date('d M Y', strtotime($data['tgl_masuk']))
    : date('d M Y');

$estimasi = !empty($data['estimasi_selesai'])
    ? date('d M Y', strtotime($data['estimasi_selesai']))
    : '-';

$logoPath = "../../assets/img/login/logo.png";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Struk Transaksi - LaundryGo</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <link rel="stylesheet" href="../../assets/css/struk.css">
</head>
<body>

<div class="struk-wrap">
  <div class="struk-card">

    <div class="struk-head">
      <div class="brand">
        <img class="brand-logo" src="<?= $logoPath ?>" alt="LaundryGo">
        <div class="brand-text">
          <div class="brand-name">LaundryGo</div>
          <div class="brand-sub">Laundry Modern & Cepat</div>
        </div>
      </div>

      <div class="struk-meta">
        <div class="meta-item">
          <span class="meta-label">Transaksi</span>
          <span class="meta-value">#<?= (int)$data['id_transaksi']; ?></span>
        </div>
        <div class="meta-item">
          <span class="meta-label">Tanggal</span>
          <span class="meta-value"><?= $tanggal; ?></span>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="grid-2">
      <div class="info-box">
        <div class="box-title">Pelanggan</div>
        <div class="row">
          <span class="k">Nama</span>
          <span class="v"><?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="row">
          <span class="k">No HP</span>
          <span class="v"><?= htmlspecialchars($data['no_hp'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </div>

      <div class="info-box">
        <div class="box-title">Status</div>
        <div class="row">
          <span class="k">Proses</span>
          <span class="v">
            <span class="status-badge <?= $statusCls; ?>">
                <?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8'); ?>
            </span>
          </span>
        </div>
        <div class="row">
          <span class="k">Estimasi</span>
          <span class="v"><?= htmlspecialchars($estimasi, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="info-box detail-box">
      <div class="box-title">Detail Layanan</div>
      <div class="row">
        <span class="k">Layanan</span>
        <span class="v"><?= htmlspecialchars($data['nama_layanan'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <div class="row">
        <span class="k">Harga / Kg</span>
        <span class="v">Rp <?= number_format($data['harga']); ?></span>
      </div>
      <div class="row">
        <span class="k">Berat</span>
        <span class="v"><?= number_format($data['berat'], 2); ?> Kg</span>
      </div>
    </div>

    <div class="divider"></div>

    <div class="pay-box">
      <div class="row">
        <span class="k">Subtotal</span>
        <span class="v">Rp <?= number_format($subtotal); ?></span>
      </div>

      <?php if ($biaya_jemput > 0): ?>
      <div class="row">
        <span class="k">Biaya Jemput</span>
        <span class="v">Rp <?= number_format($biaya_jemput); ?></span>
      </div>
      <?php endif; ?>

      <div class="divider mini"></div>

      <div class="row total">
        <span class="k">Total Bayar</span>
        <span class="v">Rp <?= number_format($total); ?></span>
      </div>
    </div>

    <div class="struk-actions">
      <a href="../user_dashboard.php" class="btn-primary">Kembali ke Dashboard</a>
      <button onclick="window.print()" class="btn-soft">Cetak</button>
    </div>

    <div class="struk-foot">
      Terima kasih telah menggunakan <b>LaundryGo</b> 💙
    </div>

  </div>
</div>

</body>
</html>