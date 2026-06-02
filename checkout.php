<?php
session_start();
require "koneksi.php";

/* CEK CART */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<h3 class='text-center mt-5'>Keranjang masih kosong</h3>";
    exit;
}

$total = 0;
$jumlahOrder = 0;

/* HITUNG TOTAL */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM produk WHERE idProduk='$idProduk'"
    );

    $data = mysqli_fetch_assoc($query);

    if (!$data) continue;

    $subtotal = $data['harga'] * $cart['qty'];
    $total += $subtotal;
    $jumlahOrder += $cart['qty'];
}

/* DISKON */
$diskon = 0;

if ($jumlahOrder >= 5) {
    $diskon = $total * 0.20;
}

$totalAkhir = $total - $diskon;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family: 'Segoe UI', sans-serif;
}

.card-checkout{
    border:none;
    border-radius:18px;
    box-shadow:0 6px 20px rgba(0,0,0,.08);
}

.btn-lavender{
    background:#8b5cf6;
    color:white;
    border:none;
    border-radius:12px;
    padding:12px;
    font-weight:600;
}

.btn-lavender:hover{
    background:#7c3aed;
    color:white;
}

.label{
    font-weight:600;
    margin-bottom:6px;
}
</style>

</head>

<body>

<div class="container py-5">

<h2 class="mb-4">Checkout</h2>

<div class="card card-checkout">
<div class="card-body p-4">

<form action="proses-checkout.php" method="POST">

<!-- ALAMAT -->
<div class="mb-3">
    <label class="label">Alamat Pengiriman</label>
    <textarea name="alamat_kirim" class="form-control" required></textarea>
</div>

<!-- JASA KIRIM -->
<div class="mb-3">
    <label class="label">Jasa Pengiriman</label>
    <select name="jasa_kirim" class="form-select" required>
        <option value="JNE">JNE</option>
        <option value="JNT">J&T</option>
        <option value="SiCepat">SiCepat</option>
    </select>
</div>

<!-- ONGKIR -->
<div class="mb-3">
    <label class="label">Ongkir</label>
    <select name="ongkir" class="form-select" required>
        <option value="10000">Jabodetabek - Rp10.000</option>
        <option value="20000">Luar Kota - Rp20.000</option>
        <option value="30000">Luar Pulau - Rp30.000</option>
    </select>
</div>

<!-- METODE BAYAR -->
<div class="mb-3">
    <label class="label">Metode Pembayaran</label>

    <select name="metode" class="form-select" required>
        <option value="bca_transfer">Transfer Bank BCA</option>
        <option value="cash">Cash</option>
    </select>

    <small class="text-muted">
        Transfer ke rekening BCA: 1234567890 a.n. the four label
    </small>
</div>

<!-- RINGKASAN -->
<div class="mb-3">
    <h5>Ringkasan Pembayaran</h5>

    <p class="mb-1">Subtotal: <b>Rp <?= number_format($total); ?></b></p>
    <p class="mb-1 text-success">Diskon: <b>- Rp <?= number_format($diskon); ?></b></p>

    <h4 class="mt-2 text-primary">
        Total: Rp <?= number_format($totalAkhir); ?>
    </h4>
</div>

<!-- HIDDEN -->
<input type="hidden" name="total" value="<?= $totalAkhir ?>">

<!-- BUTTON -->
<button type="submit" class="btn btn-lavender w-100">
    Checkout Sekarang
</button>

</form>

</div>
</div>

</div>

</body>
</html>