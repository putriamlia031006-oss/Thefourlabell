<?php

require "../koneksi.php";

$query=mysqli_query(

$koneksi,

"SELECT *

FROM pembayaran p

JOIN pesanan ps

ON p.idPesanan=
ps.idPesanan"

);

$total=0;

while(
$row=
mysqli_fetch_assoc(
$query
)

){

$total +=
$row['jumlah'];

echo "

Pesanan :

".$row['idPesanan']."

|

".$row['jumlah']."

<br>

";

}

echo "

<h2>

Total :

$total

</h2>

";