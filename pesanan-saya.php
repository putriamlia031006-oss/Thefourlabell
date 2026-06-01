<?php
session_start();
require "koneksi.php";
include "navbar.php";

$idUser = $_SESSION['user']['idUser'];

$query = mysqli_query($koneksi,
"SELECT *
 FROM pesanan p
 JOIN pelanggan pl ON p.idPelanggan = pl.idPelanggan
 WHERE pl.idUser = '$idUser'
 ORDER BY p.idPesanan DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Pesanan Saya</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family:Segoe UI;
}

.page-title{
    font-weight:700;
    color:#5b3cc4;
}

.order-card{
    border:none;
    border-radius:16px;
    box-shadow:0 6px 20px rgba(0,0,0,.08);
    transition:.2s;
}

.order-card:hover{
    transform:translateY(-2px);
}

.label{
    font-size:12px;
    color:#888;
}

.value{
    font-weight:600;
    color:#333;
}
</style>
</head>

<body>

<div class="container py-5">

<h3 class="page-title mb-4">Pesanan Saya</h3>

<?php while($row = mysqli_fetch_assoc($query)) { ?>

<?php
/* =====================
   HITUNG PEMBAYARAN
===================== */
$qBayar = mysqli_query($koneksi,
"SELECT SUM(jumlah) as totalBayar
FROM pembayaran
WHERE idPesanan='{$row['idPesanan']}'");

$bayar = mysqli_fetch_assoc($qBayar);

$totalBayar = $bayar['totalBayar'] ?? 0;
$totalPesanan = $row['total'] ?? 0;

$sisa = $totalPesanan - $totalBayar;

$isLunas = ($totalBayar >= $totalPesanan);
?>

<!-- CARD -->
<div class="card order-card mb-3">
    <div class="card-body">

        <div class="row g-3 align-items-center">

            <!-- Invoice -->
            <div class="col-md-3">
                <div class="label">Invoice</div>
                <div class="value"><?= $row['nomorInvoice']; ?></div>
            </div>

            <!-- Jenis -->
            <div class="col-md-3">
                <div class="label">Jenis Pesanan</div>
                <div class="value"><?= $row['jenisPesanan']; ?></div>
            </div>

            <!-- Total -->
            <div class="col-md-3">
                <div class="label">Total</div>
                <div class="value">
                    Rp <?= number_format($row['total']); ?>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <div class="label">Status Pembayaran</div>

                <?php if ($isLunas) { ?>
                    <span class="badge bg-success mt-1">Lunas</span>
                <?php } else { ?>
                    <span class="badge bg-warning text-dark mt-1">
                        DP / Belum Lunas
                    </span>

                    <div class="mt-2">
                        <a href="bayar-sisa.php?id=<?= $row['idPesanan']; ?>"
                           class="btn btn-sm btn-warning">
                            Bayar Sisa Rp <?= number_format($sisa); ?>
                        </a>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>
</div>

<?php } ?>

</div>

</body>
</html>