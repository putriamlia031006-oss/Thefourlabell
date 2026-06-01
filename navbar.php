<?php

if(session_status() == PHP_SESSION_NONE){

    session_start();

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>THE FOUR LABEL</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/style.css">

<style>

.navbar-custom{

    background:
    rgba(255,255,255,.92);

    backdrop-filter:
    blur(12px);

    box-shadow:
    0 3px 15px rgba(0,0,0,.06);

    padding:14px 0;

}

.logo{

    font-size:26px;

    font-weight:700;

    color:#7b4bc6 !important;

}

.nav-link{

    color:#555 !important;

    font-weight:500;

    margin-left:10px;

    transition:.3s;

}

.nav-link:hover{

    color:#8f63d9 !important;

}

.btn-login{

    background:#9d7ad6;

    color:white !important;

    border-radius:10px;

    padding:8px 18px !important;

}

.btn-login:hover{

    background:#8661cb;

}

.btn-outline-lavender{

    border:1px solid #9d7ad6;

    border-radius:10px;

    color:#9d7ad6 !important;

    padding:8px 18px !important;

}

.btn-outline-lavender:hover{

    background:#9d7ad6;

    color:white !important;

}

.badge-cart{

    background:#9d7ad6;

    color:white;

    border-radius:50%;

    padding:2px 8px;

    font-size:12px;

}

</style>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">

<div class="container">

<a
class="navbar-brand logo"
href="index.php">

THE FOUR LABEL

</a>

<button

class="navbar-toggler"

data-bs-toggle="collapse"

data-bs-target="#menu">

<span
class="navbar-toggler-icon">

</span>

</button>

<div

class="collapse navbar-collapse"

id="menu">

<ul class="navbar-nav ms-auto align-items-center">

<li class="nav-item">

<a

class="nav-link"

href="produk.php">

Produk

</a>

</li>

<li class="nav-item">

<a

class="nav-link"

href="custom-order.php">

Custom Order

</a>

</li>

<li class="nav-item">

<a

class="nav-link"

href="cart.php">

Keranjang

</a>

</li>

<?php if(isset($_SESSION['user'])){ ?>

<li class="nav-item">

<a

class="nav-link"

href="pesanan-saya.php">

Pesanan Saya

</a>

</li>

<li class="nav-item ms-2">

<a

class="nav-link btn-outline-lavender"

href="logout.php">

Logout

</a>

</li>

<?php }else{ ?>

<li class="nav-item ms-2">

<a

class="nav-link btn-login"

href="login.php">

Login

</a>

</li>

<?php } ?>

</ul>

</div>

</div>

</nav>

<div class="container mt-4">