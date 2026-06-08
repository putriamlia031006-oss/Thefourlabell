<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
.sidebar {
    background: linear-gradient(180deg, #b57edc, #9d7ad6, #7b5cb8);
    width: 240px;
    height: 100vh;
    padding: 24px 16px;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    box-shadow: 8px 0 28px rgba(157, 122, 214, 0.22);
    z-index: 1000;
}

/* BRAND */
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 8px 22px;
    margin-bottom: 18px;
    border-bottom: 1px solid rgba(255,255,255,.28);
}

.brand-logo {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: rgba(255,255,255,.20);
    border: 1px solid rgba(255,255,255,.24);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    box-shadow: 0 8px 18px rgba(80, 35, 120, .10);
    overflow: hidden;
    flex-shrink: 0;
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 12px;
}

.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
}

.brand-text strong {
    color: white;
    font-size: 15px;
    font-weight: 850;
    letter-spacing: .5px;
}

.brand-text span {
    color: rgba(255,255,255,.82);
    font-size: 11px;
    font-weight: 600;
    margin-top: 4px;
}

/* MENU */
.sidebar-section-label {
    color: rgba(255,255,255,.68);
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0 12px;
    margin: 18px 0 10px;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,.92);
    text-decoration: none;
    padding: 13px 14px;
    margin-bottom: 8px;
    border-radius: 15px;
    transition: .25s ease;
    font-size: 14px;
    font-weight: 650;
    position: relative;
}

.sidebar-menu a i {
    width: 22px;
    text-align: center;
    font-size: 16px;
    color: rgba(255,255,255,.88);
}

.sidebar-menu a:hover {
    background: rgba(255,255,255,.16);
    color: white;
    transform: translateX(4px);
}

.sidebar-menu a:hover i {
    color: white;
}

.sidebar-menu a.active {
    background: #fbf7ff;
    color: #7b3fb2;
    font-weight: 850;
    box-shadow: 0 10px 22px rgba(80, 35, 120, .14);
}

.sidebar-menu a.active i {
    color: #8e44ad;
}

.sidebar-menu a.active::before {
    content: "";
    width: 5px;
    height: 24px;
    border-radius: 999px;
    background: #d8b4fe;
    position: absolute;
    left: -6px;
    top: 50%;
    transform: translateY(-50%);
}

/* LOGOUT */
.logout {
    margin-top: 28px;
    border-top: 1px solid rgba(255,255,255,.28);
    padding-top: 18px;
}

.logout a {
    display: flex;
    align-items: center;
    gap: 12px;
    color: white;
    text-decoration: none;
    padding: 13px 14px;
    border-radius: 15px;
    transition: .25s ease;
    font-size: 14px;
    font-weight: 700;
    background: rgba(255,255,255,.13);
}

.logout a i {
    width: 22px;
    text-align: center;
    font-size: 16px;
}

.logout a:hover {
    background: #f4eaff;
    color: #8e44ad;
    transform: translateX(4px);
}

.logout a:hover i {
    color: #8e44ad;
}

/* SCROLLBAR */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,.38);
    border-radius: 999px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        min-height: auto;
        border-radius: 0 0 24px 24px;
    }

    .sidebar-menu a,
    .logout a {
        justify-content: flex-start;
    }

    .sidebar-brand {
        padding-top: 4px;
    }

    .brand-logo {
        width: 54px;
        height: 54px;
    }

    .brand-text strong {
        font-size: 14px;
    }

    .brand-text span {
        font-size: 10px;
    }
}
</style>

<div class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-logo">
            <img src="../assets/logo.png" alt="Logo The Four Label">
        </div>

        <div class="brand-text">
            <strong>ADMIN PANEL</strong>
            <span>The Four Label</span>
        </div>
    </div>

    <div class="sidebar-menu">

        <div class="sidebar-section-label">Main Menu</div>

        <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="produk.php" class="<?= $currentPage == 'produk.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-shirt"></i>
            <span>Produk</span>
        </a>

        <a href="pelanggan.php" class="<?= $currentPage == 'pelanggan.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            <span>Data Pelanggan</span>
        </a>

        <a href="pesanan.php" class="<?= $currentPage == 'pesanan.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-box-open"></i>
            <span>Pesanan</span>
        </a>

        <a href="stok.php" class="<?= $currentPage == 'stok.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-warehouse"></i>
            <span>Stok</span>
        </a>

        <a href="laporan.php" class="<?= $currentPage == 'laporan.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice"></i>
            <span>Laporan</span>
        </a>

    </div>

    <div class="logout">
        <a href="logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</div>