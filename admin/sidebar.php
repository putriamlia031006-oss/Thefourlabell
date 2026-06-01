<style>

.sidebar{

    background:#9d7ad6;

    min-height:100vh;

    padding:30px 20px;

}

.sidebar-title{

    color:white;

    font-size:24px;

    font-weight:600;

    margin-bottom:35px;

    padding-bottom:15px;

    border-bottom:1px solid rgba(255,255,255,.3);

}

.sidebar a{

    display:block;

    color:white;

    text-decoration:none;

    padding:12px 15px;

    margin-bottom:10px;

    border-radius:10px;

    transition:.3s;

    font-size:15px;

}

.sidebar a:hover{

    background:rgba(255,255,255,.15);

}

.sidebar a.active{

    background:white;

    color:#7b5cb8;

    font-weight:600;

}

.logout{

    margin-top:40px;

    border-top:1px solid rgba(255,255,255,.3);

    padding-top:20px;

}

</style>

<div class="sidebar">

    <div class="sidebar-title">

        ADMIN PANEL

    </div>

    <a href="index.php" class="active">
        Dashboard
    </a>

    <a href="produk.php">
        Produk
    </a>

    <a href="pesanan.php">
        Pesanan
    </a>

    <a href="stok.php">
        Stok
    </a>

    <a href="laporan.php">
        Laporan
    </a>

    <div class="logout">

        <a href="../logout.php">
            Logout
        </a>

    </div>

</div>