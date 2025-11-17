-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 17, 2025 at 11:24 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `order_date`, `shipping_address`) VALUES
(1, 2, 1299.99, 'delivered', '2025-11-17 10:17:01', '123 Main St, New York, NY 10001'),
(2, 3, 299.99, 'processing', '2025-11-17 10:17:01', '456 Oak Ave, Los Angeles, CA 90210'),
(3, 2, 159.98, 'shipped', '2025-11-17 10:17:01', '123 Main St, New York, NY 10001'),
(4, 4, 79.99, 'pending', '2025-11-17 10:17:01', '789 Pine Rd, Chicago, IL 60601'),
(5, 2, 1299.99, 'delivered', '2025-11-17 10:18:46', '123 Main St, New York, NY 10001'),
(6, 3, 299.99, 'processing', '2025-11-17 10:18:46', '456 Oak Ave, Los Angeles, CA 90210'),
(7, 2, 159.98, 'shipped', '2025-11-17 10:18:46', '123 Main St, New York, NY 10001'),
(8, 4, 79.99, 'pending', '2025-11-17 10:18:46', '789 Pine Rd, Chicago, IL 60601');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 1, 1299.99),
(2, 2, 4, 1, 299.99),
(3, 3, 3, 2, 199.99),
(4, 4, 6, 1, 79.99),
(5, 1, 1, 1, 1299.99),
(6, 2, 4, 1, 299.99),
(7, 3, 3, 2, 199.99),
(8, 4, 6, 1, 79.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `stock_quantity`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Pro', 'High-performance laptop for professionals', 1299.99, 'Electronics', 50, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(2, 'Smartphone X', 'Latest smartphone with advanced features', 899.99, 'Electronics', 100, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(3, 'Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 'Electronics', 75, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(4, 'Office Chair', 'Ergonomic office chair for comfort', 299.99, 'Furniture', 30, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(5, 'Desk Lamp', 'LED desk lamp with adjustable brightness', 49.99, 'Home', 200, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(6, 'Coffee Maker', 'Automatic coffee maker with timer', 79.99, 'Kitchen', 60, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(7, 'Backpack', 'Water-resistant backpack for travel', 59.99, 'Travel', 150, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(8, 'Water Bottle', 'Insulated stainless steel water bottle', 29.99, 'Sports', 300, NULL, 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(9, 'Laptop Pro', 'High-performance laptop for professionals', 1299.99, 'Electronics', 50, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(10, 'Smartphone X', 'Latest smartphone with advanced features', 899.99, 'Electronics', 100, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(11, 'Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 'Electronics', 75, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(12, 'Office Chair', 'Ergonomic office chair for comfort', 299.99, 'Furniture', 30, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(13, 'Desk Lamp', 'LED desk lamp with adjustable brightness', 49.99, 'Home', 200, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(14, 'Coffee Maker', 'Automatic coffee maker with timer', 79.99, 'Kitchen', 60, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(15, 'Backpack', 'Water-resistant backpack for travel', 59.99, 'Travel', 150, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(16, 'Water Bottle', 'Insulated stainless steel water bottle', 29.99, 'Sports', 300, NULL, 'active', '2025-11-17 10:17:07', '2025-11-17 10:17:07'),
(17, 'Laptop Pro', 'High-performance laptop for professionals', 1299.99, 'Electronics', 50, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(18, 'Smartphone X', 'Latest smartphone with advanced features', 899.99, 'Electronics', 100, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(19, 'Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 'Electronics', 75, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(20, 'Office Chair', 'Ergonomic office chair for comfort', 299.99, 'Furniture', 30, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(21, 'Desk Lamp', 'LED desk lamp with adjustable brightness', 49.99, 'Home', 200, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(22, 'Coffee Maker', 'Automatic coffee maker with timer', 79.99, 'Kitchen', 60, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(23, 'Backpack', 'Water-resistant backpack for travel', 59.99, 'Travel', 150, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21'),
(24, 'Water Bottle', 'Insulated stainless steel water bottle', 29.99, 'Sports', 300, NULL, 'active', '2025-11-17 10:18:21', '2025-11-17 10:18:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','manager','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'active', '2025-11-17 10:07:45', '2025-11-17 10:07:45'),
(2, 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'user', 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(3, 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'user', 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01'),
(4, 'manager1', 'manager@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager User', 'manager', 'active', '2025-11-17 10:17:01', '2025-11-17 10:17:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;