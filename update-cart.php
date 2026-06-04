<?php
session_start();

if (isset($_GET['index']) && isset($_GET['qty'])) {

    $index = $_GET['index'];
    $qty = (int) $_GET['qty'];

    if ($qty < 1) {
        $qty = 1;
    }

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['qty'] = $qty;
    }
}

header("Location: cart.php");
exit;
?>