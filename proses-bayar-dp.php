<?php
session_start();
require "koneksi.php";

$idPesanan = $_GET['id'];
$jumlahDP = $_POST['jumlah'];

/* upload bukti */
$file = "";
if (!empty($_FILES['bukti']['name'])) {
    $file = time() . "_" . $_FILES['bukti']['name'];
    move_uploaded_file($_FILES['bukti']['tmp_name'], "upload/" . $file);
}

/* simpan pembayaran DP */
mysqli_query($koneksi,
"INSERT INTO pembayaran (
    idPesanan,
    jumlah,
    metode,
    status,
    bukti
) VALUES (
    '$idPesanan',
    '$jumlahDP',
    'Transfer BCA',
    'DP - Menunggu Verifikasi',
    '$file'
)");

/* update status pesanan */
mysqli_query($koneksi,
"UPDATE pesanan
SET status='DP'
WHERE idPesanan='$idPesanan'");

header("Location: pesanan-saya.php");
exit;
?>