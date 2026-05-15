-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2026 at 09:42 AM
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
-- Database: `huongviet`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `number_people` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `number_of_people` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `table_id`, `booking_date`, `number_people`, `status`, `booking_time`, `number_of_people`, `note`, `created_at`) VALUES
(2, 2, 1, '2026-05-14 00:00:00', NULL, 'Đã hoàn thành', '03:40:00', 2, '', '2026-05-14 15:39:48'),
(3, 2, 1, '2026-05-14 00:00:00', NULL, 'Đã hoàn thành', '16:40:00', 1, '', '2026-05-14 15:41:00'),
(4, 2, 1, '2026-05-14 00:00:00', NULL, 'Đã hoàn thành', '17:34:00', 1, 'aa', '2026-05-14 16:34:16'),
(5, 2, 1, '2026-05-15 00:00:00', NULL, 'Đã hủy', '08:36:00', 1, '', '2026-05-15 07:36:50'),
(6, 3, 6, '2026-05-15 00:00:00', NULL, 'Chờ xác nhận', '07:38:00', 10, 'Gà', '2026-05-15 07:38:08'),
(7, 4, 4, '2026-05-15 00:00:00', NULL, 'Đã xác nhận', '08:39:00', 5, 'Lẩu hải sản', '2026-05-15 07:39:38'),
(8, 5, 1, '2026-05-24 00:00:00', NULL, 'Đã hoàn thành', '08:40:00', 2, 'Sinh nhật', '2026-05-15 07:40:42'),
(9, 6, 1, '2026-05-15 00:00:00', NULL, 'Đã hoàn thành', '08:41:00', 2, 'Gà', '2026-05-15 07:41:41');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Chưa xử lý',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `full_name`, `phone`, `email`, `address`, `subject`, `message`, `status`, `created_at`) VALUES
(2, 'Pham Dang Hoang Hieu', '0865542438', 'thptltkk55@gmail.com', 'Tan Hoa, Tan Chau, Tay Ninh', '', '', 'Đã xử lý', '2026-05-15 07:17:10'),
(3, 'Trung Dương Thành', '0938273222', 'sdfjh@gmail.com', 'Bình Dương', 'Đặt tiệc', '123', 'Đã xử lý', '2026-05-15 07:42:42'),
(4, 'Trung Dương Thành', '0938273222', 'sdfjh@gmail.com', 'Bình Dương', 'Đặt tiệc', '123', 'Chưa xử lý', '2026-05-15 08:08:33');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `customer_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `fullname`, `phone`, `customer_type`, `points`, `customer_name`, `email`, `address`, `created_at`, `status`) VALUES
(2, NULL, '0865542438', 'Khách vãng lai', 0, 'Pham Dang Hoang Hieu', 'thptltkk55@gmail.com', 'Tan Hoa, Tan Chau, Tay Ninh', '2026-05-14 15:39:48', 'Hoạt động'),
(3, NULL, '0987877654', 'Khách quen', 0, 'Lý Thị Trung Vân', '123@gmail.com', 'Bình Dương', '2026-05-15 07:38:08', 'Hoạt động'),
(4, NULL, '03726335211', 'Khách VIP', 0, 'Thẩm Văn Phong', 'tddjs@gmail.com', 'Bến Cát', '2026-05-15 07:39:38', 'Hoạt động'),
(5, NULL, '002237671622', 'Khách quen', 0, 'Trịnh Đồng Xuân', 'sdkjfh@gmail.com', 'Phú Hòa', '2026-05-15 07:40:42', 'Hoạt động'),
(6, NULL, '09938263722', 'Khách vãng lai', 0, 'Kim Vân Xuyến', 'dfg@gmail.com', 'Bình Dương', '2026-05-15 07:41:41', 'Hoạt động');

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `category_id`, `food_name`, `price`, `image`, `description`, `status`) VALUES
(1, 1, 'Gà nướng', 250000, 'ga_nuong.jpg', 'Gà nướng than hoa thơm ngon, da giòn, thịt mềm.', 'Ngừng bán'),
(2, 4, 'Lẩu hải sản', 350000, 'lau_hai_san.jpg', 'Lẩu hải sản tươi ngon với tôm, mực, nghêu và rau ăn kèm.', 'Còn bán'),
(3, 7, 'Tôm chiên', 200000, 'tom_chien.jpg', 'Tôm chiên giòn, ăn kèm tương ớt.', 'Còn bán'),
(4, 1, 'Cá lóc nướng', 280000, 'ca_loc_nuong.jpg', 'Cá lóc nướng thơm ngon, ăn kèm rau sống và bánh tráng.', 'Còn bán'),
(5, 1, 'Sườn nướng mật ong', 180000, 'suon_nuong_mat_ong.jpg', 'Sườn heo nướng mật ong đậm vị, mềm và thơm.', 'Còn bán'),
(6, 2, 'Rau muống xào tỏi', 70000, 'rau_muong_xao_toi.jpg', 'Rau muống xào tỏi dân dã, giòn xanh và thơm.', 'Còn bán'),
(7, 2, 'Mì xào hải sản', 120000, 'mi_xao_hai_san.jpg', 'Mì xào cùng tôm, mực và rau củ.', 'Còn bán'),
(8, 3, 'Mực hấp gừng', 180000, 'muc_hap_gung.jpg', 'Mực tươi hấp gừng, giữ vị ngọt tự nhiên.', 'Còn bán'),
(9, 3, 'Tôm hấp nước dừa', 220000, 'tom_hap_nuoc_dua.jpg', 'Tôm hấp nước dừa thơm ngọt, giữ vị tươi của hải sản.', 'Còn bán'),
(10, 4, 'Lẩu thái hải sản', 390000, 'lau_thai_hai_san.jpg', 'Lẩu thái chua cay với hải sản tươi.', 'Còn bán'),
(11, 4, 'Lẩu gà lá é', 320000, 'lau_ga_la_e.jpg', 'Lẩu gà lá é nóng hổi, thơm vị đặc trưng.', 'Còn bán');

-- --------------------------------------------------------

--
-- Table structure for table `food_categories`
--

CREATE TABLE `food_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_categories`
--

INSERT INTO `food_categories` (`id`, `category_name`) VALUES
(1, 'Món nướng'),
(2, 'Món xào'),
(3, 'Món hấp'),
(4, 'Lẩu'),
(5, 'Món đặc sản'),
(6, 'Món gỏi'),
(7, 'Món chiên'),
(8, 'Món canh'),
(9, 'Cơm - Mì'),
(10, 'Nước uống'),
(11, 'Món khai vị');

-- --------------------------------------------------------

--
-- Table structure for table `import_details`
--

CREATE TABLE `import_details` (
  `id` int(11) NOT NULL,
  `import_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `import_date` date NOT NULL,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `min_quantity` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Đang dùng',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `ingredient_name`, `unit`, `quantity`, `min_quantity`, `status`, `created_at`) VALUES
(1, 'Gạo', 'kg', 50.00, 10.00, 'Đang dùng', '2026-05-14 16:15:16'),
(2, 'Thịt gà', 'kg', 20.00, 5.00, 'Đang dùng', '2026-05-14 16:15:16'),
(3, 'Tôm', 'kg', 15.00, 3.00, 'Đang dùng', '2026-05-14 16:15:16'),
(4, 'Mực', 'kg', 10.00, 3.00, 'Đang dùng', '2026-05-14 16:15:16'),
(5, 'Rau muống', 'bó', 30.00, 10.00, 'Đang dùng', '2026-05-14 16:15:16'),
(6, 'Dầu ăn', 'lít', 20.00, 5.00, 'Đang dùng', '2026-05-14 16:15:16'),
(7, 'Gia vị tổng hợp', 'kg', 8.00, 2.00, 'Đang dùng', '2026-05-14 16:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `discount` decimal(12,2) DEFAULT 0.00,
  `final_amount` decimal(12,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'Tiền mặt',
  `payment_status` varchar(50) DEFAULT 'Chưa thanh toán',
  `order_status` varchar(50) DEFAULT 'Đang phục vụ',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total`, `order_date`, `status`, `table_id`, `user_id`, `total_amount`, `discount`, `final_amount`, `payment_method`, `payment_status`, `order_status`, `note`, `created_at`) VALUES
(1, 2, NULL, '2026-05-14 16:34:53', NULL, 1, 1, 560000.00, 0.00, 560000.00, 'Tiền mặt', 'Đã thanh toán', 'Đã hoàn thành', 'â', '2026-05-14 16:34:53'),
(2, NULL, NULL, '2026-05-15 07:47:34', NULL, NULL, 1, 700000.00, 0.00, 700000.00, 'Tiền mặt', 'Đã thanh toán', 'Đã hoàn thành', 'â', '2026-05-15 07:47:34'),
(3, 5, NULL, '2026-05-15 07:48:39', NULL, 6, 1, 700000.00, 0.00, 700000.00, 'Tiền mặt', 'Đã thanh toán', 'Đã hoàn thành', 'â', '2026-05-15 07:48:39'),
(4, 4, NULL, '2026-05-15 07:56:12', NULL, 2, 1, 360000.00, 0.00, 360000.00, 'Tiền mặt', 'Đã thanh toán', 'Đã hoàn thành', '', '2026-05-15 07:56:12'),
(5, 6, NULL, '2026-05-15 08:26:48', NULL, 6, 1, 1310000.00, 0.00, 1310000.00, 'Tiền mặt', 'Chưa thanh toán', 'Đang phục vụ', 'â', '2026-05-15 08:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `food_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `food_id`, `quantity`, `price`, `total_price`) VALUES
(1, 1, 4, 2, 280000, 560000.00),
(2, 2, 2, 2, 350000, 700000.00),
(3, 3, 2, 2, 350000, 700000.00),
(4, 4, 5, 2, 180000, 360000.00),
(5, 5, 4, 2, 280000, 560000.00),
(6, 5, 6, 1, 70000, 70000.00),
(7, 5, 7, 1, 120000, 120000.00),
(8, 5, 11, 1, 320000, 320000.00),
(9, 5, 7, 2, 120000, 240000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `payment_date`, `note`) VALUES
(1, 4, 'Tiền mặt', 360000.00, '2026-05-15 07:59:23', ''),
(2, 3, 'Tiền mặt', 700000.00, '2026-05-15 07:59:27', ''),
(3, 1, 'Tiền mặt', 560000.00, '2026-05-15 07:59:39', ''),
(4, 2, 'Tiền mặt', 700000.00, '2026-05-15 08:26:15', '');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `role_group` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_group`) VALUES
(1, 'Giám đốc', 'Quản lý'),
(2, 'Kế toán', ''),
(3, 'Phục vụ', 'Nhân viên'),
(4, 'Thu ngân', 'Nhân viên'),
(5, 'Trưởng phòng kế toán', 'Quản lý'),
(6, 'Trưởng phòng ẩm thực', 'Quản lý'),
(7, 'Quản lý nhà hàng', 'Quản lý'),
(8, 'Lao công', 'Nhân viên'),
(9, 'Nhân viên kho', 'Nhân viên'),
(10, 'Đầu bếp', 'Nhân viên');

-- --------------------------------------------------------

--
-- Table structure for table `service_assignments`
--

CREATE TABLE `service_assignments` (
  `id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `service_status` varchar(50) DEFAULT 'Chờ phục vụ',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Hoạt động',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `phone`, `email`, `address`, `status`, `created_at`) VALUES
(1, 'Công ty Thực phẩm Bình Dương', NULL, NULL, NULL, 'Hoạt động', '2026-05-14 16:15:16'),
(2, 'Hải sản Tươi Sống Sài Gòn', NULL, NULL, NULL, 'Hoạt động', '2026-05-14 16:15:16'),
(3, 'Rau củ sạch Thới Hòa', NULL, NULL, NULL, 'Hoạt động', '2026-05-14 16:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `tables_restaurant`
--

CREATE TABLE `tables_restaurant` (
  `id` int(11) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables_restaurant`
--

INSERT INTO `tables_restaurant` (`id`, `table_name`, `seats`, `status`, `capacity`) VALUES
(1, 'Bàn 1', NULL, 'Trống', 2),
(2, 'Bàn 2', NULL, 'Trống', 4),
(3, 'Bàn 3', NULL, 'Trống', 4),
(4, 'Bàn 4', NULL, 'Trống', 6),
(5, 'Bàn 5', NULL, 'Trống', 8),
(6, 'Bàn VIP 1', NULL, 'Đang sử dụng', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role_id`, `created_at`, `full_name`, `email`, `phone`, `status`) VALUES
(1, NULL, 'admin', '$2y$10$mFFtETLdVGImgMhtC80ZwemWdsLHszMtgwNfZCpzsTcl0pJ0aUaOq', 1, '2026-05-14 08:05:29', 'Quản trị viên', 'admin@huongviet.com', '', 'Hoạt động');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `food_categories`
--
ALTER TABLE `food_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_details`
--
ALTER TABLE `import_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_detail_import` (`import_id`),
  ADD KEY `idx_detail_ingredient` (`ingredient_id`);

--
-- Indexes for table `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_import_supplier` (`supplier_id`),
  ADD KEY `idx_import_created_by` (`created_by`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_order_id` (`order_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_assignments`
--
ALTER TABLE `service_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_table` (`table_id`),
  ADD KEY `fk_service_booking` (`booking_id`),
  ADD KEY `fk_service_order` (`order_id`),
  ADD KEY `fk_service_staff` (`staff_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tables_restaurant`
--
ALTER TABLE `tables_restaurant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `food_categories`
--
ALTER TABLE `food_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `import_details`
--
ALTER TABLE `import_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `service_assignments`
--
ALTER TABLE `service_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tables_restaurant`
--
ALTER TABLE `tables_restaurant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables_restaurant` (`id`);

--
-- Constraints for table `foods`
--
ALTER TABLE `foods`
  ADD CONSTRAINT `foods_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `food_categories` (`id`);

--
-- Constraints for table `import_details`
--
ALTER TABLE `import_details`
  ADD CONSTRAINT `fk_detail_import` FOREIGN KEY (`import_id`) REFERENCES `import_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD CONSTRAINT `fk_import_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`);

--
-- Constraints for table `service_assignments`
--
ALTER TABLE `service_assignments`
  ADD CONSTRAINT `fk_service_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_service_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_service_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_service_table` FOREIGN KEY (`table_id`) REFERENCES `tables_restaurant` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
