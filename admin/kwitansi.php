<?php

require "koneksi.php";

$id=$_GET['id'];

$query=mysqli_query(

$koneksi,

"SELECT *

FROM pembayaran

WHERE idPembayaran='$id'"

);

$data=
mysqli_fetch_assoc(
$query
);

?>

<h1>

KWITANSI

</h1>

Jumlah :

<?= $data['jumlah']; ?>

<br>

Metode :

<?= $data['metode']; ?>

<br>

Status :

<?= $data['status']; ?>

<br>

<button
onclick="window.print()">

Cetak

</button>