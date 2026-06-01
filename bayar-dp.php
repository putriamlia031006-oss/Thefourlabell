<?php
session_start();
require "koneksi.php";

$idPesanan = $_GET['id'];

/* ambil data pesanan */
$q = mysqli_query($koneksi,
"SELECT * FROM pesanan WHERE idPesanan='$idPesanan'");
$data = mysqli_fetch_assoc($q);

$dp = $data['total'] * 0.5;
?>

<!DOCTYPE html>
<html>
<head>
<title>Pembayaran DP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family:Segoe UI;
}

.card-box{
    max-width:500px;
    margin:auto;
    margin-top:60px;
    border:none;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.price{
    font-size:22px;
    font-weight:bold;
    color:#6d28d9;
}
</style>
</head>

<body>

<div class="card card-box">
<div class="card-body p-4">

<h4>Pembayaran DP</h4>
<p>Nomor Invoice: <b><?= $data['nomorInvoice']; ?></b></p>

<div class="alert alert-info">
    Total Pesanan: Rp <?= number_format($data['total']); ?><br>
    DP (50%): <span class="price">Rp <?= number_format($dp); ?></span>
</div>

<form action="proses-bayar-dp.php?id=<?= $idPesanan; ?>" method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label>Jumlah Bayar (DP)</label>
        <input type="number" name="jumlah" value="<?= $dp; ?>" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label>Metode</label>
        <select name="metode" class="form-select">
            <option>Transfer BCA</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Bukti Transfer</label>
        <input type="file" name="bukti" class="form-control" required>
    </div>

    <button class="btn btn-primary w-100">
        Bayar DP Sekarang
    </button>

</form>

</div>
</div>

</body>
</html>