<?php
require "auth.php";
require "../koneksi.php";

/* AMBIL DATA STOK */
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

/* HITUNG RINGKASAN */
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

<title>Data Stok</title>

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

.main-content {
    padding: 32px;
    min-height: 100vh;
}

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

.page-header h3,
.page-header p {
    position: relative;
    z-index: 2;
}

.page-header h3 {
    font-weight: 850;
    margin-bottom: 6px;
}

.page-header p {
    margin: 0;
    opacity: 0.92;
}

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

.card-box {
    background: white;
    border: none;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 14px;
    font-size: 14px;
    white-space: nowrap;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

.product-img {
    width: 74px;
    height: 74px;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f1e3ff;
}

.no-image {
    width: 74px;
    height: 74px;
    border-radius: 16px;
    background: #f1e3ff;
    color: #8e44ad;
    border: 1px dashed #c9a7ec;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 11px;
    font-weight: 800;
}

.produk-name {
    font-weight: 850;
    color: #4b2e63;
}

.harga {
    color: #7b3fb2;
    font-weight: 850;
    white-space: nowrap;
}

.badge-stok {
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 13px;
    display: inline-block;
}

.stok-aman {
    background: #dcfce7;
    color: #15803d;
}

.stok-menipis {
    background: #fff3cd;
    color: #856404;
}

.stok-habis {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 8px 14px;
    font-weight: 750;
    text-decoration: none;
    display: inline-block;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
}

.btn-edit {
    background: #facc15;
    color: #5a4300;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    padding: 8px 13px;
    text-decoration: none;
    display: inline-block;
}

.btn-edit:hover {
    background: #eab308;
    color: #4a3700;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 30px;
}

@media (max-width: 768px) {
    .main-content {
        padding: 20px;
    }

    .page-header {
        padding: 22px;
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
        <div class="col-md-10 main-content">

            <div class="page-header">
                <h3>🧵 Data Stok Produk</h3>
                <p>Kelola jumlah stok produk ready stock The Four Label.</p>
            </div>

            <!-- RINGKASAN -->
            <div class="row g-3">

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">📦</div>
                        <p>Total Stok</p>
                        <h3><?= $totalStok; ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">👕</div>
                        <p>Produk Ada Stok</p>
                        <h3><?= $totalProdukStok; ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">⚠️</div>
                        <p>Stok Habis</p>
                        <h3><?= $totalHabis; ?></h3>
                    </div>
                </div>

            </div>

            <div class="card-box">

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0" style="color:#6f2da8; font-weight:850;">
                        Daftar Stok
                    </h5>

                    <a href="tambah-stok.php" class="btn-lavender">
                        + Tambah Stok
                    </a>
                </div>

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
                                    } elseif ($row['jumlahStok'] <= 5) {
                                        $statusText = "Menipis";
                                        $statusClass = "stok-menipis";
                                    } else {
                                        $statusText = "Aman";
                                        $statusClass = "stok-aman";
                                    }
                                ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

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
                                            <div class="produk-name">
                                                <?= htmlspecialchars($row['namaProduk']); ?>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="harga">
                                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars($row['jumlahStok']); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['satuan']); ?>
                                        </td>

                                        <td>
                                            <span class="badge-stok <?= $statusClass; ?>">
                                                <?= $statusText; ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a 
                                                href="edit-stok.php?id=<?= $row['idStok']; ?>"
                                                class="btn-edit btn-sm">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="8" class="empty-data">
                                        Belum ada data stok.
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

</body>
</html>