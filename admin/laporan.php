<?php
session_start();
require "../koneksi.php";

/* =========================
   LAPORAN STOK
========================= */
$stok = mysqli_query($koneksi,

"SELECT 
    produk.namaProduk,
    stok_produk.jumlahStok,
    stok_produk.satuan
FROM stok_produk
JOIN produk 
ON stok_produk.idProduk = produk.idProduk"
);

/* =========================
   LAPORAN PESANAN
========================= */
$pesanan = mysqli_query($koneksi,

"SELECT 
    pesanan.*,
    pelanggan.nama
FROM pesanan
JOIN pelanggan
ON pesanan.idPelanggan = pelanggan.idPelanggan
ORDER BY pesanan.idPesanan DESC"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h2>Laporan Sistem Konveksi</h2>

<hr>

<!-- =========================
     LAPORAN STOK
========================= -->
<h4>📦 Laporan Stok Produk</h4>

<table class="table table-bordered table-striped">

<thead class="table-dark">
<tr>
    <th>Produk</th>
    <th>Stok</th>
    <th>Satuan</th>
</tr>
</thead>

<tbody>

<?php while($s = mysqli_fetch_assoc($stok)) { ?>
<tr>
    <td><?= $s['namaProduk']; ?></td>
    <td><?= $s['jumlahStok']; ?></td>
    <td><?= $s['satuan']; ?></td>
</tr>
<?php } ?>

</tbody>

</table>

<hr>

<!-- =========================
     LAPORAN PESANAN
========================= -->
<h4>🧾 Laporan Pesanan</h4>

<table class="table table-bordered table-striped">

<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Tanggal</th>
    <th>Status</th>
    <th>Total</th>
</tr>
</thead>

<tbody>

<?php while($p = mysqli_fetch_assoc($pesanan)) { ?>
<tr>
    <td><?= $p['idPesanan']; ?></td>
    <td><?= $p['nama']; ?></td>
    <td><?= $p['tanggal']; ?></td>
    <td><?= $p['status']; ?></td>
    <td>Rp <?= number_format($p['total']); ?></td>
</tr>
<?php } ?>

</tbody>

</table>

</div>

</body>
</html>