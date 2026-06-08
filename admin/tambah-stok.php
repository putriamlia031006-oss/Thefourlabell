<?php
require "auth.php";
require "../koneksi.php";

/* =========================
   CEK ID STOK
========================= */
if (!isset($_GET['id'])) {
    echo "<script>
        alert('ID stok tidak ditemukan.');
        window.location='stok.php';
    </script>";
    exit;
}

$idStok = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   AMBIL DATA STOK + PRODUK
========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        stok_produk.*,
        produk.namaProduk,
        produk.harga,
        produk.gambar
    FROM stok_produk
    JOIN produk
        ON stok_produk.idProduk = produk.idProduk
    WHERE stok_produk.idStok = '$idStok'
    LIMIT 1"
);

if (!$query) {
    die("Query stok error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
        alert('Data stok tidak ditemukan.');
        window.location='stok.php';
    </script>";
    exit;
}

/* =========================
   FUNCTION GAMBAR
========================= */
function tampilGambarProduk($namaGambar) {
    $namaGambar = trim($namaGambar ?? "");

    if ($namaGambar == "") {
        return "";
    }

    $path1 = "../image/" . $namaGambar;
    $path2 = "../upload/" . $namaGambar;
    $path3 = "../uploads/" . $namaGambar;

    if (file_exists($path1)) {
        return $path1;
    } elseif (file_exists($path2)) {
        return $path2;
    } elseif (file_exists($path3)) {
        return $path3;
    } else {
        return "";
    }
}

$gambar = tampilGambarProduk($data['gambar']);

$error = "";

/* =========================
   PROSES TAMBAH STOK
========================= */
if (isset($_POST['simpan'])) {

    $stokTambah = mysqli_real_escape_string($koneksi, $_POST['stokTambah']);

    if ($stokTambah === "") {
        $error = "Jumlah stok tambahan wajib diisi.";
    } elseif (!is_numeric($stokTambah) || $stokTambah <= 0) {
        $error = "Jumlah stok tambahan harus lebih dari 0.";
    } else {

        $stokLama = (int) $data['jumlahStok'];
        $stokTambah = (int) $stokTambah;
        $stokBaru = $stokLama + $stokTambah;

        $update = mysqli_query(
            $koneksi,
            "UPDATE stok_produk SET
                jumlahStok = '$stokBaru'
             WHERE idStok = '$idStok'"
        );

        if ($update) {
            echo "<script>
                alert('Stok berhasil ditambahkan.');
                window.location='stok.php';
            </script>";
            exit;
        } else {
            $error = "Gagal menambahkan stok: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Stok Produk - Admin The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
    box-sizing: border-box;
}

html, body {
    overflow-x: hidden;
}

body {
    margin: 0;
    background: #fbf7ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.main-content {
    margin-left: 240px;
    min-height: 100vh;
    padding: 34px;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #9d7ad6, #8e44ad);
    border-radius: 28px;
    padding: 30px;
    color: white;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 36px rgba(142, 68, 173, 0.18);
}

.page-header::before {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    background: rgba(255,255,255,.13);
    top: -80px;
    right: -55px;
}

.page-header::after {
    content: "";
    position: absolute;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: rgba(255,255,255,.10);
    bottom: -55px;
    left: 38%;
}

.page-header-content {
    position: relative;
    z-index: 2;
}

.header-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: rgba(255,255,255,.20);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 14px;
    border: 1px solid rgba(255,255,255,.22);
}

.page-title {
    font-size: 34px;
    font-weight: 900;
    margin: 0 0 8px;
}

.page-subtitle {
    margin: 0;
    font-size: 15px;
    opacity: .95;
    font-weight: 500;
}

.form-card {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 26px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    overflow: hidden;
}

.form-card-header {
    padding: 22px 24px;
    background: linear-gradient(135deg, #fbf7ff, #f4eaff);
    border-bottom: 1px solid #eadcff;
}

.form-card-header h4 {
    color: #6f2da8;
    font-weight: 900;
    margin: 0 0 5px;
}

.form-card-header p {
    color: #81758d;
    margin: 0;
    font-size: 14px;
}

.form-card-body {
    padding: 24px;
}

.form-label {
    color: #4b2e63;
    font-weight: 800;
    margin-bottom: 8px;
}

.form-control {
    height: 50px;
    border-radius: 15px;
    border: 1px solid #eadcff;
    background: #fcfbff;
    color: #33223f;
    font-weight: 600;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
}

.form-control[readonly] {
    background: #f4eaff;
    color: #7b3fb2;
    cursor: not-allowed;
}

.input-note {
    font-size: 13px;
    color: #8d7a9b;
    margin-top: 6px;
}

.preview-card {
    background: #fbf7ff;
    border: 1px solid #eadcff;
    border-radius: 22px;
    padding: 18px;
    height: 100%;
}

.preview-title {
    color: #6f2da8;
    font-weight: 900;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.product-preview {
    display: flex;
    gap: 14px;
    align-items: center;
}

.product-image {
    width: 100px;
    height: 100px;
    border-radius: 22px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f4eaff;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.10);
}

.no-image {
    width: 100px;
    height: 100px;
    border-radius: 22px;
    background: #f4eaff;
    color: #8e44ad;
    border: 1px dashed #c9a7ec;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.product-name {
    font-weight: 900;
    color: #33223f;
    margin-bottom: 4px;
}

.product-price {
    color: #7b3fb2;
    font-weight: 900;
}

.stock-info {
    margin-top: 14px;
    padding: 12px 14px;
    border-radius: 16px;
    font-weight: 800;
    background: #f1e3ff;
    color: #7b3fb2;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-lavender,
.btn-reset {
    border-radius: 15px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    transition: .25s ease;
    border: none;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    box-shadow: 0 9px 20px rgba(142, 68, 173, 0.20);
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-reset {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
}

.btn-reset:hover {
    background: #f4eaff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

.alert-custom {
    border-radius: 16px;
    font-weight: 700;
}

@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        padding: 24px;
    }

    .page-title {
        font-size: 28px;
    }
}

@media (max-width: 576px) {
    .main-content {
        padding: 18px;
    }

    .page-header {
        padding: 24px;
        border-radius: 24px;
    }

    .btn-lavender,
    .btn-reset {
        width: 100%;
        justify-content: center;
    }

    .product-preview {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<main class="main-content">

    <div class="page-header">
        <div class="page-header-content">
            <div class="header-icon">
                <i class="fa-solid fa-plus"></i>
            </div>

            <h2 class="page-title">Tambah Stok Produk</h2>
            <p class="page-subtitle">
                Tambahkan jumlah stok baru pada produk ready stock yang sudah tersedia.
            </p>
        </div>
    </div>

    <div class="form-card">

        <div class="form-card-header">
            <h4>Form Tambah Stok</h4>
            <p>Jumlah stok baru akan ditambahkan ke stok lama secara otomatis.</p>
        </div>

        <div class="form-card-body">

            <?php if ($error != "") { ?>
                <div class="alert alert-danger alert-custom">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <div class="row g-4">

                <div class="col-lg-7">

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>

                            <input 
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($data['namaProduk']); ?>"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stok Saat Ini</label>

                            <input 
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($data['jumlahStok']); ?> <?= htmlspecialchars($data['satuan']); ?>"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Stok yang Ditambahkan</label>

                            <input 
                                type="number"
                                name="stokTambah"
                                class="form-control"
                                min="1"
                                placeholder="Contoh: 10"
                                required>

                            <div class="input-note">
                                Masukkan jumlah stok tambahan. Contoh: jika stok saat ini 5 dan ditambah 10, maka stok menjadi 15.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Satuan</label>

                            <input 
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($data['satuan']); ?>"
                                readonly>

                            <div class="input-note">
                                Satuan tidak bisa diedit dari halaman ini.
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" name="simpan" class="btn-lavender">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Tambahan Stok
                            </button>

                            <a href="stok.php" class="btn-reset">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>

                <div class="col-lg-5">

                    <div class="preview-card">
                        <div class="preview-title">
                            <i class="fa-solid fa-box"></i>
                            Preview Produk
                        </div>

                        <div class="product-preview">
                            <?php if ($gambar != "") { ?>
                                <img 
                                    src="<?= htmlspecialchars($gambar); ?>"
                                    class="product-image"
                                    alt="<?= htmlspecialchars($data['namaProduk']); ?>">
                            <?php } else { ?>
                                <div class="no-image">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            <?php } ?>

                            <div>
                                <div class="product-name">
                                    <?= htmlspecialchars($data['namaProduk']); ?>
                                </div>

                                <div class="product-price">
                                    Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                </div>

                                <div class="input-note">
                                    ID Stok: <?= htmlspecialchars($data['idStok']); ?>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="stock-info">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                Stok sekarang: 
                                <?= htmlspecialchars($data['jumlahStok']); ?> 
                                <?= htmlspecialchars($data['satuan']); ?>
                            </span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

</body>
</html>