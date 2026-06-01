<?php

session_start();

require "koneksi.php";

$idUser=
$_SESSION['user']['idUser'];

$query=mysqli_query(

$koneksi,

"SELECT *

FROM pesanan p

JOIN pelanggan pl
ON p.idPelanggan=
pl.idPelanggan

WHERE pl.idUser=
'$idUser'

ORDER BY idPesanan DESC"

);

while(
$row=
mysqli_fetch_assoc(
$query
)

){

echo "

ID : ".$row['idPesanan']."

<br>

Jenis :

".$row['jenisPesanan']."

<br>

Status :

<span class="badge bg-primary">

<?= $row['status']; ?>

</span>

<hr>

";

}

?>