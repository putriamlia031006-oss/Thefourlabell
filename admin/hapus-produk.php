<?php
include "auth.php";
require "../koneksi.php";

$id = $_GET['id'];

/* hapus stok terkait */

mysqli_query(

    $koneksi,

    "DELETE FROM stok_produk
    WHERE idProduk='$id'"

);

/* hapus produk */

mysqli_query(

    $koneksi,

    "DELETE FROM produk
    WHERE idProduk='$id'"

);

header("location:produk.php");
exit;

?>