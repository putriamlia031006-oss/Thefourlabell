<?php

require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID produk tidak ditemukan!');
            window.location='produk.php';
          </script>";
    exit;
}

$id = $_GET['id'];

/* =========================
   AMBIL DATA PRODUK
========================= */
$stmt = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE idProduk = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>
            alert('Data produk tidak ditemukan!');
            window.location='produk.php';
          </script>";
    exit;
}

/* =========================
   UPDATE DATA PRODUK
========================= */
if (isset($_POST['update'])) {

    $nama = trim($_POST['nama']);
    $harga = trim($_POST['harga']);
    $deskripsi = trim($_POST['deskripsi']);

    if ($nama == "" || $harga == "" || $deskripsi == "") {
        $error = "Semua field wajib diisi!";
    } elseif (!is_numeric($harga)) {
        $error = "Harga harus berupa angka!";
    } else {
        $stmtUpdate = mysqli_prepare(
            $koneksi,
            "UPDATE produk 
             SET namaProduk = ?, harga = ?, deskripsi = ?
             WHERE idProduk = ?"
        );

        mysqli_stmt_bind_param(
            $stmtUpdate,
            "sdsi",
            $nama,
            $harga,
            $deskripsi,
            $id
        );

        if (mysqli_stmt_execute($stmtUpdate)) {
            echo "<script>
                    alert('Produk berhasil diperbarui!');
                    window.location='produk.php';
                  </script>";
            exit;
        } else {
            $error = "Produk gagal diperbarui!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
html, body {
    overflow-x: hidden;
}

body {
    background: #f8f4ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
    margin: 0;
}

/* CONTENT */
.content {
    margin-left: 240px;
    width: calc(100% - 240px);
    padding: 32px;
    min-height: 100vh;
}

/* HEADER */
.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 26px;
    padding: 30px;
    margin-bottom: 28px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.20);
}

.page-header h2 {
    font-weight: 850;
    margin-bottom: 8px;
}

.page-header p {
    margin: 0;
    opacity: 0.92;
}

/* CARD */
.form-card {
    background: white;
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
}

.form-label {
    font-weight: 700;
    color: #6f2da8;
}

.form-control {
    border-radius: 14px;
    padding: 12px 14px;
    border: 1px solid #d9c4f2;
}

.form-control:focus {
    border-color: #9d7ad6;
    box-shadow: 0 0 0 0.2rem rgba(157, 122, 214, 0.18);
}

textarea.form-control {
    min-height: 130px;
    resize: vertical;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 11px 18px;
    font-weight: 750;
    text-decoration: none;
    transition: 0.25s ease;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-outline-lavender {
    border: 1px solid #9d7ad6;
    color: #7b5cb8;
    background: white;
    border-radius: 14px;
    padding: 11px 18px;
    font-weight: 750;
    text-decoration: none;
    transition: 0.25s ease;
}

.btn-outline-lavender:hover {
    background: #f1e3ff;
    color: #6f2da8;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .content {
        margin-left: 0;
        width: 100%;
        padding: 22px;
    }

    .page-header {
        padding: 24px;
    }
}
</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<div class="content">

    <div class="page-header">
        <h2>Edit Produk</h2>
        <p>Perbarui data produk The Four Label dengan benar.</p>
    </div>

    <div class="form-card">

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input 
                    type="text"
                    name="nama"
                    class="form-control"
                    value="<?= htmlspecialchars($data['namaProduk']); ?>"
                    placeholder="Masukkan nama produk"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input 
                    type="number"
                    name="harga"
                    class="form-control"
                    value="<?= htmlspecialchars($data['harga']); ?>"
                    placeholder="Masukkan harga produk"
                    min="0"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi</label>
                <textarea 
                    name="deskripsi"
                    class="form-control"
                    placeholder="Masukkan deskripsi produk"
                    required
                ><?= htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" name="update" class="btn-lavender">
                    Simpan Perubahan
                </button>

                <a href="produk.php" class="btn-outline-lavender">
                    Kembali
                </a>
            </div>

        </form>

    </div>

</div>

</body>

</html>