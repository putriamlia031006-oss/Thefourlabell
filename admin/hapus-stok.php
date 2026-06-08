<?php
require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID stok tidak ditemukan.');
        window.location='stok.php';
    </script>";
    exit;
}

$idStok = mysqli_real_escape_string($koneksi, $_GET['id']);

/* CEK DATA STOK */
$cek = mysqli_query(
    $koneksi,
    "SELECT * FROM stok_produk WHERE idStok = '$idStok' LIMIT 1"
);

if (!$cek) {
    die("Query cek stok error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($cek);

if (!$data) {
    echo "<script>
        alert('Data stok tidak ditemukan.');
        window.location='stok.php';
    </script>";
    exit;
}

/* HAPUS STOK */
$hapus = mysqli_query(
    $koneksi,
    "DELETE FROM stok_produk WHERE idStok = '$idStok'"
);

if ($hapus) {
    echo "<script>
        alert('Data stok berhasil dihapus.');
        window.location='stok.php';
    </script>";
    exit;
} else {
    echo "<script>
        alert('Data stok gagal dihapus.');
        window.location='stok.php';
    </script>";
    exit;
}
?>