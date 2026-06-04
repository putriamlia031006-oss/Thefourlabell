<?php
require "auth.php";
require "../koneksi.php";

$pesan = "";

if (isset($_POST['simpan'])) {

    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $passwordInput = trim($_POST['password']);
    $noHp = mysqli_real_escape_string($koneksi, trim($_POST['noHp']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));

    if ($nama == "" || $email == "" || $passwordInput == "" || $noHp == "" || $alamat == "") {

        $pesan = "
        <div class='alert alert-danger'>
            Semua data wajib diisi.
        </div>";

    } else {

        $cekEmail = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");

        if (mysqli_num_rows($cekEmail) > 0) {

            $pesan = "
            <div class='alert alert-danger'>
                Email sudah digunakan.
            </div>";

        } else {

            $password = password_hash($passwordInput, PASSWORD_DEFAULT);

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
                alert('Data pelanggan berhasil ditambahkan.');
                window.location.href='pelanggan.php';
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

<title>Tambah Pelanggan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f6f0ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    color: #33223f;
}

.admin-layout {
    display: flex;
}

.sidebar-wrapper {
    width: 230px;
    min-width: 230px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}

.main-content {
    margin-left: 230px;
    width: calc(100% - 230px);
    padding: 26px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 20px 24px;
    border-radius: 18px;
    margin-bottom: 22px;
}

.page-header h3 {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}

.page-header p {
    margin: 0;
    font-size: 13px;
    opacity: 0.92;
}

.form-card {
    background: white;
    border-radius: 18px;
    padding: 24px;
    border: 1px solid #eadcff;
    box-shadow: 0 8px 20px rgba(142, 68, 173, 0.10);
    max-width: 620px;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
    font-size: 14px;
}

.form-control {
    border-radius: 12px;
    min-height: 46px;
    border: 1px solid #ddd;
    background: #fcfbff;
    font-size: 14px;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.15);
}

textarea {
    resize: none;
}

.btn-simpan {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 10px 18px;
    font-weight: 800;
}

.btn-simpan:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
}

.btn-kembali {
    background: #e5e7eb;
    color: #374151;
    border: none;
    border-radius: 12px;
    padding: 10px 18px;
    font-weight: 700;
    text-decoration: none;
}

.btn-kembali:hover {
    background: #d1d5db;
    color: #111827;
}

@media (max-width: 768px) {
    .admin-layout {
        display: block;
    }

    .sidebar-wrapper {
        position: relative;
        width: 100%;
        min-width: 100%;
        height: auto;
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 18px;
    }

    .form-card {
        max-width: 100%;
    }
}
</style>
</head>

<body>

<div class="admin-layout">

    <div class="sidebar-wrapper">
        <?php include "sidebar.php"; ?>
    </div>

    <div class="main-content">

        <div class="page-header">
            <h3>+ Tambah Pelanggan</h3>
            <p>Tambahkan akun pelanggan baru untuk The Four Label.</p>
        </div>

        <div class="form-card">

            <?= $pesan; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-control" 
                        placeholder="Masukkan nama pelanggan"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Masukkan email pelanggan"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Masukkan password"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input 
                        type="text" 
                        name="noHp" 
                        class="form-control" 
                        placeholder="Contoh: 081234567890"
                        required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Alamat</label>
                    <textarea 
                        name="alamat" 
                        class="form-control" 
                        rows="3" 
                        placeholder="Masukkan alamat pelanggan"
                        required></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-simpan">
                    Simpan
                </button>

                <a href="pelanggan.php" class="btn-kembali ms-2">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>