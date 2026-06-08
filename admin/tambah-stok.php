<?php
require "auth.php";
require "../koneksi.php";

/* =========================
   AMBIL PRODUK YANG BELUM ADA STOK
========================= */
$produk = mysqli_query(
    $koneksi,
    "SELECT 
        produk.idProduk,
        produk.namaProduk,
        produk.harga,
        produk.gambar
    FROM produk
    WHERE produk.idProduk NOT IN (
        SELECT idProduk FROM stok_produk
    )
    ORDER BY produk.namaProduk ASC"
);

if (!$produk) {
    die("Query produk error: " . mysqli_error($koneksi));
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

$error = "";

/* =========================
   SIMPAN DATA STOK
========================= */
if (isset($_POST['simpan'])) {

    $idProduk = mysqli_real_escape_string($koneksi, $_POST['idProduk']);
    $jumlahStok = mysqli_real_escape_string($koneksi, $_POST['jumlahStok']);
    $satuan = "pcs";

    if ($idProduk == "" || $jumlahStok === "") {
        $error = "Produk dan jumlah stok wajib diisi.";
    } elseif (!is_numeric($jumlahStok) || $jumlahStok < 0) {
        $error = "Jumlah stok harus berupa angka dan tidak boleh negatif.";
    } else {

        /* CEK APAKAH PRODUK SUDAH PUNYA STOK */
        $cek = mysqli_query(
            $koneksi,
            "SELECT * FROM stok_produk 
             WHERE idProduk = '$idProduk' 
             LIMIT 1"
        );

        if (!$cek) {
            die("Query cek stok error: " . mysqli_error($koneksi));
        }

        if (mysqli_num_rows($cek) > 0) {
            $error = "Produk ini sudah memiliki data stok. Silakan edit stok produk tersebut.";
        } else {

            $insert = mysqli_query(
                $koneksi,
                "INSERT INTO stok_produk
                (idProduk, jumlahStok, satuan)
                VALUES
                ('$idProduk', '$jumlahStok', '$satuan')"
            );

            if ($insert) {
                echo "<script>
                    alert('Data stok berhasil ditambahkan.');
                    window.location='stok.php';
                </script>";
                exit;
            } else {
                $error = "Gagal menambahkan stok: " . mysqli_error($koneksi);
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

/* MAIN CONTENT */
.main-content {
    margin-left: 240px;
    min-height: 100vh;
    padding: 34px;
}

/* HEADER */
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

/* FORM CARD */
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

.form-control,
.form-select {
    height: 50px;
    border-radius: 15px;
    border: 1px solid #eadcff;
    background: #fcfbff;
    color: #33223f;
    font-weight: 600;
}

.form-control:focus,
.form-select:focus {
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

/* PRODUCT SELECT INFO */
.product-option-note {
    background: #fbf7ff;
    border: 1px solid #eadcff;
    border-radius: 18px;
    padding: 15px;
    color: #5f526a;
    font-size: 14px;
    line-height: 1.6;
}

.info-card {
    background: #fbf7ff;
    border: 1px solid #eadcff;
    border-radius: 22px;
    padding: 18px;
    height: 100%;
}

.info-title {
    color: #6f2da8;
    font-weight: 900;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    gap: 10px;
    color: #5f526a;
    line-height: 1.5;
    font-size: 14px;
}

.info-item i {
    color: #8e44ad;
    margin-top: 4px;
}

/* BUTTON */
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

.empty-product-box {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
    border-radius: 18px;
    padding: 18px;
    font-weight: 700;
}

/* RESPONSIVE */
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
}
</style>

</head>

<body>

<?php include "sidebar.php"; ?>

<main class="main-content">

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="header-icon">
                <i class="fa-solid fa-plus"></i>
            </div>

            <h2 class="page-title">Tambah Stok Produk</h2>
            <p class="page-subtitle">
                Tambahkan data stok untuk produk ready stock yang belum memiliki stok.
            </p>
        </div>
    </div>

    <div class="form-card">

        <div class="form-card-header">
            <h4>Form Tambah Stok</h4>
            <p>Pilih produk, masukkan jumlah stok, lalu simpan ke sistem.</p>
        </div>

        <div class="form-card-body">

            <?php if ($error != "") { ?>
                <div class="alert alert-danger alert-custom">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <div class="row g-4">

                <!-- FORM -->
                <div class="col-lg-7">

                    <?php if (mysqli_num_rows($produk) > 0) { ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Produk</label>

                                <select name="idProduk" class="form-select" required>
                                    <option value="">Pilih produk</option>

                                    <?php while ($p = mysqli_fetch_assoc($produk)) { ?>
                                        <option value="<?= $p['idProduk']; ?>">
                                            <?= htmlspecialchars($p['namaProduk']); ?> - Rp <?= number_format($p['harga'], 0, ',', '.'); ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <div class="input-note">
                                    Produk yang muncul adalah produk yang belum memiliki data stok.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Stok</label>

                                <input 
                                    type="number"
                                    name="jumlahStok"
                                    class="form-control"
                                    min="0"
                                    placeholder="Masukkan jumlah stok"
                                    required>

                                <div class="input-note">
                                    Isi angka stok awal. Contoh: 10, 25, 100.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Satuan</label>

                                <input 
                                    type="text"
                                    class="form-control"
                                    value="pcs"
                                    readonly>

                                <div class="input-note">
                                    Satuan stok otomatis menggunakan pcs dan tidak bisa diedit.
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" name="simpan" class="btn-lavender">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Simpan Stok
                                </button>

                                <a href="stok.php" class="btn-reset">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>

                        </form>

                    <?php } else { ?>

                        <div class="empty-product-box">
                            <i class="fa-solid fa-circle-info"></i>
                            Semua produk sudah memiliki data stok. Silakan gunakan fitur Edit pada halaman Data Stok.
                        </div>

                        <div class="mt-3">
                            <a href="stok.php" class="btn-reset">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali ke Data Stok
                            </a>
                        </div>

                    <?php } ?>

                </div>

                <!-- INFO -->
                <div class="col-lg-5">

                    <div class="info-card">
                        <div class="info-title">
                            <i class="fa-solid fa-circle-info"></i>
                            Informasi Tambah Stok
                        </div>

                        <div class="info-list">

                            <div class="info-item">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>
                                    Fitur ini hanya menambahkan stok untuk produk yang belum punya data stok.
                                </span>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>
                                    Kalau produk sudah punya stok, gunakan tombol Edit di halaman Data Stok.
                                </span>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>
                                    Satuan otomatis menggunakan pcs supaya data stok tetap konsisten.
                                </span>
                            </div>

                            <div class="info-item">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>
                                    Jumlah stok tidak boleh negatif.
                                </span>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

</body>
</html>