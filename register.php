<?php

require "koneksi.php";

$pesan = "";

if(isset($_POST['register'])){

    $nama = $_POST['nama'];

    $email = $_POST['email'];

    $noHp = $_POST['noHp'];

    $alamat = $_POST['alamat'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM user WHERE email='$email'"
    );

    if(mysqli_num_rows($cek) > 0){

        $pesan = "
        <div class='alert alert-danger'>
        Email sudah digunakan
        </div>";

    }else{

        mysqli_query(

            $koneksi,

            "INSERT INTO user
            (nama,email,password,role)

            VALUES(

            '$nama',

            '$email',

            '$password',

            'pelanggan'

            )"

        );

        $idUser = mysqli_insert_id($koneksi);

        mysqli_query(

            $koneksi,

            "INSERT INTO pelanggan
            (idUser,noHp,alamat)

            VALUES(

            '$idUser',

            '$noHp',

            '$alamat'

            )"

        );

        $pesan = "
        <div class='alert alert-success'>
        Register berhasil, silakan login
        </div>";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Register</title>

<link rel="stylesheet"

href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<style>

body{

    background:
    linear-gradient(
        135deg,
        #f8f4ff,
        #e5d0ff
    );

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    font-family:Arial;

}

.register-box{

    width:480px;

    background:white;

    padding:40px;

    border-radius:25px;

    box-shadow:
    0 10px 25px rgba(0,0,0,.1);

}

.judul{

    text-align:center;

    color:#8e44ad;

    font-weight:bold;

}

.sub{

    text-align:center;

    color:gray;

    margin-bottom:25px;

}

.form-control{

    border-radius:15px;

    min-height:50px;

}

textarea{

    resize:none;

}

.btn-register{

    background:#b57edc;

    border:none;

    width:100%;

    color:white;

    height:50px;

    border-radius:15px;

    font-weight:bold;

}

.btn-register:hover{

    background:#8e44ad;

}

.logo{

    font-size:55px;

    text-align:center;

}

</style>

</head>

<body>

<div class="register-box">

    <div class="logo">

        👕

    </div>

    <h2 class="judul">

        Daftar Akun

    </h2>

    <p class="sub">

        Buat akun untuk mulai berbelanja

    </p>

    <?= $pesan; ?>

    <form method="POST">

        <div class="mb-3">

            <label>Nama Lengkap</label>

            <input
            type="text"

            name="nama"

            class="form-control"

            required>

        </div>

        <div class="mb-3">

            <label>Email</label>

            <input
            type="email"

            name="email"

            class="form-control"

            required>

        </div>

        <div class="mb-3">

            <label>No HP</label>

            <input
            type="text"

            name="noHp"

            class="form-control"

            required>

        </div>

        <div class="mb-3">

            <label>Alamat</label>

            <textarea

            name="alamat"

            class="form-control"

            rows="3"

            required>

            </textarea>

        </div>

        <div class="mb-4">

            <label>Password</label>

            <input

            type="password"

            name="password"

            class="form-control"

            required>

        </div>

        <button

        name="register"

        class="btn btn-register">

        Register

        </button>

    </form>

    <div class="text-center mt-4">

        Sudah punya akun?

        <a

        href="login.php"

        style="
        color:#8e44ad;
        text-decoration:none;
        font-weight:bold;">

        Login disini

        </a>

    </div>

</div>

</body>

</html>