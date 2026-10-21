<?php
session_start();
require_once "../config/Database.php";

// PROTEKSI ADMIN
if (!isset($_SESSION['login']) || ($_SESSION['role'] ?? '') != 'admin') {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->conn;
$active = 'dashboard';

// ======================
// DATA CARD DASHBOARD
// ======================
$data = [];

// total pelanggan
$data['pelanggan'] = $conn->query("SELECT COUNT(*) total FROM pelanggan")->fetch_assoc()['total'];

// total layanan
$data['layanan'] = $conn->query("SELECT COUNT(*) total FROM layanan")->fetch_assoc()['total'];

// total transaksi
$data['transaksi'] = $conn->query("SELECT COUNT(*) total FROM transaksi")->fetch_assoc()['total'];

// total pendapatan (status selesai)
$data['pendapatan'] = $conn->query("
    SELECT IFNULL(SUM(total_harga),0) total 
    FROM transaksi 
    WHERE status='selesai'
")->fetch_assoc()['total'];

// ======================
// GRAFIK (FIX URUTAN BULAN)
// ======================
$bulanNama = [
    1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
    7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
];

// ---- GRAFIK TRANSAKSI (berdasarkan tgl_masuk) ----
$grafikTransaksi = [];
$q1 = "
    SELECT 
        YEAR(tgl_masuk) AS th,
        MONTH(tgl_masuk) AS bln,
        COUNT(*) AS total
    FROM transaksi
    GROUP BY YEAR(tgl_masuk), MONTH(tgl_masuk)
    ORDER BY th ASC, bln ASC
";
$r1 = $conn->query($q1);
while ($row = $r1->fetch_assoc()) {
    $bln = (int)$row['bln'];
    $th  = (int)$row['th'];

    $grafikTransaksi[] = [
        'bulan' => $bulanNama[$bln] . ' ' . $th,  // contoh: Jan 2026
        'total' => (int)$row['total']
    ];
}

// ---- GRAFIK PENDAPATAN (status selesai) ----
// Kalau kamu punya kolom tgl_keluar lebih cocok pakai tgl_keluar,
// tapi kalau belum ada, tetap pakai tgl_masuk seperti punyamu.
$grafikPendapatan = [];
$q2 = "
    SELECT 
        YEAR(tgl_masuk) AS th,
        MONTH(tgl_masuk) AS bln,
        IFNULL(SUM(total_harga),0) AS total
    FROM transaksi
    WHERE status='selesai'
    GROUP BY YEAR(tgl_masuk), MONTH(tgl_masuk)
    ORDER BY th ASC, bln ASC
";
$r2 = $conn->query($q2);
while ($row = $r2->fetch_assoc()) {
    $bln = (int)$row['bln'];
    $th  = (int)$row['th'];

    $grafikPendapatan[] = [
        'bulan' => $bulanNama[$bln] . ' ' . $th,  // contoh: Jan 2026
        'total' => (int)$row['total']
    ];
}

// SIDEBAR
require_once __DIR__ . '/layout/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LaundryGo</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="main">
    <div class="dashboard-header">
        <div>
            <h1>Dashboard Admin</h1>
            <div class="subtle">Selamat datang kembali, <b><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></b></div>
        </div>
        <div class="subtle">Ringkasan aktivitas laundry</div>
    </div>

    <div class="cards">
        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value"><?= (int)$data['pelanggan'] ?></div>
                    <div class="stat-label">Total Pelanggan</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2c-4.2 0-7.5 2.2-7.5 5v1h15v-1c0-2.8-3.3-5-7.5-5Z"/></svg>
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value"><?= (int)$data['layanan'] ?></div>
                    <div class="stat-label">Total Layanan</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M7 10 12 3l5 7"/><path d="M3 10h18l-2 11H5L3 10Z"/></svg>
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value"><?= (int)$data['transaksi'] ?></div>
                    <div class="stat-label">Total Transaksi</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M3 7h18v10H3V7Z"/><path d="M3 10h18"/></svg>
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value">Rp <?= number_format((int)$data['pendapatan']) ?></div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="cards mt-24">
        <div class="card" style="grid-column: span 2;">
            <h3>Transaksi per Bulan</h3>
            <p class="subtle">Jumlah transaksi masuk tiap bulan</p>
            <canvas id="grafikTransaksi" height="120"></canvas>
        </div>

        <div class="card" style="grid-column: span 2;">
            <h3>Pendapatan per Bulan</h3>
            <p class="subtle">Total pendapatan dari transaksi selesai</p>
            <canvas id="grafikPendapatan" height="120"></canvas>
        </div>
    </div>
</div>

<script>
// GRAFIK TRANSAKSI
new Chart(document.getElementById('grafikTransaksi'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($grafikTransaksi,'bulan')) ?>,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: <?= json_encode(array_column($grafikTransaksi,'total')) ?>,
            backgroundColor: '#2563eb',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true }
        }
    }
});

// GRAFIK PENDAPATAN
new Chart(document.getElementById('grafikPendapatan'), {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($grafikPendapatan,'bulan')) ?>,
        datasets: [{
            label: 'Pendapatan',
            data: <?= json_encode(array_column($grafikPendapatan,'total')) ?>,
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.15)',
            fill: true,
            tension: 0.35,
            pointRadius: 3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>