<?php
session_start();
require "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: pesanan-saya.php");
    exit;
}

$idPesanan = $_GET['id'];

$q = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        user.nama AS namaPelanggan,
        user.email,
        pelanggan.noHp,
        pelanggan.alamat,
        pembayaran.jumlah,
        pembayaran.metode,
        pembayaran.status AS statusPembayaran
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    JOIN pembayaran 
        ON pesanan.idPesanan = pembayaran.idPesanan
    WHERE pesanan.idPesanan='$idPesanan'
    ORDER BY pembayaran.idPembayaran DESC
    LIMIT 1"
);

if (!$q) {
    die("Query kwitansi error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Data kwitansi tidak ditemukan.");
}

$sisa = $data['total'] - $data['jumlah'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Kwitansi Tunai</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f6f0ff;
    font-family: Arial, sans-serif;
    color: #222;
}

.wrapper {
    max-width: 820px;
    margin: 45px auto;
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.14);
}

.header {
    text-align: center;
    border-bottom: 2px solid #8e44ad;
    padding-bottom: 18px;
    margin-bottom: 25px;
}

.header h2 {
    color: #7b3fb2;
    font-weight: 800;
    margin-bottom: 5px;
}

.header p {
    margin: 0;
    color: #666;
}

.title {
    text-align: center;
    margin: 25px 0;
    font-weight: 800;
    color: #333;
    text-decoration: underline;
}

.info-table td {
    padding: 8px 4px;
    vertical-align: top;
}

.total-box {
    background: #f6eeff;
    border: 1px solid #eadcff;
    border-radius: 18px;
    padding: 20px;
    margin-top: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.total-row:last-child {
    margin-bottom: 0;
}

.total-row strong {
    color: #7b3fb2;
}

.ttd {
    margin-top: 55px;
    display: flex;
    justify-content: space-between;
    gap: 30px;
}

.ttd-box {
    text-align: center;
    width: 230px;
}

.ttd-space {
    height: 70px;
}

.btn-print {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 700;
}

.btn-back {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 700;
    text-decoration: none;
}

@media print {
    body {
        background: white;
    }

    .wrapper {
        box-shadow: none;
        margin: 0;
        max-width: 100%;
        border-radius: 0;
    }

    .no-print {
        display: none !important;
    }
}
</style>
</head>

<body>

<div class="wrapper">

    <div class="header">
        <h2>THE FOUR LABEL</h2>
        <p>Konveksi Custom & Ready Stock</p>
        <p>Email: thefourlabel@gmail.com | Telp: 0812-xxxx-xxxx</p>
    </div>

    <h4 class="title">KWITANSI PEMBAYARAN TUNAI</h4>

    <table class="table table-borderless info-table">
        <tr>
            <td width="190">No. Invoice</td>
            <td width="20">:</td>
            <td>
                <?= $data['nomorInvoice'] ? htmlspecialchars($data['nomorInvoice']) : "#" . $idPesanan; ?>
            </td>
        </tr>

        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?= date('d-m-Y'); ?></td>
        </tr>

        <tr>
            <td>Nama Pelanggan</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['namaPelanggan']); ?></td>
        </tr>

        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['noHp']); ?></td>
        </tr>

        <tr>
            <td>Metode Pembayaran</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['metode']); ?></td>
        </tr>

        <tr>
            <td>Status Pembayaran</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['statusPembayaran']); ?></td>
        </tr>
    </table>

    <div class="total-box">

        <div class="total-row">
            <span>Total Pesanan</span>
            <strong>Rp <?= number_format($data['total'], 0, ',', '.'); ?></strong>
        </div>

        <div class="total-row">
            <span>Jumlah Dibayar</span>
            <strong>Rp <?= number_format($data['jumlah'], 0, ',', '.'); ?></strong>
        </div>

        <div class="total-row">
            <span>Sisa Pembayaran</span>
            <strong>Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
        </div>

    </div>

    <p class="mt-4">
        Telah diterima pembayaran tunai dari pelanggan untuk pesanan di atas.
        Kwitansi ini dapat digunakan sebagai bukti pembayaran tunai.
    </p>

    <div class="ttd">

        <div class="ttd-box">
            <p>Pelanggan</p>
            <div class="ttd-space"></div>
            <p><b><?= htmlspecialchars($data['namaPelanggan']); ?></b></p>
        </div>

        <div class="ttd-box">
            <p>Admin The Four Label</p>
            <div class="ttd-space"></div>
            <p><b>________________</b></p>
        </div>

    </div>

    <div class="d-flex justify-content-between mt-4 no-print">
        <a href="pesanan-saya.php" class="btn-back">
            Kembali
        </a>

        <button onclick="window.print()" class="btn-print">
            Cetak Kwitansi
        </button>
    </div>

</div>

</body>
</html>