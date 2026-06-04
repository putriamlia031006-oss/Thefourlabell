<?php
require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID pelanggan tidak ditemukan.'); window.location.href='pelanggan.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT 
    pelanggan.*,
    user.nama,
    user.email,
    user.idUser
FROM pelanggan
JOIN user ON pelanggan.idUser = user.idUser
WHERE pelanggan.idPelanggan = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan.'); window.location.href='pelanggan.php';</script>";
    exit;
}

$pesan = "";

if (isset($_POST['update'])) {

    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $noHp = mysqli_real_escape_string($koneksi, trim($_POST['noHp']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $passwordInput = trim($_POST['password']);

    $idUser = $data['idUser'];

    if ($nama == "" || $email == "" || $noHp == "" || $alamat == "") {

        $pesan = "
        <div class='alert alert-danger'>
            Semua data wajib diisi.
        </div>";

    } else {

        $cekEmail = mysqli_query(
            $koneksi,
            "SELECT * FROM user 
             WHERE email = '$email' 
             AND idUser != '$idUser'"
        );

        if (mysqli_num_rows($cekEmail) > 0) {

            $pesan = "
            <div class='alert alert-danger'>
                Email sudah digunakan oleh akun lain.
            </div>";

        } else {

            if ($passwordInput != "") {
                $password = password_hash($passwordInput, PASSWORD_DEFAULT);

                $updateUser = mysqli_query($koneksi, "UPDATE user SET
                    nama = '$nama',
                    email = '$email',
                    password = '$password'
                    WHERE idUser = '$idUser'");
            } else {
                $updateUser = mysqli_query($koneksi, "UPDATE user SET
                    nama = '$nama',
                    email = '$email'
                    WHERE idUser = '$idUser'");
            }

            $updatePelanggan = mysqli_query($koneksi, "UPDATE pelanggan SET
                noHp = '$noHp',
                alamat = '$alamat'
                WHERE idPelanggan = '$id'");

            if ($updateUser && $updatePelanggan) {
                echo "
                <script>
                    alert('Data pelanggan berhasil diupdate.');
                    window.location.href='pelanggan.php';
                </script>";
                exit;
            } else {
                $pesan = "
                <div class='alert alert-danger'>
                    Gagal mengupdate data: " . mysqli_error($koneksi) . "
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

<title>Edit Pelanggan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
html, body {
    overflow-x: hidden;
}

body {
    background: #f6f0ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    color: #33223f;
    font-size: 14px;
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
    z-index: 1000;
}

.main-content {
    margin-left: 230px;
    width: calc(100% - 230px);
    padding: 24px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 20px 24px;
    border-radius: 18px;
    margin-bottom: 22px;
    box-shadow: 0 8px 20px rgba(111, 66, 193, 0.18);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -45px;
    right: -30px;
}

.page-header h3 {
    position: relative;
    z-index: 2;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}

.page-header p {
    position: relative;
    z-index: 2;
    margin: 0;
    opacity: 0.92;
    font-size: 13px;
}

.form-card {
    background: white;
    border-radius: 18px;
    padding: 22px;
    border: 1px solid #eadcff;
    box-shadow: 0 8px 20px rgba(142, 68, 173, 0.10);
    max-width: 650px;
}

.info-box {
    background: #f8f1ff;
    border: 1px solid #eadcff;
    border-radius: 15px;
    padding: 14px 16px;
    margin-bottom: 18px;
    color: #6f2da8;
    font-weight: 700;
    font-size: 13px;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
    font-size: 14px;
}

.form-control {
    border-radius: 12px;
    min-height: 45px;
    border: 1px solid #ddd;
    background: #fcfbff;
    font-size: 14px;
    padding: 10px 13px;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.15);
    background: white;
}

textarea {
    resize: none;
}

.btn-update {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 10px 18px;
    font-weight: 800;
}

.btn-update:hover {
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
    display: inline-block;
}

.btn-kembali:hover {
    background: #d1d5db;
    color: #111827;
}

.password-note {
    color: #888;
    font-size: 12px;
    margin-top: 5px;
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
            <h3>✏️ Edit Pelanggan</h3>
            <p>Perbarui data akun dan informasi pelanggan The Four Label.</p>
        </div>

        <div class="form-card">

            <div class="info-box">
                ID Pelanggan: #<?= htmlspecialchars($data['idPelanggan']); ?> |
                ID User: #<?= htmlspecialchars($data['idUser']); ?>
            </div>

            <?= $pesan; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-control" 
                        value="<?= htmlspecialchars($data['nama']); ?>" 
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        value="<?= htmlspecialchars($data['email']); ?>" 
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Kosongkan jika tidak ingin mengganti password">
                    <div class="password-note">
                        Password tidak akan berubah kalau kolom ini dikosongkan.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input 
                        type="text" 
                        name="noHp" 
                        class="form-control" 
                        value="<?= htmlspecialchars($data['noHp']); ?>" 
                        required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Alamat</label>
                    <textarea 
                        name="alamat" 
                        class="form-control" 
                        rows="3" 
                        required><?= htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <button name="update" class="btn btn-update">
                    Simpan Perubahan
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