<?php
session_start();
include "auth.php";
require "../koneksi.php";

/* =========================
   DATA LAPORAN STOK
========================= */
$stok = mysqli_query($koneksi, "
    SELECT 
        produk.idProduk,
        produk.namaProduk,
        produk.harga,
        kategori.namaKategori,
        stok_produk.jumlahStok,
        stok_produk.satuan
    FROM stok_produk
    JOIN produk 
        ON stok_produk.idProduk = produk.idProduk
    LEFT JOIN kategori
        ON produk.idKategori = kategori.idKategori
    ORDER BY produk.namaProduk ASC
");

if (!$stok) {
    die("Query stok error: " . mysqli_error($koneksi));
}

/* =========================
   RINGKASAN
========================= */
$qTotalProduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$totalProduk = mysqli_fetch_assoc($qTotalProduk)['total'];

$qTotalStok = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlahStok), 0) AS total FROM stok_produk");
$totalStok = mysqli_fetch_assoc($qTotalStok)['total'];

$qStokHabis = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM stok_produk WHERE jumlahStok <= 0");
$totalHabis = mysqli_fetch_assoc($qStokHabis)['total'];

$namaAdmin = "Admin";

if (isset($_SESSION['user']['nama'])) {
    $namaAdmin = $_SESSION['user']['nama'];
} elseif (isset($_SESSION['nama'])) {
    $namaAdmin = $_SESSION['nama'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Laporan Stok Produk - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
    box-sizing: border-box;
}

html, body {
    overflow-x: hidden;
}

body {
    margin: 0;
    background: #fbf7ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

/* MAIN CONTENT */
.main-content {
    margin-left: 240px;
    min-height: 100vh;
    padding: 34px;
}

/* HEADER */
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

/* SUMMARY */
.summary-card {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 24px;
    padding: 22px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: "";
    position: absolute;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: #f4eaff;
    top: -34px;
    right: -34px;
}

.summary-inner {
    position: relative;
    z-index: 2;
}

.summary-icon {
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

/* TOOLBAR */
.toolbar {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 28px;
}

.toolbar h4 {
    color: #6f2da8;
    font-weight: 850;
    margin: 0;
}

.btn-lavender,
.btn-reset {
    border-radius: 15px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    transition: .25s ease;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    box-shadow: 0 9px 20px rgba(142, 68, 173, 0.20);
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-reset {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
}

.btn-reset:hover {
    background: #f4eaff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

/* CARD */
.card-box {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 26px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    overflow: hidden;
    margin-top: 28px;
    padding: 0;
}

/* PRINT HEADER */
.print-header {
    display: none;
}

.print-logo {
    width: 82px;
    height: 82px;
    object-fit: contain;
}

.print-company h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 900;
    color: #4b2e63;
    text-transform: uppercase;
}

.print-company p {
    margin: 2px 0;
    font-size: 12px;
    color: #333;
}

.print-line {
    border-top: 3px solid #4b2e63;
    border-bottom: 1px solid #4b2e63;
    height: 5px;
    margin: 12px 0 18px;
}

.print-report-title {
    text-align: center;
    margin: 18px 0 18px;
}

.print-report-title h3 {
    font-size: 18px;
    font-weight: 900;
    text-transform: uppercase;
    margin: 0 0 6px;
    color: #33223f;
}

.print-report-title p {
    margin: 0;
    font-size: 12px;
    color: #444;
}

/* PRINT SUMMARY */
.print-summary {
    display: none;
}

.print-summary table {
    width: 100%;
    margin-bottom: 16px;
    border-collapse: collapse;
    font-size: 12px;
}

.print-summary td {
    border: 1px solid #999;
    padding: 7px 10px;
}

.print-summary td:first-child {
    font-weight: bold;
    width: 35%;
    background: #f4eaff;
}

/* TABLE */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 15px 14px;
    font-size: 13px;
    font-weight: 850;
    text-transform: uppercase;
    text-align: center;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

.empty-data {
    text-align: center;
    padding: 28px;
    color: #8d7a9b;
}

/* SIGNATURE */
.print-signature {
    display: none;
}

.signature-box {
    width: 260px;
    float: right;
    text-align: center;
    font-size: 12px;
}

.signature-space {
    height: 70px;
}

.signature-name {
    font-weight: bold;
    border-bottom: 1px solid #333;
    display: inline-block;
    padding: 0 24px 2px;
}

/* FOOTER */
.print-footer {
    display: none;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 24px;
    }

    .page-title {
        font-size: 28px;
    }

    .summary-value {
        font-size: 24px;
    }
}

/* PRINT */
@media print {
    @page {
        size: A4 portrait;
        margin: 14mm;
    }

    body {
        background: white;
        color: #000;
        font-family: Arial, Helvetica, sans-serif;
    }

    .no-print,
    .sidebar,
    .page-header,
    .summary-card,
    .toolbar {
        display: none !important;
    }

    .main-content {
        margin-left: 0;
        padding: 0;
        min-height: auto;
    }

    .card-box {
        box-shadow: none;
        border: none;
        margin: 0;
        border-radius: 0;
        padding: 0;
        overflow: visible;
    }

    .print-header,
    .print-summary,
    .print-signature,
    .print-footer {
        display: block;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }

    .table thead th {
        background: #b57edc !important;
        color: white !important;
        border: 1px solid #333 !important;
        padding: 8px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table tbody td {
        border: 1px solid #333 !important;
        padding: 7px;
    }

    .table tbody tr:hover {
        background: transparent;
    }

    .print-footer {
        clear: both;
        margin-top: 130px;
        padding-top: 8px;
        border-top: 1px solid #999;
        font-size: 11px;
        color: #555;
        text-align: center;
    }
}
</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<main class="main-content">

    <!-- HEADER WEB -->
    <div class="page-header no-print">
        <div class="page-header-content">
            <div class="header-icon">
                <i class="fa-solid fa-warehouse"></i>
            </div>

            <h2 class="page-title">Laporan Stok Produk</h2>
            <p class="page-subtitle">
                Laporan data stok produk ready stock The Four Label.
            </p>
        </div>
    </div>

    <!-- SUMMARY WEB -->
    <div class="row g-4 no-print">

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-shirt"></i>
                    </div>

                    <p class="summary-label">Total Produk</p>
                    <h3 class="summary-value"><?= $totalProduk; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>

                    <p class="summary-label">Total Stok</p>
                    <h3 class="summary-value"><?= $totalStok; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <p class="summary-label">Stok Habis</p>
                    <h3 class="summary-value"><?= $totalHabis; ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- TOOLBAR WEB -->
    <div class="toolbar no-print">
        <div>
            <h4>Daftar Stok Produk</h4>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="laporan.php" class="btn-reset">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <button onclick="window.print()" class="btn-lavender">
                <i class="fa-solid fa-print"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- AREA LAPORAN -->
    <div class="card-box">

        <!-- KOP CETAK -->
        <div class="print-header">
            <table width="100%">
                <tr>
                    <td width="95" align="center">
                        <img src="../assets/logo.png" class="print-logo" alt="Logo The Four Label">
                    </td>

                    <td class="print-company">
                        <h2>THE FOUR LABEL</h2>
                        <p>Konveksi Custom & Ready Stock Apparel</p>
                        <p>Alamat: Tangerang, Banten</p>
                        <p>Email: thefourlabel@gmail.com | Telepon: 08xxxxxxxxxx</p>
                        <p>Instagram: @thefourlabel</p>
                    </td>
                </tr>
            </table>

            <div class="print-line"></div>

            <div class="print-report-title">
                <h3>Laporan Stok Produk</h3>
                <p>Tanggal Cetak: <?= date('d-m-Y'); ?></p>
            </div>
        </div>

        <!-- RINGKASAN CETAK -->
        <div class="print-summary">
            <table>
                <tr>
                    <td>Total Produk</td>
                    <td><?= $totalProduk; ?> produk</td>
                </tr>

                <tr>
                    <td>Total Stok</td>
                    <td><?= $totalStok; ?> stok</td>
                </tr>

                <tr>
                    <td>Stok Habis</td>
                    <td><?= $totalHabis; ?> produk</td>
                </tr>
            </table>
        </div>

        <!-- TABEL CETAK & WEB -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;

                    if (mysqli_num_rows($stok) > 0) {
                        while ($s = mysqli_fetch_assoc($stok)) { 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($s['namaProduk']); ?>
                            </td>

                            <td>
                                <?= $s['namaKategori'] ? htmlspecialchars($s['namaKategori']) : "-"; ?>
                            </td>

                            <td class="text-end">
                                Rp <?= number_format($s['harga'], 0, ',', '.'); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($s['jumlahStok']); ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($s['satuan']); ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="6" class="empty-data">
                                Belum ada data stok produk.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- TTD CETAK -->
        <div class="print-signature">
            <div class="signature-box">
                <p>Tangerang, <?= date('d-m-Y'); ?></p> 
                <p>Admin The Four Label</p>

                <div class="signature-space"></div>

                <p class="signature-name">
                    <?= htmlspecialchars($namaAdmin); ?>
                </p>
            </div>
        </div>

        <!-- FOOTER CETAK -->
        <div class="print-footer">
            Dokumen ini dicetak otomatis melalui Sistem Informasi The Four Label.
        </div>

    </div>

</main>

</body>
</html>