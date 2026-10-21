<?php
session_start();

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/Transaksi.php";

$db = new Database();
$transaksi = new Transaksi($db->conn);

// LOGIC CARI (TIDAK DIUBAH)
$keyword = $_GET['keyword'] ?? '';
if ($keyword != '') {
    $list = $transaksi->search($keyword);
} else {
    $list = $transaksi->all();
}

$active = 'transaksi';

// helper format tanggal biar rapih
function fmt_tgl($val) {
    if (!$val) return '-';
    $ts = strtotime($val);
    if (!$ts) return '-';
    return date('d M Y', $ts);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">

    <style>
    /* 1) Semua judul kolom TENGAH */
    .page-transaksi .transaksi-table thead th{
        text-align:center !important;
    }

    /* 2) Default isi tabel TENGAH */
    .page-transaksi .transaksi-table tbody td{
        text-align:center;
    }

    /* 3) METODE & BERAT ke KIRI */
    .page-transaksi .transaksi-table tbody td:nth-child(4),
    .page-transaksi .transaksi-table tbody td:nth-child(5){
        text-align:left !important;
    }

    /* 4) TOTAL BALIK KE TENGAH */
    .page-transaksi .transaksi-table tbody td:nth-child(6){
        text-align:center !important;
    }

    /* Pelanggan, layanan & alamat lebih enak dibaca kiri */
    .page-transaksi .transaksi-table tbody td:nth-child(2),
    .page-transaksi .transaksi-table tbody td:nth-child(3),
    .page-transaksi .transaksi-table tbody td:nth-child(10){
        text-align:left !important;
    }
    body{ outline:5px solid red !important; }
    </style>
</head>
<body class="page-transaksi">

    <!-- SIDEBAR -->
    <?php if (file_exists(__DIR__ . "/../layout/sidebar.php")) include __DIR__ . "/../layout/sidebar.php"; ?>

    <div class="main page-transaksi">

        <!-- HEADER -->
        <div class="page-header">
            <div>
                <h1>Data Transaksi Laundry</h1>
                <div class="subtle">Kelola status transaksi pelanggan</div>
            </div>

            <!-- FORM CARI -->
            <form method="GET" class="form-search" style="max-width:520px;">
                <div style="flex:1;">
                    <input type="text"
                           name="keyword"
                           placeholder="Cari nama pelanggan..."
                           value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button type="submit" class="btn-primary">Cari</button>
            </form>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="table transaksi-table">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Metode</th>
                            <th>Berat</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal Masuk</th>
                            <th>Estimsi Selesai</th>
                            <th>Alamat Jemput</th>
                            <th width="260">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($list && $list->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $list->fetch_assoc()): ?>

                            <?php
                            // 🔴 FILTER: TRANSAKSI SELESAI TIDAK DITAMPILKAN (LOGIC TETAP)
                            if (($row['status'] ?? '') == 'selesai') continue;

                            $statusRaw = (string)($row['status'] ?? '');
                            $status = strtolower(trim($statusRaw));
                            $statusClass = preg_replace('/[^a-z0-9_-]/', '', $status);

                            // ✅ FIX: label status rapi (menunggu_pembayaran -> Menunggu Pembayaran)
                            $labelStatus = ucwords(str_replace('_', ' ', $status));

                            // ✅ FIX: class status untuk warna (menunggu_pembayaran -> pending)
                            $badgeClass = $statusClass;
                            if ($statusClass === 'menunggu_pembayaran') {
                                $badgeClass = 'pending';
                            }

                            $tglMasuk  = fmt_tgl($row['tgl_masuk'] ?? null);
                            // "tanggal keluar" sementara pakai estimasi_selesai (sesuai struktur kamu)
                            $tglKeluar = fmt_tgl($row['estimasi_selesai'] ?? null);
                            ?>

                            <tr>
                                <td><?= $no++ ?></td>

                                <td><?= htmlspecialchars($row['nama_pelanggan'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                                <td class="td-layanan">
                                    <?= htmlspecialchars($row['nama_layanan'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($row['tipe'])): ?>
                                        <div class="subtle" style="margin-top:4px;">
                                            (<?= htmlspecialchars($row['tipe'], ENT_QUOTES, 'UTF-8') ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars(ucfirst($row['metode'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>

                                <td><?= number_format((float)($row['berat'] ?? 0), 2) ?> Kg</td>

                                <td>
                                    Rp <?= number_format((float)($row['total_harga'] ?? 0), 0, ',', '.') ?>
                                </td>

                                <td class="td-status">
                                    <!-- ✅ FIX: pakai class "status" biar warna muncul -->
                                    <span class="status <?= $badgeClass ?>">
                                        <?= htmlspecialchars($labelStatus, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($tglMasuk, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($tglKeluar, ENT_QUOTES, 'UTF-8') ?></td>

                                <td class="td-alamat">
                                    <?= (($row['metode'] ?? '') == 'jemput')
                                        ? htmlspecialchars($row['alamat'] ?? '-', ENT_QUOTES, 'UTF-8')
                                        : '-' ?>
                                </td>

                                <td class="td-aksi">
                                    <form action="../../controllers/TransaksiController.php"
                                          method="POST"
                                          class="aksi-status">

                                        <input type="hidden" name="id_transaksi"
                                               value="<?= (int)($row['id_transaksi'] ?? 0) ?>">

                                        <select name="status" class="aksi-select">
                                            <option value="diproses" <?= ($status=='diproses')?'selected':''; ?>>Diproses</option>
                                            <option value="dicuci" <?= ($status=='dicuci')?'selected':''; ?>>Dicuci</option>
                                            <option value="disetrika" <?= ($status=='disetrika')?'selected':''; ?>>Disetrika</option>
                                            <option value="selesai">Selesai</option>
                                        </select>

                                        <button type="submit" name="update_status" class="btn-primary btn-mini">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="empty-state">Data transaksi tidak ditemukan</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
