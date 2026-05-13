-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 06:17 PM
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
-- Database: `strendio_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','manager','staff') DEFAULT 'staff',
  `designation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `full_name`, `password`, `role`, `designation`, `created_at`, `profile_image`) VALUES
(1, 'admin', 'Jibanur Sarker', '$2y$10$zmprKk3xqi6EQBja1/AcBeBWhX3XfvtQPMKsCO10IvMFU9C0fWbcu', 'super_admin', 'System Administrator', '2026-05-11 05:50:41', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Men', 'mens'),
(2, 'Women', 'womens'),
(3, 'Kids', 'kids'),
(4, 'Accessories', 'accessories');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `order_status`, `customer_name`, `phone`, `address`) VALUES
(1, 1, '2026-05-11 08:51:35', 3400.00, NULL, NULL, NULL, NULL),
(2, 1, '2026-05-11 14:01:08', 5950.00, NULL, NULL, NULL, NULL),
(3, 1, '2026-05-11 19:42:30', 5150.00, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 2250.00),
(2, 1, 1, 1, 1150.00),
(3, 2, 14, 1, 5950.00),
(4, 3, 16, 1, 2150.00),
(5, 3, 15, 1, 1250.00),
(6, 3, 12, 1, 1750.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `stock_quantity`, `created_at`, `updated_at`, `is_deleted`) VALUES
(2, 'Denim Jeans', 'Mens Denim blue Jeans', 2250.00, '1778484899_3887.jpg', 1, 4, '2026-05-11 07:34:59', '2026-05-11 07:34:59', 0),
(3, 'Long Dress with printed', 'womens long dress', 3250.00, '1778484988_6206.jpg', 2, 3, '2026-05-11 07:36:28', '2026-05-11 07:36:28', 0),
(4, 'White T-shirt', 'Womens white t shirt for summar', 980.00, '1778485064_1826.jpg', 2, 5, '2026-05-11 07:37:44', '2026-05-11 07:37:44', 0),
(5, 'Kids boy combo', '6y kids boy combo set', 1150.00, '1778485153_3233.jpg', 3, 6, '2026-05-11 07:39:13', '2026-05-11 07:39:13', 0),
(6, 'Sky blue kids girls party set', '7y kids girls party set with skybule', 1450.00, '1778485241_7131.jpg', 3, 3, '2026-05-11 07:40:41', '2026-05-11 07:40:41', 0),
(8, 'Adidas Sports', 'Official Addidas Sports', 5900.00, '1778485396_1118.jpg', 4, 3, '2026-05-11 07:43:16', '2026-05-11 10:03:09', 0),
(9, 'Mustard Brown Mixed Cotton Polo Shirt', 'Mustard brown polo shirt in knit cotton-polyester. Features three button placket.', 950.00, '1778506234_7226.png', 1, 5, '2026-05-11 13:30:34', '2026-05-11 13:30:34', 0),
(10, 'Navy Blue Mixed Cotton Polo Shirt', 'Navy blue polo shirt in knit cotton-polyester. Features three button placket, slit on both sides and ribbed collar.', 650.00, '1778506359_7426.png', 1, 9, '2026-05-11 13:32:39', '2026-05-11 13:32:39', 0),
(11, 'White Embroidered Cashmillon Taaga Casual Top', 'White cashmillon Taaga casual top with black and golden embroidery.', 1450.00, '1778506530_1648.png', 2, 2, '2026-05-11 13:35:30', '2026-05-11 13:35:30', 0),
(12, 'Black Printed Cashmilon Taaga Casual Skirt', 'Black cashmilon Taaga skirt with golden, grey and mauve prints. Features side pocket.', 1750.00, '1778506653_1831.png', 2, 6, '2026-05-11 13:37:33', '2026-05-11 13:37:33', 0),
(13, 'Teal Green Mirpur Katan Saree', 'Teal green Mirpur katan saree with off white and copper weaving details. Comes with matching unstitched blouse piece attached at the end of saree. Blouse shown in the photo is a styling suggestion, it is not a part of the actual product.', 32000.00, '1778506761_2789.png', 2, 2, '2026-05-11 13:39:21', '2026-05-11 13:39:21', 0),
(14, 'Sage Green Printed and Embroidered Voile Shalwar Kameez', 'Sage green voile kameez with yellow, golden, red, green and beige prints. Features red embroidery with sequin work detailing. Comes with green poplin shalwar and green, off white tie-dyed and printed voile dupatta with tassels.', 5950.00, '1778506843_2598.png', 2, 3, '2026-05-11 13:40:43', '2026-05-11 13:40:43', 0),
(15, 'Rose Pink Faux Leather Heel Sandal', 'Rose pink faux leather heel sandals with stich at upper side. Step into cultural grace and make a statement at any festive event.', 1250.00, '1778506941_6001.png', 4, 6, '2026-05-11 13:42:21', '2026-05-11 13:42:21', 0),
(16, 'Black Embroidered Chosha Fabric Purse', 'Black chosha fabric purse with magnetic flap closure. Comes with embroidery and beads detailing. Features one compartment, one zip pocket and one detachable strap.', 2150.00, '1778507053_3965.png', 4, 2, '2026-05-11 13:44:13', '2026-05-11 13:44:13', 0),
(17, 'Red Printed and Embroidered Voile Skirt Top Set', 'Red voile top with white prints and embroidery. Comes with white voile skirt with red prints.', 763.00, '1778507137_1441.png', 3, 5, '2026-05-11 13:45:37', '2026-05-11 13:45:37', 0),
(18, 'Orange Color Salwar Kameez', 'Cotton Blend Exclusive Salwar kameez', 6500.00, '1778530302_7111.png', 2, 10, '2026-05-11 20:11:42', '2026-05-11 20:11:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `address`, `created_at`) VALUES
(1, 'Milon Sarker', 'milons420@yahoo.com', NULL, '$2y$10$XSV6DpBfZVhFy1GQj3LTkeOiFHsUoQ5J9bRiRKr60JdvMLHKn.LHu', 'Dhaka', '2026-05-10 18:53:11'),
(2, 'Milon Sarker', 'milons40@yahoo.com', NULL, '$2y$10$hsGs7qHCa92Gve.RCzxFdeSfFTHilfqK6KjDOoBWeXjjQ1kQZlbN2', 'mirpur', '2026-05-10 18:58:09'),
(3, 'Milon Sarker', 'milons0@yahoo.com', NULL, '$2y$10$h5rdUhhAWHxwmSs2eAhPjO5Mb9uVVbWI9xb8zM6vh6diZmuBolYMS', 'Dhaka', '2026-05-10 18:59:19'),
(4, 'Milon Sarker', 'milons42@yahoo.com', NULL, '$2y$10$lQI7JcUsNI6ay0BxaDKAquUPBF6y7MjRgoVcy7xtvatvj/K4cb7qK', 'Dhaka', '2026-05-10 19:06:34');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
