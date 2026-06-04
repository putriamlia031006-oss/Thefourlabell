<?php
require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID pesanan tidak ditemukan.'); window.location.href='pesanan.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "SELECT 
    pesanan.*,
    pelanggan.noHp,
    pelanggan.alamat,
    user.nama AS namaPelanggan,
    user.email,
    COALESCE(SUM(pembayaran.jumlah), 0) AS totalBayar
FROM pesanan
JOIN pelanggan ON pesanan.idPelanggan = pelanggan.idPelanggan
JOIN user ON pelanggan.idUser = user.idUser
LEFT JOIN pembayaran ON pesanan.idPesanan = pembayaran.idPesanan
WHERE pesanan.idPesanan = '$id'
GROUP BY pesanan.idPesanan");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data pesanan tidak ditemukan.'); window.location.href='pesanan.php';</script>";
    exit;
}

$detail = mysqli_query($koneksi, "SELECT 
    detail_pesanan.*,
    produk.namaProduk,
    produk.harga
FROM detail_pesanan
LEFT JOIN produk ON detail_pesanan.idProduk = produk.idProduk
WHERE detail_pesanan.idPesanan = '$id'");

$pembayaran = mysqli_query($koneksi, "SELECT * FROM pembayaran 
WHERE idPesanan = '$id'
ORDER BY idPembayaran DESC");

$totalPesanan = $data['total'];
$totalBayar = $data['totalBayar'];
$sisa = $totalPesanan - $totalBayar;

if ($sisa < 0) {
    $sisa = 0;
}

function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Detail Pesanan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
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
}

.main-content {
    margin-left: 230px;
    width: calc(100% - 230px);
    padding: 24px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 22px 24px;
    border-radius: 18px;
    margin-bottom: 22px;
    box-shadow: 0 8px 20px rgba(111, 66, 193, 0.18);
}

.page-header h3 {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}

.page-header p {
    margin: 0;
    font-size: 13px;
    opacity: 0.92;
}

.card-box {
    background: white;
    border-radius: 18px;
    padding: 20px;
    border: 1px solid #eadcff;
    box-shadow: 0 8px 20px rgba(142, 68, 173, 0.10);
    margin-bottom: 20px;
}

.section-title {
    color: #6f2da8;
    font-weight: 800;
    font-size: 17px;
    margin-bottom: 16px;
}

.info-label {
    color: #777;
    font-size: 13px;
    margin-bottom: 3px;
}

.info-value {
    font-weight: 700;
    color: #33223f;
    margin-bottom: 12px;
}

.badge-status {
    background: #eadcff;
    color: #6f2da8;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    display: inline-block;
}

.payment-card {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 15px;
    padding: 16px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.payment-row:last-child {
    margin-bottom: 0;
}

.price {
    color: #7b3fb2;
    font-weight: 800;
}

.table thead th {
    background: #f1e3ff;
    color: #6f2da8;
    border: none;
    padding: 11px;
    font-size: 13px;
}

.table tbody td {
    padding: 11px;
    border-color: #f0e3ff;
    vertical-align: middle;
}

.btn-kembali {
    background: #e5e7eb;
    color: #374151;
    border-radius: 12px;
    padding: 9px 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-block;
}

.btn-kembali:hover {
    background: #d1d5db;
    color: #111827;
}

.btn-update {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border-radius: 12px;
    padding: 9px 16px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
}

.btn-update:hover {
    color: white;
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 18px;
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
            <h3>📦 Detail Pesanan</h3>
            <p>Informasi lengkap pesanan, pelanggan, produk, pengiriman, dan pembayaran.</p>
        </div>

        <div class="mb-3">
            <a href="pesanan.php" class="btn-kembali">← Kembali</a>
            <a href="update-status.php?id=<?= $data['idPesanan']; ?>" class="btn-update ms-2">Update Status</a>
        </div>

        <div class="row">

            <div class="col-md-8">

                <div class="card-box">
                    <h5 class="section-title">Data Pesanan</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Nomor Invoice</div>
                            <div class="info-value">
                                <?= !empty($data['nomorInvoice']) ? htmlspecialchars($data['nomorInvoice']) : "#" . $data['idPesanan']; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Status Pesanan</div>
                            <div class="info-value">
                                <span class="badge-status">
                                    <?= htmlspecialchars($data['status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Tanggal Pesan</div>
                            <div class="info-value">
                                <?= formatTanggal($data['tanggal']); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Deadline Selesai</div>
                            <div class="info-value">
                                <?= formatTanggal($data['deadlineSelesai'] ?? null); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Jenis Pesanan</div>
                            <div class="info-value">
                                <?= htmlspecialchars($data['jenisPesanan']); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Catatan</div>
                            <div class="info-value">
                                <?= !empty($data['catatan']) ? htmlspecialchars($data['catatan']) : "-"; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="section-title">Detail Produk</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($detail && mysqli_num_rows($detail) > 0) { ?>
                                    <?php $no = 1; while ($d = mysqli_fetch_assoc($detail)) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= !empty($d['namaProduk']) ? htmlspecialchars($d['namaProduk']) : "Produk Custom"; ?></td>
                                            <td>Rp <?= number_format($d['harga'], 0, ',', '.'); ?></td>
                                            <td><?= $d['jumlah']; ?></td>
                                            <td class="price">Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="empty-data">
                                            Belum ada detail produk.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="section-title">Riwayat Pembayaran</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($pembayaran && mysqli_num_rows($pembayaran) > 0) { ?>
                                    <?php $no = 1; while ($b = mysqli_fetch_assoc($pembayaran)) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= formatTanggal($b['tanggalPembayaran'] ?? null); ?></td>
                                            <td><?= !empty($b['metodePembayaran']) ? htmlspecialchars($b['metodePembayaran']) : "-"; ?></td>
                                            <td class="price">Rp <?= number_format($b['jumlah'], 0, ',', '.'); ?></td>
                                            <td><?= !empty($b['status']) ? htmlspecialchars($b['status']) : "-"; ?></td>
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

            <div class="col-md-4">

                <div class="card-box">
                    <h5 class="section-title">Data Pelanggan</h5>

                    <div class="info-label">Nama</div>
                    <div class="info-value"><?= htmlspecialchars($data['namaPelanggan']); ?></div>

                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($data['email']); ?></div>

                    <div class="info-label">No HP</div>
                    <div class="info-value"><?= !empty($data['noHp']) ? htmlspecialchars($data['noHp']) : "-"; ?></div>

                    <div class="info-label">Alamat Akun</div>
                    <div class="info-value"><?= !empty($data['alamat']) ? htmlspecialchars($data['alamat']) : "-"; ?></div>
                </div>

                <div class="card-box">
                    <h5 class="section-title">Pengiriman</h5>

                    <div class="info-label">Alamat Kirim</div>
                    <div class="info-value"><?= !empty($data['alamat_kirim']) ? htmlspecialchars($data['alamat_kirim']) : "-"; ?></div>

                    <div class="info-label">Jasa Kirim</div>
                    <div class="info-value"><?= !empty($data['jasa_kirim']) ? htmlspecialchars($data['jasa_kirim']) : "-"; ?></div>

                    <div class="info-label">Ongkir</div>
                    <div class="info-value price">Rp <?= number_format($data['ongkir'] ?? 0, 0, ',', '.'); ?></div>
                </div>

                <div class="card-box">
                    <h5 class="section-title">Ringkasan Bayar</h5>

                    <div class="payment-card">
                        <div class="payment-row">
                            <span>Total Pesanan</span>
                            <strong>Rp <?= number_format($totalPesanan, 0, ',', '.'); ?></strong>
                        </div>

                        <div class="payment-row">
                            <span>Total Dibayar</span>
                            <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                        </div>

                        <hr>

                        <div class="payment-row">
                            <span>Sisa Bayar</span>
                            <strong class="price">Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>