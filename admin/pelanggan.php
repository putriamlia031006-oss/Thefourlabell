<?php
require "auth.php";
require "../koneksi.php";

/* AMBIL DATA PELANGGAN + TOTAL TRANSAKSI */
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

/* RINGKASAN */
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

<title>Data Pelanggan</title>

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
    font-size: 14px;
}

.admin-layout {
    display: flex;
}

.sidebar-wrapper {
    width: 230px;
    min-width: 230px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

.main-content {
    margin-left: 230px;
    width: calc(100% - 230px);
    padding: 22px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 20px 24px;
    border-radius: 18px;
    margin-bottom: 20px;
    box-shadow: 0 8px 20px rgba(111, 66, 193, 0.18);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -45px;
    right: -30px;
}

.page-header h3 {
    position: relative;
    z-index: 2;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}

.page-header p {
    position: relative;
    z-index: 2;
    margin: 0;
    opacity: 0.92;
    font-size: 13px;
}

.card-box {
    background: white;
    border: none;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 8px 20px rgba(142, 68, 173, 0.10);
    border: 1px solid #eadcff;
}

.summary-card {
    background: white;
    border-radius: 16px;
    padding: 15px;
    border: 1px solid #eadcff;
    box-shadow: 0 6px 16px rgba(142, 68, 173, 0.09);
    margin-bottom: 16px;
}

.summary-icon {
    width: 40px;
    height: 40px;
    border-radius: 13px;
    background: #f1e3ff;
    color: #8e44ad;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 8px;
}

.summary-card p {
    margin: 0;
    color: #777;
    font-size: 13px;
}

.summary-card h3 {
    margin: 4px 0 0;
    color: #7b3fb2;
    font-size: 21px;
    font-weight: 800;
}

.btn-tambah {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 11px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
}

.btn-tambah:hover {
    color: white;
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
}

.btn-edit,
.btn-hapus {
    border: none;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    display: inline-block;
}

.btn-edit {
    background: #fef3c7;
    color: #b45309;
}

.btn-edit:hover {
    background: #fde68a;
    color: #92400e;
}

.btn-hapus {
    background: #fee2e2;
    color: #b91c1c;
}

.btn-hapus:hover {
    background: #fecaca;
    color: #991b1b;
}

.table {
    margin-bottom: 0;
    font-size: 13px;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 10px;
    font-size: 13px;
    white-space: nowrap;
}

.table tbody td {
    padding: 10px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.table tbody tr:hover {
    background: #fbf7ff;
}

.nama {
    font-weight: 800;
    color: #4b2e63;
    font-size: 13px;
}

.email {
    color: #777;
    font-size: 12px;
}

.badge-id,
.badge-transaksi,
.badge-diskon {
    padding: 5px 9px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 12px;
    display: inline-block;
    white-space: nowrap;
}

.badge-id {
    background: #eadcff;
    color: #6f2da8;
}

.badge-transaksi {
    background: #dcfce7;
    color: #15803d;
}

.badge-diskon {
    background: #fef3c7;
    color: #b45309;
}

.total-belanja {
    color: #7b3fb2;
    font-weight: 800;
    white-space: nowrap;
}

.alamat {
    max-width: 260px;
    color: #555;
    font-size: 13px;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 22px;
}

.action-box {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .admin-layout {
        display: block;
    }

    .sidebar-wrapper {
        position: relative;
        width: 100%;
        min-width: 100%;
        height: auto;
    }

    .main-content {
        margin-left: 0;
        width: 100%;
        padding: 16px;
    }

    .page-header {
        padding: 18px;
    }
}
</style>
</head>

<body>

<div class="admin-layout">

    <div class="sidebar-wrapper">
        <?php include "sidebar.php"; ?>
    </div>

    <div class="main-content">

        <div class="page-header">
            <h3>👥 Data Pelanggan</h3>
            <p>Kelola data pelanggan, total transaksi, dan total belanja pelanggan The Four Label.</p>
        </div>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-icon">👥</div>
                    <p>Total Pelanggan</p>
                    <h3><?= $totalPelanggan; ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-icon">🧾</div>
                    <p>Total Transaksi</p>
                    <h3><?= $totalSemuaTransaksi; ?></h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="summary-card">
                    <div class="summary-icon">💰</div>
                    <p>Total Belanja</p>
                    <h3>Rp <?= number_format($totalSemuaBelanja, 0, ',', '.'); ?></h3>
                </div>
            </div>

        </div>

        <div class="card-box">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color:#6f2da8; font-size:16px;">
                    Daftar Pelanggan
                </h5>

                <a href="tambah-pelanggan.php" class="btn-tambah">
                    + Tambah Pelanggan
                </a>
            </div>

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
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <span class="badge-id">
                                            #<?= htmlspecialchars($row['idPelanggan']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="nama">
                                            <?= $row['nama'] ? htmlspecialchars($row['nama']) : "Nama belum tersedia"; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="email">
                                            <?= $row['email'] ? htmlspecialchars($row['email']) : "-"; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?= $row['noHp'] ? htmlspecialchars($row['noHp']) : "-"; ?>
                                    </td>

                                    <td>
                                        <div class="alamat">
                                            <?= $row['alamat'] ? htmlspecialchars($row['alamat']) : "-"; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge-transaksi">
                                            <?= $row['totalTransaksi']; ?> transaksi
                                        </span>
                                    </td>

                                    <td>
                                        <strong class="total-belanja">
                                            Rp <?= number_format($row['totalBelanja'], 0, ',', '.'); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php if ($row['totalTransaksi'] >= 5) { ?>
                                            <span class="badge-diskon">
                                                Diskon 20%
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge-id">
                                                <?= $row['totalTransaksi']; ?>/5
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?= $row['idUser'] ? htmlspecialchars($row['idUser']) : "-"; ?>
                                    </td>

                                    <td>
                                        <div class="action-box">
                                            <a 
                                                href="edit-pelanggan.php?id=<?= $row['idPelanggan']; ?>" 
                                                class="btn-edit">
                                                Edit
                                            </a>

                                            <a 
                                                href="hapus-pelanggan.php?id=<?= $row['idPelanggan']; ?>" 
                                                class="btn-hapus"
                                                onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="11" class="empty-data">
                                    Belum ada data pelanggan.
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

</body>
</html>