<?php
session_start();

include "auth-pelanggan.php";
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID pesanan tidak ditemukan.');
        window.location='pesanan-saya.php';
    </script>";
    exit;
}

$idPesanan = mysqli_real_escape_string($koneksi, $_GET['id']);
$idUser = $_SESSION['user']['idUser'];

/* AMBIL DATA PESANAN MILIK USER LOGIN */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        pelanggan.noHp,
        pelanggan.alamat,
        user.nama AS namaPelanggan,
        user.email
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    WHERE pesanan.idPesanan = '$idPesanan'
    AND pelanggan.idUser = '$idUser'
    LIMIT 1"
);

if (!$query) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    echo "<script>
        alert('Data pesanan tidak ditemukan.');
        window.location='pesanan-saya.php';
    </script>";
    exit;
}

/* AMBIL PEMBAYARAN VALID TERAKHIR */
$qPembayaran = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pembayaran
     WHERE idPesanan = '$idPesanan'
     AND status IN ('DP Masuk', 'Lunas')
     ORDER BY idPembayaran DESC
     LIMIT 1"
);

if (!$qPembayaran) {
    die("Query pembayaran error: " . mysqli_error($koneksi));
}

$pembayaran = mysqli_fetch_assoc($qPembayaran);

if (!$pembayaran) {
    echo "<script>
        alert('Kwitansi belum tersedia karena pembayaran belum diverifikasi admin.');
        window.location='pesanan-saya.php';
    </script>";
    exit;
}

/* TOTAL BAYAR VALID */
$qTotalBayar = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar
     FROM pembayaran
     WHERE idPesanan = '$idPesanan'
     AND status IN ('DP Masuk', 'Lunas')"
);

$totalBayar = mysqli_fetch_assoc($qTotalBayar)['totalBayar'];

$totalPesanan = (int) $pesanan['total'];
$totalBayar = (int) $totalBayar;

$sisa = $totalPesanan - $totalBayar;

if ($sisa < 0) {
    $sisa = 0;
}

$statusKwitansi = "DP / Belum Lunas";

if ($totalBayar >= $totalPesanan) {
    $statusKwitansi = "Lunas";
}

$nomorInvoice = $pesanan['nomorInvoice'];

if ($nomorInvoice == "" || $nomorInvoice == NULL) {
    $nomorInvoice = "INV-" . str_pad($pesanan['idPesanan'], 5, "0", STR_PAD_LEFT);
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}

function jenisPesananText($jenis) {
    if ($jenis == "siap_pakai") {
        return "Siap Pakai";
    } elseif ($jenis == "custom") {
        return "Custom";
    }

    return $jenis;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Lihat Kwitansi - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
    padding: 35px 15px;
}

.kwitansi-box {
    max-width: 720px;
    margin: auto;
    background: white;
    border-radius: 26px;
    border: 1px solid #eadcff;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.14);
    overflow: hidden;
}

.header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 26px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.logo-box {
    width: 72px;
    height: 72px;
    border-radius: 18px;
    background: white;
    padding: 5px;
    flex-shrink: 0;
}

.logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.header h3 {
    margin: 0;
    font-weight: 900;
}

.header p {
    margin: 3px 0 0;
    opacity: .95;
}

.content {
    padding: 28px;
}

.title {
    text-align: center;
    margin-bottom: 24px;
}

.title h4 {
    color: #6f2da8;
    font-weight: 900;
    text-decoration: underline;
    margin-bottom: 6px;
}

.title p {
    color: #777;
    margin: 0;
    font-size: 14px;
}

.info-table {
    width: 100%;
    margin-bottom: 20px;
}

.info-table td {
    padding: 8px 4px;
    vertical-align: top;
    font-size: 14px;
}

.info-table td:first-child {
    width: 180px;
    font-weight: 800;
    color: #4b2e63;
}

.status {
    display: inline-block;
    padding: 7px 13px;
    border-radius: 999px;
    background: #f1e3ff;
    color: #7b3fb2;
    font-weight: 850;
    border: 1px solid #d9c0f0;
}

.total-box {
    border: 1px solid #eadcff;
    border-radius: 18px;
    overflow: hidden;
    margin-top: 18px;
}

.total-row {
    display: flex;
    border-bottom: 1px solid #eadcff;
}

.total-row:last-child {
    border-bottom: none;
}

.total-label {
    width: 45%;
    padding: 12px;
    background: #f4eaff;
    color: #4b2e63;
    font-weight: 850;
}

.total-value {
    width: 55%;
    padding: 12px;
    text-align: right;
    font-weight: 850;
}

.note {
    margin-top: 18px;
    background: #fbf7ff;
    border: 1px solid #eadcff;
    border-radius: 16px;
    padding: 14px;
    color: #5f526a;
    font-size: 14px;
    line-height: 1.6;
}

.action {
    text-align: center;
    margin-top: 24px;
}

.btn-back {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 14px;
    padding: 10px 18px;
    text-decoration: none;
    font-weight: 850;
    display: inline-block;
}

.btn-back:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
}
</style>
</head>

<body>

<div class="kwitansi-box">

    <div class="header">
        <div class="logo-box">
            <img src="assets/logo.png" alt="Logo The Four Label">
        </div>

        <div>
            <h3>THE FOUR LABEL</h3>
            <p>Kwitansi Pembayaran</p>
        </div>
    </div>

    <div class="content">

        <div class="title">
            <h4>KWITANSI PEMBAYARAN</h4>
            <p>No. Kwitansi: KW-<?= htmlspecialchars($pesanan['idPesanan']); ?>-<?= date('Ymd'); ?></p>
        </div>

        <table class="info-table">
            <tr>
                <td>No Invoice</td>
                <td>: <?= htmlspecialchars($nomorInvoice); ?></td>
            </tr>

            <tr>
                <td>Tanggal Pesanan</td>
                <td>: <?= formatTanggal($pesanan['tanggal']); ?></td>
            </tr>

            <tr>
                <td>Nama Pelanggan</td>
                <td>: <?= htmlspecialchars($pesanan['namaPelanggan']); ?></td>
            </tr>

            <tr>
                <td>No HP</td>
                <td>: <?= htmlspecialchars($pesanan['noHp']); ?></td>
            </tr>

            <tr>
                <td>Jenis Pesanan</td>
                <td>: <?= htmlspecialchars(jenisPesananText($pesanan['jenisPesanan'])); ?></td>
            </tr>

            <tr>
                <td>Metode Pembayaran</td>
                <td>: <?= htmlspecialchars($pembayaran['metode']); ?></td>
            </tr>

            <tr>
                <td>Tanggal Pembayaran</td>
                <td>
                    : 
                    <?php
                    if (isset($pembayaran['tanggal']) && $pembayaran['tanggal'] != "") {
                        echo formatTanggal($pembayaran['tanggal']);
                    } else {
                        echo date('d-m-Y');
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <td>Status Pembayaran</td>
                <td>: <span class="status"><?= htmlspecialchars($statusKwitansi); ?></span></td>
            </tr>
        </table>

        <div class="total-box">
            <div class="total-row">
                <div class="total-label">Total Pesanan</div>
                <div class="total-value">
                    Rp <?= number_format($totalPesanan, 0, ',', '.'); ?>
                </div>
            </div>

            <div class="total-row">
                <div class="total-label">Total Dibayar</div>
                <div class="total-value">
                    Rp <?= number_format($totalBayar, 0, ',', '.'); ?>
                </div>
            </div>

            <div class="total-row">
                <div class="total-label">Sisa Pembayaran</div>
                <div class="total-value">
                    Rp <?= number_format($sisa, 0, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="note">
            Kwitansi ini tersedia setelah pembayaran diverifikasi oleh admin.
            <?php if ($statusKwitansi == "DP / Belum Lunas") { ?>
                Pembayaran masih berstatus DP dan belum lunas.
            <?php } else { ?>
                Pembayaran telah dinyatakan lunas.
            <?php } ?>
        </div>

        <div class="action">
            <a href="pesanan-saya.php" class="btn-back">
                Kembali ke Pesanan Saya
            </a>
        </div>

    </div>

</div>

</body>
</html>