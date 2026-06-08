<?php

require "auth.php";
require "../koneksi.php";

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM produk
     ORDER BY idProduk DESC"
);

if (!$query) {
    die("Query produk error: " . mysqli_error($koneksi));
}

function gambarProdukAdmin($namaGambar) {
    $namaGambar = trim($namaGambar ?? "");

    if ($namaGambar == "") {
        return "";
    }

    $paths = [
        "../image/" . $namaGambar,
        "../upload/" . $namaGambar,
        "../uploads/" . $namaGambar,
        "../assets/" . $namaGambar
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return "";
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Produk - Admin The Four Label</title>

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

/* TOOLBAR */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
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

/* CARD */
.card-custom {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 26px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    overflow: hidden;
}

.card-custom .card-body {
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
    padding: 16px;
    font-size: 13px;
    font-weight: 850;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}

.table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-color: #f0e3ff;
    color: #44324f;
    font-size: 14px;
}

.table tbody tr:hover {
    background: #fbf7ff;
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

.produk-id {
    font-size: 12px;
    color: #9a8ca8;
    margin-top: 3px;
}

.price-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #f4eaff;
    color: #7b3fb2;
    border: 1px solid #eadcff;
    padding: 8px 12px;
    border-radius: 999px;
    font-weight: 850;
    white-space: nowrap;
}

.product-image {
    width: 78px;
    height: 78px;
    border-radius: 18px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f4eaff;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.10);
}

.no-image-box {
    width: 78px;
    height: 78px;
    border-radius: 18px;
    background: #f4eaff;
    color: #8e44ad;
    border: 1px solid #eadcff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

/* ACTION */
.action-group {
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

.btn-delete {
    background: #fef2f2;
    color: #b91c1c;
}

.btn-delete:hover {
    background: #fecaca;
    color: #991b1b;
    transform: translateY(-2px);
}

/* EMPTY */
.empty-data {
    text-align: center;
    padding: 45px 20px;
    color: #8d7a9b;
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
    }

    .table tbody td:last-child {
        border-bottom: none;
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
                <i class="fa-solid fa-shirt"></i>
            </div>

            <h2 class="page-title">Data Produk</h2>
            <p class="page-subtitle">
                Kelola daftar produk The Four Label, mulai dari nama produk, harga, gambar, hingga data edit dan hapus.
            </p>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-title">
            <h4>Daftar Produk</h4>
            <p>Semua produk ditampilkan berdasarkan data terbaru.</p>
        </div>

        <a href="tambah-produk.php" class="btn-lavender">
            <i class="fa-solid fa-plus"></i>
            Tambah Produk
        </a>
    </div>

    <div class="card-custom">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                                <?php $gambar = gambarProdukAdmin($row['gambar']); ?>

                                <tr>
                                    <td data-label="Nama Produk">
                                        <div class="produk-info">
                                            <span class="produk-name">
                                                <?= htmlspecialchars($row['namaProduk']); ?>
                                            </span>

                                            <span class="produk-id">
                                                ID Produk: <?= htmlspecialchars($row['idProduk']); ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td data-label="Harga">
                                        <span class="price-badge">
                                            <i class="fa-solid fa-tag"></i>
                                            Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                        </span>
                                    </td>

                                    <td data-label="Gambar">
                                        <?php if ($gambar != "") { ?>
                                            <img
                                                src="<?= htmlspecialchars($gambar); ?>"
                                                class="product-image"
                                                alt="<?= htmlspecialchars($row['namaProduk']); ?>">
                                        <?php } else { ?>
                                            <div class="no-image-box">
                                                <i class="fa-regular fa-image"></i>
                                            </div>
                                        <?php } ?>
                                    </td>

                                    <td data-label="Aksi">
                                        <div class="action-group">
                                            <a
                                                href="edit-produk.php?id=<?= $row['idProduk']; ?>"
                                                class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit
                                            </a>

                                            <a
                                                href="hapus-produk.php?id=<?= $row['idProduk']; ?>"
                                                class="btn-action btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus produk ini?');">
                                                <i class="fa-solid fa-trash"></i>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="4">
                                    <div class="empty-data">
                                        <div>
                                            <i class="fa-regular fa-folder-open"></i>
                                        </div>

                                        <h5>Belum ada produk</h5>
                                        <p>Silakan tambah produk baru untuk ditampilkan di halaman ini.</p>
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