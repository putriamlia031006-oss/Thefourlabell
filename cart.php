<?php

session_start();

require "koneksi.php";

include "navbar.php";

$total=0;

?>

<h2>Keranjang</h2>

<table class="table">

<tr>

<th>Produk</th>

<th>Qty</th>

<th>Subtotal</th>

</tr>

<?php

if(isset($_SESSION['cart'])){

foreach(
$_SESSION['cart']
as $cart
){

$query=mysqli_query(

$koneksi,

"SELECT *

FROM produk

WHERE idProduk=
'$cart[idProduk]'"

);

$data=
mysqli_fetch_assoc(
$query
);

$sub=
$data['harga']
*
$cart['qty'];

$total += $sub;

?>

<tr>

<td>

<?= $data['namaProduk']; ?>

</td>

<td>

<?= $cart['qty']; ?>

</td>

<td>

<?= number_format($sub); ?>

</td>

</tr>

<?php
}
}
?>

</table>

<h3>

Total :

<?= number_format($total); ?>

</h3>

<a
href="checkout.php"

class="btn btn-success">

Checkout

</a>

<?php
include "footer.php";
?>