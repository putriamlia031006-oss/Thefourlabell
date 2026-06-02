<?php
session_start();

/* Kalau keranjang belum ada */
if (!isset($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

/* Hapus semua isi keranjang */
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);

    header("Location: cart.php");
    exit;
}

/* Hapus 1 produk berdasarkan index */
if (isset($_GET['index'])) {

    $index = (int) $_GET['index'];

    if (isset($_SESSION['cart'][$index])) {

        unset($_SESSION['cart'][$index]);

        /* Rapikan ulang index array */
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

header("Location: cart.php");
exit;
?>