<?php
session_start();
$active = 'layanan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Layanan</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-layanan">

    <?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

    <div class="main">
        <div class="page-header">
            <div>
                <h1>Tambah Layanan</h1>
                <div class="subtle">Tambahkan layanan baru untuk LaundryGo</div>
            </div>
            <a href="index.php" class="btn-secondary no-underline">Kembali</a>
        </div>

        <div class="card card-form">
            <h2 style="margin:0 0 12px;">➕ Tambah Layanan</h2>

            <form action="../../controllers/LayananController.php" method="post" class="form-transaksi">
                <input type="hidden" name="aksi" value="tambah">

                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input
                        type="text"
                        name="nama_layanan"
                        placeholder="Contoh: Cuci Setrika"
                        required
                    >
                </div>

                <div class="form-actions" style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="index.php" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>