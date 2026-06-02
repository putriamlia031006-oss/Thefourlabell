<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
.sidebar {
    background: linear-gradient(180deg, #9d7ad6, #7b5cb8);
    height: 100vh;
    padding: 28px 18px;
    position: sticky;
    top: 0;
    overflow-y: auto;
    box-shadow: 6px 0 22px rgba(111, 66, 193, 0.18);
}

.sidebar-title {
    color: white;
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 30px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255,255,255,.3);
    letter-spacing: .5px;
}

.sidebar-menu a {
    display: block;
    color: rgba(255,255,255,.92);
    text-decoration: none;
    padding: 13px 15px;
    margin-bottom: 10px;
    border-radius: 14px;
    transition: .25s ease;
    font-size: 15px;
    font-weight: 600;
}

.sidebar-menu a:hover {
    background: rgba(255,255,255,.16);
    color: white;
    transform: translateX(4px);
}

.sidebar-menu a.active {
    background: white;
    color: #7b5cb8;
    font-weight: 800;
    box-shadow: 0 8px 18px rgba(255,255,255,.16);
}

.logout {
    margin-top: 35px;
    border-top: 1px solid rgba(255,255,255,.3);
    padding-top: 20px;
}

.logout a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 13px 15px;
    border-radius: 14px;
    transition: .25s ease;
    font-size: 15px;
    font-weight: 600;
    background: rgba(255,255,255,.10);
}

.logout a:hover {
    background: rgba(255,255,255,.22);
    transform: translateX(4px);
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,.35);
    border-radius: 999px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

@media (max-width: 768px) {
    .sidebar {
        position: relative;
        height: auto;
        min-height: auto;
        border-radius: 0 0 22px 22px;
    }
}
</style>

<div class="sidebar">

    <div class="sidebar-title">
        ADMIN PANEL
    </div>

    <div class="sidebar-menu">

        <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : ''; ?>">
            🏠 Dashboard
        </a>

        <a href="produk.php" class="<?= $currentPage == 'produk.php' ? 'active' : ''; ?>">
            👕 Produk
        </a>

        <a href="pelanggan.php" class="<?= $currentPage == 'pelanggan.php' ? 'active' : ''; ?>">
            👥 Data Pelanggan
        </a>

        <a href="pesanan.php" class="<?= $currentPage == 'pesanan.php' ? 'active' : ''; ?>">
            📦 Pesanan
        </a>

        <a href="stok.php" class="<?= $currentPage == 'stok.php' ? 'active' : ''; ?>">
            🧵 Stok
        </a>

        <a href="laporan.php" class="<?= $currentPage == 'laporan.php' ? 'active' : ''; ?>">
            📊 Laporan
        </a>

    </div>

    <div class="logout">
        <a href="../logout.php">
            🚪 Logout
        </a>
    </div>

</div>