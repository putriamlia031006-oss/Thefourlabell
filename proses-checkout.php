<?php
session_start();
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['user']['idUser'];

/* CEK CART */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    die("Keranjang kosong");
}

/* AMBIL PELANGGAN */
$cari = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

if (!$cari) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

$pelanggan = mysqli_fetch_assoc($cari);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan. Silakan lengkapi data pelanggan terlebih dahulu.");
}

/* TOTAL */
$total = isset($_POST['total']) ? $_POST['total'] : 0;

/* =========================
   1. CEK STOK DI stok_produk
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];
    $qty = $cart['qty'];

    $cek = mysqli_query(
        $koneksi,
        "SELECT jumlahStok 
         FROM stok_produk 
         WHERE idProduk='$idProduk'"
    );

    if (!$cek) {
        die("Query cek stok error: " . mysqli_error($koneksi));
    }

    $stokData = mysqli_fetch_assoc($cek);

    if (!$stokData) {
        die("Stok produk tidak ditemukan untuk ID Produk: " . $idProduk);
    }

    if ($qty > $stokData['jumlahStok']) {
        die("Stok tidak cukup untuk salah satu produk. Stok tersedia: " . $stokData['jumlahStok']);
    }
}

/* =========================
   2. SIMPAN PESANAN
========================= */
$simpanPesanan = mysqli_query(
    $koneksi,
    "INSERT INTO pesanan (
        idPelanggan,
        tanggal,
        status,
        jenisPesanan,
        total
    ) VALUES (
        '$pelanggan[idPelanggan]',
        CURDATE(),
        'Menunggu',
        'siap_pakai',
        '$total'
    )"
);

if (!$simpanPesanan) {
    die("Gagal menyimpan pesanan: " . mysqli_error($koneksi));
}

$idPesanan = mysqli_insert_id($koneksi);

/* =========================
   3. SIMPAN DETAIL + KURANGI STOK
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];
    $qty = $cart['qty'];

    /* AMBIL STOK */
    $cek = mysqli_query(
        $koneksi,
        "SELECT jumlahStok 
         FROM stok_produk 
         WHERE idProduk='$idProduk'"
    );

    if (!$cek) {
        die("Query ambil stok error: " . mysqli_error($koneksi));
    }

    $stokData = mysqli_fetch_assoc($cek);

    if (!$stokData) {
        die("Stok produk tidak ditemukan saat update.");
    }

    /* INSERT DETAIL PESANAN */
    $simpanDetail = mysqli_query(
        $koneksi,
        "INSERT INTO detail_pesanan (
            idPesanan,
            idProduk,
            qty
        ) VALUES (
            '$idPesanan',
            '$idProduk',
            '$qty'
        )"
    );

    if (!$simpanDetail) {
        die("Gagal menyimpan detail pesanan: " . mysqli_error($koneksi));
    }

    /* UPDATE STOK */
    $stokBaru = $stokData['jumlahStok'] - $qty;

    $updateStok = mysqli_query(
        $koneksi,
        "UPDATE stok_produk 
         SET jumlahStok='$stokBaru' 
         WHERE idProduk='$idProduk'"
    );

    if (!$updateStok) {
        die("Gagal update stok: " . mysqli_error($koneksi));
    }
}

/* =========================
   4. DP
========================= */
$dp = $total * 0.5;

/* =========================
   5. PEMBAYARAN
========================= */
$simpanPembayaran = mysqli_query(
    $koneksi,
    "INSERT INTO pembayaran (
        idPesanan,
        jumlah,
        dp,
        metode,
        status
    ) VALUES (
        '$idPesanan',
        '$dp',
        '$dp',
        'Transfer BCA',
        'DP Masuk'
    )"
);

if (!$simpanPembayaran) {
    die("Gagal menyimpan pembayaran: " . mysqli_error($koneksi));
}

/* =========================
   6. INVOICE
========================= */
$invoice = "INV-" . date("Ymd") . "-" . $idPesanan;

$updateInvoice = mysqli_query(
    $koneksi,
    "UPDATE pesanan 
     SET nomorInvoice='$invoice'
     WHERE idPesanan='$idPesanan'"
);

if (!$updateInvoice) {
    die("Gagal update invoice: " . mysqli_error($koneksi));
}

/* =========================
   7. CLEAR CART
========================= */
unset($_SESSION['cart']);

/* =========================
   8. REDIRECT
========================= */
header("Location: upload-pembayaran.php?id=$idPesanan");
exit;
?>