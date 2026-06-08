<?php
include "auth.php";
require "../koneksi.php";

/* ambil kategori */

$kategori = mysqli_query(

    $koneksi,

    "SELECT * FROM kategori"

);

if(isset($_POST['submit'])){

    $file = "";

    if($_FILES['gambar']['name'] != ""){

        $file = time()."_".$_FILES['gambar']['name'];

        move_uploaded_file(

            $_FILES['gambar']['tmp_name'],

            "../image/".$file

        );

    }

    mysqli_query(

        $koneksi,

        "INSERT INTO produk(

        namaProduk,
        harga,
        gambar,
        deskripsi,
        idKategori

        )

        VALUES(

        '$_POST[nama]',
        '$_POST[harga]',
        '$file',
        '$_POST[deskripsi]',
        '$_POST[kategori]'

        )"

    );

    $idProduk = mysqli_insert_id($koneksi);

    mysqli_query(

        $koneksi,

        "INSERT INTO stok_produk(

        idProduk,
        jumlahStok,
        satuan

        )

        VALUES(

        '$idProduk',
        '$_POST[stok]',
        'pcs'

        )"

    );

    header("location:produk.php");
    exit;

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Tambah Produk</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background:#f7f3fc;

}

.content{

    padding:35px;

}

.card-form{

    border:none;

    border-radius:18px;

    box-shadow:
    0 3px 15px rgba(0,0,0,.08);

}

.title{

    color:#7c59c0;

    font-weight:600;

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

.form-control,
.form-select{

    border-radius:10px;

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

        <div class="card card-form">

            <div class="card-body p-4">

                <h3 class="title mb-4">

                    Tambah Produk

                </h3>

                <form
                method="POST"
                enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>Nama Produk</label>

                        <input
                        type="text"

                        name="nama"

                        class="form-control"

                        required>

                    </div>

                    <div class="mb-3">

                        <label>Harga</label>

                        <input
                        type="number"

                        name="harga"

                        class="form-control"

                        required>

                    </div>

                    <div class="mb-3">

                        <label>Stok</label>

                        <input
                        type="number"

                        name="stok"

                        class="form-control"

                        required>

                    </div>

                    <div class="mb-3">

                        <label>Deskripsi</label>

                        <textarea

                        name="deskripsi"

                        class="form-control"

                        rows="4"></textarea>

                    </div>

                    <div class="mb-3">

                        <label>Kategori</label>

                        <select

                        name="kategori"

                        class="form-select">

                            <?php while(
                                $k =
                                mysqli_fetch_assoc($kategori)
                            ){ ?>

                            <option
                            value="<?= $k['idKategori']; ?>">

                                <?= $k['namaKategori']; ?>

                            </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label>Gambar</label>

                        <input

                        type="file"

                        name="gambar"

                        class="form-control">

                    </div>

                    <button

                    name="submit"

                    class="btn btn-lavender">

                    Simpan Produk

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</div>

</body>

</html>