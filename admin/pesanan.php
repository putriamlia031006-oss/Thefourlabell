<?php
session_start();

require "auth.php";
require "../koneksi.php";

/* =========================
   AMBIL FILTER PELANGGAN
========================= */
$idPelangganFilter = "";

if (isset($_GET['idPelanggan'])) {
    $idPelangganFilter = $_GET['idPelanggan'];
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
        COALESCE(SUM(pembayaran.jumlah), 0) AS totalBayar
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    LEFT JOIN pembayaran
        ON pesanan.idPesanan = pembayaran.idPesanan
    $where
    GROUP BY pesanan.idPesanan
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Daftar Pesanan</title>

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

.filter-card {
    background: white;
    border-radius: 22px;
    padding: 22px;
    margin-bottom: 24px;
    border: 1px solid #eadcff;
    box-shadow: 0 8px 22px rgba(111, 66, 193, 0.12);
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
}

.form-select {
    border-radius: 14px;
    padding: 12px 14px;
    border: 1px solid #ddd;
    background: #fcfbff;
}

.form-select:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.17);
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    font-weight: 750;
    padding: 11px 16px;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s ease;
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
    border-radius: 14px;
    font-weight: 750;
    padding: 11px 16px;
    text-decoration: none;
    display: inline-block;
}

.btn-reset:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.card-lavender {
    border: none;
    border-radius: 22px;
    background: #ffffff;
    box-shadow: 0 8px 22px rgba(111, 66, 193, 0.14);
    transition: 0.3s;
    height: 100%;
    border: 1px solid #eadcff;
    overflow: hidden;
}

.card-lavender:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(111, 66, 193, 0.22);
}

.card-top {
    background: #faf5ff;
    padding: 18px;
    border-bottom: 1px solid #eadcff;
}

.invoice-title {
    color: #6f42c1;
    font-weight: 850;
    margin-bottom: 6px;
}

.customer-name {
    font-weight: 800;
    color: #4b2e63;
}

.customer-email {
    color: #777;
    font-size: 13px;
}

.card-body-custom {
    padding: 18px;
}

.info-label {
    color: #888;
    font-size: 13px;
    margin-bottom: 2px;
    font-weight: 650;
}

.info-value {
    font-weight: 750;
    color: #333;
}

.price {
    color: #7b3fb2;
    font-weight: 850;
}

.badge-lavender {
    background: #eadcff;
    color: #4b2a7a;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    display: inline-block;
}

.badge-paid {
    background: #dcfce7;
    color: #15803d;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    display: inline-block;
}

.badge-unpaid {
    background: #fff3cd;
    color: #856404;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    display: inline-block;
}

.payment-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 16px;
    padding: 14px;
    margin-top: 12px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 14px;
}

.payment-row:last-child {
    margin-bottom: 0;
}

.btn-update {
    background: #7c4dff;
    color: white;
    border-radius: 12px;
    font-weight: 700;
}

.btn-update:hover {
    background: #5e35b1;
    color: white;
}

.btn-detail {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
    border-radius: 12px;
    font-weight: 700;
}

.btn-detail:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.btn-delete {
    background: #ef4444;
    color: white;
    border-radius: 12px;
    font-weight: 700;
}

.btn-delete:hover {
    background: #dc2626;
    color: white;
}

.empty-box {
    background: white;
    border-radius: 22px;
    padding: 35px;
    text-align: center;
    box-shadow: 0 8px 22px rgba(111, 66, 193, 0.12);
    border: 1px solid #eadcff;
}

.empty-box h5 {
    color: #6f42c1;
    font-weight: 850;
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
                <h3>💜 Daftar Pesanan</h3>
                <p>Kelola pesanan berdasarkan pelanggan, update status, dan lihat detail pembayaran.</p>
            </div>

            <!-- FILTER PELANGGAN -->
            <div class="filter-card">
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
                            <button type="submit" class="btn btn-lavender w-100">
                                Filter
                            </button>
                        </div>

                        <div class="col-md-2">
                            <a href="pesanan.php" class="btn-reset w-100 text-center">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            <!-- DAFTAR PESANAN -->
            <div class="row g-4">

                <?php if (mysqli_num_rows($query) == 0) { ?>

                    <div class="col-12">
                        <div class="empty-box">
                            <h5>Belum ada pesanan</h5>
                            <p class="text-muted mb-0">
                                Pesanan customer akan muncul di halaman ini.
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

                    $totalPesanan = $row['total'];
                    $totalBayar = $row['totalBayar'];
                    $sisa = $totalPesanan - $totalBayar;

                    if ($sisa < 0) {
                        $sisa = 0;
                    }

                    $isLunas = ($totalBayar >= $totalPesanan);
                    ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="card card-lavender">

                            <div class="card-top">
                                <h5 class="invoice-title">
                                    <?= htmlspecialchars($invoice); ?>
                                </h5>

                                <div class="customer-name">
                                    <?= htmlspecialchars($row['namaPelanggan']); ?>
                                </div>

                                <div class="customer-email">
                                    <?= htmlspecialchars($row['email']); ?>
                                </div>
                            </div>

                            <div class="card-body-custom">

                                <div class="mb-2">
                                    <div class="info-label">No HP</div>
                                    <div class="info-value">
                                        <?= !empty($row['noHp']) ? htmlspecialchars($row['noHp']) : "-"; ?>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="info-label">Jenis Pesanan</div>
                                    <div class="info-value">
                                        <?= htmlspecialchars(jenisPesananText($row['jenisPesanan'])); ?>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="info-label">Tanggal Pesan</div>
                                    <div class="info-value">
                                        <?= formatTanggal($row['tanggal']); ?>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="info-label">Deadline Selesai</div>
                                    <div class="info-value">
                                        <?= formatTanggal($row['deadlineSelesai'] ?? null); ?>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="info-label">Alamat Kirim</div>
                                    <div class="info-value">
                                        <?= !empty($row['alamat_kirim']) ? htmlspecialchars($row['alamat_kirim']) : "-"; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <div class="info-label">Jasa Kirim</div>
                                        <div class="info-value">
                                            <?= !empty($row['jasa_kirim']) ? htmlspecialchars($row['jasa_kirim']) : "-"; ?>
                                        </div>
                                    </div>

                                    <div class="col-6 mb-2">
                                        <div class="info-label">Ongkir</div>
                                        <div class="info-value">
                                            Rp <?= number_format($row['ongkir'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="info-label">Status Pesanan</div>
                                    <span class="badge-lavender">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </div>

                                <div class="payment-box">
                                    <div class="payment-row">
                                        <span>Total</span>
                                        <strong>Rp <?= number_format($totalPesanan, 0, ',', '.'); ?></strong>
                                    </div>

                                    <div class="payment-row">
                                        <span>Dibayar</span>
                                        <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                                    </div>

                                    <div class="payment-row">
                                        <span>Sisa</span>
                                        <strong>Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <?php if ($isLunas) { ?>
                                        <span class="badge-paid">Lunas</span>
                                    <?php } else { ?>
                                        <span class="badge-unpaid">Belum Lunas</span>
                                    <?php } ?>
                                </div>

                                <div class="row g-2 mt-3">

                                    <div class="col-4">
                                        <a 
                                            href="update-status.php?id=<?= $row['idPesanan']; ?>"
                                            class="btn btn-update btn-sm w-100">
                                            Update
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a 
                                            href="detail-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                            class="btn btn-detail btn-sm w-100">
                                            Detail
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <a 
                                            href="hapus-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                            class="btn btn-delete btn-sm w-100"
                                            onclick="return confirm('Yakin ingin menghapus pesanan ini? Data pembayaran dan detail pesanan juga akan terhapus.')">
                                            Hapus
                                        </a>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>
</div>

</body>
</html>