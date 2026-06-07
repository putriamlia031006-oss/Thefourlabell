<?php
session_start();
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* CEK ID PESANAN */
if (!isset($_GET['id'])) {
    header("Location: pesanan-saya.php");
    exit;
}

$idPesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

/* AMBIL TOTAL PESANAN */
$q = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        pelanggan.idUser,
        user.nama AS namaPelanggan
     FROM pesanan
     JOIN pelanggan ON pesanan.idPelanggan = pelanggan.idPelanggan
     JOIN user ON pelanggan.idUser = user.idUser
     WHERE pesanan.idPesanan='$idPesanan'"
);

if (!$q) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$pesanan = mysqli_fetch_assoc($q);

if (!$pesanan) {
    die("Data pesanan tidak ditemukan.");
}

/* CEK SUPAYA USER TIDAK BAYAR PESANAN ORANG LAIN */
if ($pesanan['idUser'] != $_SESSION['user']['idUser']) {
    die("Akses ditolak.");
}

$totalPesanan = $pesanan['total'];

/* AMBIL TOTAL PEMBAYARAN */
$q2 = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar 
     FROM pembayaran 
     WHERE idPesanan='$idPesanan'"
);

if (!$q2) {
    die("Query pembayaran error: " . mysqli_error($koneksi));
}

$bayar = mysqli_fetch_assoc($q2);
$totalBayar = $bayar['totalBayar'];

/* HITUNG SISA */
$sisa = $totalPesanan - $totalBayar;

if ($sisa < 0) {
    $sisa = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Bayar Sisa - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    min-height: 100vh;
    margin: 0;
    background:
        radial-gradient(circle at top left, #f7edff 0%, transparent 36%),
        radial-gradient(circle at bottom right, #ead6ff 0%, transparent 34%),
        linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.payment-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 45px 15px;
}

.payment-card {
    width: 100%;
    max-width: 570px;
    margin: auto;
    background: white;
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid #eadcff;
    box-shadow: 0 16px 40px rgba(142, 68, 173, 0.14);
}

.payment-header {
    background:
        radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 35%),
        linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 26px 28px;
}

.payment-header h4 {
    margin: 0;
    font-weight: 850;
}

.payment-header p {
    margin: 6px 0 0;
    opacity: 0.92;
    font-size: 14px;
}

.payment-body {
    padding: 28px;
}

.info-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 20px;
    padding: 18px;
    margin-bottom: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 11px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-row span {
    color: #6b5a78;
}

.info-row strong {
    color: #4b2e63;
    text-align: right;
}

.sisa-box {
    background: #fff8e7;
    border: 1px solid #ffe3a3;
    color: #7c5a14;
    border-radius: 18px;
    padding: 16px;
    margin-bottom: 20px;
}

.sisa-box b {
    font-size: 20px;
}

.lunas-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    border-radius: 18px;
    padding: 18px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 750;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
}

.form-control {
    border-radius: 15px;
    padding: 13px 15px;
    border: 1px solid #ddd;
    background: #fcfbff;
}

.form-control:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181,126,220,0.17);
}

.upload-note {
    font-size: 13px;
    color: #777;
    margin-top: 8px;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 16px;
    padding: 14px;
    font-weight: 850;
    transition: 0.25s;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(142, 68, 173, 0.22);
}

.btn-back {
    background: white;
    color: #8e44ad;
    border: 1.5px solid #d9c0f0;
    border-radius: 16px;
    padding: 14px;
    font-weight: 850;
    text-decoration: none;
    display: block;
    text-align: center;
    transition: 0.25s;
}

.btn-back:hover {
    background: #f6eeff;
    color: #7b3fb2;
    transform: translateY(-2px);
}

@media (max-width: 576px) {
    .payment-body {
        padding: 22px;
    }

    .info-row {
        flex-direction: column;
        gap: 2px;
    }

    .info-row strong {
        text-align: left;
    }
}
</style>
</head>

<body>

<div class="payment-wrapper">
    <div class="payment-card">

        <div class="payment-header">
            <h4>Bayar Sisa Pembayaran</h4>
            <p>Selesaikan sisa pembayaran pesanan kamu.</p>
        </div>

        <div class="payment-body">

            <div class="info-box">

                <div class="info-row">
                    <span>Invoice</span>
                    <strong>
                        <?= !empty($pesanan['nomorInvoice']) ? htmlspecialchars($pesanan['nomorInvoice']) : "#" . htmlspecialchars($idPesanan); ?>
                    </strong>
                </div>

                <div class="info-row">
                    <span>Nama</span>
                    <strong><?= htmlspecialchars($pesanan['namaPelanggan']); ?></strong>
                </div>

                <div class="info-row">
                    <span>Total Pesanan</span>
                    <strong>Rp <?= number_format($totalPesanan, 0, ',', '.'); ?></strong>
                </div>

                <div class="info-row">
                    <span>Total Sudah Dibayar</span>
                    <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                </div>

            </div>

            <?php if ($sisa <= 0) { ?>

                <div class="lunas-box">
                    Pesanan ini sudah lunas. Tidak ada sisa pembayaran.
                </div>

                <a href="pesanan-saya.php" class="btn-back w-100">
                    Kembali ke Pesanan Saya
                </a>

            <?php } else { ?>

                <div class="sisa-box">
                    Sisa pembayaran Anda:<br>
                    <b>Rp <?= number_format($sisa, 0, ',', '.'); ?></b>
                </div>

                <form action="proses-bayar-sisa.php?id=<?= htmlspecialchars($idPesanan); ?>" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar</label>
                        <input 
                            type="number" 
                            name="jumlah" 
                            class="form-control" 
                            value="<?= htmlspecialchars($sisa); ?>" 
                            readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input 
                            type="file" 
                            name="bukti" 
                            class="form-control" 
                            accept="image/*,.pdf"
                            required>

                        <div class="upload-note">
                            Format bukti pembayaran: JPG, JPEG, PNG, WEBP, atau PDF.
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="pesanan-saya.php" class="btn-back w-100">
                                Kembali
                            </a>
                        </div>

                        <div class="col-md-6">
                            <button type="submit" class="btn btn-lavender w-100">
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>

                </form>

            <?php } ?>

        </div>

    </div>
</div>

</body>
</html>