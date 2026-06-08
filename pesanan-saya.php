<?php
session_start();
include "auth-pelanggan.php";
require "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include "navbar.php";

$idUser = $_SESSION['user']['idUser'];

/* 
   AMBIL DATA PESANAN
   totalBayar = hanya pembayaran yang sudah diverifikasi admin
   totalPending = bukti yang sudah diupload tapi belum diverifikasi
   isCashOrder = pesanan cash di toko
*/
$query = mysqli_query($koneksi, "
    SELECT 
        p.*,
        pl.idUser,

        COALESCE((
            SELECT SUM(pb1.jumlah)
            FROM pembayaran pb1
            WHERE pb1.idPesanan = p.idPesanan
            AND pb1.status IN ('DP Masuk', 'Lunas')
        ), 0) AS totalBayar,

        COALESCE((
            SELECT SUM(pb2.jumlah)
            FROM pembayaran pb2
            WHERE pb2.idPesanan = p.idPesanan
            AND pb2.status = 'Pending'
        ), 0) AS totalPending,

        COALESCE((
            SELECT COUNT(*)
            FROM pembayaran pb3
            WHERE pb3.idPesanan = p.idPesanan
            AND pb3.metode = 'Cash di Toko'
        ), 0) AS isCashOrder

    FROM pesanan p
    JOIN pelanggan pl 
        ON p.idPelanggan = pl.idPelanggan
    WHERE pl.idUser = '$idUser'
    ORDER BY p.idPesanan DESC
");

if (!$query) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d M Y", strtotime($tanggal));
}

function badgeStatusPesanan($status) {
    $statusLower = strtolower($status);

    if ($statusLower == "menunggu") {
        return "badge-menunggu";
    } elseif ($statusLower == "menunggu upload bukti pembayaran") {
        return "badge-upload";
    } elseif ($statusLower == "menunggu verifikasi pembayaran") {
        return "badge-verifikasi";
    } elseif ($statusLower == "menunggu pembayaran di toko") {
        return "badge-tunai";
    } elseif ($statusLower == "menunggu pembayaran tunai di toko") {
        return "badge-tunai";
    } elseif ($statusLower == "diproses" || $statusLower == "proses") {
        return "badge-proses";
    } elseif ($statusLower == "selesai") {
        return "badge-selesai";
    } elseif ($statusLower == "batal") {
        return "badge-batal";
    } else {
        return "badge-menunggu";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Pesanan Saya - The Four Label</title>

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

.header-page {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 28px;
    padding: 32px;
    margin-bottom: 28px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.18);
    position: relative;
    overflow: hidden;
}

.header-page::before {
    content: "";
    position: absolute;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    top: -60px;
    right: -40px;
}

.header-page h3,
.header-page p {
    position: relative;
    z-index: 2;
}

.header-page h3 {
    font-weight: 850;
    margin-bottom: 8px;
}

.header-page p {
    margin: 0;
    opacity: 0.95;
}

.order-card {
    border: none;
    border-radius: 24px;
    box-shadow: 0 12px 32px rgba(142, 68, 173, 0.12);
    border: 1px solid #eadcff;
    overflow: hidden;
    margin-bottom: 20px;
    transition: 0.25s ease;
    background: white;
}

.order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 38px rgba(142, 68, 173, 0.17);
}

.order-top {
    background: #faf5ff;
    border-bottom: 1px solid #eadcff;
    padding: 18px 22px;
}

.invoice {
    font-weight: 850;
    color: #6f2da8;
    font-size: 17px;
}

.order-date {
    color: #777;
    font-size: 14px;
}

.order-body {
    padding: 22px;
}

.label {
    font-size: 12px;
    color: #888;
    margin-bottom: 5px;
    font-weight: 650;
}

.value {
    font-weight: 750;
    color: #333;
}

.badge-custom {
    padding: 8px 13px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    display: inline-block;
    line-height: 1.5;
}

.badge-menunggu {
    background: #fff3cd;
    color: #856404;
}

.badge-upload {
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

.badge-pending {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-cash {
    background: #f1e3ff;
    color: #7b3fb2;
}

.payment-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 18px;
    padding: 16px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
    color: #5c4b6d;
    font-size: 14px;
}

.payment-row:last-child {
    margin-bottom: 0;
}

.payment-row strong {
    color: #33223f;
}

.btn-lavender {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 16px;
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
    padding: 10px 16px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
    transition: 0.25s;
}

.btn-outline-lavender:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.empty-box {
    background: white;
    border-radius: 26px;
    padding: 45px 30px;
    text-align: center;
    border: 1px solid #eadcff;
    box-shadow: 0 12px 32px rgba(142, 68, 173, 0.12);
}

.empty-icon {
    font-size: 52px;
    margin-bottom: 14px;
}

.empty-box h4 {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 8px;
}

.empty-box p {
    color: #777;
    margin-bottom: 18px;
}

.deadline-warning {
    color: #b45309;
    font-size: 13px;
    margin-top: 4px;
}

.deadline-safe {
    color: #15803d;
    font-size: 13px;
    margin-top: 4px;
}

.note-payment {
    font-size: 13px;
    color: #777;
    margin-top: 8px;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .header-page {
        padding: 26px;
    }

    .order-body {
        padding: 18px;
    }

    .payment-box {
        margin-top: 12px;
    }
}
</style>
</head>

<body>

<div class="container page-wrapper">

    <div class="header-page">
        <h3>Pesanan Saya</h3>
        <p>Lihat status pesanan, deadline selesai, dan pembayaran kamu di sini.</p>
    </div>

    <?php if (mysqli_num_rows($query) == 0) { ?>

        <div class="empty-box">
            <div class="empty-icon">🛍️</div>
            <h4>Belum ada pesanan</h4>
            <p>Kamu belum melakukan pemesanan produk atau custom order.</p>
            <a href="produk.php" class="btn-lavender">
                Lihat Produk
            </a>
        </div>

    <?php } ?>

    <?php while ($row = mysqli_fetch_assoc($query)) { ?>

        <?php
        $totalBayar = (int) ($row['totalBayar'] ?? 0);
        $totalPending = (int) ($row['totalPending'] ?? 0);
        $totalPesanan = (int) ($row['total'] ?? 0);
        $isCashOrder = (int) ($row['isCashOrder'] ?? 0);

        $sisa = $totalPesanan - $totalBayar;

        if ($sisa < 0) {
            $sisa = 0;
        }

        $isLunas = ($totalBayar >= $totalPesanan && $totalPesanan > 0);

        $invoice = $row['nomorInvoice'];
        if ($invoice == "" || $invoice == NULL) {
            $invoice = "#" . $row['idPesanan'];
        }

        $jenisPesanan = $row['jenisPesanan'];
        if ($jenisPesanan == "siap_pakai") {
            $jenisPesananText = "Siap Pakai";
        } elseif ($jenisPesanan == "custom") {
            $jenisPesananText = "Custom";
        } else {
            $jenisPesananText = $jenisPesanan;
        }

        $deadline = $row['deadlineSelesai'] ?? null;

        $deadlineText = "-";
        $deadlineInfo = "";

        if ($deadline != "" && $deadline != NULL && $deadline != "0000-00-00") {
            $deadlineText = formatTanggal($deadline);

            $hariIni = date("Y-m-d");

            if ($deadline < $hariIni && strtolower($row['status']) != "selesai") {
                $deadlineInfo = "<div class='deadline-warning'>Deadline sudah lewat</div>";
            } else {
                $deadlineInfo = "<div class='deadline-safe'>Estimasi selesai sesuai jadwal</div>";
            }
        }

        if ($isLunas) {
            $statusPembayaranText = "Lunas";
            $statusPembayaranClass = "badge-lunas";
        } elseif ($totalPending > 0) {
            $statusPembayaranText = "Menunggu Verifikasi";
            $statusPembayaranClass = "badge-pending";
        } elseif ($isCashOrder > 0) {
            $statusPembayaranText = "Cash di Toko";
            $statusPembayaranClass = "badge-cash";
        } elseif ($totalBayar > 0) {
            $statusPembayaranText = "DP / Belum Lunas";
            $statusPembayaranClass = "badge-belum";
        } else {
            $statusPembayaranText = "Belum Bayar";
            $statusPembayaranClass = "badge-belum";
        }
        ?>

        <div class="card order-card">

            <div class="order-top">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="label">Invoice</div>
                        <div class="invoice">
                            <?= htmlspecialchars($invoice); ?>
                        </div>
                    </div>

                    <div class="text-md-end">
                        <div class="label">Tanggal Pesan</div>
                        <div class="order-date">
                            <?= formatTanggal($row['tanggal']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-body">

                <div class="row g-3 align-items-start">

                    <div class="col-md-2">
                        <div class="label">Jenis Pesanan</div>
                        <div class="value">
                            <?= htmlspecialchars($jenisPesananText); ?>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="label">Deadline Selesai</div>
                        <div class="value">
                            <?= htmlspecialchars($deadlineText); ?>
                        </div>
                        <?= $deadlineInfo; ?>
                    </div>

                    <div class="col-md-2">
                        <div class="label">Status Pesanan</div>
                        <span class="badge-custom <?= badgeStatusPesanan($row['status']); ?>">
                            <?= htmlspecialchars($row['status']); ?>
                        </span>
                    </div>

                    <div class="col-md-3">
                        <div class="payment-box">

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

                        <?php if ($totalPending > 0) { ?>
                            <div class="note-payment">
                                Bukti pembayaran sudah diupload dan sedang menunggu verifikasi admin.
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-3">
                        <div class="label">Status Pembayaran</div>

                        <span class="badge-custom <?= $statusPembayaranClass; ?>">
                            <?= htmlspecialchars($statusPembayaranText); ?>
                        </span>

                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <?php if (!$isLunas && $totalPending <= 0 && $isCashOrder <= 0 && $totalBayar <= 0) { ?>

                                <a
                                    href="upload-pembayaran.php?id=<?= $row['idPesanan']; ?>"
                                    class="btn-lavender">
                                    Upload Pembayaran
                                </a>

                            <?php } elseif (!$isLunas && $totalPending <= 0 && $isCashOrder <= 0 && $totalBayar > 0) { ?>

                                <a
                                    href="bayar-sisa.php?id=<?= $row['idPesanan']; ?>"
                                    class="btn-lavender">
                                    Bayar Sisa
                                </a>

                            <?php } ?>

                            <a
                                href="detail-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                class="btn-outline-lavender">
                                Detail
                            </a>

                        </div>

                        <?php if ($isCashOrder > 0 && !$isLunas) { ?>
                            <div class="note-payment">
                                Pembayaran dilakukan langsung di toko saat pengambilan barang.
                            </div>
                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    <?php } ?>

</div>

<?php include "footer.php"; ?>

</body>
</html>