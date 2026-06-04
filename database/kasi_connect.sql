-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 09:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasi_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(8, 1, 6, 1, '2026-05-23 14:10:57', '2026-05-23 14:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Card',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `order_status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `customer_name` varchar(120) DEFAULT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(80) NOT NULL,
  `location_area` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_label` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_class` varchar(30) DEFAULT 'peach',
  `status` enum('active','inactive','pending','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `title`, `description`, `category`, `location_area`, `price`, `price_label`, `image_path`, `image_class`, `status`, `created_at`) VALUES
(1, 130, 'Chicken Kota Meal', 'Chicken Kota Meal', 'Food', 'Germiston', 59.99, 'R59.99', 'uploads/products/product_130_1779533123.png', 'peach', 'active', '2026-05-23 10:45:23'),
(2, 131, 'Denim Jacket', 'Our FF Denim Jacket is a classic style that\'s perfect for layering your look with authentic denim style and lightweight warmth. Wear it with jeans for an on-trend denim-on-denim look, or open over a maxi dress or skirt.\r\n\r\nStretch denim\r\n\r\nCollar\r\n\r\nButton through\r\n\r\nLong sleeves\r\n\r\nChest pockets', 'Fashion', 'Katlehong', 450.00, 'R450.00', 'uploads/products/product_131_1779534371.png', 'peach', 'active', '2026-05-23 11:06:11'),
(3, 132, 'Phone Screen Repair', 'We fix Andriod and Iphone broken screens', 'Repairs', 'Daveyton', 250.00, 'R250.00', 'uploads/products/product_132_1779534525.png', 'peach', 'active', '2026-05-23 11:08:45'),
(4, 133, 'Braids & Styling', 'We do types of braiding hair styles', 'Beauty', 'Boksburg', 200.00, 'R200.00', 'uploads/products/product_133_1779534710.png', 'peach', 'active', '2026-05-23 11:11:50'),
(5, 134, 'Home Cleaning', 'Clean Space Services, we clean all space including garages etc', 'Services', 'Germiston', 150.00, 'R150.00', 'uploads/products/product_134_1779534848.png', 'peach', 'active', '2026-05-23 11:14:08'),
(6, 135, 'Fresh Vegetables Box', 'Fresh Veg Box, we sell vegetables in boxes.\r\nYou select the types of vegetables you want and we package it for you and deliver it to you.', 'Food', 'Vosloorus', 120.00, 'R120.00', 'uploads/products/product_135_1779535005.png', 'peach', 'active', '2026-05-23 11:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `seller_profiles`
--

CREATE TABLE `seller_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `business_category` varchar(80) NOT NULL,
  `township_area` varchar(100) NOT NULL,
  `fulfilment_option` enum('pickup','delivery','pickup_delivery') NOT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_profiles`
--

INSERT INTO `seller_profiles` (`id`, `user_id`, `business_name`, `business_category`, `township_area`, `fulfilment_option`, `approval_status`, `created_at`) VALUES
(1, 130, 'Thando Kitchen', 'Food', 'Germiston', 'pickup', 'approved', '2026-05-23 10:41:47'),
(2, 131, 'Urban Wear Kasi', 'Fashion', 'Katlehong', 'pickup_delivery', 'approved', '2026-05-23 10:48:05'),
(3, 132, 'Fix It Fast', 'Repairs', 'Daveyton', 'pickup', 'approved', '2026-05-23 10:50:21'),
(4, 133, 'Nandi Hair Studio', 'Beauty', 'Boksburg', 'pickup', 'approved', '2026-05-23 10:52:06'),
(5, 134, 'Clean Space Services', 'Services', 'Germiston', 'pickup', 'approved', '2026-05-23 10:54:20'),
(6, 135, 'Fresh Veg Box', 'Groceries', 'Vosloorus', 'delivery', 'approved', '2026-05-23 10:55:43'),
(7, 136, 'Kasi Cakes', 'Food', 'Tembisa', 'pickup', 'approved', '2026-05-23 10:57:01'),
(8, 137, 'Quick Transport', 'Transport', 'Springs', 'delivery', 'approved', '2026-05-23 10:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','seller','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','pending','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `status`, `created_at`) VALUES
(1, 'Alex Fon', 'falexmech@gmail.com', '0687405254', '$2y$10$Pio6Z3Hvz4Pj2dd5Xg1H2eNuWc0CJae1.FJAX/5Xlf834msbQt/Wa', 'customer', 'active', '2026-05-11 10:22:43'),
(2, 'Nche Fon', 'axylashford17@gmail.com', '0687405254', '$2y$10$BFAzySQIN5SGx4eYVlbjHODPB/XPMS5Qu8tMc/Xgx59pmJFj0EqaO', 'customer', 'active', '2026-05-11 11:37:03'),
(6, 'Axel Fon', 'axel@kasi.com', '+27000000000', '$2y$10$.OdYmsASTbnCWslehXAcfuTMUamPkAEEzxX1EgxnqYVdyuXOwPEBm', 'admin', 'active', '2026-05-17 18:33:45'),
(130, 'Thando Kitchen', 'seller1@kasi.com', '0688710000', '$2y$10$/h/yJPQbzbE7QionDdbzY.qj2XbZcfdH1QxR92Y2l.ciuPF7O8KtC', 'seller', 'active', '2026-05-23 10:41:47'),
(131, 'Urban Wear Kasi', 'seller2@kasi.com', '0635710000', '$2y$10$BI1Rdieb67oeqB7TMcaAlu.ppdwRxY6A7LRGROJj1GiVQ9MsXKWWW', 'seller', 'active', '2026-05-23 10:48:05'),
(132, 'Fix It Fast', 'seller3@kasi.com', '0688713400', '$2y$10$OjmAtfbEdTE1LuM58FPxSuciNTK.8t4ixSCppAWhcDvmLIBbSSH56', 'seller', 'active', '2026-05-23 10:50:21'),
(133, 'Nandi Hair Studio', 'seller4@kasi.com', '0688713489', '$2y$10$dI7Njg6ypfKM/As3P2ufMuTHqbE86GPJ15HIGfWThSmu7R.EsnEKW', 'seller', 'active', '2026-05-23 10:52:06'),
(134, 'Clean Space Services', 'seller5@kasi.com', '0688715600', '$2y$10$jRb/DqYHdY5AMWELnbNhFus6e4fQVdIYqoxDbdpr4T3xw76OFlo1K', 'seller', 'active', '2026-05-23 10:54:20'),
(135, 'Fresh Veg Box', 'seller6@kasi.com', '0628718000', '$2y$10$P1VWYEQcasgzfnRg4.AACOvu2gxiIVorRfbsu471OtvVfjATIZb76', 'seller', 'active', '2026-05-23 10:55:43'),
(136, 'Kasi Cakes', 'seller7@kasi.com', '0688864489', '$2y$10$SoBqqgiZxe7UUXa5vqH6tO6JFDkzVXCwiOip6rZCg3QMgFPfmDfb6', 'seller', 'active', '2026-05-23 10:57:01'),
(137, 'Quick Transport', 'seller10@kasi.com', '0688713400', '$2y$10$QNX1yU.dfb01XpiSbGAh/uCN2dJaYlUSFgw0h.RFgRi6XPCLhPKGe', 'seller', 'active', '2026-05-23 10:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD CONSTRAINT `seller_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
