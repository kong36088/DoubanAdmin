-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.7.18 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Linux
-- HeidiSQL 版本:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- 导出 douban 的数据库结构
CREATE DATABASE IF NOT EXISTS `douban` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `douban`;

-- 导出  表 douban.group 结构
CREATE TABLE IF NOT EXISTS `group` (
  `url` varchar(200) CHARACTER SET utf8 NOT NULL,
  `group_id` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `author` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `reply_num` int(11) unsigned NOT NULL,
  `last_reply_time` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '最后回复时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`url`),
  KEY `title` (`title`),
  KEY `group_id` (`group_id`),
  KEY `group_id_title` (`group_id`,`title`),
  KEY `update_time` (`last_reply_time`),
  KEY `author` (`author`),
  KEY `reply_num` (`reply_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 数据导出被取消选择。
-- 导出  表 douban.group_mark 结构
CREATE TABLE IF NOT EXISTS `group_mark` (
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('dislike','star','read') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int(1) NOT NULL,
  PRIMARY KEY (`url`,`type`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
