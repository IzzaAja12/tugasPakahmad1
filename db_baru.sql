-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 22, 2025 at 11:41 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simakademik`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int NOT NULL,
  `user` varchar(502) COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `lvl` enum('admin','petugas','wks_1','wks_2','wks_3','wks_4','walikelas') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_wali` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `user`, `pass`, `lvl`, `id_wali`) VALUES
(1, 'admin', 'admin', 'admin', NULL),
(2, 'petugas', 'petugas', 'petugas', NULL),
(3, 'walikelas_pplg', 'pplg', 'walikelas', 1),
(4, 'walikelas_akl', 'akl', 'walikelas', 2),
(5, 'walikelas_mplb', 'mplb', 'walikelas', 3),
(6, 'walikelas_pm', 'pm', 'walikelas', 4),
(7, 'wakasek_1', 'wks', 'wks_1', NULL),
(8, 'wakasek_2', 'wks', 'wks_2', NULL),
(9, 'wakasek_3', 'wks', 'wks_3', NULL),
(10, 'wakasek_4', 'wks', 'wks_4', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
