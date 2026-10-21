<?php
session_start();
require_once "../config/Database.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// JIKA NAMA BELUM ADA DI SESSION → AMBIL DARI DB
if (!isset($_SESSION['nama'])) {
    $db = new Database();
    $conn = $db->conn;

    $username = $_SESSION['user'];

    $stmt = $conn->prepare("SELECT nama FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $_SESSION['nama'] = $user['nama'] ?? 'User';
}

$active = 'dashboard';
require_once __DIR__ . '/layout/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - LaundryGo</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="main">
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p class="subtle">Halo, <b><?= htmlspecialchars($_SESSION['nama']); ?></b> 👋</p>
        </div>
        <div class="subtle">Ayo mulai transaksi laundry kamu</div>
    </div>

    <div class="cards">
        <!-- Card 1 -->
        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value">Buat</div>
                    <div class="stat-label">Transaksi Laundry</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </div>
            </div>
            <p class="stat-desc">Pesan laundry reguler / express.</p>
            <div class="stat-actions">
                <a href="transaksi/tambah.php" class="btn btn-primary">Mulai Transaksi</a>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value">Cek</div>
                    <div class="stat-label">Status Laundry</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
            </div>
            <p class="stat-desc">Pantau proses cucian yang sedang berjalan.</p>
            <div class="stat-actions">
                <a href="transaksi/status.php" class="btn btn-soft">Lihat Status</a>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="card stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-value">Lihat</div>
                    <div class="stat-label">Riwayat Transaksi</div>
                </div>
                <div class="stat-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                        <path d="M3 3v6h6"/>
                        <path d="M12 7v6l4 2"/>
                    </svg>
                </div>
            </div>
            <p class="stat-desc">Daftar transaksi yang sudah pernah kamu buat.</p>
            <div class="stat-actions">
                <a href="user/riwayat.php" class="btn btn-soft">Buka Riwayat</a>
            </div>
        </div>
    </div>

    <!-- ✅ TARUH DI SINI (masih di dalam .main) -->
    <div class="dashboard-extra">

        <div class="promo-card">
            <div class="promo-content">
                <div class="promo-badge">PROMO</div>
                <h2>Cuci lebih hemat, hasil tetap rapi ✨</h2>
                <p>
                    Nikmati layanan Reguler & Express. Pilih jemput/antar atau datang langsung.
                    Pantau status cucian kamu real-time di LaundryGo.
                </p>

                <div class="promo-actions">
                    <a href="transaksi/tambah.php" class="btn btn-primary">Mulai Transaksi</a>
                    <a href="transaksi/status.php" class="btn btn-soft">Cek Status</a>
                </div>
            </div>

            <div class="promo-visual" aria-hidden="true">
            <img 
                src="../assets/img/Dashboard/promo.png" 
                        alt="LaundryGo Promo"
                        class="promo-img">
            </div>
        </div>

        <div class="about-grid">
            <div class="about-card">
                <div class="about-top">
                    <div class="about-ico">⚡</div>
                    <div>
                        <div class="about-title">Express & Reguler</div>
                        <div class="about-desc">Sesuaikan kebutuhan cepat atau standar.</div>
                    </div>
                </div>
            </div>

            <div class="about-card">
                <div class="about-top">
                    <div class="about-ico">📦</div>
                    <div>
                        <div class="about-title">Jemput / Datang</div>
                        <div class="about-desc">Pilih metode yang paling nyaman buat kamu.</div>
                    </div>
                </div>
            </div>

            <div class="about-card">
                <div class="about-top">
                    <div class="about-ico">🕒</div>
                    <div>
                        <div class="about-title">Tracking Status</div>
                        <div class="about-desc">Pantau: Dicuci, Disetrika, hingga Selesai.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ✅ END dashboard-extra -->

</div>
<!-- ✅ END .main -->

</body>
</html>