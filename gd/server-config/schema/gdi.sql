-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2015 at 09:46 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gdi`
--

-- --------------------------------------------------------

--
-- Table structure for table `h_product`
--

CREATE TABLE IF NOT EXISTS `h_product` (
  `jurisdiction_product_id` int(11) NOT NULL,
  `history` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_branch`
--

CREATE TABLE IF NOT EXISTS `m_branch` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(30) NOT NULL,
  `branch_abbr` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`branch_id`),
  UNIQUE KEY `branch_name` (`branch_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `m_branch`
--

INSERT INTO `m_branch` (`branch_id`, `branch_name`, `branch_abbr`, `is_deleted`, `create_user_id`, `create_time`, `update_user_id`, `update_time`) VALUES
(1, 'Aruze Gaming Philippines', 'AGA - PB', 0, 0, '2015-10-14 13:24:29', 0, '2015-10-13 16:59:15');

-- --------------------------------------------------------

--
-- Table structure for table `m_game_category`
--

CREATE TABLE IF NOT EXISTS `m_game_category` (
  `game_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_category_name` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_category_id`),
  UNIQUE KEY `game_category_name` (`game_category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_game_group`
--

CREATE TABLE IF NOT EXISTS `m_game_group` (
  `game_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_group_name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`game_group_id`),
  UNIQUE KEY `game_group_name` (`game_group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_jurisdiction`
--

CREATE TABLE IF NOT EXISTS `m_jurisdiction` (
  `jurisdiction_id` int(11) NOT NULL AUTO_INCREMENT,
  `jurisdiction_name` varchar(30) NOT NULL,
  `jurisdiction_abbr` varchar(4) NOT NULL,
  `is_regulator` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jurisdiction_id`),
  UNIQUE KEY `jurisdiction_name` (`jurisdiction_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_language`
--

CREATE TABLE IF NOT EXISTS `m_language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `name_en` varchar(30) NOT NULL,
  `name_native` varchar(30) NOT NULL,
  `code` varchar(5) NOT NULL,
  `resource` varchar(5) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `name_en` (`name_en`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `m_language`
--

INSERT INTO `m_language` (`language_id`, `name_en`, `name_native`, `code`, `resource`, `is_deleted`, `create_user_id`, `create_time`, `update_user_id`, `update_time`) VALUES
(1, 'English', 'en_US', 'en_US', 'en', 0, 0, '2015-10-14 00:00:00', 0, '2015-10-13 17:04:06'),
(2, 'Japanese', 'æ—¥æœ¬èªž', 'ja_JP', 'ja', 0, 0, '2015-10-14 00:00:00', 0, '2015-10-13 17:05:01');

-- --------------------------------------------------------

--
-- Table structure for table `m_market`
--

CREATE TABLE IF NOT EXISTS `m_market` (
  `market_id` int(11) NOT NULL AUTO_INCREMENT,
  `market_name` varchar(30) NOT NULL,
  `market_abbr` varchar(30) NOT NULL,
  `criterion_jurisdiction_id` varchar(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`market_id`),
  UNIQUE KEY `market_name` (`market_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_page`
--

CREATE TABLE IF NOT EXISTS `m_page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `page_name` (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_platform`
--

CREATE TABLE IF NOT EXISTS `m_platform` (
  `platform_id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`platform_id`),
  UNIQUE KEY `platform_name` (`platform_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_role`
--

CREATE TABLE IF NOT EXISTS `m_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_session`
--

CREATE TABLE IF NOT EXISTS `m_session` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `m_session`
--

INSERT INTO `m_session` (`id`, `name`, `modified`, `lifetime`, `data`) VALUES
('r0h73avcqcd2rm6tghisif0rv0', 'gdi_aruze', 1444895023, 1800, '{"id":"2","ip_address":"192.168.4.200","user_agent":"Mozilla\\/5.0 (Windows NT 6.1; WOW64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/45.0.2454.101 Safari\\/537.36"}');

-- --------------------------------------------------------

--
-- Table structure for table `m_status`
--

CREATE TABLE IF NOT EXISTS `m_status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(30) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `m_user`
--

CREATE TABLE IF NOT EXISTS `m_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `pw_error_count` tinyint(4) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_address` (`email_address`),
  KEY `m_user_fi1` (`branch_id`),
  KEY `m_user_fi2` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `m_user`
--

INSERT INTO `m_user` (`user_id`, `first_name`, `last_name`, `email_address`, `password`, `branch_id`, `language_id`, `pw_error_count`, `is_active`, `is_deleted`, `create_user_id`, `create_time`, `update_user_id`, `update_time`) VALUES
(2, 'marvin', 'manguait', 'marvin.manguiat@aruzegaming.com', '5d7845ac6ee7cfffafc5fe5f35cf666d', 1, 1, 2, 0, 0, 0, '2015-10-14 00:00:00', 0, '2015-10-14 05:34:44'),
(3, 'Jen', 'Lovendino', 'lovely.jen.lovendino@aruzegaming.com', '5d7845ac6ee7cfffafc5fe5f35cf666d', 1, 1, 2, 0, 0, 0, '2015-10-14 00:00:00', 0, '2015-10-14 05:34:44'),
(4, 'jonathan', 'antivo', 'jonathan.antivo@aruzegaming.com', '5d7845ac6ee7cfffafc5fe5f35cf666d', 1, 1, 2, 0, 0, 0, '2015-10-14 00:00:00', 0, '2015-10-14 05:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `r_market_jurisdiction`
--

CREATE TABLE IF NOT EXISTS `r_market_jurisdiction` (
  `market_id` int(11) NOT NULL,
  `jurisdiction_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `r_market_jurisdiction_fi1` (`market_id`),
  KEY `r_market_jurisdiction_fi2` (`jurisdiction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `r_role_page`
--

CREATE TABLE IF NOT EXISTS `r_role_page` (
  `role_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `r_role_pagefi1` (`role_id`),
  KEY `r_role_pagefi2` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `r_user_role`
--

CREATE TABLE IF NOT EXISTS `r_user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `r_user_role_fi1` (`user_id`),
  KEY `r_user_role_fi2` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_jurisdiction_product`
--

CREATE TABLE IF NOT EXISTS `t_jurisdiction_product` (
  `jurisdiction_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `market_product_id` int(11) NOT NULL COMMENT 'Parent Market Product ID',
  `jurisdiction_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `e_submission_date` date DEFAULT NULL COMMENT 'Estimated Submission Date',
  `r_submission_date` date DEFAULT NULL COMMENT 'Result Submission Date',
  `e_regulator_date` date DEFAULT NULL COMMENT 'Estimated Regulator Date',
  `r_regulator_date` date DEFAULT NULL COMMENT 'Result Regulator Date',
  `e_approval_date` date DEFAULT NULL,
  `r_approval_date` date DEFAULT NULL,
  `e_release_date` date DEFAULT NULL COMMENT 'Estimated Master Release Date',
  `r_release_date` date DEFAULT NULL COMMENT 'Result Master Release Date',
  `e_launch_date` date DEFAULT NULL,
  `r_launch_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jurisdiction_product_id`),
  KEY `t_jurisdiction_product_fi1` (`market_product_id`),
  KEY `t_jurisdiction_product_fi2` (`jurisdiction_id`),
  KEY `t_jurisdiction_product_fi3` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_market_product`
--

CREATE TABLE IF NOT EXISTS `t_market_product` (
  `market_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `gvn` varchar(30) DEFAULT NULL COMMENT 'Game Version No.',
  `title` varchar(100) DEFAULT NULL COMMENT 'Game Title',
  `platform_id` int(11) DEFAULT NULL COMMENT 'Game Platform ID',
  `branch_id` int(11) DEFAULT NULL COMMENT 'Developed Branch ID',
  `market_id` int(11) NOT NULL,
  `game_category_id` int(11) DEFAULT NULL,
  `game_group_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `overview` varchar(200) DEFAULT NULL COMMENT 'Game Overview',
  `character` varchar(200) DEFAULT NULL COMMENT 'Game Characteristics',
  `remarks` text,
  `e_dev_start_date` date DEFAULT NULL COMMENT 'Estimated Development Start Date',
  `r_dev_start_date` date DEFAULT NULL COMMENT 'Result Development Start Date',
  `e_dev_finish_date` date DEFAULT NULL COMMENT 'Estimated Development Finish Date',
  `r_dev_finish_date` date DEFAULT NULL COMMENT 'Result Development Finish Date',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`market_product_id`),
  KEY `t_market_product_fi1` (`product_id`),
  KEY `t_market_product_fi2` (`platform_id`),
  KEY `t_market_product_fi3` (`branch_id`),
  KEY `t_market_product_fi4` (`market_id`),
  KEY `t_market_product_fi5` (`game_category_id`),
  KEY `t_market_product_fi6` (`game_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_product`
--

CREATE TABLE IF NOT EXISTS `t_product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `control_id` varchar(20) NOT NULL,
  `e_artwork_sp_date` date DEFAULT NULL COMMENT 'Estimated Artwork (Sales Promotion) Upload Date',
  `r_artwork_sp_date` date DEFAULT NULL COMMENT 'Result Artwork (Sales Promotion) Upload Date',
  `e_gslick_date` date DEFAULT NULL COMMENT 'Estimated G-Slick Upload Date',
  `r_gslick_date` date DEFAULT NULL COMMENT 'Result G-Slick Upload Date',
  `e_demo_date` date DEFAULT NULL COMMENT 'Estimated Demo Software Upload Date',
  `r_demo_date` date DEFAULT NULL COMMENT 'Result Demo Software Upload Date',
  `e_movie_date` date DEFAULT NULL COMMENT 'Estimated Movie Upload Date',
  `r_movie_date` date DEFAULT NULL COMMENT 'Result Movie Upload Date',
  `e_artwork_tr_date` date DEFAULT NULL COMMENT 'Estimated Artwork (Training) Upload Date',
  `r_artwork_tr_date` date DEFAULT NULL COMMENT 'Result Artwork (Training) Upload Date',
  `e_website_date` date DEFAULT NULL COMMENT 'Estimated Website Upload Date',
  `r_website_date` date DEFAULT NULL COMMENT 'Result Website Upload Date',
  `is_return_demo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Return Demo Software',
  `game_image_pass` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `w_jurisdiction_product`
--

CREATE TABLE IF NOT EXISTS `w_jurisdiction_product` (
  `jurisdiction_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `market_product_id` int(11) NOT NULL COMMENT 'Parent Market Product ID',
  `jurisdiction_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `e_submission_date` date DEFAULT NULL COMMENT 'Estimated Submission Date',
  `r_submission_date` date DEFAULT NULL COMMENT 'Result Submission Date',
  `e_regulator_date` date DEFAULT NULL COMMENT 'Estimated Regulator Date',
  `r_regulator_date` date DEFAULT NULL COMMENT 'Result Regulator Date',
  `e_approval_date` date DEFAULT NULL,
  `r_approval_date` date DEFAULT NULL,
  `e_release_date` date DEFAULT NULL COMMENT 'Estimated Master Release Date',
  `r_release_date` date DEFAULT NULL COMMENT 'Result Master Release Date',
  `e_launch_date` date DEFAULT NULL,
  `r_launch_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jurisdiction_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `w_market_product`
--

CREATE TABLE IF NOT EXISTS `w_market_product` (
  `market_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `gvn` varchar(30) DEFAULT NULL COMMENT 'Game Version No.',
  `title` varchar(100) DEFAULT NULL COMMENT 'Game Title',
  `platform_id` int(11) DEFAULT NULL COMMENT 'Game Platform ID',
  `branch_id` int(11) DEFAULT NULL COMMENT 'Developed Branch ID',
  `market_id` int(11) NOT NULL,
  `game_category_id` int(11) DEFAULT NULL,
  `game_group_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `overview` varchar(200) DEFAULT NULL COMMENT 'Game Overview',
  `character` varchar(200) DEFAULT NULL COMMENT 'Game Characteristics',
  `remarks` text,
  `e_dev_start_date` date DEFAULT NULL COMMENT 'Estimated Development Start Date',
  `r_dev_start_date` date DEFAULT NULL COMMENT 'Result Development Start Date',
  `e_dev_finish_date` date DEFAULT NULL COMMENT 'Estimated Development Finish Date',
  `r_dev_finish_date` date DEFAULT NULL COMMENT 'Result Development Finish Date',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`market_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `w_product`
--

CREATE TABLE IF NOT EXISTS `w_product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `control_id` varchar(20) NOT NULL,
  `e_artwork_sp_date` date DEFAULT NULL COMMENT 'Estimated Artwork (Sales Promotion) Upload Date',
  `r_artwork_sp_date` date DEFAULT NULL COMMENT 'Result Artwork (Sales Promotion) Upload Date',
  `e_gslick_date` date DEFAULT NULL COMMENT 'Estimated G-Slick Upload Date',
  `r_gslick_date` date DEFAULT NULL COMMENT 'Result G-Slick Upload Date',
  `e_demo_date` date DEFAULT NULL COMMENT 'Estimated Demo Software Upload Date',
  `r_demo_date` date DEFAULT NULL COMMENT 'Result Demo Software Upload Date',
  `e_movie_date` date DEFAULT NULL COMMENT 'Estimated Movie Upload Date',
  `r_movie_date` date DEFAULT NULL COMMENT 'Result Movie Upload Date',
  `e_artwork_tr_date` date DEFAULT NULL COMMENT 'Estimated Artwork (Training) Upload Date',
  `r_artwork_tr_date` date DEFAULT NULL COMMENT 'Result Artwork (Training) Upload Date',
  `e_website_date` date DEFAULT NULL COMMENT 'Estimated Website Upload Date',
  `r_website_date` date DEFAULT NULL COMMENT 'Result Website Upload Date',
  `is_return_demo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Return Demo Software',
  `game_image_pass` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `create_user_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_user_id` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_user`
--
ALTER TABLE `m_user`
  ADD CONSTRAINT `FK_m_branch_m_user_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `m_branch` (`branch_id`),
  ADD CONSTRAINT `FK_m_language_m_user_language_id` FOREIGN KEY (`language_id`) REFERENCES `m_language` (`language_id`);

--
-- Constraints for table `r_market_jurisdiction`
--
ALTER TABLE `r_market_jurisdiction`
  ADD CONSTRAINT `FK_m_jurisdiction_r_market_jurisdiction_jurisdiction_id` FOREIGN KEY (`jurisdiction_id`) REFERENCES `m_jurisdiction` (`jurisdiction_id`),
  ADD CONSTRAINT `FK_m_market_r_market_jurisdiction_market_id` FOREIGN KEY (`market_id`) REFERENCES `m_market` (`market_id`);

--
-- Constraints for table `r_role_page`
--
ALTER TABLE `r_role_page`
  ADD CONSTRAINT `FK_m_page_r_role_page_page_id` FOREIGN KEY (`page_id`) REFERENCES `m_page` (`page_id`),
  ADD CONSTRAINT `FK_m_role_r_role_page_role_id` FOREIGN KEY (`role_id`) REFERENCES `m_role` (`role_id`);

--
-- Constraints for table `r_user_role`
--
ALTER TABLE `r_user_role`
  ADD CONSTRAINT `FK_m_role_r_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `m_role` (`role_id`),
  ADD CONSTRAINT `FK_m_user_r_user_role_user_id` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`);

--
-- Constraints for table `t_jurisdiction_product`
--
ALTER TABLE `t_jurisdiction_product`
  ADD CONSTRAINT `FK_m_jurisdiction_t_jurisdiction_product_jurisdiction_id` FOREIGN KEY (`jurisdiction_id`) REFERENCES `m_jurisdiction` (`jurisdiction_id`),
  ADD CONSTRAINT `FK_m_status_t_jurisdiction_product_status_id` FOREIGN KEY (`status_id`) REFERENCES `m_status` (`status_id`),
  ADD CONSTRAINT `FK_t_market_product_t_jurisdiction_product_market_product_id` FOREIGN KEY (`market_product_id`) REFERENCES `t_market_product` (`market_product_id`);

--
-- Constraints for table `t_market_product`
--
ALTER TABLE `t_market_product`
  ADD CONSTRAINT `FK_m_branch_t_product_market_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `m_branch` (`branch_id`),
  ADD CONSTRAINT `FK_m_game_category_t_product_market_game_category_id` FOREIGN KEY (`game_category_id`) REFERENCES `m_game_category` (`game_category_id`),
  ADD CONSTRAINT `FK_m_game_group_t_product_market_game_group_id` FOREIGN KEY (`game_group_id`) REFERENCES `m_game_group` (`game_group_id`),
  ADD CONSTRAINT `FK_m_market_t_product_market_market_id` FOREIGN KEY (`market_id`) REFERENCES `m_market` (`market_id`),
  ADD CONSTRAINT `FK_m_platform_t_product_market_platform_id` FOREIGN KEY (`platform_id`) REFERENCES `m_platform` (`platform_id`),
  ADD CONSTRAINT `FK_m_product_t_product_market_product_id` FOREIGN KEY (`product_id`) REFERENCES `t_product` (`product_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
