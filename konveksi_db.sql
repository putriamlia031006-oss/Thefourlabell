-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 03:53 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `konveksi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idAdmin` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idAdmin`, `idUser`, `jabatan`) VALUES
(1, 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `detail_custom`
--

CREATE TABLE `detail_custom` (
  `idCustom` int(11) NOT NULL,
  `idPesanan` int(11) NOT NULL,
  `jenis` varchar(100) NOT NULL,
  `ukuran` varchar(20) NOT NULL,
  `qty` int(11) NOT NULL,
  `catatan` text,
  `desain` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `detail_custom`
--

INSERT INTO `detail_custom` (`idCustom`, `idPesanan`, `jenis`, `ukuran`, `qty`, `catatan`, `desain`) VALUES
(2, 20, 'T-Shirt', 'L', 10, 'tulisan dibelakang', '1780388508_1780334455_ej6P2o1HHVCeuhR5ovDJ.jpg'),
(3, 21, 'Varsity', 'M', 100, 'logo dilengan', '1780389012_1780334455_ej6P2o1HHVCeuhR5ovDJ.jpg'),
(4, 25, 'Long Sleeve T-Shirt', 'M', 3, 'nama dikerah', '1780969973_1780915515_panjang1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `idDetail` int(11) NOT NULL,
  `idPesanan` int(11) DEFAULT NULL,
  `idProduk` int(11) DEFAULT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `ukuran` varchar(20) DEFAULT NULL,
  `desain` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `customText` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`idDetail`, `idPesanan`, `idProduk`, `jenis`, `ukuran`, `desain`, `qty`, `customText`) VALUES
(2, 2, 4, NULL, NULL, NULL, 1, NULL),
(3, 3, 4, NULL, NULL, NULL, 1, NULL),
(4, 4, NULL, 'Varsity', 'xl', '1780334455_ej6P2o1HHVCeuhR5ovDJ.jpg', 220, 'bagus'),
(5, 5, NULL, 'hoodie', 'xl', '1780334892_lavender.jpeg', 220, 'apapun'),
(6, 6, NULL, 'hoodie', 'xl', '1780334982_lavender.jpeg', 220, 'apapun'),
(7, 7, NULL, 'hoodie', 'xl', '1780335039_lavender.jpeg', 220, 'apapun'),
(8, 8, NULL, 'hoodie', 'xl', '1780335124_lavender.jpeg', 220, 'apapun'),
(9, 9, NULL, 'hoodie', 'xl', '1780335152_lavender.jpeg', 220, 'apapun'),
(10, 10, NULL, 'polo', 'l', '1780335391_lavender2.jpg', 1, 'aege'),
(11, 11, NULL, 'polo', 'l', '1780335556_lavender2.jpg', 1, 'aege'),
(12, 12, NULL, 'Varsity', 'n', '1780356143_JTHtz8t98ElnrXBM8PIB.jpg', 4, 'apapun'),
(13, 13, NULL, 'polo', 's', '1780356295_qBaC8k8RJmK0z0QjTFaH.jpg', 2, 'alsd'),
(14, 14, NULL, 'Polo Shirt', 's', '1780356580_qBaC8k8RJmK0z0QjTFaH.jpg', 2, 'alsd'),
(15, 15, 4, NULL, NULL, NULL, 1, NULL),
(16, 16, 4, NULL, NULL, NULL, 2, NULL),
(17, 22, 6, NULL, NULL, NULL, 1, NULL),
(18, 23, 5, NULL, NULL, NULL, 1, NULL),
(19, 24, 7, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `idKategori` int(11) NOT NULL,
  `namaKategori` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`idKategori`, `namaKategori`) VALUES
(1, 'T-Shirt'),
(2, 'Hoodie'),
(3, 'Varsity'),
(4, 'Long Sleeve T-shirt'),
(5, 'Kemeja');

-- --------------------------------------------------------

--
-- Table structure for table `kwitansi`
--

CREATE TABLE `kwitansi` (
  `idKwitansi` int(11) NOT NULL,
  `idPembayaran` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `idPelanggan` int(11) NOT NULL,
  `idUser` int(11) DEFAULT NULL,
  `noHp` varchar(20) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`idPelanggan`, `idUser`, `noHp`, `alamat`) VALUES
(1, 2, '081211437354', '\r\n            curug'),
(2, 4, '081234567890', '\r\n        Tangerang    '),
(3, 5, '089123456789', 'Tangerang'),
(4, 13, '08723824949', 'tangerang');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `idPembayaran` int(11) NOT NULL,
  `idPesanan` int(11) DEFAULT NULL,
  `jumlah` double DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `dp` double DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`idPembayaran`, `idPesanan`, `jumlah`, `metode`, `status`, `bukti`, `dp`) VALUES
(1, 3, 125000, 'Transfer', 'DP Masuk', NULL, 125000),
(2, 3, 150000, 'Transfer BCA', 'Pending', '1780333313_kanja.jpg', 0),
(3, 3, 150000, 'Transfer BCA', 'Pending', '1780333472_kanja.jpg', 0),
(4, 2, 250000, 'Transfer BCA', 'Pending', '1780334218_bahanl.jpg', 0),
(5, 4, 0, 'Transfer BCA', 'DP Masuk', NULL, 0),
(6, 5, 0, 'Transfer BCA', 'DP Masuk', NULL, 0),
(7, 6, 75000, 'Transfer BCA', 'DP Masuk', NULL, 75000),
(8, 7, 75000, 'Transfer BCA', 'DP Masuk', NULL, 75000),
(9, 8, 0, 'Transfer BCA', 'DP Masuk', NULL, 0),
(10, 9, 16500000, 'Transfer BCA', 'DP Masuk', NULL, 16500000),
(11, 9, 16500000, 'Transfer BCA', 'Pending', '1780335176_lavender.jpeg', 0),
(12, 10, 87000, 'Transfer BCA', 'DP Masuk', NULL, 87000),
(13, 11, 87000, 'Transfer BCA', 'DP Masuk', NULL, 87000),
(14, 11, 87000, 'Transfer BCA', 'Menunggu Verifikasi', '1780335573_lavender.jpeg', 87000),
(15, 12, 500000, 'Transfer BCA', 'DP', NULL, 0),
(16, 13, 0, 'Transfer BCA', 'DP', NULL, 0),
(17, 14, 175000, 'Transfer BCA', 'DP - Menunggu Verifikasi', '1780356590_lavender.jpeg', 0),
(18, 14, 175000, 'Transfer BCA', 'Pending', '1780356604_kanja.jpg', 0),
(19, 15, 125000, 'Transfer BCA', 'DP Masuk', NULL, 125000),
(20, 15, 125000, 'Transfer BCA', 'Pending', '1780375207_1780334455_ej6P2o1HHVCeuhR5ovDJ.jpg', 0),
(21, 16, 200000, 'Transfer BCA', 'DP Masuk', NULL, 200000),
(22, 16, 200000, 'Tunai', 'Tunai', '', 0),
(23, 12, 500000, 'Transfer BCA', 'Pending', '1780378706_1780335124_lavender.jpeg', 0),
(26, 20, 400000, 'Transfer BCA', 'Pending', '1780388524_1780335152_lavender.jpeg', 0),
(27, 20, 400000, 'Transfer BCA', 'Pending', '1780388553_1780334455_ej6P2o1HHVCeuhR5ovDJ.jpg', 0),
(28, 21, 10010000, 'Transfer BCA', 'Pending', '1780389036_1780335152_lavender.jpeg', 0),
(29, 22, 87000, 'Transfer Bank BCA', 'Pending', '1780968595_WhatsApp_Image_2026_06_09_at_08.14.55.jpeg', 87000),
(30, 23, 97000, 'Transfer Bank BCA', 'DP Masuk', '1780968739_WhatsApp_Image_2026_06_09_at_08.14.55.jpeg', 97000),
(31, 24, 102000, 'Transfer Bank BCA', 'Lunas', '1780969423_WhatsApp_Image_2026_06_09_at_08.14.55.jpeg', 102000),
(32, 24, 102000, 'Transfer BCA', 'DP Masuk', '1780969452_WhatsApp Image 2026-06-09 at 08.14.55.jpeg', 0),
(33, 25, 155000, 'Transfer Bank BCA', 'Pending', '1780969986_WhatsApp_Image_2026_06_09_at_08.14.55.jpeg', 155000);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `idPesanan` int(11) NOT NULL,
  `idPelanggan` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `deadlineSelesai` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `jenisPesanan` enum('siap_pakai','custom') DEFAULT NULL,
  `total` double DEFAULT NULL,
  `nomorInvoice` varchar(100) DEFAULT NULL,
  `ongkir` double DEFAULT '0',
  `alamat_kirim` text,
  `jasa_kirim` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idPesanan`, `idPelanggan`, `tanggal`, `deadlineSelesai`, `status`, `jenisPesanan`, `total`, `nomorInvoice`, `ongkir`, `alamat_kirim`, `jasa_kirim`) VALUES
(2, 1, '2026-06-01', NULL, 'Lunas', 'siap_pakai', 250000, 'INV-20260601-2', 0, NULL, NULL),
(3, 1, '2026-06-02', NULL, 'Diproses', 'siap_pakai', 250000, 'INV-20260601-3', 0, NULL, NULL),
(4, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 0, 'INV-20260601-4', 0, NULL, NULL),
(5, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 0, 'INV-20260601-5', 0, NULL, NULL),
(6, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 150000, 'INV-20260601-6', 0, NULL, NULL),
(7, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 150000, 'INV-20260601-7', 0, NULL, NULL),
(8, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 0, 'INV-20260601-8', 0, NULL, NULL),
(9, 1, '2026-06-02', NULL, 'Lunas', 'custom', 33000000, 'INV-20260601-9', 0, NULL, NULL),
(10, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 174000, 'INV-20260601-10', 0, NULL, NULL),
(11, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 174000, 'INV-20260601-11', 0, NULL, NULL),
(12, 1, '2026-06-02', NULL, 'Lunas', 'custom', 1000000, 'INV-20260602-12', 0, NULL, NULL),
(13, 1, '2026-06-02', NULL, 'Menunggu', 'custom', 0, 'INV-20260602-13', 0, NULL, NULL),
(14, 1, '2026-06-02', NULL, 'Lunas', 'custom', 350000, 'INV-20260602-14', 0, NULL, NULL),
(15, 1, '2026-06-02', NULL, 'Menunggu Verifikasi Pembayaran', 'siap_pakai', 250000, 'INV-20260602-15', 0, NULL, NULL),
(16, 1, '2026-06-02', NULL, 'Menunggu Pembayaran Tunai', 'siap_pakai', 400000, 'INV-20260602-16', 0, NULL, NULL),
(20, 3, '2026-06-02', '2026-07-02', 'Lunas', 'custom', 800000, 'INV-CUS-20260602-20', 0, NULL, NULL),
(21, 3, '2026-06-02', '2026-07-02', 'Menunggu Verifikasi Pembayaran', 'custom', 20020000, 'INV-CUS-20260602-21', 20000, 'Tangerang', 'JNE'),
(22, 4, '2026-06-09', NULL, 'Menunggu Verifikasi Pembayaran', 'siap_pakai', 174000, 'INV-20260609-22', 0, NULL, NULL),
(23, 4, '2026-06-09', NULL, 'Diproses', 'siap_pakai', 194000, 'INV-20260609-23', 0, NULL, NULL),
(24, 4, '2026-06-09', NULL, 'Selesai', 'siap_pakai', 204000, 'INV-20260609-24', 5000, 'tangerang', 'JNE'),
(25, 4, '2026-06-09', '2026-07-09', 'Menunggu Verifikasi Pembayaran', 'custom', 310000, 'INV-CUS-20260609-25', 10000, 'periuk, tangerang', 'J&T');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idProduk` int(11) NOT NULL,
  `namaProduk` varchar(100) DEFAULT NULL,
  `harga` double DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `idKategori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idProduk`, `namaProduk`, `harga`, `gambar`, `deskripsi`, `idKategori`) VALUES
(4, 'Jacket Classic Blue White', 250000, '1780324515_JTHtz8t98ElnrXBM8PIB.jpg', 'Varsity Jacket Classic Blue White hadir dengan desain sporty dan timeless yang cocok untuk berbagai aktivitas. Kombinasi warna biru dan putih memberikan tampilan yang elegan sekaligus trendi. Dilengkapi detail bordir huruf pada bagian dada dan lengan yang menambah kesan premium. Terbuat dari bahan berkualitas yang nyaman digunakan, hangat, dan tahan lama. Cocok dipadukan dengan kaos, hoodie, maupun kemeja untuk gaya kasual sehari-hari.', 3),
(5, 'Polka White Premium', 189000, '1780913066_hoodie1.jpeg', 'Hoodie warna putih dengan desain minimalis dan aksen motif polkadot pada bagian hoodie, lengan, dan pinggang. Terbuat dari bahan fleece premium yang lembut, hangat, dan nyaman digunakan sehari-hari. Cocok untuk gaya kasual maupun semi-formal.', 2),
(6, 'Zip Pastel Clip Kids', 169000, '1780913125_hoodie2.jpeg', 'Hoodie anak model resleting dengan warna abu-abu dan kombinasi aksen pastel yang unik. Dilengkapi hoodie nyaman dan bahan yang lembut sehingga cocok digunakan untuk aktivitas sehari-hari. Desain lucu dan modern membuat tampilan anak lebih stylish.', 2),
(7, 'Brooklyn Navy Oversize', 199000, '1780913190_hoodie3.jpeg', 'Hoodie oversize warna navy dengan sablon tulisan \"Brooklyn New York\" pada bagian depan. Menggunakan bahan fleece berkualitas yang tebal, hangat, dan nyaman dipakai. Cocok untuk pria maupun wanita yang menyukai gaya streetwear dan kasual.', 2),
(8, 'Jacket Heritage Burgundy', 279000, '1780914315_varsity3.jpeg', 'Varsity jacket warna burgundy dengan kombinasi lengan krem dan detail bordir premium. Dilengkapi kancing depan, kantong samping, serta rib striped pada kerah, manset, dan pinggang. Cocok digunakan untuk melengkapi gaya casual maupun streetwear.', 3),
(9, 'Jacket Authentic Green', 289000, '1780914387_varsity2.jpeg', 'Varsity jacket premium warna hijau dengan detail patch bordir dan kombinasi warna yang elegan. Menggunakan bahan berkualitas yang nyaman, hangat, dan tahan lama. Desain klasik ala kampus Amerika menjadikan jaket ini cocok untuk berbagai aktivitas dan gaya berpakaian modern.', 3),
(10, 'Oversize AW Spiky Head', 89000, '1780914458_t-shirt3.jpeg', 'Kaos oversize dengan desain sporty dan kombinasi warna merah, hitam, dan krem yang menarik. Dilengkapi print logo pada bagian depan serta potongan longgar yang nyaman digunakan sehari-hari. Cocok untuk gaya streetwear dan casual modern.', 1),
(11, 'Vintage Classic Car', 95000, '1780914525_t-shirt2.jpeg', 'Kaos oversize warna putih dengan desain mobil klasik bergaya vintage. Menggunakan bahan cotton combed yang lembut, adem, dan nyaman dipakai sepanjang hari. Cocok dipadukan dengan jeans maupun celana cargo untuk tampilan kasual yang trendi.', 1),
(12, 'Vintage Flower Cow', 99000, '1780914573_t-shirt1.jpeg', 'Kaos oversize washed warna hijau dengan ilustrasi unik berbagai karakter sapi. Desain playful dan estetik membuat kaos ini cocok untuk pecinta fashion vintage dan casual. Terbuat dari bahan premium yang nyaman dan menyerap keringat.', 1),
(13, 'Polo Shirt Stripe Classic', 149000, '1780915388_panjang3.jpeg', 'Polo shirt lengan panjang dengan motif garis horizontal warna navy, merah, dan putih. Memiliki kerah klasik yang memberikan kesan rapi namun tetap santai. Terbuat dari bahan katun premium yang nyaman, adem, dan cocok digunakan untuk aktivitas sehari-hari maupun acara kasual.', 4),
(14, 'Long Sleeve Layered Tee Black', 119000, '1780915447_panjang2.jpeg', 'Kaos lengan panjang model layered dengan kombinasi warna hitam dan putih yang modern. Desain minimalis dan potongan loose fit memberikan tampilan streetwear yang stylish. Cocok dipadukan dengan jeans, cargo, maupun jogger.', 4),
(15, 'Knit Long Sleeve Beige', 139000, '1780915515_panjang1.jpeg', 'Atasan lengan panjang berwarna beige dengan kerah cokelat kontras yang elegan. Menggunakan bahan knit premium yang lembut dan nyaman digunakan. Cocok untuk tampilan casual chic maupun semi formal.', 4),
(16, 'Stripe Tie Casual', 159000, '1780915577_kemeja3.jpeg', 'Kemeja lengan pendek motif garis vertikal dengan tambahan aksesoris dasi yang memberikan kesan unik dan fashionable. Menggunakan bahan ringan dan nyaman sehingga cocok digunakan untuk hangout, kuliah, maupun acara santai.', 5),
(17, 'Oversize Stripe Layer', 169000, '1780915632_kemeja2.jpeg', 'Kemeja oversize motif garis vertikal dengan detail sweater layer di bagian bahu yang menciptakan tampilan preppy dan modern. Potongan longgar membuatnya nyaman digunakan sepanjang hari dan mudah dipadukan dengan berbagai outfit.', 5),
(18, 'Peter Pan Collar White', 2000000, '1780915671_kemeja1.jpeg', 'Kemeja putih lengan panjang dengan kerah peter pan beraksen bordir merah yang manis dan elegan. Terbuat dari bahan katun yang lembut dan nyaman dipakai. Cocok untuk tampilan feminin, formal, maupun semi kasual.', 5);

-- --------------------------------------------------------

--
-- Table structure for table `stok_produk`
--

CREATE TABLE `stok_produk` (
  `idStok` int(11) NOT NULL,
  `idProduk` int(11) DEFAULT NULL,
  `jumlahStok` int(11) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stok_produk`
--

INSERT INTO `stok_produk` (`idStok`, `idProduk`, `jumlahStok`, `satuan`) VALUES
(4, 4, 137, 'pcs'),
(5, 5, 107, 'pcs'),
(6, 6, 99, 'pcs'),
(7, 7, 19, 'pcs'),
(8, 8, 18, 'pcs'),
(9, 9, 15, 'pcs'),
(10, 10, 40, 'pcs'),
(11, 11, 35, 'pcs'),
(12, 12, 12, 'pcs'),
(13, 13, 25, 'pcs'),
(14, 14, 30, 'pcs'),
(15, 15, 22, 'pcs'),
(16, 16, 20, 'pcs'),
(17, 17, 71, 'pcs'),
(18, 18, 99, 'pcs');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `idUser` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','pelanggan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`idUser`, `nama`, `email`, `password`, `role`) VALUES
(1, 'putri amalia ramadani', '1224160102@global.ac.id', '8b6d9f5dd2385331a05b2e2d8a94f5a0', 'admin'),
(2, 'Putri Amalia Ramadani', 'putri.amlia031006@gmail.com', '$2y$10$/FNE0glxsBMkThiEJkUZSuDItebn6grVXxBlRjmT/Fz67qKjCl6Q2', 'pelanggan'),
(3, 'putri', 'mizum1265@gmail.com', '$2y$10$tIlyi4L2GKzNV0GmTJ.nye1r9UOLrT9/.nUnuwuf/Xp15.Z0GRjDS', 'admin'),
(4, 'khanza afifah karina putri', 'khanza@gmail.com', '$2y$10$uq.5PcBJ/m91uiwg1vF3AeXpWxHflNwPGoazjd7rT1f.r.cgPqZFS', 'pelanggan'),
(5, 'putri sofiatun muzofar', 'sofi@gmail.com', '$2y$10$ZZSAUy.1c0ZDMZDOFup98.HGAEC3LSmzXvtZ0NHyqchtvd7Jzixrq', 'pelanggan'),
(7, 'Cindy Setio', 'cindi@gmail.com', '6ea31ff746dacf297e333900384cd19e', 'pelanggan'),
(10, 'Admin The Four Label', 'admin@t4l.com', '0192023a7bbd73250516f069df18b500', 'admin'),
(11, 'Cindi Setio Rhamadani', 'admincindi@gmail.com', '$2y$10$fDNgOTsTgtOLshPSDmb.ge.nwZ6/IZKhlOaSoMR0jT8As7OOuar4m', 'admin'),
(12, 'Rara', 'rara@gmail.com', '$2y$10$FNsVcxmR1Pv5zf8kQ.cA0.HWuFq.6XDK4qqUOlw6Un0f1laxd.Bk2', 'pelanggan'),
(13, 'Nabila', 'nabila@gmail.com', '$2y$10$fZLCZA608wU34NfwoG0YLuhXk72sZNsWE5HyPix35Ju2MhGoNrsF2', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idAdmin`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `detail_custom`
--
ALTER TABLE `detail_custom`
  ADD PRIMARY KEY (`idCustom`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`idDetail`),
  ADD KEY `idPesanan` (`idPesanan`),
  ADD KEY `idProduk` (`idProduk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`idKategori`);

--
-- Indexes for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD PRIMARY KEY (`idKwitansi`),
  ADD KEY `idPembayaran` (`idPembayaran`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`idPelanggan`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`idPembayaran`),
  ADD KEY `idPesanan` (`idPesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`idPesanan`),
  ADD KEY `idPelanggan` (`idPelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`idProduk`),
  ADD KEY `idKategori` (`idKategori`);

--
-- Indexes for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD PRIMARY KEY (`idStok`),
  ADD KEY `idProduk` (`idProduk`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `idAdmin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_custom`
--
ALTER TABLE `detail_custom`
  MODIFY `idCustom` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `idDetail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `idKategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kwitansi`
--
ALTER TABLE `kwitansi`
  MODIFY `idKwitansi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `idPelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `idPembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `idPesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idProduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `stok_produk`
--
ALTER TABLE `stok_produk`
  MODIFY `idStok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`);

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`idPesanan`) REFERENCES `pesanan` (`idPesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`idProduk`) REFERENCES `produk` (`idProduk`);

--
-- Constraints for table `kwitansi`
--
ALTER TABLE `kwitansi`
  ADD CONSTRAINT `kwitansi_ibfk_1` FOREIGN KEY (`idPembayaran`) REFERENCES `pembayaran` (`idPembayaran`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`idPesanan`) REFERENCES `pesanan` (`idPesanan`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`idPelanggan`) REFERENCES `pelanggan` (`idPelanggan`);

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`idKategori`) REFERENCES `kategori` (`idKategori`);

--
-- Constraints for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD CONSTRAINT `stok_produk_ibfk_1` FOREIGN KEY (`idProduk`) REFERENCES `produk` (`idProduk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
