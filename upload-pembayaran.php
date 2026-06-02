<?php
session_start();
require "koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: pesanan-saya.php");
    exit;
}

$idPesanan = $_GET['id'];

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
        pelanggan.alamat
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

$total = $data['total'];
$minDP = $total * 0.5;
$error = "";

/* =========================
   PROSES PEMBAYARAN
========================= */
if (isset($_POST['jumlah'])) {

    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode'];

    if ($jumlah < $minDP) {

        $error = "Minimal pembayaran adalah 50% yaitu Rp " . number_format($minDP, 0, ',', '.');

    } else {

        $file = "";
        $statusPembayaran = "";
        $statusPesanan = "";

        /* =========================
           TRANSFER BCA
        ========================= */
        if ($metode == "Transfer BCA") {

            if (empty($_FILES['bukti']['name'])) {
                $error = "Bukti transfer wajib diupload untuk pembayaran transfer.";
            } else {

                $namaFile = $_FILES['bukti']['name'];
                $tmpFile = $_FILES['bukti']['tmp_name'];
                $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

                if (!in_array($ext, $allowed)) {
                    $error = "Format bukti transfer harus JPG, JPEG, PNG, WEBP, atau PDF.";
                } else {

                    if (!is_dir("upload")) {
                        mkdir("upload", 0777, true);
                    }

                    $file = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);

                    move_uploaded_file(
                        $tmpFile,
                        "upload/" . $file
                    );

                    $statusPembayaran = "Pending";
                    $statusPesanan = "Menunggu Verifikasi Pembayaran";
                }
            }

        }

        /* =========================
           TUNAI
        ========================= */
        if ($metode == "Tunai") {
            $file = "";
            $statusPembayaran = "Tunai";
            $statusPesanan = "Menunggu Pembayaran Tunai";
        }

        /* =========================
           SIMPAN KE DATABASE
        ========================= */
        if ($error == "") {

            $simpan = mysqli_query(
                $koneksi,
                "INSERT INTO pembayaran (
                    idPesanan,
                    jumlah,
                    metode,
                    status,
                    bukti
                ) VALUES (
                    '$idPesanan',
                    '$jumlah',
                    '$metode',
                    '$statusPembayaran',
                    '$file'
                )"
            );

            if (!$simpan) {
                die("Gagal menyimpan pembayaran: " . mysqli_error($koneksi));
            }

            mysqli_query(
                $koneksi,
                "UPDATE pesanan 
                 SET status='$statusPesanan'
                 WHERE idPesanan='$idPesanan'"
            );

            if ($metode == "Tunai") {
                header("Location: kwitansi-tunai.php?id=$idPesanan");
                exit;
            } else {
                header("Location: pesanan-saya.php");
                exit;
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

<title>Upload Pembayaran</title>

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
    max-width: 560px;
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

.transfer-info {
    background: #eef6ff;
    border: 1px solid #cfe8ff;
    color: #1d4ed8;
    border-radius: 16px;
    padding: 14px;
    font-size: 14px;
    margin-top: 10px;
}

.tunai-info {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    border-radius: 16px;
    padding: 14px;
    font-size: 14px;
    margin-top: 10px;
    display: none;
}
</style>
</head>

<body>

<div class="payment-wrapper">
    <div class="container">

        <div class="card card-box">

            <div class="card-header-custom">
                <h4>Upload Pembayaran</h4>
                <p>Selesaikan pembayaran pesanan kamu</p>
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
                            <?= $data['nomorInvoice'] ? htmlspecialchars($data['nomorInvoice']) : "#" . $idPesanan; ?>
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
                        <span>Minimal DP 50%</span>
                        <strong>Rp <?= number_format($minDP, 0, ',', '.'); ?></strong>
                    </div>

                </div>

                <div class="dp-highlight">
                    Minimal pembayaran adalah <b>50%</b> dari total pesanan.  
                    Untuk pembayaran tunai, kwitansi dapat langsung dicetak setelah submit.
                </div>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar</label>
                        <input 
                            type="number" 
                            name="jumlah" 
                            class="form-control" 
                            min="<?= $minDP; ?>"
                            placeholder="Masukkan jumlah pembayaran"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="metode" id="metode" class="form-select" required onchange="ubahMetode()">
                            <option value="Transfer BCA">Transfer BCA</option>
                            <option value="Tunai">Tunai</option>
                        </select>

                        <div class="transfer-info" id="transferInfo">
                            Transfer ke rekening BCA: <b>1234567890</b> a.n. <b>The Four Label</b>
                        </div>

                        <div class="tunai-info" id="tunaiInfo">
                            Pembayaran dilakukan secara tunai. Setelah submit, sistem akan membuat kwitansi tunai.
                        </div>
                    </div>

                    <div class="mb-4" id="buktiBox">
                        <label class="form-label">Upload Bukti Transfer</label>
                        <input 
                            type="file" 
                            name="bukti" 
                            id="bukti"
                            class="form-control"
                            accept="image/*,.pdf"
                            required>
                    </div>

                    <button type="submit" class="btn btn-lavender w-100">
                        Simpan Pembayaran
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

<script>
function ubahMetode() {
    const metode = document.getElementById("metode").value;
    const buktiBox = document.getElementById("buktiBox");
    const bukti = document.getElementById("bukti");
    const transferInfo = document.getElementById("transferInfo");
    const tunaiInfo = document.getElementById("tunaiInfo");

    if (metode === "Tunai") {
        buktiBox.style.display = "none";
        bukti.required = false;
        bukti.value = "";
        transferInfo.style.display = "none";
        tunaiInfo.style.display = "block";
    } else {
        buktiBox.style.display = "block";
        bukti.required = true;
        transferInfo.style.display = "block";
        tunaiInfo.style.display = "none";
    }
}
</script>

</body>
</html>