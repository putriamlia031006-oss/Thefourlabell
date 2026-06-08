<?php
session_start();
include "auth-pelanggan.php";
require "koneksi.php";
include "navbar.php";

/* CEK LOGIN */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$idUser = $_SESSION['user']['idUser'];

/* CEK CART */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "
    <div style='min-height:60vh; display:flex; align-items:center; justify-content:center; font-family:Arial;'>
        <div style='text-align:center; background:white; padding:35px; border-radius:20px; box-shadow:0 10px 25px rgba(142,68,173,.15);'>
            <h3 style='color:#7b3fb2;'>Keranjang masih kosong</h3>
            <p style='color:#777;'>Silakan pilih produk terlebih dahulu.</p>
            <a href='produk.php' style='display:inline-block; margin-top:10px; background:#8e44ad; color:white; padding:12px 22px; border-radius:12px; text-decoration:none; font-weight:bold;'>Lihat Produk</a>
        </div>
    </div>";
    exit;
}

/* AMBIL DATA PELANGGAN */
$qPelanggan = mysqli_query(
    $koneksi,
    "SELECT * FROM pelanggan WHERE idUser='$idUser'"
);

if (!$qPelanggan) {
    die("Query pelanggan error: " . mysqli_error($koneksi));
}

$pelanggan = mysqli_fetch_assoc($qPelanggan);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan.");
}

$idPelanggan = $pelanggan['idPelanggan'];

/* HITUNG TOTAL KERANJANG */
$total = 0;
$items = [];

foreach ($_SESSION['cart'] as $cart) {

    $idProduk = mysqli_real_escape_string($koneksi, $cart['idProduk']);
    $qty = (int) $cart['qty'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM produk WHERE idProduk='$idProduk'"
    );

    if (!$query) {
        die("Query produk error: " . mysqli_error($koneksi));
    }

    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        continue;
    }

    $subtotal = $data['harga'] * $qty;
    $total += $subtotal;

    $items[] = [
        'idProduk' => $data['idProduk'],
        'namaProduk' => $data['namaProduk'],
        'harga' => $data['harga'],
        'gambar' => $data['gambar'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

/* CEK JUMLAH TRANSAKSI CUSTOMER */
$qTransaksi = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS totalTransaksi
     FROM pesanan
     WHERE idPelanggan='$idPelanggan'
     AND status != 'Batal'"
);

if (!$qTransaksi) {
    die("Query transaksi error: " . mysqli_error($koneksi));
}

$dataTransaksi = mysqli_fetch_assoc($qTransaksi);
$jumlahTransaksi = $dataTransaksi['totalTransaksi'];

/* DISKON 20% JIKA SUDAH 5 TRANSAKSI */
$diskon = 0;
$persenDiskon = 0;

if ($jumlahTransaksi >= 5) {
    $persenDiskon = 20;
    $diskon = $total * 0.20;
}

$totalSetelahDiskon = $total - $diskon;

/* ONGKIR DEFAULT DARI ALAMAT */
function hitungOngkirAlamat($alamat) {
    $alamat = strtolower($alamat);

    if (trim($alamat) == "") {
        return 5000;
    }

    // Area sangat dekat toko / Tangerang sekitar
    if (
        strpos($alamat, "pasar kemis") !== false ||
        strpos($alamat, "rajeg") !== false ||
        strpos($alamat, "cikupa") !== false ||
        strpos($alamat, "sepatan") !== false ||
        strpos($alamat, "sindang jaya") !== false ||
        strpos($alamat, "balaraja") !== false
    ) {
        return 3000;
    }

    // Tangerang umum
    if (
        strpos($alamat, "tangerang") !== false ||
        strpos($alamat, "kabupaten tangerang") !== false ||
        strpos($alamat, "kota tangerang") !== false ||
        strpos($alamat, "tangsel") !== false ||
        strpos($alamat, "tangerang selatan") !== false
    ) {
        return 5000;
    }

    // Jabodetabek
    if (
        strpos($alamat, "jakarta") !== false ||
        strpos($alamat, "bogor") !== false ||
        strpos($alamat, "depok") !== false ||
        strpos($alamat, "bekasi") !== false
    ) {
        return 8000;
    }

    // Provinsi dekat
    if (
        strpos($alamat, "banten") !== false ||
        strpos($alamat, "jawa barat") !== false ||
        strpos($alamat, "jabar") !== false
    ) {
        return 12000;
    }

    // Pulau Jawa
    if (
        strpos($alamat, "jawa tengah") !== false ||
        strpos($alamat, "jateng") !== false ||
        strpos($alamat, "jawa timur") !== false ||
        strpos($alamat, "jatim") !== false ||
        strpos($alamat, "yogyakarta") !== false ||
        strpos($alamat, "jogja") !== false
    ) {
        return 15000;
    }

    // Luar Jawa
    return 20000;
}

$alamatAwal = isset($pelanggan['alamat']) ? $pelanggan['alamat'] : "";
$ongkirDefault = hitungOngkirAlamat($alamatAwal);

$totalAkhirDefault = $totalSetelahDiskon + $ongkirDefault;
$dpDefault = $totalAkhirDefault * 0.5;

function tampilGambarProduk($namaGambar) {
    $namaGambar = trim($namaGambar);

    if ($namaGambar == "") {
        return "";
    }

    $path1 = "upload/" . $namaGambar;
    $path2 = "uploads/" . $namaGambar;
    $path3 = "image/" . $namaGambar;

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Checkout - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #fbf7ff, #efe1ff);
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.checkout-wrapper {
    padding: 45px 0 70px;
}

.page-title {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 8px;
}

.page-subtitle {
    color: #777;
    margin-bottom: 28px;
}

.card-checkout {
    border: none;
    border-radius: 26px;
    box-shadow: 0 14px 35px rgba(142, 68, 173, 0.13);
    border: 1px solid #eadcff;
    overflow: hidden;
    background: white;
}

.card-header-custom {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    padding: 22px 26px;
}

.card-header-custom h5 {
    margin: 0;
    font-weight: 800;
}

.card-body-custom {
    padding: 26px;
}

.label {
    font-weight: 700;
    margin-bottom: 8px;
    color: #4b2e63;
    font-size: 14px;
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

.note-box {
    background: #f6eeff;
    border: 1px solid #e4d2ff;
    color: #6e41a8;
    padding: 14px 16px;
    border-radius: 16px;
    font-size: 14px;
    margin-bottom: 20px;
}

.product-item {
    display: flex;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f0e4ff;
}

.product-item:last-child {
    border-bottom: none;
}

.product-img {
    width: 78px;
    height: 78px;
    border-radius: 16px;
    object-fit: cover;
    background: #f1e3ff;
    border: 1px solid #eadcff;
}

.no-img {
    width: 78px;
    height: 78px;
    border-radius: 16px;
    background: #f1e3ff;
    color: #8e44ad;
    font-size: 12px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.product-name {
    font-weight: 800;
    color: #3d2a4d;
    margin-bottom: 4px;
}

.product-meta {
    color: #777;
    font-size: 14px;
}

.product-price {
    color: #8e44ad;
    font-weight: 800;
    font-size: 15px;
}

.summary-box {
    background: #faf5ff;
    border: 1px solid #eadcff;
    border-radius: 22px;
    padding: 22px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 13px;
    color: #5c4b6d;
}

.summary-row strong {
    color: #33223f;
}

.summary-row.discount {
    color: #15803d;
}

.summary-divider {
    border-top: 1px dashed #d9c0f0;
    margin: 16px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    align-items: center;
    margin-top: 10px;
}

.total-row span {
    font-size: 16px;
    font-weight: 800;
    color: #4b2e63;
}

.total-row h3 {
    color: #7b3fb2;
    font-weight: 900;
    margin: 0;
    font-size: 25px;
}

.dp-box {
    margin-top: 16px;
    background: #fff8e7;
    border: 1px solid #ffe3a3;
    color: #7c5a14;
    padding: 14px;
    border-radius: 16px;
    font-size: 14px;
}

.payment-box {
    background: #f6eeff;
    border: 1px solid #e4d2ff;
    color: #6e41a8;
    padding: 14px 16px;
    border-radius: 16px;
    font-size: 14px;
    margin-top: 12px;
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
    border: 1px solid #d9c0f0;
    border-radius: 16px;
    padding: 14px;
    font-weight: 800;
    text-decoration: none;
    display: block;
    text-align: center;
}

.btn-back:hover {
    background: #f6eeff;
    color: #7b3fb2;
}

.discount-badge {
    display: inline-block;
    background: #dcfce7;
    color: #15803d;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 12px;
}

.no-discount-badge {
    display: inline-block;
    background: #f1e3ff;
    color: #7b3fb2;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 12px;
}

.alamat-textarea {
    height: 120px;
    padding: 15px;
    resize: vertical;
    line-height: 1.5;
}

@media (max-width: 991px) {
    .summary-box {
        margin-top: 24px;
    }
}
</style>
</head>

<body>

<div class="container checkout-wrapper">

    <h2 class="page-title">Checkout</h2>
    <p class="page-subtitle">Lengkapi pengiriman dan metode pembayaran untuk melanjutkan pesanan.</p>

    <form action="proses-checkout.php" method="POST" id="checkoutForm">

        <div class="row g-4">

            <div class="col-lg-7">

                <div class="card-checkout mb-4">
                    <div class="card-header-custom">
                        <h5>Detail Pengiriman</h5>
                    </div>

                    <div class="card-body-custom">

                        <div class="note-box">
                            Alamat otomatis terisi dari akun kamu. Ongkir akan dihitung berdasarkan area alamat.
                        </div>

                        <div class="mb-3">
                            <label class="label">Pilihan Pengiriman</label>
                            <select name="tipe_pengiriman" id="tipe_pengiriman" class="form-select" required onchange="aturPengiriman()">
                                <option value="dikirim">Dikirim ke Alamat</option>
                                <option value="ambil">Ambil di Tempat</option>
                            </select>
                        </div>

                        <div class="mb-3" id="alamatBox">
                            <label class="label">Alamat Pengiriman</label>

                            <textarea
                                name="alamat_kirim"
                                id="alamat_kirim"
                                class="form-control alamat-textarea"
                                placeholder="Masukkan alamat lengkap pengiriman"
                                required
                                oninput="hitungOngkirOtomatis()"><?= isset($pelanggan['alamat']) ? htmlspecialchars(trim($pelanggan['alamat'])) : ''; ?></textarea>

                            <small class="text-muted d-block mt-2">
                                Contoh: Pasar Kemis, Tangerang / Jakarta / Bekasi / Jawa Barat.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="label">Jasa Pengiriman</label>
                                <select name="jasa_kirim" id="jasa_kirim" class="form-select" required>
                                    <option value="JNE">JNE</option>
                                    <option value="J&T">J&T</option>
                                    <option value="SiCepat">SiCepat</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="label">Ongkir</label>
                                <input
                                    type="text"
                                    id="ongkirLabel"
                                    class="form-control"
                                    value="Rp <?= number_format($ongkirDefault, 0, ',', '.'); ?>"
                                    readonly>

                                <input
                                    type="hidden"
                                    name="ongkir"
                                    id="ongkir"
                                    value="<?= $ongkirDefault; ?>">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-checkout">
                    <div class="card-header-custom">
                        <h5>Metode Pembayaran</h5>
                    </div>

                    <div class="card-body-custom">

                        <div class="mb-3">
                            <label class="label">Pilih Metode Pembayaran</label>

                            <select name="metode" id="metode" class="form-select" required onchange="aturMetodeBayar()">
                                <option value="bca_transfer" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BCA
                                </option>

                                <option value="mandiri_transfer" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank MANDIRI
                                </option>

                                <option value="bni_transfer" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BNI
                                </option>

                                <option value="bri_transfer" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    Transfer Bank BRI
                                </option>

                                <option value="seabank_transfer" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                </option>

                                <option value="dana" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    DANA
                                </option>

                                <option value="ovo" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    OVO
                                </option>

                                <option value="gopay" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    GoPay
                                </option>

                                <option value="shopeepay" data-info="Transfer Bank BCA: 1234567890 a.n. The Four Label">
                                    ShopeePay
                                </option>

                                <option value="cash_toko" id="cashOption">
                                    Cash di Toko
                                </option>
                            </select>

                            <div id="paymentInfo" class="payment-box">
                                <span id="paymentText">
                                    Transfer Bank BCA: <b>1234567890</b> a.n. <b>The Four Label</b>
                                </span>
                            </div>

                            <div id="cashInfo" class="dp-box" style="display:none;">
                                Kamu memilih <b>Cash di Toko</b>. Pesanan dibayar langsung saat pengambilan barang di toko.
                            </div>
                        </div>

                        <div class="dp-box" id="dpInfo">
                            Untuk pembayaran transfer/e-wallet, setelah checkout kamu akan diarahkan ke halaman upload bukti pembayaran.
                            Minimal pembayaran adalah <b>DP 50%</b> dari total pesanan.
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-5">

                <div class="card-checkout">
                    <div class="card-header-custom">
                        <h5>Ringkasan Pesanan</h5>
                    </div>

                    <div class="card-body-custom">

                        <?php if ($persenDiskon > 0) { ?>
                            <div class="discount-badge">
                                Selamat! Kamu mendapat diskon 20% karena sudah <?= $jumlahTransaksi; ?> transaksi.
                            </div>
                        <?php } else { ?>
                            <div class="no-discount-badge">
                                Transaksi kamu: <?= $jumlahTransaksi; ?> / 5 untuk diskon 20%
                            </div>
                        <?php } ?>

                        <?php foreach ($items as $item) { ?>
                            <?php $gambar = tampilGambarProduk($item['gambar']); ?>

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
                                        Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?>
                                    </div>
                                </div>

                            </div>
                        <?php } ?>

                        <div class="summary-box mt-4">

                            <div class="summary-row">
                                <span>Subtotal</span>
                                <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong>
                            </div>

                            <div class="summary-row discount">
                                <span>Diskon <?= $persenDiskon; ?>%</span>
                                <strong>- Rp <?= number_format($diskon, 0, ',', '.'); ?></strong>
                            </div>

                            <div class="summary-row">
                                <span>Ongkir</span>
                                <strong id="ongkirText">Rp <?= number_format($ongkirDefault, 0, ',', '.'); ?></strong>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="total-row">
                                <span>Total</span>
                                <h3 id="totalText">
                                    Rp <?= number_format($totalAkhirDefault, 0, ',', '.'); ?>
                                </h3>
                            </div>

                            <div class="summary-row mt-3 mb-0" id="dpRow">
                                <span>Minimal DP 50%</span>
                                <strong id="dpText">
                                    Rp <?= number_format($dpDefault, 0, ',', '.'); ?>
                                </strong>
                            </div>

                        </div>

                        <input type="hidden" name="subtotal" value="<?= $total; ?>">
                        <input type="hidden" name="diskon" value="<?= $diskon; ?>">
                        <input type="hidden" name="total_setelah_diskon" value="<?= $totalSetelahDiskon; ?>">
                        <input type="hidden" name="total" id="totalInput" value="<?= $totalAkhirDefault; ?>">

                        <div class="row g-2 mt-4">
                            <div class="col-md-5">
                                <a href="cart.php" class="btn-back">
                                    Kembali
                                </a>
                            </div>

                            <div class="col-md-7">
                                <button type="submit" class="btn btn-lavender w-100">
                                    Checkout Sekarang
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<?php include "footer.php"; ?>

<script>
const totalSetelahDiskon = <?= $totalSetelahDiskon; ?>;

function formatRupiah(angka) {
    angka = parseInt(angka) || 0;
    return "Rp " + angka.toLocaleString("id-ID");
}

function tentukanOngkirDariAlamat(alamat) {
    alamat = alamat.toLowerCase();

    if (alamat.trim() === "") {
        return 5000;
    }

    if (
        alamat.includes("pasar kemis") ||
        alamat.includes("rajeg") ||
        alamat.includes("cikupa") ||
        alamat.includes("sepatan") ||
        alamat.includes("sindang jaya") ||
        alamat.includes("balaraja")
    ) {
        return 3000;
    }

    if (
        alamat.includes("tangerang") ||
        alamat.includes("kabupaten tangerang") ||
        alamat.includes("kota tangerang") ||
        alamat.includes("tangsel") ||
        alamat.includes("tangerang selatan")
    ) {
        return 5000;
    }

    if (
        alamat.includes("jakarta") ||
        alamat.includes("bogor") ||
        alamat.includes("depok") ||
        alamat.includes("bekasi")
    ) {
        return 8000;
    }

    if (
        alamat.includes("banten") ||
        alamat.includes("jawa barat") ||
        alamat.includes("jabar")
    ) {
        return 12000;
    }

    if (
        alamat.includes("jawa tengah") ||
        alamat.includes("jateng") ||
        alamat.includes("jawa timur") ||
        alamat.includes("jatim") ||
        alamat.includes("yogyakarta") ||
        alamat.includes("jogja")
    ) {
        return 15000;
    }

    return 20000;
}

function updateRingkasan(ongkir) {
    const totalAkhir = totalSetelahDiskon + ongkir;
    const dp = totalAkhir * 0.5;

    document.getElementById("ongkir").value = ongkir;
    document.getElementById("ongkirLabel").value = formatRupiah(ongkir);
    document.getElementById("ongkirText").innerText = formatRupiah(ongkir);
    document.getElementById("totalText").innerText = formatRupiah(totalAkhir);
    document.getElementById("dpText").innerText = formatRupiah(dp);
    document.getElementById("totalInput").value = totalAkhir;
}

function hitungOngkirOtomatis() {
    const tipe = document.getElementById("tipe_pengiriman").value;
    const alamat = document.getElementById("alamat_kirim").value;

    if (tipe === "ambil") {
        updateRingkasan(0);
        return;
    }

    const ongkir = tentukanOngkirDariAlamat(alamat);
    updateRingkasan(ongkir);
}

function isiJasaKirimDikirim() {
    const jasaKirim = document.getElementById("jasa_kirim");

    jasaKirim.innerHTML = `
        <option value="JNE">JNE</option>
        <option value="J&T">J&T</option>
        <option value="SiCepat">SiCepat</option>
    `;

    jasaKirim.removeAttribute("disabled");
}

function isiJasaKirimAmbil() {
    const jasaKirim = document.getElementById("jasa_kirim");

    jasaKirim.innerHTML = `
        <option value="Ambil di Tempat">Ambil di Tempat</option>
    `;

    jasaKirim.value = "Ambil di Tempat";
    jasaKirim.setAttribute("disabled", "disabled");
}

function aturPengiriman() {
    const tipe = document.getElementById("tipe_pengiriman").value;
    const alamatBox = document.getElementById("alamatBox");
    const alamatKirim = document.getElementById("alamat_kirim");
    const metode = document.getElementById("metode");
    const cashOption = document.getElementById("cashOption");

    if (tipe === "ambil") {
        alamatBox.style.display = "none";
        alamatKirim.removeAttribute("required");

        isiJasaKirimAmbil();

        cashOption.style.display = "block";
        metode.value = "cash_toko";

        updateRingkasan(0);
    } else {
        alamatBox.style.display = "block";
        alamatKirim.setAttribute("required", "required");

        isiJasaKirimDikirim();

        if (metode.value === "cash_toko") {
            metode.value = "bca_transfer";
        }

        cashOption.style.display = "none";

        hitungOngkirOtomatis();
    }

    aturMetodeBayar();
}

function aturMetodeBayar() {
    const metode = document.getElementById("metode");
    const selectedOption = metode.options[metode.selectedIndex];

    const paymentInfo = document.getElementById("paymentInfo");
    const paymentText = document.getElementById("paymentText");
    const cashInfo = document.getElementById("cashInfo");
    const dpInfo = document.getElementById("dpInfo");
    const dpRow = document.getElementById("dpRow");

    if (metode.value === "cash_toko") {
        paymentInfo.style.display = "none";
        cashInfo.style.display = "block";
        dpInfo.style.display = "none";
        dpRow.style.display = "none";
    } else {
        paymentInfo.style.display = "block";
        cashInfo.style.display = "none";
        dpInfo.style.display = "block";
        dpRow.style.display = "flex";

        const info = selectedOption.getAttribute("data-info");
        paymentText.innerHTML = info;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    aturPengiriman();

    const checkoutForm = document.getElementById("checkoutForm");

    checkoutForm.addEventListener("submit", function () {
        const tipe = document.getElementById("tipe_pengiriman").value;
        const jasa = document.getElementById("jasa_kirim");

        if (tipe === "ambil") {
            jasa.removeAttribute("disabled");
            jasa.value = "Ambil di Tempat";
        }
    });
});
</script>

</body>
</html>