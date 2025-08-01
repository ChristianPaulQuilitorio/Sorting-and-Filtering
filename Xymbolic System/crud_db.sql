-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 09:11 AM
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
(1, 'admin', 'admin123');

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
(9, 'Wistron InfoComm Philippines Corporation', 'Appliances, Electrical, and Electronics Manufacturing', '', 'R7GV+MV5, Olongapo City, 2200 Zambales', '886-2-6616-999', 'enterpriseBG@wistron.com', 'https://www.wistron.com/en', '', 'active', ''),
(10, 'Subic Bay Travelers Hotel', 'Bembangan for the rich', '', 'R7CM+42H, Corner Aguinaldo and Raymundo Street Freeport Zone Subic (Olongapo), Subic Bay Freeport Zone', '0999 995 5238', 'hotelinquiry@subicbaytravelershotel.com', 'https://www.subicbaytravelershotel.com/', '', 'active', '5'),
(11, 'Gordon College', 'College ng mga matatalino', '', 'Olongapo City Sports Complex, Gordon College,Philippine, Olongapo City, Zambales', '098989898', 'reynaldo.bautista@gordoncollege.edu.ph', 'https://gordoncollege.edu.ph/w3/', '', 'active', '5'),
(32, 'SubicWorx', 'Subic Works', '', 'Building 1056, Rizal Highway, Subic Bay Freeport Zone, Olongapo, Philippines, 2222', '0472513680', 'inquiry@subicworx.com', 'https://www.facebook.com/subicworxinc/', '', 'active', '4'),
(33, 'Kusinerong Kalbo', 'GC Hot pagkain', '', 'Gordon College', '09090909', 'kusinerongkalbo@gmail.com', 'https://www.facebook.com/KusinerongKalbooo/', '', 'active', '5'),
(39, 'IONTECH inc.', 'We represent prominent technology brands as their exclusive or authorized distributor in the Philippines. This strategic alliance enables the company to be at the forefront of digital convergence of consumer electronics, telecoms and IT products.', '', 'Suite 1104 11th Floor, Taipan Place, F. Ortigas Jr. Rd, Pasig, 1605 Metro Manila', '(02)87362482', 'iontech@gmail.com', 'https://iontech.com.ph/', '', 'active', '4');

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
  `category` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `name`, `price`, `description`, `category`, `status`) VALUES
(102, 7, 'Asus', NULL, NULL, 'Laptop', 'active'),
(103, 7, 'Acer', NULL, NULL, 'Laptop', 'active'),
(146, 7, 'Epson', NULL, NULL, 'Printer', 'active'),
(147, 7, 'HyperX', NULL, NULL, 'Ram', 'active'),
(148, 7, 'b', NULL, NULL, 'A', 'active'),
(149, 7, 'B', NULL, NULL, 'B', 'active'),
(150, 7, 'Z', NULL, NULL, 'Z', 'active'),
(153, 7, 'a', NULL, NULL, 'A', 'active'),
(155, 7, 'z', NULL, NULL, 'A', 'active'),
(156, 7, 'C', NULL, NULL, 'A', 'active'),
(157, 33, 'Pares Overload', NULL, NULL, 'Pares Combo', 'active'),
(158, 33, 'Chicharong kahoy /w unli rice', NULL, NULL, 'Pares Combo', 'active'),
(160, 33, 'Kilaw na gummy bears w/ sabaw ice tea', NULL, NULL, 'Pares Combo', 'active'),
(170, 39, 'Lenovo ThinkPad Series', NULL, NULL, 'LENOVO', 'active'),
(171, 39, 'Lenovo Yoga Series', NULL, NULL, 'LENOVO', 'active'),
(172, 39, 'Lenovo Ideapad 100s', NULL, NULL, 'LENOVO', 'active'),
(173, 39, 'Asus TUF Gaming F15', NULL, NULL, 'ASUS', 'active'),
(174, 39, 'ASUS ROG (Republic of Gamers) Series', NULL, NULL, 'ASUS', 'active');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

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
