<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$jumlahCart = 0;

if (isset($_SESSION['cart'])) {
    $jumlahCart = count($_SESSION['cart']);
}

?>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid px-4 px-lg-5">

        <a class="navbar-brand logo" href="index.php">
            <span class="logo-icon">T4L</span>
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
                    <a class="nav-link" href="index.php">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="produk.php">
                        Produk
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="custom-order.php">
                        Custom Order
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link cart-link" href="cart.php">
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
                        <a class="nav-link" href="pesanan-saya.php">
                            Pesanan Saya
                        </a>
                    </li>

                    <li class="nav-item dropdown ms-lg-2">

                        <a
                            class="nav-link akun-btn dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <?= isset($_SESSION['user']['nama']) ? htmlspecialchars($_SESSION['user']['nama']) : 'Akun'; ?>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end dropdown-custom">

                            <li>
                                <a class="dropdown-item" href="pesanan-saya.php">
                                    Pesanan Saya
                                </a>
                            </li>

                            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == "admin") { ?>
                                <li>
                                    <a class="dropdown-item" href="admin/index.php">
                                        Dashboard Admin
                                    </a>
                                </li>
                            <?php } ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    Logout
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
.navbar-custom {
    background: rgba(255, 255, 255, 0.90);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 4px 22px rgba(142, 68, 173, 0.10);
    padding: 13px 0;
    z-index: 999;
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

.navbar-nav .nav-link:hover {
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

.akun-btn {
    background: #f4eaff;
    color: #7b3fb2 !important;
    border-radius: 999px !important;
    padding: 10px 18px !important;
}

.dropdown-custom {
    border: none;
    border-radius: 18px;
    padding: 10px;
    box-shadow: 0 12px 35px rgba(142, 68, 173, 0.16);
    margin-top: 12px;
}

.dropdown-custom .dropdown-item {
    border-radius: 12px;
    padding: 10px 14px;
    font-weight: 600;
    color: #4d3b5f;
}

.dropdown-custom .dropdown-item:hover {
    background: #f4eaff;
    color: #7b3fb2;
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
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>