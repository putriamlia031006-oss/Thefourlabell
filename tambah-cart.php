<?php
session_start();

require "koneksi.php";

/* AMBIL ID PRODUK DARI POST / GET */
if (isset($_POST['idProduk'])) {
    $idProduk = $_POST['idProduk'];
} elseif (isset($_GET['id'])) {
    $idProduk = $_GET['id'];
} elseif (isset($_GET['idProduk'])) {
    $idProduk = $_GET['idProduk'];
} else {
    die("ID Produk tidak ditemukan.");
}

/* AMBIL QTY */
if (isset($_POST['qty'])) {
    $qty = (int) $_POST['qty'];
} else {
    $qty = 1;
}

if ($qty < 1) {
    $qty = 1;
}

$idProduk = mysqli_real_escape_string($koneksi, $idProduk);

/* CEK PRODUK */
$qProduk = mysqli_query(
    $koneksi,
    "SELECT 
        p.*,
        COALESCE(s.jumlahStok, 0) AS jumlahStok
    FROM produk p
    LEFT JOIN stok_produk s
        ON p.idProduk = s.idProduk
    WHERE p.idProduk='$idProduk'
    LIMIT 1"
);

if (!$qProduk) {
    die("Query produk error: " . mysqli_error($koneksi));
}

$produk = mysqli_fetch_assoc($qProduk);

if (!$produk) {
    die("Produk tidak ditemukan.");
}

$stok = (int) $produk['jumlahStok'];

if ($stok <= 0) {
    die("Stok produk sedang kosong.");
}

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