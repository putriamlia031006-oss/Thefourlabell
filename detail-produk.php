<?php
session_start();
include "auth-pelanggan.php";
require "koneksi.php";

include "navbar.php";

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT 
        p.*,
        k.namaKategori,
        s.jumlahStok,
        s.satuan
    FROM produk p
    LEFT JOIN kategori k
        ON p.idKategori = k.idKategori
    LEFT JOIN stok_produk s
        ON p.idProduk = s.idProduk
    WHERE p.idProduk='$id'"
);

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Produk tidak ditemukan.");
}

function tampilGambarProduk($namaGambar) {
    $namaGambar = trim($namaGambar);

    if ($namaGambar == "") {
        return "";
    }

    $path1 = "upload/" . $namaGambar;
    $path2 = "uploads/" . $namaGambar;
    $path3 = "image/" . $namaGambar;

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

$stok = isset($data['jumlahStok']) ? $data['jumlahStok'] : 0;
$satuan = isset($data['satuan']) ? $data['satuan'] : "pcs";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Detail Produk - <?= htmlspecialchars($data['namaProduk']); ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.page-section {
    padding: 55px 0 70px;
}

.detail-box {
    background: white;
    border-radius: 30px;
    padding: 35px;
    box-shadow: 0 16px 40px rgba(142, 68, 173, 0.13);
    border: 1px solid #eadcff;
}

.image-wrap {
    width: 100%;
    height: 470px;
    border-radius: 26px;
    overflow: hidden;
    background: #f1e3ff;
    position: relative;
}

.gambar {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    color: #8e44ad;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.badge-ready {
    position: absolute;
    top: 18px;
    left: 18px;
    background: white;
    color: #7b3fb2;
    padding: 8px 15px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    box-shadow: 0 8px 18px rgba(0,0,0,0.12);
}

.kategori {
    background: #f1e3ff;
    color: #7b3fb2;
    padding: 9px 17px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 18px;
    font-weight: 700;
    font-size: 14px;
}

.nama {
    color: #4b2e63;
    font-weight: 850;
    font-size: 42px;
    line-height: 1.15;
    margin-bottom: 15px;
}

.harga {
    color: #8e44ad;
    font-size: 34px;
    font-weight: 850;
    margin: 18px 0;
}

.info-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 20px 0;
}

.info-pill {
    background: #faf5ff;
    border: 1px solid #eadcff;
    color: #5d4773;
    padding: 10px 14px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 650;
}

.deskripsi {
    color: #666;
    line-height: 1.8;
    font-size: 15px;
    margin-top: 18px;
}

.qty-box {
    margin-top: 30px;
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 20px;
    padding: 20px;
}

.qty {
    width: 110px;
    border-radius: 14px;
    padding: 12px;
}

.btn-cart {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    border: none;
    color: white;
    padding: 13px 26px;
    border-radius: 16px;
    font-weight: 800;
    transition: 0.25s;
}

.btn-cart:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(142, 68, 173, 0.22);
}

.btn-cart:disabled {
    background: #cfc4d8;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

.btn-back {
    display: inline-block;
    color: #8e44ad;
    text-decoration: none;
    font-weight: 750;
    margin-bottom: 18px;
}

.btn-back:hover {
    color: #6f2da8;
}

.alert-custom {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffe6a7;
    border-radius: 16px;
    padding: 13px 16px;
    margin-top: 18px;
}

@media (max-width: 768px) {
    .detail-box {
        padding: 24px;
    }

    .image-wrap {
        height: 340px;
        margin-bottom: 25px;
    }

    .nama {
        font-size: 32px;
    }

    .harga {
        font-size: 28px;
    }
}
</style>
</head>

<body>

<div class="container page-section">

    <a href="produk.php" class="btn-back">
        ← Kembali ke Produk
    </a>

    <div class="detail-box">

        <div class="row align-items-center g-5">

            <div class="col-lg-5">

                <div class="image-wrap">

                    <div class="badge-ready">
                        Ready Stock
                    </div>

                    <?php if ($gambar != "") { ?>

                        <img
                            src="<?= htmlspecialchars($gambar); ?>"
                            class="gambar"
                            alt="<?= htmlspecialchars($data['namaProduk']); ?>">

                    <?php } else { ?>

                        <div class="no-image">
                            Gambar Produk<br>Belum Tersedia
                        </div>

                    <?php } ?>

                </div>

            </div>

            <div class="col-lg-7">

                <div class="kategori">
                    <?= $data['namaKategori'] ? htmlspecialchars($data['namaKategori']) : "Tanpa Kategori"; ?>
                </div>

                <h1 class="nama">
                    <?= htmlspecialchars($data['namaProduk']); ?>
                </h1>

                <div class="harga">
                    Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                </div>

                <div class="info-row">

                    <div class="info-pill">
                        Stok: <?= htmlspecialchars($stok); ?> <?= htmlspecialchars($satuan); ?>
                    </div>

                    <div class="info-pill">
                        Produk Siap Pakai
                    </div>

                    <div class="info-pill">
                        Bisa Pesan Online
                    </div>

                </div>

                <p class="deskripsi">
                    <?= nl2br(htmlspecialchars($data['deskripsi'])); ?>
                </p>

                <?php if ($stok <= 0) { ?>

                    <div class="alert-custom">
                        Stok produk ini sedang kosong.
                    </div>

                <?php } ?>

                <form action="tambah-cart.php" method="POST">

                    <input type="hidden" name="idProduk" value="<?= htmlspecialchars($data['idProduk']); ?>">

                    <div class="qty-box">

                        <label class="fw-bold mb-2">
                            Jumlah Pesanan
                        </label>

                        <div class="d-flex flex-wrap gap-3 align-items-center">

                            <input
                                type="number"
                                name="qty"
                                value="1"
                                min="1"
                                max="<?= htmlspecialchars($stok); ?>"
                                class="form-control qty"
                                required>

                            <button
                                type="submit"
                                class="btn btn-cart"
                                <?= $stok <= 0 ? "disabled" : ""; ?>>

                                Tambah ke Keranjang

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<?php include "footer.php"; ?>

</body>
</html>