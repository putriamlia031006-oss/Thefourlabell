<?php
session_start();
require "../koneksi.php";

if (!isset($_SESSION['idUser']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pesan = "";

if (isset($_POST['register'])) {

    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $passwordInput = trim($_POST['password']);

    if ($nama == "" || $email == "" || $passwordInput == "") {

        $pesan = "
        <div class='alert alert-danger'>
            Semua data wajib diisi.
        </div>";

    } else {

        $cek = mysqli_query(
            $koneksi,
            "SELECT * FROM user WHERE email='$email'"
        );

        if (mysqli_num_rows($cek) > 0) {

            $pesan = "
            <div class='alert alert-danger'>
                Email sudah digunakan.
            </div>";

        } else {

            $password = password_hash($passwordInput, PASSWORD_DEFAULT);

            $simpan = mysqli_query(
                $koneksi,
                "INSERT INTO user (
                    nama,
                    email,
                    password,
                    role
                ) VALUES (
                    '$nama',
                    '$email',
                    '$password',
                    'admin'
                )"
            );

            if ($simpan) {
                $pesan = "
                <div class='alert alert-success'>
                    Admin baru berhasil ditambahkan.
                </div>";
            } else {
                $pesan = "
                <div class='alert alert-danger'>
                    Gagal menambahkan admin: " . mysqli_error($koneksi) . "
                </div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Admin - The Four Label</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<style>
body {
    background: linear-gradient(135deg, #f8f4ff, #ead7ff);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Arial, sans-serif;
    padding: 20px;
}

.register-box {
    width: 480px;
    background: white;
    padding: 40px;
    border-radius: 28px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.16);
    border: 1px solid #eadcff;
}

.logo {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    font-size: 32px;
}

.judul {
    text-align: center;
    color: #7b3fb2;
    font-weight: 850;
    margin-bottom: 8px;
}

.sub {
    text-align: center;
    color: gray;
    margin-bottom: 25px;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
}

.form-control {
    border-radius: 15px;
    min-height: 50px;
    border: 1px solid #ddd;
    background: #fcfbff;
    padding: 12px 14px;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
}

.btn-register {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    border: none;
    width: 100%;
    color: white;
    height: 52px;
    border-radius: 15px;
    font-weight: 800;
}

.btn-register:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
}

.link-kembali {
    color: #8e44ad;
    text-decoration: none;
    font-weight: bold;
}

.link-kembali:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="register-box">

    <div class="logo">👤</div>

    <h2 class="judul">Tambah Admin</h2>

    <p class="sub">
        Halaman ini hanya untuk admin
    </p>

    <?= $pesan; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Admin</label>
            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Masukkan nama admin"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Admin</label>
            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email admin"
                required>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password"
                required>
        </div>

        <button name="register" class="btn btn-register">
            Tambah Admin
        </button>

    </form>

    <div class="text-center mt-4">
        <a href="index.php" class="link-kembali">
            Kembali ke Dashboard
        </a>
    </div>

</div>

</body>
</html>