<?php
session_start();

require "../koneksi.php";

/* AMBIL DATA PRODUK */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        produk.*,
        kategori.namaKategori
    FROM produk
    LEFT JOIN kategori 
        ON produk.idKategori = kategori.idKategori
    ORDER BY produk.idProduk DESC"
);

if (!$query) {
    die("Query produk error: " . mysqli_error($koneksi));
}

/* FUNGSI CEK GAMBAR */
function tampilGambarProduk($namaGambar) {
    $namaGambar = trim($namaGambar);

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

<title>Data Produk</title>

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
}

/* CONTENT */
.content {
    padding: 32px;
    min-height: 100vh;
}

/* HEADER */
.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 28px;
    border-radius: 24px;
    margin-bottom: 28px;
    box-shadow: 0 12px 28px rgba(111, 66, 193, 0.20);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -60px;
    right: -40px;
}

.page-header h3 {
    position: relative;
    z-index: 2;
    font-weight: 850;
    margin-bottom: 6px;
}

.page-header p {
    position: relative;
    z-index: 2;
    margin: 0;
    opacity: 0.92;
}

/* BUTTON */
.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 18px;
    font-weight: 750;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s ease;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(142, 68, 173, 0.20);
}

.btn-edit {
    background: #facc15;
    color: #5a4300;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    padding: 8px 13px;
}

.btn-edit:hover {
    background: #eab308;
    color: #4a3700;
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    padding: 8px 13px;
}

.btn-delete:hover {
    background: #dc2626;
    color: white;
}

/* CARD */
.card-custom {
    border: none;
    border-radius: 24px;
    background: white;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
    overflow: hidden;
}

.card-body {
    padding: 24px;
}

/* TABLE */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 15px;
    font-size: 14px;
    white-space: nowrap;
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

/* PRODUCT */
.produk-name {
    font-weight: 800;
    color: #4b2e63;
}

.kategori-badge {
    background: #eadcff;
    color: #6f2da8;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    display: inline-block;
}

.harga {
    color: #7b3fb2;
    font-weight: 850;
    white-space: nowrap;
}

.product-img {
    width: 82px;
    height: 82px;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f1e3ff;
}

.no-image {
    width: 82px;
    height: 82px;
    border-radius: 16px;
    background: #f1e3ff;
    color: #8e44ad;
    border: 1px dashed #c9a7ec;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
}

.aksi {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 30px;
}

/* SUMMARY */
.summary-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    border: 1px solid #eadcff;
    box-shadow: 0 8px 22px rgba(142, 68, 173, 0.10);
    margin-bottom: 22px;
}

.summary-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: #f1e3ff;
    color: #8e44ad;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
}

.summary-card p {
    margin: 0;
    color: #777;
    font-size: 14px;
}

.summary-card h3 {
    margin: 6px 0 0;
    color: #7b3fb2;
    font-weight: 850;
}

@media (max-width: 768px) {
    .content {
        padding: 20px;
    }

    .page-header {
        padding: 22px;
    }

    .aksi {
        flex-direction: column;
    }

    .btn-edit,
    .btn-delete {
        width: 100%;
    }
}
</style>

</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 p-0">
            <?php include "sidebar.php"; ?>
        </div>

        <!-- CONTENT -->
        <div class="col-md-10 content">

            <!-- HEADER -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3>👕 Data Produk</h3>
                        <p>Kelola produk The Four Label, mulai dari nama, harga, kategori, dan gambar.</p>
                    </div>

                    <a href="tambah-produk.php" class="btn-lavender">
                        + Tambah Produk
                    </a>
                </div>
            </div>

            <!-- SUMMARY -->
            <?php
            $totalProduk = mysqli_num_rows($query);

            $qTotalHarga = mysqli_query(
                $koneksi,
                "SELECT COALESCE(SUM(harga), 0) AS totalHarga FROM produk"
            );

            $totalHarga = 0;
            if ($qTotalHarga) {
                $dataHarga = mysqli_fetch_assoc($qTotalHarga);
                $totalHarga = $dataHarga['totalHarga'];
            }

            $qKategori = mysqli_query(
                $koneksi,
                "SELECT COUNT(*) AS totalKategori FROM kategori"
            );

            $totalKategori = 0;
            if ($qKategori) {
                $dataKategori = mysqli_fetch_assoc($qKategori);
                $totalKategori = $dataKategori['totalKategori'];
            }
            ?>

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">👕</div>
                        <p>Total Produk</p>
                        <h3><?= $totalProduk; ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">🏷️</div>
                        <p>Total Kategori</p>
                        <h3><?= $totalKategori; ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">💰</div>
                        <p>Total Nilai Harga Produk</p>
                        <h3>Rp <?= number_format($totalHarga, 0, ',', '.'); ?></h3>
                    </div>
                </div>

            </div>

            <!-- TABLE -->
            <div class="card card-custom">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if ($totalProduk > 0) { ?>

                                    <?php 
                                    $no = 1;
                                    mysqli_data_seek($query, 0);
                                    while ($row = mysqli_fetch_assoc($query)) { 
                                        $gambar = tampilGambarProduk($row['gambar']);
                                    ?>

                                        <tr>

                                            <td><?= $no++; ?></td>

                                            <td>
                                                <div class="produk-name">
                                                    <?= htmlspecialchars($row['namaProduk']); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?php if (!empty($row['namaKategori'])) { ?>
                                                    <span class="kategori-badge">
                                                        <?= htmlspecialchars($row['namaKategori']); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="text-muted">Tanpa kategori</span>
                                                <?php } ?>
                                            </td>

                                            <td>
                                                <span class="harga">
                                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($gambar != "") { ?>

                                                    <img
                                                        src="<?= htmlspecialchars($gambar); ?>"
                                                        class="product-img"
                                                        alt="<?= htmlspecialchars($row['namaProduk']); ?>">

                                                <?php } else { ?>

                                                    <div class="no-image">
                                                        No<br>Image
                                                    </div>

                                                <?php } ?>
                                            </td>

                                            <td>
                                                <div class="aksi">

                                                    <a
                                                        href="edit-produk.php?id=<?= $row['idProduk']; ?>"
                                                        class="btn btn-sm btn-edit">
                                                        Edit
                                                    </a>

                                                    <a
                                                        href="hapus-produk.php?id=<?= $row['idProduk']; ?>"
                                                        class="btn btn-sm btn-delete"
                                                        onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                        Hapus
                                                    </a>

                                                </div>
                                            </td>

                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>
                                        <td colspan="6" class="empty-data">
                                            Belum ada data produk.
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

</body>

</html>