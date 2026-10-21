<?php
session_start();
require_once __DIR__ . "/../../config/Database.php";

$db = new Database();
$conn = $db->conn;

$active = 'harga_layanan';

// dropdown layanan
$layanan = $conn->query("SELECT id_layanan, nama_layanan FROM layanan ORDER BY nama_layanan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Harga Layanan</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-harga">

<?php include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">
  <div class="page-header">
    <div>
      <h1>Tambah Harga</h1>
      <div class="subtle">Tambah harga untuk layanan tertentu</div>
    </div>
    <a class="btn-soft" href="index.php">Kembali</a>
  </div>

  <div class="card" style="padding:18px; max-width:720px;">
    <form action="../../controllers/HargaLayananController.php" method="POST" class="form-transaksi">
      <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="form-group">
          <label>Layanan</label>
          <select name="id_layanan" required>
            <option value="">-- Pilih --</option>
            <?php while($l = $layanan->fetch_assoc()): ?>
              <option value="<?= (int)$l['id_layanan'] ?>">
                <?= htmlspecialchars($l['nama_layanan'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" required>
            <option value="Reguler">Reguler</option>
            <option value="Express">Express</option>
          </select>
        </div>

        <div class="form-group">
          <label>Harga</label>
          <input type="number" name="harga" required min="0" placeholder="contoh: 8000">
        </div>

        <div class="form-group">
          <label>Durasi (hari)</label>
          <input type="number" name="durasi_hari" required min="1" placeholder="contoh: 2">
        </div>

        <div class="form-group">
          <label>Satuan</label>
          <input type="text" name="satuan" required value="Kg">
        </div>
      </div>

      <button class="btn-primary" type="submit" name="tambah_harga">Simpan</button>
    </form>
  </div>
</div>

</body>
</html>