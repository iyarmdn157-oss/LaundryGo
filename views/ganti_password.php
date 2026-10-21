<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$active = 'ganti_password'; // biar menu sidebar aktif

$pesan = $_GET['pesan'] ?? '';

function alert_text($pesan) {
    switch ($pesan) {
        case 'berhasil':   return ['success', 'Password berhasil diubah.'];
        case 'lama_salah': return ['danger', 'Password lama salah.'];
        case 'beda':       return ['danger', 'Konfirmasi password tidak sama.'];
        case 'kurang':     return ['danger', 'Password baru minimal 6 karakter.'];
        case 'kosong':     return ['danger', 'Semua field wajib diisi.'];
        case 'gagal':      return ['danger', 'Gagal mengubah password. Coba lagi.'];
        default:           return [null, null];
    }
}

list($type, $msg) = alert_text($pesan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Ganti Password - LaundryGo</title>

  <!-- path sudah benar karena file ada di /views -->
  <link rel="stylesheet" href="../assets/css/dashboard.css">

  <style>
    /* card-nya biar tetap rapi, tapi sekarang ada sidebar */
    .page-ganti-password .card{
      margin: 0;
      background:#fff;
      border-radius:14px;
      padding:22px;
      box-shadow:0 10px 30px rgba(0,0,0,.06)
    }
    .page-ganti-password .row{margin-bottom:14px}
    .page-ganti-password label{display:block;margin-bottom:6px;font-weight:600}
    .page-ganti-password input{width:100%;padding:11px 12px;border:1px solid #ddd;border-radius:10px}
    .page-ganti-password .btn{display:inline-block;padding:10px 14px;border-radius:10px;border:0;cursor:pointer;font-weight:700}
    .page-ganti-password .btn-primary{background:#2f6fed;color:#fff}
    .page-ganti-password .btn-soft{background:#eef3ff;color:#2f6fed;text-decoration:none}
    .page-ganti-password .alert{padding:12px 14px;border-radius:10px;margin-bottom:14px}
    .page-ganti-password .alert-success{background:#eafff0;color:#0c6a2b;border:1px solid #b7f0c5}
    .page-ganti-password .alert-danger{background:#fff1f1;color:#b42318;border:1px solid #ffd0d0}
    .page-ganti-password .actions{display:flex;gap:10px;align-items:center;margin-top:12px}
  </style>
</head>

<body class="page-ganti-password">

<?php
// ✅ tampilkan sidebar seperti halaman lain
if (file_exists(__DIR__ . "/layout/sidebar.php")) {
    include __DIR__ . "/layout/sidebar.php";
}
?>

<div class="main">
  <div class="page-header">
    <div>
      <h1>Ganti Password</h1>
      <p class="subtle">Perbarui password akun kamu dengan aman.</p>
    </div>
  </div>

  <div class="card">
    <p>Halo, <b><?= htmlspecialchars($_SESSION['nama'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></b></p>

    <?php if ($type && $msg): ?>
      <div class="alert alert-<?= $type === 'success' ? 'success' : 'danger'; ?>">
        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/PasswordController.php" method="POST">
      <div class="row">
        <label>Password Lama</label>
        <input type="password" name="password_lama" required>
      </div>

      <div class="row">
        <label>Password Baru</label>
        <input type="password" name="password_baru" required minlength="6">
      </div>

      <div class="row">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="konfirmasi_password" required minlength="6">
      </div>

      <div class="actions">
        <button class="btn btn-primary" type="submit" name="ganti_password">Simpan</button>
        <a class="btn btn-soft" href="user_dashboard.php">Kembali</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>