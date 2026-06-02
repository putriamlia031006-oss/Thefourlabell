<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Custom Order - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f8f4ff;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}

.page-wrapper {
    padding-bottom: 50px;
}

/* HERO */
.hero {
    background: linear-gradient(135deg, #a56de2, #c7a6f7);
    color: white;
    padding: 70px 20px 60px;
    text-align: center;
    border-radius: 0 0 32px 32px;
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    background: rgba(255,255,255,0.12);
    border-radius: 50%;
    top: -60px;
    left: -60px;
}

.hero::after {
    content: "";
    position: absolute;
    width: 280px;
    height: 280px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    bottom: -100px;
    right: -80px;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 999px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.28);
    font-size: 14px;
    margin-bottom: 16px;
    font-weight: 500;
}

.hero h1 {
    font-size: 52px;
    font-weight: 800;
    margin-bottom: 14px;
}

.hero p {
    font-size: 18px;
    margin: 0;
    opacity: 0.95;
}

/* CONTENT */
.content-section {
    margin-top: 35px;
}

.info-card,
.form-card {
    background: white;
    border: none;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(140, 100, 180, 0.12);
    height: 100%;
}

/* INFO CARD */
.info-card {
    padding: 28px 24px;
}

.info-title {
    color: #7e42bc;
    font-weight: 700;
    margin-bottom: 22px;
}

.step-item {
    display: flex;
    gap: 14px;
    margin-bottom: 18px;
    align-items: flex-start;
}

.step-number {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #efe4ff;
    color: #7e42bc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.step-item h6 {
    margin: 0 0 4px;
    font-weight: 600;
}

.step-item p {
    margin: 0;
    color: #777;
    font-size: 14px;
    line-height: 1.5;
}

/* FORM CARD */
.form-card {
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #b784ea, #8d59d9);
    color: white;
    padding: 24px 28px;
}

.form-header h4 {
    margin: 0 0 6px;
    font-weight: 700;
}

.form-header p {
    margin: 0;
    font-size: 14px;
    opacity: 0.95;
}

.form-body {
    padding: 28px;
}

.label-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #444;
}

.form-control,
.form-select {
    border-radius: 14px;
    padding: 12px 14px;
    border: 1px solid #ddd;
    background: #fcfbff;
}

.form-control:focus,
.form-select:focus {
    border-color: #b784ea;
    box-shadow: 0 0 0 4px rgba(183,132,234,0.18);
    background: white;
}

/* NOTE */
.note-box {
    background: #f6eeff;
    border: 1px solid #e4d2ff;
    color: #6e41a8;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 20px;
    font-size: 14px;
}

.deadline-preview {
    display: none;
    background: #fff8e7;
    border: 1px solid #ffe3a3;
    color: #7c5a14;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 20px;
    font-size: 14px;
}

/* UPLOAD */
.upload-box {
    border: 2px dashed #ceb0f6;
    background: #faf6ff;
    border-radius: 18px;
    padding: 22px;
    text-align: center;
}

.upload-box .icon {
    font-size: 34px;
    margin-bottom: 8px;
}

.upload-box p {
    margin: 8px 0 0;
    color: #777;
    font-size: 13px;
}

/* PREVIEW */
.preview-box {
    display: none;
    margin-top: 16px;
    padding: 16px;
    border-radius: 16px;
    background: #faf6ff;
    border: 1px solid #eadbff;
}

.preview-box h6 {
    color: #7e42bc;
    font-weight: 600;
    margin-bottom: 12px;
}

.preview-box img {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 14px;
    border: 2px solid #e3d1ff;
}

/* BUTTON */
.btn-lavender {
    background: linear-gradient(135deg, #b784ea, #8d59d9);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 13px;
    font-weight: 700;
    transition: 0.2s;
}

.btn-lavender:hover {
    background: linear-gradient(135deg, #a96ee5, #7d47cd);
    color: white;
    transform: translateY(-1px);
}

.btn-reset {
    background: white;
    color: #8d59d9;
    border: 1px solid #d8c0f7;
    border-radius: 14px;
    padding: 13px;
    font-weight: 600;
    transition: 0.2s;
}

.btn-reset:hover {
    background: #f7f0ff;
    color: #7d47cd;
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .hero h1 {
        font-size: 42px;
    }

    .info-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 576px) {
    .hero {
        padding: 55px 18px 48px;
    }

    .hero h1 {
        font-size: 34px;
    }

    .hero p {
        font-size: 16px;
    }

    .form-body {
        padding: 22px;
    }
}
</style>
</head>

<body>

<?php include "navbar.php"; ?>

<div class="page-wrapper">

    <!-- HERO -->
    <div class="hero">
        <div class="hero-content">
            <div class="hero-badge">✨ Custom Apparel by The Four Label</div>
            <h1>Custom Order</h1>
            <p>Buat hoodie, varsity, polo shirt, atau t-shirt sesuai desainmu sendiri.</p>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="container content-section">
        <div class="row g-4 align-items-start">

            <!-- INFO -->
            <div class="col-lg-4">
                <div class="info-card">
                    <h4 class="info-title">Cara Pesan Custom</h4>

                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h6>Pilih jenis pakaian</h6>
                            <p>Pilih produk custom seperti hoodie, varsity, polo shirt, atau t-shirt.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h6>Isi detail pesanan</h6>
                            <p>Masukkan ukuran, jumlah, alamat, pengiriman, dan catatan custom.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h6>Deadline otomatis</h6>
                            <p>Setiap 100 pcs membutuhkan waktu produksi 1 bulan.</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div>
                            <h6>Upload desain</h6>
                            <p>Upload gambar desain yang ingin dicetak atau dijadikan referensi.</p>
                        </div>
                    </div>

                    <div class="step-item mb-0">
                        <div class="step-number">5</div>
                        <div>
                            <h6>Tunggu konfirmasi</h6>
                            <p>Pesananmu akan diproses admin setelah detail custom diperiksa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="col-lg-8">
                <div class="form-card">

                    <div class="form-header">
                        <h4>Form Custom Order</h4>
                        <p>Isi form di bawah ini dengan lengkap ya.</p>
                    </div>

                    <div class="form-body">

                        <div class="note-box">
                            Deadline selesai dihitung otomatis berdasarkan jumlah pesanan:
                            <b>per 100 pcs = 1 bulan produksi</b>.
                        </div>

                        <div class="deadline-preview" id="deadlinePreview">
                            Estimasi deadline selesai:
                            <b id="deadlineText"></b>
                        </div>

                        <form action="proses-custom.php" method="POST" enctype="multipart/form-data">

                            <!-- Jenis -->
                            <div class="mb-3">
                                <label class="label-title">Jenis Pakaian</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="">Pilih Jenis Pakaian</option>
                                    <option value="Hoodie">Hoodie</option>
                                    <option value="Varsity">Varsity</option>
                                    <option value="Polo Shirt">Polo Shirt</option>
                                    <option value="T-Shirt">T-Shirt</option>
                                </select>
                            </div>

                            <!-- Ukuran & Qty -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="label-title">Ukuran</label>
                                    <select name="ukuran" class="form-select" required>
                                        <option value="">Pilih Ukuran</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="label-title">Jumlah</label>
                                    <input
                                        type="number"
                                        name="qty"
                                        id="qty"
                                        class="form-control"
                                        placeholder="Masukkan jumlah pesanan"
                                        min="1"
                                        required
                                        oninput="hitungDeadline()">
                                </div>
                            </div>

                            <!-- Alamat Pengiriman -->
                            <div class="mb-3">
                                <label class="label-title">Alamat Pengiriman</label>
                                <textarea
                                    name="alamat_kirim"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Masukkan alamat lengkap pengiriman"
                                    required></textarea>
                            </div>

                            <!-- Jasa Kirim & Ongkir -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="label-title">Jasa Kirim</label>
                                    <select name="jasa_kirim" id="jasa_kirim" class="form-select" required onchange="ubahOngkir()">
                                        <option value="">Pilih Jasa Kirim</option>
                                        <option value="JNE">JNE</option>
                                        <option value="J&T">J&T</option>
                                        <option value="SiCepat">SiCepat</option>
                                        <option value="Ambil di Tempat">Ambil di Tempat</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="label-title">Ongkir</label>
                                    <select name="ongkir" id="ongkir" class="form-select" required>
                                        <option value="">Pilih Ongkir</option>
                                        <option value="10000">Jabodetabek - Rp10.000</option>
                                        <option value="20000">Luar Kota - Rp20.000</option>
                                        <option value="30000">Luar Pulau - Rp30.000</option>
                                        <option value="0">Ambil di Tempat - Rp0</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="mb-3">
                                <label class="label-title">Catatan Custom</label>
                                <textarea
                                    name="catatan"
                                    rows="4"
                                    class="form-control"
                                    placeholder="Contoh: logo di dada kiri, warna hitam, tulisan di belakang, dll."></textarea>
                            </div>

                            <!-- Upload -->
                            <div class="mb-4">
                                <label class="label-title">Upload Desain</label>

                                <div class="upload-box">
                                    <div class="icon">🖼️</div>
                                    <strong>Upload file desain kamu</strong>
                                    <p>Format gambar: JPG, JPEG, PNG</p>

                                    <input
                                        type="file"
                                        name="desain"
                                        class="form-control mt-3"
                                        accept="image/*"
                                        id="previewInput">
                                </div>

                                <div class="preview-box" id="previewBox">
                                    <h6>Preview Desain</h6>
                                    <img id="previewImage" alt="Preview Desain">
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <button type="reset" class="btn btn-reset w-100" onclick="resetFormCustom()">
                                        Reset
                                    </button>
                                </div>

                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-lavender w-100">
                                        Pesan Sekarang
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
const input = document.getElementById("previewInput");
const previewBox = document.getElementById("previewBox");
const previewImage = document.getElementById("previewImage");

input.addEventListener("change", function () {
    const file = this.files[0];

    if (file) {
        previewImage.src = URL.createObjectURL(file);
        previewBox.style.display = "block";
    } else {
        previewImage.src = "";
        previewBox.style.display = "none";
    }
});

function resetFormCustom() {
    previewImage.src = "";
    previewBox.style.display = "none";

    document.getElementById("deadlinePreview").style.display = "none";
    document.getElementById("deadlineText").innerText = "";

    document.getElementById("ongkir").value = "";
}

function hitungDeadline() {
    const qty = parseInt(document.getElementById("qty").value);
    const deadlinePreview = document.getElementById("deadlinePreview");
    const deadlineText = document.getElementById("deadlineText");

    if (!qty || qty < 1) {
        deadlinePreview.style.display = "none";
        deadlineText.innerText = "";
        return;
    }

    const bulanDeadline = Math.ceil(qty / 100);

    let tanggal = new Date();
    tanggal.setMonth(tanggal.getMonth() + bulanDeadline);

    const tanggalFormat = tanggal.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });

    deadlineText.innerText = tanggalFormat + " (" + bulanDeadline + " bulan produksi)";
    deadlinePreview.style.display = "block";
}

function ubahOngkir() {
    const jasaKirim = document.getElementById("jasa_kirim").value;
    const ongkir = document.getElementById("ongkir");

    if (jasaKirim === "Ambil di Tempat") {
        ongkir.value = "0";
    }
}
</script>

</body>
</html>