<?php
require "auth.php";
require "../koneksi.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID pesanan tidak ditemukan.'); window.location.href='pesanan.php';</script>";
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   DATA PESANAN
========================= */
$query = mysqli_query($koneksi, "
    SELECT 
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
        ), 0) AS isCashOrder

    FROM pesanan
    JOIN pelanggan ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user ON pelanggan.idUser = user.idUser
    WHERE pesanan.idPesanan = '$id'
");

if (!$query) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data pesanan tidak ditemukan.'); window.location.href='pesanan.php';</script>";
    exit;
}

/* =========================
   CEK JENIS PESANAN
========================= */
$jenisPesanan = isset($data['jenisPesanan']) ? strtolower(trim($data['jenisPesanan'])) : "";
$isCustom = ($jenisPesanan == "custom");

/* =========================
   DETAIL PRODUK
========================= */
if ($isCustom) {

    $detail = mysqli_query($koneksi, "
        SELECT *
        FROM detail_custom
        WHERE idPesanan = '$id'
    ");

} else {

    $detail = mysqli_query($koneksi, "
        SELECT 
            detail_pesanan.*,
            produk.namaProduk,
            produk.harga
        FROM detail_pesanan
        LEFT JOIN produk ON detail_pesanan.idProduk = produk.idProduk
        WHERE detail_pesanan.idPesanan = '$id'
    ");

}

if (!$detail) {
    die("Query detail produk error: " . mysqli_error($koneksi));
}

/* =========================
   RIWAYAT PEMBAYARAN
========================= */
$pembayaran = mysqli_query($koneksi, "
    SELECT * 
    FROM pembayaran 
    WHERE idPesanan = '$id'
    ORDER BY idPembayaran DESC
");

if (!$pembayaran) {
    die("Query pembayaran error: " . mysqli_error($koneksi));
}

/* =========================
   PEMBAYARAN PENDING TERBARU
========================= */
$qPending = mysqli_query($koneksi, "
    SELECT * 
    FROM pembayaran
    WHERE idPesanan = '$id'
    AND status = 'Pending'
    ORDER BY idPembayaran DESC
    LIMIT 1
");

if (!$qPending) {
    die("Query pending error: " . mysqli_error($koneksi));
}

$pending = mysqli_fetch_assoc($qPending);

$totalPesanan = (int) $data['total'];
$totalBayar = (int) $data['totalBayar'];
$totalPending = (int) $data['totalPending'];

$sisa = $totalPesanan - $totalBayar;
if ($sisa < 0) {
    $sisa = 0;
}

/* =========================
   FUNCTION
========================= */
function formatTanggal($tanggal) {
    if ($tanggal == "" || $tanggal == NULL || $tanggal == "0000-00-00") {
        return "-";
    }

    return date("d-m-Y", strtotime($tanggal));
}

function formatJenisPesanan($jenis) {
    if ($jenis == "siap_pakai") {
        return "Siap Pakai";
    } elseif ($jenis == "custom") {
        return "Custom";
    } else {
        return $jenis;
    }
}

function badgePembayaran($status) {
    $status = strtolower($status);

    if ($status == "pending") {
        return "badge-blue";
    } elseif ($status == "dp masuk") {
        return "badge-yellow";
    } elseif ($status == "lunas") {
        return "badge-green";
    } elseif ($status == "belum bayar") {
        return "badge-red";
    } else {
        return "badge-purple";
    }
}

function ambilKolom($array, $listKolom, $default = "-") {
    foreach ($listKolom as $kolom) {
        if (isset($array[$kolom]) && $array[$kolom] !== "" && $array[$kolom] !== null) {
            return $array[$kolom];
        }
    }

    return $default;
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
    font-weight: 650;
}

.info-value {
    font-weight: 700;
    color: #33223f;
    margin-bottom: 12px;
}

.badge-status,
.badge-purple,
.badge-blue,
.badge-green,
.badge-yellow,
.badge-red {
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    display: inline-block;
}

.badge-status,
.badge-purple {
    background: #eadcff;
    color: #6f2da8;
}

.badge-blue {
    background: #dbeafe;
    color: #1d4ed8;
}

.badge-green {
    background: #dcfce7;
    color: #15803d;
}

.badge-yellow {
    background: #fff3cd;
    color: #856404;
}

.badge-red {
    background: #fee2e2;
    color: #b91c1c;
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
    gap: 12px;
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

.btn-verify {
    background: #16a34a;
    color: white;
    border-radius: 12px;
    padding: 9px 16px;
    font-weight: 800;
    text-decoration: none;
    display: inline-block;
}

.btn-verify:hover {
    color: white;
    background: #15803d;
}

.btn-proof {
    background: white;
    color: #8e44ad;
    border: 1px solid #d9c0f0;
    border-radius: 12px;
    padding: 8px 12px;
    font-weight: 750;
    text-decoration: none;
    display: inline-block;
}

.btn-proof:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.empty-data {
    text-align: center;
    color: #888;
    padding: 18px;
}

.pending-box {
    background: #eef6ff;
    border: 1px solid #cfe8ff;
    color: #1d4ed8;
    border-radius: 15px;
    padding: 14px;
    font-size: 14px;
    margin-bottom: 16px;
}

.proof-img {
    width: 100%;
    max-height: 260px;
    object-fit: contain;
    border-radius: 14px;
    border: 1px solid #eadcff;
    background: #faf5ff;
    padding: 8px;
}

.custom-detail-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 12px;
    padding: 10px 12px;
    margin-top: 6px;
    font-size: 13px;
    color: #4b315f;
}

.custom-detail-box div {
    margin-bottom: 3px;
}

.custom-detail-box div:last-child {
    margin-bottom: 0;
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

            <?php if ($pending) { ?>
                <a 
                    href="verifikasi-pembayaran.php?id=<?= $data['idPesanan']; ?>" 
                    class="btn-verify ms-2"
                    onclick="return confirm('Verifikasi pembayaran ini? Pastikan bukti sudah benar.')">
                    Verifikasi Pembayaran
                </a>
            <?php } ?>
        </div>

        <?php if ($pending) { ?>
            <div class="pending-box">
                Ada pembayaran pending sebesar
                <b>Rp <?= number_format($pending['jumlah'], 0, ',', '.'); ?></b>
                melalui <b><?= htmlspecialchars($pending['metode']); ?></b>.
                Cek bukti pembayaran di bagian <b>Riwayat Pembayaran</b>, lalu klik tombol
                <b>Verifikasi Pembayaran</b> kalau bukti sudah benar.
            </div>
        <?php } ?>

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
                                <?= htmlspecialchars(formatJenisPesanan($data['jenisPesanan'])); ?>
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
                                <?php if ($isCustom) { ?>
                                    <tr>
                                        <th style="width: 80px;">No</th>
                                        <th>Produk</th>
                                        <th style="width: 120px;">Qty</th>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                <?php } ?>
                            </thead>

                            <tbody>
                                <?php if ($detail && mysqli_num_rows($detail) > 0) { ?>
                                    <?php $no = 1; while ($d = mysqli_fetch_assoc($detail)) { ?>

                                        <?php if ($isCustom) { ?>

                                            <?php
                                            $namaCustom = ambilKolom(
                                                $d,
                                                ['namaProduk', 'nama_custom', 'produk_custom', 'jenisProduk', 'jenis_baju'],
                                                'Pesanan Custom'
                                            );

                                            $jenisBaju = ambilKolom($d, ['jenis_baju', 'jenisBaju', 'model', 'model_baju'], '');
                                            $bahan = ambilKolom($d, ['bahan', 'jenis_bahan'], '');
                                            $ukuran = ambilKolom($d, ['ukuran', 'size'], '');
                                            $warna = ambilKolom($d, ['warna', 'color'], '');
                                            $desain = ambilKolom($d, ['desain', 'gambar_desain', 'file_desain'], '');
                                            $catatanCustom = ambilKolom($d, ['catatan', 'keterangan', 'detail', 'request'], '');

                                            $qtyProduk = (int) ambilKolom($d, ['qty', 'jumlah', 'quantity'], 0);
                                            ?>

                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <b><?= htmlspecialchars($namaCustom); ?></b>

                                                    <div class="custom-detail-box">
                                                        <?php if (!empty($jenisBaju)) { ?>
                                                            <div><b>Jenis:</b> <?= htmlspecialchars($jenisBaju); ?></div>
                                                        <?php } ?>

                                                        <?php if (!empty($bahan)) { ?>
                                                            <div><b>Bahan:</b> <?= htmlspecialchars($bahan); ?></div>
                                                        <?php } ?>

                                                        <?php if (!empty($ukuran)) { ?>
                                                            <div><b>Ukuran:</b> <?= htmlspecialchars($ukuran); ?></div>
                                                        <?php } ?>

                                                        <?php if (!empty($warna)) { ?>
                                                            <div><b>Warna:</b> <?= htmlspecialchars($warna); ?></div>
                                                        <?php } ?>

                                                        <?php if (!empty($catatanCustom)) { ?>
                                                            <div><b>Catatan:</b> <?= htmlspecialchars($catatanCustom); ?></div>
                                                        <?php } ?>

                                                        <?php if (!empty($desain)) { ?>
                                                            <div>
                                                                <b>Desain:</b>
                                                                <a href="../upload/<?= htmlspecialchars($desain); ?>" target="_blank">
                                                                    Lihat Desain
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </td>

                                                <td><?= $qtyProduk; ?></td>
                                            </tr>

                                        <?php } else { ?>

                                            <?php
                                            $qtyProduk = isset($d['qty']) ? (int) $d['qty'] : 0;
                                            $hargaProduk = isset($d['harga']) ? (int) $d['harga'] : 0;
                                            $subtotalProduk = $hargaProduk * $qtyProduk;
                                            ?>

                                            <tr>
                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= !empty($d['namaProduk']) 
                                                        ? htmlspecialchars($d['namaProduk']) 
                                                        : "Produk"; 
                                                    ?>
                                                </td>

                                                <td>Rp <?= number_format($hargaProduk, 0, ',', '.'); ?></td>
                                                <td><?= $qtyProduk; ?></td>
                                                <td class="price">Rp <?= number_format($subtotalProduk, 0, ',', '.'); ?></td>
                                            </tr>

                                        <?php } ?>

                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="<?= $isCustom ? '3' : '5'; ?>" class="empty-data">
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
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Bukti</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($pembayaran && mysqli_num_rows($pembayaran) > 0) { ?>
                                    <?php $no = 1; while ($b = mysqli_fetch_assoc($pembayaran)) { ?>

                                        <?php
                                        $bukti = isset($b['bukti']) ? $b['bukti'] : "";
                                        $extBukti = strtolower(pathinfo($bukti, PATHINFO_EXTENSION));
                                        ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <?= !empty($b['metode']) ? htmlspecialchars($b['metode']) : "-"; ?>
                                            </td>

                                            <td class="price">
                                                Rp <?= number_format($b['jumlah'], 0, ',', '.'); ?>
                                            </td>

                                            <td>
                                                <span class="<?= badgePembayaran($b['status']); ?>">
                                                    <?= !empty($b['status']) ? htmlspecialchars($b['status']) : "-"; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if (!empty($bukti)) { ?>

                                                    <?php if (in_array($extBukti, ['jpg', 'jpeg', 'png', 'webp'])) { ?>
                                                        <a href="../upload/<?= htmlspecialchars($bukti); ?>" target="_blank">
                                                            <img src="../upload/<?= htmlspecialchars($bukti); ?>" class="proof-img" alt="Bukti Pembayaran">
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="../upload/<?= htmlspecialchars($bukti); ?>" target="_blank" class="btn-proof">
                                                            Lihat Bukti
                                                        </a>
                                                    <?php } ?>

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
                    <div class="info-value">
                        <?= !empty($data['alamat_kirim']) ? htmlspecialchars($data['alamat_kirim']) : "-"; ?>
                    </div>

                    <div class="info-label">Jasa Kirim</div>
                    <div class="info-value">
                        <?= !empty($data['jasa_kirim']) ? htmlspecialchars($data['jasa_kirim']) : "-"; ?>
                    </div>

                    <div class="info-label">Ongkir</div>
                    <div class="info-value price">
                        Rp <?= number_format($data['ongkir'] ?? 0, 0, ',', '.'); ?>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="section-title">Ringkasan Bayar</h5>

                    <div class="payment-card">
                        <div class="payment-row">
                            <span>Total Pesanan</span>
                            <strong>Rp <?= number_format($totalPesanan, 0, ',', '.'); ?></strong>
                        </div>

                        <div class="payment-row">
                            <span>Diverifikasi</span>
                            <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                        </div>

                        <?php if ($totalPending > 0) { ?>
                            <div class="payment-row">
                                <span>Pending</span>
                                <strong>Rp <?= number_format($totalPending, 0, ',', '.'); ?></strong>
                            </div>
                        <?php } ?>

                        <hr>

                        <div class="payment-row">
                            <span>Sisa Bayar</span>
                            <strong class="price">Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
                        </div>
                    </div>

                    <?php if ($pending) { ?>
                        <a 
                            href="verifikasi-pembayaran.php?id=<?= $data['idPesanan']; ?>" 
                            class="btn-verify w-100 text-center mt-3"
                            onclick="return confirm('Verifikasi pembayaran ini? Pastikan bukti sudah benar.')">
                            Verifikasi Pembayaran
                        </a>
                    <?php } ?>
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>