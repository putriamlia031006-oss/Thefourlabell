<?php
session_start();
require "koneksi.php";
include "navbar.php";

$total = 0;
$totalItem = 0;

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

<title>Keranjang - The Four Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background:
        radial-gradient(circle at top left, #f7edff 0%, transparent 36%),
        radial-gradient(circle at bottom right, #ead6ff 0%, transparent 34%),
        #fbf7ff;
    font-family: 'Segoe UI', Arial, sans-serif;
    color: #33223f;
}

.cart-section {
    padding: 55px 0 75px;
}

.page-header {
    background:
        linear-gradient(135deg, rgba(83, 35, 128, 0.78), rgba(181, 126, 220, 0.58)),
        url('assets/cart.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    padding: 70px 20px;
    text-align: center;
    border-radius: 0 0 34px 34px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(255,255,255,0.10);
    top: -80px;
    left: -80px;
}

.page-header::after {
    content: "";
    position: absolute;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    bottom: -130px;
    right: -100px;
}

.page-header-content {
    position: relative;
    z-index: 2;
}

.page-header h1 {
    font-size: 46px;
    font-weight: 850;
    margin-bottom: 10px;
}

.page-header p {
    margin: 0;
    font-size: 17px;
    opacity: 0.96;
}

.cart-card,
.summary-card {
    background: rgba(255,255,255,0.96);
    border: 1px solid #eadcff;
    border-radius: 28px;
    box-shadow: 0 16px 38px rgba(142, 68, 173, 0.13);
}

.cart-card {
    overflow: hidden;
}

.cart-card-header {
    padding: 24px 28px;
    border-bottom: 1px solid #eadcff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background:
        radial-gradient(circle at top right, rgba(185, 132, 224, 0.14), transparent 35%),
        #fff;
}

.cart-card-header h4 {
    margin: 0;
    color: #6f2da8;
    font-weight: 850;
}

.cart-count {
    background: #f1e3ff;
    color: #7b3fb2;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
}

.cart-body {
    padding: 0;
}

.cart-item {
    display: grid;
    grid-template-columns: 90px 1fr 160px 150px 50px;
    gap: 18px;
    align-items: center;
    padding: 22px 28px;
    border-bottom: 1px solid #f0e4fb;
}

.cart-item:last-child {
    border-bottom: none;
}

.product-img {
    width: 90px;
    height: 90px;
    border-radius: 20px;
    background: #f1e3ff;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8e44ad;
    font-weight: 800;
    font-size: 12px;
    text-align: center;
}

.product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info h5 {
    margin: 0 0 7px;
    font-size: 17px;
    font-weight: 850;
    color: #362447;
}

.product-info .price {
    color: #8e44ad;
    font-weight: 800;
    margin-bottom: 5px;
}

.product-info .small-note {
    color: #8a7899;
    font-size: 13px;
}

.qty-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.qty-btn {
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 11px;
    background: #ede1ff;
    color: #6f2da8;
    font-weight: 900;
    transition: 0.2s;
}

.qty-btn:hover {
    background: #d9c0f0;
}

.qty-input {
    width: 62px;
    height: 38px;
    text-align: center;
    border-radius: 12px;
    border: 1.5px solid #e2d8ea;
    font-weight: 750;
    color: #3f176b;
}

.subtotal {
    text-align: right;
}

.subtotal span {
    display: block;
    color: #8a7899;
    font-size: 13px;
    margin-bottom: 5px;
}

.subtotal strong {
    color: #4b2e63;
    font-size: 17px;
}

.btn-remove {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    background: #fff0f3;
    color: #dc3545;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: 0.2s;
}

.btn-remove:hover {
    background: #dc3545;
    color: white;
}

.summary-card {
    padding: 26px;
    position: sticky;
    top: 105px;
}

.summary-card h4 {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 22px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 14px;
    color: #5d4773;
    font-size: 15px;
}

.summary-row strong {
    color: #362447;
}

.summary-line {
    height: 1px;
    background: #eadcff;
    margin: 18px 0;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    align-items: center;
    margin-bottom: 22px;
}

.summary-total span {
    font-size: 17px;
    font-weight: 850;
    color: #362447;
}

.summary-total strong {
    font-size: 24px;
    color: #8e44ad;
}

.btn-checkout {
    width: 100%;
    border: none;
    border-radius: 17px;
    padding: 14px;
    font-weight: 850;
    color: white;
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    box-shadow: 0 14px 28px rgba(142, 67, 189, 0.26);
    transition: 0.25s;
    text-decoration: none;
    display: block;
    text-align: center;
}

.btn-checkout:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white;
    transform: translateY(-2px);
}

.btn-shop {
    width: 100%;
    border-radius: 17px;
    padding: 13px;
    font-weight: 800;
    color: #8e44ad;
    background: white;
    border: 1.5px solid #d9c0f0;
    transition: 0.25s;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 12px;
}

.btn-shop:hover {
    background: #f4eaff;
    color: #7b3fb2;
}

.empty-cart {
    text-align: center;
    padding: 70px 25px;
}

.empty-icon {
    width: 95px;
    height: 95px;
    background: #f1e3ff;
    color: #8e44ad;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin: 0 auto 18px;
}

.empty-cart h4 {
    color: #6f2da8;
    font-weight: 850;
    margin-bottom: 8px;
}

.empty-cart p {
    color: #8a7899;
    margin-bottom: 24px;
}

.empty-cart .btn-shop {
    max-width: 230px;
    margin: 0 auto;
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    border: none;
}

.empty-cart .btn-shop:hover {
    color: white;
}

@media (max-width: 991px) {
    .cart-item {
        grid-template-columns: 82px 1fr;
    }

    .qty-box,
    .subtotal,
    .btn-remove {
        grid-column: 2;
        justify-content: flex-start;
        text-align: left;
    }

    .btn-remove {
        width: fit-content;
        padding: 0 14px;
    }

    .summary-card {
        position: static;
        margin-top: 22px;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 55px 18px;
    }

    .page-header h1 {
        font-size: 34px;
    }

    .cart-card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .cart-item {
        padding: 20px;
        gap: 14px;
    }
}
</style>
</head>

<body>

<div class="page-header">
    <div class="page-header-content">
        <h1>Keranjang Belanja</h1>
        <p>Cek kembali produk pilihanmu sebelum lanjut checkout.</p>
    </div>
</div>

<div class="container cart-section">

    <?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) { ?>

        <div class="cart-card">
            <div class="empty-cart">
                <div class="empty-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <h4>Keranjang masih kosong</h4>
                <p>Yuk pilih produk The Four Label dulu sebelum checkout.</p>

                <a href="produk.php" class="btn-shop">
                    Lihat Produk
                </a>
            </div>
        </div>

    <?php } else { ?>

        <div class="row g-4">

            <div class="col-lg-8">

                <div class="cart-card">

                    <div class="cart-card-header">
                        <h4>Produk di Keranjang</h4>
                        <div class="cart-count">
                            <?= count($_SESSION['cart']); ?> Produk
                        </div>
                    </div>

                    <div class="cart-body">

                        <?php foreach ($_SESSION['cart'] as $index => $cart) { ?>

                            <?php
                            $idProduk = mysqli_real_escape_string($koneksi, $cart['idProduk']);

                            $query = mysqli_query(
                                $koneksi,
                                "SELECT * FROM produk WHERE idProduk='$idProduk'"
                            );

                            $data = mysqli_fetch_assoc($query);

                            if (!$data) continue;

                            $qty = (int) $cart['qty'];
                            $sub = $data['harga'] * $qty;
                            $total += $sub;
                            $totalItem += $qty;

                            $gambar = tampilGambarProduk($data['gambar']);
                            ?>

                            <div class="cart-item">

                                <div class="product-img">
                                    <?php if ($gambar != "") { ?>
                                        <img src="<?= htmlspecialchars($gambar); ?>" alt="<?= htmlspecialchars($data['namaProduk']); ?>">
                                    <?php } else { ?>
                                        No Image
                                    <?php } ?>
                                </div>

                                <div class="product-info">
                                    <h5><?= htmlspecialchars($data['namaProduk']); ?></h5>

                                    <div class="price">
                                        Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                                    </div>

                                    <div class="small-note">
                                        Ready Stock | Bisa checkout online
                                    </div>
                                </div>

                                <div class="qty-box">
                                    <button 
                                        type="button" 
                                        class="qty-btn"
                                        onclick="ubahQty(<?= $index; ?>, -1)">
                                        -
                                    </button>

                                    <input 
                                        type="number" 
                                        id="qty-<?= $index; ?>"
                                        value="<?= $qty; ?>" 
                                        min="1"
                                        class="qty-input"
                                        onchange="setQty(<?= $index; ?>, this.value)">

                                    <button 
                                        type="button" 
                                        class="qty-btn"
                                        onclick="ubahQty(<?= $index; ?>, 1)">
                                        +
                                    </button>
                                </div>

                                <div class="subtotal">
                                    <span>Subtotal</span>
                                    <strong>
                                        Rp <?= number_format($sub, 0, ',', '.'); ?>
                                    </strong>
                                </div>

                                <a 
                                    href="hapus-cart.php?index=<?= $index; ?>" 
                                    class="btn-remove"
                                    onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')"
                                    title="Hapus Produk">
                                    <i class="fa-solid fa-trash"></i>
                                </a>

                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="summary-card">

                    <h4>Ringkasan Belanja</h4>

                    <div class="summary-row">
                        <span>Total Produk</span>
                        <strong><?= count($_SESSION['cart']); ?> Produk</strong>
                    </div>

                    <div class="summary-row">
                        <span>Total Item</span>
                        <strong><?= $totalItem; ?> Item</strong>
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong>
                    </div>

                    <div class="summary-row">
                        <span>Ongkir</span>
                        <strong>Dihitung saat checkout</strong>
                    </div>

                    <div class="summary-line"></div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong>
                    </div>

                    <a href="checkout.php" class="btn-checkout">
                        Lanjut Checkout
                    </a>

                    <a href="produk.php" class="btn-shop">
                        Lanjut Belanja
                    </a>

                </div>

            </div>

        </div>

    <?php } ?>

</div>

<?php include "footer.php"; ?>

<script>
function ubahQty(index, perubahan) {
    let input = document.getElementById("qty-" + index);
    let qty = parseInt(input.value);

    if (isNaN(qty) || qty < 1) {
        qty = 1;
    }

    qty += perubahan;

    if (qty < 1) {
        qty = 1;
    }

    input.value = qty;

    window.location.href = "update-cart.php?index=" + index + "&qty=" + qty;
}

function setQty(index, qty) {
    qty = parseInt(qty);

    if (isNaN(qty) || qty < 1) {
        qty = 1;
    }

    window.location.href = "update-cart.php?index=" + index + "&qty=" + qty;
}
</script>

</body>
</html>