SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `Config`;
CREATE TABLE `Config` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `version` float NOT NULL,
                          `approved` enum('','2','3') DEFAULT NULL,
                          `lastmodify` datetime NOT NULL,
                          `create` datetime NOT NULL,
                          `hosts` tinytext NOT NULL,
                          `commandvalue` tinytext NOT NULL,
                          `name` tinytext NOT NULL,
                          `comment` varchar(255) DEFAULT NULL,
                          `userid` int NOT NULL,
                          `commandid` int NOT NULL,
                          PRIMARY KEY (`id`),
                          KEY `commandid` (`commandid`),
                          KEY `userid` (`userid`),
                          CONSTRAINT `config_ibfk_1` FOREIGN KEY (`commandid`) REFERENCES `Command` (`id`),
                          CONSTRAINT `config_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;