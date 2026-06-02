<?php
require "koneksi.php";

/* =========================
   AMBIL DATA PESANAN
========================= */
$idPesanan = $_GET['id'];

$q = mysqli_query(
    $koneksi,
    "SELECT total FROM pesanan WHERE idPesanan='$idPesanan'"
);

$data = mysqli_fetch_assoc($q);

$total = $data['total'];
$minDP = $total * 0.5;

/* =========================
   PROSES UPLOAD
========================= */
if (isset($_POST['jumlah'])) {

    $jumlah = $_POST['jumlah'];
    $metode = $_POST['metode'];

    /* VALIDASI DP */
    if ($jumlah < $minDP) {
        $error = "Minimal pembayaran adalah 50% (Rp " . number_format($minDP) . ")";
    } else {

        /* UPLOAD FILE */
        $file = "";

        if (!empty($_FILES['bukti']['name'])) {

            $file = time() . "_" . $_FILES['bukti']['name'];

            move_uploaded_file(
                $_FILES['bukti']['tmp_name'],
                "upload/" . $file
            );
        }

        /* SIMPAN PEMBAYARAN */
        mysqli_query(
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
                'Pending',
                '$file'
            )"
        );

        /* UPDATE STATUS PESANAN */
        mysqli_query(
            $koneksi,
            "UPDATE pesanan 
             SET status='Menunggu Verifikasi Pembayaran'
             WHERE idPesanan='$idPesanan'"
        );

        /* REDIRECT setelah sukses */
        header("Location: pesanan-saya.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Upload Pembayaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-family:'Segoe UI', sans-serif;
}

.card-box{
    max-width:500px;
    margin:60px auto;
    border:none;
    border-radius:18px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

.title{
    font-weight:700;
    color:#5b3cc4;
}

.btn-lavender{
    background:#8b5cf6;
    color:white;
    border:none;
    border-radius:10px;
    padding:10px;
    font-weight:600;
}

.btn-lavender:hover{
    background:#7c3aed;
    color:white;
}
</style>

</head>

<body>

<div class="card card-box">
<div class="card-body p-4">

<h4 class="title mb-3">Upload Pembayaran</h4>

<!-- ERROR -->
<?php if (!empty($error)) { ?>
    <div class="alert alert-danger text-center">
        <?= $error; ?>
    </div>
<?php } ?>

<!-- INFO DP -->
<div class="alert alert-info">
    Total Pesanan: <b>Rp <?= number_format($total); ?></b><br>
    Minimal DP (50%): <b>Rp <?= number_format($minDP); ?></b>
</div>

<!-- FORM -->
<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">Jumlah Transfer</label>
        <input type="number" name="jumlah" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Metode Pembayaran</label>
        <select name="metode" class="form-select" required>
            <option value="Transfer BCA">Transfer BCA</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Upload Bukti Transfer</label>
        <input type="file" name="bukti" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-lavender w-100">
        Upload Pembayaran
    </button>

</form>

</div>
</div>

</body>
</html>