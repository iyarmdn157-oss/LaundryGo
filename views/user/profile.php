<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/User.php";

$db = new Database();
$userModel = new User($db->conn);

$active = 'profil';

// ambil data user terbaru dari DB
$id_user = (int)($_SESSION['id_user'] ?? 0);
$data = $userModel->findById($id_user);

if (!$data) {
    die("Data user tidak ditemukan.");
}

// pesan alert dari controller
$pesan = $_GET['pesan'] ?? '';

function alert_profile($pesan) {
    switch ($pesan) {
        case 'berhasil':
            return [
                'success',
                'Berhasil!',
                'Profil kamu berhasil diperbarui.',
                '✅'
            ];
        case 'username':
            return [
                'danger',
                'Oops!',
                'Username sudah dipakai, coba yang lain.',
                '⚠️'
            ];
        case 'kosong':
            return [
                'danger',
                'Lengkapi data',
                'Semua field wajib diisi.',
                '📝'
            ];
        case 'gagal':
            return [
                'danger',
                'Gagal!',
                'Gagal menyimpan profil. Coba lagi.',
                '❌'
            ];
        default:
            return [null, null, null, null];
    }
}

list($type, $title, $msg, $icon) = alert_profile($pesan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Profil - LaundryGo</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="page-profil">

<?php
if (file_exists(__DIR__ . "/../layout/sidebar.php")) {
    include __DIR__ . "/../layout/sidebar.php";
}
?>

<div class="main">
  <div class="page-header">
    <div>
      <h1>Profil</h1>
      <p class="subtle">Kelola data akun kamu.</p>
    </div>
  </div>

  <div class="card profile-card">

    <?php if ($type && $msg): ?>
      <div class="alert alert-<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>">
        <span class="alert-icon"><?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?></span>
        <div class="alert-body">
          <div class="alert-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="alert-msg"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>
    <?php endif; ?>

    <form action="../../controllers/ProfileController.php" method="POST" class="profile-form">
      <div class="profile-grid">

        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" required
                 value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
          <label>Nama</label>
          <input type="text" name="nama" required
                 value="<?= htmlspecialchars($data['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group profile-full">
          <label>Alamat</label>
          <input type="text" name="alamat" required
                 value="<?= htmlspecialchars($data['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
          <label>No HP</label>
          <input type="text" name="no_hp" required
                 value="<?= htmlspecialchars($data['no_hp'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

      </div>

      <div class="profile-actions">
        <button type="submit" name="update_profil" class="btn-primary">Simpan</button>
        <a href="../user_dashboard.php" class="btn-soft">Kembali</a>
      </div>
    </form>

  </div>
</div>

</body>
</html>
