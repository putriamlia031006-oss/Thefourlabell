<?php
require "../koneksi.php";

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan ORDER BY idPesanan DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pesanan</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f0ff; /* lavender soft */
        }

        .judul {
            color: #6f42c1;
            font-weight: bold;
        }

        .card-lavender {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.15);
            transition: 0.3s;
        }

        .card-lavender:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(111, 66, 193, 0.25);
        }

        .badge-lavender {
            background: #d6b8ff;
            color: #4b2a7a;
        }

        .btn-lavender {
            background: #7c4dff;
            color: white;
            border-radius: 12px;
        }

        .btn-lavender:hover {
            background: #5e35b1;
            color: white;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <h3 class="text-center judul mb-4">💜 Daftar Pesanan</h3>

    <div class="row g-4">

        <?php while($row = mysqli_fetch_assoc($query)) { ?>

        <div class="col-md-4">

            <div class="card card-lavender p-3">

                <h5 class="mb-2">
                    #<?= $row['idPesanan']; ?>
                </h5>

                <p class="mb-1">
                    <strong>Jenis:</strong> <?= $row['jenisPesanan']; ?>
                </p>

                <p class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge badge-lavender">
                        <?= $row['status']; ?>
                    </span>
                </p>

                <a href="update-status.php?id=<?= $row['idPesanan']; ?>"
                   class="btn btn-lavender btn-sm w-100">
                    Update Status
                </a>

            </div>

        </div>

        <?php } ?>

    </div>
</div>

</body>
</html>