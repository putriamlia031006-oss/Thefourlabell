<?php
session_start();

require "auth.php";
require "../koneksi.php";

/* =========================
   CEK ID PESANAN
========================= */
if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID pesanan tidak ditemukan.');
        window.location='pesanan.php';
    </script>";
    exit;
}

$idPesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   AMBIL DATA PESANAN
========================= */
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
    LIMIT 1"
);

if (!$query) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    echo "<script>
        alert('Data pesanan tidak ditemukan.');
        window.location='pesanan.php';
    </script>";
    exit;
}

/* =========================
   AMBIL PEMBAYARAN VALID
   HANYA DP MASUK / LUNAS
========================= */
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
        alert('Kwitansi belum bisa dicetak karena pembayaran belum diverifikasi.');
        window.location='pesanan.php';
    </script>";
    exit;
}

/* =========================
   HITUNG TOTAL BAYAR VALID
========================= */
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

if ($totalBayar >= $totalPesanan) {
    $statusKwitansi = "Lunas";
} else {
    $statusKwitansi = "DP / Belum Lunas";
}

/* =========================
   NOMOR INVOICE
========================= */
$nomorInvoice = $pesanan['nomorInvoice'];

if ($nomorInvoice == "" || $nomorInvoice == NULL) {
    $nomorInvoice = "INV-" . str_pad($pesanan['idPesanan'], 5, "0", STR_PAD_LEFT);
}

/* =========================
   ADMIN
========================= */
$namaAdmin = "Admin";

if (isset($_SESSION['user']['nama'])) {
    $namaAdmin = $_SESSION['user']['nama'];
} elseif (isset($_SESSION['nama'])) {
    $namaAdmin = $_SESSION['nama'];
}

/* =========================
   FUNCTION
========================= */
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

<title>Kwitansi <?= htmlspecialchars($nomorInvoice); ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body {
    background: #fbf7ff;
    font-family: Arial, Helvetica, sans-serif;
    color: #222;
    padding: 28px;
}

.no-print {
    text-align: center;
    margin-bottom: 18px;
}

.btn-print,
.btn-back {
    border-radius: 12px;
    padding: 10px 17px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 4px;
}

.btn-print {
    background: #8e44ad;
    color: white;
    border: none;
}

.btn-print:hover {
    background: #7b3fb2;
    color: white;
}

.btn-back {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
}

.btn-back:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.kwitansi-box {
    max-width: 720px;
    margin: auto;
    background: white;
    border: 1px solid #ddd;
    padding: 30px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.12);
}

.header {
    display: flex;
    align-items: center;
    gap: 16px;
    border-bottom: 3px solid #4b2e63;
    padding-bottom: 14px;
    margin-bottom: 22px;
}

.logo {
    width: 78px;
    height: 78px;
    object-fit: contain;
}

.company h2 {
    margin: 0;
    color: #4b2e63;
    font-size: 24px;
    font-weight: 900;
}

.company p {
    margin: 2px 0;
    font-size: 13px;
    color: #555;
}

.title {
    text-align: center;
    margin-bottom: 22px;
}

.title h3 {
    font-size: 20px;
    font-weight: 900;
    text-decoration: underline;
    margin-bottom: 5px;
    color: #33223f;
}

.title p {
    margin: 0;
    font-size: 13px;
    color: #555;
}

.info-table {
    width: 100%;
    margin-bottom: 18px;
}

.info-table td {
    padding: 7px 4px;
    font-size: 14px;
    vertical-align: top;
}

.info-table td:first-child {
    width: 180px;
    font-weight: bold;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: #f1e3ff;
    color: #7b3fb2;
    border: 1px solid #d9c0f0;
    font-weight: bold;
}

.total-box {
    border: 1px solid #333;
    margin-top: 14px;
}

.total-row {
    display: flex;
    border-bottom: 1px solid #333;
}

.total-row:last-child {
    border-bottom: none;
}

.total-label {
    width: 45%;
    padding: 10px;
    font-weight: bold;
    background: #f4eaff;
    border-right: 1px solid #333;
}

.total-value {
    width: 55%;
    padding: 10px;
    text-align: right;
    font-weight: bold;
}

.note {
    margin-top: 18px;
    background: #fbf7ff;
    border: 1px solid #eadcff;
    padding: 12px;
    border-radius: 10px;
    font-size: 13px;
    line-height: 1.5;
}

.signature {
    margin-top: 45px;
    display: flex;
    justify-content: flex-end;
}

.signature-box {
    width: 230px;
    text-align: center;
    font-size: 14px;
}

.signature-space {
    height: 65px;
}

.signature-name {
    font-weight: bold;
    border-bottom: 1px solid #333;
    display: inline-block;
    padding: 0 24px 3px;
}

.footer {
    margin-top: 28px;
    border-top: 1px solid #aaa;
    padding-top: 8px;
    text-align: center;
    font-size: 12px;
    color: #555;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        background: white;
        padding: 0;
    }

    .no-print {
        display: none !important;
    }

    .kwitansi-box {
        max-width: 100%;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .total-label {
        background: #f4eaff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .note {
        background: #fbf7ff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>

</head>

<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i>
        Cetak
    </button>

    <a href="pesanan.php" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali
    </a>
</div>

<div class="kwitansi-box">

    <div class="header">
        <img src="../assets/logo.png" class="logo" alt="Logo The Four Label">

        <div class="company">
            <h2>THE FOUR LABEL</h2>
            <p>Konveksi Custom & Ready Stock Apparel</p>
            <p>Tangerang, Banten</p>
            <p>Instagram: @thefourlabel</p>
        </div>
    </div>

    <div class="title">
        <h3>KWITANSI PEMBAYARAN</h3>
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
        Kwitansi ini diterbitkan setelah pembayaran diverifikasi oleh admin.
        <?php if ($statusKwitansi == "DP / Belum Lunas") { ?>
            Status pembayaran masih DP dan belum lunas.
        <?php } else { ?>
            Pembayaran telah dinyatakan lunas.
        <?php } ?>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>Tangerang, <?= date('d-m-Y'); ?></p>
            <p>Admin The Four Label</p>

            <div class="signature-space"></div>

            <p class="signature-name">
                <?= htmlspecialchars($namaAdmin); ?>
            </p>
        </div>
    </div>

    <div class="footer">
        Kwitansi ini dicetak otomatis melalui Sistem Informasi The Four Label.
    </div>

</div>

</body>
</html>