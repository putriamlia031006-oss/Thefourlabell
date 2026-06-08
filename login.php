<?php
session_start();
require "koneksi.php";

$error = "";

if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == "admin") {
        header("Location: admin/index.php");
        exit;
    } elseif ($_SESSION['role'] == "pelanggan") {
        header("Location: index.php");
        exit;
    }
}

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($koneksi, trim($_POST['email_pelanggan']));
    $password = $_POST['password_pelanggan'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM user WHERE email='$email' LIMIT 1"
    );

    if (!$query) {
        die("Query login error: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_assoc($query);

    if ($data) {

        $passwordDb = $data['password'];
        $loginBerhasil = false;

        if (password_verify($password, $passwordDb)) {
            $loginBerhasil = true;
        } elseif (strlen($passwordDb) == 32 && md5($password) == $passwordDb) {
            $loginBerhasil = true;

            $passwordBaru = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query(
                $koneksi,
                "UPDATE user 
                 SET password='$passwordBaru'
                 WHERE idUser='{$data['idUser']}'"
            );
        } elseif ($password == $passwordDb) {
            $loginBerhasil = true;

            $passwordBaru = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query(
                $koneksi,
                "UPDATE user 
                 SET password='$passwordBaru'
                 WHERE idUser='{$data['idUser']}'"
            );
        }

        if ($loginBerhasil) {

            $role = strtolower(trim($data['role']));

            if ($role == "admin") {
                $error = "Akun admin tidak bisa login di halaman pelanggan. Silakan login melalui halaman admin.";
            } else {

                $_SESSION['user'] = $data;
                $_SESSION['idUser'] = $data['idUser'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['role'] = "pelanggan";

                header("Location: index.php");
                exit;
            }

        } else {
            $error = "Password salah";
        }

    } else {
        $error = "Email tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login Pelanggan - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    min-height: 100vh;
    background: linear-gradient(135deg, #fbf7ff, #eadcff);
    font-family: 'Segoe UI', Arial, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-box {
    width: 100%;
    max-width: 430px;
    background: white;
    border-radius: 28px;
    padding: 38px;
    border: 1px solid #eadcff;
    box-shadow: 0 16px 40px rgba(142, 68, 173, 0.15);
}

.logo-box {
    width: 88px;
    height: 88px;
    border-radius: 24px;
    background: #fbf7ff;
    border: 1px solid #eadcff;
    margin: 0 auto 18px;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 18px;
}

.title {
    color: #6f2da8;
    font-weight: 850;
    text-align: center;
    margin-bottom: 6px;
}

.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 28px;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
}

.form-control {
    height: 50px;
    border-radius: 15px;
    background: #fcfbff;
    border: 1px solid #ddd;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
}

.btn-login {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    height: 52px;
    border-radius: 16px;
    font-weight: 850;
    width: 100%;
}

.btn-login:hover {
    color: white;
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
}

.link {
    color: #8e44ad;
    font-weight: 800;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="login-box">

    <div class="logo-box">
        <img src="assets/logo.png" alt="Logo The Four Label">
    </div>

    <h2 class="title">Login Pelanggan</h2>
    <p class="subtitle">Masuk untuk mulai belanja di The Four Label</p>

    <?php if ($error != "") { ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Email Pelanggan</label>
            <input 
                type="email" 
                name="email_pelanggan" 
                id="email_pelanggan"
                class="form-control" 
                placeholder="Masukkan email pelanggan"
                autocomplete="off"
                required>
        </div>

        <div class="mb-4">
            <label class="form-label">Password Pelanggan</label>
            <input 
                type="password" 
                name="password_pelanggan" 
                id="password_pelanggan"
                class="form-control" 
                placeholder="Masukkan password pelanggan"
                autocomplete="new-password"
                required>
        </div>

        <button type="submit" name="login" class="btn-login">
            Login
        </button>

    </form>

    <div class="text-center mt-4">
        Belum punya akun?
        <a href="register.php" class="link">Daftar di sini</a>
    </div>

</div>

</body>
</html>