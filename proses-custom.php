<?php
session_start();
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['user']['idUser'];

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
    die("Data pelanggan tidak ditemukan. Silakan lengkapi data pelanggan terlebih dahulu.");
}

$idPelanggan = $pelanggan['idPelanggan'];

/* AMBIL DATA FORM */
$jenis = $_POST['jenis'];
$ukuran = $_POST['ukuran'];
$qty = $_POST['qty'];
$catatan = $_POST['catatan'];

if ($qty < 1) {
    die("Jumlah pesanan tidak valid.");
}

/* =========================
   HITUNG DEADLINE OTOMATIS
   1 - 100 pcs     = 1 bulan
   101 - 200 pcs   = 2 bulan
   201 - 300 pcs   = 3 bulan
========================= */
$bulanDeadline = ceil($qty / 100);

$deadlineSelesai = date(
    'Y-m-d',
    strtotime("+$bulanDeadline month")
);

/* =========================
   UPLOAD DESAIN
========================= */
$desain = "";

if (!empty($_FILES['desain']['name'])) {

    if (!is_dir("upload")) {
        mkdir("upload", 0777, true);
    }

    $namaFile = $_FILES['desain']['name'];
    $tmpFile = $_FILES['desain']['tmp_name'];
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
        die("Format desain harus JPG, JPEG, PNG, atau WEBP.");
    }

    $desain = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);

    $upload = move_uploaded_file(
        $tmpFile,
        "upload/" . $desain
    );

    if (!$upload) {
        die("Gagal upload desain.");
    }
}

/* =========================
   TOTAL CUSTOM
   Sesuaikan harga custom kamu di sini
========================= */
$hargaSatuan = 0;

if ($jenis == "Hoodie") {
    $hargaSatuan = 150000;
} elseif ($jenis == "Varsity") {
    $hargaSatuan = 200000;
} elseif ($jenis == "Polo Shirt") {
    $hargaSatuan = 100000;
} elseif ($jenis == "T-Shirt") {
    $hargaSatuan = 80000;
} else {
    die("Jenis pakaian tidak valid.");
}

$total = $hargaSatuan * $qty;

/* =========================
   SIMPAN PESANAN CUSTOM
========================= */
$simpanPesanan = mysqli_query(
    $koneksi,
    "INSERT INTO pesanan (
        idPelanggan,
        tanggal,
        deadlineSelesai,
        status,
        jenisPesanan,
        total
    ) VALUES (
        '$idPelanggan',
        CURDATE(),
        '$deadlineSelesai',
        'Menunggu',
        'custom',
        '$total'
    )"
);

if (!$simpanPesanan) {
    die("Gagal menyimpan pesanan custom: " . mysqli_error($koneksi));
}

$idPesanan = mysqli_insert_id($koneksi);

/* =========================
   NOMOR INVOICE
========================= */
$invoice = "INV-CUS-" . date("Ymd") . "-" . $idPesanan;

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
   SIMPAN DETAIL CUSTOM
   Jika tabel detail_custom belum ada, jalankan SQL di bawah
========================= */
$simpanCustom = mysqli_query(
    $koneksi,
    "INSERT INTO detail_custom (
        idPesanan,
        jenis,
        ukuran,
        qty,
        catatan,
        desain
    ) VALUES (
        '$idPesanan',
        '$jenis',
        '$ukuran',
        '$qty',
        '$catatan',
        '$desain'
    )"
);

if (!$simpanCustom) {
    die("Gagal menyimpan detail custom: " . mysqli_error($koneksi));
}

/* =========================
   REDIRECT KE PEMBAYARAN
========================= */
header("Location: upload-pembayaran.php?id=$idPesanan");
exit;
?>