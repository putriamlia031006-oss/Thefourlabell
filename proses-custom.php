<?php

session_start();

require "koneksi.php";

$idUser=$_SESSION['user']['idUser'];

$cari=mysqli_query(

$koneksi,

"SELECT *
FROM pelanggan
WHERE idUser='$idUser'"

);

$pelanggan=
mysqli_fetch_assoc($cari);

$namaFile="";

if($_FILES['desain']['name']!=""){

$namaFile=

time()."_".
$_FILES['desain']['name'];

move_uploaded_file(

$_FILES['desain']['tmp_name'],

"upload/".$namaFile

);

}

mysqli_query(

$koneksi,

"INSERT INTO pesanan
(idPelanggan,tanggal,status,jenisPesanan,total)

VALUES(

'$pelanggan[idPelanggan]',

NOW(),

'Menunggu',

'custom',

0

)"

);

$idPesanan=
mysqli_insert_id($koneksi);

mysqli_query(

$koneksi,

"INSERT INTO detail_pesanan

(idPesanan,jenis,ukuran,desain,qty,customText)

VALUES(

'$idPesanan',

'$_POST[jenis]',

'$_POST[ukuran]',

'$namaFile',

'$_POST[qty]',

'$_POST[catatan]'

)"

);

header(
"location:pesanan-saya.php"
);

?>