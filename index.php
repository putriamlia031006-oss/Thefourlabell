<?php
require "koneksi.php";
include "navbar.php";

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk LIMIT 6"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konveksi Custom</title>

    <style>

        body{
            background:#f8f4ff;
            font-family:Arial, sans-serif;
        }

        /* HERO */
        .hero{
            background: linear-gradient(
                rgba(155,89,182,0.8),
                rgba(186,104,200,0.8)
            ),
            url('upload/banner.jpg');

            background-size:cover;
            background-position:center;

            height:420px;

            display:flex;
            flex-direction:column;

            justify-content:center;
            align-items:center;

            color:white;

            border-radius:0 0 40px 40px;

            text-align:center;

            margin-bottom:50px;
        }

        .hero h1{
            font-size:52px;
            font-weight:bold;
        }

        .hero p{
            font-size:20px;
        }

        .btn-custom{
            background:#fff;
            color:#8e44ad;
            padding:12px 25px;
            border-radius:30px;
            text-decoration:none;
            font-weight:bold;
        }

        /* PRODUK */

        .judul-section{
            color:#7b2cbf;
            font-weight:bold;
            margin-bottom:30px;
        }

        .card-produk{

            border:none;

            border-radius:20px;

            overflow:hidden;

            background:white;

            transition:0.3s;

            box-shadow:0 4px 15px rgba(0,0,0,.08);

            margin-bottom:25px;
        }

        .card-produk:hover{

            transform:translateY(-8px);

            box-shadow:0 10px 25px rgba(0,0,0,.15);

        }

        .card-produk img{

            width:100%;

            object-fit:cover;

            border-radius:15px;

        }

        .harga{

            color:#8e44ad;

            font-size:20px;

            font-weight:bold;

        }

        .btn-detail{

            background:#b57edc;

            color:white;

            border:none;

            border-radius:25px;

            width:100%;
        }

        .btn-detail:hover{

            background:#8e44ad;

            color:white;

        }

        .hero-banner{
    width: 100%;
    height: 300px;
    background-image: url('assets/lavender2.jpg'); /* ganti dengan gambar banner kamu */
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

}

        .hero-overlay{
    background: rgba(0,0,0,0.5); /* efek gelap biar teks jelas */
    width: 100%;
    height: 100%;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.hero-overlay h1{
    font-size: 42px;
    font-weight: bold;
}

.hero-overlay p{
    font-size: 18px;
    margin-top: 10px;
}

    </style>

</head>

<body>

<div class="hero-banner">
    <div class="hero-overlay">
        <h1>Katalog Produk</h1>
        <p>Konveksi Custom & Ready Stock</p>
        <a href="produk.php" class="btn-custom">
        Lihat Produk
    </a>
    </div>
</div>
    


<div class="container">

    <h2 class="text-center judul-section">
        Produk Terbaru
    </h2>

    <div class="row">

        <?php while($row=mysqli_fetch_assoc($query)){ ?>

        <div class="col-md-4">

            <div class="card card-produk p-3">

                <img
                src="image/<?= $row['gambar']; ?>"
                height="240">

                <div class="mt-3">

                    <h4>
                        <?= $row['namaProduk']; ?>
                    </h4>

                    <p class="harga">
                        Rp <?= number_format($row['harga']); ?>
                    </p>

                    <a
                    href="detail-produk.php?id=<?= $row['idProduk']; ?>"
                    class="btn btn-detail">

                    Detail Produk

                    </a>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<?php include "footer.php"; ?>

</body>
</html>