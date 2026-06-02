<?php
session_start();

require "koneksi.php";

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = $_POST['password'];

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

        /*
            1. Cek password modern dari password_hash()
            Biasanya diawali $2y$ atau $argon
        */
        if (password_verify($password, $passwordDb)) {
            $loginBerhasil = true;
        }

        /*
            2. Cek password lama MD5
            Contoh: 8b6d9f5dd2385331a05b2e2d8a94f5a0
        */
        elseif (strlen($passwordDb) == 32 && md5($password) == $passwordDb) {
            $loginBerhasil = true;

            // Update otomatis ke password_hash()
            $passwordBaru = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query(
                $koneksi,
                "UPDATE user 
                 SET password='$passwordBaru'
                 WHERE idUser='{$data['idUser']}'"
            );
        }

        /*
            3. Cek password plain text
            Contoh di database: putriadmin
        */
        elseif ($password == $passwordDb) {
            $loginBerhasil = true;

            // Update otomatis ke password_hash()
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

            $_SESSION['user'] = $data;
            $_SESSION['idUser'] = $data['idUser'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['role'] = $role;

            if ($role == "admin") {
                header("Location: admin/index.php");
                exit;
            } else {
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

<title>Login - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f8f4ff, #e7d4ff);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', Arial, sans-serif;
    padding: 20px;
}

.login-box {
    background: white;
    width: 430px;
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
    color: #7b3fb2;
    font-weight: 850;
    text-align: center;
    margin-bottom: 10px;
}

.subjudul {
    text-align: center;
    color: gray;
    margin-bottom: 30px;
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
    background: white;
}

.btn-login {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    border: none;
    height: 52px;
    border-radius: 15px;
    width: 100%;
    color: white;
    font-weight: 800;
    transition: 0.25s ease;
}

.btn-login:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(142, 68, 173, 0.22);
}

.link-register {
    color: #8e44ad;
    text-decoration: none;
    font-weight: bold;
}

.link-register:hover {
    text-decoration: underline;
}

@media (max-width: 576px) {
    .login-box {
        width: 100%;
        padding: 30px 24px;
    }
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

    <?php if ($error != "") { ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email"
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

        <button
            type="submit"
            name="login"
            class="btn btn-login">
            Login
        </button>

    </form>

    <div class="text-center mt-4">
        Belum punya akun?
        <a href="register.php" class="link-register">
            Daftar di sini
        </a>
    </div>

</div>

</body>

</html>