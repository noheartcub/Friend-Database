-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1:3306
-- Tid vid skapande: 05 nov 2024 kl 12:25
-- Serverversion: 8.3.0
-- PHP-version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `friend_manager_v2`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `avatars`
--

DROP TABLE IF EXISTS `avatars`;
CREATE TABLE IF NOT EXISTS `avatars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `avatarid` varchar(100) NOT NULL,
  `avatarimage` varchar(255) NOT NULL,
  `avatar_name` varchar(255) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `base_model` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` varchar(255) NOT NULL,
  `features` varchar(9999) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `avatarid` (`avatarid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `uploader_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `type` enum('info','warning','update') DEFAULT 'info',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL DEFAULT ((now() + interval 30 minute)),
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `people`
--

DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `id` int NOT NULL AUTO_INCREMENT,
  `display_name` varchar(100) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `discord` varchar(100) DEFAULT NULL,
  `steam` varchar(100) DEFAULT NULL,
  `youtube` varchar(100) NOT NULL,
  `vrchat` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `twitch` varchar(100) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `is_mute` tinyint(1) DEFAULT '0',
  `is_deaf` tinyint(1) DEFAULT '0',
  `meeting_places` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category` enum('Friend','Family','Best Friend','Ex-Colleagues','Girlfriend','Boyfriend','Ex Girlfriend','Ex Boyfriend','Pet','Master','BDSM Pet','D.I.D Core','D.I.D Alter','D.I.D Protector','D.I.D Caregiver','D.I.D Gatekeeper','D.I.D Introject','D.I.D Helper') DEFAULT 'Friend',
  `hide_age` tinyint DEFAULT '0',
  `hide_discord` tinyint(1) DEFAULT '0',
  `hide_email` tinyint(1) DEFAULT '0',
  `hide_steam_id` tinyint(1) DEFAULT '0',
  `hide_birthday` tinyint(1) DEFAULT '0',
  `hide_vrchat_id` tinyint(1) DEFAULT '0',
  `hide_first_name` tinyint DEFAULT '0',
  `hide_last_name` tinyint DEFAULT '0',
  `hide_phone_number` tinyint DEFAULT '0',
  `hide_address` tinyint(1) NOT NULL DEFAULT '0',
  `warning_message` varchar(255) DEFAULT NULL,
  `warning_level` enum('low','medium','high') DEFAULT 'low',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `people`
--
-- --------------------------------------------------------

--
-- Tabellstruktur `people_events`
--

DROP TABLE IF EXISTS `people_events`;
CREATE TABLE IF NOT EXISTS `people_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `event_type` enum('meeting','call','conflict','gaming_session','movie_night','note','cancel_meeting','suggestion','lewding','nightcall') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'note',
  `event_date` date DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Unknown',
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `people_events`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `people_gallery`
--

DROP TABLE IF EXISTS `people_gallery`;
CREATE TABLE IF NOT EXISTS `people_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `people_gallery`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `description`) VALUES
(7, 'view_friends', 'Allows viewing of friends list'),
(8, 'edit_profile', 'Allows editing user profile'),
(9, 'add_user', 'Allows adding new users'),
(10, 'remove_user', 'Allows removing users'),
(11, 'delete_user', 'Allows permanently deleting users'),
(12, 'add_profile', 'Allows adding new profiles'),
(13, 'delete_profile', 'Allows deleting profiles'),
(14, 'view_roles', 'Allows viewing of roles'),
(15, 'edit_roles', 'Allows editing roles'),
(16, 'manage_smtp_settings', 'Allows editing SMTP settings'),
(17, 'manage_system_settings', 'Allows editing core system settings');

-- --------------------------------------------------------

--
-- Tabellstruktur `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(3, 'moderator'),
(2, 'user');

-- --------------------------------------------------------

--
-- Tabellstruktur `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(0, 'current_version', '2.0.0'),
(1, 'site_url', 'http://192.168.1.8'),
(2, 'site_title', 'Friend Manager v3'),
(3, 'site_description', 'A description of the site'),
(4, 'support_email', 'support@lumavex.com'),
(5, 'smtp_host', 'smtp.sendgrid.net'),
(6, 'smtp_port', '587'),
(7, 'smtp_user', 'apikey'),
(8, 'smtp_pass', 'your-password'),
(9, 'smtp_encryption', 'TLS'),
(12, 'time_format', '24-hour'),
(13, 'smtp_provider', 'sendgrid');

-- --------------------------------------------------------

--
-- Tabellstruktur `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `description` text,
  `priority` enum('High','Medium','Low') DEFAULT 'Medium',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `task_categories`
--

DROP TABLE IF EXISTS `task_categories`;
CREATE TABLE IF NOT EXISTS `task_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `task_projects`
--

DROP TABLE IF EXISTS `task_projects`;
CREATE TABLE IF NOT EXISTS `task_projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `banned` tinyint(1) DEFAULT '0',
  `ban_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `users`
--
-- --------------------------------------------------------

--
-- Tabellstruktur `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
