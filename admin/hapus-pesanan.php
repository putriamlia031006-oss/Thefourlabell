<?php
session_start();
include "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$idPesanan = $_GET['id'];

/* AMBIL DATA PESANAN */
$qPesanan = mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan WHERE idPesanan='$idPesanan'"
);

if (!$qPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($qPesanan);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

/* 
   Kalau pesanan siap_pakai dihapus,
   stok dikembalikan lagi berdasarkan detail_pesanan.
*/
if ($pesanan['jenisPesanan'] == "siap_pakai") {

    $qDetail = mysqli_query(
        $koneksi,
        "SELECT * FROM detail_pesanan WHERE idPesanan='$idPesanan'"
    );

    if ($qDetail) {
        while ($d = mysqli_fetch_assoc($qDetail)) {

            $idProduk = $d['idProduk'];
            $qty = $d['qty'];

            mysqli_query(
                $koneksi,
                "UPDATE stok_produk
                 SET jumlahStok = jumlahStok + $qty
                 WHERE idProduk='$idProduk'"
            );
        }
    }
}

/* HAPUS DATA TERKAIT */
mysqli_query($koneksi, "DELETE FROM pembayaran WHERE idPesanan='$idPesanan'");
mysqli_query($koneksi, "DELETE FROM detail_pesanan WHERE idPesanan='$idPesanan'");
mysqli_query($koneksi, "DELETE FROM detail_custom WHERE idPesanan='$idPesanan'");

/* HAPUS PESANAN */
$hapus = mysqli_query(
    $koneksi,
    "DELETE FROM pesanan WHERE idPesanan='$idPesanan'"
);

if (!$hapus) {
    die("Gagal menghapus pesanan: " . mysqli_error($koneksi));
}

header("Location: pesanan.php");
exit;
?>