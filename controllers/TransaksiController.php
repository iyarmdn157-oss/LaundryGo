<?php
session_start();

require_once "../config/Database.php";
require_once "../models/Transaksi.php";

/**
 * TransaksiController
 * PHP Native + OOP
 */
class TransaksiController
{
    public function handle(): void
    {
        $db   = new Database();
        $conn = $db->conn;
        $transaksi = new Transaksi($conn);

        /* =====================================
           SIMPAN TRANSAKSI BARU (USER)
        ===================================== */
        if (isset($_POST['simpan'])) {

            if (!isset($_SESSION['id_user'])) {
                header("Location: ../views/login.php");
                exit;
            }

            $id_user = (int)$_SESSION['id_user'];

            // === CEK / INSERT PELANGGAN ===
            $stmt = $conn->prepare("
                SELECT id_pelanggan FROM pelanggan WHERE id_user = ?
            ");
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $cekPelanggan = $stmt->get_result();

            if ($cekPelanggan->num_rows === 0) {

                $stmt = $conn->prepare("
                    SELECT nama, alamat, no_hp FROM user WHERE id_user = ?
                ");
                $stmt->bind_param("i", $id_user);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();

                $stmt = $conn->prepare("
                    INSERT INTO pelanggan (id_user, nama, alamat, no_hp)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "isss",
                    $id_user,
                    $user['nama'],
                    $user['alamat'],
                    $user['no_hp']
                );
                $stmt->execute();
            }

            // === AMBIL ID PELANGGAN ===
            $stmt = $conn->prepare("
                SELECT id_pelanggan FROM pelanggan WHERE id_user = ?
            ");
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $id_pelanggan = $stmt->get_result()->fetch_assoc()['id_pelanggan'];

            // === DATA FORM ===
            $id_harga = (int)$_POST['id_harga'];
            $metode   = $_POST['metode']; // datang / jemput
            $berat    = (float)$_POST['berat'];
            $metode_pembayaran = $_POST['metode_pembayaran'];

            // === DATA HARGA ===
            $stmt = $conn->prepare("
                SELECT harga, durasi_hari
                FROM harga_layanan
                WHERE id_harga = ?
            ");
            $stmt->bind_param("i", $id_harga);
            $stmt->execute();
            $harga = $stmt->get_result()->fetch_assoc();

            if (!$harga) {
                die("Data harga tidak ditemukan");
            }

            // === HITUNG TOTAL ===
            $subtotal     = $harga['harga'] * $berat;
            $biaya_jemput = ($metode === 'jemput') ? 5000 : 0;
            $total_harga  = $subtotal + $biaya_jemput;

            $tgl_masuk = date('Y-m-d');
            $estimasi  = date('Y-m-d', strtotime("+{$harga['durasi_hari']} days"));

            // === SIMPAN TRANSAKSI ===
            $stmt = $conn->prepare("
                INSERT INTO transaksi 
                (
                    id_user, id_pelanggan, id_harga, metode, berat,
                    biaya_jemput, total_harga, metode_pembayaran,
                    status, tgl_masuk, estimasi_selesai
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'menunggu_pembayaran', ?, ?)
            ");
            $stmt->bind_param(
                "iiisiddsss",
                $id_user,
                $id_pelanggan,
                $id_harga,
                $metode,
                $berat,
                $biaya_jemput,
                $total_harga,
                $metode_pembayaran,
                $tgl_masuk,
                $estimasi
            );
            $stmt->execute();

            // ambil id transaksi terakhir
            $id_transaksi = $conn->insert_id;

            // ✅ REDIRECT SESUAI METODE PEMBAYARAN
            $mp = strtolower(trim($metode_pembayaran));

            if ($mp === 'qris') {
                header("Location: ../views/transaksi/qris.php?id=" . $id_transaksi);
                exit;
            }

            if ($mp === 'transfer') {
                header("Location: ../views/transaksi/transfer.php?id=" . $id_transaksi);
                exit;
            }

            // fallback kalau metode pembayaran selain dua itu
            header("Location: ../views/transaksi/struk.php?id=" . $id_transaksi);
            exit;
        }

        /* =====================================
           UPDATE STATUS TRANSAKSI (ADMIN)
        ===================================== */
        if (isset($_POST['update_status'])) {

            $id     = (int)$_POST['id_transaksi'];
            $status = $_POST['status'];

            $transaksi->updateStatus($id, $status);

            header("Location: ../views/transaksi/index.php");
            exit;
        }
    }
}

(new TransaksiController())->handle();
