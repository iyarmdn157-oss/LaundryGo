<?php
session_start();

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/Pelanggan.php";

// Validasi id
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$pelanggan = new Pelanggan($db->conn);

// 🔴 find() SUDAH return array
$data = $pelanggan->find($id);

// Jika data tidak ditemukan
if (!$data || !is_array($data)) {
    header("Location: index.php");
    exit;
}

$active = 'pelanggan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pelanggan - LaundryGo</title>

  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>

<body class="page-pelanggan page-pelanggan-form">

<!-- SIDEBAR biar nyambung -->
<?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">

  <div class="page-header">
    <div>
      <h1>Edit Pelanggan</h1>
      <div class="subtle">Perbarui data pelanggan yang dipilih</div>
    </div>
    <a href="index.php" class="btn-secondary no-underline">← Kembali</a>
  </div>

  <div class="card pelanggan-form-card">
    <form action="../../controllers/PelangganController.php" method="post" class="pelanggan-form">
      <!-- ✅ LOGIC TETAP -->
      <input type="hidden" name="aksi" value="update">
      <input type="hidden" name="id" value="<?= (int)($data['id_pelanggan'] ?? 0) ?>">

      <div class="form-grid-2">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username"
                 value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          <small class="hint">Gunakan tanpa spasi.</small>
        </div>

        <div class="form-group">
          <label>No HP</label>
          <input type="text" name="no_hp"
                 value="<?= htmlspecialchars($data['no_hp'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
          <small class="hint">Disarankan hanya angka.</small>
        </div>

        <div class="form-group span-2">
          <label>Nama</label>
          <input type="text" name="nama"
                 value="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-group span-2">
          <label>Alamat</label>
          <textarea name="alamat" rows="4" required><?= htmlspecialchars($data['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Update</button>
        <a href="index.php" class="btn-secondary no-underline">Batal</a>
      </div>
    </form>
  </div>

</div>

</body>
</html>
