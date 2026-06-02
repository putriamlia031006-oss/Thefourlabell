<?php
session_start();
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include "navbar.php";

if (!isset($_GET['id'])) {
    header("Location: pesanan-saya.php");
    exit;
}

$idPesanan = $_GET['id'];
$idUser = $_SESSION['user']['idUser'];

/* =========================
   AMBIL DATA PESANAN
========================= */
$qPesanan = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        pelanggan.idUser,
        pelanggan.noHp,
        pelanggan.alamat,
        user.nama AS namaPelanggan,
        user.email
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    WHERE pesanan.idPesanan = '$idPesanan'
    AND pelanggan.idUser = '$idUser'"
);

if (!$qPesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($qPesanan);

if (!$pesanan) {
    die("Pesanan tidak ditemukan atau bukan milik akun ini.");
}

/* =========================
   AMBIL DETAIL PRODUK SIAP PAKAI
========================= */
$qDetailProduk = mysqli_query(
    $koneksi,
    "SELECT 
        detail_pesanan.*,
        produk.namaProduk,
        produk.harga,
        produk.gambar
    FROM detail_pesanan
    JOIN produk 
        ON detail_pesanan.idProduk = produk.idProduk
    WHERE detail_pesanan.idPesanan = '$idPesanan'"
);

/* =========================
   AMBIL DETAIL CUSTOM
========================= */
$qDetailCustom = mysqli_query(
    $koneksi,
    "SELECT *
    FROM detail_custom
    WHERE idPesanan = '$idPesanan'"
);

/* =========================
   AMBIL PEMBAYARAN
========================= */
$qPembayaran = mysqli_query(
    $koneksi,
    "SELECT *
    FROM pembayaran
    WHERE idPesanan = '$idPesanan'
    ORDER BY idPembayaran DESC"
);

if (!$qPembayaran) {
    die("Query pembayaran error: " . mysqli_error($koneksi));
}

/* HITUNG TOTAL BAYAR */
$qTotalBayar = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar
    FROM pembayaran
    WHERE idPesanan = '$idPesanan'"
);

$dataBayar = mysqli_fetch_assoc($qTotalBayar);

$totalBayar = $dataBayar['totalBayar'];
$totalPesanan = $pesanan['total'];
$sisa = $totalPesanan - $totalBayar;

if ($sisa < 0) {
    $sisa = 0;
}

$isLunas = ($totalBayar >= $totalPesanan);

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}

function tampilGambarProduk($namaGambar) {
    $namaGambar = trim($namaGambar);

    if ($namaGambar == "") {
        return "";
    }

    $path1 = "image/" . $namaGambar;
    $path2 = "upload/" . $namaGambar;
    $path3 = "uploads/" . $namaGambar;

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

function badgeStatusPesanan($status) {
    $statusLower = strtolower($status);

    if ($statusLower == "menunggu") {
        return "badge-menunggu";
    } elseif ($statusLower == "menunggu verifikasi pembayaran") {
        return "badge-verifikasi";
    } elseif ($statusLower == "diproses" || $statusLower == "proses") {
        return "badge-proses";
    } elseif ($statusLower == "selesai") {
        return "badge-selesai";
    } elseif ($statusLower == "batal") {
        return "badge-batal";
    } elseif ($statusLower == "menunggu pembayaran tunai") {
        return "badge-tunai";
    } else {
        return "badge-menunggu";
    }
}

$invoice = $pesanan['nomorInvoice'];

if ($invoice == "" || $invoice == NULL) {
    $invoice = "#" . $pesanan['idPesanan'];
}

$jenisPesanan = $pesanan['jenisPesanan'];

if ($jenisPesanan == "siap_pakai") {
    $jenisPesananText = "Siap Pakai";
} elseif ($jenisPesanan == "custom") {
    $jenisPesananText = "Custom";
} else {
    $jenisPesananText = $jenisPesanan;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Detail Pesanan - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.page-wrapper {
    padding: 45px 0 70px;
}

.header-box {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 28px;
    padding: 32px;
    margin-bottom: 28px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.18);
    position: relative;
    overflow: hidden;
}

.header-box::before {
    content: "";
    position: absolute;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -60px;
    right: -40px;
}

.header-box h3,
.header-box p {
    position: relative;
    z-index: 2;
}

.header-box h3 {
    font-weight: 850;
    margin-bottom: 8px;
}

.header-box p {
    margin: 0;
    opacity: 0.94;
}

.card-box {
    background: white;
    border: 1px solid #eadcff;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 10px 28px rgba(142, 68, 173, 0.12);
    margin-bottom: 24px;
}

.section-title {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 18px;
}

.info-label {
    color: #888;
    font-size: 13px;
    font-weight: 650;
    margin-bottom: 4px;
}

.info-value {
    color: #33223f;
    font-weight: 750;
}

.badge-custom {
    padding: 8px 13px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    display: inline-block;
}

.badge-menunggu {
    background: #fff3cd;
    color: #856404;
}

.badge-verifikasi {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-proses {
    background: #f1e3ff;
    color: #7b3fb2;
}

.badge-selesai {
    background: #dcfce7;
    color: #15803d;
}

.badge-batal {
    background: #fee2e2;
    color: #b91c1c;
}

.badge-tunai {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-lunas {
    background: #dcfce7;
    color: #15803d;
}

.badge-belum {
    background: #fff3cd;
    color: #856404;
}

.summary-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 20px;
    padding: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.summary-row:last-child {
    margin-bottom: 0;
}

.summary-row span {
    color: #6b5a78;
}

.summary-row strong {
    color: #33223f;
}

.total-final {
    color: #7b3fb2 !important;
    font-size: 22px;
    font-weight: 900;
}

.product-item {
    display: flex;
    gap: 14px;
    padding: 15px 0;
    border-bottom: 1px solid #f0e4ff;
}

.product-item:last-child {
    border-bottom: none;
}

.product-img {
    width: 85px;
    height: 85px;
    border-radius: 16px;
    object-fit: cover;
    background: #f1e3ff;
    border: 1px solid #eadcff;
}

.no-img {
    width: 85px;
    height: 85px;
    border-radius: 16px;
    background: #f1e3ff;
    color: #8e44ad;
    font-size: 12px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    border: 1px dashed #c9a7ec;
}

.product-name {
    font-weight: 850;
    color: #4b2e63;
    margin-bottom: 5px;
}

.product-meta {
    color: #777;
    font-size: 14px;
}

.product-price {
    color: #8e44ad;
    font-weight: 850;
}

.custom-detail-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 18px;
    padding: 18px;
    margin-bottom: 14px;
}

.desain-img {
    width: 140px;
    height: 140px;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid #eadcff;
    background: #f1e3ff;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 14px;
    font-size: 14px;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    border-color: #f0e3ff;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-outline-lavender {
    border: 1px solid #d9c0f0;
    color: #8e44ad;
    background: white;
    border-radius: 14px;
    padding: 11px 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s;
}

.btn-outline-lavender:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.empty-data {
    color: #888;
    text-align: center;
    padding: 22px;
}

@media (max-width: 768px) {
    .header-box {
        padding: 24px;
    }

    .card-box {
        padding: 20px;
    }

    .product-item {
        flex-direction: column;
    }
}
</style>
</head>

<body>

<div class="container page-wrapper">

    <div class="header-box">
        <h3>Detail Pesanan</h3>
        <p>Invoice <?= htmlspecialchars($invoice); ?></p>
    </div>

    <div class="row g-4">

        <!-- KIRI -->
        <div class="col-lg-8">

            <!-- INFORMASI PESANAN -->
            <div class="card-box">
                <h4 class="section-title">Informasi Pesanan</h4>

                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="info-label">Invoice</div>
                        <div class="info-value">
                            <?= htmlspecialchars($invoice); ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Tanggal Pesan</div>
                        <div class="info-value">
                            <?= formatTanggal($pesanan['tanggal']); ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Deadline Selesai</div>
                        <div class="info-value">
                            <?= formatTanggal($pesanan['deadlineSelesai'] ?? null); ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Jenis Pesanan</div>
                        <div class="info-value">
                            <?= htmlspecialchars($jenisPesananText); ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Status Pesanan</div>
                        <span class="badge-custom <?= badgeStatusPesanan($pesanan['status']); ?>">
                            <?= htmlspecialchars($pesanan['status']); ?>
                        </span>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Status Pembayaran</div>
                        <?php if ($isLunas) { ?>
                            <span class="badge-custom badge-lunas">Lunas</span>
                        <?php } else { ?>
                            <span class="badge-custom badge-belum">Belum Lunas</span>
                        <?php } ?>
                    </div>

                </div>
            </div>

            <!-- DATA PELANGGAN -->
            <div class="card-box">
                <h4 class="section-title">Data Pelanggan</h4>

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="info-label">Nama</div>
                        <div class="info-value">
                            <?= htmlspecialchars($pesanan['namaPelanggan']); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <?= htmlspecialchars($pesanan['email']); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">No HP</div>
                        <div class="info-value">
                            <?= $pesanan['noHp'] ? htmlspecialchars($pesanan['noHp']) : "-"; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Alamat Pelanggan</div>
                        <div class="info-value">
                            <?= $pesanan['alamat'] ? htmlspecialchars($pesanan['alamat']) : "-"; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="info-label">Alamat Pengiriman</div>
                        <div class="info-value">
                            <?= !empty($pesanan['alamat_kirim']) ? htmlspecialchars($pesanan['alamat_kirim']) : "-"; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Jasa Kirim</div>
                        <div class="info-value">
                            <?= !empty($pesanan['jasa_kirim']) ? htmlspecialchars($pesanan['jasa_kirim']) : "-"; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Ongkir</div>
                        <div class="info-value">
                            Rp <?= number_format($pesanan['ongkir'] ?? 0, 0, ',', '.'); ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- DETAIL PRODUK -->
            <?php if ($pesanan['jenisPesanan'] == "siap_pakai") { ?>

                <div class="card-box">
                    <h4 class="section-title">Detail Produk</h4>

                    <?php if ($qDetailProduk && mysqli_num_rows($qDetailProduk) > 0) { ?>

                        <?php while ($item = mysqli_fetch_assoc($qDetailProduk)) { ?>

                            <?php
                            $gambar = tampilGambarProduk($item['gambar']);
                            $subtotal = $item['harga'] * $item['qty'];
                            ?>

                            <div class="product-item">

                                <?php if ($gambar != "") { ?>
                                    <img 
                                        src="<?= htmlspecialchars($gambar); ?>" 
                                        class="product-img"
                                        alt="<?= htmlspecialchars($item['namaProduk']); ?>">
                                <?php } else { ?>
                                    <div class="no-img">No<br>Image</div>
                                <?php } ?>

                                <div class="flex-grow-1">
                                    <div class="product-name">
                                        <?= htmlspecialchars($item['namaProduk']); ?>
                                    </div>

                                    <div class="product-meta">
                                        Qty: <?= $item['qty']; ?> x Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                                    </div>

                                    <div class="product-price">
                                        Subtotal: Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                                    </div>
                                </div>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <div class="empty-data">
                            Detail produk tidak ditemukan.
                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

            <!-- DETAIL CUSTOM -->
            <?php if ($pesanan['jenisPesanan'] == "custom") { ?>

                <div class="card-box">
                    <h4 class="section-title">Detail Custom</h4>

                    <?php if ($qDetailCustom && mysqli_num_rows($qDetailCustom) > 0) { ?>

                        <?php while ($custom = mysqli_fetch_assoc($qDetailCustom)) { ?>

                            <div class="custom-detail-box">

                                <div class="row g-3 align-items-start">

                                    <div class="col-md-8">

                                        <div class="row g-3">

                                            <div class="col-md-6">
                                                <div class="info-label">Jenis Pakaian</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($custom['jenis']); ?>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="info-label">Ukuran</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($custom['ukuran']); ?>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="info-label">Qty</div>
                                                <div class="info-value">
                                                    <?= htmlspecialchars($custom['qty']); ?> pcs
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="info-label">Catatan</div>
                                                <div class="info-value">
                                                    <?= !empty($custom['catatan']) ? nl2br(htmlspecialchars($custom['catatan'])) : "-"; ?>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="info-label mb-2">Desain</div>

                                        <?php 
                                        $desainPath = "";
                                        if (!empty($custom['desain'])) {
                                            if (file_exists("upload/" . $custom['desain'])) {
                                                $desainPath = "upload/" . $custom['desain'];
                                            }
                                        }
                                        ?>

                                        <?php if ($desainPath != "") { ?>
                                            <img 
                                                src="<?= htmlspecialchars($desainPath); ?>" 
                                                class="desain-img"
                                                alt="Desain Custom">
                                        <?php } else { ?>
                                            <div class="no-img">
                                                No<br>Design
                                            </div>
                                        <?php } ?>

                                    </div>

                                </div>

                            </div>

                        <?php } ?>

                    <?php } else { ?>

                        <div class="empty-data">
                            Detail custom tidak ditemukan.
                        </div>

                    <?php } ?>

                </div>

            <?php } ?>

            <!-- RIWAYAT PEMBAYARAN -->
            <div class="card-box">
                <h4 class="section-title">Riwayat Pembayaran</h4>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (mysqli_num_rows($qPembayaran) > 0) { ?>

                                <?php 
                                $no = 1;
                                while ($pay = mysqli_fetch_assoc($qPembayaran)) { 
                                ?>

                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($pay['metode']); ?></td>

                                        <td>
                                            <strong>
                                                Rp <?= number_format($pay['jumlah'], 0, ',', '.'); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <span class="badge-custom badge-verifikasi">
                                                <?= htmlspecialchars($pay['status']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php if (!empty($pay['bukti'])) { ?>
                                                <a 
                                                    href="upload/<?= htmlspecialchars($pay['bukti']); ?>" 
                                                    target="_blank"
                                                    class="btn-outline-lavender">
                                                    Lihat Bukti
                                                </a>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="5" class="empty-data">
                                        Belum ada pembayaran.
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KANAN -->
        <div class="col-lg-4">

            <div class="card-box">

                <h4 class="section-title">Ringkasan Pembayaran</h4>

                <div class="summary-box">

                    <div class="summary-row">
                        <span>Total Pesanan</span>
                        <strong>
                            Rp <?= number_format($totalPesanan, 0, ',', '.'); ?>
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Total Dibayar</span>
                        <strong>
                            Rp <?= number_format($totalBayar, 0, ',', '.'); ?>
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Sisa Pembayaran</span>
                        <strong>
                            Rp <?= number_format($sisa, 0, ',', '.'); ?>
                        </strong>
                    </div>

                    <hr>

                    <div class="summary-row">
                        <span>Status</span>
                        <?php if ($isLunas) { ?>
                            <strong class="text-success">Lunas</strong>
                        <?php } else { ?>
                            <strong class="text-warning">Belum Lunas</strong>
                        <?php } ?>
                    </div>

                    <div class="summary-row">
                        <span>Total Akhir</span>
                        <strong class="total-final">
                            Rp <?= number_format($totalPesanan, 0, ',', '.'); ?>
                        </strong>
                    </div>

                </div>

                <div class="d-grid gap-2 mt-4">

                    <?php if ($totalBayar <= 0) { ?>

                        <a 
                            href="upload-pembayaran.php?id=<?= $pesanan['idPesanan']; ?>" 
                            class="btn-lavender">
                            Upload Pembayaran
                        </a>

                    <?php } elseif (!$isLunas) { ?>

                        <a 
                            href="bayar-sisa.php?id=<?= $pesanan['idPesanan']; ?>" 
                            class="btn-lavender">
                            Bayar Sisa
                        </a>

                    <?php } ?>

                    <?php if ($pesanan['status'] == "Menunggu Pembayaran Tunai") { ?>

                        <a 
                            href="kwitansi-tunai.php?id=<?= $pesanan['idPesanan']; ?>" 
                            class="btn-outline-lavender">
                            Cetak Kwitansi Tunai
                        </a>

                    <?php } ?>

                    <a href="pesanan-saya.php" class="btn-outline-lavender">
                        Kembali
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>