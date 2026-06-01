<?php

require "koneksi.php";
include "navbar.php";

/* =====================
   AMBIL DATA KATEGORI
===================== */

$queryKategori = mysqli_query(
    $koneksi,
    "SELECT * FROM kategori"
);

/* =====================
   FILTER PRODUK
===================== */

if(isset($_GET['cari'])){

    $keyword = $_GET['cari'];

    $queryProduk = mysqli_query(

        $koneksi,

        "SELECT *

        FROM produk p

        LEFT JOIN kategori k
        ON p.idKategori = k.idKategori

        WHERE namaProduk
        LIKE '%$keyword%'"

    );

}elseif(isset($_GET['kategori'])){

    $kategori = $_GET['kategori'];

    $queryProduk = mysqli_query(

        $koneksi,

        "SELECT *

        FROM produk p

        LEFT JOIN kategori k
        ON p.idKategori = k.idKategori

        WHERE p.idKategori = '$kategori'"

    );

}else{

    $queryProduk = mysqli_query(

        $koneksi,

        "SELECT *

        FROM produk p

        LEFT JOIN kategori k
        ON p.idKategori = k.idKategori"

    );

}

$totalProduk = mysqli_num_rows($queryProduk);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>Produk</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

    background:#f6f3fc;

}

/* HERO */

.hero{

    background:
    linear-gradient(
        135deg,
        #9d7ad6,
        #c6a6ff
    );

    color:white;

    padding:70px;

    text-align:center;

    border-radius:0 0 35px 35px;

}

/* CATEGORY */

.category-scroll{

    display:flex;

    gap:12px;

    overflow-x:auto;

    padding-bottom:10px;

    scrollbar-width:none;

}

.category-scroll::-webkit-scrollbar{

    display:none;

}

.category-pill{

    text-decoration:none;

    background:white;

    color:#7b4bc6;

    padding:10px 22px;

    border-radius:30px;

    border:1px solid #ddd;

    white-space:nowrap;

    transition:.3s;

}

.category-pill:hover{

    background:#9d7ad6;

    color:white;

}

/* CARD */

.card-produk{

    border:none;

    border-radius:20px;

    overflow:hidden;

    box-shadow:
    0 3px 15px rgba(0,0,0,.08);

    transition:.3s;

}

.card-produk:hover{

    transform:translateY(-8px);

}

.image-box{

    overflow:hidden;

}

.image-box img{

    width:100%;

    height:250px;

    object-fit:cover;

    transition:.4s;

}

.card-produk:hover img{

    transform:scale(1.08);

}

.product-title{

    min-height:55px;

    font-weight:600;

}

.btn-lavender{

    background:#9d7ad6;

    color:white;

    border:none;

}

.btn-lavender:hover{

    background:#835dc8;

    color:white;

}

.cart-toast{

    position:fixed;

    bottom:30px;

    right:30px;

    background:#198754;

    color:white;

    padding:15px 25px;

    border-radius:40px;

    display:none;

}

.show-toast{

    display:block;

}

</style>

</head>

<body>

<!-- HERO -->

<div class="hero">

    <h1>Katalog Produk</h1>

    <p>
        Konveksi Custom & Ready Stock
    </p>

</div>

<div class="container py-5">

    <!-- SEARCH -->

    <form method="GET">

        <div class="input-group mb-5">

            <input

            type="text"

            name="cari"

            class="form-control"

            placeholder="Cari produk...">

            <button class="btn btn-lavender">

                Cari

            </button>

        </div>

    </form>

    <!-- CATEGORY -->

    <div class="category-scroll mb-5">

        <a
        href="produk.php"

        class="category-pill">

        Semua

        </a>

        <?php while($kat = mysqli_fetch_assoc($queryKategori)){ ?>

        <a

        href="produk.php?kategori=<?= $kat['idKategori']; ?>"

        class="category-pill">

        <?= $kat['namaKategori']; ?>

        </a>

        <?php } ?>

    </div>

    <!-- PRODUK -->

    <div class="row">

        <?php

        if($totalProduk < 1){

            echo "

            <div class='text-center'>

                <h4>Produk tidak ditemukan</h4>

            </div>

            ";

        }

        while($row = mysqli_fetch_assoc($queryProduk)){

        ?>

        <div class="col-md-3 mb-4">

            <div class="card card-produk h-100">

                <div class="image-box">

                    <img

                    src="image/<?= $row['gambar']; ?>"

                    alt="<?= $row['namaProduk']; ?>">

                </div>

                <div class="card-body d-flex flex-column">

                    <h5 class="product-title">

                        <?= $row['namaProduk']; ?>

                    </h5>

                    <p class="text-muted">

                        <?= substr(
                            $row['deskripsi'] ?? "",
                            0,
                            60
                        ); ?>...

                    </p>

                    <h5 class="mt-auto">

                        Rp <?= number_format(
                            $row['harga']
                        ); ?>

                    </h5>

                    <div class="d-flex gap-2">

                        <a

                        href="detail-produk.php?id=<?= $row['idProduk']; ?>"

                        class="btn btn-outline-secondary w-100">

                        Detail

                        </a>

                        <button

                        class="btn btn-lavender add-cart">

                            <i class="fas fa-cart-plus"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <?php } ?>

    </div>

</div>

<div

id="toast"

class="cart-toast">

Produk masuk keranjang

</div>

<script>

document.querySelectorAll(
".add-cart"
).forEach(btn=>{

    btn.onclick = ()=>{

        const toast =
        document.getElementById(
        "toast"
        );

        toast.classList.add(
        "show-toast"
        );

        setTimeout(()=>{

            toast.classList.remove(
            "show-toast"
            );

        },2000);

    };

});

</script>

</body>

</html>