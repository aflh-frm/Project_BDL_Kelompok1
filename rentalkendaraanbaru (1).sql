-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Jun 2025 pada 05.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentalkendaraanbaru`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `vehicle_id` varchar(10) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `daily_price` int(10) DEFAULT NULL,
  `status` enum('Tersedia','Maintenance','Sedang disewa') DEFAULT 'Sedang disewa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`vehicle_id`, `brand`, `model`, `year`, `daily_price`, `status`) VALUES
('V001', 'Toyota', 'Avanza', 2021, 450000, 'Sedang disewa'),
('V002', 'Honda', 'Brio', 2018, 360000, 'Maintenance'),
('V003', 'Mazda', 'RX-8', 2012, 3400000, 'Tersedia'),
('V004', 'Mitsubishi', 'Pajero', 2015, 1000000, 'Tersedia'),
('V005', 'Daihatsu', 'Xenia', 2021, 370000, 'Tersedia'),
('V007', 'Toyota', 'Alphard', 2020, 2800000, 'Tersedia'),
('V008', 'Honda', 'Civic', 2022, 800000, 'Sedang disewa'),
('V009', 'Toyota', 'Fortuner', 2020, 700000, 'Tersedia'),
('V010', 'BMW', 'M3', 2024, 700, 'Sedang disewa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyewa`
--

CREATE TABLE `penyewa` (
  `renter_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penyewa`
--

INSERT INTO `penyewa` (`renter_id`, `name`, `phone`, `email`) VALUES
(1, 'Dzaky', '0811111111', 'dzaky@email.com'),
(2, 'Edo', '0812222222', 'edo@email.com'),
(3, 'Ricky', '0813333333', 'ricky@email.com'),
(4, 'Ilham', '0814444444', 'ilham@email.com'),
(5, 'Athia', '0815555555', 'athia@email.com'),
(6, 'Rais', '08166666666', 'rais@email.com'),
(7, 'Dimas', '0817777777', 'dimas@email.com'),
(8, 'Gilang', '0818888888', 'gilang@email.com'),
(9, 'Emir', '0818492049', 'emir@email.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental`
--

CREATE TABLE `rental` (
  `rental_id` int(11) NOT NULL,
  `vehicle_id` varchar(10) DEFAULT NULL,
  `renter_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `total_price` int(10) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Selesai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rental`
--

INSERT INTO `rental` (`rental_id`, `vehicle_id`, `renter_id`, `start_date`, `end_date`, `total_price`, `status`) VALUES
(0, 'V001', 1, '2025-06-22', '2025-06-24', 900000, 'Selesai'),
(1, 'V008', 3, '2025-06-11', '2025-06-14', 600000, 'Selesai'),
(2, 'V009', 2, '2025-06-21', '2025-06-22', 1400000, 'Selesai'),
(3, 'V004', 6, '2025-06-23', '2025-06-25', 3000000, 'Selesai'),
(4, 'V009', 5, '2025-06-18', '2025-06-19', 1400000, 'Selesai'),
(5, 'V004', 7, '2025-06-22', '2025-06-24', 3000000, 'Selesai'),
(6, 'V005', 4, '2025-06-24', '2025-06-26', 1110000, 'Selesai'),
(7, 'V008', 2, '2025-06-22', '2025-06-28', 5600000, 'Aktif'),
(8, 'V001', 5, '2025-06-20', '2025-06-24', 2250000, 'Aktif'),
(9, 'V007', 6, '2025-06-23', '2025-06-25', 8400000, 'Selesai'),
(10, 'V003', 3, '2025-06-26', '2025-06-28', 10200000, 'Selesai'),
(11, 'V007', 7, '2025-06-30', '2025-07-03', 11200000, 'Selesai');

--
-- Trigger `rental`
--
DELIMITER $$
CREATE TRIGGER `tr_after_rental_delete` AFTER DELETE ON `rental` FOR EACH ROW BEGIN
    UPDATE kendaraan 
    SET status = 'Tersedia' 
    WHERE vehicle_id = OLD.vehicle_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_after_rental_insert` AFTER INSERT ON `rental` FOR EACH ROW BEGIN
    UPDATE kendaraan 
    SET status = 'Sedang disewa' 
    WHERE vehicle_id = NEW.vehicle_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_rental`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_rental` (
`rental_id` int(11)
,`brand` varchar(50)
,`model` varchar(50)
,`name` varchar(100)
,`start_date` date
,`end_date` date
,`total_price` int(10)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_rental_baru`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_rental_baru` (
`rental_id` int(11)
,`brand` varchar(50)
,`model` varchar(50)
,`name` varchar(100)
,`start_date` date
,`end_date` date
,`total_price` int(10)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_rental`
--
DROP TABLE IF EXISTS `v_rental`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_rental`  AS SELECT `r`.`rental_id` AS `rental_id`, ucase(`k`.`brand`) AS `brand`, `k`.`model` AS `model`, `p`.`name` AS `name`, `r`.`start_date` AS `start_date`, `r`.`end_date` AS `end_date`, `r`.`total_price` AS `total_price` FROM ((`rental` `r` join `kendaraan` `k` on(`r`.`vehicle_id` = `k`.`vehicle_id`)) join `penyewa` `p` on(`r`.`renter_id` = `p`.`renter_id`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_rental_baru`
--
DROP TABLE IF EXISTS `v_rental_baru`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_rental_baru`  AS SELECT `r`.`rental_id` AS `rental_id`, ucase(`k`.`brand`) AS `brand`, `k`.`model` AS `model`, `p`.`name` AS `name`, `r`.`start_date` AS `start_date`, `r`.`end_date` AS `end_date`, `r`.`total_price` AS `total_price` FROM ((`rental` `r` join `penyewa` `p` on(`r`.`renter_id` = `p`.`renter_id`)) join `kendaraan` `k` on(`r`.`vehicle_id` = `k`.`vehicle_id`)) WHERE `r`.`status` = 'Aktif' ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `idx_kendaraan_brand` (`brand`);

--
-- Indeks untuk tabel `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`renter_id`);

--
-- Indeks untuk tabel `rental`
--
ALTER TABLE `rental`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `renter_id` (`renter_id`);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `rental`
--
ALTER TABLE `rental`
  ADD CONSTRAINT `rental_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `kendaraan` (`vehicle_id`),
  ADD CONSTRAINT `rental_ibfk_2` FOREIGN KEY (`renter_id`) REFERENCES `penyewa` (`renter_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
