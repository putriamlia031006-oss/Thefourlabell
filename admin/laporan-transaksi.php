<?php
session_start();
include "auth.php";
require "../koneksi.php";

/* =========================
   DATA LAPORAN TRANSAKSI
========================= */
$pesanan = mysqli_query($koneksi, "
    SELECT 
        pesanan.idPesanan,
        pesanan.nomorInvoice,
        pesanan.tanggal,
        pesanan.status,
        pesanan.jenisPesanan,
        pesanan.total,
        pesanan.ongkir,
        pesanan.alamat_kirim,
        pesanan.jasa_kirim,
        user.nama AS namaPelanggan,
        user.email,
        pelanggan.noHp,
        pelanggan.alamat
    FROM pesanan
    JOIN pelanggan
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user
        ON pelanggan.idUser = user.idUser
    ORDER BY pesanan.idPesanan DESC
");

if (!$pesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

/* =========================
   RINGKASAN
========================= */
$qTotalPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$totalPesanan = mysqli_fetch_assoc($qTotalPesanan)['total'];

$qTotalPendapatan = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total FROM pesanan");
$totalPendapatan = mysqli_fetch_assoc($qTotalPendapatan)['total'];

$qSelesai = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status='selesai'");
$totalSelesai = mysqli_fetch_assoc($qSelesai)['total'];

$qBatal = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status='batal'");
$totalBatal = mysqli_fetch_assoc($qBatal)['total'];

$namaAdmin = "Admin";

if (isset($_SESSION['user']['nama'])) {
    $namaAdmin = $_SESSION['user']['nama'];
} elseif (isset($_SESSION['nama'])) {
    $namaAdmin = $_SESSION['nama'];
}

/* =========================
   FUNCTION
========================= */
function statusClass($status) {
    $status = strtolower($status);

    if ($status == "selesai" || $status == "lunas") {
        return "status-selesai";
    } elseif ($status == "batal") {
        return "status-batal";
    } elseif ($status == "proses" || $status == "diproses") {
        return "status-proses";
    } else {
        return "status-pending";
    }
}

function jenisText($jenis) {
    if ($jenis == "custom") {
        return "Custom";
    } elseif ($jenis == "siap_pakai") {
        return "Siap Pakai";
    }

    return $jenis;
}

function jenisClass($jenis) {
    if ($jenis == "custom") {
        return "jenis-custom";
    }

    return "jenis-siap";
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Laporan Transaksi - The Four Label</title>

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

/* HEADER WEB */
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

/* SUMMARY WEB */
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
    white-space: nowrap;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

.text-small {
    font-size: 12px;
    color: #81758d;
}

/* BADGE */
.badge-status {
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 850;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.status-pending {
    background: #fff7ed;
    color: #c2410c;
}

.status-proses {
    background: #f1e3ff;
    color: #8e44ad;
}

.status-selesai {
    background: #ecfdf5;
    color: #047857;
}

.status-batal {
    background: #fef2f2;
    color: #b91c1c;
}

.jenis-custom {
    background: #f1e3ff;
    color: #8e44ad;
}

.jenis-siap {
    background: #f4eaff;
    color: #7b3fb2;
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
        size: A4 landscape;
        margin: 10mm;
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
        font-size: 10px;
        border-collapse: collapse;
    }

    .table thead th {
        background: #b57edc !important;
        color: white !important;
        border: 1px solid #333 !important;
        padding: 7px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table tbody td {
        border: 1px solid #333 !important;
        padding: 6px;
    }

    .table tbody tr:hover {
        background: transparent;
    }

    .badge-status {
        border: 1px solid #999;
        padding: 4px 7px;
        font-size: 10px;
    }

    .text-small {
        color: #333;
        font-size: 10px;
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
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>

            <h2 class="page-title">Laporan Transaksi</h2>
            <p class="page-subtitle">
                Laporan data transaksi pesanan pelanggan The Four Label.
            </p>
        </div>
    </div>

    <!-- SUMMARY WEB -->
    <div class="row g-4 no-print">

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>

                    <p class="summary-label">Total Pesanan</p>
                    <h3 class="summary-value"><?= $totalPesanan; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <p class="summary-label">Pesanan Selesai</p>
                    <h3 class="summary-value"><?= $totalSelesai; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>

                    <p class="summary-label">Pesanan Batal</p>
                    <h3 class="summary-value"><?= $totalBatal; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
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

    <!-- TOOLBAR WEB -->
    <div class="toolbar no-print">
        <div>
            <h4>Daftar Transaksi Pesanan</h4>
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
                <h3>Laporan Transaksi</h3>
                <p>Tanggal Cetak: <?= date('d-m-Y'); ?></p>
            </div>
        </div>

        <!-- RINGKASAN CETAK -->
        <div class="print-summary">
            <table>
                <tr>
                    <td>Total Pesanan</td>
                    <td><?= $totalPesanan; ?> pesanan</td>
                </tr>

                <tr>
                    <td>Pesanan Selesai</td>
                    <td><?= $totalSelesai; ?> pesanan</td>
                </tr>

                <tr>
                    <td>Pesanan Batal</td>
                    <td><?= $totalBatal; ?> pesanan</td>
                </tr>

                <tr>
                    <td>Total Pendapatan</td>
                    <td>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <!-- TABEL CETAK & WEB -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Kontak</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Pengiriman</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;

                    if (mysqli_num_rows($pesanan) > 0) {
                        while ($p = mysqli_fetch_assoc($pesanan)) { 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>

                            <td class="text-center">
                                <?php if ($p['nomorInvoice'] != "") { ?>
                                    <?= htmlspecialchars($p['nomorInvoice']); ?>
                                <?php } else { ?>
                                    #<?= htmlspecialchars($p['idPesanan']); ?>
                                <?php } ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($p['namaPelanggan']); ?></strong>
                                <div class="text-small">
                                    <?= htmlspecialchars($p['email']); ?>
                                </div>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['noHp']); ?>
                                <div class="text-small">
                                    <?= htmlspecialchars($p['alamat']); ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <?= formatTanggal($p['tanggal']); ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-status <?= jenisClass($p['jenisPesanan']); ?>">
                                    <?= htmlspecialchars(jenisText($p['jenisPesanan'])); ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-status <?= statusClass($p['status']); ?>">
                                    <?= htmlspecialchars($p['status']); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($p['jasa_kirim'] != "") { ?>
                                    <strong><?= htmlspecialchars($p['jasa_kirim']); ?></strong>
                                <?php } else { ?>
                                    <strong>Ambil di tempat</strong>
                                <?php } ?>

                                <div class="text-small">
                                    <?= $p['alamat_kirim'] ? htmlspecialchars($p['alamat_kirim']) : "-"; ?>
                                </div>

                                <div class="text-small">
                                    Ongkir: Rp <?= number_format($p['ongkir'], 0, ',', '.'); ?>
                                </div>
                            </td>

                            <td class="text-end">
                                <strong>
                                    Rp <?= number_format($p['total'], 0, ',', '.'); ?>
                                </strong>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="9" class="empty-data">
                                Belum ada data transaksi.
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