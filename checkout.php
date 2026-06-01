<?php

session_start();

require "koneksi.php";

$total=0;

foreach($_SESSION['cart'] as $cart){

$idProduk=$cart['idProduk'];

$query=mysqli_query(

$koneksi,

"SELECT *
FROM produk
WHERE idProduk='$idProduk'"

);

$data=
mysqli_fetch_assoc($query);

$subtotal=

$data['harga']
*
$cart['qty'];

$total += $subtotal;

}

?>

<h2>Total :

<?= number_format($total); ?>

</h2>

<form action="proses-checkout.php"
method="POST">

<input
type="hidden"
name="total"
value="<?= $total ?>">

<button>

Checkout

</button>

</form>