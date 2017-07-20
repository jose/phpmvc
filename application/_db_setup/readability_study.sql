-- DB Example
-- version 0.0.1
--
-- Creation Time: May 06, 2017
-- MySQL Server version: 5.5.35-1
-- PHP Version: 5.5.9-

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `readability-study`
--

DROP DATABASE IF EXISTS `readability-study`;
CREATE DATABASE `readability-study`
  CHARACTER SET utf8
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  DEFAULT COLLATE utf8_general_ci;
USE `readability-study`;

-- -------------------------------------------------------------------
-- Competency Evaluation

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

-- -------------------------------------------------------------------
-- Study tables

--
-- Table structure for table `Container`
--

CREATE TABLE `Container` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` ENUM('like', 'dislike'),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Tag`
--
-- Note: 'value' must be a single string *without* spaces
--

CREATE TABLE `Tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ContainerTag`
--

CREATE TABLE `ContainerTag` (
  `container_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`container_id`, `tag_id`),
  FOREIGN KEY (`container_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`)
    REFERENCES Tag(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Snippet`
--

CREATE TABLE `Snippet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL, -- local path, e.g, http://localhost/.../public/snippets/.../nu.xom.Attribute.postprocessed.java
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Study`
--

CREATE TABLE `Study` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` ENUM('individual', 'pair'),
  `user_id` varchar(32) NOT NULL,
  `time_to_answer` int(11) NOT NULL,
  `dont_know_answer` text NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`)
    REFERENCES User(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `IndividualStudy`
--

CREATE TABLE `IndividualStudy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `study_id` int(11) NOT NULL,
  `snippet_id` int(11) NOT NULL,
  `num_stars` decimal(2,1) NOT NULL,
  `like_id` int(11) NOT NULL,
  `dislike_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`study_id`)
    REFERENCES Study(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`snippet_id`)
    REFERENCES Snippet(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`like_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`dislike_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `PairStudy`
--

CREATE TABLE `PairStudy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `study_id` int(11) NOT NULL,
  `snippet_a_id` int(11) NOT NULL,
  `like_a_id` int(11) NOT NULL,
  `dislike_a_id` int(11) NOT NULL,
  `snippet_b_id` int(11) NOT NULL,
  `like_b_id` int(11) NOT NULL,
  `dislike_b_id` int(11) NOT NULL,
  `chosen_snippet_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`study_id`)
    REFERENCES Study(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`snippet_a_id`)
    REFERENCES Snippet(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`like_a_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`dislike_a_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`snippet_b_id`)
    REFERENCES Snippet(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`like_b_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`dislike_b_id`)
    REFERENCES Container(`id`)
    ON DELETE CASCADE,
  FOREIGN KEY (`chosen_snippet_id`)
    REFERENCES Snippet(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- END

