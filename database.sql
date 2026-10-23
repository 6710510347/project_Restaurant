-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 07:08 PM
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
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `stock_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `low_stock_threshold` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `name`, `unit`, `stock_qty`, `low_stock_threshold`) VALUES
(1, 'ช็อตกาแฟ', 'มิลลิลิตร', 5000.00, 1000.00),
(2, 'ผงมัทฉะ', 'กรัม', 5000.00, 1000.00),
(3, 'ผงโกโก้', 'กรัม', 5000.00, 1000.00),
(4, 'ใบชา', 'กรัม', 5000.00, 1000.00),
(5, 'น้ำมะนาว', 'มิลลิลิตร', 2000.00, 500.00),
(6, 'น้ำเชื่อมกลิ่นสละ', 'มิลลิลิตร', 5000.00, 1420.00),
(7, 'นมสด', 'มิลลิลิตร', 10000.00, 3000.00),
(8, 'น้ำดื่ม', 'มิลลิลิตร', 5000.00, 3000.00),
(9, 'นมข้นหวาน', 'มิลลิลิตร', 5000.00, 2000.00),
(10, 'น้ำตาล', 'กรัม', 10000.00, 3000.00),
(11, 'น้ำตาลไอซิ่ง', 'กรัม', 5000.00, 1500.00),
(12, 'กลิ่นวนิลา', 'มิลลิลิตร', 600.00, 60.00),
(13, 'แป้งสาลี', 'กรัม', 6500.00, 2000.00),
(14, 'แป้งเค้ก', 'กรัม', 6500.00, 2000.00),
(15, 'ช็อคโคแลต', 'กรัม', 6500.00, 2000.00),
(16, 'ผงฟู', 'กรัม', 1000.00, 300.00),
(17, 'เนยจืด', 'กรัม', 6000.00, 2000.00),
(18, 'ไข่ไก่', 'ฟอง', 72.00, 24.00),
(19, 'ไข่แดง', 'ฟอง', 72.00, 24.00),
(20, 'ไข่ขาว', 'ฟอง', 72.00, 24.00),
(21, 'วิปครีม', 'มิลลิลิตร', 3500.00, 1000.00),
(22, 'สตรอว์เบอร์รี่', 'กรัม', 2000.00, 500.00),
(23, 'มิกซ์เบอร์รี่', 'กรัม', 1000.00, 300.00),
(24, 'กล้วย', 'ลูก', 50.00, 10.00),
(25, 'ซอสคาราเมล', 'มิลลิลิตร', 2000.00, 500.00),
(26, 'แครกเกอร์', 'กรัม', 1500.00, 300.00),
(27, 'เลดี้ฟิงเกอร์', 'กรัม', 2000.00, 500.00),
(28, 'มาสคาโปนชีส', 'กรัม', 2000.00, 500.00),
(29, 'เกลือ', 'กรัม', 1000.00, 500.00),
(30, 'ข้าวสาร', 'กรัม', 10000.00, 3000.00),
(31, 'ไก่บด', 'กรัม', 2000.00, 500.00),
(32, 'เนื้อวัว', 'กรัม', 3000.00, 1000.00),
(33, 'ใบกะเพรา', 'กรัม', 500.00, 100.00),
(34, 'กระเทียม', 'กรัม', 1000.00, 300.00),
(35, 'พริก', 'กรัม', 1000.00, 300.00),
(36, 'หอมใหญ่', 'กรัม', 650.00, 200.00),
(37, 'แครอท', 'กรัม', 500.00, 200.00),
(38, 'มะเขือเทศ', 'กรัม', 600.00, 200.00),
(39, 'ผักสลัด', 'กรัม', 1200.00, 500.00),
(40, 'มันฝรั่ง', 'กรัม', 1200.00, 200.00),
(41, 'ซอสมะเขือเทศ', 'มิลลิลิตร', 1200.00, 300.00),
(42, 'แตงกวา', 'กรัม', 1200.00, 200.00),
(43, 'ซอสปรุงรส', 'มิลลิลิตร', 3000.00, 1000.00),
(44, 'ซีอิ๊วขาว', 'มิลลิลิตร', 1000.00, 500.00),
(45, 'ซอสหอยนางรม', 'มิลลิลิตร', 1000.00, 500.00),
(46, 'น้ำสลัด', 'มิลลิลิตร', 1000.00, 500.00),
(47, 'ซอสเกรวี่', 'มิลลิลิตร', 700.00, 200.00),
(48, 'น้ำมัน', 'มิลลิลิตร', 7000.00, 2000.00),
(49, 'เส้นสปาเก็ตตี้', 'กรัม', 1000.00, 500.00),
(50, 'เห็ดแชมปิญอง', 'กรัม', 700.00, 500.00),
(51, 'น้ำแข็ง', 'กรัม', 10000.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `menu_ingredients`
--

CREATE TABLE `menu_ingredients` (
  `menu_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `qty_used` decimal(10,2) NOT NULL,
  `temp_type` enum('all','hot','cold') NOT NULL DEFAULT 'all',
  `is_sweetener` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_ingredients`
--

INSERT INTO `menu_ingredients` (`menu_id`, `ingredient_id`, `qty_used`, `temp_type`, `is_sweetener`) VALUES
(1, 1, 18.00, 'all', 0),
(1, 8, 120.00, 'hot', 0),
(1, 8, 100.00, 'cold', 0),
(1, 10, 10.00, 'all', 1),
(1, 51, 150.00, 'cold', 0),
(2, 1, 18.00, 'all', 0),
(2, 7, 150.00, 'hot', 0),
(2, 7, 120.00, 'cold', 0),
(2, 9, 10.00, 'all', 1),
(2, 10, 5.00, 'all', 1),
(2, 51, 150.00, 'cold', 0),
(3, 1, 18.00, 'all', 0),
(3, 7, 50.00, 'hot', 0),
(3, 7, 60.00, 'cold', 0),
(3, 9, 20.00, 'all', 1),
(3, 10, 5.00, 'all', 1),
(3, 51, 150.00, 'cold', 0),
(4, 2, 3.00, 'hot', 0),
(4, 2, 4.00, 'cold', 0),
(4, 7, 150.00, 'hot', 0),
(4, 7, 120.00, 'cold', 0),
(4, 8, 30.00, 'all', 0),
(4, 9, 10.00, 'hot', 1),
(4, 9, 20.00, 'cold', 1),
(4, 10, 5.00, 'all', 1),
(4, 51, 150.00, 'cold', 0),
(5, 2, 3.00, 'hot', 0),
(5, 2, 4.00, 'cold', 0),
(5, 8, 180.00, 'hot', 0),
(5, 8, 120.00, 'cold', 0),
(5, 10, 10.00, 'all', 1),
(5, 51, 150.00, 'cold', 0),
(6, 4, 8.00, 'hot', 0),
(6, 4, 10.00, 'cold', 0),
(6, 5, 15.00, 'hot', 0),
(6, 5, 25.00, 'cold', 0),
(6, 8, 180.00, 'hot', 0),
(6, 8, 150.00, 'cold', 0),
(6, 10, 20.00, 'all', 1),
(6, 51, 150.00, 'cold', 0),
(7, 4, 8.00, 'hot', 0),
(7, 4, 10.00, 'cold', 0),
(7, 7, 50.00, 'all', 0),
(7, 8, 150.00, 'hot', 0),
(7, 8, 120.00, 'cold', 0),
(7, 9, 25.00, 'all', 1),
(7, 10, 10.00, 'all', 1),
(7, 51, 150.00, 'cold', 0),
(8, 6, 20.00, 'hot', 0),
(8, 6, 30.00, 'cold', 0),
(8, 7, 150.00, 'hot', 0),
(8, 7, 120.00, 'cold', 0),
(8, 8, 30.00, 'all', 0),
(8, 9, 20.00, 'all', 1),
(8, 51, 150.00, 'cold', 0),
(9, 7, 180.00, 'hot', 0),
(9, 7, 150.00, 'cold', 0),
(9, 8, 30.00, 'cold', 0),
(9, 9, 20.00, 'all', 1),
(9, 10, 10.00, 'all', 1),
(9, 51, 150.00, 'cold', 0),
(10, 3, 12.00, 'hot', 0),
(10, 3, 15.00, 'cold', 0),
(10, 7, 160.00, 'hot', 0),
(10, 7, 120.00, 'cold', 0),
(10, 8, 30.00, 'all', 0),
(10, 9, 25.00, 'all', 1),
(10, 10, 5.00, 'all', 1),
(10, 51, 150.00, 'cold', 0),
(11, 3, 10.00, 'all', 0),
(11, 10, 25.00, 'all', 0),
(11, 12, 1.00, 'all', 0),
(11, 13, 35.00, 'all', 0),
(11, 15, 20.00, 'all', 0),
(11, 16, 1.00, 'all', 0),
(11, 17, 20.00, 'all', 0),
(11, 18, 1.00, 'all', 0),
(11, 23, 20.00, 'all', 0),
(11, 29, 0.50, 'all', 0),
(12, 5, 2.00, 'all', 0),
(12, 7, 15.00, 'all', 0),
(12, 11, 20.00, 'all', 0),
(12, 12, 1.00, 'all', 0),
(12, 14, 35.00, 'all', 0),
(12, 17, 10.00, 'all', 0),
(12, 19, 1.00, 'all', 0),
(12, 20, 1.00, 'all', 0),
(12, 21, 50.00, 'all', 0),
(12, 22, 50.00, 'all', 0),
(12, 29, 0.50, 'all', 0),
(13, 1, 30.00, 'all', 0),
(13, 3, 3.00, 'all', 0),
(13, 8, 30.00, 'all', 0),
(13, 10, 15.00, 'all', 0),
(13, 11, 10.00, 'all', 0),
(13, 12, 1.00, 'all', 0),
(13, 19, 1.00, 'all', 0),
(13, 21, 40.00, 'all', 0),
(13, 27, 40.00, 'all', 0),
(13, 28, 50.00, 'all', 0),
(13, 29, 0.50, 'all', 0),
(14, 3, 2.00, 'all', 0),
(14, 10, 5.00, 'all', 0),
(14, 15, 10.00, 'all', 0),
(14, 17, 15.00, 'all', 0),
(14, 21, 40.00, 'all', 0),
(14, 24, 1.00, 'all', 0),
(14, 25, 30.00, 'all', 0),
(14, 26, 35.00, 'all', 0),
(15, 3, 8.00, 'all', 0),
(15, 10, 30.00, 'all', 0),
(15, 12, 1.00, 'all', 0),
(15, 13, 25.00, 'all', 0),
(15, 15, 30.00, 'all', 0),
(15, 16, 0.50, 'all', 0),
(15, 17, 20.00, 'all', 0),
(15, 18, 1.00, 'all', 0),
(15, 29, 0.50, 'all', 0),
(16, 18, 2.00, 'all', 0),
(16, 30, 150.00, 'all', 0),
(16, 43, 5.00, 'all', 0),
(16, 48, 15.00, 'all', 0),
(17, 10, 3.00, 'all', 0),
(17, 18, 1.00, 'all', 0),
(17, 30, 150.00, 'all', 0),
(17, 31, 120.00, 'all', 0),
(17, 33, 15.00, 'all', 0),
(17, 34, 5.00, 'all', 0),
(17, 35, 5.00, 'all', 0),
(17, 43, 3.00, 'all', 0),
(17, 44, 5.00, 'all', 0),
(17, 45, 10.00, 'all', 0),
(17, 48, 15.00, 'all', 0),
(18, 8, 800.00, 'all', 0),
(18, 10, 5.00, 'all', 0),
(18, 31, 60.00, 'all', 0),
(18, 34, 5.00, 'all', 0),
(18, 36, 20.00, 'all', 0),
(18, 37, 20.00, 'all', 0),
(18, 38, 40.00, 'all', 0),
(18, 41, 80.00, 'all', 0),
(18, 43, 5.00, 'all', 0),
(18, 48, 10.00, 'all', 0),
(18, 49, 50.00, 'all', 0),
(18, 50, 50.00, 'all', 0),
(19, 37, 20.00, 'all', 0),
(19, 38, 15.00, 'all', 0),
(19, 39, 100.00, 'all', 0),
(19, 42, 20.00, 'all', 0),
(19, 46, 30.00, 'all', 0),
(20, 17, 5.00, 'all', 0),
(20, 29, 2.00, 'all', 0),
(20, 32, 120.00, 'all', 0),
(20, 37, 30.00, 'all', 0),
(20, 40, 80.00, 'all', 0),
(20, 47, 25.00, 'all', 0),
(20, 48, 5.00, 'all', 0),
(20, 50, 30.00, 'all', 0);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_hot` decimal(10,2) DEFAULT NULL,
  `price_cold` decimal(10,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`menu_id`, `name`, `category`, `description`, `image_url`, `price`, `price_hot`, `price_cold`, `is_available`, `is_deleted`) VALUES
(1, 'อเมริกาโน่', 'เครื่องดื่ม', 'อเมริกาโน่ กาแฟสด ขั่วเข้ม', 'https://images.pexels.com/photos/18238545/pexels-photo-18238545.jpeg', 50.00, 50.00, 60.00, 1, 0),
(2, 'ลาเต้', 'เครื่องดื่ม', 'กาแฟผสมนม นุ่มละมุน หอมกลมกล่อม', 'https://images.pexels.com/photos/11814075/pexels-photo-11814075.jpeg', 50.00, 50.00, 60.00, 1, 0),
(3, 'เอสเพรสโซ่', 'เครื่องดื่ม', 'ช็อตกาแฟเข้มข้น หอมกาแฟชัด', 'https://images.pexels.com/photos/26238864/pexels-photo-26238864.jpeg', 50.00, 50.00, 60.00, 1, 0),
(4, 'มัทฉะลาเต้', 'เครื่องดื่ม', 'มัทฉะนมหอม รสชาติกลมกล่อม', 'https://images.pexels.com/photos/34334424/pexels-photo-34334424.jpeg', 50.00, 50.00, 60.00, 1, 0),
(5, 'เคลียร์มัทฉะ', 'เครื่องดื่ม', 'มัทฉะเข้มข้น หอม ติดขมปลายลิ้น', 'https://images.pexels.com/photos/32713602/pexels-photo-32713602.jpeg', 50.00, 50.00, 60.00, 1, 0),
(6, 'ชามะนาว', 'เครื่องดื่ม', 'ชาหอมสดชื่น เปรี้ยวมะนาวพอดี', 'https://images.pexels.com/photos/11009226/pexels-photo-11009226.jpeg', 50.00, 50.00, 60.00, 1, 0),
(7, 'ชาไทย', 'เครื่องดื่ม', 'ชาหอมเข้มข้นแบบใต้แท้ๆ ดื่มแล้วสดชื่น', 'https://images.pexels.com/photos/33241808/pexels-photo-33241808.jpeg', 50.00, 50.00, 60.00, 1, 0),
(8, 'นมชมพู', 'เครื่องดื่ม', 'นมสดหอมกลิ่นสละ สีชมพูน่ารัก เด็กๆดื่มง่าย', 'https://images.pexels.com/photos/31540470/pexels-photo-31540470.jpeg', 50.00, 50.00, 60.00, 1, 0),
(9, 'นมสด', 'เครื่องดื่ม', 'นมสดหอมมัน สดชื่น', 'https://images.pexels.com/photos/18635174/pexels-photo-18635174.jpeg', 50.00, 50.00, 60.00, 1, 0),
(10, 'โกโก้', 'เครื่องดื่ม', 'โกโก้เข้มข้น หอมช็อคโคแลต', 'https://images.pexels.com/photos/12642346/pexels-photo-12642346.jpeg', 50.00, 50.00, 60.00, 1, 0),
(11, 'เค้กช็อคโคแลต', 'ของหวาน', 'รสชาติเข้มข้นของช็อคโคแลตแท้ มีความนุ่มของเค้ก', 'https://images.pexels.com/photos/12286226/pexels-photo-12286226.jpeg', 60.00, NULL, NULL, 1, 0),
(12, 'สตรอว์เบอร์รี่ช็อตเค้ก', 'ของหวาน', 'เค้กเนื้อนุ่ม ครีมหอมหวาน ตัดเปรี้ยวนิดๆด้วยสตรอว์เบอร์รี่สด', 'https://images.pexels.com/photos/6565736/pexels-photo-6565736.jpeg', 75.00, NULL, NULL, 1, 0),
(13, 'ทีรามิสุ', 'ของหวาน', 'เนื้อนุ่มละลายในปาก กลิ่นหอมกาแฟ', 'https://images.pexels.com/photos/27305271/pexels-photo-27305271.jpeg', 90.00, NULL, NULL, 1, 0),
(14, 'บานอฟฟี่', 'ของหวาน', 'หอมกลิ่นกล้วยสดๆ ครีมนุ่มละมุน เพิ่มความกรอบด้วยฐานบิสกิต', 'https://images.pexels.com/photos/36515964/pexels-photo-36515964.jpeg', 60.00, NULL, NULL, 1, 0),
(15, 'บราวนี่', 'ของหวาน', 'เนื้อหนึบ เข้มข้น หอมช็อคโคแลต', 'https://images.pexels.com/photos/15033469/pexels-photo-15033469.jpeg', 60.00, NULL, NULL, 1, 0),
(16, 'ข้าวไข่เจียว', 'อาหาร', 'ไข่เจียวฟูกรอบ เสิร์ฟคู่กับข้าวสวยร้อนๆ', 'https://images.pexels.com/photos/35049886/pexels-photo-35049886.jpeg', 40.00, NULL, NULL, 1, 0),
(17, 'ข้าวกะเพราไก่ไข่ดาว', 'อาหาร', 'ไก่ผัดหอมกลิ่นกะเพรา เสิร์ฟคู่กับข้าวสวยร้อนๆ และไข่ดาว', 'https://images.pexels.com/photos/26390890/pexels-photo-26390890.jpeg', 50.00, NULL, NULL, 1, 0),
(18, 'สปาเก็ตตี้ซอสมะเขือเทศ', 'อาหาร', 'เส้นเหนียวนุ่ม คลุกซอสมะเขือเทศเข้มข้น', 'https://images.pexels.com/photos/32632539/pexels-photo-32632539.jpeg', 65.00, NULL, NULL, 1, 0),
(19, 'สลัดผัก', 'อาหาร', 'ผักสดกรอบนานาชนิด เสิร์ฟพร้อมน้ำสลัด', 'https://images.pexels.com/photos/32754767/pexels-photo-32754767.jpeg', 60.00, NULL, NULL, 1, 0),
(20, 'สเต็กเนื้อ', 'อาหาร', 'ได้สัมผัสความนุ่ม และชุ่มฉ่ำของเนื้อ', 'https://images.pexels.com/photos/16845482/pexels-photo-16845482.jpeg', 75.00, NULL, NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `order_type` enum('dine_in_reserved','dine_in_walkin','takeaway','pre_order') NOT NULL,
  `order_channel` enum('counter','web_app') NOT NULL DEFAULT 'counter',
  `payment_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pickup_time` datetime DEFAULT NULL,
  `status` enum('pending','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `redemption_id` int(11) DEFAULT NULL
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `guest_name`, `table_id`, `reservation_id`, `order_type`, `order_channel`, `payment_status`, `created_at`, `pickup_time`, `status`, `discount_amount`, `redemption_id`) VALUES
(1, 1, NULL, NULL, NULL, 'pre_order', 'web_app', 'unpaid', '2026-09-20 18:29:49', NULL, 'pending', 0.00, NULL),
(2, 5, NULL, 2, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 15:32:00', NULL, 'pending', 0.00, NULL),
(3, 5, NULL, 2, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 15:36:14', NULL, 'pending', 0.00, NULL),
(4, 5, NULL, 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 15:40:13', NULL, 'pending', 0.00, NULL),
(5, 5, NULL, 9, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 16:15:48', NULL, 'pending', 0.00, NULL),
(6, 5, NULL, 10, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 16:16:23', NULL, 'pending', 0.00, NULL),
(7, 5, NULL, 10, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 16:16:41', NULL, 'pending', 0.00, NULL),
(8, 5, NULL, NULL, NULL, 'pre_order', 'web_app', 'unpaid', '2026-09-22 16:26:10', '2026-09-23 12:00:00', 'pending', 0.00, NULL),
(9, 5, NULL, 2, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 16:49:00', '2026-09-23 12:00:00', 'pending', 0.00, NULL),
(10, 2, NULL, 5, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-22 17:01:23', NULL, 'pending', 0.00, NULL),
(11, 8, NULL, 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 16:22:33', NULL, 'pending', 0.00, NULL),
(12, 8, NULL, NULL, NULL, 'pre_order', 'web_app', 'unpaid', '2026-09-27 16:22:54', '2026-09-30 12:00:00', 'pending', 0.00, NULL),
(13, 8, NULL, NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 16:25:54', NULL, 'pending', 0.00, NULL),
(14, 7, NULL, 8, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 17:24:18', NULL, 'preparing', 0.00, NULL),
(15, 7, NULL, 8, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 17:36:31', NULL, 'pending', 0.00, NULL),
(16, 3, NULL, NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:00:59', NULL, 'pending', 0.00, NULL),
(17, 7, NULL, NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:18:21', NULL, 'pending', 0.00, NULL),
(18, 7, NULL, NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:27:11', NULL, 'cancelled', 0.00, NULL),
(19, 7, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:34:05', NULL, 'completed', 0.00, NULL),
(20, 7, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:43:45', NULL, 'pending', 0.00, NULL),
(21, 7, 'ก้า', 4, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 18:43:57', NULL, 'pending', 0.00, NULL),
(22, 7, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 18:55:11', NULL, 'pending', 0.00, NULL),
(23, 7, 'ก้า', 4, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 18:55:30', NULL, 'pending', 0.00, NULL),
(24, 7, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-09-27 19:00:34', NULL, 'pending', 0.00, NULL),
(25, 7, 'ก้า', 5, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 19:00:55', NULL, 'pending', 0.00, NULL),
(26, 7, 'ก้า', 9, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-09-27 19:03:43', NULL, 'completed', 0.00, NULL),
(27, 2, 'kla', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-02 16:52:12', NULL, 'preparing', 0.00, NULL),
(28, 2, 'ก้า', 2, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-12 15:27:08', NULL, 'pending', 0.00, NULL),
(29, 7, 'ก้า', 10, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-12 15:34:55', NULL, 'preparing', 0.00, NULL),
(30, 7, 'กานต์', 8, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-12 16:16:48', NULL, 'cancelled', 0.00, NULL),
(31, 10, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-20 15:18:33', NULL, 'completed', 0.00, NULL),
(32, 11, 'gift', 4, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 14:07:39', NULL, 'completed', 0.00, NULL),
(33, 2, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 14:49:17', NULL, 'completed', 0.00, NULL),
(34, 2, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 14:51:44', NULL, 'completed', 0.00, NULL),
(35, 11, 'gift', 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 16:22:14', NULL, 'completed', 0.00, NULL),
(36, 11, 'gift', 3, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 16:27:24', NULL, 'completed', 0.00, NULL),
(37, 11, 'gift', 7, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 16:29:13', NULL, 'completed', 0.00, NULL),
(38, 7, 'ก้า', 2, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 17:34:53', NULL, 'completed', 0.00, NULL),
(39, 11, 'gift', 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 19:17:04', NULL, 'completed', 0.00, NULL),
(40, 11, 'gift', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 19:21:37', NULL, 'completed', 0.00, NULL),
(41, 11, 'gift', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 19:32:21', NULL, 'completed', 20.00, 2),
(42, 11, 'gift', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 19:38:16', NULL, 'pending', 60.00, 3),
(43, 2, 'ก้า', 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 19:39:58', NULL, 'preparing', 15.00, 1),
(44, 2, 'ก้า', 9, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 19:43:51', NULL, 'completed', 0.00, NULL),
(45, 2, 'ก้า', 8, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 20:12:23', NULL, 'preparing', 0.00, 4),
(46, 2, 'ก้า', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-21 21:27:42', NULL, 'cancelled', 0.00, NULL),
(47, 2, 'ก้า', 10, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 21:35:31', NULL, 'completed', 0.00, NULL),
(48, 11, 'gift', 11, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-21 23:16:38', NULL, 'completed', 0.00, NULL),
(49, 2, 'gift', 1, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-22 14:59:04', NULL, 'completed', 20.00, 5),
(50, 2, 'gift', NULL, NULL, 'takeaway', 'web_app', 'unpaid', '2026-10-22 15:37:44', NULL, 'completed', 0.00, NULL),
(51, 11, 'ก้า', 10, NULL, 'dine_in_walkin', 'web_app', 'unpaid', '2026-10-22 17:05:05', NULL, 'completed', 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `cook_status` enum('pending','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `item_status` enum('pending','ready','serve') NOT NULL DEFAULT 'pending',
  `temperature` enum('hot','cold') DEFAULT NULL,
  `sweetness` enum('less','normal','extra') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `menu_id`, `qty`, `unit_price`, `cook_status`, `item_status`, `temperature`, `sweetness`) VALUES
(11, 10, 11, 1, 60.00, 'pending', 'pending', NULL, NULL),
(12, 10, 3, 1, 60.00, 'pending', 'pending', NULL, NULL),
(13, 11, 7, 1, 60.00, 'pending', 'pending', NULL, NULL),
(14, 12, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(15, 13, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(16, 14, 7, 1, 50.00, 'pending', 'pending', NULL, NULL),
(17, 15, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(18, 16, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(19, 17, 7, 1, 60.00, 'pending', 'pending', NULL, NULL),
(20, 17, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(21, 18, 7, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(22, 19, 9, 1, 60.00, 'pending', 'pending', 'cold', 'extra'),
(23, 20, 8, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(24, 21, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(25, 22, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(26, 23, 17, 1, 50.00, 'pending', 'pending', NULL, NULL),
(27, 24, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(28, 25, 10, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(29, 26, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(30, 27, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(31, 28, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(32, 28, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(33, 29, 9, 1, 60.00, 'pending', 'pending', 'cold', 'extra'),
(35, 31, 3, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(36, 31, 17, 1, 50.00, 'pending', 'pending', NULL, NULL),
(37, 32, 7, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(38, 32, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(39, 33, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(40, 33, 19, 1, 60.00, 'pending', 'pending', NULL, NULL),
(41, 33, 3, 1, 50.00, 'pending', 'pending', 'hot', 'less'),
(42, 34, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(43, 34, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(44, 34, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(45, 34, 14, 1, 60.00, 'pending', 'pending', NULL, NULL),
(46, 34, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(47, 34, 11, 1, 60.00, 'pending', 'pending', NULL, NULL),
(48, 34, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(49, 34, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(50, 34, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(51, 34, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(52, 34, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(53, 34, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(54, 34, 2, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(55, 34, 1, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(56, 35, 6, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(57, 35, 14, 1, 60.00, 'pending', 'pending', NULL, NULL),
(58, 35, 19, 1, 60.00, 'pending', 'pending', NULL, NULL),
(59, 36, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(60, 36, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(61, 36, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(62, 36, 4, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(63, 36, 6, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(64, 36, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(65, 36, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(66, 37, 2, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(67, 38, 6, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(68, 39, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(69, 39, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(70, 39, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(71, 39, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(72, 39, 1, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(73, 39, 1, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(74, 39, 1, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(75, 39, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(76, 39, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(77, 39, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(78, 39, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(79, 39, 19, 1, 60.00, 'pending', 'pending', NULL, NULL),
(80, 40, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(81, 40, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(82, 40, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(83, 40, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(84, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(85, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(86, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(87, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(88, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(89, 40, 10, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(90, 40, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(91, 40, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(92, 40, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(93, 40, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(94, 40, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(95, 40, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(96, 40, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(97, 40, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(98, 40, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(99, 41, 9, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(100, 41, 2, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(101, 42, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(102, 42, 10, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(103, 43, 8, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(104, 43, 15, 1, 60.00, 'pending', 'pending', NULL, NULL),
(105, 43, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(106, 44, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(107, 44, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(108, 44, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(109, 44, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(110, 44, 7, 1, 50.00, 'pending', 'pending', 'hot', 'normal'),
(111, 44, 1, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(112, 44, 1, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(113, 44, 1, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(114, 44, 1, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(115, 44, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(116, 44, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(117, 44, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(118, 44, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(119, 44, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(120, 44, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(121, 44, 20, 1, 75.00, 'pending', 'pending', NULL, NULL),
(122, 44, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(123, 44, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(124, 44, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(125, 44, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(126, 44, 13, 1, 90.00, 'pending', 'pending', NULL, NULL),
(127, 44, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(128, 44, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(129, 44, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(130, 44, 18, 1, 65.00, 'pending', 'pending', NULL, NULL),
(131, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(132, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(133, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(134, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(135, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(136, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(137, 44, 6, 1, 60.00, 'pending', 'pending', 'cold', 'normal'),
(138, 45, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(139, 46, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(140, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(141, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(142, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(143, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(144, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(145, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(146, 47, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(147, 48, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(148, 48, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(149, 48, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(150, 48, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(151, 48, 16, 1, 40.00, 'pending', 'pending', NULL, NULL),
(152, 49, 12, 1, 75.00, 'pending', 'pending', NULL, NULL),
(153, 50, 7, 1, 60.00, 'pending', 'pending', 'cold', 'less'),
(154, 51, 16, 1, 40.00, 'pending', 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `type` enum('deposit','full_payment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `point_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `points_amount` int(11) NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `points`
--

INSERT INTO `points` (`point_id`, `customer_id`, `order_id`, `points_amount`, `reason`, `created_at`) VALUES
(1, 2, 33, 15, 'ได้รับแต้มจากออเดอร์ #33', '2026-10-21 14:49:46'),
(2, 2, 34, 100, 'ได้รับแต้มจากออเดอร์ #34', '2026-10-21 14:52:07'),
(3, 2, NULL, -100, 'แลกของรางวัล: ส่วนลด 10%', '2026-10-21 14:52:24'),
(4, 11, 35, 15, 'ได้รับแต้มจากออเดอร์ #35', '2026-10-21 16:25:16'),
(5, 11, 36, 35, 'ได้รับแต้มจากออเดอร์ #36', '2026-10-21 16:27:44'),
(6, 11, NULL, -50, 'แลกของรางวัล: ส่วนลด 20 บาท', '2026-10-21 16:28:38'),
(7, 11, 39, 65, 'ได้รับแต้มจากออเดอร์ #39', '2026-10-21 19:17:12'),
(8, 11, 40, 115, 'ได้รับแต้มจากออเดอร์ #40', '2026-10-21 19:21:58'),
(9, 11, NULL, -150, 'แลกของรางวัล: เครื่องดื่มฟรี 1 แก้ว', '2026-10-21 19:31:24'),
(10, 2, 44, 210, 'ได้รับแต้มจากออเดอร์ #44', '2026-10-21 20:03:20'),
(11, 2, NULL, -200, 'แลกของรางวัล: กระเป๋าผ้า', '2026-10-21 20:11:50'),
(12, 2, 47, 25, 'ได้รับแต้มจากออเดอร์ #47', '2026-10-21 21:36:00'),
(13, 2, NULL, -50, 'แลกของรางวัล: ส่วนลด 20 บาท', '2026-10-21 21:36:08'),
(14, 11, 48, 20, 'ได้รับแต้มจากออเดอร์ #48', '2026-10-21 23:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_pct` decimal(5,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redemptions`
--

CREATE TABLE `redemptions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `reward_name` varchar(100) NOT NULL,
  `points_used` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `used_order_id` int(11) DEFAULT NULL,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `redemptions`
--

INSERT INTO `redemptions` (`id`, `member_id`, `reward_id`, `reward_name`, `points_used`, `created_at`, `used_order_id`, `used_at`) VALUES
(1, 2, 1, 'ส่วนลด 10%', 100, '2026-10-21 21:52:24', 43, '2026-10-22 02:39:58'),
(2, 11, 6, 'ส่วนลด 20 บาท', 50, '2026-10-21 23:28:38', 41, '2026-10-22 02:32:21'),
(3, 11, 4, 'เครื่องดื่มฟรี 1 แก้ว', 150, '2026-10-22 02:31:24', 42, '2026-10-22 02:38:16'),
(4, 2, 5, 'กระเป๋าผ้า', 200, '2026-10-22 03:11:50', 45, '2026-10-22 03:12:23'),
(5, 2, 6, 'ส่วนลด 20 บาท', 50, '2026-10-22 04:36:08', 49, '2026-10-22 21:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `reservation_time` datetime NOT NULL,
  `status` enum('pending','confirmed','seated','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(10) NOT NULL,
  `zone` varchar(50) NOT NULL DEFAULT 'ด้านใน',
  `seats` int(11) NOT NULL DEFAULT 4,
  `status` enum('available','occupied','reserved') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`table_id`, `table_number`, `zone`, `seats`, `status`) VALUES
(1, 'A1', 'ด้านใน', 4, 'available'),
(2, 'A2', 'ด้านใน', 4, 'available'),
(3, 'A3', 'ด้านใน', 6, 'available'),
(4, 'B1', 'ริมหน้าต่าง', 4, 'available'),
(5, 'B2', 'ริมหน้าต่าง', 4, 'available'),
(6, 'B3', 'ริมหน้าต่าง', 4, 'available'),
(7, 'C1', 'หน้าเคาน์เตอร์', 2, 'available'),
(8, 'C2', 'หน้าเคาน์เตอร์', 2, 'available'),
(9, 'D1', 'ด้านนอก', 4, 'available'),
(10, 'D2', 'ด้านนอก', 4, 'available'),
(11, 'D3', 'ด้านนอก', 6, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `points_required` int(11) NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL DEFAULT -1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('percent','fixed','free_item','gift') NOT NULL DEFAULT 'gift',
  `value` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `name`, `points_required`, `stock`, `is_active`, `type`, `value`) VALUES
(1, 'ส่วนลด 10%', 100, -1, 1, 'percent', 10),
(4, 'เครื่องดื่มฟรี 1 แก้ว', 150, -1, 1, 'free_item', 0),
(5, 'กระเป๋าผ้า', 200, -1, 1, 'gift', 0),
(6, 'ส่วนลด 20 บาท', 50, -1, 1, 'fixed', 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('customer','host','cashier','server','kitchen','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `phone`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Cha', '0827153547', NULL, '$2y$10$pOR91ljX/X17Kf9SMuZzzOriKTVC8ZLXmbcF2xw7Ci5pXmoekns9u', 'customer', '2026-09-21 13:28:26'),
(3, 'fang', '0952752226', NULL, '$2y$10$x6a4i.8eCtv/jX/VBOYTqu/qbjgfumSva8rQ6y4KTDkszXD43HQF2', 'customer', '2026-09-21 17:22:06'),
(5, 'nee', '0661421752', NULL, '$2y$10$jIx0D9Fv4RsYocX9UgZPL.a8JDpHJgT/09IMZ5ac8ZInnWUzBrG/K', 'customer', '2026-09-22 14:45:44'),
(7, 'แอดมินครัว', '0812345678', NULL, '$2b$10$frMFFSjr65A8gIEe0LFgIOKS4sWvUshNzDrJsRfTcdw3q36FqosKC', 'kitchen', '2026-09-27 14:08:54'),
(8, 'pxcci', '0976346578', NULL, '$2y$10$81UsNkTW1T.TsD2jPGJlb.FYUtCqcbQJTuC7aigCdeNm0r7F5MZ9q', 'customer', '2026-09-27 15:37:56'),
(10, 'แอดมินร้าน', '0912345678', NULL, '$2y$10$heMiuutpWtjXscQ3WX98Nuync/oplMNiXKPBO6WXeRnkFLDojT33i', 'admin', '2026-10-16 13:50:55'),
(11, 'gift', '0996068288', NULL, '$2y$10$jCI6Y8nmBfYvhUoc.m6ky.qjUMQDB/.3YrMOVv2lTtZKA1FB56ARG', 'customer', '2026-10-21 14:06:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`);

--
-- Indexes for table `menu_ingredients`
--
ALTER TABLE `menu_ingredients`
  ADD PRIMARY KEY (`menu_id`,`ingredient_id`,`temp_type`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`point_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `redemptions`
--
ALTER TABLE `redemptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_member` (`member_id`,`created_at`),
  ADD KEY `idx_unused` (`member_id`,`used_at`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_2` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `point_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `redemptions`
--
ALTER TABLE `redemptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_ingredients`
--
ALTER TABLE `menu_ingredients`
  ADD CONSTRAINT `menu_ingredients_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`menu_id`),
  ADD CONSTRAINT `menu_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`table_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`menu_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`);

--
-- Constraints for table `points`
--
ALTER TABLE `points`
  ADD CONSTRAINT `points_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `points_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`table_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
