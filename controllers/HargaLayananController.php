<?php
session_start();
require_once __DIR__ . "/../config/Database.php";

$db = new Database();
$conn = $db->conn;

function back_to_index() {
  header("Location: ../views/harga_layanan/index.php");
  exit;
}

// TAMBAH
if (isset($_POST['tambah_harga'])) {
  $id_layanan  = (int)($_POST['id_layanan'] ?? 0);
  $tipe        = trim($_POST['tipe'] ?? '');
  $harga       = (int)($_POST['harga'] ?? 0);
  $durasi_hari = (int)($_POST['durasi_hari'] ?? 0);
  $satuan      = trim($_POST['satuan'] ?? 'Kg');

  if ($id_layanan <= 0 || $tipe=='' || $harga < 0 || $durasi_hari <= 0) back_to_index();

  $stmt = $conn->prepare("
    INSERT INTO harga_layanan (id_layanan, tipe, harga, durasi_hari, satuan)
    VALUES (?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("isiis", $id_layanan, $tipe, $harga, $durasi_hari, $satuan);
  $stmt->execute();
  back_to_index();
}

// UPDATE
if (isset($_POST['update_harga'])) {
  $id_harga    = (int)($_POST['id_harga'] ?? 0);
  $id_layanan  = (int)($_POST['id_layanan'] ?? 0);
  $tipe        = trim($_POST['tipe'] ?? '');
  $harga       = (int)($_POST['harga'] ?? 0);
  $durasi_hari = (int)($_POST['durasi_hari'] ?? 0);
  $satuan      = trim($_POST['satuan'] ?? 'Kg');

  if ($id_harga <= 0 || $id_layanan <= 0 || $tipe=='' || $harga < 0 || $durasi_hari <= 0) back_to_index();

  $stmt = $conn->prepare("
    UPDATE harga_layanan
    SET id_layanan=?, tipe=?, harga=?, durasi_hari=?, satuan=?
    WHERE id_harga=?
  ");
  $stmt->bind_param("isiisi", $id_layanan, $tipe, $harga, $durasi_hari, $satuan, $id_harga);
  $stmt->execute();
  back_to_index();
}

// HAPUS
if (isset($_POST['hapus_harga'])) {
  $id_harga = (int)($_POST['id_harga'] ?? 0);
  if ($id_harga <= 0) back_to_index();

  $stmt = $conn->prepare("DELETE FROM harga_layanan WHERE id_harga=?");
  $stmt->bind_param("i", $id_harga);
  $stmt->execute();
  back_to_index();
}

back_to_index();