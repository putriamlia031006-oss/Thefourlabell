<?php
session_start();
require "../koneksi.php";

$query = mysqli_query(
    $koneksi,
    "SELECT 
        pesanan.*,
        user.nama AS namaPelanggan
    FROM pesanan
    JOIN pelanggan 
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user 
        ON pelanggan.idUser = user.idUser
    ORDER BY pesanan.idPesanan DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pesanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f0ff;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }

        .main-content {
            padding: 32px;
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, #b57edc, #8e44ad);
            color: white;
            padding: 28px;
            border-radius: 24px;
            margin-bottom: 28px;
            box-shadow: 0 12px 28px rgba(111, 66, 193, 0.20);
        }

        .page-header h3 {
            font-weight: 800;
            margin-bottom: 6px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.92;
        }

        .judul {
            color: #6f42c1;
            font-weight: 800;
        }

        .card-lavender {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(111, 66, 193, 0.14);
            transition: 0.3s;
            height: 100%;
            border: 1px solid #eadcff;
        }

        .card-lavender:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(111, 66, 193, 0.22);
        }

        .invoice-title {
            color: #6f42c1;
            font-weight: 800;
        }

        .badge-lavender {
            background: #eadcff;
            color: #4b2a7a;
            padding: 7px 12px;
            border-radius: 999px;
            font-weight: 700;
        }

        .btn-lavender {
            background: #7c4dff;
            color: white;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-lavender:hover {
            background: #5e35b1;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }

        .info-label {
            color: #888;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .info-value {
            font-weight: 700;
            color: #333;
        }

        .price {
            color: #7b3fb2;
            font-weight: 800;
        }

        .empty-box {
            background: white;
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 8px 22px rgba(111, 66, 193, 0.12);
            border: 1px solid #eadcff;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .page-header {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 p-0">
            <?php include "sidebar.php"; ?>
        </div>

        <!-- CONTENT -->
        <div class="col-md-10 main-content">

            <div class="page-header">
                <h3>💜 Daftar Pesanan</h3>
                <p>Kelola pesanan customer, update status, dan hapus pesanan jika diperlukan.</p>
            </div>

            <div class="row g-4">

                <?php if (mysqli_num_rows($query) == 0) { ?>

                    <div class="col-12">
                        <div class="empty-box">
                            <h5 class="judul">Belum ada pesanan</h5>
                            <p class="text-muted mb-0">Pesanan customer akan muncul di halaman ini.</p>
                        </div>
                    </div>

                <?php } ?>

                <?php while ($row = mysqli_fetch_assoc($query)) { ?>

                    <div class="col-md-6 col-lg-4">

                        <div class="card card-lavender p-3">

                            <h5 class="invoice-title mb-3">
                                <?= $row['nomorInvoice'] ? htmlspecialchars($row['nomorInvoice']) : "#" . $row['idPesanan']; ?>
                            </h5>

                            <div class="mb-2">
                                <div class="info-label">Customer</div>
                                <div class="info-value">
                                    <?= htmlspecialchars($row['namaPelanggan']); ?>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="info-label">Jenis Pesanan</div>
                                <div class="info-value">
                                    <?php
                                    if ($row['jenisPesanan'] == "siap_pakai") {
                                        echo "Siap Pakai";
                                    } elseif ($row['jenisPesanan'] == "custom") {
                                        echo "Custom";
                                    } else {
                                        echo htmlspecialchars($row['jenisPesanan']);
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="info-label">Tanggal Pesan</div>
                                <div class="info-value">
                                    <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="info-label">Deadline Selesai</div>
                                <div class="info-value">
                                    <?php if (!empty($row['deadlineSelesai']) && $row['deadlineSelesai'] != "0000-00-00") { ?>
                                        <?= date('d-m-Y', strtotime($row['deadlineSelesai'])); ?>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="info-label">Total</div>
                                <div class="price">
                                    Rp <?= number_format($row['total'], 0, ',', '.'); ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Status</div>
                                <span class="badge badge-lavender">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </div>

                            <div class="row g-2">

                                <div class="col-6">
                                    <a 
                                        href="update-status.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn btn-lavender btn-sm w-100">
                                        Update
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a 
                                        href="hapus-pesanan.php?id=<?= $row['idPesanan']; ?>"
                                        class="btn btn-delete btn-sm w-100"
                                        onclick="return confirm('Yakin ingin menghapus pesanan ini? Data pembayaran dan detail pesanan juga akan terhapus.')">
                                        Hapus
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>
</div>

</body>
</html>