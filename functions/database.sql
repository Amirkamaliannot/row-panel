-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 07, 2023 at 07:16 PM
-- Server version: 5.7.36
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barber`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE IF NOT EXISTS `tbl_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `username` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `date_c` int(11) NOT NULL,
  `date_m` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL,
  `login_session` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `first_name`, `last_name`, `username`, `password`, `access`, `status`, `date_c`, `date_m`, `last_login`, `login_session`) VALUES
(1, 'امیر  ', 'کمالیان', 'root', '55100dba2585010666b17d5143fdb4cb', 1, 1, 1694106566, 1694111937, 1694111301, 'dvSOVwli5FN3eGpokNIOCe7mfpD6p9nj');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin_log`
--

DROP TABLE IF EXISTS `tbl_admin_log`;
CREATE TABLE IF NOT EXISTS `tbl_admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `ip` varchar(16) COLLATE utf8_persian_ci NOT NULL,
  `date_c` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_log` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `tbl_admin_log`
--

INSERT INTO `tbl_admin_log` (`id`, `admin_id`, `action`, `ip`, `date_c`, `status`) VALUES
(1, 1, '1', '127.0.0.1', 1694107523, 0),
(2, 1, '1', '127.0.0.1', 1694107532, 1),
(3, 1, '1', '127.0.0.1', 1694110732, 0),
(4, 1, '1', '127.0.0.1', 1694110740, 1),
(5, 1, '1', '127.0.0.1', 1694110863, 1),
(6, 1, '1', '127.0.0.1', 1694110880, 1),
(7, 1, '1', '127.0.0.1', 1694111076, 1),
(8, 1, '1', '127.0.0.1', 1694111101, 1),
(9, 1, '1', '127.0.0.1', 1694111266, 1),
(10, 1, '1', '127.0.0.1', 1694111301, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_option`
--

DROP TABLE IF EXISTS `tbl_option`;
CREATE TABLE IF NOT EXISTS `tbl_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `value` varchar(32) COLLATE utf8_persian_ci NOT NULL,
  `date_c` int(11) NOT NULL,
  `date_m` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_request`
--

DROP TABLE IF EXISTS `tbl_request`;
CREATE TABLE IF NOT EXISTS `tbl_request` (
  `id` int(11) NOT NULL,
  `phone` varchar(11) COLLATE utf8_persian_ci NOT NULL,
  `name` varchar(64) COLLATE utf8_persian_ci NOT NULL,
  `service_id` int(11) NOT NULL,
  `request_time` int(11) NOT NULL,
  `date_c` int(11) NOT NULL,
  `date_m` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  KEY `request_service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service`
--

DROP TABLE IF EXISTS `tbl_service`;
CREATE TABLE IF NOT EXISTS `tbl_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_persian_ci NOT NULL,
  `time` int(11) NOT NULL,
  `date_c` int(11) NOT NULL,
  `date_m` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_admin_log`
--
ALTER TABLE `tbl_admin_log`
  ADD CONSTRAINT `admin_log` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`id`);

--
-- Constraints for table `tbl_request`
--
ALTER TABLE `tbl_request`
  ADD CONSTRAINT `request_service_id` FOREIGN KEY (`service_id`) REFERENCES `tbl_service` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
