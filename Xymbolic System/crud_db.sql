-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 05:18 PM
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
(7, 'Xymbolic', 'I.T Solutions for your every needs. :)))', '', 'Building 1056 Rizal Highway Subic Bay Freeport Zone, Olongapo City, 2200 Zambales', '+63 (47) 222.8707', 'inquiry@xymbolic.com.ph', 'https://www.xymbolic.com.ph/', '', 'active', ''),
(8, 'Allied Care Experts (ACE) Medical Center - Baypointe, Inc.', 'Hospital', '', 'Block 8, Lot 1A and 1B Dewey Avenue Subic Bay Freeport Zone, Olongapo City, 2222 Zambales', '(047) 250-6070', 'mis@unihealthbaypointe.com', 'https://www.acebaypointe.com/index.php', '', 'active', ''),
(9, 'Wistron InfoComm Philippines Corporation', 'Appliances, Electrical, and Electronics Manufacturing', '', 'R7GV+MV5, Olongapo City, 2200 Zambales', '886-2-6616-999', 'enterpriseBG@wistron.com', 'https://www.wistron.com/en', '', 'active', ''),
(10, 'Subic Bay Travelers Hotel', 'Bembangan for the rich', '', 'R7CM+42H, Corner Aguinaldo and Raymundo Street Freeport Zone Subic (Olongapo), Subic Bay Freeport Zone', '0999 995 5238', 'hotelinquiry@subicbaytravelershotel.com', 'https://www.subicbaytravelershotel.com/', '', 'active', ''),
(11, 'Gordon College', 'College ng mga KUPAL', '', 'Olongapo City Sports Complex, Gordon College,Philippine, Olongapo City, Zambales', '098989898', 'reynaldo.bautista@gordoncollege.edu.ph', 'https://gordoncollege.edu.ph/w3/', '', 'active', ''),
(12, 'Jollibee', 'fast food restau', '', 'Argonaut Highway Gateway District corner Rizal Highway Subic Bay Freeport Zone, Olongapo City, 2200 Zambales', '639985515040', 'jb0912@jollibee.com.ph', 'https://order.jollibee.com/en/ph', '', 'active', ''),
(13, 'McDonald\'s - Subic Bay Gateway', 'pagkain ng lahat', '', '24 Rizal Hwy, Subic Bay Freeport Zone, 2200 Zambales', '(02) 8888 6236', 'mcdo2025@yahoo.com', 'https://www.mcdelivery.com.ph/account/location/', '', 'active', ''),
(14, 'Tonton\'s Shabu-shabu', 'good items at a low price', '', 'sa pwet ni mavs malaki', '09991221921', 'tontontonpakitongkitong69@yahoo.com', 'https://tontonpakitongkitong69.com', '', 'active', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `name`, `price`, `description`) VALUES
(8, 7, 'CANON G3730', NULL, NULL),
(10, 8, 'Blood', NULL, NULL),
(11, 8, 'Laman Loob', NULL, NULL),
(12, 8, 'Betamax', NULL, NULL),
(13, 8, 'Menudo(utak ng tao)', NULL, NULL),
(14, 9, 'TAGA ayos ng ref', NULL, NULL),
(15, 9, 'Microwave fixer', NULL, NULL),
(16, 10, 'Babae', NULL, NULL),
(17, 10, 'Lalake', NULL, NULL),
(18, 10, 'Bakla', NULL, NULL),
(19, 10, 'Tomboy', NULL, NULL),
(20, 11, 'KUPAL NA COORDINATOR', NULL, NULL),
(21, 11, 'MGA BOBONG STUDYANTE', NULL, NULL),
(22, 11, 'Lumpia', NULL, NULL),
(23, 11, 'NIGGA', NULL, NULL),
(24, 11, 'tarantadong professor', NULL, NULL),
(25, 12, '1 - pc. Chickenjoy, Regular Fries & Half Jolly Spaghetti Super Meal w/ Drink', NULL, NULL),
(26, 12, '1-pc. Burger Steak w/ Double Rice and Drink', NULL, NULL),
(27, 12, '1-pc. Chickenjoy w/ Drink', NULL, NULL),
(28, 12, 'Jolly Spaghetti w/ Fries & Drink', NULL, NULL),
(29, 12, 'Crunchy Chicken Sandwich GameJoy Combo', NULL, NULL),
(30, 11, 'burger na basa', NULL, NULL),
(31, 11, '1-pc. Chickenjoy w/ Drink', NULL, NULL),
(32, 13, 'Big Mac', NULL, NULL),
(33, 13, 'Cheeseburger', NULL, NULL),
(34, 13, 'Bacon Quarter Pounder with Cheese', NULL, NULL),
(35, 13, 'Hamburger Happy Meal', NULL, NULL),
(36, 13, 'McCafe Caramel Macchiato', NULL, NULL),
(37, 13, 'Americano', NULL, NULL),
(38, 14, '199 SHABU-BEST SELLER!!!!', NULL, NULL),
(39, 14, '99 SHABU- STUDENT FRIENDLY MEAL', NULL, NULL),
(40, 14, 'MAUWINA- SECRET ITEM- 18 ABOVE ONLY', NULL, NULL),
(41, 14, '299 SHABU- HINDI TO PWEDE SA BATA', NULL, NULL),
(42, 14, '699 SHABU- SARAP NA NAKAKAHIGH ;)', NULL, NULL),
(43, 14, '129 SHABU-RAT-NI-MAVS', NULL, NULL),
(44, 14, '69 UNLI SHABU', NULL, NULL),
(45, 14, '59 SEMI UNLI SHABU', NULL, NULL),
(46, 14, '49 SHABU?', NULL, NULL),
(47, 14, '39 PWEDE NA PANTAGUYOD NG GUTOM', NULL, NULL);

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
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
