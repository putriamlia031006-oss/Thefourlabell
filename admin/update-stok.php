<?php
include "auth.php";
require "../koneksi.php";

$id=$_GET['id'];

if(isset($_POST['stok'])){

mysqli_query(

$koneksi,

"UPDATE stok_produk

SET jumlahStok='$_POST[stok]'

WHERE idStok='$id'"

);

header(
"location:stok.php"
);

}

?>

<form method="POST">

<input
name="stok">

<button>

Update

</button>

</form>