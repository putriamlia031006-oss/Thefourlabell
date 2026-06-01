<?php

require "../koneksi.php";

$query=mysqli_query(

$koneksi,

"SELECT *

FROM pesanan

ORDER BY idPesanan DESC"

);

while(

$row=
mysqli_fetch_assoc(
$query
)

){

?>

<div>

ID :

<?= $row['idPesanan']; ?>

<br>

Jenis :

<?= $row['jenisPesanan']; ?>

<br>

Status :

<?= $row['status']; ?>

<br>

<a href=

"update-status.php?id=
<?= $row['idPesanan']; ?>

">

Update

</a>

</div>

<hr>

<?php } ?>