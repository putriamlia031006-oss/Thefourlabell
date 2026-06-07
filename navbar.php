<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$jumlahCart = 0;

if (isset($_SESSION['cart'])) {
    $jumlahCart = count($_SESSION['cart']);
}

$currentPage = basename($_SERVER['PHP_SELF']);

?>
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid px-4 px-lg-5">

        <a class="navbar-brand logo" href="index.php">
            <span class="logo-icon">THE4L</span>
            <span>THE FOUR LABEL</span>
        </a>

        <button
            class="navbar-toggler custom-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu"
            aria-controls="menu"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 mt-3 mt-lg-0">

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'index.php' ? 'active-link' : ''; ?>" href="index.php">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'produk.php' ? 'active-link' : ''; ?>" href="produk.php">
                        Produk
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'custom-order.php' ? 'active-link' : ''; ?>" href="custom-order.php">
                        Custom Order
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link cart-link <?= $currentPage == 'cart.php' ? 'active-link' : ''; ?>" href="cart.php">
                        Keranjang

                        <?php if ($jumlahCart > 0) { ?>
                            <span class="badge-cart">
                                <?= $jumlahCart; ?>
                            </span>
                        <?php } ?>
                    </a>
                </li>

                <?php if (isset($_SESSION['user'])) { ?>

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'pesanan-saya.php' ? 'active-link' : ''; ?>" href="pesanan-saya.php">
                            Pesanan Saya
                        </a>
                    </li>

                  <li class="nav-item dropdown ms-lg-4 akun-wrapper">

                       <a
    class="nav-link akun-btn dropdown-toggle"
    href="javascript:void(0)"
    id="dropdownAkun"
    role="button">

    <i class="fas fa-user"></i>

    <span class="nama-user">
        <?= htmlspecialchars($_SESSION['user']['nama']); ?>
    </span>

</a>
 <ul class="dropdown-menu dropdown-menu-end dropdown-custom">

    <li>
        <a class="dropdown-item" href="pesanan-saya.php">
            <i class="fas fa-clipboard-list"></i>
            <span>Pesanan Saya</span>
        </a>
    </li>

    <li><hr class="dropdown-divider"></li>

    <li>
        <a class="dropdown-item text-danger" href="logout.php">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </li>

</ul>
                    </li>

                <?php } else { ?>

                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn-login" href="login.php">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link btn-register" href="register.php">
                            Daftar
                        </a>
                    </li>

                <?php } ?>

            </ul>

        </div>

    </div>
</nav>

<style>
.avatar-user{
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: white;
    color: #8e44ad;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-size: 14px;
    box-shadow: 0 3px 10px rgba(0,0,0,.1);
}

.nama-user{
    font-weight: 700;
}
.navbar-custom {
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 4px 22px rgba(142, 68, 173, 0.10);
    padding: 13px 0;
    position: sticky;
    top: 0;
    z-index: 999999 !important;
}

.navbar-custom,
.navbar-custom *{
    pointer-events: auto !important;
}

.navbar {
    overflow: visible !important;
}

.navbar-collapse {
    overflow: visible !important;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #6f2da8 !important;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-decoration: none;
}

.logo-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 900;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.22);
}

.navbar-nav .nav-link {
    color: #51425f !important;
    font-weight: 650;
    font-size: 15px;
    padding: 10px 14px !important;
    border-radius: 14px;
    transition: 0.25s ease;
}

.navbar-nav .nav-link:hover,
.navbar-nav .active-link {
    color: #7b3fb2 !important;
    background: #f4eaff;
}

.cart-link {
    position: relative;
}

.badge-cart {
    background: #8e44ad;
    color: white;
    border-radius: 999px;
    padding: 2px 7px;
    font-size: 11px;
    font-weight: 800;
    margin-left: 4px;
}

.btn-login {
    background: linear-gradient(135deg, #b57edc, #8e44ad);
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 999px !important;
    box-shadow: 0 8px 18px rgba(142, 68, 173, 0.20);
}

.btn-login:hover {
    background: linear-gradient(135deg, #a76bd4, #7b3fb2);
    color: white !important;
    transform: translateY(-1px);
}

.btn-register {
    border: 1px solid #cdb1ea;
    color: #8e44ad !important;
    padding: 10px 20px !important;
    border-radius: 999px !important;
    background: white;
}

.btn-register:hover {
    background: #f4eaff;
    color: #7b3fb2 !important;
}

.akun-wrapper{
    border-left: 1px solid #ddd;
    padding-left: 18px;
}

.navbar-nav .akun-btn{
    background: linear-gradient(135deg,#c084fc,#8b5cf6);
    color: white !important;
    border: none !important;
    border-radius: 14px !important;

    padding: 10px 16px !important;
    min-height: 52px;

    display: flex !important;
    align-items: center;
    gap: 10px;

    font-weight: 700;
    box-shadow: 0 6px 15px rgba(139,92,246,.2);
}

.akun-btn i{
    font-size: 20px;
    color: white;
}

.nama-user{
    font-size: 15px;
    font-weight: 700;
}
.akun-btn::after{
    margin-left: 8px;
    color: white;
}

.dropdown {
    position: relative;
}

.dropdown-menu{
    display:none;
}

.dropdown-menu.show{
    display:block;
}

.dropdown-custom{
    background: #fff;
    border: none;
    border-radius: 18px;
    min-width: 220px;
    padding: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,.12);
}

.dropdown-custom .dropdown-item{
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 12px 16px;
    border-radius: 12px;

    font-size: 15px;
    font-weight: 600;
    color: #4d3b5f;
}

.dropdown-custom .dropdown-item i{
    width: 20px;
    text-align: center;
}

.dropdown-custom .dropdown-item:hover{
    background: #f4eaff;
    color: #8e44ad;
}

.dropdown-divider{
    margin: 6px 0;
}

.custom-toggler {
    border: none;
    box-shadow: none !important;
    background: #f4eaff;
    border-radius: 12px;
    padding: 9px 11px;
}

.navbar-toggler-icon {
    width: 22px;
    height: 22px;
}

@media (max-width: 991px) {
    .navbar-custom {
        padding: 10px 0;
    }

    .navbar-nav {
        background: white;
        padding: 16px;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(142, 68, 173, 0.12);
    }

    .navbar-nav .nav-link {
        margin-bottom: 6px;
    }

    .btn-login,
    .btn-register,
    .akun-btn {
        text-align: center;
        margin-top: 6px;
    }

    .dropdown-menu {
        position: static !important;
        transform: none !important;
        box-shadow: none;
        margin-top: 8px;
        background: #fbf7ff;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const akunBtn = document.getElementById("dropdownAkun");

    if (akunBtn) {

        akunBtn.addEventListener("click", function(e){

            e.preventDefault();

            const menu = this.nextElementSibling;

            if(menu.style.display === "block"){
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
                menu.style.position = "absolute";
                menu.style.right = "0";
                menu.style.top = "100%";
                menu.style.zIndex = "999999";
            }

        });

        document.addEventListener("click", function(e){

            const menu = akunBtn.nextElementSibling;

            if(
                !akunBtn.contains(e.target) &&
                !menu.contains(e.target)
            ){
                menu.style.display = "none";
            }

        });

    }

});
</script>