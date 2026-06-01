<?php

session_start();

require "koneksi.php";

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];

    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM user WHERE email='$email'"
    );

    $data = mysqli_fetch_assoc($query);

    if(
        $data &&
        password_verify(
            $password,
            $data['password']
        )
    ){

        $_SESSION['user'] = $data;

        if($data['role'] == "admin"){

    header("Location: admin/index.php");
    exit;

}else{

    header("Location: index.php");
    exit;

}
    }else{

        $error = "Email atau password salah";

    }

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Login Konveksi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

    background: linear-gradient(
        135deg,
        #f8f4ff,
        #e7d4ff
    );

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    font-family:Arial;

}

.login-box{

    background:white;

    width:420px;

    padding:40px;

    border-radius:25px;

    box-shadow:
    0 10px 30px rgba(0,0,0,.12);

}

.judul{

    color:#8e44ad;

    font-weight:bold;

    text-align:center;

    margin-bottom:10px;

}

.subjudul{

    text-align:center;

    color:gray;

    margin-bottom:30px;

}

.form-control{

    height:50px;

    border-radius:15px;

}

.btn-login{

    background:#b57edc;

    border:none;

    height:50px;

    border-radius:15px;

    width:100%;

    color:white;

    font-weight:bold;

}

.btn-login:hover{

    background:#8e44ad;

}

.logo{

    font-size:60px;

    text-align:center;

    margin-bottom:10px;

}

</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        👕
    </div>

    <h2 class="judul">

        THE FOUR LABEL

    </h2>

    <p class="subjudul">

        Login untuk melanjutkan belanja

    </p>

    <?php if($error != ""){ ?>

        <div class="alert alert-danger">

            <?= $error; ?>

        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label>Email</label>

            <input
            type="email"
            name="email"

            class="form-control"

            required>

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
        name="login"

        class="btn btn-login">

        Login

        </button>

    </form>

    <div class="text-center mt-4">

        Belum punya akun?

        <a
        href="register.php"

        style="
        color:#8e44ad;
        text-decoration:none;
        font-weight:bold;">

        Daftar disini

        </a>

    </div>

</div>

</body>

</html>