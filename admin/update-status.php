<?php

require "../koneksi.php";

$id=
$_GET['id'];

if(isset($_POST['status'])){

mysqli_query(

$koneksi,

"UPDATE pesanan

SET status='$_POST[status]'

WHERE idPesanan='$id'"

);

header(
"location:pesanan.php"
);

}

?>

<form method="POST">

<select name="status">

<option>Menunggu</option>

<option>Diproses</option>

<option>Produksi</option>

<option>Selesai</option>

<option>Dikirim</option>

</select>

<button>

Update

</button>

</form>