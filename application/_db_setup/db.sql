-- DB Example
-- version 0.0.1
--
-- Creation Time: May 06, 2017
-- MySQL Server version: 5.5.35-1
-- PHP Version: 5.5.9-

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `phpmvc-example`
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
-- Table structure for table `Competency`
--

CREATE TABLE `Competency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `CompetencyAnswer`
--

CREATE TABLE `CompetencyAnswer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `competency_id` int(11) NOT NULL,
  `question_num` int(11) NOT NULL,
  `choice` int(11) NOT NULL,
  `time_to_answer` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`competency_id`)
    REFERENCES Competency(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `id` varchar(32) NOT NULL,
  `competency_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`competency_id`)
    REFERENCES Competency(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- END

