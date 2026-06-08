<?php
session_start();

require "auth.php";
require "../koneksi.php";

/* =========================
   AMBIL FILTER PELANGGAN
========================= */
$idPelangganFilter = "";

if (isset($_GET['idPelanggan'])) {
    $idPelangganFilter = mysqli_real_escape_string($koneksi, $_GET['idPelanggan']);
}

/* =========================
   AMBIL DATA PELANGGAN UNTUK DROPDOWN
========================= */
$dataPelanggan = mysqli_query(
    $koneksi,
    "SELECT 
        pelanggan.idPelanggan,
        user.nama,
        user.email
    FROM pelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    ORDER BY user.nama ASC"
);

if (!$dataPelanggan) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

/* =========================
   QUERY PESANAN SESUAI PELANGGAN
========================= */
$where = "";

if ($idPelangganFilter != "") {
    $where = "WHERE pesanan.idPelanggan = '$idPelangganFilter'";
}

$query = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        pelanggan.noHp,
        pelanggan.alamat,
        user.nama AS namaPelanggan,
        user.email,

        COALESCE((
            SELECT SUM(pb1.jumlah)
            FROM pembayaran pb1
            WHERE pb1.idPesanan = pesanan.idPesanan
            AND pb1.status IN ('DP Masuk', 'Lunas')
        ), 0) AS totalBayar,

        COALESCE((
            SELECT SUM(pb2.jumlah)
            FROM pembayaran pb2
            WHERE pb2.idPesanan = pesanan.idPesanan
            AND pb2.status = 'Pending'
        ), 0) AS totalPending,

        COALESCE((
            SELECT COUNT(*)
            FROM pembayaran pb3
            WHERE pb3.idPesanan = pesanan.idPesanan
            AND pb3.metode = 'Cash di Toko'
        ), 0) AS isCashOrder,

        (
            SELECT pb4.bukti
            FROM pembayaran pb4
            WHERE pb4.idPesanan = pesanan.idPesanan
            AND pb4.status = 'Pending'
            ORDER BY pb4.idPembayaran DESC
            LIMIT 1
        ) AS buktiPending,

        (
            SELECT pb5.metode
            FROM pembayaran pb5
            WHERE pb5.idPesanan = pesanan.idPesanan
            ORDER BY pb5.idPembayaran DESC
            LIMIT 1
        ) AS metodeTerakhir

    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    $where
    ORDER BY pesanan.idPesanan DESC"
);

if (!$query) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}

function jenisPesananText($jenis) {
    if ($jenis == "siap_pakai") {
        return "Siap Pakai";
    } elseif ($jenis == "custom") {
        return "Custom";
    } else {
        return $jenis;
    }
}

function badgeStatusPesanan($status) {
    $statusLower = strtolower($status);

    if ($statusLower == "menunggu upload bukti pembayaran") {
        return "badge-warning-soft";
    } elseif ($statusLower == "menunggu verifikasi pembayaran") {
        return "badge-info-soft";
    } elseif ($statusLower == "menunggu pembayaran di toko") {
        return "badge-purple-soft";
    } elseif ($statusLower == "menunggu pembayaran tunai di toko") {
        return "badge-purple-soft";
    } elseif ($statusLower == "diproses" || $statusLower == "proses") {
        return "badge-purple-soft";
    } elseif ($statusLower == "selesai") {
        return "badge-green-soft";
    } elseif ($statusLower == "batal") {
        return "badge-red-soft";
    } elseif ($statusLower == "lunas") {
        return "badge-green-soft";
    } else {
        return "badge-warning-soft";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Daftar Pesanan - Admin The Four Label</title>

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
    color: white;
    padding: 30px;
    border-radius: 28px;
    margin-bottom: 28px;
    box-shadow: 0 16px 36px rgba(142, 68, 173, 0.18);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -80px;
    right: -55px;
}

.page-header::after {
    content: "";
    position: absolute;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
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

/* FILTER */
.filter-card {
    background: white;
    border-radius: 24px;
    padding: 22px;
    margin-bottom: 26px;
    border: 1px solid #eadcff;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
}

.filter-title {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.form-label {
    font-weight: 750;
    color: #4b2e63;
    margin-bottom: 8px;
}

.form-select {
    border-radius: 15px;
    padding: 12px 14px;
    border: 1px solid #eadcff;
    background: #fcfbff;
    color: #44324f;
    font-weight: 600;
}

.form-select:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
}

.btn-lavender,
.btn-reset {
    border-radius: 15px;
    font-weight: 800;
    padding: 12px 16px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: 0.25s ease;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    box-shadow: 0 9px 20px rgba(142, 68, 173, 0.20);
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-reset {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
}

.btn-reset:hover {
    background: #f4eaff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

/* ORDER CARD */
.card-lavender {
    border: 1px solid #eadcff;
    border-radius: 24px;
    background: #ffffff;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    transition: 0.28s ease;
    height: 100%;
    overflow: hidden;
}

.card-lavender:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.16);
}

.card-top {
    background: linear-gradient(135deg, #fbf7ff, #f4eaff);
    padding: 20px;
    border-bottom: 1px solid #eadcff;
    position: relative;
}

.card-top::before {
    content: "";
    position: absolute;
    width: 86px;
    height: 86px;
    border-radius: 50%;
    background: rgba(181, 126, 220, .13);
    right: -30px;
    top: -30px;
}

.card-top-content {
    position: relative;
    z-index: 2;
}

.invoice-title {
    color: #6f2da8;
    font-weight: 900;
    margin-bottom: 10px;
    font-size: 18px;
}

.customer-name {
    font-weight: 850;
    color: #33223f;
    display: flex;
    align-items: center;
    gap: 8px;
}

.customer-email {
    color: #81758d;
    font-size: 13px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body-custom {
    padding: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.info-box {
    background: #fcfbff;
    border: 1px solid #f0e3ff;
    border-radius: 16px;
    padding: 12px;
}

.info-label {
    color: #8d7a9b;
    font-size: 12px;
    margin-bottom: 4px;
    font-weight: 750;
    display: flex;
    align-items: center;
    gap: 7px;
}

.info-value {
    font-weight: 800;
    color: #33223f;
    line-height: 1.45;
}

/* BADGE */
.badge-main {
    padding: 8px 13px;
    border-radius: 999px;
    font-weight: 850;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    line-height: 1.5;
    font-size: 12px;
}

.badge-purple-soft {
    background: #f1e3ff;
    color: #8e44ad;
}

.badge-info-soft {
    background: #f4eaff;
    color: #7b3fb2;
}

.badge-green-soft {
    background: #ecfdf5;
    color: #047857;
}

.badge-warning-soft {
    background: #fff7ed;
    color: #c2410c;
}

.badge-red-soft {
    background: #fef2f2;
    color: #b91c1c;
}

/* PAYMENT */
.payment-box {
    background: #fbf7ff;
    border: 1px solid #eadcff;
    border-radius: 18px;
    padding: 15px;
    margin-top: 14px;
}

.payment-title {
    font-weight: 850;
    color: #6f2da8;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 9px;
    font-size: 14px;
    color: #5f526a;
}

.payment-row:last-child {
    margin-bottom: 0;
    padding-top: 9px;
    border-top: 1px dashed #dcc5ef;
}

.payment-row strong {
    color: #33223f;
    white-space: nowrap;
}

.pending-note,
.cash-note {
    border-radius: 16px;
    padding: 12px 13px;
    font-size: 13px;
    margin-top: 12px;
    line-height: 1.55;
    display: flex;
    align-items: flex-start;
    gap: 9px;
}

.pending-note {
    background: #f4eaff;
    border: 1px solid #eadcff;
    color: #7b3fb2;
}

.cash-note {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #c2410c;
}

/* BUTTON ACTION */
.btn-action {
    border-radius: 13px;
    font-weight: 800;
    padding: 8px 10px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none;
    transition: .25s ease;
    border: none;
}

.btn-update {
    background: #f1e3ff;
    color: #7b3fb2;
}

.btn-update:hover {
    background: #e4d0ff;
    color: #6f2da8;
    transform: translateY(-2px);
}

.btn-verify {
    background: #ecfdf5;
    color: #047857;
}

.btn-verify:hover {
    background: #bbf7d0;
    color: #065f46;
    transform: translateY(-2px);
}

.btn-detail {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
}

.btn-detail:hover {
    background: #f4eaff;
    color: #7b3fb2;
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
.empty-box {
    background: white;
    border-radius: 24px;
    padding: 42px 24px;
    text-align: center;
    box-shadow: 0 12px 30px rgba(142, 68, 173, 0.10);
    border: 1px solid #eadcff;
}

.empty-icon {
    width: 62px;
    height: 62px;
    border-radius: 20px;
    background: #f1e3ff;
    color: #8e44ad;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
}

.empty-box h5 {
    color: #6f2da8;
    font-weight: 900;
}

.empty-box p {
    color: #81758d;
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

@media (max-width: 768px) {
    .main-content {
        padding: 18px;
    }

    .page-header {
        padding: 24px;
        border-radius: 24px;
    }

    .page-title {
        font-size: 26px;
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
                <i class="fa-solid fa-box-open"></i>
            </div>

            <h2 class="page-title">Daftar Pesanan</h2>
            <p class="page-subtitle">
                Kelola pesanan, verifikasi pembayaran, update status, dan lihat detail transaksi The Four Label.
            </p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="filter-card">
        <div class="filter-title">
            <i class="fa-solid fa-filter"></i>
            Filter Pesanan
        </div>

        <form method="GET">
            <div class="row g-3 align-items-end">

                <div class="col-md-8">
                    <label class="form-label">Filter Berdasarkan Pelanggan</label>
                    <select name="idPelanggan" class="form-select">
                        <option value="">Tampilkan Semua Pelanggan</option>

                        <?php while ($p = mysqli_fetch_assoc($dataPelanggan)) { ?>
                            <option 
                                value="<?= $p['idPelanggan']; ?>"
                                <?= $idPelangganFilter == $p['idPelanggan'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['nama']); ?> - <?= htmlspecialchars($p['email']); ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn-lavender w-100">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Filter
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="pesanan.php" class="btn-reset w-100">
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- PESANAN -->
    <div class="row g-4">

        <?php if (mysqli_num_rows($query) == 0) { ?>

            <div class="col-12">
                <div class="empty-box">
                    <div class="empty-icon">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>

                    <h5>Belum ada pesanan</h5>
                    <p class="mb-0">
                        Pesanan customer akan muncul di halaman ini setelah pelanggan melakukan checkout.
                    </p>
                </div>
            </div>

        <?php } ?>

        <?php while ($row = mysqli_fetch_assoc($query)) { ?>

            <?php
            $invoice = $row['nomorInvoice'];

            if ($invoice == "" || $invoice == NULL) {
                $invoice = "#" . $row['idPesanan'];
            }

            $totalPesanan = (int) $row['total'];
            $totalBayar = (int) $row['totalBayar'];
            $totalPending = (int) $row['totalPending'];
            $isCashOrder = (int) $row['isCashOrder'];

            $sisa = $totalPesanan - $totalBayar;

            if ($sisa < 0) {
                $sisa = 0;
            }

            $isLunas = ($totalBayar >= $totalPesanan && $totalPesanan > 0);

            if ($isLunas) {
                $statusBayarText = "Lunas";
                $statusBayarClass = "badge-green-soft";
                $statusBayarIcon = "fa-circle-check";
            } elseif ($totalPending > 0) {
                $statusBayarText = "Menunggu Verifikasi";
                $statusBayarClass = "badge-info-soft";
                $statusBayarIcon = "fa-clock";
            } elseif ($isCashOrder > 0) {
                $statusBayarText = "Cash di Toko";
                $statusBayarClass = "badge-purple-soft";
                $statusBayarIcon = "fa-store";
            } elseif ($totalBayar > 0) {
                $statusBayarText = "DP / Belum Lunas";
                $statusBayarClass = "badge-warning-soft";
                $statusBayarIcon = "fa-hourglass-half";
            } else {
                $statusBayarText = "Belum Bayar";
                $statusBayarClass = "badge-warning-soft";
                $statusBayarIcon = "fa-circle-exclamation";
            }
            ?>

            <div class="col-md-6 col-lg-4">

                <div class="card-lavender">

                    <div class="card-top">
                        <div class="card-top-content">
                            <h5 class="invoice-title">
                                <i class="fa-solid fa-file-invoice"></i>
                                <?= htmlspecialchars($invoice); ?>
                            </h5>

                            <div class="customer-name">
                                <i class="fa-solid fa-user"></i>
                                <?= htmlspecialchars($row['namaPelanggan']); ?>
                            </div>

                            <div class="customer-email">
                                <i class="fa-solid fa-envelope"></i>
                                <?= htmlspecialchars($row['email']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body-custom">

                        <div class="info-grid">

                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fa-solid fa-phone"></i>
                                    No HP
                                </div>
                                <div class="info-value">
                                    <?= !empty($row['noHp']) ? htmlspecialchars($row['noHp']) : "-"; ?>
                                </div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fa-solid fa-shirt"></i>
                                    Jenis Pesanan
                                </div>
                                <div class="info-value">
                                    <?= htmlspecialchars(jenisPesananText($row['jenisPesanan'])); ?>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="info-box h-100">
                                        <div class="info-label">
                                            <i class="fa-solid fa-calendar-day"></i>
                                            Tanggal
                                        </div>
                                        <div class="info-value">
                                            <?= formatTanggal($row['tanggal']); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="info-box h-100">
                                        <div class="info-label">
                                            <i class="fa-solid fa-calendar-check"></i>
                                            Deadline
                                        </div>
                                        <div class="info-value">
                                            <?= formatTanggal($row['deadlineSelesai'] ?? null); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fa-solid fa-location-dot"></i>
                                    Alamat Kirim
                                </div>
                                <div class="info-value">
                                    <?= !empty($row['alamat_kirim']) ? htmlspecialchars($row['alamat_kirim']) : "-"; ?>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="info-box h-100">
                                        <div class="info-label">
                                            <i class="fa-solid fa-truck"></i>
                                            Jasa Kirim
                                        </div>
                                        <div class="info-value">
                                            <?= !empty($row['jasa_kirim']) ? htmlspecialchars($row['jasa_kirim']) : "-"; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="info-box h-100">
                                        <div class="info-label">
                                            <i class="fa-solid fa-money-bill"></i>
                                            Ongkir
                                        </div>
                                        <div class="info-value">
                                            Rp <?= number_format($row['ongkir'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Status Pesanan
                                </div>

                                <span class="badge-main <?= badgeStatusPesanan($row['status']); ?>">
                                    <i class="fa-solid fa-circle"></i>
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </div>

                        </div>

                        <div class="payment-box">
                            <div class="payment-title">
                                <i class="fa-solid fa-wallet"></i>
                                Ringkasan Pembayaran
                            </div>

                            <div class="payment-row">
                                <span>Total</span>
                                <strong>Rp <?= number_format($totalPesanan, 0, ',', '.'); ?></strong>
                            </div>

                            <div class="payment-row">
                                <span>Dibayar</span>
                                <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                            </div>

                            <?php if ($totalPending > 0) { ?>
                                <div class="payment-row">
                                    <span>Pending</span>
                                    <strong>Rp <?= number_format($totalPending, 0, ',', '.'); ?></strong>
                                </div>
                            <?php } ?>

                            <div class="payment-row">
                                <span>Sisa</span>
                                <strong>Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="badge-main <?= $statusBayarClass; ?>">
                                <i class="fa-solid <?= $statusBayarIcon; ?>"></i>
                                <?= htmlspecialchars($statusBayarText); ?>
                            </span>
                        </div>

                        <?php if ($totalPending > 0) { ?>
                            <div class="pending-note">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>
                                    Customer sudah upload bukti pembayaran. Silakan klik <b>Verifikasi</b> setelah bukti dicek.
                                </span>
                            </div>
                        <?php } ?>

                        <?php if ($isCashOrder > 0 && !$isLunas) { ?>
                            <div class="cash-note">
                                <i class="fa-solid fa-store"></i>
                                <span>
                                    Pesanan ini menggunakan <b>Cash di Toko</b>. Pembayaran dikonfirmasi saat customer ambil barang.
                                </span>
                            </div>
                        <?php } ?>

                        <div class="row g-2 mt-3">

                            <?php if ($totalPending > 0) { ?>

                                <div class="col-6">
                                    <a 
                                        href="verifikasi-pembayaran.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-verify w-100"
                                        onclick="return confirm('Verifikasi pembayaran pesanan ini?')">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Verifikasi
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a 
                                        href="detail-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-detail w-100">
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a 
                                        href="update-status.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-update w-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Update
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a 
                                        href="hapus-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-delete w-100"
                                        onclick="return confirm('Yakin ingin menghapus pesanan ini? Data pembayaran dan detail pesanan juga akan terhapus.')">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </a>
                                </div>

                            <?php } else { ?>

                                <div class="col-4">
                                    <a 
                                        href="update-status.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-update w-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Update
                                    </a>
                                </div>

                                <div class="col-4">
                                    <a 
                                        href="detail-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-detail w-100">
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>
                                </div>

                                <div class="col-4">
                                    <a 
                                        href="hapus-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn-action btn-delete w-100"
                                        onclick="return confirm('Yakin ingin menghapus pesanan ini? Data pembayaran dan detail pesanan juga akan terhapus.')">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </a>
                                </div>

                            <?php } ?>

                        </div>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</main>

</body>
</html>