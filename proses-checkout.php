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

/* AMBIL DATA PELANGGAN */
$cari = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

if (!$cari) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

$pelanggan = mysqli_fetch_assoc($cari);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan.");
}

$idPelanggan = $pelanggan['idPelanggan'];

/* =========================
   HITUNG TOTAL DARI CART
========================= */
$totalAwal = 0;

foreach ($_SESSION['cart'] as $cart) {

    $idProduk = $cart['idProduk'];
    $qty = $cart['qty'];

    $qProduk = mysqli_query(
        $koneksi,
        "SELECT harga FROM produk WHERE idProduk='$idProduk'"
    );

    if (!$qProduk) {
        die("Query produk error: " . mysqli_error($koneksi));
    }

    $produk = mysqli_fetch_assoc($qProduk);

    if (!$produk) {
        die("Produk tidak ditemukan.");
    }

    $subtotal = $produk['harga'] * $qty;
    $totalAwal += $subtotal;
}

/* =========================
   CEK JUMLAH TRANSAKSI CUSTOMER
   Kalau sudah 5 transaksi, diskon 20%
========================= */
$qTransaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS totalTransaksi
     FROM pesanan
     WHERE idPelanggan='$idPelanggan'
     AND status != 'Batal'"
);

if (!$qTransaksi) {
    die("Query transaksi error: " . mysqli_error($koneksi));
}

$dataTransaksi = mysqli_fetch_assoc($qTransaksi);
$jumlahTransaksi = $dataTransaksi['totalTransaksi'];

$diskonPersen = 0;
$nominalDiskon = 0;
$total = $totalAwal;

if ($jumlahTransaksi >= 5) {
    $diskonPersen = 20;
    $nominalDiskon = $totalAwal * 0.2;
    $total = $totalAwal - $nominalDiskon;
}

/* =========================
   CEK STOK DI stok_produk
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
   SIMPAN PESANAN
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
        '$idPelanggan',
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
   SIMPAN DETAIL + KURANGI STOK
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
        die("Query ambil stok error: " . mysqli_error($koneksi));
    }

    $stokData = mysqli_fetch_assoc($cek);

    if (!$stokData) {
        die("Stok produk tidak ditemukan saat update.");
    }

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
   DP 50% DARI TOTAL SETELAH DISKON
========================= */
$dp = $total * 0.5;

/* =========================
   SIMPAN PEMBAYARAN AWAL
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
   INVOICE
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
   SIMPAN INFO DISKON KE SESSION
   untuk ditampilkan di halaman pembayaran
========================= */
$_SESSION['info_diskon'] = [
    'jumlahTransaksi' => $jumlahTransaksi,
    'diskonPersen' => $diskonPersen,
    'totalAwal' => $totalAwal,
    'nominalDiskon' => $nominalDiskon,
    'totalAkhir' => $total
];

/* CLEAR CART */
unset($_SESSION['cart']);

/* REDIRECT */
header("Location: upload-pembayaran.php?id=$idPesanan");
exit;
?>