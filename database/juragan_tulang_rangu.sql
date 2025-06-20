-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2025 at 02:45 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `juragan_tulang_rangu`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_by` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `product_id` int NOT NULL,
  `option_id` int DEFAULT NULL,
  `extra_ids` text,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `product_id`, `option_id`, `extra_ids`, `quantity`, `created_at`, `updated_at`) VALUES
(38, 'CSTJTR1', 1, 2, '4', 1, '2025-06-19 12:51:19', '2025-06-19 19:51:19'),
(40, 'CSTJTR1', 1, 2, '', 3, '2025-06-19 14:37:39', '2025-06-19 21:37:42');

-- --------------------------------------------------------

--
-- Table structure for table `daily_financial_records`
--

CREATE TABLE `daily_financial_records` (
  `id` int NOT NULL,
  `record_date` date NOT NULL,
  `product_income` int DEFAULT '0',
  `delivery_income` int DEFAULT '0',
  `total_income` int GENERATED ALWAYS AS ((`product_income` + `delivery_income`)) STORED,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_zones`
--

CREATE TABLE `delivery_zones` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `fee` int NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_zones`
--

INSERT INTO `delivery_zones` (`id`, `name`, `city`, `fee`, `active`, `created_at`) VALUES
(1, 'Telukjambe Timur', 'Karawang', 5000, 1, '2025-06-19 13:27:29'),
(2, 'Telukjambe Barat', 'Karawang', 7000, 1, '2025-06-19 13:27:29'),
(3, 'Karawang Barat', 'Karawang', 8000, 1, '2025-06-19 13:27:29'),
(4, 'Karawang Timur', 'Karawang', 8000, 1, '2025-06-19 13:27:29'),
(5, 'Cikampek', 'Karawang', 12000, 1, '2025-06-19 13:27:29'),
(6, 'Cilamaya', 'Karawang', 15000, 1, '2025-06-19 13:27:29'),
(7, 'Purwakarta Kota', 'Purwakarta', 20000, 1, '2025-06-19 13:27:29'),
(8, 'Campaka', 'Purwakarta', 21000, 1, '2025-06-19 13:27:29'),
(9, 'Cikarang Barat', 'Bekasi', 18000, 1, '2025-06-19 13:27:29'),
(10, 'Tambun Selatan', 'Bekasi', 17000, 1, '2025-06-19 13:27:29'),
(11, 'Subang Kota', 'Subang', 22000, 1, '2025-06-19 13:27:29'),
(12, 'Pagaden', 'Subang', 21000, 1, '2025-06-19 13:27:29'),
(13, 'Cibitung', 'Bekasi', 19000, 1, '2025-06-19 13:27:29'),
(14, 'Bekasi Selatan', 'Bekasi', 23000, 1, '2025-06-19 13:27:29'),
(15, 'Jakarta Barat', 'Jakarta', 25000, 1, '2025-06-19 13:27:29'),
(16, 'Jakarta Timur', 'Jakarta', 25000, 1, '2025-06-19 13:27:29'),
(17, 'Bandung Kota', 'Bandung', 27000, 1, '2025-06-19 13:27:29'),
(18, 'Luar Zona / Jabodetabek', 'Luar Area', 30000, 1, '2025-06-19 13:27:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `label` varchar(50) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image_path`, `label`, `stock`, `created_at`) VALUES
(1, 'Dimsum', 'Apa ya', 'uploads/1750243522_Bukti_submit.jpg', 'NEW', 15, '2025-06-18 10:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant` varchar(100) NOT NULL,
  `category` enum('size','extra') NOT NULL,
  `price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `variant`, `category`, `price`) VALUES
(1, 1, 'Small', 'size', 5000),
(2, 1, 'Medium', 'size', 10000),
(3, 1, 'Large', 'size', 15000),
(4, 1, 'Extra Spicy', 'extra', 2000);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` varchar(30) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `delivery_address` text NOT NULL,
  `message` text,
  `payment_method` enum('COD','Online') NOT NULL,
  `delivery_method` enum('Delivery','Pickup') NOT NULL,
  `delivery_fee` int DEFAULT '0',
  `total_price` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `delivery_zone_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `role` enum('customer','admin','employee') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `remember_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `remember_token`) VALUES
('ADMNJTR1', 'Naufal', 'naufalsafiqq@gmail.com', '$2y$10$DTBd0433OefMQjTi3Qm99OFTqJEDGGoL5KISn6c.f2CjuuDcsn.i.', '6281385278551', 'Jakarta', 'admin', '2025-06-18 09:07:53', NULL),
('ADMNJTR2', 'ferdi', 'ferdiyansah@gmail.com', '$2y$10$MpyGQePCxFAaHb6TcnbZBuXqiPPZNAgV/jXFfPXkZ1xiQPUy6rmKy', '62859121392342', 'Cirebon', 'admin', '2025-06-18 09:14:28', NULL),
('CSTJTR1', 'sapiq', 'naufalsafiq.f@gmail.com', '$2y$10$DxZcr5w92qfrIszizlLLJOyWh5dL8TrtKx2b1ZLsdDZUwIt5xSEF2', '0813876545267', 'Jepang', 'customer', '2025-06-19 06:17:45', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `option_id` (`option_id`);

--
-- Indexes for table `daily_financial_records`
--
ALTER TABLE `daily_financial_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `record_date` (`record_date`);

--
-- Indexes for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_delivery_zone` (`delivery_zone_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `daily_financial_records`
--
ALTER TABLE `daily_financial_records`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_zones`
--
ALTER TABLE `delivery_zones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_carts_option_variant` FOREIGN KEY (`option_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_delivery_zone` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
