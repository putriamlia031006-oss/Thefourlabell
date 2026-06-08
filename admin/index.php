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

<title>Dashboard Admin - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
html, body {
    overflow-x: hidden;
}

body {
    background: #fbf7ff !important;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
    margin: 0;
}

/* SIDEBAR AREA */
.sidebar-area {
    min-height: 100vh;
    padding: 0;
}

/* CONTENT */
.content {
    padding: 0;
    min-height: 100vh;
}

/* HEADER */
.header-dashboard {
    position: relative;
    min-height: 285px;
    padding: 44px 50px;
    margin: 0;
    border-radius: 0 0 32px 32px;
    overflow: hidden;

    background: linear-gradient(135deg, #b57edc 0%, #9d7ad6 45%, #8e44ad 100%) !important;

    display: flex;
    align-items: flex-end;
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.18);
}

.header-dashboard::before {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -85px;
    right: -65px;
    z-index: 1;
}

.header-dashboard::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    bottom: -85px;
    left: 42%;
    z-index: 1;
}

.header-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(83, 35, 128, 0.22),
        rgba(181, 126, 220, 0.16),
        rgba(142, 68, 173, 0.18)
    ) !important;
    z-index: 1;
}

.header-content {
    position: relative;
    z-index: 2;
    color: white;
    max-width: 850px;
}

.header-icon {
    width: 86px;
    height: 86px;
    border-radius: 24px;
    background: rgba(255,255,255,0.20);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    margin-bottom: 22px;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.22);
}

.header-dashboard h2 {
    font-weight: 900;
    margin-bottom: 12px;
    font-size: 50px;
    line-height: 1.1;
    letter-spacing: .2px;
}

.header-dashboard p {
    margin: 0;
    opacity: 0.95;
    font-weight: 600;
    font-size: 18px;
}

/* DASHBOARD BODY */
.dashboard-body {
    padding: 30px 34px 34px;
}

/* SUMMARY CARD */
.card-dashboard {
    border: none;
    border-radius: 24px;
    padding: 24px;
    background: white !important;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    border: 1px solid #eadcff;
    transition: .25s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.card-dashboard::before {
    content: "";
    position: absolute;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #f4eaff;
    right: -35px;
    top: -35px;
}

.card-dashboard:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.16);
}

.card-inner {
    position: relative;
    z-index: 2;
}

.icon-box {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    background: #f1e3ff !important;
    color: #8e44ad !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 18px;
    border: 1px solid #eadcff;
}

.total {
    font-size: 31px;
    font-weight: 850;
    color: #7b3fb2 !important;
    margin-bottom: 5px;
    line-height: 1.2;
}

.label {
    color: #4b2e63;
    font-size: 15px;
    font-weight: 700;
}

.card-desc {
    color: #8d7a9b;
    font-size: 13px;
    margin-top: 8px;
}

/* SECTION */
.section-card {
    background: white !important;
    border-radius: 24px;
    padding: 24px;
    margin-top: 28px;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    border: 1px solid #eadcff;
}

.section-title {
    color: #6f2da8 !important;
    font-weight: 850;
    margin-bottom: 0;
}

.section-subtitle {
    color: #777;
    font-size: 14px;
    margin-top: 4px;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad) !important;
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 16px;
    font-weight: 750;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.25s ease;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.20);
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2) !important;
    color: white;
    transform: translateY(-2px);
}

/* TABLE */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f1e3ff !important;
    color: #6f2da8 !important;
    border: none;
    padding: 15px 14px;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.table tbody td {
    padding: 15px 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
    color: #44324f;
    font-size: 14px;
}

.table tbody tr:hover {
    background: #fbf7ff !important;
}

.invoice-text {
    color: #7b3fb2;
    font-weight: 800;
}

.nominal-text {
    font-weight: 800;
    color: #33223f;
}

.badge-status {
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.badge-status i {
    font-size: 7px;
}

/* STATUS */
.status-menunggu {
    background: #fff7ed;
    color: #c2410c;
}

.status-verifikasi {
    background: #f1e3ff;
    color: #7b3fb2;
}

.status-proses {
    background: #f4eaff;
    color: #8e44ad;
}

.status-selesai {
    background: #ecfdf5;
    color: #047857;
}

.status-batal {
    background: #fef2f2;
    color: #b91c1c;
}

.status-tunai {
    background: #f7edff;
    color: #6f2da8;
}

/* EMPTY */
.empty-data {
    text-align: center;
    color: #888;
    padding: 28px;
}

.empty-data i {
    font-size: 28px;
    color: #b794d8;
    margin-bottom: 10px;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .sidebar-area {
        min-height: auto;
    }

    .content {
        width: 100%;
    }

    .header-dashboard {
        min-height: 240px;
        padding: 34px 28px;
        border-radius: 0 0 26px 26px;
    }

    .header-dashboard h2 {
        font-size: 38px;
    }

    .header-dashboard p {
        font-size: 16px;
    }

    .dashboard-body {
        padding: 24px;
    }
}

@media (max-width: 768px) {
    .header-dashboard {
        min-height: 220px;
        padding: 28px 24px;
    }

    .header-icon {
        width: 68px;
        height: 68px;
        font-size: 28px;
        margin-bottom: 18px;
    }

    .header-dashboard h2 {
        font-size: 34px;
    }

    .header-dashboard p {
        font-size: 15px;
    }

    .dashboard-body {
        padding: 20px;
    }

    .section-card {
        padding: 18px;
    }

    .total {
        font-size: 27px;
    }
}
</style>

</head>

<body>

<div class="container-fluid p-0">
    <div class="row g-0">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar-area">
            <?php include "sidebar.php"; ?>
        </div>

        <!-- CONTENT -->
        <div class="col-md-10 content">

            <!-- HEADER -->
            <div class="header-dashboard">
                <div class="header-overlay"></div>

                <div class="header-content">
                    <div class="header-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                    <h2>Dashboard Admin</h2>
                    <p>
                        Kelola produk, pelanggan, stok, pesanan, dan laporan The Four Label dalam satu halaman.
                    </p>
                </div>
            </div>

            <div class="dashboard-body">

                <!-- CARD RINGKASAN -->
                <div class="row g-4">

                    <div class="col-md-4 col-lg-3">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-shirt"></i>
                                </div>

                                <div class="total"><?= $produk; ?></div>
                                <div class="label">Total Produk</div>
                                <div class="card-desc">Produk aktif yang tersedia</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-box"></i>
                                </div>

                                <div class="total"><?= $pesanan; ?></div>
                                <div class="label">Total Pesanan</div>
                                <div class="card-desc">Seluruh transaksi pesanan</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-users"></i>
                                </div>

                                <div class="total"><?= $pelanggan; ?></div>
                                <div class="label">Total Pelanggan</div>
                                <div class="card-desc">Pelanggan terdaftar</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-warehouse"></i>
                                </div>

                                <div class="total"><?= $stok; ?></div>
                                <div class="label">Total Stok</div>
                                <div class="card-desc">Akumulasi stok produk</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>

                                <div class="total">
                                    Rp <?= number_format($pendapatan, 0, ',', '.'); ?>
                                </div>

                                <div class="label">Total Nilai Pesanan</div>
                                <div class="card-desc">Total nominal dari seluruh pesanan</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card-dashboard">
                            <div class="card-inner">
                                <div class="icon-box">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>

                                <div class="total"><?= $menunggu; ?></div>
                                <div class="label">Pesanan Menunggu</div>
                                <div class="card-desc">Pesanan yang perlu ditindaklanjuti</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- PESANAN TERBARU -->
                <div class="section-card">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h4 class="section-title">Pesanan Terbaru</h4>
                            <div class="section-subtitle">
                                Daftar pesanan terbaru yang masuk ke sistem.
                            </div>
                        </div>

                        <a href="pesanan.php" class="btn-lavender">
                            <i class="fa-solid fa-list-check"></i>
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
                                                <span class="invoice-text">
                                                    <?= $row['nomorInvoice'] ? htmlspecialchars($row['nomorInvoice']) : "#" . $row['idPesanan']; ?>
                                                </span>
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
                                                <span class="nominal-text">
                                                    Rp <?= number_format($row['total'], 0, ',', '.'); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge-status <?= statusClass($row['status']); ?>">
                                                    <i class="fa-solid fa-circle"></i>
                                                    <?= htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                <?php } else { ?>

                                    <tr>
                                        <td colspan="7" class="empty-data">
                                            <div>
                                                <i class="fa-solid fa-folder-open"></i>
                                            </div>
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
</div>

</body>

</html>