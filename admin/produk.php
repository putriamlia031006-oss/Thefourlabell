<?php

session_start();

require "../koneksi.php";

$query = mysqli_query(

    $koneksi,

    "SELECT *
    FROM produk
    ORDER BY idProduk DESC"

);

?>

<!DOCTYPE html>

<html>

<head>

<title>Data Produk</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#f7f3fc;

}

.content{

    padding:30px;

}

.judul{

    color:#6f42c1;

    font-weight:600;

}

.card-custom{

    border:none;

    border-radius:15px;

    box-shadow:
    0 2px 12px rgba(0,0,0,.08);

}

.btn-lavender{

    background:#9d7ad6;

    color:white;

    border:none;

}

.btn-lavender:hover{

    background:#8863cc;

    color:white;

}

.table thead{

    background:#ede4ff;

}

.table img{

    border-radius:8px;

    object-fit:cover;

}

.aksi a{

    text-decoration:none;

    margin-right:10px;

}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">

    <div class="col-md-2 p-0">

        <?php include "sidebar.php"; ?>

    </div>

    <div class="col-md-10 content">

        <div class="d-flex
        justify-content-between
        align-items-center
        mb-4">

            <h3 class="judul">

                Data Produk

            </h3>

            <a
            href="tambah-produk.php"

            class="btn btn-lavender">

            Tambah Produk

            </a>

        </div>

        <div class="card card-custom">

            <div class="card-body">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Nama Produk</th>

                            <th>Harga</th>

                            <th>Gambar</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while(
                        $row=
                        mysqli_fetch_assoc($query)
                    ){ ?>

                        <tr>

                            <td>

                                <?= $row['namaProduk']; ?>

                            </td>

                            <td>

                                Rp <?= number_format(
                                $row['harga']
                                ); ?>

                            </td>

                            <td>

                                <img
                                src="../image/<?= $row['gambar']; ?>"

                                width="70"

                                height="70">

                            </td>

                            <td class="aksi">

                                <a
                                href="edit-produk.php?id=<?= $row['idProduk']; ?>"

                                class="btn btn-sm btn-warning">

                                Edit

                                </a>

                                <a
                                href="hapus-produk.php?id=<?= $row['idProduk']; ?>"

                                class="btn btn-sm btn-danger">

                                Hapus

                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</div>

</body>

</html>