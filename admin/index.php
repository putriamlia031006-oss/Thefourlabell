<?php

require "auth.php";
require "../koneksi.php";

/* =========================
   DATA RINGKASAN
========================= */

$qProduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$produk = mysqli_fetch_assoc($qProduk)['total'];

$qPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$pesanan = mysqli_fetch_assoc($qPesanan)['total'];

$qPelanggan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pelanggan");
$pelanggan = mysqli_fetch_assoc($qPelanggan)['total'];

$qStok = mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlahStok), 0) AS total FROM stok_produk");
$stok = mysqli_fetch_assoc($qStok)['total'];

$qPendapatan = mysqli_query($koneksi, "SELECT COALESCE(SUM(total), 0) AS total FROM pesanan");
$pendapatan = mysqli_fetch_assoc($qPendapatan)['total'];

$qMenunggu = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total 
     FROM pesanan 
     WHERE status LIKE '%Menunggu%'"
);
$menunggu = mysqli_fetch_assoc($qMenunggu)['total'];

/* =========================
   PESANAN TERBARU
========================= */
$pesananTerbaru = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        user.nama AS namaPelanggan
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    ORDER BY pesanan.idPesanan DESC
    LIMIT 5"
);

if (!$pesananTerbaru) {
    die("Query pesanan terbaru error: " . mysqli_error($koneksi));
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}

function statusClass($status) {
    $status = strtolower($status);

    if ($status == "menunggu") {
        return "status-menunggu";
    } elseif ($status == "menunggu verifikasi pembayaran") {
        return "status-verifikasi";
    } elseif ($status == "diproses" || $status == "proses") {
        return "status-proses";
    } elseif ($status == "selesai") {
        return "status-selesai";
    } elseif ($status == "batal") {
        return "status-batal";
    } elseif ($status == "menunggu pembayaran tunai") {
        return "status-tunai";
    } else {
        return "status-menunggu";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
html, body {
    overflow-x: hidden;
}

body {
    background: #f8f4ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
    margin: 0;
}

/* SIDEBAR */
.sidebar-area {
    min-height: 100vh;
    padding: 0;
}

/* CONTENT */
.content {
    padding: 32px;
    min-height: 100vh;
}

/* HEADER */
.header-dashboard {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 26px;
    padding: 30px;
    margin-bottom: 28px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.20);
    position: relative;
    overflow: hidden;
}

.header-dashboard::before {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -65px;
    right: -45px;
}

.header-dashboard h2,
.header-dashboard p {
    position: relative;
    z-index: 2;
}

.header-dashboard h2 {
    font-weight: 850;
    margin-bottom: 8px;
}

.header-dashboard p {
    margin: 0;
    opacity: 0.92;
}

/* CARD */
.card-dashboard {
    border: none;
    border-radius: 24px;
    padding: 24px;
    background: white;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
    transition: .25s ease;
    height: 100%;
}

.card-dashboard:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 35px rgba(142, 68, 173, 0.18);
}

.icon-box {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: #f1e3ff;
    color: #8e44ad;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    margin-bottom: 18px;
}

.total {
    font-size: 32px;
    font-weight: 850;
    color: #7b3fb2;
    margin-bottom: 4px;
}

.label {
    color: #777;
    font-size: 15px;
    font-weight: 600;
}

/* SECTION */
.section-card {
    background: white;
    border-radius: 24px;
    padding: 24px;
    margin-top: 28px;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
}

.section-title {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 18px;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 16px;
    font-weight: 750;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s ease;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

/* TABLE */
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

.badge-status {
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    display: inline-block;
}

.status-menunggu {
    background: #fff3cd;
    color: #856404;
}

.status-verifikasi {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-proses {
    background: #f1e3ff;
    color: #7b3fb2;
}

.status-selesai {
    background: #dcfce7;
    color: #15803d;
}

.status-batal {
    background: #fee2e2;
    color: #b91c1c;
}

.status-tunai {
    background: #e0f2fe;
    color: #0369a1;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 25px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .content {
        padding: 22px;
    }

    .header-dashboard {
        padding: 24px;
    }

    .total {
        font-size: 28px;
    }
}
</style>

</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar-area">
            <?php include "sidebar.php"; ?>
        </div>

        <!-- CONTENT -->
        <div class="col-md-10 content">

            <!-- HEADER -->
            <div class="header-dashboard">
                <h2>Dashboard Admin</h2>
                <p>Selamat datang di halaman pengelolaan The Four Label.</p>
            </div>

            <!-- CARD RINGKASAN -->
            <div class="row g-4">

                <div class="col-md-4 col-lg-3">
                    <div class="card-dashboard">
                        <div class="icon-box">👕</div>
                        <div class="total"><?= $produk; ?></div>
                        <div class="label">Total Produk</div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="card-dashboard">
                        <div class="icon-box">📦</div>
                        <div class="total"><?= $pesanan; ?></div>
                        <div class="label">Total Pesanan</div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="card-dashboard">
                        <div class="icon-box">👥</div>
                        <div class="total"><?= $pelanggan; ?></div>
                        <div class="label">Total Pelanggan</div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="card-dashboard">
                        <div class="icon-box">🧵</div>
                        <div class="total"><?= $stok; ?></div>
                        <div class="label">Total Stok</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-dashboard">
                        <div class="icon-box">💰</div>
                        <div class="total">
                            Rp <?= number_format($pendapatan, 0, ',', '.'); ?>
                        </div>
                        <div class="label">Total Nilai Pesanan</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-dashboard">
                        <div class="icon-box">⏳</div>
                        <div class="total"><?= $menunggu; ?></div>
                        <div class="label">Pesanan Menunggu</div>
                    </div>
                </div>

            </div>

            <!-- PESANAN TERBARU -->
            <div class="section-card">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h4 class="section-title mb-0">Pesanan Terbaru</h4>

                    <a href="pesanan.php" class="btn-lavender">
                        Lihat Semua
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Deadline</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (mysqli_num_rows($pesananTerbaru) > 0) { ?>

                                <?php while ($row = mysqli_fetch_assoc($pesananTerbaru)) { ?>

                                    <tr>
                                        <td>
                                            <strong>
                                                <?= $row['nomorInvoice'] ? htmlspecialchars($row['nomorInvoice']) : "#" . $row['idPesanan']; ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['namaPelanggan']); ?>
                                        </td>

                                        <td>
                                            <?php
                                            if ($row['jenisPesanan'] == "siap_pakai") {
                                                echo "Siap Pakai";
                                            } elseif ($row['jenisPesanan'] == "custom") {
                                                echo "Custom";
                                            } else {
                                                echo htmlspecialchars($row['jenisPesanan']);
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?= formatTanggal($row['tanggal']); ?>
                                        </td>

                                        <td>
                                            <?= formatTanggal($row['deadlineSelesai'] ?? null); ?>
                                        </td>

                                        <td>
                                            <strong>
                                                Rp <?= number_format($row['total'], 0, ',', '.'); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <span class="badge-status <?= statusClass($row['status']); ?>">
                                                <?= htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="7" class="empty-data">
                                        Belum ada pesanan terbaru.
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