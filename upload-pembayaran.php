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
$idUser = $_SESSION['user']['idUser'];

/* =========================
   AMBIL DATA PESANAN
========================= */
$q = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        user.nama AS namaPelanggan,
        user.email,
        pelanggan.noHp,
        pelanggan.alamat,
        pelanggan.idUser
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    WHERE pesanan.idPesanan='$idPesanan'"
);

if (!$q) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Data pesanan tidak ditemukan.");
}

/* CEK AGAR USER TIDAK AKSES PESANAN ORANG LAIN */
if ($data['idUser'] != $idUser) {
    die("Akses ditolak.");
}

$total = (int) $data['total'];
$minDP = $total * 0.5;
$error = "";

/* =========================
   CEK PEMBAYARAN YANG SUDAH DIVERIFIKASI
   Pending TIDAK dihitung sebagai dibayar
========================= */
$qBayar = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalBayar
     FROM pembayaran
     WHERE idPesanan='$idPesanan'
     AND status IN ('DP Masuk', 'Lunas')"
);

if (!$qBayar) {
    die("Query pembayaran error: " . mysqli_error($koneksi));
}

$dataBayar = mysqli_fetch_assoc($qBayar);
$totalBayar = (int) $dataBayar['totalBayar'];

$sisa = $total - $totalBayar;

if ($sisa < 0) {
    $sisa = 0;
}

/* =========================
   CEK APA ADA PEMBAYARAN PENDING
   Supaya user tidak upload berkali-kali
========================= */
$qPending = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(jumlah), 0) AS totalPending
     FROM pembayaran
     WHERE idPesanan='$idPesanan'
     AND status='Pending'"
);

if (!$qPending) {
    die("Query pending error: " . mysqli_error($koneksi));
}

$dataPending = mysqli_fetch_assoc($qPending);
$totalPending = (int) $dataPending['totalPending'];

/* =========================
   JIKA PESANAN CASH, TIDAK PERLU UPLOAD
========================= */
$qCash = mysqli_query(
    $koneksi,
    "SELECT * FROM pembayaran
     WHERE idPesanan='$idPesanan'
     AND metode='Cash di Toko'
     LIMIT 1"
);

$isCashOrder = mysqli_num_rows($qCash) > 0;

/* =========================
   PROSES UPLOAD PEMBAYARAN
========================= */
if (isset($_POST['jumlah'])) {

    if ($isCashOrder) {
        $error = "Pesanan ini menggunakan metode Cash di Toko, jadi tidak perlu upload bukti pembayaran.";
    } elseif ($sisa <= 0) {
        $error = "Pesanan ini sudah lunas.";
    } elseif ($totalPending > 0) {
        $error = "Kamu sudah mengupload bukti pembayaran. Silakan tunggu verifikasi admin.";
    } else {

        $jumlah = (int) $_POST['jumlah'];
        $metode = mysqli_real_escape_string($koneksi, $_POST['metode']);

        if ($jumlah < $minDP) {

            $error = "Minimal pembayaran adalah 50% yaitu Rp " . number_format($minDP, 0, ',', '.');

        } elseif ($jumlah > $sisa) {

            $error = "Jumlah pembayaran tidak boleh melebihi sisa tagihan.";

        } else {

            if (empty($_FILES['bukti']['name'])) {

                $error = "Bukti pembayaran wajib diupload.";

            } else {

                $namaFile = $_FILES['bukti']['name'];
                $tmpFile = $_FILES['bukti']['tmp_name'];
                $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

                if (!in_array($ext, $allowed)) {
                    $error = "Format bukti pembayaran harus JPG, JPEG, PNG, WEBP, atau PDF.";
                } else {

                    if (!is_dir("upload")) {
                        mkdir("upload", 0777, true);
                    }

                    $file = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);

                    $upload = move_uploaded_file(
                        $tmpFile,
                        "upload/" . $file
                    );

                    if (!$upload) {
                        $error = "Gagal upload bukti pembayaran.";
                    } else {

                        $dp = $jumlah;

                        $simpan = mysqli_query(
                            $koneksi,
                            "INSERT INTO pembayaran (
                                idPesanan,
                                jumlah,
                                dp,
                                metode,
                                status,
                                bukti
                            ) VALUES (
                                '$idPesanan',
                                '$jumlah',
                                '$dp',
                                '$metode',
                                'Pending',
                                '$file'
                            )"
                        );

                        if (!$simpan) {
                            die("Gagal menyimpan pembayaran: " . mysqli_error($koneksi));
                        }

                        $updatePesanan = mysqli_query(
                            $koneksi,
                            "UPDATE pesanan 
                             SET status='Menunggu Verifikasi Pembayaran'
                             WHERE idPesanan='$idPesanan'"
                        );

                        if (!$updatePesanan) {
                            die("Gagal update status pesanan: " . mysqli_error($koneksi));
                        }

                        header("Location: pesanan-saya.php");
                        exit;
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Upload Pembayaran - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.payment-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 40px 0;
}

.card-box {
    max-width: 600px;
    margin: auto;
    border: none;
    border-radius: 28px;
    box-shadow: 0 16px 40px rgba(142, 68, 173, 0.14);
    border: 1px solid #eadcff;
    overflow: hidden;
}

.card-header-custom {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 26px;
}

.card-header-custom h4 {
    margin: 0;
    font-weight: 850;
}

.card-header-custom p {
    margin: 6px 0 0;
    opacity: 0.9;
}

.card-body-custom {
    padding: 28px;
    background: white;
}

.info-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 20px;
    padding: 18px;
    margin-bottom: 22px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    gap: 10px;
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

.dp-highlight {
    background: #fff8e7;
    border: 1px solid #ffe3a3;
    color: #7c5a14;
    border-radius: 16px;
    padding: 14px;
    font-size: 14px;
    margin-bottom: 20px;
}

.pending-box {
    background: #eef6ff;
    border: 1px solid #cfe8ff;
    color: #1d4ed8;
    border-radius: 16px;
    padding: 16px;
    font-size: 14px;
    margin-bottom: 20px;
}

.lunas-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    border-radius: 16px;
    padding: 16px;
    font-size: 14px;
    margin-bottom: 20px;
}

.cash-box {
    background: #f6eeff;
    border: 1px solid #e4d2ff;
    color: #6e41a8;
    border-radius: 16px;
    padding: 16px;
    font-size: 14px;
    margin-bottom: 20px;
}

.form-label {
    font-weight: 700;
    color: #4b2e63;
}

.form-control,
.form-select {
    border-radius: 15px;
    padding: 13px 15px;
    border: 1px solid #ddd;
    background: #fcfbff;
}

.form-control:focus,
.form-select:focus {
    border-color: #b57edc;
    box-shadow: 0 0 0 4px rgba(181,126,220,0.17);
}

.payment-info {
    background: #eef6ff;
    border: 1px solid #cfe8ff;
    color: #1d4ed8;
    border-radius: 16px;
    padding: 14px;
    font-size: 14px;
    margin-top: 10px;
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
    .payment-wrapper {
        padding: 24px 12px;
    }

    .card-body-custom {
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
    <div class="container">

        <div class="card card-box">

            <div class="card-header-custom">
                <h4>Upload Pembayaran</h4>
                <p>Upload bukti pembayaran untuk pesanan kamu</p>
            </div>

            <div class="card-body-custom">

                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php } ?>

                <div class="info-box">

                    <div class="info-row">
                        <span>Invoice</span>
                        <strong>
                            <?= !empty($data['nomorInvoice']) ? htmlspecialchars($data['nomorInvoice']) : "#" . htmlspecialchars($idPesanan); ?>
                        </strong>
                    </div>

                    <div class="info-row">
                        <span>Nama</span>
                        <strong><?= htmlspecialchars($data['namaPelanggan']); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Total Pesanan</span>
                        <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Sudah Diverifikasi</span>
                        <strong>Rp <?= number_format($totalBayar, 0, ',', '.'); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Sisa Tagihan</span>
                        <strong>Rp <?= number_format($sisa, 0, ',', '.'); ?></strong>
                    </div>

                    <div class="info-row">
                        <span>Minimal DP 50%</span>
                        <strong>Rp <?= number_format($minDP, 0, ',', '.'); ?></strong>
                    </div>

                </div>

                <?php if ($isCashOrder) { ?>

                    <div class="cash-box">
                        Pesanan ini menggunakan metode <b>Cash di Toko</b>, jadi tidak perlu upload bukti pembayaran.
                        Pembayaran dilakukan langsung saat pengambilan barang.
                    </div>

                    <a href="pesanan-saya.php" class="btn-back w-100">
                        Kembali ke Pesanan Saya
                    </a>

                <?php } elseif ($sisa <= 0) { ?>

                    <div class="lunas-box">
                        Pesanan ini sudah lunas. Tidak ada pembayaran yang perlu diupload.
                    </div>

                    <a href="pesanan-saya.php" class="btn-back w-100">
                        Kembali ke Pesanan Saya
                    </a>

                <?php } elseif ($totalPending > 0) { ?>

                    <div class="pending-box">
                        Kamu sudah mengupload bukti pembayaran sebesar
                        <b>Rp <?= number_format($totalPending, 0, ',', '.'); ?></b>.
                        Silakan tunggu verifikasi admin.
                    </div>

                    <a href="pesanan-saya.php" class="btn-back w-100">
                        Kembali ke Pesanan Saya
                    </a>

                <?php } else { ?>

                    <div class="dp-highlight">
                        Minimal pembayaran adalah <b>50%</b> dari total pesanan.
                        Bukti pembayaran akan dicek admin terlebih dahulu, jadi statusnya akan menjadi <b>Pending</b>.
                    </div>

                    <form method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar</label>
                            <input 
                                type="number" 
                                name="jumlah" 
                                id="jumlah"
                                class="form-control" 
                                min="<?= htmlspecialchars($minDP); ?>"
                                max="<?= htmlspecialchars($sisa); ?>"
                                value="<?= htmlspecialchars($minDP); ?>"
                                placeholder="Masukkan jumlah pembayaran"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode" id="metode" class="form-select" required onchange="ubahMetode()">
                                <option value="Transfer Bank BCA" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BCA
                                </option>

                                <option value="Transfer Bank MANDIRI" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank MANDIRI
                                </option>

                                <option value="Transfer Bank BRI" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BRI
                                </option>

                                <option value="Transfer Bank BNI" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BNI
                                </option>

                                <option value="Transfer SeaBank" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    Transfer SeaBank
                                </option>

                                <option value="DANA" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    DANA
                                </option>

                                <option value="OVO" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    OVO
                                </option>

                                <option value="GoPay" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    GoPay
                                </option>

                                <option value="ShopeePay" data-info="Transfer BCA: 1234567890 a.n. The Four Label">
                                    ShopeePay
                                </option>
                            </select>

                            <div class="payment-info" id="paymentInfo">
                                Transfer BCA: <b>1234567890</b> a.n. <b>The Four Label</b>
                            </div>
                        </div>

                        <div class="mb-4" id="buktiBox">
                            <label class="form-label">Upload Bukti Pembayaran</label>
                            <input 
                                type="file" 
                                name="bukti" 
                                id="bukti"
                                class="form-control"
                                accept="image/*,.pdf"
                                required>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <a href="pesanan-saya.php" class="btn-back w-100">
                                    Kembali
                                </a>
                            </div>

                            <div class="col-md-6">
                                <button type="submit" class="btn btn-lavender w-100">
                                    Simpan Pembayaran
                                </button>
                            </div>
                        </div>

                    </form>

                <?php } ?>

            </div>

        </div>

    </div>
</div>

<script>
function ubahMetode() {
    const metode = document.getElementById("metode");
    const selectedOption = metode.options[metode.selectedIndex];
    const paymentInfo = document.getElementById("paymentInfo");

    const info = selectedOption.getAttribute("data-info");
    paymentInfo.innerHTML = info;
}

document.addEventListener("DOMContentLoaded", function () {
    ubahMetode();
});
</script>

</body>
</html>