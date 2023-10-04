
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `id` int NOT NULL AUTO_INCREMENT,
  `displayname` varchar(255) NOT NULL,
  `gender` enum('Female','Male') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `First_Name` varchar(50) DEFAULT NULL,
  `Last_Name` varchar(50) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `Date_of_Birth` date DEFAULT NULL,
  `Interests` varchar(255) DEFAULT NULL,
  `date_of_added` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Phone_Number` varchar(20) DEFAULT NULL,
  `friend_group` enum('New Online Friend','New Friend','Friend','Best Friend','Acquaintance','Colleague','School/University Friend','Childhood Friend','Social Club Member','Sports Team Member','Neighbor','Workmate','Online Friend','Travel Buddy','Hobby Group Member','Study Group Member','Support Group Member') NOT NULL,
  `Comments` varchar(255) DEFAULT NULL,
  `mute` enum('True','False') NOT NULL DEFAULT 'False',
  `deaf` enum('True','False') NOT NULL DEFAULT 'False',
  `twitter` varchar(255) DEFAULT NULL,
  `twitch` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `discord` varchar(255) DEFAULT NULL,
  `discord_server` varchar(255) DEFAULT NULL,
  `blocked` enum('True','False') NOT NULL,
  `defriended` enum('True','False') NOT NULL,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `people_events`
--

DROP TABLE IF EXISTS `people_events`;
CREATE TABLE IF NOT EXISTS `people_events` (
  `ID` int NOT NULL,
  `people_ID` int DEFAULT NULL,
  `Event_Date` date DEFAULT NULL,
  `Event_Description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Event_Type` enum('Added','Removed','Blocked','Argument','Updated','Apology') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL,
  `Site_Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `Site_Name` (`Site_Name`(250))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `Site_Name`) VALUES
(0, 'Site Name');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
