SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `Command`;
CREATE TABLE `Command` (
                           `id` int NOT NULL AUTO_INCREMENT,
                           `prevalue` tinytext NOT NULL,
                           `postvalue` tinytext,
                           `name` tinytext NOT NULL,
                           `comment` varchar(255) DEFAULT NULL,
                           `enable` tinyint(1) NOT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

