<?php
require "koneksi.php";

$pesan = "";

if (isset($_POST['register'])) {

    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $noHp = mysqli_real_escape_string($koneksi, trim($_POST['noHp']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $passwordInput = $_POST['password'];

    if ($nama == "" || $email == "" || $noHp == "" || $alamat == "" || $passwordInput == "") {

        $pesan = "
        <div class='alert alert-danger'>
            Semua data wajib diisi.
        </div>";

    } else {

        $password = password_hash($passwordInput, PASSWORD_DEFAULT);

        $cek = mysqli_query(
            $koneksi,
            "SELECT * FROM user WHERE email='$email'"
        );

        if (!$cek) {
            die("Query cek email error: " . mysqli_error($koneksi));
        }

        if (mysqli_num_rows($cek) > 0) {

            $pesan = "
            <div class='alert alert-danger'>
                Email sudah digunakan.
            </div>";

        } else {

            $simpanUser = mysqli_query(
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
                    'pelanggan'
                )"
            );

            if (!$simpanUser) {
                die("Gagal menyimpan user: " . mysqli_error($koneksi));
            }

            $idUser = mysqli_insert_id($koneksi);

            $simpanPelanggan = mysqli_query(
                $koneksi,
                "INSERT INTO pelanggan (
                    idUser,
                    noHp,
                    alamat
                ) VALUES (
                    '$idUser',
                    '$noHp',
                    '$alamat'
                )"
            );

            if (!$simpanPelanggan) {
                die("Gagal menyimpan pelanggan: " . mysqli_error($koneksi));
            }

            echo "
            <script>
                alert('Register berhasil, silakan login.');
                window.location.href = 'login.php';
            </script>";
            exit;

        }

    }

}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Register - The Four Label</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<style>
* {
    box-sizing: border-box;
}

body {
    background:
        linear-gradient(
            135deg,
            #f8f4ff,
            #ead7ff
        );

    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Arial, sans-serif;
    padding: 20px;
}

.register-box {
    width: 500px;
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
    box-shadow: 0 10px 22px rgba(142, 68, 173, 0.22);
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
    background: white;
}

textarea {
    resize: none;
}

.btn-register {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    border: none;
    width: 100%;
    color: white;
    height: 52px;
    border-radius: 15px;
    font-weight: 800;
    transition: 0.25s ease;
}

.btn-register:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(142, 68, 173, 0.22);
}

.link-login {
    color: #8e44ad;
    text-decoration: none;
    font-weight: bold;
}

.link-login:hover {
    text-decoration: underline;
}

@media (max-width: 576px) {
    .register-box {
        width: 100%;
        padding: 30px 24px;
    }
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
            <label class="form-label">Nama Lengkap</label>
            <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Masukkan nama lengkap"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">No HP</label>
            <input
                type="text"
                name="noHp"
                class="form-control"
                placeholder="Masukkan nomor HP"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea
                name="alamat"
                class="form-control"
                rows="3"
                placeholder="Masukkan alamat lengkap"
                required></textarea>
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
            Register
        </button>

    </form>

    <div class="text-center mt-4">
        Sudah punya akun?
        <a href="login.php" class="link-login">
            Login di sini
        </a>
    </div>

</div>

</body>

</html>