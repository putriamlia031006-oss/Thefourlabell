<?php
session_start();
include "auth.php";
require "../koneksi.php";

$qProduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$totalProduk = mysqli_fetch_assoc($qProduk)['total'];

$qStok = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlahStok), 0) AS total FROM stok_produk");
$totalStok = mysqli_fetch_assoc($qStok)['total'];

$qPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$totalPesanan = mysqli_fetch_assoc($qPesanan)['total'];

$qPendapatan = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total FROM pesanan");
$totalPendapatan = mysqli_fetch_assoc($qPendapatan)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Laporan - Admin The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #fbf7ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.main-content {
    margin-left: 240px;
    min-height: 100vh;
    padding: 34px;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #9d7ad6, #8e44ad);
    border-radius: 28px;
    padding: 30px;
    color: white;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px rgba(142, 68, 173, 0.18);
}

.page-header::before {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    background: rgba(255,255,255,.13);
    top: -80px;
    right: -55px;
}

.page-header-content {
    position: relative;
    z-index: 2;
}

.header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: rgba(255,255,255,.20);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 14px;
    border: 1px solid rgba(255,255,255,.22);
}

.page-title {
    font-size: 34px;
    font-weight: 900;
    margin: 0 0 8px;
}

.page-subtitle {
    margin: 0;
    font-size: 15px;
    opacity: .95;
}

.summary-card,
.report-card {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 24px;
    padding: 22px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    height: 100%;
    transition: .25s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before,
.report-card::before {
    content: "";
    position: absolute;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: #f4eaff;
    top: -34px;
    right: -34px;
}

.summary-card:hover,
.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.16);
}

.card-inner {
    position: relative;
    z-index: 2;
}

.icon-box {
    width: 54px;
    height: 54px;
    border-radius: 18px;
    background: #f1e3ff;
    color: #8e44ad;
    border: 1px solid #eadcff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}

.summary-label {
    margin: 0;
    color: #6b6175;
    font-size: 14px;
    font-weight: 700;
}

.summary-value {
    margin: 5px 0 0;
    color: #7b3fb2;
    font-size: 28px;
    font-weight: 900;
}

.section-title {
    color: #6f2da8;
    font-weight: 850;
    margin: 30px 0 18px;
}

.report-card h4 {
    color: #4b2e63;
    font-weight: 900;
    margin-bottom: 10px;
}

.report-card p {
    color: #81758d;
    margin-bottom: 18px;
    line-height: 1.6;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 15px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    box-shadow: 0 9px 20px rgba(142, 68, 173, 0.20);
    transition: .25s ease;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-outline-lavender {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
    border-radius: 15px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    transition: .25s ease;
}

.btn-outline-lavender:hover {
    background: #f4eaff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 24px;
    }
}
</style>
</head>

<body>

<?php include "sidebar.php"; ?>

<main class="main-content">

    <div class="page-header">
        <div class="page-header-content">
            <div class="header-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>

            <h2 class="page-title">Laporan</h2>
            <p class="page-subtitle">
                Pilih jenis laporan yang ingin dilihat atau dicetak pada sistem The Four Label.
            </p>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <p class="summary-label">Total Produk</p>
                    <h3 class="summary-value"><?= $totalProduk; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <p class="summary-label">Total Stok</p>
                    <h3 class="summary-value"><?= $totalStok; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <p class="summary-label">Total Pesanan</p>
                    <h3 class="summary-value"><?= $totalPesanan; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <p class="summary-label">Total Pendapatan</p>
                    <h3 class="summary-value">
                        Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?>
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <h4 class="section-title">Pilih Laporan</h4>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="report-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>

                    <h4>Laporan Stok Produk</h4>
                    <p>
                        Menampilkan daftar stok produk, kategori, harga, jumlah stok, dan satuan produk.
                    </p>

                    <a href="laporan-stok.php" class="btn-lavender">
                        <i class="fa-solid fa-eye"></i>
                        Buka Laporan Stok
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="report-card">
                <div class="card-inner">
                    <div class="icon-box">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>

                    <h4>Laporan Transaksi</h4>
                    <p>
                        Menampilkan daftar transaksi pesanan, customer, status, pengiriman, dan total pembayaran.
                    </p>

                    <a href="laporan-transaksi.php" class="btn-outline-lavender">
                        <i class="fa-solid fa-eye"></i>
                        Buka Laporan Transaksi
                    </a>
                </div>
            </div>
        </div>

    </div>

</main>

</body>
</html>