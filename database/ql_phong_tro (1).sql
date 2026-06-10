-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 04:52 AM
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
-- Database: `ql_phong_tro`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `vai_tro` enum('quan_ly','chu_tro','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `ho_ten`, `username`, `password`, `email`, `sdt`, `vai_tro`, `created_at`) VALUES
(1, 'Tô Minh Thức', 'minhthuc0103', '$2y$10$XBe49YovSVDjX1EKNu6lS.OZ8fbiUkRlX8gsQaG.00Vkp362WxuIy', 'minhthuc0103@gmail.com', '0941032534', 'quan_ly', '2026-05-08 04:32:33'),
(3, 'Phạm Thái Phong', 'thaiphong2306', '$2y$10$SdvVWBa9vKwVYMYN6KGoAu6MICmCpxlnCkukvZ1bAIYeV/V8pZZwi', 'thaiphong2306@gmail.com', '0941032535', 'user', '2026-05-08 06:26:58'),
(4, 'Tô Thế Kiệt', 'thekiet123', '$2y$10$4mpnTm8JPS8GC6W/K8GHxunOu0JNAloF497VdWITm8.VriSMuvgTe', 'thekiet123@gmail.com', '0357168681', 'user', '2026-05-08 11:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `don_gia`
--

CREATE TABLE `don_gia` (
  `id` int(11) NOT NULL,
  `gia_dien` decimal(10,2) DEFAULT 3500.00,
  `gia_nuoc` decimal(10,2) DEFAULT 15000.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `don_gia`
--

INSERT INTO `don_gia` (`id`, `gia_dien`, `gia_nuoc`) VALUES
(1, 3500.00, 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` int(11) NOT NULL,
  `phong_id` int(11) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `chi_so_dien_cu` decimal(10,2) DEFAULT 0.00,
  `chi_so_dien_moi` decimal(10,2) DEFAULT 0.00,
  `chi_so_nuoc_cu` decimal(10,2) DEFAULT 0.00,
  `chi_so_nuoc_moi` decimal(10,2) DEFAULT 0.00,
  `tien_phong` decimal(12,2) DEFAULT 0.00,
  `tien_dien` decimal(12,2) DEFAULT 0.00,
  `tien_nuoc` decimal(12,2) DEFAULT 0.00,
  `tong_tien` decimal(12,2) DEFAULT 0.00,
  `trang_thai` enum('chua_tt','da_tt') DEFAULT 'chua_tt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hop_dong`
--

CREATE TABLE `hop_dong` (
  `id` int(11) NOT NULL,
  `phong_id` int(11) NOT NULL,
  `nguoi_thue_id` int(11) NOT NULL,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `tien_coc` decimal(12,2) DEFAULT 0.00,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` enum('hieu_luc','het_han','da_huy') DEFAULT 'hieu_luc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `yeu_cau_huy` tinyint(1) DEFAULT 0 COMMENT '1 = đã báo hủy',
  `ngay_bao_huy` date DEFAULT NULL COMMENT 'Ngày người thuê báo hủy',
  `ngay_du_kien_ra` date DEFAULT NULL COMMENT 'Ngày dự kiến ra (ngày 25 tháng báo)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khu_tro`
--

CREATE TABLE `khu_tro` (
  `id` int(11) NOT NULL,
  `ten_khu` varchar(100) NOT NULL,
  `ma_khu` varchar(10) NOT NULL,
  `dia_chi` text DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `quan_ly_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khu_tro`
--

INSERT INTO `khu_tro` (`id`, `ten_khu`, `ma_khu`, `dia_chi`, `mo_ta`, `quan_ly_id`) VALUES
(1, 'KHU A', 'A', '71 Đường 185 Phước Long B', 'bãi xe rộng, camera an ninh, khóa vân tay', NULL),
(2, 'KHU B', 'B', '256 Bưng Ông Thoàn, Phú Hữu', 'camera an ninh, khóa vân tay', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_thue`
--

CREATE TABLE `nguoi_thue` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT '',
  `account_id` int(11) DEFAULT NULL,
  `cccd_truoc` varchar(255) DEFAULT '' COMMENT 'Đường dẫn ảnh CCCD mặt trước',
  `cccd_sau` varchar(255) DEFAULT '' COMMENT 'Đường dẫn ảnh CCCD mặt sau',
  `ngay_sinh` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_thue`
--

INSERT INTO `nguoi_thue` (`id`, `ho_ten`, `cccd`, `sdt`, `dia_chi`, `avatar`, `account_id`, `cccd_truoc`, `cccd_sau`, `ngay_sinh`) VALUES
(1, 'Phạm Thái Phong', '897684643612', '0941032535', 'mnasmbnasjhfashfjkoaslk 1231', '', 3, '', '', NULL),
(2, 'Tô Thế Kiệt', '354383586168', '0357168681', 'ạhduhwiuhksabiughwighasndna12', '', 4, '', '', '2004-11-20');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_thue_phong`
--

CREATE TABLE `nguoi_thue_phong` (
  `id` int(11) NOT NULL,
  `hop_dong_id` int(11) NOT NULL,
  `phong_id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('nam','nu','khac') DEFAULT 'nam',
  `que_quan` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT '',
  `la_chu_hop_dong` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phong`
--

CREATE TABLE `phong` (
  `id` int(11) NOT NULL,
  `khu_id` int(11) DEFAULT NULL,
  `so_phong` varchar(50) NOT NULL,
  `gia` decimal(12,2) NOT NULL,
  `dien_tich` decimal(6,2) DEFAULT 0.00,
  `so_nguoi` int(11) DEFAULT 4 COMMENT 'Sức chứa tối đa',
  `mo_ta` text DEFAULT NULL,
  `anh_phong` varchar(255) DEFAULT '',
  `trang_thai` enum('trong','dang_thue','bao_tri') DEFAULT 'trong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phong`
--

INSERT INTO `phong` (`id`, `khu_id`, `so_phong`, `gia`, `dien_tich`, `so_nguoi`, `mo_ta`, `anh_phong`, `trang_thai`) VALUES
(2, 1, 'A101', 4900000.00, 20.00, 4, 'điều hòa', '[\"public\\/uploads\\/phong\\/phong_1778220823_7816_0.jpg\",\"public\\/uploads\\/phong\\/phong_1778220823_6171_1.jpg\",\"public\\/uploads\\/phong\\/phong_1778220823_8486_2.jpg\",\"public\\/uploads\\/phong\\/phong_1778220823_8349_3.jpg\",\"public\\/uploads\\/phong\\/phong_1778220', 'trong'),
(3, 1, 'A102', 4900000.00, 18.00, 2, 'Điều Hòa, Nóng Lạnh', '', 'trong'),
(4, 2, 'B101', 3300000.00, 20.00, 4, 'Thoáng mát', '[\"public\\/uploads\\/phong\\/phong_1778221175_4148_0.jpg\",\"public\\/uploads\\/phong\\/phong_1778221175_6433_1.jpg\"]', 'trong'),
(5, 2, 'B102', 3000000.00, 17.00, 3, 'ban công', '', 'trong');

-- --------------------------------------------------------

--
-- Table structure for table `xe`
--

CREATE TABLE `xe` (
  `id` int(11) NOT NULL,
  `hop_dong_id` int(11) NOT NULL,
  `phong_id` int(11) NOT NULL,
  `bien_so` varchar(20) NOT NULL,
  `loai_xe` enum('xe_may','xe_dien','xe_dap') DEFAULT 'xe_may',
  `mau_sac` varchar(50) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `don_gia`
--
ALTER TABLE `don_gia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_phong_thang_nam` (`phong_id`,`thang`,`nam`);

--
-- Indexes for table `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phong_id` (`phong_id`),
  ADD KEY `nguoi_thue_id` (`nguoi_thue_id`);

--
-- Indexes for table `khu_tro`
--
ALTER TABLE `khu_tro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_khu` (`ma_khu`);

--
-- Indexes for table `nguoi_thue`
--
ALTER TABLE `nguoi_thue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `nguoi_thue_phong`
--
ALTER TABLE `nguoi_thue_phong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hop_dong_id` (`hop_dong_id`),
  ADD KEY `phong_id` (`phong_id`);

--
-- Indexes for table `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `khu_id` (`khu_id`);

--
-- Indexes for table `xe`
--
ALTER TABLE `xe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hop_dong_id` (`hop_dong_id`),
  ADD KEY `phong_id` (`phong_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `don_gia`
--
ALTER TABLE `don_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hop_dong`
--
ALTER TABLE `hop_dong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `khu_tro`
--
ALTER TABLE `khu_tro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nguoi_thue`
--
ALTER TABLE `nguoi_thue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nguoi_thue_phong`
--
ALTER TABLE `nguoi_thue_phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phong`
--
ALTER TABLE `phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `xe`
--
ALTER TABLE `xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD CONSTRAINT `hop_dong_ibfk_1` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hop_dong_ibfk_2` FOREIGN KEY (`nguoi_thue_id`) REFERENCES `nguoi_thue` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nguoi_thue`
--
ALTER TABLE `nguoi_thue`
  ADD CONSTRAINT `nguoi_thue_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `nguoi_thue_phong`
--
ALTER TABLE `nguoi_thue_phong`
  ADD CONSTRAINT `nguoi_thue_phong_ibfk_1` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nguoi_thue_phong_ibfk_2` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `phong_ibfk_1` FOREIGN KEY (`khu_id`) REFERENCES `khu_tro` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `xe`
--
ALTER TABLE `xe`
  ADD CONSTRAINT `xe_ibfk_1` FOREIGN KEY (`hop_dong_id`) REFERENCES `hop_dong` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `xe_ibfk_2` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- BỔ SUNG TÍNH NĂNG YÊU CẦU CHUYỂN PHÒNG
-- =====================================================
CREATE TABLE IF NOT EXISTS `yeu_cau_chuyen_phong` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT NOT NULL,
  `nguoi_thue_id`  INT NOT NULL,
  `hop_dong_id`    INT NOT NULL,
  `phong_cu_id`    INT NOT NULL,
  `phong_moi_id`   INT NOT NULL,
  `ly_do`          TEXT NOT NULL,
  `trang_thai`     ENUM('cho_duyet','da_duyet','tu_choi','da_huy') DEFAULT 'cho_duyet',
  `phan_hoi_ql`    TEXT,
  `nguoi_duyet_id` INT DEFAULT NULL,
  `duyet_luc`      DATETIME DEFAULT NULL,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_yccp_user` (`user_id`),
  KEY `idx_yccp_hop_dong` (`hop_dong_id`),
  KEY `idx_yccp_phong_moi` (`phong_moi_id`),
  KEY `idx_yccp_trang_thai` (`trang_thai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
