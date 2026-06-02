<?php
session_start();
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

.page-header h3 {
    position: relative;
    z-index: 2;
    font-weight: 800;
    margin-bottom: 6px;
}

.page-header p {
    position: relative;
    z-index: 2;
    margin: 0;
    opacity: 0.92;
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

.nama {
    font-weight: 800;
    color: #4b2e63;
}

.email {
    color: #777;
    font-size: 13px;
}

.badge-id {
    background: #eadcff;
    color: #6f2da8;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 13px;
    display: inline-block;
}

.badge-transaksi {
    background: #dcfce7;
    color: #15803d;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 13px;
    display: inline-block;
    white-space: nowrap;
}

.badge-diskon {
    background: #fef3c7;
    color: #b45309;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 13px;
    display: inline-block;
    white-space: nowrap;
}

.total-belanja {
    color: #7b3fb2;
    font-weight: 800;
    white-space: nowrap;
}

.alamat {
    max-width: 360px;
    color: #555;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 30px;
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
    font-weight: 800;
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
                <h3>👥 Data Pelanggan</h3>
                <p>Kelola data pelanggan, total transaksi, dan total belanja pelanggan The Four Label.</p>
            </div>

            <?php
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

            <!-- RINGKASAN -->
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
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="10" class="empty-data">
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
</div>

</body>
</html>