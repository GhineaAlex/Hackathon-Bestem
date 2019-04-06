-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2019 at 04:45 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steam`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `ID` int(11) NOT NULL,
  `openid` decimal(39,0) NOT NULL,
  `provider` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `personaname` varchar(255) NOT NULL DEFAULT '',
  `profileurl` longtext NOT NULL,
  `avatar` longtext NOT NULL,
  `avatar_medium` longtext NOT NULL,
  `avatar_full` longtext NOT NULL,
  `chat_session` varchar(32) DEFAULT NULL,
  `address` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`ID`, `openid`, `provider`, `email`, `personaname`, `profileurl`, `avatar`, `avatar_medium`, `avatar_full`, `chat_session`, `address`) VALUES
(1, '117635027950546000641', 'Google', 'ghinea.alexandru.george@gmail.com', 'Ghinea Alexandru George', 'https://plus.google.com/u/0/117635027950546000641', 'https://i.pinimg.com/originals/c9/b1/6e/c9b16eceedd12986cd5b762474103507.webp', 'https://i.pinimg.com/originals/c9/b1/6e/c9b16eceedd12986cd5b762474103507.webp', 'https://i.pinimg.com/originals/c9/b1/6e/c9b16eceedd12986cd5b762474103507.webp', 'c78b51ea8804ff925669b06d3cd29d68', '');

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `ID` int(2) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `connections`
--

INSERT INTO `connections` (`ID`, `number`) VALUES
(1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `steamid` (`openid`);

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `ID` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
