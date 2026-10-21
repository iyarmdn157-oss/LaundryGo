<?php
session_start();

$active = 'pelanggan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Pelanggan - LaundryGo</title>

  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>

<body class="page-pelanggan page-pelanggan-form">

<!-- SIDEBAR -->
<?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

<div class="main">

  <div class="page-header">
    <div>
      <h1>Tambah Pelanggan</h1>
      <div class="subtle">Lengkapi data pelanggan untuk ditambahkan</div>
    </div>
    <a href="index.php" class="btn-secondary no-underline">← Kembali</a>
  </div>

  <div class="card pelanggan-form-card">
    <form action="../../controllers/PelangganController.php" method="post" class="pelanggan-form">
      <input type="hidden" name="aksi" value="tambah">

      <div class="form-grid-2">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="contoh: suci123" required>
          <small class="hint">Gunakan tanpa spasi.</small>
        </div>

        <div class="form-group">
          <label>No HP</label>
          <input type="text" name="no_hp" placeholder="contoh: 08xxxxxxxxxx" required>
          <small class="hint">Disarankan hanya angka.</small>
        </div>

        <div class="form-group span-2">
          <label>Nama</label>
          <input type="text" name="nama" placeholder="Nama lengkap pelanggan" required>
        </div>

        <div class="form-group span-2">
          <label>Alamat</label>
          <textarea name="alamat" rows="4" placeholder="Alamat lengkap pelanggan" required></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary">Simpan</button>
        <a href="index.php" class="btn-secondary no-underline">Batal</a>
      </div>
    </form>
  </div>

</div>

</body>
</html>