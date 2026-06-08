<?php
session_start();
require "../koneksi.php";

$error = "";

/* 
   Kalau sudah login sebagai admin,
   langsung masuk dashboard admin.
*/
if (isset($_SESSION['user']) && isset($_SESSION['role'])) {

    if (strtolower(trim($_SESSION['role'])) == "admin") {
        header("Location: index.php");
        exit;
    }

    if (strtolower(trim($_SESSION['role'])) == "pelanggan") {
        $error = "Akun pelanggan tidak bisa masuk ke halaman admin.";
    }
}

/* =========================
   PROSES LOGIN ADMIN
========================= */
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

        /* Password dari password_hash */
        if (password_verify($password, $passwordDb)) {
            $loginBerhasil = true;
        }

        /* Password lama MD5 */
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

        /* Password plain text */
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

            if ($role != "admin") {
                $error = "Akun ini bukan akun admin.";
            } else {

                $_SESSION['user'] = $data;
                $_SESSION['idUser'] = $data['idUser'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['role'] = "admin";

                header("Location: index.php");
                exit;
            }

        } else {
            $error = "Password salah.";
        }

    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login Admin - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    margin: 0;
    background:
        radial-gradient(circle at top left, rgba(234, 220, 255, 0.95) 0%, transparent 34%),
        radial-gradient(circle at bottom right, rgba(181, 126, 220, 0.55) 0%, transparent 38%),
        linear-gradient(135deg, #4d1f7c, #8e44ad);
    font-family: 'Segoe UI', Arial, sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-box {
    width: 100%;
    max-width: 440px;
    background: rgba(255, 255, 255, 0.97);
    border-radius: 30px;
    padding: 38px;
    border: 1px solid rgba(255,255,255,0.35);
    box-shadow: 0 20px 50px rgba(35, 13, 55, 0.28);
}

.logo-box {
    width: 92px;
    height: 92px;
    border-radius: 25px;
    background: #fbf7ff;
    border: 1px solid #eadcff;
    margin: 0 auto 18px;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 24px rgba(142, 68, 173, 0.16);
}

.logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 19px;
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
    font-weight: 750;
    color: #4b2e63;
}

.form-control {
    height: 52px;
    border-radius: 16px;
    background: #fcfbff;
    border: 1px solid #ddd;
    padding: 12px 15px;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
    background: white;
}

.btn-login {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    height: 54px;
    border-radius: 17px;
    font-weight: 850;
    width: 100%;
    transition: 0.25s;
}

.btn-login:hover {
    color: white;
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(142, 68, 173, 0.25);
}

.link {
    color: #8e44ad;
    font-weight: 850;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.register-note {
    color: #6b5a78;
    font-size: 15px;
}

.alert {
    border-radius: 15px;
    font-weight: 600;
}

@media (max-width: 576px) {
    .login-box {
        padding: 30px 24px;
    }
}
</style>

</head>

<body>

<div class="login-box">

    <div class="logo-box">
        <img src="../assets/logo.png" alt="Logo The Four Label">
    </div>

    <h2 class="title">
        Login Admin
    </h2>

    <p class="subtitle">
        Masuk ke panel admin The Four Label
    </p>

    <?php if ($error != "") { ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">
                Email Admin
            </label>

            <input 
                type="email" 
                name="email" 
                class="form-control" 
                placeholder="Masukkan email admin"
                required>
        </div>

        <div class="mb-4">
            <label class="form-label">
                Password
            </label>

            <input 
                type="password" 
                name="password" 
                class="form-control" 
                placeholder="Masukkan password"
                required>
        </div>

        <button type="submit" name="login" class="btn-login">
            Login Admin
        </button>

    </form>

    <div class="text-center mt-4 register-note">
        Belum punya akun admin?
        <a href="register-admin.php" class="link">
            Registrasi Admin
        </a>
    </div>

</div>

</body>
</html>