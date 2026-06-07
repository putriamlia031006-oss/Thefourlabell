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

        // Cek password modern dari password_hash()
        if (password_verify($password, $passwordDb)) {
            $loginBerhasil = true;
        }

        // Cek password lama MD5
        elseif (strlen($passwordDb) == 32 && md5($password) == $passwordDb) {
            $loginBerhasil = true;

            $passwordBaru = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query(
                $koneksi,
                "UPDATE user 
                 SET password='$passwordBaru'
                 WHERE idUser='{$data['idUser']}'"
            );
        }

        // Cek password plain text
        elseif ($password == $passwordDb) {
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background:
        radial-gradient(circle at top left, #f7edff 0%, transparent 35%),
        radial-gradient(circle at bottom right, #ead6ff 0%, transparent 35%),
        linear-gradient(135deg, #f3e8ff, #eadcff, #f9f5ff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 28px 15px;
}

.login-box {
    width: 100%;
    max-width: 450px;
    background:
        radial-gradient(circle at top center, rgba(185, 132, 224, 0.13), transparent 35%),
        rgba(255,255,255,0.96);
    padding: 42px 42px 36px;
    border-radius: 34px;
    box-shadow: 0 26px 65px rgba(125, 82, 180, 0.24);
    border: 1px solid rgba(176, 127, 220, 0.28);
}

.logo {
    width: 112px;
    height: 112px;
    margin: 0 auto 18px;
    border-radius: 30px;
    background: linear-gradient(135deg, #f7f0ff, #ffffff);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 16px 34px rgba(142, 85, 215, 0.20);
    border: 1.5px solid #eadcf7;
    padding: 9px;
}

.logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 24px;
}

.judul {
    color: #7437b8;
    font-weight: 850;
    text-align: center;
    margin-bottom: 7px;
    font-size: 32px;
    letter-spacing: -0.5px;
}

.subjudul {
    text-align: center;
    color: #8a7899;
    margin-bottom: 28px;
    font-size: 15px;
}

.alert {
    border-radius: 16px;
    font-size: 14px;
    padding: 13px 16px;
    border: none;
    background: #fff0f3;
    color: #b02a37;
    margin-bottom: 20px;
}

.form-label {
    font-weight: 700;
    color: #3f176b;
    margin-bottom: 8px;
    font-size: 14px;
}

.input-group-custom {
    position: relative;
    margin-bottom: 18px;
}

.input-group-custom i {
    position: absolute;
    top: 50%;
    left: 17px;
    transform: translateY(-50%);
    color: #9c6fd0;
    font-size: 15px;
}

.form-control {
    height: 52px;
    border-radius: 16px;
    border: 1.5px solid #e2d8ea;
    padding: 14px 16px 14px 46px;
    background: #fbfaff;
    color: #342344;
    font-size: 15px;
    transition: 0.25s;
}

.form-control:focus {
    border-color: #a765d6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(167, 101, 214, 0.14);
}

.form-control::placeholder {
    color: #a59aad;
}

.btn-login {
    width: 100%;
    border: none;
    border-radius: 17px;
    padding: 15px;
    margin-top: 8px;
    font-weight: 800;
    color: white;
    background: linear-gradient(135deg, #b573df, #8e43bd);
    box-shadow: 0 14px 28px rgba(142, 67, 189, 0.28);
    transition: 0.25s;
}

.btn-login:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #a65bd6, #7c35ac);
    box-shadow: 0 18px 35px rgba(142, 67, 189, 0.35);
    color: white;
}

.register-text {
    text-align: center;
    margin-top: 22px;
    color: #8a7899;
    font-size: 14px;
}

.link-register {
    color: #7b35b4;
    font-weight: 700;
    text-decoration: none;
}

.link-register:hover {
    text-decoration: underline;
}

@media (max-width: 576px) {
    .login-box {
        padding: 36px 26px 32px;
        border-radius: 28px;
    }

    .logo {
        width: 100px;
        height: 100px;
    }

    .judul {
        font-size: 29px;
    }
}
</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        <img src="assets/logoT4L.png" alt="Logo The Four Label">
    </div>

    <h2 class="judul">
        Login Akun
    </h2>

    <p class="subjudul">
        Selamat datang kembali di The Four Label
    </p>

    <?php if ($error != "") { ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php } ?>

    <form method="POST" autocomplete="off">

        <label class="form-label">Email</label>
        <div class="input-group-custom">
            <i class="fa-solid fa-envelope"></i>
            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email"
                autocomplete="new-email"
                required>
        </div>

        <label class="form-label">Password</label>
        <div class="input-group-custom">
            <i class="fa-solid fa-lock"></i>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password"
                autocomplete="new-password"
                required>
        </div>

        <button
            type="submit"
            name="login"
            class="btn-login">
            Login
        </button>

    </form>

    <div class="register-text">
        Belum punya akun?
        <a href="register.php" class="link-register">
            Daftar di sini
        </a>
    </div>

</div>

</body>

</html>