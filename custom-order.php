<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include "navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Custom Order</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: #f4f6fb;
    font-family: 'Segoe UI', sans-serif;
}

/* HERO */
.hero{
    background: linear-gradient(135deg, #8b5cf6, #c4b5fd);
    color: white;
    padding: 70px 20px;
    text-align: center;
    border-radius: 0 0 40px 40px;
    margin-bottom: 40px;
}

.hero h1{
    font-weight: 700;
}

/* CARD FORM */
.card-form{
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

/* INPUT */
.form-control,
.form-select{
    border-radius: 12px;
    padding: 12px;
}

/* BUTTON */
.btn-lavender{
    background: #8b5cf6;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px;
    font-weight: 600;
}

.btn-lavender:hover{
    background: #7c3aed;
    color: white;
}

/* LABEL */
.label-title{
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

/* PREVIEW */
.preview-box{
    margin-top: 15px;
    display: none;
}

.preview-box img{
    width: 160px;
    height: 160px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #ddd;
}
</style>

</head>

<body>

<!-- HERO -->
<div class="hero">
    <h1>Custom Order</h1>
    <p>Upload desain dan buat pakaian sesuai kebutuhanmu</p>
</div>

<!-- FORM -->
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card card-form">
                <div class="card-body p-4 p-md-5">

                    <form action="proses-custom.php" method="POST" enctype="multipart/form-data">

                        <!-- Jenis -->
                        <div class="mb-3">
                            <label class="label-title">Jenis Pakaian</label>
                            <select name="jenis" class="form-select" required>
                                <option value="">Pilih Jenis</option>
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
                                <input type="text" name="ukuran" class="form-control" placeholder="S / M / L / XL" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="label-title">Jumlah</label>
                                <input type="number" name="qty" class="form-control" placeholder="Jumlah Pesanan" required>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="mb-3">
                            <label class="label-title">Catatan Custom</label>
                            <textarea name="catatan" rows="4" class="form-control" placeholder="Tuliskan detail custom..."></textarea>
                        </div>

                        <!-- Upload -->
                        <div class="mb-3">
                            <label class="label-title">Upload Desain</label>
                            <input type="file" name="desain" class="form-control" accept="desain/*" id="previewInput">

                            <div class="preview-box" id="previewBox">
                                <img id="previewImage">
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <button class="btn btn-lavender w-100">
                            Pesan Sekarang
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- PREVIEW SCRIPT -->
<script>
const input = document.getElementById("previewInput");

input.addEventListener("change", function () {
    const file = this.files[0];

    if (file) {
        document.getElementById("previewImage").src = URL.createObjectURL(file);
        document.getElementById("previewBox").style.display = "block";
    }
});
</script>

</body>
</html>