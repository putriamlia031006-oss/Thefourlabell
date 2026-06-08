<?php
include "auth.php";
require "../koneksi.php";

$id = $_GET['id'];

/* ambil data pesanan + pembayaran */
$cek = mysqli_query($koneksi,
"SELECT p.status, py.jumlah
FROM pesanan p
LEFT JOIN pembayaran py ON p.idPesanan = py.idPesanan
WHERE p.idPesanan='$id'");

$data = mysqli_fetch_assoc($cek);

/* proses update */
if (isset($_POST['status'])) {

    $statusBaru = $_POST['status'];

    /* VALIDASI: tidak boleh selesai kalau belum lunas */
    $lunas = ($data['status'] == "Lunas" || $data['jumlah'] > 0);

    if ($statusBaru == "Selesai" && !$lunas) {
        die("Pesanan belum lunas!");
    }

    mysqli_query(
        $koneksi,
        "UPDATE pesanan
        SET status='$statusBaru'
        WHERE idPesanan='$id'"
    );

    header("Location: pesanan.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Update Status Pesanan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family:'Segoe UI', sans-serif;
}

.card-box{
    max-width:500px;
    margin:80px auto;
    border:none;
    border-radius:18px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

.btn-lavender{
    background:#8b5cf6;
    color:white;
    border:none;
    border-radius:10px;
    padding:10px 16px;
}

.btn-lavender:hover{
    background:#7c3aed;
    color:white;
}
</style>

</head>

<body>

<div class="card card-box">
<div class="card-body p-4">

<h4 class="mb-3">Update Status Pesanan</h4>

<form method="POST">

<label class="mb-2">Status</label>

<select name="status" class="form-select mb-3" required>
    <option value="Menunggu">Menunggu</option>
    <option value="Diproses">Diproses</option>
    <option value="Produksi">Produksi</option>
    <option value="Selesai">Selesai</option>
    <option value="Dikirim">Dikirim</option>
</select>

<button type="submit" class="btn btn-lavender w-100">
    Update
</button>

</form>

</div>
</div>

</body>
</html>