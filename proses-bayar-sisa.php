<?php
require "koneksi.php";

$idPesanan = $_GET['id'];
$jumlah = $_POST['jumlah'];

$file = "";

if (!empty($_FILES['bukti']['name'])) {
    $file = time() . "_" . $_FILES['bukti']['name'];
    move_uploaded_file($_FILES['bukti']['tmp_name'], "upload/" . $file);
}

/* simpan pembayaran */
mysqli_query($koneksi,
"INSERT INTO pembayaran (
    idPesanan,
    jumlah,
    metode,
    status,
    bukti
) VALUES (
    '$idPesanan',
    '$jumlah',
    'Transfer BCA',
    'Pending',
    '$file'
)");

/* cek total bayar */
$q = mysqli_query($koneksi,
"SELECT SUM(jumlah) as totalBayar FROM pembayaran WHERE idPesanan='$idPesanan'");

$bayar = mysqli_fetch_assoc($q)['totalBayar'];

/* cek total pesanan */
$q2 = mysqli_query($koneksi,
"SELECT total FROM pesanan WHERE idPesanan='$idPesanan'");

$pesanan = mysqli_fetch_assoc($q2);

if ($bayar >= $pesanan['total']) {
    mysqli_query($koneksi,
    "UPDATE pesanan SET status='Lunas' WHERE idPesanan='$idPesanan'");
}

header("Location: pesanan-saya.php");
exit;
?>