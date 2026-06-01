<?php

require "koneksi.php";

include "navbar.php";

$id = $_GET['id'];

$query = mysqli_query(

    $koneksi,

    "SELECT *

    FROM produk p

    LEFT JOIN kategori k

    ON p.idKategori = k.idKategori

    WHERE idProduk='$id'"

);

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>

<html>

<head>

<title>Detail Produk</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#f7f3fc;

}

.detail-box{

    background:white;

    border-radius:20px;

    padding:35px;

    box-shadow:
    0 4px 20px rgba(0,0,0,.08);

}

.gambar{

    width:100%;

    height:450px;

    object-fit:cover;

    border-radius:15px;

}

.kategori{

    background:#e8dbff;

    color:#7a56c5;

    padding:8px 15px;

    border-radius:20px;

    display:inline-block;

    margin-bottom:20px;

}

.nama{

    color:#5d3ea8;

    font-weight:600;

}

.harga{

    color:#8e44ad;

    font-size:32px;

    font-weight:bold;

    margin:20px 0;

}

.deskripsi{

    color:#666;

    line-height:1.8;

}

.qty{

    width:100px;

}

.btn-cart{

    background:#9d7ad6;

    border:none;

    color:white;

    padding:12px 25px;

    border-radius:10px;

}

.btn-cart:hover{

    background:#845ec2;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="detail-box">

<div class="row align-items-center">

    <div class="col-md-5">

        <img

        src="image/<?= $data['gambar']; ?>"

        class="gambar">

    </div>

    <div class="col-md-7">

        <div class="kategori">

            <?= $data['namaKategori']; ?>

        </div>

        <h1 class="nama">

            <?= $data['namaProduk']; ?>

        </h1>

        <div class="harga">

            Rp <?= number_format($data['harga']); ?>

        </div>

        <p class="deskripsi">

            <?= $data['deskripsi']; ?>

        </p>

        <form
        action="cart.php"
        method="POST">

            <input
            type="hidden"

            name="idProduk"

            value="<?= $id; ?>">

            <div class="d-flex gap-3 mt-4">

                <input

                type="number"

                name="qty"

                value="1"

                min="1"

                class="form-control qty">

                <button
                class="btn btn-cart">

                Tambah ke Keranjang

                </button>

            </div>

        </form>

    </div>

</div>

</div>

</div>

<?php include "footer.php"; ?>

</body>

</html>