<?php
require "auth.php";
require "../koneksi.php";

/* =========================
   AMBIL DATA PELANGGAN
========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        pelanggan.idPelanggan,
        pelanggan.idUser,
        pelanggan.noHp,
        pelanggan.alamat,
        user.nama,
        user.email,
        COUNT(pesanan.idPesanan) AS totalTransaksi,
        COALESCE(SUM(pesanan.total), 0) AS totalBelanja
    FROM pelanggan
    LEFT JOIN user 
        ON pelanggan.idUser = user.idUser
    LEFT JOIN pesanan
        ON pelanggan.idPelanggan = pesanan.idPelanggan
    GROUP BY 
        pelanggan.idPelanggan,
        pelanggan.idUser,
        pelanggan.noHp,
        pelanggan.alamat,
        user.nama,
        user.email
    ORDER BY pelanggan.idPelanggan DESC"
);

if (!$query) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

/* =========================
   DATA RINGKASAN
========================= */
$totalPelanggan = 0;
$totalSemuaTransaksi = 0;
$totalSemuaBelanja = 0;

$summary = mysqli_query(
    $koneksi,
    "SELECT 
        COUNT(DISTINCT pelanggan.idPelanggan) AS totalPelanggan,
        COUNT(pesanan.idPesanan) AS totalTransaksi,
        COALESCE(SUM(pesanan.total), 0) AS totalBelanja
    FROM pelanggan
    LEFT JOIN pesanan
        ON pelanggan.idPelanggan = pesanan.idPelanggan"
);

if ($summary) {
    $s = mysqli_fetch_assoc($summary);
    $totalPelanggan = $s['totalPelanggan'];
    $totalSemuaTransaksi = $s['totalTransaksi'];
    $totalSemuaBelanja = $s['totalBelanja'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Data Pelanggan - Admin The Four Label</title>

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

/* SUMMARY CARD */
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

.btn-tambah {
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

.btn-tambah:hover {
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

.customer-name {
    font-weight: 850;
    color: #33223f;
    font-size: 15px;
}

.customer-email {
    color: #81758d;
    font-size: 13px;
    margin-top: 3px;
}

.customer-contact {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #4b2e63;
    font-weight: 650;
}

.customer-address {
    max-width: 280px;
    color: #5f526a;
    line-height: 1.5;
}

.badge-id,
.badge-transaksi,
.badge-diskon,
.badge-progress {
    padding: 7px 11px;
    border-radius: 999px;
    font-weight: 850;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.badge-id {
    background: #f4eaff;
    color: #7b3fb2;
    border: 1px solid #eadcff;
}

.badge-transaksi {
    background: #ecfdf5;
    color: #047857;
}

.badge-diskon {
    background: #fff7ed;
    color: #c2410c;
}

.badge-progress {
    background: #f1e3ff;
    color: #8e44ad;
}

.total-belanja {
    color: #7b3fb2;
    font-weight: 900;
    white-space: nowrap;
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

.btn-hapus {
    background: #fef2f2;
    color: #b91c1c;
}

.btn-hapus:hover {
    background: #fecaca;
    color: #991b1b;
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

    .btn-tambah {
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

    .customer-address {
        max-width: 170px;
        text-align: right;
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
                <i class="fa-solid fa-users"></i>
            </div>

            <h2 class="page-title">Data Pelanggan</h2>
            <p class="page-subtitle">
                Kelola data pelanggan, riwayat transaksi, total belanja, dan status diskon pelanggan The Four Label.
            </p>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>

                    <p class="summary-label">Total Pelanggan</p>
                    <h3 class="summary-value"><?= $totalPelanggan; ?></h3>
                    <div class="summary-desc">Jumlah pelanggan yang terdaftar</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>

                    <p class="summary-label">Total Transaksi</p>
                    <h3 class="summary-value"><?= $totalSemuaTransaksi; ?></h3>
                    <div class="summary-desc">Seluruh transaksi pelanggan</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-icon">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>

                    <p class="summary-label">Total Belanja</p>
                    <h3 class="summary-value">
                        Rp <?= number_format($totalSemuaBelanja, 0, ',', '.'); ?>
                    </h3>
                    <div class="summary-desc">Akumulasi nilai belanja pelanggan</div>
                </div>
            </div>
        </div>

    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
        <div class="toolbar-title">
            <h4>Daftar Pelanggan</h4>
            <p>Data pelanggan ditampilkan berdasarkan data terbaru.</p>
        </div>

        <a href="tambah-pelanggan.php" class="btn-tambah">
            <i class="fa-solid fa-plus"></i>
            Tambah Pelanggan
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
                            <th>ID Pelanggan</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th>Total Transaksi</th>
                            <th>Total Belanja</th>
                            <th>Diskon</th>
                            <th>ID User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0) { ?>

                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)) { 
                            ?>

                                <tr>
                                    <td data-label="No">
                                        <?= $no++; ?>
                                    </td>

                                    <td data-label="ID Pelanggan">
                                        <span class="badge-id">
                                            <i class="fa-solid fa-id-card"></i>
                                            #<?= htmlspecialchars($row['idPelanggan']); ?>
                                        </span>
                                    </td>

                                    <td data-label="Nama">
                                        <div class="customer-name">
                                            <?= $row['nama'] ? htmlspecialchars($row['nama']) : "Nama belum tersedia"; ?>
                                        </div>
                                    </td>

                                    <td data-label="Email">
                                        <div class="customer-email">
                                            <?= $row['email'] ? htmlspecialchars($row['email']) : "-"; ?>
                                        </div>
                                    </td>

                                    <td data-label="No HP">
                                        <span class="customer-contact">
                                            <i class="fa-solid fa-phone"></i>
                                            <?= $row['noHp'] ? htmlspecialchars($row['noHp']) : "-"; ?>
                                        </span>
                                    </td>

                                    <td data-label="Alamat">
                                        <div class="customer-address">
                                            <?= $row['alamat'] ? htmlspecialchars($row['alamat']) : "-"; ?>
                                        </div>
                                    </td>

                                    <td data-label="Total Transaksi">
                                        <span class="badge-transaksi">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                            <?= $row['totalTransaksi']; ?> transaksi
                                        </span>
                                    </td>

                                    <td data-label="Total Belanja">
                                        <strong class="total-belanja">
                                            Rp <?= number_format($row['totalBelanja'], 0, ',', '.'); ?>
                                        </strong>
                                    </td>

                                    <td data-label="Diskon">
                                        <?php if ($row['totalTransaksi'] >= 5) { ?>
                                            <span class="badge-diskon">
                                                <i class="fa-solid fa-percent"></i>
                                                Diskon 20%
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge-progress">
                                                <i class="fa-solid fa-clock"></i>
                                                <?= $row['totalTransaksi']; ?>/5
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td data-label="ID User">
                                        <span class="badge-id">
                                            <?= $row['idUser'] ? htmlspecialchars($row['idUser']) : "-"; ?>
                                        </span>
                                    </td>

                                    <td data-label="Aksi">
                                        <div class="action-box">
                                            <a 
                                                href="edit-pelanggan.php?id=<?= $row['idPelanggan']; ?>" 
                                                class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Edit
                                            </a>

                                            <a 
                                                href="hapus-pelanggan.php?id=<?= $row['idPelanggan']; ?>" 
                                                class="btn-action btn-hapus"
                                                onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">
                                                <i class="fa-solid fa-trash"></i>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="11">
                                    <div class="empty-data">
                                        <div>
                                            <i class="fa-regular fa-folder-open"></i>
                                        </div>

                                        <h5>Belum ada data pelanggan</h5>
                                        <p>Silakan tambahkan data pelanggan terlebih dahulu.</p>
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