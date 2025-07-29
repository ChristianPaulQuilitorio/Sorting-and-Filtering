-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 09:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crud_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'Boss Jake', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `review` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `description`, `sector`, `location`, `contact_number`, `email`, `url`, `opening_hours`, `status`, `review`) VALUES
(7, 'Xymbolic', 'I.T Solutions for your every needs. :)))', '', 'Building 1056 Rizal Highway Subic Bay Freeport Zone, Olongapo City, 2200 Zambales', '+63 (47) 222.8707', 'inquiry@xymbolic.com.ph', 'https://www.xymbolic.com.ph/', '', 'active', '4'),
(8, 'Allied Care Experts (ACE) Medical Center - Baypointe, Inc.', 'Hospital', '', 'Block 8, Lot 1A and 1B Dewey Avenue Subic Bay Freeport Zone, Olongapo City, 2222 Zambales', '(047) 250-6070', 'mis@unihealthbaypointe.com', 'https://www.acebaypointe.com/index.php', '', 'active', ''),
(9, 'Wistron InfoComm Philippines Corporation', 'Appliances, Electrical, and Electronics Manufacturing', '', 'R7GV+MV5, Olongapo City, 2200 Zambales', '886-2-6616-999', 'enterpriseBG@wistron.com', 'https://www.wistron.com/en', '', 'active', ''),
(10, 'Subic Bay Travelers Hotel', 'Bembangan for the rich', '', 'R7CM+42H, Corner Aguinaldo and Raymundo Street Freeport Zone Subic (Olongapo), Subic Bay Freeport Zone', '0999 995 5238', 'hotelinquiry@subicbaytravelershotel.com', 'https://www.subicbaytravelershotel.com/', '', 'active', ''),
(11, 'Gordon College', 'College ng mga KUPAL', '', 'Olongapo City Sports Complex, Gordon College,Philippine, Olongapo City, Zambales', '098989898', 'reynaldo.bautista@gordoncollege.edu.ph', 'https://gordoncollege.edu.ph/w3/', '', 'active', ''),
(12, 'McDonald\'s - Subic Bay Gateway', 'fast food restau', '', 'Argonaut Highway Gateway District corner Rizal Highway Subic Bay Freeport Zone, Olongapo City, 2200 Zambales', '639985515040', 'jb0912@jollibee.com.ph', 'https://order.jollibee.com/en/ph', '', 'active', '3'),
(13, 'McDonald\'s - Subic Bay Gateway', 'hotel, services and clinic', '', 'Argonaut Highway Gateway District corner Rizal Highway Subic Bay Freeport Zone, Olongapo City, 2200 Zambales', '(02) 8888 6236', 'mcdo2025@yahoo.com', 'https://www.mcdelivery.com.ph/account/location/', '', 'active', '5');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `name`, `price`, `description`, `status`) VALUES
(8, 7, 'EPSON L300', NULL, NULL, 'active'),
(11, 8, 'Laman Loob', NULL, NULL, 'active'),
(12, 8, 'Betamax', NULL, NULL, 'active'),
(13, 8, 'Menudo(utak ng tao)', NULL, NULL, 'active'),
(14, 9, 'TAGA ayos ng ref', NULL, NULL, 'active'),
(15, 9, 'Microwave fixer', NULL, NULL, 'active'),
(16, 10, 'Babae', NULL, NULL, 'active'),
(17, 10, 'Lalake', NULL, NULL, 'active'),
(18, 10, 'Bakla', NULL, NULL, 'active'),
(19, 10, 'Tomboy', NULL, NULL, 'active'),
(20, 11, 'KUPAL NA COORDINATOR', NULL, NULL, 'active'),
(21, 11, 'MGA BOBONG STUDYANTE', NULL, NULL, 'active'),
(22, 11, 'Lumpia', NULL, NULL, 'active'),
(23, 11, 'NIGGA', NULL, NULL, 'active'),
(24, 11, 'tarantadong professor', NULL, NULL, 'active'),
(27, 12, '1-pc. Chickenjoy w/ Drink', NULL, NULL, 'active'),
(28, 12, 'Jolly Spaghetti w/ Fries & Drink', NULL, NULL, 'active'),
(30, 11, 'burger na basa', NULL, NULL, 'active'),
(31, 11, '1-pc. Chickenjoy w/ Drink', NULL, NULL, 'active'),
(36, 13, 'McCafe Caramel Macchiatos', NULL, NULL, 'active'),
(53, 12, 'Hotdog ', NULL, NULL, 'active'),
(54, 10, 'asd', NULL, NULL, 'active'),
(65, 13, 'asdasd', NULL, NULL, 'active'),
(70, 12, 'asdasdsa', NULL, NULL, 'active'),
(71, 13, 'asdasdasdsa', NULL, NULL, 'active'),
(72, 13, 'asdasdsa', NULL, NULL, 'active'),
(73, 13, 'asd', NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
