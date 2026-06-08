<head>
    <meta charset="UTF-8">
    <title>The Four Label</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<!-- FOOTER -->
<footer class="footer">
    <div class="container">

        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <div class="footer-logo">
                    <div class="footer-logo-box">
                        <img src="assets/logoT4L.png" alt="Logo The Four Label">
                    </div>

                    <div>
                        <h4>The Four Label</h4>
                        <span>Stitched With Style</span>
                    </div>
                </div>

                <p>
                    The Four Label adalah layanan konveksi modern untuk kebutuhan fashion,
                    seragam, komunitas, organisasi, dan custom apparel dengan tampilan elegan.
                </p>

                <div class="social-links">
                    <a href="https://www.instagram.com/stu_cindy/" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://wa.me/6282119116190" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">Menu</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="produk.php">Produk</a></li>
                    <li><a href="custom-order.php">Custom Order</a></li>
                    <li><a href="pesanan-saya.php">Pesanan Saya</a></li>
                    <li><a href="cart.php">Keranjang</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Layanan</h5>
                <ul class="footer-links">
                    <li><a href="produk.php">Ready Stock</a></li>
                    <li><a href="custom-order.php">Custom Apparel</a></li>
                    <li><a href="custom-order.php">Seragam Komunitas</a></li>
                    <li><a href="custom-order.php">Hoodie & T-Shirt</a></li>
                    <li><a href="custom-order.php">Polo & Varsity</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-title">Kontak Kami</h5>

                <div class="footer-contact">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>
                        Jl. Siswaraya, Belendung No. 12, Tangerang, Banten
                    </span>
                </div>

                <div class="footer-contact">
                    <i class="fa-solid fa-phone"></i>
                    <span>+62 812-3456-7890</span>
                </div>

                <div class="footer-contact">
                    <i class="fa-solid fa-envelope"></i>
                    <span>thefourlabel@gmail.com</span>
                </div>

                <div class="footer-contact">
                    <i class="fa-solid fa-clock"></i>
                    <span>Senin - Sabtu, 08.00 - 17.00</span>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y'); ?> The Four Label. All Rights Reserved.
        </div>

    </div>
</footer>

<style>
.footer {
    background:
        radial-gradient(circle at top right, rgba(255,255,255,0.12), transparent 35%),
        linear-gradient(160deg, #4d1f7c, #6f2da8, #9b4ac0);
    color: white;
    padding: 50px 0 0;
    margin-top: 50px;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 22px;
}

.footer-logo-box {
    width: 92px;
    height: 92px;
    border-radius: 24px;
    background: rgba(255,255,255,0.95);
    border: 1.5px solid rgba(255,255,255,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    box-shadow: 0 12px 26px rgba(40, 13, 70, 0.18);
    overflow: hidden;
    flex-shrink: 0;
}

.footer-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 19px;
}

.footer-logo h4 {
    margin: 0;
    font-weight: 850;
    font-size: 28px;
    line-height: 1.1;
}

.footer-logo span {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #f0ddff;
    font-weight: 800;
}

.footer p {
    color: #f7efff;
    line-height: 1.8;
    font-size: 15px;
    margin-bottom: 0;
}

.footer-title {
    font-size: 20px;
    font-weight: 850;
    margin-bottom: 22px;
    color: white;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 15px;
}

.footer-links a {
    color: #f7efff;
    text-decoration: none;
    font-size: 15px;
    transition: 0.25s;
}

.footer-links a:hover {
    color: white;
    padding-left: 5px;
}

.footer-contact {
    display: flex;
    gap: 14px;
    margin-bottom: 18px;
    color: #f7efff;
    font-size: 15px;
    line-height: 1.6;
    align-items: flex-start;
}

.footer-contact i {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    font-size: 15px;
}

.social-links {
    display: flex;
    gap: 12px;
    margin-top: 26px;
}

.social-links a {
    width: 42px;
    height: 42px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.24);
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: 0.25s;
    font-size: 17px;
}

.social-links a:hover {
    background: white;
    color: #7b3fb2;
    transform: translateY(-3px);
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.18);
    margin-top: 45px;
    padding: 20px 0;
    text-align: center;
    color: #f7efff;
    font-size: 15px;
}

@media (max-width: 576px) {
    .footer {
        padding-top: 42px;
    }

    .footer-logo {
        align-items: flex-start;
    }

    .footer-logo h4 {
        font-size: 24px;
    }

    .footer-logo-box {
        width: 78px;
        height: 78px;
    }
}
</style>