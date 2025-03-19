-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 10:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travelblog`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `idArticles` int(11) NOT NULL,
  `Title` varchar(120) NOT NULL,
  `Content` longtext NOT NULL,
  `ProfileImg` varchar(45) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Author` int(11) NOT NULL,
  `Destination` int(11) NOT NULL,
  `DatePublic` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`idArticles`, `Title`, `Content`, `ProfileImg`, `Image`, `Author`, `Destination`, `DatePublic`) VALUES
(7, 'Japan – Where Tradition Meets Modernity', 'Japan blends ancient history with ultra-modern tech. Tokyo’s neon lights never stop shining, while Kyoto’s peaceful temples and geishas in kimonos show off old-world charm. Spring brings stunning cherry blossoms, autumn bursts into fiery reds, and the food—sushi, ramen, matcha desserts—is next-level.\r\n\r\n• Tokyo – A never-sleeping city mixing skyscrapers and sacred shrines.\r\n• Kyoto – Old Japan with serene temples and mysterious alleys.\r\n• Nara – Historic town where tame deer roam idyllic parks.', '', 'japan.jpg', 5, 3, '2025-03-17');

-- --------------------------------------------------------

--
-- Table structure for table `destination`
--

CREATE TABLE `destination` (
  `idDestination` int(11) NOT NULL,
  `Name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `destination`
--

INSERT INTO `destination` (`idDestination`, `Name`) VALUES
(1, 'Paris'),
(2, 'New York'),
(3, 'Japan'),
(4, 'Plzen');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `idUsers` int(11) NOT NULL,
  `UserName` varchar(45) NOT NULL,
  `UserEmail` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  `Role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUsers`, `UserName`, `UserEmail`, `Password`, `Role`) VALUES
(1, 'admin', 'admin@example.com', 'adminpass', 'admin'),
(3, 'pepa', 'pepa@pepa.pepa', 'pepa', 'delegate'),
(4, 'test', 'test@test.test', 'test', 'delegate'),
(5, 'jakub vana', 'jakubvana7@gmail.com', 'Ahoj123', 'delegate');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`idArticles`),
  ADD KEY `Author` (`Author`),
  ADD KEY `Destination` (`Destination`);

--
-- Indexes for table `destination`
--
ALTER TABLE `destination`
  ADD PRIMARY KEY (`idDestination`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUsers`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `idArticles` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `destination`
--
ALTER TABLE `destination`
  MODIFY `idDestination` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `idUsers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`Author`) REFERENCES `users` (`idUsers`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`Destination`) REFERENCES `destination` (`idDestination`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
