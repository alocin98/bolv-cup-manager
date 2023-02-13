-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 13. Feb 2023 um 08:16
-- Server-Version: 8.0.31
-- PHP-Version: 7.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `nachwuchs_cup`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clubs`
--

CREATE TABLE `clubs` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `clubs`
--

INSERT INTO `clubs` (`id`, `name`) VALUES
(1, 'ol norska'),
(2, 'OLG Oberwil'),
(3, 'OLG Herzogenbuchsee'),
(4, 'OLG Hondrich'),
(5, 'ol.biel.seeland'),
(6, 'OLG Thun'),
(7, 'OL Regio Burgdorf'),
(8, 'OLG Bern'),
(9, 'Bucheggberger OL'),
(10, 'OLG Biberist SO'),
(11, 'OLV Langenthal'),
(12, 'OLC Omström Sense'),
(13, 'OLG Huttwil'),
(14, 'Ol Regio Olten');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cups`
--

CREATE TABLE `cups` (
  `season` smallint NOT NULL,
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cups_categories`
--

CREATE TABLE `cups_categories` (
  `cup_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cups_clubs`
--

CREATE TABLE `cups_clubs` (
  `clubId` int NOT NULL,
  `cupId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `races`
--

CREATE TABLE `races` (
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `club` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `solv_id` int NOT NULL,
  `cupId` int NOT NULL,
  `calculation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NACHWUCHSCUP_STANDARD',
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `results`
--

CREATE TABLE `results` (
  `runnerId` int NOT NULL,
  `raceId` int NOT NULL,
  `points` smallint NOT NULL,
  `striked` tinyint(1) NOT NULL DEFAULT '0',
  `cupId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `runners`
--

CREATE TABLE `runners` (
  `id` int NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `year` smallint DEFAULT NULL,
  `club` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `canton` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'BE',
  `category` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `cupId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `clubs`
--
ALTER TABLE `clubs`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `cups`
--
ALTER TABLE `cups`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `cups_categories`
--
ALTER TABLE `cups_categories`
  ADD KEY `cups_categories_ibfk_1` (`cup_id`);

--
-- Indizes für die Tabelle `cups_clubs`
--
ALTER TABLE `cups_clubs`
  ADD KEY `cupId` (`cupId`),
  ADD KEY `cups_clubs_ibfk_2` (`clubId`);

--
-- Indizes für die Tabelle `races`
--
ALTER TABLE `races`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `solv_id_2` (`solv_id`),
  ADD KEY `cupId` (`cupId`);

--
-- Indizes für die Tabelle `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`runnerId`,`raceId`,`cupId`),
  ADD KEY `results_ibfk_3` (`raceId`),
  ADD KEY `cupId` (`cupId`);

--
-- Indizes für die Tabelle `runners`
--
ALTER TABLE `runners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cupId` (`cupId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `cups`
--
ALTER TABLE `cups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `races`
--
ALTER TABLE `races`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `runners`
--
ALTER TABLE `runners`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `cups_categories`
--
ALTER TABLE `cups_categories`
  ADD CONSTRAINT `cups_categories_ibfk_1` FOREIGN KEY (`cup_id`) REFERENCES `cups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `cups_clubs`
--
ALTER TABLE `cups_clubs`
  ADD CONSTRAINT `cups_clubs_ibfk_1` FOREIGN KEY (`cupId`) REFERENCES `cups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cups_clubs_ibfk_2` FOREIGN KEY (`clubId`) REFERENCES `clubs` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `races`
--
ALTER TABLE `races`
  ADD CONSTRAINT `races_ibfk_1` FOREIGN KEY (`cupId`) REFERENCES `cups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`runnerId`) REFERENCES `runners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_3` FOREIGN KEY (`raceId`) REFERENCES `races` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `results_ibfk_4` FOREIGN KEY (`cupId`) REFERENCES `cups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `runners`
--
ALTER TABLE `runners`
  ADD CONSTRAINT `runners_ibfk_1` FOREIGN KEY (`cupId`) REFERENCES `cups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
