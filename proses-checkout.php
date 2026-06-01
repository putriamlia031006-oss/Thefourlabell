<?php

session_start();

require "koneksi.php";

$idUser=
$_SESSION['user']['idUser'];

$cari=mysqli_query(

$koneksi,

"SELECT *
FROM pelanggan
WHERE idUser='$idUser'"

);

$pelanggan=
mysqli_fetch_assoc($cari);

mysqli_query(

$koneksi,

"INSERT INTO pesanan

(idPelanggan,tanggal,status,jenisPesanan,total)

VALUES(

'$pelanggan[idPelanggan]',

NOW(),

'Menunggu',

'siap_pakai',

'$_POST[total]'

)"

);

$idPesanan=
mysqli_insert_id($koneksi);

foreach(
$_SESSION['cart']
as $cart
){

mysqli_query(

$koneksi,

"INSERT INTO detail_pesanan(

idPesanan,

idProduk,

qty

)

VALUES(

'$idPesanan',

'$cart[idProduk]',

'$cart[qty]'

)"

);

}

unset($_SESSION['cart']);

header(
"location:upload-pembayaran.php?id=$idPesanan"
);

?>