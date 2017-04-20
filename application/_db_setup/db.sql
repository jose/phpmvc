-- DB Example
-- version 0.0.1
--
-- Creation Time: Apr 20, 2017
-- MySQL Server version: 5.5.35-1
-- PHP Version: 5.5.9-

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `readability`
--

DROP DATABASE IF EXISTS `phpmvc-example`;
CREATE DATABASE `phpmvc-example`
  CHARACTER SET utf8
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  DEFAULT COLLATE utf8_general_ci;
USE `phpmvc-example`;

-- -------------------------------------------------------------------
-- Tables

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- END

