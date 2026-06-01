<?php
session_start();

/* validasi id produk */
$idProduk = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idProduk) {
    exit("ID produk tidak valid");
}

/* inisialisasi cart */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;

/* cek apakah produk sudah ada di cart */
foreach ($_SESSION['cart'] as $key => $item) {

    if ($item['idProduk'] == $idProduk) {

        $_SESSION['cart'][$key]['qty'] += 1;
        $found = true;
        break;
    }
}

/* jika belum ada, tambah baru */
if (!$found) {
    $_SESSION['cart'][] = [
        'idProduk' => $idProduk,
        'qty' => 1
    ];
}

/* redirect (lebih UX daripada echo angka) */
header("Location: cart.php");
exit;
?>