-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

TRUNCATE `users`;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `roles`) VALUES
(1,	'admin',	'admin@admin.ch',	'$2y$12$uyfcBXbakFWti4ohJHB72.2W.9a2B7v1fGKfLHcy5X93BBc97W4IO',	'2019-11-27 19:06:45',	'[\"user\", \"admin\"]'), -- Passwort : 121212
(2,	'user_passuser',	'user_passuser@test.ch',	'$2y$12$wWfk.xAVn2htcNlfRUplceL5J5T/hn9lOiF3rA/zIcrdDPM3JfIUW',	'2019-11-27 19:14:26',	'[\"user\"]'); -- Passwort : userpass

-- 2019-11-27 19:19:03
