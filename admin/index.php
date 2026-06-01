<?php

require "../koneksi.php";

$produk = mysqli_num_rows(

    mysqli_query(

        $koneksi,

        "SELECT * FROM produk"

    )

);

$pesanan = mysqli_num_rows(

    mysqli_query(

        $koneksi,

        "SELECT * FROM pesanan"

    )

);

?>

<!DOCTYPE html>

<html>

<head>

<title>Dashboard Admin</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#f8f4ff;

    font-family:Arial;

}

/* SIDEBAR AREA */

.sidebar-area{

    background:#b57edc;

    min-height:100vh;

    padding:0;

}

/* CONTENT */

.content{

    padding:35px;

}

.judul{

    color:#7b2cbf;

    font-weight:bold;

    margin-bottom:30px;

}

/* CARD */

.card-dashboard{

    border:none;

    border-radius:20px;

    padding:25px;

    background:white;

    box-shadow:
    0 6px 18px rgba(0,0,0,.08);

    transition:.3s;

}

.card-dashboard:hover{

    transform:translateY(-5px);

}

.icon{

    font-size:45px;

}

.total{

    font-size:38px;

    font-weight:bold;

    color:#8e44ad;

}

.label{

    color:gray;

    font-size:18px;

}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

    <!-- Sidebar -->

    <div class="col-md-2 sidebar-area">

        <?php include "sidebar.php"; ?>

    </div>

    <!-- Content -->

    <div class="col-md-10 content">

        <h2 class="judul">

            Dashboard Admin

        </h2>

        <div class="row">

            <!-- Produk -->

            <div class="col-md-4 mb-4">

                <div class="card-dashboard">

                    <div class="icon">

                        👕

                    </div>

                    <div class="total">

                        <?= $produk; ?>

                    </div>

                    <div class="label">

                        Total Produk

                    </div>

                </div>

            </div>

            <!-- Pesanan -->

            <div class="col-md-4 mb-4">

                <div class="card-dashboard">

                    <div class="icon">

                        📦

                    </div>

                    <div class="total">

                        <?= $pesanan; ?>

                    </div>

                    <div class="label">

                        Total Pesanan

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>

</body>

</html>