<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/base_url.php';

$role   = $_SESSION['role'] ?? 'user';
$active = $active ?? '';

// fallback kalau base_url kosong
if (!isset($base_url) || $base_url === '') {
    $base_url = '/laundrygo'; // sesuaikan kalau folder project beda
}

/* ICON SVG */
if (!function_exists('lg_icon')) {
    function lg_icon($name)
    {
        $icons = [
            'dashboard' => '<svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>',
            'user'      => '<svg viewBox="0 0 24 24"><path d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12Zm0 2c-4.2 0-7.5 2.2-7.5 5v1h15v-1c0-2.8-3.3-5-7.5-5Z"/></svg>',
            'basket'    => '<svg viewBox="0 0 24 24"><path d="M7 10 12 3l5 7"/><path d="M3 10h18l-2 11H5L3 10Z"/></svg>',
            'card'      => '<svg viewBox="0 0 24 24"><path d="M3 7h18v10H3V7Z"/><path d="M3 10h18"/></svg>',
            'history'   => '<svg viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 3v6h6"/><path d="M12 7v6l4 2"/></svg>',
            'plus'      => '<svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>',
            'clock'     => '<svg viewBox="0 0 24 24"><path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z"/><path d="M12 6v6l4 2"/></svg>',
            'price'     => '<svg viewBox="0 0 24 24"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>',
            'key'       => '<svg viewBox="0 0 24 24"><path d="M7 14a5 5 0 1 1 3.9 4.9L9 21H7v-2H5v-2l2.1-2.1A5 5 0 0 1 7 14Z"/><circle cx="17" cy="11" r="2"/></svg>',
            'logout'    => '<svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3h-8v18h8"/></svg>',
        ];
        return $icons[$name] ?? '';
    }
}
?>

<div class="sidebar">

    <!-- BRAND -->
    <div class="brand" style="display:flex;align-items:center;gap:12px;">
        <div class="brand-logo"
             style="width:44px;height:44px;border-radius:14px;overflow:hidden;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.18);flex:0 0 44px;display:flex;align-items:center;justify-content:center;">
            <img
                src="<?= $base_url ?>/assets/img/Login/logo.png"
                alt="LaundryGo"
                style="width:100%;height:100%;object-fit:cover;display:block;"
                onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=&quot;color:#fff;font-weight:900;&quot;>LG</div>';"
            >
        </div>

        <div class="title" style="color:#fff;">
            <b style="display:block;font-size:16px;line-height:1.1;">LaundryGo</b>
            <span style="display:block;font-size:12px;opacity:.85;margin-top:2px;">
                <?= ($role === 'admin') ? 'Admin Panel' : 'User Area' ?>
            </span>
        </div>
    </div>

    <!-- MENU -->
    <div class="menu">

        <!-- DASHBOARD -->
        <a href="<?= $base_url ?>/views/<?= ($role === 'admin') ? 'dashboard.php' : 'user_dashboard.php' ?>"
           class="<?= ($active === 'dashboard') ? 'active' : '' ?>">
            <span class="icon"><?= lg_icon('dashboard') ?></span>
            <span class="text">Dashboard</span>
        </a>

        <?php if ($role === 'admin'): ?>

            <a href="<?= $base_url ?>/views/pelanggan/index.php"
               class="<?= ($active === 'pelanggan') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('user') ?></span>
                <span class="text">Pelanggan</span>
            </a>

            <a href="<?= $base_url ?>/views/layanan/index.php"
               class="<?= ($active === 'layanan') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('basket') ?></span>
                <span class="text">Layanan</span>
            </a>

            <a href="<?= $base_url ?>/views/transaksi/index.php"
               class="<?= ($active === 'transaksi') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('card') ?></span>
                <span class="text">Transaksi</span>
            </a>

            <a href="<?= $base_url ?>/views/transaksi/riwayat.php"
               class="<?= ($active === 'riwayat') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('history') ?></span>
                <span class="text">Riwayat</span>
            </a>

            <a href="<?= $base_url ?>/views/harga_layanan/index.php"
               class="<?= ($active === 'harga_layanan') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('price') ?></span>
                <span class="text">Harga Layanan</span>
            </a>

        <?php else: ?>

            <!-- USER MENU -->
            <a href="<?= $base_url ?>/views/transaksi/tambah.php"
               class="<?= ($active === 'transaksi') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('plus') ?></span>
                <span class="text">Buat Transaksi</span>
            </a>

            <a href="<?= $base_url ?>/views/user/riwayat.php"
               class="<?= ($active === 'riwayat') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('history') ?></span>
                <span class="text">Riwayat</span>
            </a>

            <a href="<?= $base_url ?>/views/transaksi/status.php"
               class="<?= ($active === 'status') ? 'active' : '' ?>">
                <span class="icon"><?= lg_icon('clock') ?></span>
                <span class="text">Status Laundry</span>
            </a>

            <a href="<?= $base_url ?>/views/user/profile.php"
                class="<?= ($active === 'profil') ? 'active' : '' ?>">
                 <span class="icon"><?= lg_icon('user') ?></span>
                 <span class="text">Profil</span>
            </a>
        <?php endif; ?>

        <!-- COMMON -->
        <a href="<?= $base_url ?>/views/ganti_password.php"
           class="<?= ($active === 'ganti_password') ? 'active' : '' ?>">
            <span class="icon"><?= lg_icon('key') ?></span>
            <span class="text">Ganti Password</span>
        </a>
    </div>

    <!-- LOGOUT -->
    <div class="logout">
        <a href="<?= $base_url ?>/controllers/LogoutController.php">
            <span class="icon"><?= lg_icon('logout') ?></span>
            Logout
        </a>
    </div>

</div>