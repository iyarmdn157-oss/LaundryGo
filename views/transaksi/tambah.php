<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

require_once "../../config/Database.php";

$db = new Database();
$conn = $db->conn;

// sidebar active menu (dipakai oleh sidebar.php)
$active = 'transaksi';

// ===============================
// AMBIL DATA LAYANAN
// ===============================
$layanan = $conn->query("
    SELECT 
        h.id_harga,
        l.nama_layanan,
        l.foto,
        h.tipe,
        h.harga,
        h.satuan
    FROM harga_layanan h
    JOIN layanan l ON h.id_layanan = l.id_layanan
");

// ===============================
// DATA DARI REQUEST
// ===============================
$id_harga_raw = $_GET['id_harga'] ?? $_POST['id_harga'] ?? '';
$id_harga = ($id_harga_raw !== '') ? (int)$id_harga_raw : 0;

$berat    = $_POST['berat'] ?? '';
$metode   = $_POST['metode'] ?? '';
$bayar    = $_POST['metode_pembayaran'] ?? '';

$total = 0;
$harga = 0;
$biaya_jemput = 5000;

// ===============================
// AMBIL HARGA SATUAN
// ===============================
if ($id_harga > 0) {
    $q = $conn->prepare("SELECT harga FROM harga_layanan WHERE id_harga = ?");
    $q->bind_param("i", $id_harga);
    $q->execute();
    $harga = (float)($q->get_result()->fetch_assoc()['harga'] ?? 0);
}

// ===============================
// HITUNG TOTAL
// ===============================
$berat_num = is_numeric($berat) ? (float)$berat : 0;
if ($berat_num > 0 && $harga > 0) {
    $total = $berat_num * $harga;
    if ($metode === 'jemput') {
        $total += $biaya_jemput;
    }
}

// ===============================
// AMBIL DETAIL LAYANAN TERPILIH (untuk preview UI step 2)
// ===============================
$detail = null;
if ($id_harga > 0) {
    $stmt = $conn->prepare("
      SELECT h.id_harga, l.nama_layanan, l.foto, h.tipe, h.harga, h.satuan
      FROM harga_layanan h
      JOIN layanan l ON h.id_layanan = l.id_layanan
      WHERE h.id_harga = ?
      LIMIT 1
    ");
    $stmt->bind_param("i", $id_harga);
    $stmt->execute();
    $detail = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Transaksi Laundry</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-transaksi">

<?php if (file_exists(__DIR__ . '/../layout/sidebar.php')) include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main">

    <div class="page-header">
        <div>
            <h1>Buat Transaksi Laundry</h1>
            <p class="subtle">Pilih paket layanan, tentukan metode, lalu simpan transaksi.</p>
        </div>
    </div>

    <div class="card content-card">

        <!-- ===============================
             STEP 1 : PILIH LAYANAN
        =============================== -->
        <?php if ($id_harga <= 0): ?>
        <form method="get">
            <h3>Pilih Layanan</h3>
            <p class="subtle">Klik salah satu kartu layanan, lalu lanjut ke detail transaksi.</p>

            <div class="service-grid">
                <?php if ($layanan && $layanan->num_rows > 0): ?>
                    <?php while ($row = $layanan->fetch_assoc()): ?>
                        <?php
                            $tipeRaw = (string)($row['tipe'] ?? '');
                            $tipeClass = preg_replace('/[^a-z0-9_-]/', '', strtolower($tipeRaw));
                            $foto = basename((string)($row['foto'] ?? ''));
                            $idHargaItem = (int)($row['id_harga'] ?? 0);
                        ?>
                        <label class="service-card" data-card>
                            <input type="radio" name="id_harga" value="<?= $idHargaItem; ?>" required>

                            <div class="media">
                                <img
                                    src="../../assets/img/layanan/<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="<?= htmlspecialchars($row['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="body">
                                <div class="service-head">
                                    <h4><?= htmlspecialchars($row['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <span class="type-pill <?= $tipeClass; ?>">
                                        <?= htmlspecialchars($tipeRaw, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>

                                <div class="desc">Harga per <?= htmlspecialchars($row['satuan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="price">
                                    Rp <?= number_format((float)($row['harga'] ?? 0)); ?> / <?= htmlspecialchars($row['satuan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            </div>
                        </label>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">Data layanan belum tersedia.</div>
                <?php endif; ?>
            </div>

            <div class="mt-16">
                <button type="submit" class="btn-simpan">Lanjut</button>
            </div>
        </form>
        <?php endif; ?>

        <!-- ===============================
             STEP 2 : DETAIL + HITUNG
        =============================== -->
        <?php if ($id_harga > 0): ?>

        <div class="checkout-layout">

            <!-- KIRI: ringkasan layanan -->
            <div class="checkout-summary card">
                <div class="sum-head">
                    <div>
                        <div class="sum-title">Layanan Dipilih</div>
                        <div class="sum-sub">Pastikan paketnya sudah sesuai ya.</div>
                    </div>
                    <a class="sum-change" href="tambah.php">Ganti</a>
                </div>

                <?php if ($detail): ?>
                    <?php
                        $detailTipeRaw = (string)($detail['tipe'] ?? '');
                        $detailTipeClass = preg_replace('/[^a-z0-9_-]/', '', strtolower($detailTipeRaw));
                        $detailFoto = basename((string)($detail['foto'] ?? ''));
                    ?>
                    <div class="sum-item">
                        <div class="sum-thumb">
                            <img
                                src="../../assets/img/layanan/<?= htmlspecialchars($detailFoto, ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?= htmlspecialchars($detail['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="sum-info">
                            <div class="sum-name"><?= htmlspecialchars($detail['nama_layanan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="sum-meta">
                                <span class="type-pill <?= $detailTipeClass; ?>">
                                    <?= htmlspecialchars($detailTipeRaw, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                            <div class="sum-price">
                                Rp <?= number_format((float)($detail['harga'] ?? 0)); ?>
                                <span>/ <?= htmlspecialchars($detail['satuan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="sum-note">
                        <div class="note-row">
                            <span>Biaya jemput</span>
                            <b>Rp 5.000</b>
                        </div>
                        <div class="note-hint">Biaya jemput hanya berlaku jika metode “Jemput”.</div>
                    </div>
                <?php endif; ?>

                <?php if ($total > 0): ?>
                <div class="sum-totalbox" style="margin-top:12px;">
                    <div class="note-row">
                        <span>Estimasi Total</span>
                        <b>Rp <?= number_format((float)$total); ?></b>
                    </div>
                    <?php if ($metode === 'jemput'): ?>
                        <div class="note-hint">Termasuk biaya jemput Rp 5.000</div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- KANAN: form transaksi -->
            <div class="checkout-form card">
                <div class="form-head">
                    <div>
                        <div class="form-title">Detail Transaksi</div>
                        <div class="form-sub">Isi metode, berat, dan pembayaran.</div>
                    </div>
                </div>

                <!-- FORM HITUNG -->
                <form method="post" class="form-transaksi">
                    <input type="hidden" name="id_harga" value="<?= (int)$id_harga ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Metode</label>
                            <select name="metode" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="datang" <?= ($metode === 'datang') ? 'selected' : '' ?>>Datang</option>
                                <option value="jemput" <?= ($metode === 'jemput') ? 'selected' : '' ?>>Jemput (+Rp 5.000)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Berat (Kg)</label>
                            <input
                                type="number"
                                name="berat"
                                step="0.1"
                                min="1"
                                value="<?= htmlspecialchars((string)$berat, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="metode_pembayaran" required>
                                <option value="">-- Pilih Pembayaran --</option>
                                <option value="Transfer" <?= ($bayar === 'Transfer') ? 'selected' : '' ?>>Transfer</option>
                                <option value="QRIS" <?= ($bayar === 'QRIS') ? 'selected' : '' ?>>QRIS</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-simpan">Hitung Total</button>
                </form>

                <!-- PREVIEW TOTAL + SIMPAN -->
                <?php if ($total > 0): ?>
                <div style="margin-top:14px;">
                    <div class="total-card">
                        <div class="total-row">
                            <div>
                                <div class="total-label">Total</div>
                                <div class="total-value">Rp <?= number_format((float)$total); ?></div>
                                <?php if ($metode === 'jemput'): ?>
                                    <div class="total-note">Termasuk biaya jemput Rp 5.000</div>
                                <?php endif; ?>
                            </div>

                            <div>
                                <form action="../../controllers/TransaksiController.php" method="post" style="margin:0;">
                                    <input type="hidden" name="simpan" value="1">
                                    <input type="hidden" name="id_harga" value="<?= (int)$id_harga ?>">
                                    <input type="hidden" name="berat" value="<?= htmlspecialchars((string)$berat, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="metode" value="<?= htmlspecialchars((string)$metode, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="metode_pembayaran" value="<?= htmlspecialchars((string)$bayar, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="total_harga" value="<?= htmlspecialchars((string)$total, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn-simpan" style="width:auto; padding-left:18px; padding-right:18px;">
                                        Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>