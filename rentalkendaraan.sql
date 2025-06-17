-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Jun 2025 pada 04.16
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
-- Database: `rentalkendaraan`
--

DELIMITER $$
--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_total_harga` (`p_daily_price` DECIMAL(10,2), `p_start_date` DATE, `p_end_date` DATE) RETURNS DECIMAL(10,2)  BEGIN
    DECLARE total DECIMAL(10,2);
    SET total = p_daily_price * DATEDIFF(p_end_date, p_start_date);
    RETURN total;
END$$

DELIMITER ;

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
  `status` enum('Tersedia','Maintenance','Sedang disewa') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`vehicle_id`, `brand`, `model`, `year`, `daily_price`, `status`) VALUES
('V001', 'Toyota', 'Avanza', 2021, 450000, 'Sedang disewa'),
('V002', 'Honda', 'Brio', 2018, 300000, 'Tersedia'),
('V003', 'Mazda', 'RX-8', 2012, 5000000, 'Maintenance'),
('V004', 'Mitsubishi', 'Pajero', 2015, 1700000, 'Sedang disewa'),
('V005', 'Daihatsu', 'Xenia', 2021, 370000, 'Sedang disewa'),
('V006', 'Mitsubishi', 'L300', 2013, 200000, 'Maintenance'),
('V007', 'Toyota', 'Alphard', 2020, 4200000, 'Sedang disewa');

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
(8, 'Gilang', '0818888888', 'gilang@email.com');

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
  `total_price` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rental`
--

INSERT INTO `rental` (`rental_id`, `vehicle_id`, `renter_id`, `start_date`, `end_date`, `total_price`) VALUES
(1, 'V001', 1, '2024-11-10', '2024-11-15', 2250000),
(2, 'V004', 2, '2025-04-07', '2025-04-14', 11900000),
(3, 'V005', 5, '2025-03-26', '2025-04-02', 2590000),
(4, 'V005', 7, '2025-01-07', '2025-01-12', 1850000),
(5, 'V007', 8, '2025-05-22', '2025-05-26', 16800000);

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
-- Stand-in struktur untuk tampilan `v_rental_aktif`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_rental_aktif` (
`rental_id` int(11)
,`brand` varchar(50)
,`model` varchar(50)
,`penyewa` varchar(100)
,`start_date` date
,`end_date` date
,`total_price` int(10)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_rental_aktif`
--
DROP TABLE IF EXISTS `v_rental_aktif`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_rental_aktif`  AS SELECT `r`.`rental_id` AS `rental_id`, `k`.`brand` AS `brand`, `k`.`model` AS `model`, `p`.`name` AS `penyewa`, `r`.`start_date` AS `start_date`, `r`.`end_date` AS `end_date`, `r`.`total_price` AS `total_price` FROM ((`rental` `r` join `kendaraan` `k` on(`r`.`vehicle_id` = `k`.`vehicle_id`)) join `penyewa` `p` on(`r`.`renter_id` = `p`.`renter_id`)) WHERE `r`.`end_date` >= curdate() ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`vehicle_id`);

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
