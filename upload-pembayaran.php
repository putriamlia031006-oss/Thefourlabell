<?php

require "koneksi.php";

$idPesanan=
$_GET['id'];

?>

<form
method="POST"
enctype="multipart/form-data">

Jumlah :

<input
name="jumlah">

<br>

<select
name="metode">

<option>Transfer</option>

<option>E-Wallet</option>

</select>

<input
type="file"
name="bukti">

<button>

Upload

</button>

</form>

<?php

if(isset($_POST['jumlah'])){

$file="";

if($_FILES['bukti']['name']){

$file=

time()."_".
$_FILES['bukti']['name'];

move_uploaded_file(

$_FILES['bukti']['tmp_name'],

"upload/".$file

);

}

mysqli_query(

$koneksi,

"INSERT INTO pembayaran(

idPesanan,

jumlah,

metode,

status,

bukti

)

VALUES(

'$idPesanan',

'$_POST[jumlah]',

'$_POST[metode]',

'Pending',

'$file'

)"

);

echo "Menunggu Verifikasi";

}

?>