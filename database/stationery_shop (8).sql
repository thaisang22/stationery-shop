-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2026 at 11:15 AM
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
-- Database: `stationery_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `hidden_at`, `created_at`, `updated_at`) VALUES
(1, 'Thiên Long', 'thien-long', 'Thương hiệu văn phòng phẩm Thiên Long.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(2, 'Flexo', 'flexo', 'Thương hiệu sản phẩm văn phòng phẩm Flexo.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(3, 'LK', 'lk', 'Thương hiệu văn phòng phẩm LK.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `hidden_at`, `created_at`, `updated_at`) VALUES
(1, 'Bút viết', 'but-viet', 'Các loại bút phục vụ học tập và công việc.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(2, 'Vở & sổ tay', 'vo-so-tay', 'Các loại tập, vở và sổ tay phục vụ ghi chép.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(3, 'Giấy các loại', 'giay-cac-loai', 'Các loại giấy phục vụ học tập và văn phòng.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(4, 'Dụng cụ học tập', 'dung-cu-hoc-tap', 'Các dụng cụ phục vụ học tập.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(5, 'Văn phòng phẩm', 'van-phong-pham', 'Các sản phẩm và dụng cụ phục vụ công việc văn phòng.', NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `ghn_orders`
--

CREATE TABLE `ghn_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL COMMENT 'ID đơn hàng trong hệ thống',
  `order_code` varchar(50) NOT NULL COMMENT 'Mã vận đơn GHN',
  `client_order_code` varchar(50) DEFAULT NULL COMMENT 'Mã đơn hàng phía Mộc Nhiên',
  `status` varchar(50) NOT NULL DEFAULT 'ready_to_pick' COMMENT 'Trạng thái vận đơn GHN',
  `total_fee` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Phí vận chuyển GHN',
  `cod_amount` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Tiền thu hộ COD',
  `weight` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Gram',
  `length` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cm',
  `width` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cm',
  `height` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cm',
  `to_name` varchar(100) DEFAULT NULL,
  `to_phone` varchar(20) DEFAULT NULL,
  `to_address` text DEFAULT NULL,
  `to_district_id` int(10) UNSIGNED DEFAULT NULL,
  `to_ward_code` varchar(20) DEFAULT NULL,
  `expected_delivery_time` timestamp NULL DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Response GHN / dữ liệu webhook' CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `recipient_name` varchar(100) NOT NULL,
  `recipient_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `order_status` enum('PENDING','CONFIRMED','PROCESSING','SHIPPING','DELIVERED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `payment_status` enum('UNPAID','PAID','REFUNDED') NOT NULL DEFAULT 'UNPAID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `recipient_name`, `recipient_phone`, `shipping_address`, `order_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(2, 1, 66000.00, 'Võ Thái Sang', '0901234567', 'bach dang, Hồ Chí Minh', 'CONFIRMED', 'PAID', '2026-09-13 11:43:05', '2026-09-13 11:43:12'),
(3, 1, 22000.00, 'Võ Thái Sang', '0901234567', 'aaa, Hồ Chí Minh', 'PENDING', 'UNPAID', '2026-09-13 11:50:58', '2026-09-13 11:50:58'),
(4, 2, 65000.00, 'Võ Thái Sang', '0559657785', '12 trịnh đình thảo, Hồ Chí Minh', 'PENDING', 'UNPAID', '2026-09-30 08:02:49', '2026-09-30 08:02:49'),
(5, 2, 44000.00, 'Võ Thái Sang', '0559657785', '12 trịnh đình thảo, Hồ Chí Minh', 'CONFIRMED', 'PAID', '2026-09-30 17:30:49', '2026-09-30 17:48:27'),
(6, 2, 115000.00, 'Võ Thái Sang', '0559657785', '12 trịnh đình thảo, Hồ Chí Minh', 'CONFIRMED', 'PAID', '2026-09-30 17:54:06', '2026-09-30 17:56:10'),
(7, 2, 326000.00, 'Võ Thái Sang', '0559657785', '12 trịnh đình thảo, Hồ Chí Minh', 'CONFIRMED', 'PAID', '2026-09-30 18:14:40', '2026-09-30 18:14:57');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `variant_name` varchar(100) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `variant_id`, `product_name`, `variant_name`, `quantity`, `unit_price`, `sub_total`) VALUES
(1, 2, 32, 'Băng xóa kéo Thiên Long Pokémon CT-012/PKM 5mm x 10m – Khô Nhanh, Che Phủ Tốt', 'Mặc định', 3, 22000.00, 66000.00),
(2, 3, 32, 'Băng xóa kéo Thiên Long Pokémon CT-012/PKM 5mm x 10m – Khô Nhanh, Che Phủ Tốt', 'Mặc định', 1, 22000.00, 22000.00),
(3, 4, 41, 'Ream giấy A4 70 GSM Thiên Long Copy Plus PP-003', 'Mặc định', 1, 65000.00, 65000.00),
(4, 5, 32, 'Băng xóa kéo Thiên Long Pokémon CT-012/PKM 5mm x 10m – Khô Nhanh, Che Phủ Tốt', 'Mặc định', 2, 22000.00, 44000.00),
(5, 6, 40, 'Ream giấy A3 80 GSM IK Copy 500 tờ – Hàng nhập khẩu Indonesia', 'Mặc định', 1, 115000.00, 115000.00),
(6, 7, 30, 'Bìa còng LK 5cm A4', 'Xanh biển', 2, 48000.00, 96000.00),
(7, 7, 40, 'Ream giấy A3 80 GSM IK Copy 500 tờ – Hàng nhập khẩu Indonesia', 'Mặc định', 2, 115000.00, 230000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `payment_method` enum('COD','BANK_TRANSFER','MOMO','VNPAY') NOT NULL,
  `transaction_id` varchar(150) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_status` enum('PENDING','SUCCESS','FAILED','CANCELLED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `payment_status`, `gateway_response`, `created_at`, `updated_at`) VALUES
(1, 2, 'BANK_TRANSFER', 'SIM_SEPAY_1789299792', 66000.00, 'SUCCESS', '{\"id\":\"SIM_SEPAY_1789299792\",\"gateway\":\"MBBank (Simulated Test)\",\"transactionDate\":\"2026-09-13 13:43:12\",\"accountNumber\":\"0123456789\",\"code\":\"DH2\",\"content\":\"DH2 Test payment via local simulator\",\"transferType\":\"in\",\"transferAmount\":66000,\"accumulated\":10000000,\"referenceCode\":\"SIM_856045\"}', '2026-09-13 11:43:05', '2026-09-13 11:43:12'),
(2, 3, 'COD', NULL, 22000.00, 'PENDING', NULL, '2026-09-13 11:50:58', '2026-09-13 11:50:58'),
(3, 4, 'COD', NULL, 65000.00, 'PENDING', NULL, '2026-09-30 08:02:49', '2026-09-30 08:02:49'),
(4, 5, 'BANK_TRANSFER', 'SBC5E76F9C7275', 44000.00, 'SUCCESS', '{\"gateway\":\"BIDV\",\"transactionDate\":\"2026-10-01 00:48:35\",\"accountNumber\":\"0000000001\",\"subAccount\":\"SBSEPAYNLVJQGCXLXEN\",\"code\":\"DH5\",\"content\":\"DH5\",\"transferType\":\"in\",\"description\":\"DH5\",\"transferAmount\":44000,\"referenceCode\":\"SBC5E76F9C7275\",\"accumulated\":0,\"id\":35202}', '2026-09-30 17:30:49', '2026-09-30 17:48:27'),
(5, 6, 'BANK_TRANSFER', 'SBD5F9B108ACC9', 115000.00, 'SUCCESS', '{\"gateway\":\"BIDV\",\"transactionDate\":\"2026-10-01 00:56:21\",\"accountNumber\":\"0000000001\",\"subAccount\":\"SBSEPAYNLVJQGCXLXEN\",\"code\":\"DH6\",\"content\":\"DH6\",\"transferType\":\"in\",\"description\":\"DH6\",\"transferAmount\":115000,\"referenceCode\":\"SBD5F9B108ACC9\",\"accumulated\":0,\"id\":35204}', '2026-09-30 17:54:06', '2026-09-30 17:56:10'),
(6, 7, 'BANK_TRANSFER', 'SBD931C040985D', 326000.00, 'SUCCESS', '{\"gateway\":\"BIDV\",\"transactionDate\":\"2026-10-01 01:15:06\",\"accountNumber\":\"0000000001\",\"subAccount\":\"SBSEPAYNLVJQGCXLXEN\",\"code\":\"DH7\",\"content\":\"DH7\",\"transferType\":\"in\",\"description\":\"DH7\",\"transferAmount\":326000,\"referenceCode\":\"SBD931C040985D\",\"accumulated\":0,\"id\":35206}', '2026-09-30 18:14:40', '2026-09-30 18:14:57');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `post_type` enum('ABOUT','BLOG','NEWS') NOT NULL DEFAULT 'BLOG',
  `hidden_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `img_post` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `author_id`, `title`, `slug`, `content`, `post_type`, `hidden_at`, `published_at`, `created_at`, `updated_at`, `img_post`) VALUES
(1, 1, 'Bí quyết ghi chép phương pháp Cornell hiệu quả cùng sổ tay và bút gel', 'bi-quyet-ghi-chep-phuong-phap-cornell-hieu-qua', 'Phương pháp ghi chép Cornell giúp bạn tổng hợp kiến thức thông minh bằng cách chia trang giấy thành 3 phần: Ghi chú chính, Từ khóa và Tóm tắt. Để thực hiện phương pháp này hoàn hảo, bạn nên chuẩn bị một cuốn Sổ tay lò xo LK A5 160 trang kết hợp cùng bộ Bút gel Fasgel Thiên Long nhiều màu để phân loại các ý chính và ý phụ một cách trực quan.', 'BLOG', NULL, '2026-08-23 01:00:00', '2026-08-23 01:00:00', '2026-09-25 15:37:48', ''),
(2, 1, '5 mẹo ôn thi nước rút cực nhã với bút dạ quang và giấy note', '5-meo-on-thi-nuoc-rut-cuc-nha-voi-but-da-quang-va-giay-note', 'Khi thời gian ôn thi gấp rút, việc đọc lại toàn bộ sách giáo khoa là bất khả thi. Hãy áp dụng ngay kỹ thuật đánh dấu trọng tâm: sử dụng Bút dạ quang Flexo HL-03 với màu sắc tươi sáng để làm nổi bật các định nghĩa quan trọng. Đồng thời, viết các công thức khó lên Giấy ghi chú Flexo 3x3 inch và dán trực tiếp lên góc bàn học để dễ dàng ghi nhớ mọi lúc mọi nơi.', 'BLOG', NULL, '2026-08-24 02:30:00', '2026-08-24 02:30:00', '2026-09-25 15:37:47', ''),
(3, 1, 'Xây dựng góc học tập khoa học, nâng cao năng suất mỗi ngày', 'xay-dung-hoc-tap-khoa-hoc-nang-cao-nang-suat', 'Một không gian học tập bừa bộn sẽ làm giảm đi sự tập trung của bạn. Hãy bắt đầu sắp xếp lại bàn học bằng cách phân loại đề thi và tài liệu vào Bìa 8 ngăn hồ sơ Demon Slayer thật ngăn nắp. Khi giải các bài tập tính toán, đừng quên đặt chiếc Máy tính Flexio CAL-011 nhỏ gọn ngay ngắn bên cạnh để thao tác nhanh chóng và chính xác nhất[cite: 1].', 'BLOG', NULL, '2026-08-25 03:15:00', '2026-08-25 03:15:00', '2026-09-25 15:37:45', ''),
(5, NULL, 'abc', 'abc', '<h2><strong>2.1 Phát hiện Host (Host\r\nDiscovery)</strong></h2>\r\n\r\n<p class=\"MsoListParagraph\"><strong>- Mục tiêu: Xác định tất cả\r\ncác máy đang hoạt động trong phân đoạn mạng 192.168.X.0/24.</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>2.2 Quét cổng TCP và UDP\r\n(Port Scanning)</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>- Mục tiêu: Tìm các cổng\r\nTCP và UDP đang mở trên hai máy mục tiêu.</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>2.3 Phát hiện Dịch vụ và\r\nPhiên bản (Service / Version Detection)</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>- Mục tiêu: Xác định chính\r\nxác tên dịch vụ, phiên bản phần mềm đang chạy trên các cổng đã mở.</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>2.4 Phát hiện Hệ điều hành\r\n(OS Detection)</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>- Mục tiêu: Dự đoán hệ điều\r\nhành của máy mục tiêu (dựa trên đặc điểm ngăn xếp TCP/IP).</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>2.5 Sử dụng NSE Scripts\r\n(Nmap Scripting Engine)</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>- Mục tiêu: Tự động kiểm\r\ntra các lỗ hổng, cấu hình sai, hoặc thu thập thông tin bổ sung (ví dụ: liệt kê\r\nngười dùng SMB, kiểm tra lỗi SSL, v.v.).</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>&nbsp;</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>BÁO CÁO</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><b>1. Xây dựng sơ đồ và cấu\r\nhình dải mạng Lab</b></p>\r\n\r\n<p class=\"MsoListParagraph\">Dựa trên mã số sinh viên (25001521), hai số cuối là <b>21</b>. Dải mạng nội\r\nbộ được thiết lập là <b>192.168.21.0/24</b>:</p>\r\n\r\n<p class=\"MsoListParagraph\">o&nbsp;&nbsp;\r\n<b>Ubuntu Desktop (Nmap Scanner):</b> 192.168.21.10 (Máy thực hiện quét và đánh\r\ngiá mạng).</p>\r\n\r\n<p class=\"MsoListParagraph\">o&nbsp;&nbsp;\r\n<b>Ubuntu Server (Target):</b> 192.168.21.20 (Máy chủ dịch vụ Web, SSH,\r\nDNS...).</p>\r\n\r\n<p class=\"MsoListParagraph\">o&nbsp;&nbsp;\r\n<b>Windows (Target):</b> 192.168.21.30 (Máy chủ dịch vụ SMB, RDP, IIS...).</p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>Ta cần đặt địa chỉ ip cho\r\ncác máy ping thấy được nhau</strong></p>\r\n\r\n<p class=\"MsoListParagraph\"><strong>Máy Ubuntu </strong><strong>desktop</strong><strong> : 192.168.21.20</strong></p>', 'BLOG', NULL, NULL, '2026-09-16 07:07:58', '2026-09-25 15:37:46', 'images/posts/post-a86b5d7d85f8b4fc.png');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_best_seller` tinyint(1) NOT NULL DEFAULT 0,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `price`, `is_best_seller`, `hidden_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Bút gel Fasgel Thiên Long GEL-068', 'but-gel-fasgel-thien-long-gel-068', 'Bút gel Fasgel Thiên Long GEL-068 với thiết kế nhỏ gọn, mực viết êm và nhiều màu sắc.', 8500.00, 1, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(2, 1, 1, 'Bút gel Joyee Thiên Long GEL-067', 'but-gel-joyee-thien-long-gel-067', 'Bút gel Joyee Thiên Long GEL-067 với thiết kế trẻ trung, mực viết mượt.', 7000.00, 1, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(3, 5, 2, 'Dao rọc giấy Flexoffice FO-KN04B', 'dao-roc-giay-flexoffice-fo-kn04b', 'Dao rọc giấy Flexoffice FO-KN04B nhỏ gọn và tiện dụng cho học tập, văn phòng.', 12000.00, 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(4, 5, 1, 'Dao rọc giấy Thiên Long', 'dao-roc-giay-thien-long', 'Dao rọc giấy Thiên Long phục vụ học tập và công việc văn phòng.', 10000.00, 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(5, 4, 2, 'Máy tính Flexio CAL-011', 'may-tinh-flexio-cal-011', 'Máy tính Flexio CAL-011 thiết kế nhỏ gọn, phù hợp cho học sinh, sinh viên và văn phòng.', 89000.00, 1, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(6, 2, NULL, 'Tập học sinh 200 trang', 'tap-hoc-sinh-200-trang', 'Tập học sinh 200 trang phục vụ học tập và ghi chép.', 18000.00, 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(7, 1, 1, 'Bút bi Thiên Long TL-027', 'but-bi-thien-long-tl-027', 'Bút bi Thiên Long TL-027 nét viết trơn êm, mực ra đều, thích hợp cho học sinh và văn phòng.', 5000.00, 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(8, 1, 2, 'Bút dạ quang Flexo HL-03', 'but-da-quang-flexo-hl-03', 'Bút nhớ dòng Flexo HL-03 màu sắc tươi sáng, không làm lem chữ khi đánh dấu.', 12000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(9, 1, 1, 'Bút chì 2B Thiên Long GP-01', 'but-chi-2b-thien-long-gp-01', 'Bút chì gỗ 2B Thiên Long nét đậm, ruột chì dẻo dai khó gãy khi gọt.', 4000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(10, 1, 1, 'Bút xóa nước Thiên Long CP-02', 'but-xoa-nuoc-thien-long-cp-02', 'Bút xóa nước độ che phủ cao, khô nhanh, an toàn khi sử dụng.', 16000.00, 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(11, 2, 3, 'Sổ tay lò xo LK A5 160 trang', 'so-tay-lo-xo-lk-a5-160-trang', 'Sổ tay lò xo bìa cứng LK cỡ A5, giấy chống lóa mắt, thích hợp ghi chép cá nhân.', 28000.00, 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(12, 2, 1, 'Vở kẻ ngang Thiên Long 96 trang', 'vo-ke-ngang-thien-long-96-trang', 'Vở kẻ ngang Thiên Long định lượng 70gsm, nét in rõ ràng, không thấm mực.', 9500.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(13, 2, 3, 'Tập vẽ A4 LK 20 tờ', 'tap-ve-a4-lk-20-to', 'Tập vẽ A4 LK chất liệu giấy dày dặn, bám màu tốt cho màu sáp và màu nước.', 15000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(14, 3, 3, 'Giấy in A4 LK 70gsm (Tập 500 tờ)', 'giay-in-a4-lk-70gsm-tap-500-to', 'Giấy in A4 LK định lượng 70gsm độ trắng cao, thích hợp cho máy in và máy photo văn phòng.', 65000.00, 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(15, 3, 2, 'Giấy ghi chú Flexo 3x3 inch', 'giay-ghi-chu-flexo-3x3-inch', 'Giấy note Flexo dán nhớ tiện lợi, keo dán chắc chắn không để lại vết keo.', 14000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(16, 4, 1, 'Thước kẻ nhựa Thiên Long 20cm', 'thuoc-ke-nhua-thien-long-20cm', 'Thước kẻ nhựa trong suốt Thiên Long vạch chia mm rõ ràng, độ bền cao.', 6000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(17, 4, 3, 'Bộ Compa học sinh LK', 'bo-compa-hoc-sinh-lk', 'Bộ compa học sinh LK kèm chì bấm và hộp đựng tiện lợi cho môn hình học.', 35000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(18, 5, 2, 'Băng keo trong Flexo 4.8cm', 'bang-keo-trong-flexo-4-8cm', 'Băng dính trong Flexo độ dính cao, dai, dùng cho đóng gói hàng hóa và văn phòng.', 18000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(19, 5, 3, 'Bìa còng LK 5cm A4', 'bia-cong-lk-5cm-a4', 'Bìa còng lưu trữ hồ sơ LK gáy 5cm chứa được số lượng lớn tài liệu A4.', 48000.00, 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(20, 5, 2, 'Kéo văn phòng Flexo SC-02', 'keo-van-phong-flexo-sc-02', 'Kéo cắt Flexo lưỡi thép không gỉ sắc bén, tay cầm bọc nhựa êm ái.', 22000.00, 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(21, 5, 1, 'Băng xóa kéo Thiên Long Pokémon CT-012/PKM 5mm x 10m – Khô Nhanh, Che Phủ Tốt', 'bang-xoa-keo-thien-long-pokemon-ct-012-pkm-5mm-x-10m-kho-nhanh-che-phu-tot', 'Băng xóa kéo Thiên Long Pokémon CT-012/PKM thiết kế sinh động, xóa sạch và khô nhanh.', 22000.00, 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(22, 5, 1, 'Bìa 30 lá - Bìa hồ sơ - Túi lưu trữ tài liệu A4 Pokémon Thiên Long DB-014/PKM', 'bia-30-la-bia-ho-so-tui-luu-tru-tai-lieu-a4-pokemon-thien-long-db-014-pkm', 'Bìa 30 lá A4 Pokémon Thiên Long tiện lợi cho việc lưu trữ hồ sơ, tài liệu học tập.', 35000.00, 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(23, 5, 1, 'Bìa 8 ngăn hồ sơ tài liệu văn phòng - học sinh A4 Thiên Long Demon Slayer DF-006/DS', 'bia-8-ngan-ho-so-tai-lieu-van-phong-hoc-sinh-a4-thien-long-demon-slayer-df-006-ds', 'Bìa 8 ngăn Demon Slayer giúp phân loại giấy tờ, tài liệu văn phòng gọn gàng.', 28000.00, 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(24, 5, 1, 'Bút xóa nước Thiên Long 12ml CP-017/DS - Phiên bản Demon Slayer - Mẫu ngẫu nhiên', 'but-xoa-nuoc-thien-long-12ml-cp-017-ds-phien-ban-demon-slayer-mau-ngau-nhien', 'Bút xóa nước Thiên Long 12ml chất lượng cao, độ che phủ tốt.', 18000.00, 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(25, 4, 1, 'Cặp - Balo học sinh chống gù Thiên Long Baby Gon BP-016/BG', 'cap-balo-hoc-sinh-chong-gu-thien-long-baby-gon-bp-016-bg', 'Balo học sinh chống gù Thiên Long Baby Gon bảo vệ cột sống cho bé.', 380000.00, 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(26, 4, 1, 'Cặp - Balo chống gù Ergo Move Thiên Long BP-036', 'cap-balo-chong-gu-ergo-move-thien-long-bp-036', 'Balo chống gù Ergo Move Thiên Long BP-036 thiết kế công thái học cao cấp.', 450000.00, 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(27, 4, 1, 'Cặp - Balo đa năng Thiên Long BP-042/01 – 2 kiểu đeo, chống sốc laptop 15.6 inch', 'cap-balo-da-nang-thien-long-bp-042-01-2-kieu-deo-chong-soc-laptop-15-6-inch', 'Balo đa năng Thiên Long BP-042/01 tích hợp chống sốc laptop 15.6 inch.', 420000.00, 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(28, 3, NULL, 'Ream giấy A3 80 GSM IK Copy 500 tờ – Hàng nhập khẩu Indonesia', 'ream-giay-a3-80-gsm-ik-copy-500-to-hang-nhap-khau-indonesia', 'Ream giấy A3 80 GSM IK Copy 500 tờ cao cấp nhập khẩu Indonesia, bề mặt láng mịn, in ấn sắc nét.', 115000.00, 0, NULL, '2026-08-22 04:15:34', '2026-08-22 04:15:34'),
(29, 3, 1, 'Ream giấy A4 70 GSM Thiên Long Copy Plus PP-003', 'ream-giay-a4-70-gsm-thien-long-copy-plus-pp-003', 'Ream giấy A4 70 GSM Thiên Long Copy Plus PP-003 độ trắng cao, thích hợp cho máy in và máy photo.', 65000.00, 1, NULL, '2026-08-22 04:15:34', '2026-08-22 04:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `color_code` varchar(20) DEFAULT NULL,
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `hidden_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `variant_name`, `color_code`, `stock_quantity`, `sold`, `image_url`, `is_primary`, `hidden_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đen', '#000000', 100, 20, '/public/images/products/but-gel-fasgel-thien-long-gel-068-den.webp', 1, NULL, '2026-08-19 15:46:41', '2026-09-12 03:10:54'),
(2, 1, 'Đỏ', '#FF0000', 80, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-do.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(3, 1, 'Tím', '#800080', 60, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-tim.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(4, 1, 'Xanh', '#0000FF', 90, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-xanh.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(5, 2, 'Đen', '#000000', 120, 0, '/public/images/products/but-gel-joyee-thien-long-gel-067-den.webp', 1, NULL, '2026-08-19 15:46:41', '2026-08-20 15:07:06'),
(6, 2, 'Tím', '#800080', 70, 0, '/public/images/products/but-gel-joyee-thien-long-gel-067-tim.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(7, 2, 'Xanh', '#0000FF', 100, 0, '/public/images/products/but-gel-joyee-thien-long-gel-067-xanh.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(8, 3, 'Đỏ', '#FF0000', 50, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-do.webp', 1, NULL, '2026-08-19 15:46:41', '2026-08-20 15:07:11'),
(9, 3, 'Vàng', '#FFD700', 50, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-vang.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(10, 3, 'Xanh biển', '#0000FF', 40, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-xanhbien.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(11, 3, 'Xanh lá', '#008000', 40, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-xanhla.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(12, 4, 'Mặc định', NULL, 100, 0, '/public/images/products/dao-roc-giay-thien-long-01.webp', 1, NULL, '2026-08-19 15:46:41', '2026-08-20 15:07:18'),
(13, 5, 'Tím', '#800080', 30, 0, '/public/images/products/may-tinh-tien-so-chong-truot-flexio-cal-011-tim.webp', 1, NULL, '2026-08-19 15:46:41', '2026-08-20 15:07:23'),
(14, 5, 'Trà sữa', '#D2B48C', 30, 0, '/public/images/products/may-tinh-tien-so-chong-truot-flexio-cal-011-trasua.webp', 0, NULL, '2026-08-19 15:46:41', '2026-08-19 15:46:41'),
(15, 6, 'Mặc định', NULL, 200, 0, '/public/images/products/tap-hoc-sinh-200-trang-01.webp', 1, NULL, '2026-08-19 15:46:41', '2026-08-20 15:07:26'),
(16, 7, 'Xanh', '#0000FF', 150, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-xanh.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(17, 7, 'Đen', '#000000', 100, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-den.webp', 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(18, 7, 'Đỏ', '#FF0000', 80, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-do.webp', 0, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(19, 8, 'Vàng', '#FFD700', 90, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-vang.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(20, 9, 'Mặc định', NULL, 300, 0, '/public/images/products/but-gel-joyee-thien-long-gel-067-den.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(21, 10, 'Mặc định', NULL, 110, 0, '/public/images/products/but-gel-fasgel-thien-long-gel-068-tim.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(22, 11, 'Xanh biển', '#0000FF', 45, 0, '/public/images/products/tap-hoc-sinh-200-trang-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(23, 12, 'Mặc định', NULL, 250, 0, '/public/images/products/tap-hoc-sinh-200-trang-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(24, 13, 'Mặc định', NULL, 80, 0, '/public/images/products/tap-hoc-sinh-200-trang-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(25, 14, 'Mặc định', NULL, 60, 0, '/public/images/products/tap-hoc-sinh-200-trang-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(26, 15, 'Vàng', '#FFD700', 130, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-vang.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(27, 16, 'Trong suốt', NULL, 180, 0, '/public/images/products/dao-roc-giay-thien-long-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(28, 17, 'Xanh lá', '#008000', 40, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-xanhla.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(29, 18, 'Mặc định', NULL, 95, 0, '/public/images/products/dao-roc-giay-thien-long-01.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(30, 19, 'Xanh biển', '#0000FF', 33, 2, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-xanhbien.webp', 1, NULL, '2026-08-21 04:30:02', '2026-09-30 18:14:40'),
(31, 20, 'Đỏ', '#FF0000', 70, 0, '/public/images/products/dao-roc-giay-flexoffice-fo-kn04b-do.webp', 1, NULL, '2026-08-21 04:30:02', '2026-08-21 04:30:02'),
(32, 21, 'Mặc định', NULL, 94, 6, '/public/images/products/bang-xoa-keo-thien-long-pokemon-ct-012-pkm-5mm-x-10m-kho-nhanh-che-phu-tot.webp', 1, NULL, '2026-08-22 04:06:50', '2026-09-30 17:30:49'),
(33, 22, 'Mặc định', NULL, 100, 0, '/public/images/products/bia-30-la-bia-ho-so-tui-luu-tru-tai-lieu-a4-pokemon-thien-long-db-014-pkm.jpg', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(34, 23, 'Mặc định', NULL, 100, 0, '/public/images/products/bia-8-ngan-ho-so-tai-lieu-van-phong-hoc-sinh-a4-thien-long-demon-slayer-df-006-ds.webp', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(35, 24, 'Mẫu ngẫu nhiên', NULL, 100, 0, '/public/images/products/but-xoa-nuoc-thien-long-12ml-cp-017-ds-phien-ban-demon-slayer-mau-ngau-nhien.webp', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(36, 25, 'Mặc định', NULL, 50, 0, '/public/images/products/cap-balo-hoc-sinh-chong-gu-thien-long-baby-gon-bp-016-bg.webp', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(37, 26, 'Mặc định', NULL, 50, 0, '/public/images/products/cap-balo-chong-gu-ergo-move-thien-long-bp-036.webp', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(38, 27, 'Mẫu 1', NULL, 50, 0, '/public/images/products/cap-balo-da-nang-thien-long-bp-042-01-2-kieu-deo-chong-soc-laptop-15-6-inch.webp', 1, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(39, 27, 'Mẫu 2', NULL, 50, 0, '/public/images/products/cap-balo-da-nang-thien-long-bp-042-01-2-kieu-deo-chong-soc-laptop-15-6-inch-01.webp', 0, NULL, '2026-08-22 04:06:50', '2026-08-22 04:06:50'),
(40, 28, 'Mặc định', NULL, 97, 3, '/public/images/products/ream-giay-a3-80-gsm-ik-copy-500-to-hang-nhap-khau-indonesia.webp', 1, NULL, '2026-08-22 04:15:34', '2026-09-30 18:14:40'),
(41, 29, 'Mặc định', NULL, 149, 1, '/public/images/products/ream-giay-a4-70-gsm-thien-long-copy-plus-pp-003-01.webp', 1, NULL, '2026-08-22 04:15:34', '2026-09-30 08:02:49');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`id`, `user_id`, `variant_id`, `quantity`, `created_at`, `updated_at`) VALUES
(3, 1, 32, 1, '2026-09-13 12:08:36', '2026-09-13 12:08:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` enum('ADMIN','CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `full_name`, `email`, `password_hash`, `phone_number`, `activated_at`, `banned_at`, `created_at`, `updated_at`) VALUES
(1, 'CUSTOMER', 'Võ Thái Sang', 'thaisang@gmail.com', '$2y$10.w2M73cK9XU1V3A.9S0UO9Y7.8X9g.Y8x9X9g8x9X9g8x9X9g8x9', '0901234567', '2026-09-13 11:42:31', NULL, '2026-09-13 11:42:31', '2026-09-13 11:42:31'),
(2, 'CUSTOMER', 'Võ Thái Sang', 'thaisang06072004@gmail.com', '$2y$10$4p4PiAMhRNqPMqJ0vsZguug4WoBdbBrINPiH/UN2Um1hyqAD6spxG', '0559657785', NULL, NULL, '2026-09-27 17:39:11', '2026-09-30 08:13:08'),
(3, 'CUSTOMER', 'Võ Thái Sang', 'hocthu1@gmail.com', '$2y$10$Ye1UVCoNtSSmoKQrcHiPteJIhEqgBeL4NmYnl36GMzyTXtKPK8YeS', NULL, NULL, NULL, '2026-09-27 18:04:12', '2026-09-27 18:04:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_brands_hidden_at` (`hidden_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categories_hidden_at` (`hidden_at`);

--
-- Indexes for table `ghn_orders`
--
ALTER TABLE `ghn_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ghn_order_code` (`order_code`),
  ADD UNIQUE KEY `uq_ghn_order_id` (`order_id`),
  ADD KEY `idx_ghn_status` (`status`),
  ADD KEY `idx_ghn_created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`order_status`),
  ADD KEY `idx_orders_payment_status` (`payment_status`),
  ADD KEY `idx_orders_created_at` (`created_at`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_details_order` (`order_id`),
  ADD KEY `idx_order_details_variant` (`variant_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_order` (`order_id`),
  ADD KEY `idx_payments_status` (`payment_status`),
  ADD KEY `idx_payments_transaction` (`transaction_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_posts_author` (`author_id`),
  ADD KEY `idx_posts_type` (`post_type`),
  ADD KEY `idx_posts_hidden_at` (`hidden_at`),
  ADD KEY `idx_posts_published_at` (`published_at`),
  ADD KEY `idx_posts_created_at` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_brand` (`brand_id`),
  ADD KEY `idx_products_price` (`price`),
  ADD KEY `idx_products_best_seller` (`is_best_seller`),
  ADD KEY `idx_products_hidden_at` (`hidden_at`),
  ADD KEY `idx_products_created_at` (`created_at`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_variants_product` (`product_id`),
  ADD KEY `idx_variants_stock` (`stock_quantity`),
  ADD KEY `idx_variants_hidden_at` (`hidden_at`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_user_variant` (`user_id`,`variant_id`),
  ADD KEY `idx_cart_user` (`user_id`),
  ADD KEY `idx_cart_variant` (`variant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_activated_at` (`activated_at`),
  ADD KEY `idx_users_banned_at` (`banned_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ghn_orders`
--
ALTER TABLE `ghn_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ghn_orders`
--
ALTER TABLE `ghn_orders`
  ADD CONSTRAINT `fk_ghn_orders_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_details_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
