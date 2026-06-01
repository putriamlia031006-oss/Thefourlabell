<?php

require "koneksi.php";

include "navbar.php";

$cari = isset($_GET['cari'])
? $_GET['cari']
: "";

$kategori = isset($_GET['kategori'])
? $_GET['kategori']
: "";

$harga = isset($_GET['harga'])
? $_GET['harga']
: "";

$sql = "

SELECT *

FROM produk p

LEFT JOIN kategori k

ON p.idKategori = k.idKategori

WHERE 1=1

";

if($cari != ""){

    $sql .= "

    AND namaProduk
    LIKE '%$cari%'

    ";

}

if($kategori != ""){

    $sql .= "

    AND p.idKategori =
    '$kategori'

    ";

}

if($harga != ""){

    $sql .= "

    AND harga <=
    '$harga'

    ";

}

$query = mysqli_query(
    $koneksi,
    $sql
);

$listKategori = mysqli_query(

    $koneksi,

    "SELECT * FROM kategori"

);

?>

<!DOCTYPE html>

<html>

<head>

<title>Produk</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#f8f4ff;

}

.filter-box{

    background:white;

    padding:25px;

    border-radius:18px;

    box-shadow:
    0 3px 12px rgba(0,0,0,.08);

    margin-bottom:35px;

}

.judul{

    color:#7c59c0;

    font-weight:600;

}

.card-produk{

    border:none;

    border-radius:18px;

    overflow:hidden;

    box-shadow:
    0 3px 15px rgba(0,0,0,.08);

    transition:.3s;

    background:white;

}

.card-produk:hover{

    transform:translateY(-5px);

}

.card-produk img{

    width:100%;

    height:240px;

    object-fit:cover;

}

.nama-kategori{

    color:#8e44ad;

    font-size:14px;

}

.harga{

    color:#6f42c1;

    font-weight:bold;

    font-size:20px;

}

.btn-lavender{

    background:#9d7ad6;

    color:white;

    border:none;

}

.btn-lavender:hover{

    background:#8661cb;

    color:white;

}

</style>

</head>

<body>

<div class="container py-5">

    <h2 class="judul mb-4">

        Katalog Produk

    </h2>

    <!-- FILTER -->

    <div class="filter-box">

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <input

                    class="form-control"

                    name="cari"

                    value="<?= $cari; ?>"

                    placeholder="Cari produk">

                </div>

                <div class="col-md-3">

                    <select

                    name="kategori"

                    class="form-select">

                        <option value="">

                            Semua Kategori

                        </option>

                        <?php while(
                            $kat =
                            mysqli_fetch_assoc(
                            $listKategori
                            )
                        ){ ?>

                        <option

                        value="<?= $kat['idKategori']; ?>"

                        <?= ($kategori == $kat['idKategori'])
                        ? "selected"
                        : ""; ?>>

                        <?= $kat['namaKategori']; ?>

                        </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="col-md-3">

                    <input

                    name="harga"

                    class="form-control"

                    value="<?= $harga; ?>"

                    placeholder="Harga maksimal">

                </div>

                <div class="col-md-2">

                    <button

                    class="btn btn-lavender w-100">

                    Cari

                    </button>

                </div>

            </div>

        </form>

    </div>

    <!-- PRODUK -->

    <div class="row">

        <?php while(
            $row =
            mysqli_fetch_assoc(
            $query
            )
        ){ ?>

        <div class="col-md-4 mb-4">

            <div class="card card-produk">

                <img

                src="image/<?= $row['gambar']; ?>">

                <div class="p-3">

                    <small class="nama-kategori">

                        <?= $row['namaKategori']; ?>

                    </small>

                    <h5>

                        <?= $row['namaProduk']; ?>

                    </h5>

                    <div class="harga">

                        Rp <?= number_format(
                        $row['harga']
                        ); ?>

                    </div>

                    <a

                    href="detail-produk.php?id=<?= $row['idProduk']; ?>"

                    class="btn btn-lavender w-100 mt-3">

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