# 🧺 LaundryGo — Web-Based Laundry Management System

[![PHP Native](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Architecture](https://img.shields.io/badge/Architecture-MVC%20%2F%20OOP-orange?style=for-the-badge)]()
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)]()

**LaundryGo** adalah aplikasi manajemen layanan laundry berbasis web yang dibangun menggunakan **PHP Native dengan pendekatan Object-Oriented Programming (OOP)** serta arsitektur **Model-View-Controller (MVC)**. 

Aplikasi ini dirancang untuk mempermudah operasional usaha laundry, mulai dari pencatatan data pelanggan, pemrosesan transaksi reguler/express, hingga pelacakan status cucian (*order tracking*) secara real-time oleh pelanggan.

---

## 📌 Fitur Utama

### 👤 Hak Akses Pelanggan (User Area)
- **Autentikasi Akun:** Registrasi, login, dan manajemen profil pengguna.
- **Pemesanan Layanan:** Pemilihan tipe layanan (Reguler, Express, Dry Clean, dll) dengan opsi antar-jemput.
- **Real-Time Order Tracking:** Pemantauan status pengerjaan cucian (Dicuci, Dijemur, Disetrika, Siap Diambil).
- **Riwayat & Cetak Struk:** Transparansi riwayat transaksi serta opsi cetak rincian pembayaran.

### 🛡️ Hak Akses Admin (Back-Office Management)
- **Dashboard Ringkasan:** Statistik pendapatan dan jumlah pesanan aktif.
- **Manajemen Transaksi:** Pembaharuan status pengerjaan dan konfirmasi pembayaran (Cash / QRIS / Transfer).
- **Manajemen Data Master:** Pengelolaan daftar harga layanan dan informasi pelanggan.

---

## 🏗️ Arsitektur & Struktur Proyek

Proyek ini menerapkan pola arsitektur **MVC (Model-View-Controller)** untuk memisahkan logika bisnis, struktur data, dan tampilan antarmuka:

```text
laundrygo/
├── assets/            # Resource statis (CSS, Gambar, Icons)
├── config/            # Konfigurasi koneksi database & URL dasar
│   ├── Database.php   # Kelas koneksi PDO/MySQLi (OOP)
│   └── base_url.php
├── controllers/       # Logika bisnis & pengontrol alur data
├── models/            # Query & pemrosesan data MySQL (Database Layer)
├── views/             # Tampilan antarmuka pengguna (UI/HTML)
└── index.php          # Entry point utama aplikasi
