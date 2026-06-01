<?php

require "../koneksi.php";

$id=$_GET['id'];

$query=mysqli_query(

$koneksi,

"SELECT *

FROM produk

WHERE idProduk='$id'"

);

$data=
mysqli_fetch_assoc(
$query
);

if(isset($_POST['update'])){

mysqli_query(

$koneksi,

"UPDATE produk

SET

namaProduk='$_POST[nama]',

harga='$_POST[harga]',

deskripsi='$_POST[deskripsi]'

WHERE idProduk='$id'"

);

header(
"location:produk.php"
);

}

?>

<form method="POST">

<input

name="nama"

value="<?= $data['namaProduk']; ?>">

<input

name="harga"

value="<?= $data['harga']; ?>">

<textarea
name="deskripsi">

<?= $data['deskripsi']; ?>

</textarea>

<button
name="update">

Update

</button>

</form>