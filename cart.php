<?php
session_start();
require "koneksi.php";
include "navbar.php";

$total = 0;


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Keranjang</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family: 'Segoe UI', sans-serif;
}

.title{
    font-weight:700;
    color:#5b3cc4;
}

.card-cart{
    border:none;
    border-radius:18px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

.table th{
    font-weight:600;
    color:#555;
}

.btn-lavender{
    background:#8b5cf6;
    color:white;
    border:none;
    border-radius:10px;
    padding:8px 16px;
}

.btn-lavender:hover{
    background:#7c3aed;
    color:white;
}

.badge-empty{
    background:#eee;
    color:#666;
    padding:10px 15px;
    border-radius:10px;
}
</style>

</head>

<body>

<div class="container py-5">

<h2 class="title mb-4">Keranjang Belanja</h2>

<div class="card card-cart">
<div class="card-body p-4">

<?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) { ?>

    <div class="text-center py-5">
        <div class="badge-empty">
            Keranjang masih kosong
        </div>
    </div>

<?php } else { ?>

<table class="table align-middle">
<thead>
<tr>
    <th>Produk</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach ($_SESSION['cart'] as $index => $cart) { ?>

<?php
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk WHERE idProduk='{$cart['idProduk']}'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) continue;

$sub = $data['harga'] * $cart['qty'];
$total += $sub;
?>

<tr>
    <td><?= $data['namaProduk']; ?></td>
    <td>Rp <?= number_format($data['harga']); ?></td>
    <td><?= $cart['qty']; ?></td>
    <td>Rp <?= number_format($sub); ?></td>
    <td>
        <a 
            href="hapus-cart.php?index=<?= $index; ?>" 
            class="btn btn-danger btn-sm"
            onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
            Hapus
        </a>
    </td>
</tr>

<?php } ?>

</tbody>
</table>

<hr>

<div class="d-flex justify-content-between align-items-center">

    <h4 class="mb-0">
        Total: <span class="text-primary">Rp <?= number_format($total); ?></span>
    </h4>

    <a href="checkout.php" class="btn btn-lavender">
        Checkout
    </a>

</div>

<?php } ?>

</div>
</div>

</div>

<?php include "footer.php"; ?>

</body>
</html>