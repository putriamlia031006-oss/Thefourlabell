<?php
require "koneksi.php";

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pembayaran WHERE idPembayaran='$id'"
);

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran</title>

    <style>
        body {
            background: #f6f0ff;
            font-family: Arial, sans-serif;
        }

        .kwitansi {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.2);
            border-top: 8px solid #7c4dff;
        }

        h1 {
            text-align: center;
            color: #6f42c1;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .item {
            margin-bottom: 12px;
            font-size: 16px;
        }

        .label {
            font-weight: bold;
            color: #4b2a7a;
        }

        .value {
            color: #333;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 10px;
            background: #d6b8ff;
            color: #4b2a7a;
            font-size: 13px;
        }

        .btn {
            width: 100%;
            margin-top: 20px;
            padding: 10px;
            background: #7c4dff;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #5e35b1;
        }

        @media print {
            .btn {
                display: none;
            }

            body {
                background: white;
            }

            .kwitansi {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>

<div class="kwitansi">

    <h1>KWITANSI</h1>

    <div class="item">
        <span class="label">Jumlah:</span>
        <span class="value">Rp <?= number_format($data['jumlah'], 0, ',', '.'); ?></span>
    </div>

    <div class="item">
        <span class="label">Metode:</span>
        <span class="value"><?= $data['metode']; ?></span>
    </div>

    <div class="item">
        <span class="label">Status:</span>
        <span class="badge"><?= $data['status']; ?></span>
    </div>

    <button class="btn" onclick="window.print()">
        🖨 Cetak Kwitansi
    </button>

</div>

</body>
</html>     