-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 24, 2017 at 11:08 PM
-- Server version: 5.5.54-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_city_practice`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_route`
--

CREATE TABLE IF NOT EXISTS `tbl_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` text NOT NULL,
  `from_city` text NOT NULL,
  `to_city` text NOT NULL,
  `packet` int(11) NOT NULL,
  `time` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `tbl_route`
--

INSERT INTO `tbl_route` (`id`, `company_name`, `from_city`, `to_city`, `packet`, `time`) VALUES
(1, 'abc', 'surat', 'abd', 100, '1'),
(2, 'xyz', 'abd', 'baroda', 200, '2'),
(3, 'pqr', 'baroda', 'mumbai', 400, '2'),
(4, 'abc', 'surat', 'baroda', 270, '1'),
(5, 'abc', 'abd', 'mumbai', 600, '2'),
(6, 'xyz', 'surat', 'jam', 700, '2'),
(7, 'pqr', 'jam', 'rajkot', 200, '1'),
(8, 'pqr', 'rajkot', 'abd', 90, '3'),
(9, 'abc', 'surat', 'mumbai', 220, '2'),
(10, 'xyz', 'surat', 'baroda', 250, '1'),
(11, 'Maruti', 'Bhavnagar', 'abd', 320, '3'),
(12, 'por', 'vapi', 'valsad', 120, '1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
