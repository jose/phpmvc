-- DB Example
-- version 0.0.1
--
-- Creation Time: Apr 20, 2017
-- MySQL Server version: 5.5.35-1
-- PHP Version: 5.5.9-

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `phpmvc-example`
--

USE `phpmvc-example`;

-- -------------------------------------------------------------------
-- Data

--
-- Data for table `Competency`
--

INSERT INTO `Competency` (`id`, `score`) VALUES
(1, 90),
(2, 25);

--
-- Data for table `User`
--

INSERT INTO `User` (`id`, `competency_id`) VALUES
("allowed", 1),
("notallowed", 2);

-- END

