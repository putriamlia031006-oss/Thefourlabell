<?php

session_start();

require "koneksi.php";

if(!isset($_GET['id'])){
    die("ID Pesanan tidak ditemukan");
}

$id = $_GET['id'];

$query = mysqli_query(

    $koneksi,

    "SELECT 
        p.*,
        pl.nama,
        pl.alamat,
        py.metode,
        py.status as statusPembayaran

    FROM pesanan p

    LEFT JOIN pelanggan pl
    ON p.idPelanggan = pl.idPelanggan

    LEFT JOIN pembayaran py
    ON p.idPesanan = py.idPesanan

    WHERE p.idPesanan = '$id'"
);

$data = mysqli_fetch_assoc($query);

if(!$data){
    die("Data tidak ditemukan");
}

$detail = mysqli_query(

    $koneksi,

    "SELECT *

    FROM detail_pesanan

    WHERE idPesanan='$id'"

);

?>

<!DOCTYPE html>

<html>

<head>

<title>Invoice</title>

<link href=
"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

.invoice-box{

max-width:900px;

margin:auto;

padding:30px;

border:1px solid #ddd;

background:white;

}

</style>

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="invoice-box">

<h2>

INVOICE

</h2>

<hr>

<b>No Invoice :</b>

<?= $data['nomorInvoice']; ?>

<br>

<b>Tanggal :</b>

<?= $data['tanggal']; ?>

<br>

<b>Status :</b>

<?= $data['status']; ?>

<br>

<b>Pembayaran :</b>

<?= $data['statusPembayaran']; ?>

<hr>

<h5>Customer</h5>

Nama :

<?= $data['nama']; ?>

<br>

Alamat :

<?= $data['alamat']; ?>

<hr>

<table class="table table-bordered">

<tr>

<th>Produk</th>

<th>Ukuran</th>

<th>Qty</th>

<th>Jenis</th>

</tr>

<?php

while(
$row=
mysqli_fetch_assoc(
$detail
))

{

?>

<tr>

<td>

<?= $row['jenis']; ?>

</td>

<td>

<?= $row['ukuran']; ?>

</td>

<td>

<?= $row['qty']; ?>

</td>

<td>

<?= $data['jenisPesanan']; ?>

</td>

</tr>

<?php } ?>

</table>

<div class="text-end">

<h4>

Total :

Rp

<?= number_format(
$data['total']
); ?>

Diskon :
Rp <?= number_format($diskon); ?>

</h4>

<p>

Ongkir :

Rp

<?= number_format(
$data['ongkir']
); ?>

</p>

<h3>

Grand Total :

Rp

<?= number_format(

$data['total']
+
$data['ongkir']

); ?>

</h3>

</div>

<button
onclick="window.print()"

class="btn btn-success">

Cetak Invoice

</button>

<a
href="pesanan-saya.php"

class="btn btn-secondary">

Kembali

</a>

</div>

</div>

</body>

</html>