<?php
session_start();
require "koneksi.php";

$idUser = $_SESSION['user']['idUser'];

/* =========================
   AMBIL DATA PELANGGAN
========================= */
$q = mysqli_query($koneksi,
"SELECT * FROM pelanggan WHERE idUser='$idUser'");
$pelanggan = mysqli_fetch_assoc($q);

/* =========================
   AMBIL DATA FORM
========================= */
$jenis   = $_POST['jenis'];
$ukuran  = $_POST['ukuran'];
$qty     = $_POST['qty'];
$catatan = $_POST['catatan'];

/* =========================
   HARGA PER JENIS
========================= */
$hargaList = [
    "Hoodie" => 150000,
    "Varsity" => 250000,
    "Polo Shirt" => 175000,
    "T-Shirt" => 90000
];

$hargaSatuan = $hargaList[$jenis] ?? 0;

/* =========================
   TOTAL & DP
========================= */
$total = $hargaSatuan * $qty;
$dp    = $total * 0.5;

/* =========================
   UPLOAD DESAIN
========================= */
$file = "";
if (!empty($_FILES['desain']['name'])) {
    $file = time() . "_" . $_FILES['desain']['name'];
    move_uploaded_file($_FILES['desain']['tmp_name'], "desain/" . $file);
}

/* =========================
   SIMPAN PESANAN
   (status awal = DP)
========================= */
mysqli_query($koneksi,
"INSERT INTO pesanan (
    idPelanggan,
    tanggal,
    status,
    jenisPesanan,
    total
) VALUES (
    '{$pelanggan['idPelanggan']}',
    NOW(),
    'DP', 
    'custom',
    '$total'
)");

$idPesanan = mysqli_insert_id($koneksi);

/* =========================
   DETAIL PESANAN
========================= */
mysqli_query($koneksi,
"INSERT INTO detail_pesanan (
    idPesanan,
    jenis,
    ukuran,
    desain,
    qty,
    customText
) VALUES (
    '$idPesanan',
    '$jenis',
    '$ukuran',
    '$file',
    '$qty',
    '$catatan'
)");

/* =========================
   PEMBAYARAN DP
========================= */
mysqli_query($koneksi,
"INSERT INTO pembayaran (
    idPesanan,
    jumlah,
    metode,
    status,
    tipe
) VALUES (
    '$idPesanan',
    '$dp',
    'Transfer BCA',
    'Menunggu Verifikasi',
    'DP'
)");

/* =========================
   INVOICE
========================= */
$invoice = "INV-" . date("Ymd") . "-" . $idPesanan;

mysqli_query($koneksi,
"UPDATE pesanan 
SET nomorInvoice='$invoice'
WHERE idPesanan='$idPesanan'");

/* =========================
   REDIRECT (WAJIB ID)
========================= */
header("Location: bayar-dp.php?id=$idPesanan");
exit;
?>