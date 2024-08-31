-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2024 at 07:25 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `textidc`
--

-- --------------------------------------------------------

--
-- Table structure for table `nusers`
--

CREATE TABLE `nusers` (
  `id` int(11) NOT NULL,
  `studyid` varchar(100) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `typedDate` date NOT NULL,
  `typedTime` time NOT NULL,
  `keyboard` varchar(30) NOT NULL,
  `kbseq` int(11) NOT NULL,
  `language` varchar(10) NOT NULL,
  `sessions` int(11) NOT NULL,
  `nmph` int(11) NOT NULL,
  `phraseNumber` int(11) NOT NULL,
  `phraseShown` varchar(400) NOT NULL,
  `phraseTyped` varchar(400) NOT NULL,
  `flag` int(11) NOT NULL,
  `editdistance1` int(11) NOT NULL,
  `editdistance2` int(11) NOT NULL,
  `editdistance3` int(11) NOT NULL,
  `typingTime` int(11) NOT NULL,
  `backspace` int(11) NOT NULL,
  `IncorrNF1` int(11) NOT NULL,
  `IncorrNF2` int(11) NOT NULL,
  `IncorrNF3` int(11) NOT NULL,
  `IncorrF` int(11) NOT NULL,
  `Correct1` int(11) NOT NULL,
  `Correct2` int(11) NOT NULL,
  `Correct3` int(11) NOT NULL,
  `Fixed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nusers`
--
ALTER TABLE `nusers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nusers`
--
ALTER TABLE `nusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35846;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
