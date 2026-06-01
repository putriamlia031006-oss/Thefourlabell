<?php
require "koneksi.php";

$idPesanan = $_GET['id'];

/* ambil total pesanan */
$q = mysqli_query($koneksi,
"SELECT total FROM pesanan WHERE idPesanan='$idPesanan'");
$pesanan = mysqli_fetch_assoc($q);

$totalPesanan = $pesanan['total'];

/* ambil total pembayaran */
$q2 = mysqli_query($koneksi,
"SELECT SUM(jumlah) as totalBayar 
FROM pembayaran 
WHERE idPesanan='$idPesanan'");

$bayar = mysqli_fetch_assoc($q2)['totalBayar'] ?? 0;

/* hitung sisa */
$sisa = $totalPesanan - $bayar;
?>

<!DOCTYPE html>
<html>
<head>
<title>Bayar Sisa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-5">

<div class="card p-4" style="max-width:500px;margin:auto;">

<h4>Bayar Sisa Pembayaran</h4>

<div class="alert alert-info">
    Sisa pembayaran Anda: <b>Rp <?= number_format($sisa); ?></b>
</div>

<form action="proses-bayar-sisa.php?id=<?= $idPesanan; ?>" method="POST" enctype="multipart/form-data">

<!-- AUTO FILL JUMLAH -->
<div class="mb-3">
    <label>Jumlah Bayar (Otomatis)</label>
    <input type="number" name="jumlah" class="form-control" value="<?= $sisa; ?>" readonly>
</div>

<div class="mb-3">
    <label>Bukti Transfer</label>
    <input type="file" name="bukti" class="form-control" required>
</div>

<button class="btn btn-primary w-100">
    Bayar Sekarang
</button>

</form>

</div>

</body>
</html>