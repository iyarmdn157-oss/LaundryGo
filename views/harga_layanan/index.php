<?php
session_start();

if (!isset($_SESSION['id_user'])) {
  header("Location: ../login.php");
  exit;
}

require_once __DIR__ . "/../../config/Database.php";

$db   = new Database();
$conn = $db->conn;

$active = 'harga_layanan';

// Ambil data harga + nama layanan
$stmt = $conn->prepare("
  SELECT 
    h.id_harga,
    h.tipe,
    h.harga,
    h.durasi_hari,
    h.satuan,
    l.nama_layanan
  FROM harga_layanan h
  JOIN layanan l ON h.id_layanan = l.id_layanan
  ORDER BY l.nama_layanan ASC, h.tipe ASC
");
$stmt->execute();
$list = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Harga Layanan</title>
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>

<!-- penting: pake class page-harga (sesuai punya kamu), tambahin page-harga-layanan biar gampang override css kalau perlu -->
<body class="page-harga page-harga-layanan">

<?php
// SIDEBAR
if (file_exists(__DIR__ . "/../layout/sidebar.php")) {
  include __DIR__ . "/../layout/sidebar.php";
}
?>

<div class="main">
  <div class="page-header">
    <div>
      <h1>Harga Layanan</h1>
      <div class="subtle">Kelola harga, durasi, dan tipe layanan.</div>
    </div>

    <a class="btn-primary" href="tambah.php">+ Tambah Harga</a>
  </div>

  <div class="card table-card">
    <div class="toolbar">
      <div class="meta">Menampilkan daftar harga layanan</div>
    </div>

    <div class="table-wrap">
      <table class="table harga-table">
        <thead>
          <tr>
            <th style="width:70px">No</th>
            <th>Layanan</th>
            <th style="width:120px">Tipe</th>
            <th style="width:160px">Harga</th>
            <th style="width:120px">Durasi</th>
            <th style="width:90px">Satuan</th>
            <th style="width:220px; text-align:center;">Aksi</th>
          </tr>
        </thead>

        <tbody>
        <?php if ($list && $list->num_rows > 0): ?>
          <?php $no = 1; while ($row = $list->fetch_assoc()): ?>
            <tr>
              <td style="text-align:center;"><?= $no++ ?></td>

              <td>
                <?= htmlspecialchars($row['nama_layanan'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
              </td>

              <td>
                <?= htmlspecialchars($row['tipe'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
              </td>

              <td style="white-space:nowrap;">
                Rp <?= number_format((float)($row['harga'] ?? 0), 0, ',', '.') ?>
              </td>

              <td style="white-space:nowrap;">
                <?= (int)($row['durasi_hari'] ?? 0) ?> hari
              </td>

              <td style="white-space:nowrap;">
                <?= htmlspecialchars($row['satuan'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
              </td>

              <td class="td-aksi">
                <!-- dibungkus biar tombol ga ketutup CSS lain -->
                <div class="aksi-btns">
                  <a class="btn-soft btn-mini" href="edit.php?id=<?= (int)$row['id_harga'] ?>">
                    Edit
                  </a>

                  <form action="../../controllers/HargaLayananController.php"
                        method="POST"
                        style="display:inline;"
                        onsubmit="return confirm('Yakin hapus harga ini?');">
                    <input type="hidden" name="id_harga" value="<?= (int)$row['id_harga'] ?>">
                    <button class="btn-danger btn-mini" type="submit" name="hapus_harga">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="empty-state">Belum ada data harga</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
</html>