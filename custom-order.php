<?php

session_start();

if(!isset($_SESSION['user'])){
    header("location:login.php");
}

?>

<h2>Custom Order</h2>

<form action="proses-custom.php"
method="POST"
enctype="multipart/form-data">

<label>Jenis Pakaian</label>

<select name="jenis">

<option>Hoodie</option>

<option>Varsity</option>

<option>Polo Shirt</option>

<option>T-Shirt</option>

</select>

<br>

<input
type="text"
name="ukuran"
placeholder="Ukuran">

<br>

<input
type="number"
name="qty"
placeholder="Qty">

<br>

<textarea
name="catatan"
placeholder="Catatan custom">
</textarea>

<br>

Upload Desain :

<input
type="file"
name="desain">

<br>

<button>

Pesan Sekarang

</button>

</form>