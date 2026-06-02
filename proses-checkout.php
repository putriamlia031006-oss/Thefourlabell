<?php
session_start();
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['user']['idUser'];

/* CEK CART */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Keranjang kosong");
}

/* ambil pelanggan */
$cari = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

$pelanggan = mysqli_fetch_assoc($cari);

/* total */
$total = $_POST['total'];

$metode = $_POST['metode'];

/* =========================
   1. CEK STOK DI stok_produk
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];
    $qty = $cart['qty'];

    $cek = mysqli_query(
        $koneksi,
        "SELECT jumlahStok FROM stok_produk WHERE idProduk='$idProduk'"
    );

    $stokData = mysqli_fetch_assoc($cek);

    if (!$stokData) {
        die("Stok produk tidak ditemukan");
    }

    if ($qty > $stokData['jumlahStok']) {
        die("Stok tidak cukup untuk salah satu produk");
    }
}

/* =========================
   2. SIMPAN PESANAN
========================= */
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

/* =========================
   3. SIMPAN DETAIL + KURANGI STOK
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];
    $qty = $cart['qty'];

    /* ambil stok */
    $cek = mysqli_query(
        $koneksi,
        "SELECT jumlahStok FROM stok_produk WHERE idProduk='$idProduk'"
    );

    $stokData = mysqli_fetch_assoc($cek);

    /* insert detail pesanan */
    mysqli_query(
        $koneksi,
        "INSERT INTO detail_pesanan (
            idPesanan,
            idProduk,
            qty
        ) VALUES (
            '$idPesanan',
            '$idProduk',
            '$qty'
        )"
    );

    /* UPDATE stok di tabel stok_produk */
    $stokBaru = $stokData['jumlahStok'] - $qty;

    mysqli_query(
        $koneksi,
        "UPDATE stok_produk 
         SET jumlahStok='$stokBaru' 
         WHERE idProduk='$idProduk'"
    );
}

/* =========================
   4. DP
========================= */
$dp = $total * 0.5;

/* =========================
   5. PEMBAYARAN
========================= */
/* =========================
   5. PEMBAYARAN
========================= */

if ($metode == "cash") {

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
            '$total',
            '$total',
            'Cash',
            'Lunas'
        )"
    );

} else {

    $dp = $total * 0.5;

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

}
/* =========================
   6. INVOICE
========================= */
$invoice = "INV-" . date("Ymd") . "-" . $idPesanan;

mysqli_query(
    $koneksi,
    "UPDATE pesanan 
     SET nomorInvoice='$invoice'
     WHERE idPesanan='$idPesanan'"
);

/* =========================
   7. CLEAR CART
========================= */
unset($_SESSION['cart']);

/* =========================
   8. REDIRECT
========================= */
if ($metode == "cash") {

    header("Location: invoice.php?id=$idPesanan");

} else {

    header("Location: upload-pembayaran.php?id=$idPesanan");

}

exit;
?>