<?php
session_start();

require "auth.php";
require "../koneksi.php";

/* CEK ID PESANAN */
if (!isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$idPesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

/* AMBIL DATA PESANAN */
$qPesanan = mysqli_query(
    $koneksi,
    "SELECT * 
     FROM pesanan 
     WHERE idPesanan='$idPesanan'"
);

if (!$qPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($qPesanan);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

$totalPesanan = (int) $pesanan['total'];

/* HITUNG TOTAL PEMBAYARAN YANG SUDAH DIVERIFIKASI */
$qBayar = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar
     FROM pembayaran
     WHERE idPesanan='$idPesanan'
     AND status IN ('DP Masuk', 'Lunas')"
);

if (!$qBayar) {
    die("Query total pembayaran error: " . mysqli_error($koneksi));
}

$dataBayar = mysqli_fetch_assoc($qBayar);
$totalBayar = (int) $dataBayar['totalBayar'];

/* AMBIL PEMBAYARAN PENDING TERBARU */
$qPending = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pembayaran
     WHERE idPesanan='$idPesanan'
     AND status='Pending'
     ORDER BY idPembayaran DESC
     LIMIT 1"
);

if (!$qPending) {
    die("Query pembayaran pending error: " . mysqli_error($koneksi));
}

$pending = mysqli_fetch_assoc($qPending);

if (!$pending) {
    echo "<script>
        alert('Tidak ada pembayaran pending yang perlu diverifikasi.');
        window.location.href='detail-pesanan.php?id=$idPesanan';
    </script>";
    exit;
}

$idPembayaran = $pending['idPembayaran'];
$jumlahPending = (int) $pending['jumlah'];

$totalSetelahVerifikasi = $totalBayar + $jumlahPending;

/* TENTUKAN STATUS PEMBAYARAN */
if ($totalSetelahVerifikasi >= $totalPesanan) {
    $statusPembayaranBaru = "Lunas";
    $statusPesananBaru = "Diproses";
} else {
    $statusPembayaranBaru = "DP Masuk";
    $statusPesananBaru = "Diproses";
}

/* UPDATE PEMBAYARAN */
$updatePembayaran = mysqli_query(
    $koneksi,
    "UPDATE pembayaran
     SET status='$statusPembayaranBaru'
     WHERE idPembayaran='$idPembayaran'"
);

if (!$updatePembayaran) {
    die("Gagal verifikasi pembayaran: " . mysqli_error($koneksi));
}

/* UPDATE STATUS PESANAN */
$updatePesanan = mysqli_query(
    $koneksi,
    "UPDATE pesanan
     SET status='$statusPesananBaru'
     WHERE idPesanan='$idPesanan'"
);

if (!$updatePesanan) {
    die("Gagal update status pesanan: " . mysqli_error($koneksi));
}

/* KEMBALI KE DETAIL PESANAN */
echo "<script>
    alert('Pembayaran berhasil diverifikasi.');
    window.location.href='detail-pesanan.php?id=$idPesanan';
</script>";
exit;
?>