<?php
session_start();

require "koneksi.php";

if (!isset($_POST['idProduk']) || !isset($_POST['qty'])) {
    header("Location: produk.php");
    exit;
}

$idProduk = $_POST['idProduk'];
$qty = $_POST['qty'];

if ($qty < 1) {
    $qty = 1;
}

/* CEK PRODUK */
$qProduk = mysqli_query(
    $koneksi,
    "SELECT 
        p.*,
        s.jumlahStok
    FROM produk p
    LEFT JOIN stok_produk s
        ON p.idProduk = s.idProduk
    WHERE p.idProduk='$idProduk'"
);

if (!$qProduk) {
    die("Query produk error: " . mysqli_error($koneksi));
}

$produk = mysqli_fetch_assoc($qProduk);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

$stok = isset($produk['jumlahStok']) ? $produk['jumlahStok'] : 0;

if ($qty > $stok) {
    die("Jumlah melebihi stok yang tersedia.");
}

/* BUAT SESSION CART JIKA BELUM ADA */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* CEK APA PRODUK SUDAH ADA DI CART */
$produkSudahAda = false;

foreach ($_SESSION['cart'] as $key => $item) {

    if ($item['idProduk'] == $idProduk) {

        $qtyBaru = $_SESSION['cart'][$key]['qty'] + $qty;

        if ($qtyBaru > $stok) {
            die("Jumlah di keranjang melebihi stok yang tersedia.");
        }

        $_SESSION['cart'][$key]['qty'] = $qtyBaru;
        $produkSudahAda = true;
        break;
    }
}

/* KALAU PRODUK BELUM ADA, MASUKKAN */
if (!$produkSudahAda) {
    $_SESSION['cart'][] = [
        'idProduk' => $produk['idProduk'],
        'namaProduk' => $produk['namaProduk'],
        'harga' => $produk['harga'],
        'gambar' => $produk['gambar'],
        'qty' => $qty
    ];
}

header("Location: cart.php");
exit;
?>