<?php
session_start();

require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID pesanan tidak ditemukan.');
        window.location='pesanan.php';
    </script>";
    exit;
}

$idPesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   AMBIL DATA PESANAN
========================= */
$qPesanan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM pesanan
     WHERE idPesanan = '$idPesanan'
     LIMIT 1"
);

if (!$qPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($qPesanan);

if (!$pesanan) {
    echo "<script>
        alert('Data pesanan tidak ditemukan.');
        window.location='pesanan.php';
    </script>";
    exit;
}

$totalPesanan = (int) $pesanan['total'];

/* =========================
   CEK TOTAL PEMBAYARAN VALID
========================= */
$qTotalBayar = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar
     FROM pembayaran
     WHERE idPesanan = '$idPesanan'
     AND status IN ('DP Masuk', 'Lunas')"
);

if (!$qTotalBayar) {
    die("Query total bayar error: " . mysqli_error($koneksi));
}

$dataBayar = mysqli_fetch_assoc($qTotalBayar);
$totalBayar = (int) $dataBayar['totalBayar'];

if ($totalBayar >= $totalPesanan && $totalPesanan > 0) {
    echo "<script>
        alert('Pesanan ini sudah lunas.');
        window.location='pesanan.php';
    </script>";
    exit;
}

/* =========================
   HITUNG SISA BAYAR
========================= */
$sisaBayar = $totalPesanan - $totalBayar;

if ($sisaBayar <= 0) {
    $sisaBayar = $totalPesanan;
}

/* =========================
   SIMPAN PEMBAYARAN CASH
   Catatan: tidak pakai kolom tanggal
   karena tabel pembayaran kamu tidak punya kolom tanggal
========================= */
$insert = mysqli_query(
    $koneksi,
    "INSERT INTO pembayaran
    (idPesanan, metode, jumlah, status, bukti)
    VALUES
    ('$idPesanan', 'Cash di Toko', '$sisaBayar', 'Lunas', '')"
);

if (!$insert) {
    die("Gagal menyimpan pembayaran cash: " . mysqli_error($koneksi));
}

/* =========================
   UPDATE STATUS PESANAN
========================= */
$updatePesanan = mysqli_query(
    $koneksi,
    "UPDATE pesanan SET
        status = 'Selesai'
     WHERE idPesanan = '$idPesanan'"
);

if (!$updatePesanan) {
    die("Gagal update status pesanan: " . mysqli_error($koneksi));
}

echo "<script>
    alert('Pembayaran cash berhasil dikonfirmasi. Kwitansi sudah tersedia.');
    window.location='pesanan.php';
</script>";
exit;
?>