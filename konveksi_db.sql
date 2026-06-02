-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 02:50 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `customText` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`idDetail`, `idPesanan`, `idProduk`, `jenis`, `ukuran`, `desain`, `qty`, `customText`) VALUES
(1, 1, NULL, 'Varsity', ';', '1780330488_bahanl.jpg', 200, 'apapin\r\n'),
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
(14, 14, NULL, 'Polo Shirt', 's', '1780356580_qBaC8k8RJmK0z0QjTFaH.jpg', 2, 'alsd');

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
(4, 'Polo Shirt'),
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
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`idPelanggan`, `idUser`, `noHp`, `alamat`) VALUES
(1, 2, '081211437354', '\r\n            curug');

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
  `dp` double DEFAULT 0
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
(18, 14, 175000, 'Transfer BCA', 'Pending', '1780356604_kanja.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `idPesanan` int(11) NOT NULL,
  `idPelanggan` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `jenisPesanan` enum('siap_pakai','custom') DEFAULT NULL,
  `total` double DEFAULT NULL,
  `nomorInvoice` varchar(100) DEFAULT NULL,
  `ongkir` double DEFAULT 0,
  `alamat_kirim` text DEFAULT NULL,
  `jasa_kirim` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idPesanan`, `idPelanggan`, `tanggal`, `status`, `jenisPesanan`, `total`, `nomorInvoice`, `ongkir`, `alamat_kirim`, `jasa_kirim`) VALUES
(1, 1, '2026-06-01', 'Menunggu', 'custom', 0, 'INV-20260601-1', 0, NULL, NULL),
(2, 1, '2026-06-01', 'Lunas', 'siap_pakai', 250000, 'INV-20260601-2', 0, NULL, NULL),
(3, 1, '2026-06-02', 'Diproses', 'siap_pakai', 250000, 'INV-20260601-3', 0, NULL, NULL),
(4, 1, '2026-06-02', 'Menunggu', 'custom', 0, 'INV-20260601-4', 0, NULL, NULL),
(5, 1, '2026-06-02', 'Menunggu', 'custom', 0, 'INV-20260601-5', 0, NULL, NULL),
(6, 1, '2026-06-02', 'Menunggu', 'custom', 150000, 'INV-20260601-6', 0, NULL, NULL),
(7, 1, '2026-06-02', 'Menunggu', 'custom', 150000, 'INV-20260601-7', 0, NULL, NULL),
(8, 1, '2026-06-02', 'Menunggu', 'custom', 0, 'INV-20260601-8', 0, NULL, NULL),
(9, 1, '2026-06-02', 'Lunas', 'custom', 33000000, 'INV-20260601-9', 0, NULL, NULL),
(10, 1, '2026-06-02', 'Menunggu', 'custom', 174000, 'INV-20260601-10', 0, NULL, NULL),
(11, 1, '2026-06-02', 'Menunggu', 'custom', 174000, 'INV-20260601-11', 0, NULL, NULL),
(12, 1, '2026-06-02', 'Menunggu', 'custom', 1000000, 'INV-20260602-12', 0, NULL, NULL),
(13, 1, '2026-06-02', 'Menunggu', 'custom', 0, 'INV-20260602-13', 0, NULL, NULL),
(14, 1, '2026-06-02', 'Lunas', 'custom', 350000, 'INV-20260602-14', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `idProduk` int(11) NOT NULL,
  `namaProduk` varchar(100) DEFAULT NULL,
  `harga` double DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `idKategori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`idProduk`, `namaProduk`, `harga`, `gambar`, `deskripsi`, `idKategori`) VALUES
(4, 'corduroy', 250000, '1780324515_JTHtz8t98ElnrXBM8PIB.jpg', 'bagus', 3);

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
(4, 4, 140, 'pcs');

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
(1, 'putri amalia ramadani', '1224160102@global.ac.id', 'putri', 'admin'),
(2, 'Putri Amalia Ramadani', 'putri.amlia031006@gmail.com', '$2y$10$/FNE0glxsBMkThiEJkUZSuDItebn6grVXxBlRjmT/Fz67qKjCl6Q2', 'pelanggan'),
(3, 'putri', 'mizum1265@gmail.com', 'putriadmin', 'admin');

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
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `idDetail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `idPelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `idPembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `idPesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `idProduk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stok_produk`
--
ALTER TABLE `stok_produk`
  MODIFY `idStok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
