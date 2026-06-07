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
$qPelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

if (!$qPelanggan) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

$pelanggan = mysqli_fetch_assoc($qPelanggan);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan.");
}

$idPelanggan = $pelanggan['idPelanggan'];

/* =========================
   AMBIL DATA DARI FORM CHECKOUT
========================= */
$alamatKirim = isset($_POST['alamat_kirim'])
    ? mysqli_real_escape_string($koneksi, trim($_POST['alamat_kirim']))
    : "";

$tipePengiriman = isset($_POST['tipe_pengiriman'])
    ? mysqli_real_escape_string($koneksi, $_POST['tipe_pengiriman'])
    : "dikirim";

$jasaKirim = isset($_POST['jasa_kirim'])
    ? mysqli_real_escape_string($koneksi, $_POST['jasa_kirim'])
    : "";

$ongkir = isset($_POST['ongkir'])
    ? (int) $_POST['ongkir']
    : 0;

$metode = isset($_POST['metode'])
    ? mysqli_real_escape_string($koneksi, $_POST['metode'])
    : "bca_transfer";

/* =========================
   VALIDASI PENGIRIMAN
========================= */
if ($tipePengiriman == "ambil") {
    $alamatKirim = "";
    $jasaKirim = "Ambil di Tempat";
    $ongkir = 0;
}

if ($tipePengiriman == "dikirim" && $alamatKirim == "") {
    die("Alamat pengiriman wajib diisi.");
}

/* =========================
   HITUNG TOTAL DARI CART
========================= */
$totalAwal = 0;
$items = [];

foreach ($_SESSION['cart'] as $cart) {

    $idProduk = mysqli_real_escape_string($koneksi, $cart['idProduk']);
    $qty = (int) $cart['qty'];

    $qProduk = mysqli_query(
        $koneksi,
        "SELECT * FROM produk WHERE idProduk='$idProduk'"
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

    $items[] = [
        'idProduk' => $produk['idProduk'],
        'namaProduk' => $produk['namaProduk'],
        'harga' => $produk['harga'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

/* =========================
   CEK JUMLAH TRANSAKSI UNTUK DISKON
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
$jumlahTransaksi = (int) $dataTransaksi['totalTransaksi'];

$diskonPersen = 0;
$nominalDiskon = 0;

if ($jumlahTransaksi >= 5) {
    $diskonPersen = 20;
    $nominalDiskon = $totalAwal * 0.20;
}

$totalSetelahDiskon = $totalAwal - $nominalDiskon;
$totalAkhir = $totalSetelahDiskon + $ongkir;

/* Pakai total hitungan server */
$total = $totalAkhir;

/* =========================
   CEK STOK
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = mysqli_real_escape_string($koneksi, $cart['idProduk']);
    $qty = (int) $cart['qty'];

    $cekStok = mysqli_query(
        $koneksi,
        "SELECT jumlahStok 
         FROM stok_produk 
         WHERE idProduk='$idProduk'"
    );

    if (!$cekStok) {
        die("Query cek stok error: " . mysqli_error($koneksi));
    }

    $stokData = mysqli_fetch_assoc($cekStok);

    if (!$stokData) {
        die("Stok produk tidak ditemukan untuk ID Produk: " . $idProduk);
    }

    if ($qty > $stokData['jumlahStok']) {
        die("Stok tidak cukup untuk salah satu produk. Stok tersedia: " . $stokData['jumlahStok']);
    }
}

/* =========================
   LABEL METODE PEMBAYARAN
========================= */
$labelMetode = "Transfer Bank BCA";

if ($metode == "bca_transfer") {
    $labelMetode = "Transfer Bank BCA";
} elseif ($metode == "seabank_transfer") {
    $labelMetode = "Transfer SeaBank";
} elseif ($metode == "dana") {
    $labelMetode = "DANA";
} elseif ($metode == "ovo") {
    $labelMetode = "OVO";
} elseif ($metode == "gopay") {
    $labelMetode = "GoPay";
} elseif ($metode == "shopeepay") {
    $labelMetode = "ShopeePay";
} elseif ($metode == "cash_toko") {
    $labelMetode = "Cash di Toko";
}

/* =========================
   STATUS PESANAN
========================= */
if ($metode == "cash_toko") {
    $statusPesanan = "Menunggu Pembayaran di Toko";
} else {
    $statusPesanan = "Menunggu Upload Bukti Pembayaran";
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
        '$statusPesanan',
        'siap_pakai',
        '$total'
    )"
);

if (!$simpanPesanan) {
    die("Gagal menyimpan pesanan: " . mysqli_error($koneksi));
}

$idPesanan = mysqli_insert_id($koneksi);

/* =========================
   BUAT INVOICE
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
   SIMPAN DETAIL PESANAN + KURANGI STOK
========================= */
foreach ($_SESSION['cart'] as $cart) {

    $idProduk = mysqli_real_escape_string($koneksi, $cart['idProduk']);
    $qty = (int) $cart['qty'];

    $cekStok = mysqli_query(
        $koneksi,
        "SELECT jumlahStok 
         FROM stok_produk 
         WHERE idProduk='$idProduk'"
    );

    if (!$cekStok) {
        die("Query ambil stok error: " . mysqli_error($koneksi));
    }

    $stokData = mysqli_fetch_assoc($cekStok);

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
   SIMPAN PEMBAYARAN AWAL
   CATATAN:
   - Transfer/e-wallet TIDAK langsung insert pembayaran.
   - Pembayaran baru masuk setelah user upload bukti.
   - Cash di toko hanya dicatat Rp0 supaya ada jejak metode.
========================= */
if ($metode == "cash_toko") {

    $simpanPembayaranCash = mysqli_query(
        $koneksi,
        "INSERT INTO pembayaran (
            idPesanan,
            jumlah,
            dp,
            metode,
            status
        ) VALUES (
            '$idPesanan',
            '0',
            '0',
            '$labelMetode',
            'Belum Bayar'
        )"
    );

    if (!$simpanPembayaranCash) {
        die("Gagal menyimpan pembayaran cash: " . mysqli_error($koneksi));
    }
}

/* =========================
   SIMPAN INFO CHECKOUT KE SESSION
========================= */
$_SESSION['info_diskon'] = [
    'jumlahTransaksi' => $jumlahTransaksi,
    'diskonPersen' => $diskonPersen,
    'totalAwal' => $totalAwal,
    'nominalDiskon' => $nominalDiskon,
    'ongkir' => $ongkir,
    'totalAkhir' => $total
];

$_SESSION['info_checkout'] = [
    'idPesanan' => $idPesanan,
    'invoice' => $invoice,
    'tipePengiriman' => $tipePengiriman,
    'alamatKirim' => $alamatKirim,
    'jasaKirim' => $jasaKirim,
    'ongkir' => $ongkir,
    'metode' => $labelMetode,
    'statusPesanan' => $statusPesanan
];

/* CLEAR CART */
unset($_SESSION['cart']);

/* =========================
   REDIRECT
========================= */
if ($metode == "cash_toko") {
    header("Location: pesanan-saya.php");
    exit;
} else {
    header("Location: upload-pembayaran.php?id=$idPesanan");
    exit;
}
?>