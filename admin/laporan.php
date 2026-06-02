<?php
session_start();
require "../koneksi.php";

/* =========================
   LAPORAN STOK
========================= */
$stok = mysqli_query($koneksi, "
    SELECT 
        produk.idProduk,
        produk.namaProduk,
        produk.harga,
        kategori.namaKategori,
        stok_produk.jumlahStok,
        stok_produk.satuan
    FROM stok_produk
    JOIN produk 
        ON stok_produk.idProduk = produk.idProduk
    LEFT JOIN kategori
        ON produk.idKategori = kategori.idKategori
    ORDER BY produk.namaProduk ASC
");

if (!$stok) {
    die("Query stok error: " . mysqli_error($koneksi));
}

/* =========================
   LAPORAN PESANAN
========================= */
$pesanan = mysqli_query($koneksi, "
    SELECT 
        pesanan.idPesanan,
        pesanan.nomorInvoice,
        pesanan.tanggal,
        pesanan.status,
        pesanan.jenisPesanan,
        pesanan.total,
        pesanan.ongkir,
        pesanan.alamat_kirim,
        pesanan.jasa_kirim,
        user.nama AS namaPelanggan,
        user.email,
        pelanggan.noHp,
        pelanggan.alamat
    FROM pesanan
    JOIN pelanggan
        ON pesanan.idPelanggan = pelanggan.idPelanggan
    JOIN user
        ON pelanggan.idUser = user.idUser
    ORDER BY pesanan.idPesanan DESC
");

if (!$pesanan) {
    die("Query pesanan error: " . mysqli_error($koneksi));
}

/* =========================
   RINGKASAN DATA
========================= */
$qProduk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$totalProduk = mysqli_fetch_assoc($qProduk)['total'];

$qStok = mysqli_query($koneksi, "SELECT SUM(jumlahStok) AS total FROM stok_produk");
$totalStok = mysqli_fetch_assoc($qStok)['total'];
if ($totalStok == NULL) {
    $totalStok = 0;
}

$qPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$totalPesanan = mysqli_fetch_assoc($qPesanan)['total'];

$qPendapatan = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM pesanan");
$totalPendapatan = mysqli_fetch_assoc($qPendapatan)['total'];
if ($totalPendapatan == NULL) {
    $totalPendapatan = 0;
}

$qPembayaran = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM pembayaran");
$totalPembayaran = mysqli_fetch_assoc($qPembayaran)['total'];
if ($totalPembayaran == NULL) {
    $totalPembayaran = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sistem Konveksi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f3ff, #eadbff);
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }

        .container {
            max-width: 1250px;
        }

        .header-laporan {
            background: linear-gradient(135deg, #b57edc, #8e44ad);
            color: white;
            padding: 28px;
            border-radius: 24px;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(142, 68, 173, 0.25);
        }

        .logo-box {
            width: 65px;
            height: 65px;
            background: rgba(255,255,255,0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-cetak {
            background: white;
            color: #8e44ad;
            border: none;
            padding: 10px 18px;
            border-radius: 14px;
            font-weight: bold;
        }

        .btn-cetak:hover {
            background: #f3e8ff;
            color: #7b3fb2;
        }

        .card-ringkasan {
            background: white;
            border-radius: 20px;
            padding: 22px;
            border: 1px solid #eadcff;
            box-shadow: 0 8px 20px rgba(142, 68, 173, 0.12);
            height: 100%;
        }

        .card-ringkasan .icon {
            width: 46px;
            height: 46px;
            border-radius: 15px;
            background: #f1e3ff;
            color: #8e44ad;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .card-ringkasan p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        .card-ringkasan h3 {
            margin: 6px 0 0;
            color: #7b3fb2;
            font-weight: bold;
            font-size: 24px;
        }

        .box-laporan {
            background: white;
            border-radius: 22px;
            padding: 24px;
            margin-top: 26px;
            border: 1px solid #eadcff;
            box-shadow: 0 8px 20px rgba(142, 68, 173, 0.12);
        }

        .judul-section {
            color: #7b3fb2;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #b57edc;
            color: white;
            border: none;
            padding: 13px;
            text-align: center;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #f0e3ff;
        }

        .table tbody tr:hover {
            background: #fbf7ff;
        }

        .badge-status {
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .proses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .selesai {
            background: #dcfce7;
            color: #15803d;
        }

        .batal {
            background: #fee2e2;
            color: #b91c1c;
        }

        .custom {
            background: #f3e8ff;
            color: #7b3fb2;
        }

        .siap {
            background: #e0f2fe;
            color: #0369a1;
        }

        .kosong {
            text-align: center;
            color: #888;
            padding: 25px;
        }

        .text-small {
            font-size: 13px;
            color: #666;
        }

        @media print {
            body {
                background: white;
            }

            .btn-cetak {
                display: none;
            }

            .header-laporan,
            .card-ringkasan,
            .box-laporan {
                box-shadow: none;
            }

            .table thead th {
                background: #b57edc !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="header-laporan">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-box">T4L</div>
                <div>
                    <h2 class="mb-1 fw-bold">THE FOUR LABEL</h2>
                    <p class="mb-0">Laporan Sistem Konveksi</p>
                </div>
            </div>

            <button onclick="window.print()" class="btn-cetak">
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="row g-3">

        <div class="col-md-3 col-sm-6">
            <div class="card-ringkasan">
                <div class="icon">👕</div>
                <p>Total Produk</p>
                <h3><?= $totalProduk; ?></h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card-ringkasan">
                <div class="icon">📦</div>
                <p>Total Stok</p>
                <h3><?= $totalStok; ?></h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card-ringkasan">
                <div class="icon">🧾</div>
                <p>Total Pesanan</p>
                <h3><?= $totalPesanan; ?></h3>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card-ringkasan">
                <div class="icon">💰</div>
                <p>Total Pendapatan</p>
                <h3>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></h3>
            </div>
        </div>

    </div>

    <!-- LAPORAN STOK -->
    <div class="box-laporan">
        <h4 class="judul-section">📦 Laporan Stok Produk</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;

                    if (mysqli_num_rows($stok) > 0) {
                        while ($s = mysqli_fetch_assoc($stok)) { 
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($s['namaProduk']); ?></td>
                            <td><?= htmlspecialchars($s['namaKategori']); ?></td>
                            <td class="text-end">
                                Rp <?= number_format($s['harga'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center"><?= htmlspecialchars($s['jumlahStok']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($s['satuan']); ?></td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="6" class="kosong">
                                Belum ada data stok produk.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- LAPORAN PESANAN -->
    <div class="box-laporan">
        <h4 class="judul-section">🧾 Laporan Pesanan</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Kontak</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Pengiriman</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;

                    if (mysqli_num_rows($pesanan) > 0) {
                        while ($p = mysqli_fetch_assoc($pesanan)) { 

                            $status = strtolower($p['status']);

                            if ($status == "pending") {
                                $classStatus = "pending";
                            } elseif ($status == "proses" || $status == "diproses") {
                                $classStatus = "proses";
                            } elseif ($status == "selesai") {
                                $classStatus = "selesai";
                            } elseif ($status == "batal") {
                                $classStatus = "batal";
                            } else {
                                $classStatus = "pending";
                            }

                            if ($p['jenisPesanan'] == "custom") {
                                $classJenis = "custom";
                                $textJenis = "Custom";
                            } else {
                                $classJenis = "siap";
                                $textJenis = "Siap Pakai";
                            }
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>

                            <td class="text-center">
                                <?php if ($p['nomorInvoice'] != "") { ?>
                                    <?= htmlspecialchars($p['nomorInvoice']); ?>
                                <?php } else { ?>
                                    #<?= htmlspecialchars($p['idPesanan']); ?>
                                <?php } ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($p['namaPelanggan']); ?></strong>
                                <div class="text-small">
                                    <?= htmlspecialchars($p['email']); ?>
                                </div>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['noHp']); ?>
                                <div class="text-small">
                                    <?= htmlspecialchars($p['alamat']); ?>
                                </div>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($p['tanggal']); ?>
                            </td>

                            <td class="text-center">
                                <span class="badge-status <?= $classJenis; ?>">
                                    <?= $textJenis; ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-status <?= $classStatus; ?>">
                                    <?= htmlspecialchars($p['status']); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($p['jasa_kirim'] != "") { ?>
                                    <strong><?= htmlspecialchars($p['jasa_kirim']); ?></strong>
                                <?php } else { ?>
                                    <strong>Ambil di tempat</strong>
                                <?php } ?>

                                <div class="text-small">
                                    <?= htmlspecialchars($p['alamat_kirim']); ?>
                                </div>

                                <div class="text-small">
                                    Ongkir: Rp <?= number_format($p['ongkir'], 0, ',', '.'); ?>
                                </div>
                            </td>

                            <td class="text-end">
                                <strong>
                                    Rp <?= number_format($p['total'], 0, ',', '.'); ?>
                                </strong>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="9" class="kosong">
                                Belum ada data pesanan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>