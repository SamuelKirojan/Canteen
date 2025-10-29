-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Okt 2023 pada 10.04
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Ensure target database exists and is selected
CREATE DATABASE IF NOT EXISTS `canteen` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `canteen`;

--
-- Database: `canteen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `name`, `price`, `stock`, `description`, `image_url`) VALUES
(1, 'Nasi Campur Ikan', 15.00, 25, 'Nasi Campur Ikan.', 'assets/img/menu/Food/foto1.jpg'),
(2, 'Nasi Ayam Kampung', 22.00, 25, 'Nasi Ayam Kampung.', 'assets/img/menu/Food/foto2.jpg'),
(3, 'Nasi Goreng', 15.00, 25, 'Nasi Goreng.', 'assets/img/menu/Food/foto3.jpg'),
(4, 'Mie Goreng', 15.00, 25, 'Mie Goreng.', 'assets/img/menu/Food/foto4.jpeg'),
(5, 'Nasi Tempe Penyet', 15.00, 25, 'Nasi Tempe Penyet.', 'assets/img/menu/Food/foto5.jpg'),
(6, 'Nasi Ayam Geprek', 25.00, 25, 'Nasi Ayam Geprek.', 'assets/img/menu/Food/foto6.png'),
(7, 'Nutrisari', 5.00, 25, 'Nutrisari Hot/Cold.', 'assets/img/menu/Drink/drink1.jpg'),
(8, 'Cholocatos', 10.00, 25, 'Chocolatos Hot/Cold.', 'assets/img/menu/Drink/drink2.jpg'),
(9, 'Beng Beng Drink', 10.00, 25, 'Beng Beng Drink Hot/Cold.', 'assets/img/menu/Drink/drink3.jpg'),
(10, 'Pop Ice', 6.00, 25, 'Pop Ice all var.', 'assets/img/menu/Drink/drink4.jpg'),
(11, 'Saraba', 6.00, 25, 'Saraba with milk/natural.', 'assets/img/menu/Drink/drink5.jpg'),
(12, 'Milo', 10.00, 25, 'Milo Hot/Cold.', 'assets/img/menu/Drink/drink6.jpeg'),
(13, 'Pisang Goreng Sepatu', 15.00, 25, 'Pisang Goreng Sepatu.', 'assets/img/menu/Snacks/snacks1.jpeg'),
(14, 'Pisang Goreng Stick', 10.00, 25, 'Pisang Goreng Goroho Stick.', 'assets/img/menu/Snacks/snacks2.jpg'),
(15, 'Roti Bakar', 18.00, 25, 'Roti Bakar Coklat/Keju/Ovaltine.', 'assets/img/menu/Snacks/snacks3.jpg'),
(16, 'Kentang Goreng', 18.00, 25, 'Kentang Goreng.', 'assets/img/menu/Snacks/snacks4.jpg'),
(17, 'Pie Coklat', 7.00, 25, 'Pie Coklat.', 'assets/img/menu/Snacks/snacks5.jpg'),
(18, 'Ubi Goreng', 7.00, 25, 'Ubi Goreng.', 'assets/img/menu/Snacks/snacks6.jpg');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
