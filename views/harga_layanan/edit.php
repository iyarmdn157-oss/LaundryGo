<?php
session_start();
require_once __DIR__ . "/../../config/Database.php";

$db = new Database();
$conn = $db->conn;

$active = 'harga_layanan';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: index.php"); exit; }

// data harga
$stmt = $conn->prepare("SELECT * FROM harga_layanan WHERE id_harga=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) { header("Location: index.php"); exit; }

// dropdown layanan
$layanan = $conn->query("SELECT id_layanan, nama_layanan FROM layanan ORDER BY nama_layanan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Harga Layanan</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-harga">

<?php include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">
  <div class="page-header">
    <div>
      <h1>Edit Harga</h1>
      <div class="subtle">Ubah harga/durasi/satuan</div>
    </div>
    <a class="btn-soft" href="index.php">Kembali</a>
  </div>

  <div class="card" style="padding:18px; max-width:720px;">
    <form action="../../controllers/HargaLayananController.php" method="POST" class="form-transaksi">
      <input type="hidden" name="id_harga" value="<?= (int)$data['id_harga'] ?>">

      <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="form-group">
          <label>Layanan</label>
          <select name="id_layanan" required>
            <?php while($l = $layanan->fetch_assoc()): ?>
              <option value="<?= (int)$l['id_layanan'] ?>"
                <?= ((int)$data['id_layanan'] === (int)$l['id_layanan']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['nama_layanan'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" required>
            <option value="Reguler" <?= ($data['tipe']=='Reguler')?'selected':''; ?>>Reguler</option>
            <option value="Express" <?= ($data['tipe']=='Express')?'selected':''; ?>>Express</option>
          </select>
        </div>

        <div class="form-group">
          <label>Harga</label>
          <input type="number" name="harga" required min="0"
                 value="<?= (int)$data['harga'] ?>">
        </div>

        <div class="form-group">
          <label>Durasi (hari)</label>
          <input type="number" name="durasi_hari" required min="1"
                 value="<?= (int)$data['durasi_hari'] ?>">
        </div>

        <div class="form-group">
          <label>Satuan</label>
          <input type="text" name="satuan" required
                 value="<?= htmlspecialchars($data['satuan'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <button class="btn-primary" type="submit" name="update_harga">Update</button>
    </form>
  </div>
</div>

</body>
</html>