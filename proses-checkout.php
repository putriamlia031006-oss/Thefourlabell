<?php
session_start();
require "koneksi.php";

$idUser = $_SESSION['user']['idUser'];

/* ambil data pelanggan */
$cari = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

$pelanggan = mysqli_fetch_assoc($cari);

/* ambil total & hitung DP */
$total = $_POST['total'];
$dp = $total * 0.5;

/* simpan pesanan */
mysqli_query(
    $koneksi,
    "INSERT INTO pesanan (
        idPelanggan,
        tanggal,
        status,
        jenisPesanan,
        total
    ) VALUES (
        '$pelanggan[idPelanggan]',
        NOW(),
        'Menunggu',
        'siap_pakai',
        '$total'
    )"
);

$idPesanan = mysqli_insert_id($koneksi);

/* simpan detail pesanan dari cart */
foreach ($_SESSION['cart'] as $cart) {

    mysqli_query(
        $koneksi,
        "INSERT INTO detail_pesanan (
            idPesanan,
            idProduk,
            qty
        ) VALUES (
            '$idPesanan',
            '$cart[idProduk]',
            '$cart[qty]'
        )"
    );
}

/* simpan pembayaran (dipindah ke luar loop biar tidak dobel) */
mysqli_query(
    $koneksi,
    "INSERT INTO pembayaran (
        idPesanan,
        jumlah,
        dp,
        metode,
        status
    ) VALUES (
        '$idPesanan',
        '$dp',
        '$dp',
        'Transfer',
        'DP Masuk'
    )"
);

/* buat invoice */
$invoice = "INV-" . date("Ymd") . "-" . $idPesanan;

/* update invoice */
mysqli_query(
    $koneksi,
    "UPDATE pesanan 
    SET nomorInvoice='$invoice'
    WHERE idPesanan='$idPesanan'"
);

/* kosongkan cart */
unset($_SESSION['cart']);

/* redirect */
header("Location: upload-pembayaran.php?id=$idPesanan");
exit;
?>