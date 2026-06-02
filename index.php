<?php
require "koneksi.php";
include "navbar.php";

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM produk ORDER BY idProduk DESC LIMIT 6"
);

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>The Four Label - Konveksi Custom</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    background: #fbf7ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

/* HERO FULL BACKGROUND */
.hero {
    width: 100%;
    min-height: 100vh;
    background-image:
        linear-gradient(
            135deg,
            rgba(83, 35, 128, 0.72),
            rgba(181, 126, 220, 0.50)
        ),
        url('assets/lavender2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    padding: 90px 0;
}

.hero::before {
    content: "";
    position: absolute;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    top: -70px;
    left: -70px;
}

.hero::after {
    content: "";
    position: absolute;
    width: 360px;
    height: 360px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    bottom: -150px;
    right: -100px;
}

.hero-content {
    position: relative;
    z-index: 2;
    color: white;
    max-width: 680px;
}

.hero-badge {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 999px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.30);
    margin-bottom: 18px;
    font-size: 14px;
    font-weight: 600;
    backdrop-filter: blur(5px);
}

.hero h1 {
    font-size: 64px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 18px;
}

.hero p {
    font-size: 20px;
    line-height: 1.7;
    opacity: 0.96;
    margin-bottom: 28px;
}

.hero-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.btn-hero-primary {
    background: white;
    color: #7b3fb2;
    padding: 13px 26px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 800;
    transition: 0.25s;
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.btn-hero-primary:hover {
    background: #f3e8ff;
    color: #6f2da8;
    transform: translateY(-2px);
}

.btn-hero-outline {
    background: rgba(255,255,255,0.14);
    color: white;
    padding: 13px 26px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 800;
    border: 1px solid rgba(255,255,255,0.38);
    transition: 0.25s;
}

.btn-hero-outline:hover {
    background: rgba(255,255,255,0.25);
    color: white;
    transform: translateY(-2px);
}

.hero-card {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.30);
    border-radius: 30px;
    padding: 28px;
    color: white;
    backdrop-filter: blur(8px);
    box-shadow: 0 16px 36px rgba(0,0,0,0.12);
}

.hero-card h4 {
    font-weight: 800;
    margin-bottom: 12px;
}

.hero-card ul {
    padding-left: 18px;
    margin: 0;
    line-height: 2;
}

/* SECTION TITLE */
.section-title {
    text-align: center;
    margin-bottom: 36px;
}

.section-title span {
    display: inline-block;
    background: #f1e3ff;
    color: #8e44ad;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
}

.section-title h2 {
    color: #6f2da8;
    font-weight: 800;
    font-size: 36px;
    margin-bottom: 10px;
}

.section-title p {
    color: #777;
    margin: 0;
}

/* BENEFIT */
.benefit-section {
    margin: 70px 0;
}

.benefit-card {
    background: white;
    border-radius: 22px;
    padding: 26px;
    height: 100%;
    border: 1px solid #eadcff;
    box-shadow: 0 10px 24px rgba(142, 68, 173, 0.10);
    transition: 0.25s;
}

.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(142, 68, 173, 0.16);
}

.benefit-icon {
    width: 54px;
    height: 54px;
    border-radius: 18px;
    background: #f1e3ff;
    color: #8e44ad;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
}

.benefit-card h5 {
    font-weight: 800;
    color: #4b2e63;
    margin-bottom: 8px;
}

.benefit-card p {
    color: #777;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

/* PRODUK */
.produk-section {
    margin-bottom: 70px;
}

.card-produk {
    border: none;
    border-radius: 26px;
    overflow: hidden;
    background: white;
    transition: 0.28s ease;
    box-shadow: 0 10px 25px rgba(142, 68, 173, 0.12);
    height: 100%;
    border: 1px solid #eadcff;
}

.card-produk:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 36px rgba(142, 68, 173, 0.18);
}

.produk-img-wrap {
    height: 240px;
    background: #f4ecff;
    overflow: hidden;
    position: relative;
}

.produk-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.35s ease;
}

.card-produk:hover .produk-img-wrap img {
    transform: scale(1.06);
}

.produk-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: rgba(255,255,255,0.94);
    color: #7b3fb2;
    padding: 7px 13px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    box-shadow: 0 6px 16px rgba(0,0,0,0.10);
    z-index: 2;
}

.no-image {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8e44ad;
    font-weight: 800;
    background: #f1e3ff;
    text-align: center;
}

.produk-body {
    padding: 22px;
}

.produk-body h4 {
    font-size: 20px;
    font-weight: 800;
    color: #362447;
    margin-bottom: 10px;
}

.harga {
    color: #8e44ad;
    font-size: 21px;
    font-weight: 800;
    margin-bottom: 18px;
}

.btn-detail {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 16px;
    width: 100%;
    padding: 12px;
    font-weight: 800;
    transition: 0.25s;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-detail:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 18px rgba(142, 68, 173, 0.22);
}

.btn-lihat-semua {
    display: inline-block;
    margin-top: 22px;
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
    padding: 12px 25px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 800;
    transition: 0.25s;
}

.btn-lihat-semua:hover {
    background: #f3e8ff;
    color: #7b3fb2;
}

/* CTA */
.cta-section {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 32px;
    padding: 42px;
    margin-bottom: 70px;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: "";
    position: absolute;
    width: 190px;
    height: 190px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    top: -70px;
    right: -50px;
}

.cta-content {
    position: relative;
    z-index: 2;
}

.cta-section h2 {
    font-weight: 800;
    margin-bottom: 12px;
}

.cta-section p {
    opacity: 0.95;
    margin-bottom: 0;
}

.btn-cta {
    background: white;
    color: #8e44ad;
    border-radius: 999px;
    padding: 13px 25px;
    text-decoration: none;
    font-weight: 800;
    display: inline-block;
    transition: 0.25s;
}

.btn-cta:hover {
    background: #f3e8ff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .hero {
        min-height: auto;
        padding: 80px 0;
    }

    .hero h1 {
        font-size: 46px;
    }

    .hero-card {
        margin-top: 32px;
    }
}

@media (max-width: 576px) {
    .hero {
        min-height: auto;
        padding: 60px 0;
    }

    .hero h1 {
        font-size: 34px;
    }

    .hero p {
        font-size: 16px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .section-title h2 {
        font-size: 28px;
    }

    .cta-section {
        padding: 30px;
    }
}
</style>
</head>

<body>

<!-- HERO FULL -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">

            <div class="col-lg-7">
                <div class="hero-content">
                    <div class="hero-badge">
                        ✨ Konveksi Custom & Ready Stock
                    </div>

                    <h1>Wujudkan Desain Pakaianmu Sendiri</h1>

                    <p>
                        The Four Label menyediakan layanan konveksi untuk hoodie, t-shirt,
                        polo shirt, varsity, dan berbagai kebutuhan custom apparel.
                    </p>

                    <div class="hero-buttons">
                        <a href="produk.php" class="btn-hero-primary">Lihat Produk</a>
                        <a href="custom-order.php" class="btn-hero-outline">Pesan Custom</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-card">
                    <h4>Kenapa pilih The Four Label?</h4>
                    <ul>
                        <li>Bisa custom desain sesuai kebutuhan</li>
                        <li>Produk ready stock dan custom tersedia</li>
                        <li>Pemesanan mudah dan praktis</li>
                        <li>Pembayaran bisa DP minimal 50%</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- BENEFIT -->
<section class="benefit-section">
    <div class="container">

        <div class="section-title">
            <span>Keunggulan Kami</span>
            <h2>Layanan Konveksi Lebih Mudah</h2>
            <p>Pilih produk ready stock atau buat pesanan custom sesuai kebutuhanmu.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="benefit-card">
                    <div class="benefit-icon">👕</div>
                    <h5>Custom Apparel</h5>
                    <p>Buat desain sendiri untuk kebutuhan kelas, organisasi, komunitas, atau brand.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="benefit-card">
                    <div class="benefit-icon">📦</div>
                    <h5>Ready Stock</h5>
                    <p>Pilih produk yang tersedia dan langsung lakukan pemesanan dengan mudah.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="benefit-card">
                    <div class="benefit-icon">💳</div>
                    <h5>DP 50%</h5>
                    <p>Pembayaran pesanan dapat dimulai dengan minimal DP 50% dari total transaksi.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- PRODUK -->
<section class="produk-section">
    <div class="container">

        <div class="section-title">
            <span>Katalog</span>
            <h2>Produk Terbaru</h2>
            <p>Beberapa produk terbaru dari The Four Label.</p>
        </div>

        <div class="row g-4">

            <?php if (mysqli_num_rows($query) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                    <?php $gambar = tampilGambarProduk($row['gambar']); ?>

                    <div class="col-md-6 col-lg-4">
                        <div class="card-produk">

                            <div class="produk-img-wrap">
                                <div class="produk-badge">Produk Baru</div>

                                <?php if ($gambar != "") { ?>
                                    <img
                                        src="<?= htmlspecialchars($gambar); ?>"
                                        alt="<?= htmlspecialchars($row['namaProduk']); ?>">
                                <?php } else { ?>
                                    <div class="no-image">
                                        Gambar Produk<br>Belum Tersedia
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="produk-body">
                                <h4><?= htmlspecialchars($row['namaProduk']); ?></h4>

                                <p class="harga">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </p>

                                <a
                                    href="detail-produk.php?id=<?= $row['idProduk']; ?>"
                                    class="btn-detail">
                                    Detail Produk
                                </a>
                            </div>

                        </div>
                    </div>

                <?php } ?>
            <?php } else { ?>

                <div class="col-12">
                    <div class="alert alert-light text-center p-4 rounded-4">
                        Belum ada produk yang tersedia.
                    </div>
                </div>

            <?php } ?>

        </div>

        <div class="text-center">
            <a href="produk.php" class="btn-lihat-semua">
                Lihat Semua Produk
            </a>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="container">
    <div class="cta-section">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="cta-content">
                    <h2>Mau bikin desain custom sendiri?</h2>
                    <p>Upload desainmu dan isi detail pesanan custom sekarang.</p>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end">
                <a href="custom-order.php" class="btn-cta">
                    Mulai Custom
                </a>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>