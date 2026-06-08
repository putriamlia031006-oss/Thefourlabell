<?php
require "auth.php";
require "../koneksi.php";

/* =========================
   AMBIL DATA STOK
========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        stok_produk.*,
        produk.namaProduk,
        produk.gambar,
        produk.harga
    FROM stok_produk
    JOIN produk 
        ON stok_produk.idProduk = produk.idProduk
    ORDER BY stok_produk.idStok DESC"
);

if (!$query) {
    die("Query stok error: " . mysqli_error($koneksi));
}

/* =========================
   HITUNG RINGKASAN
========================= */
$qTotalStok = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlahStok), 0) AS totalStok FROM stok_produk"
);

$totalStok = mysqli_fetch_assoc($qTotalStok)['totalStok'];

$qProdukStok = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS totalProdukStok FROM stok_produk"
);

$totalProdukStok = mysqli_fetch_assoc($qProdukStok)['totalProdukStok'];

$qStokHabis = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS totalHabis FROM stok_produk WHERE jumlahStok <= 0"
);

$totalHabis = mysqli_fetch_assoc($qStokHabis)['totalHabis'];

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
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Stok Produk - Admin The Four Label</title>

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

/* SUMMARY */
.summary-card {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 24px;
    padding: 22px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    height: 100%;
    transition: .25s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: "";
    position: absolute;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: #f4eaff;
    top: -34px;
    right: -34px;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.16);
}

.summary-inner {
    position: relative;
    z-index: 2;
}

.summary-icon {
    width: 54px;
    height: 54px;
    border-radius: 18px;
    background: #f1e3ff;
    color: #8e44ad;
    border: 1px solid #eadcff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}

.summary-label {
    margin: 0;
    color: #6b6175;
    font-size: 14px;
    font-weight: 700;
}

.summary-value {
    margin: 5px 0 0;
    color: #7b3fb2;
    font-size: 28px;
    font-weight: 900;
    line-height: 1.2;
}

.summary-desc {
    color: #9a8ca8;
    font-size: 13px;
    margin-top: 8px;
}

/* TOOLBAR */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin: 28px 0 18px;
}

.toolbar-title h4 {
    color: #6f2da8;
    font-weight: 850;
    margin: 0;
}

.toolbar-title p {
    margin: 5px 0 0;
    color: #81758d;
    font-size: 14px;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 15px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    box-shadow: 0 9px 20px rgba(142, 68, 173, 0.20);
    transition: .25s ease;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

/* CARD TABLE */
.card-box {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 26px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    overflow: hidden;
}

.card-box-body {
    padding: 0;
}

/* TABLE */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 15px 14px;
    font-size: 13px;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}

.table tbody td {
    padding: 15px 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
    color: #44324f;
    font-size: 14px;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

.nomor {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: #f4eaff;
    color: #7b3fb2;
    border: 1px solid #eadcff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
}

.product-img {
    width: 78px;
    height: 78px;
    border-radius: 18px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f4eaff;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.10);
}

.no-image {
    width: 78px;
    height: 78px;
    border-radius: 18px;
    background: #f4eaff;
    color: #8e44ad;
    border: 1px dashed #c9a7ec;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.produk-info {
    display: flex;
    flex-direction: column;
}

.produk-name {
    font-weight: 850;
    color: #33223f;
    font-size: 15px;
}

.produk-note {
    color: #9a8ca8;
    font-size: 12px;
    margin-top: 3px;
}

.harga {
    color: #7b3fb2;
    font-weight: 900;
    white-space: nowrap;
}

.stok-number {
    color: #33223f;
    font-weight: 900;
    font-size: 16px;
}

.satuan-badge {
    background: #f4eaff;
    color: #7b3fb2;
    border: 1px solid #eadcff;
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 850;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.badge-stok {
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 850;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.stok-aman {
    background: #ecfdf5;
    color: #047857;
}

.stok-menipis {
    background: #fff7ed;
    color: #c2410c;
}

.stok-habis {
    background: #fef2f2;
    color: #b91c1c;
}

/* ACTION */
.action-box {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-action {
    border: none;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: .25s ease;
}

.btn-edit {
    background: #fff7ed;
    color: #c2410c;
}

.btn-edit:hover {
    background: #fed7aa;
    color: #9a3412;
    transform: translateY(-2px);
}

/* EMPTY */
.empty-data {
    text-align: center;
    color: #8d7a9b;
    padding: 46px 20px;
}

.empty-data i {
    font-size: 38px;
    color: #b57edc;
    margin-bottom: 12px;
}

.empty-data h5 {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 6px;
}

.empty-data p {
    margin: 0;
    color: #8d7a9b;
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

    .summary-value {
        font-size: 24px;
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

    .toolbar {
        align-items: stretch;
    }

    .btn-lavender {
        width: 100%;
        justify-content: center;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin: 14px;
        border: 1px solid #eadcff;
        border-radius: 20px;
        overflow: hidden;
        background: white;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        border-bottom: 1px solid #f0e3ff;
    }

    .table tbody td::before {
        content: attr(data-label);
        font-weight: 850;
        color: #6f2da8;
        min-width: 120px;
    }

    .table tbody td:last-child {
        border-bottom: none;
    }

    .action-box {
        justify-content: flex-end;
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
                <i class="fa-solid fa-warehouse"></i>
            </div>

            <h2 class="page-title">Data Stok Produk</h2>
            <p class="page-subtitle">
                Kelola jumlah stok produk ready stock The Four Label agar persediaan tetap terpantau.
            </p>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>

                    <p class="summary-label">Total Stok</p>
                    <h3 class="summary-value"><?= $totalStok; ?></h3>
                    <div class="summary-desc">Jumlah seluruh stok produk</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-shirt"></i>
                    </div>

                    <p class="summary-label">Produk Ada Stok</p>
                    <h3 class="summary-value"><?= $totalProdukStok; ?></h3>
                    <div class="summary-desc">Produk yang memiliki data stok</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <p class="summary-label">Stok Habis</p>
                    <h3 class="summary-value"><?= $totalHabis; ?></h3>
                    <div class="summary-desc">Produk dengan stok kosong</div>
                </div>
            </div>
        </div>

    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="toolbar-title">
            <h4>Daftar Stok</h4>
            <p>Semua stok produk ditampilkan berdasarkan data terbaru.</p>
        </div>

        <a href="tambah-stok.php" class="btn-lavender">
            <i class="fa-solid fa-plus"></i>
            Tambah Stok
        </a>
    </div>

    <!-- TABLE -->
    <div class="card-box">
        <div class="card-box-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah Stok</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)) { 
                                $gambar = tampilGambarProduk($row['gambar']);

                                if ($row['jumlahStok'] <= 0) {
                                    $statusText = "Habis";
                                    $statusClass = "stok-habis";
                                    $statusIcon = "fa-circle-xmark";
                                } elseif ($row['jumlahStok'] <= 5) {
                                    $statusText = "Menipis";
                                    $statusClass = "stok-menipis";
                                    $statusIcon = "fa-triangle-exclamation";
                                } else {
                                    $statusText = "Aman";
                                    $statusClass = "stok-aman";
                                    $statusIcon = "fa-circle-check";
                                }
                            ?>

                                <tr>
                                    <td data-label="No">
                                        <span class="nomor">
                                            <?= $no++; ?>
                                        </span>
                                    </td>

                                    <td data-label="Gambar">
                                        <?php if ($gambar != "") { ?>
                                            <img 
                                                src="<?= htmlspecialchars($gambar); ?>" 
                                                class="product-img"
                                                alt="<?= htmlspecialchars($row['namaProduk']); ?>">
                                        <?php } else { ?>
                                            <div class="no-image">
                                                <i class="fa-regular fa-image"></i>
                                            </div>
                                        <?php } ?>
                                    </td>

                                    <td data-label="Produk">
                                        <div class="produk-info">
                                            <span class="produk-name">
                                                <?= htmlspecialchars($row['namaProduk']); ?>
                                            </span>
                                            <span class="produk-note">
                                                Produk ready stock
                                            </span>
                                        </div>
                                    </td>

                                    <td data-label="Harga">
                                        <span class="harga">
                                            Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                        </span>
                                    </td>

                                    <td data-label="Jumlah Stok">
                                        <span class="stok-number">
                                            <?= htmlspecialchars($row['jumlahStok']); ?>
                                        </span>
                                    </td>

                                    <td data-label="Satuan">
                                        <span class="satuan-badge">
                                            <i class="fa-solid fa-tag"></i>
                                            <?= htmlspecialchars($row['satuan']); ?>
                                        </span>
                                    </td>

                                    <td data-label="Status">
                                        <span class="badge-stok <?= $statusClass; ?>">
                                            <i class="fa-solid <?= $statusIcon; ?>"></i>
                                            <?= $statusText; ?>
                                        </span>
                                    </td>

                                    <td data-label="Aksi">
                                        <div class="action-box">
                                            <a 
                                                href="edit-stok.php?id=<?= $row['idStok']; ?>"
                                                class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="8">
                                    <div class="empty-data">
                                        <div>
                                            <i class="fa-regular fa-folder-open"></i>
                                        </div>

                                        <h5>Belum ada data stok</h5>
                                        <p>Silakan tambahkan stok produk terlebih dahulu.</p>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</main>

</body>
</html>