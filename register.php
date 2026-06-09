<?php
session_start();
require "koneksi.php";

$pesan = "";

if (isset($_POST['register'])) {

    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $noHp     = mysqli_real_escape_string($koneksi, trim($_POST['noHp']));
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek email sudah terdaftar atau belum
    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");

    if (!$cek) {
        $pesan = "Query cek email error: " . mysqli_error($koneksi);
    } elseif (mysqli_num_rows($cek) > 0) {
        $pesan = "Email sudah terdaftar!";
    } else {

        // Simpan ke tabel user
        $insertUser = mysqli_query($koneksi, "
            INSERT INTO user (nama, email, password, role)
            VALUES ('$nama', '$email', '$password', 'pelanggan')
        ");

        if ($insertUser) {

            // Ambil idUser yang baru dibuat
            $idUser = mysqli_insert_id($koneksi);

            // Simpan ke tabel pelanggan
            // Sesuai tabel kamu: idPelanggan, idUser, noHp, alamat
            $insertPelanggan = mysqli_query($koneksi, "
                INSERT INTO pelanggan (idUser, noHp, alamat)
                VALUES ('$idUser', '$noHp', '$alamat')
            ");

            if ($insertPelanggan) {
                echo "<script>
                    alert('Registrasi berhasil! Silakan login.');
                    window.location='login.php';
                </script>";
                exit;
            } else {
                $pesan = "User berhasil dibuat, tapi data pelanggan gagal: " . mysqli_error($koneksi);
            }

        } else {
            $pesan = "Registrasi gagal: " . mysqli_error($koneksi);
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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background:
        radial-gradient(circle at top left, #f7edff 0%, transparent 36%),
        radial-gradient(circle at bottom right, #ead6ff 0%, transparent 34%),
        linear-gradient(135deg, #f3e8ff, #eadcff, #f9f5ff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 35px 15px;
}

.register-wrapper {
    width: 100%;
    max-width: 1000px;
    min-height: 650px;
    display: grid;
    grid-template-columns: 1fr 1.12fr;
    background: rgba(255, 255, 255, 0.96);
    border-radius: 36px;
    overflow: hidden;
    box-shadow: 0 28px 70px rgba(125, 82, 180, 0.25);
    border: 1px solid rgba(176, 127, 220, 0.28);
}

.brand-side {
    background:
        radial-gradient(circle at top right, rgba(255,255,255,0.20), transparent 35%),
        linear-gradient(160deg, #5f2e99, #7b42b6, #a56bd4);
    padding: 48px 38px;
    color: white;
    position: relative;
    overflow: hidden;
}

.brand-side::before {
    content: "";
    position: absolute;
    width: 310px;
    height: 310px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -105px;
    right: -95px;
}

.brand-side::after {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    bottom: -70px;
    left: -65px;
}

.logo-box {
    width: 155px;
    height: 155px;
    background: rgba(255,255,255,0.16);
    border: 1.5px solid rgba(255,255,255,0.35);
    border-radius: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
    position: relative;
    z-index: 1;
    box-shadow: 0 20px 45px rgba(69, 31, 110, 0.28);
    padding: 10px;
    backdrop-filter: blur(8px);
}

.logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 28px;
}

.brand-side h1 {
    position: relative;
    z-index: 1;
    font-size: 38px;
    font-weight: 800;
    margin-bottom: 8px;
    line-height: 1.2;
    letter-spacing: -0.4px;
}

.brand-subtitle {
    position: relative;
    z-index: 1;
    color: #f3e9ff;
    font-size: 13px;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 22px;
    font-weight: 700;
}

.brand-side p {
    position: relative;
    z-index: 1;
    font-size: 15px;
    line-height: 1.8;
    opacity: 0.95;
    margin-bottom: 30px;
    max-width: 360px;
}

.brand-point {
    position: relative;
    z-index: 1;
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 14px;
    font-size: 14px;
}

.brand-point i {
    width: 34px;
    height: 34px;
    background: rgba(255,255,255,0.22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-note {
    position: absolute;
    z-index: 1;
    left: 38px;
    right: 38px;
    bottom: 36px;
    padding: 16px 18px;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: 20px;
    font-size: 13px;
    line-height: 1.6;
    backdrop-filter: blur(8px);
}

.form-side {
    padding: 42px 50px;
    background:
        radial-gradient(circle at top center, rgba(185, 132, 224, 0.13), transparent 35%),
        #fff;
}

.form-header {
    text-align: center;
    margin-bottom: 24px;
}

.form-logo {
    width: 100px;
    height: 100px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f7f0ff, #ffffff);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 14px 32px rgba(142, 85, 215, 0.18);
    border: 1.5px solid #eadcf7;
    padding: 8px;
}

.form-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 30%;
}

.form-header h2 {
    color: #7437b8;
    font-weight: 800;
    margin-bottom: 6px;
    font-size: 34px;
    letter-spacing: -0.5px;
}

.form-header p {
    color: #8a7899;
    margin: 0;
    font-size: 15px;
}

.alert-custom {
    border: none;
    border-radius: 16px;
    padding: 13px 15px;
    margin-bottom: 18px;
    color: #6b2d89;
    background: #f3e4ff;
    font-weight: 600;
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

.btn-register {
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

.btn-register:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #a65bd6, #7c35ac);
    box-shadow: 0 18px 35px rgba(142, 67, 189, 0.35);
}

.login-text {
    text-align: center;
    margin-top: 20px;
    color: #8a7899;
    font-size: 14px;
}

.login-text a {
    color: #7b35b4;
    font-weight: 700;
    text-decoration: none;
}

.login-text a:hover {
    text-decoration: underline;
}

@media (max-width: 850px) {
    body {
        padding: 25px 12px;
    }

    .register-wrapper {
        grid-template-columns: 1fr;
        max-width: 560px;
        min-height: auto;
    }

    .brand-side {
        display: none;
    }

    .form-side {
        padding: 38px 28px;
    }

    .form-logo {
        width: 88px;
        height: 88px;
    }

    .form-header h2 {
        font-size: 31px;
    }
}
</style>
</head>

<body>

<div class="register-wrapper">

    <div class="brand-side">

        <div class="logo-box">
            <img src="assets/logoT4L.png" alt="Logo The Four Label">
        </div>

        <h1>The Four Label</h1>
        <div class="brand-subtitle">Stitched With Style</div>

        <p>
            Buat akun pelanggan dan mulai pesan produk konveksi favoritmu.
            Pilih produk ready atau custom sesuai kebutuhan dan gaya kamu.
        </p>

        <div class="brand-point">
            <i class="fa-solid fa-check"></i>
            <span>Custom pakaian sesuai desain</span>
        </div>

        <div class="brand-point">
            <i class="fa-solid fa-truck-fast"></i>
            <span>Pengiriman atau ambil langsung di toko</span>
        </div>

        <div class="brand-point">
            <i class="fa-solid fa-tags"></i>
            <span>Diskon khusus untuk pelanggan setia</span>
        </div>

        <div class="brand-note">
            Konveksi modern dengan sentuhan elegan untuk kebutuhan fashion,
            seragam, dan custom order.
        </div>

    </div>

    <div class="form-side">

        <div class="form-header">
            <div class="form-logo">
                <img src="assets/logoT4L.png" alt="Logo The Four Label">
            </div>

            <h2>Daftar Akun</h2>
            <p>Bergabung dengan The Four Label</p>
        </div>

        <?php if (!empty($pesan)) { ?>
            <div class="alert-custom">
                <?= $pesan; ?>
            </div>
        <?php } ?>

        <form method="POST" autocomplete="off">

            <label class="form-label">Nama Lengkap</label>
            <div class="input-group-custom">
                <i class="fa-solid fa-user"></i>
                <input 
                    type="text" 
                    name="nama" 
                    class="form-control" 
                    placeholder="Masukkan nama lengkap"
                    required>
            </div>

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

            <label class="form-label">No HP</label>
            <div class="input-group-custom">
                <i class="fa-solid fa-phone"></i>
                <input 
                    type="text" 
                    name="noHp" 
                    class="form-control" 
                    placeholder="Masukkan nomor HP"
                    required>
            </div>

            <label class="form-label">Alamat</label>
            <div class="input-group-custom">
                <i class="fa-solid fa-location-dot"></i>
                <input 
                    type="text" 
                    name="alamat" 
                    class="form-control" 
                    placeholder="Masukkan alamat lengkap"
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

            <button type="submit" name="register" class="btn-register">
                Register
            </button>

            <div class="login-text">
                Sudah punya akun? <a href="login.php">Login sekarang</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>