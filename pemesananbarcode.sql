-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 09, 2025 at 02:08 PM
-- Server version: 8.0.36
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pemesananbarcode`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `harga`, `stok`) VALUES
(1, 'Nasi Soto Ayam', '20000.00', 47),
(2, 'Nasi Rawon', '23000.00', 16),
(3, 'Nasi Ketela Sambal Matah', '20000.00', 16),
(4, 'Nasi Goreng Seafood', '25000.00', 24),
(5, 'Sayur Lodeh', '20000.00', 65),
(6, 'Teh Panas', '5000.00', 60),
(7, 'Kopi Rempah', '10000.00', 98),
(8, 'Coklat Panas', '15000.00', 29),
(9, 'Green Tea Lattee', '20000.00', 21),
(10, 'Cappucino', '20000.00', 97),
(11, 'Choco Marsmellow', '25000.00', 64);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail_pesanan` int NOT NULL,
  `id_pesanan` int NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_barang`, `jumlah`, `harga_satuan`) VALUES
(1, 1, 10, 2, '20000.00');

--
-- Triggers `detail_pesanan`
--
DELIMITER $$
CREATE TRIGGER `handle_detail_pesanan_insert` BEFORE INSERT ON `detail_pesanan` FOR EACH ROW BEGIN
    DECLARE stok_saat_ini INT;
    DECLARE harga_barang_saat_ini DECIMAL(10, 2);

    -- Ambil stok dan harga barang dari tabel 'barang'
    SELECT stok, harga INTO stok_saat_ini, harga_barang_saat_ini
    FROM barang
    WHERE id_barang = NEW.id_barang;

    -- 1. Cek Ketersediaan Stok
    -- Jika jumlah yang dipesan (NEW.jumlah) lebih besar dari stok yang tersedia (stok_saat_ini)
    IF NEW.jumlah > stok_saat_ini THEN
        -- Batalkan operasi INSERT dan kembalikan pesan kesalahan
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok barang tidak mencukupi untuk pesanan ini.';
    END IF;

    -- 2. Set Harga Satuan (otomatis mengisi harga dari tabel barang)
    -- Ini akan menimpa nilai harga_satuan yang mungkin Anda berikan secara manual
    -- saat INSERT, dan akan menggunakan harga yang ada di tabel 'barang'.
    SET NEW.harga_satuan = harga_barang_saat_ini;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `kurangi_stok_barang` AFTER INSERT ON `detail_pesanan` FOR EACH ROW BEGIN
    UPDATE barang
    SET stok = stok - NEW.jumlah
    WHERE id_barang = NEW.id_barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login_admin`
--

CREATE TABLE `login_admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_admin`
--

INSERT INTO `login_admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin\r\n'),
(2, 'pepelo', 'pelo');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int NOT NULL,
  `tanggal_pesanan` datetime NOT NULL,
  `nama_pemesan` varchar(255) NOT NULL,
  `status_pesanan` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `tanggal_pesanan`, `nama_pemesan`, `status_pesanan`) VALUES
(1, '2025-06-09 10:33:36', 'memekila', 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail_pesanan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `login_admin`
--
ALTER TABLE `login_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_admin`
--
ALTER TABLE `login_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
