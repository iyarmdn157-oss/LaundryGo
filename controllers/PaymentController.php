<?php
session_start();

require_once "../config/Database.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../views/login.php");
    exit;
}

$db = new Database();
$conn = $db->conn;

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action !== 'confirm' || $id <= 0) {
    die("Aksi tidak valid.");
}

$id_user = (int)$_SESSION['id_user'];

// pastikan transaksi milik user
$stmt = $conn->prepare("SELECT id_transaksi FROM transaksi WHERE id_transaksi = ? AND id_user = ? LIMIT 1");
$stmt->bind_param("ii", $id, $id_user);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Transaksi tidak ditemukan.");
}

// ✅ update status jadi diproses
$stmt = $conn->prepare("UPDATE transaksi SET status = 'diproses' WHERE id_transaksi = ? AND id_user = ?");
$stmt->bind_param("ii", $id, $id_user);
$stmt->execute();

// lanjut ke struk
header("Location: ../views/transaksi/struk.php?id=" . $id);
exit;
