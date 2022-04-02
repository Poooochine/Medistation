-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2022 at 11:18 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medistation`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `AccountID` varchar(25) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Pword` varchar(255) DEFAULT NULL,
  `AccType` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`AccountID`, `Email`, `Pword`, `AccType`, `isActive`) VALUES
('cli6213fa8da5130', 'janedoe@gmail.com', '$2y$10$5aBkPfDOIWvNzp7VShzif.J2NXO.89UreiRy3QGsSzpAP9dYSfFyW', 'client', 1),
('phr6215106f8b8c9', 'admin@localhost', '$2y$10$8C1VXwHov/rW6OiqI2Khl.TqOLFNmxTm/sz2vm.uBJuy0WMluTKDm', 'System Admin', 1),
('phy621512b48f885', 'test@localhost', '$2y$10$5aBkPfDOIWvNzp7VShzif.J2NXO.89UreiRy3QGsSzpAP9dYSfFyW', 'Physician', 1);

-- --------------------------------------------------------

--
-- Table structure for table `activateaccount`
--

CREATE TABLE `activateaccount` (
  `id` int(11) NOT NULL,
  `AccountID` varchar(25) DEFAULT NULL,
  `activator` varchar(50) DEFAULT NULL,
  `token` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `activateaccount`
--

INSERT INTO `activateaccount` (`id`, `AccountID`, `activator`, `token`) VALUES
(6, 'phy622bc928d4fe6', '5c728da28628852a', '3d1a32b951cfb1fb');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `apptID` varchar(25) NOT NULL,
  `apptDate` date DEFAULT NULL,
  `apptTime` time DEFAULT NULL,
  `clientID` varchar(25) NOT NULL,
  `physicianID` varchar(25) NOT NULL,
  `apptStatus` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `clientID` varchar(25) NOT NULL,
  `FName` varchar(255) DEFAULT NULL,
  `LName` varchar(255) DEFAULT NULL,
  `Gender` varchar(255) DEFAULT NULL,
  `DOB` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Street` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `Parish` varchar(255) DEFAULT NULL,
  `Country` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `physicianID` varchar(25) DEFAULT NULL,
  `AccountID` varchar(25) NOT NULL,
  `registrationStatus` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`clientID`, `FName`, `LName`, `Gender`, `DOB`, `Phone`, `Street`, `City`, `Parish`, `Country`, `image`, `physicianID`, `AccountID`, `registrationStatus`) VALUES
('6213fa8db398a', 'Jane', 'Doe', NULL, NULL, '+1(876) 545-4151', '13 Real Road', 'Kingston 17', 'St. Andrew', 'Jamaica', 'uploads/1647318763.png', NULL, 'cli6213fa8da5130', 'Complete');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `itemID` int(11) NOT NULL,
  `itemName` varchar(255) DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `clientID` varchar(25) NOT NULL,
  `pharmacistID` varchar(25) NOT NULL,
  `pymtID` varchar(25) NOT NULL,
  `total` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pymtID` varchar(25) NOT NULL,
  `cardName` varchar(255) DEFAULT NULL,
  `cardNum` int(11) DEFAULT NULL,
  `cardCVC` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacist`
--

CREATE TABLE `pharmacist` (
  `pharmacistID` varchar(25) NOT NULL,
  `FName` varchar(255) DEFAULT NULL,
  `LName` varchar(255) DEFAULT NULL,
  `Gender` varchar(50) DEFAULT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `AccountID` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `physician`
--

CREATE TABLE `physician` (
  `physicianID` varchar(25) NOT NULL,
  `FName` varchar(255) DEFAULT NULL,
  `LName` varchar(255) DEFAULT NULL,
  `Gender` varchar(255) DEFAULT NULL,
  `Speciality` varchar(255) DEFAULT NULL,
  `AccountID` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `physician`
--

INSERT INTO `physician` (`physicianID`, `FName`, `LName`, `Gender`, `Speciality`, `AccountID`) VALUES
('phy621512b49d6a2', 'James', 'Jameson', 'Male', 'Mental Health', 'phy621512b48f885');

-- --------------------------------------------------------

--
-- Table structure for table `physicianavailability`
--

CREATE TABLE `physicianavailability` (
  `id` int(11) NOT NULL,
  `avaDate` date NOT NULL,
  `avaTime` time NOT NULL,
  `status` varchar(50) NOT NULL,
  `physicianID` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `physicianavailability`
--

INSERT INTO `physicianavailability` (`id`, `avaDate`, `avaTime`, `status`, `physicianID`) VALUES
(1, '2022-02-23', '13:37:00', 'Closed', 'phy621512b49d6a2'),
(2, '2022-02-23', '15:45:00', 'Closed', 'phy621512b49d6a2'),
(3, '2022-02-25', '17:50:00', 'Closed', 'phy621512b49d6a2'),
(4, '2022-04-26', '10:30:00', 'Booked', 'phy621512b49d6a2'),
(5, '2022-03-30', '15:30:00', 'Closed', 'phy621512b49d6a2'),
(6, '2022-03-01', '15:45:00', 'Booked', 'phy621512b49d6a2'),
(7, '2022-03-03', '14:30:00', 'Closed', 'phy621512b49d6a2'),
(8, '2022-03-31', '13:30:00', 'Booked', 'phy621512b49d6a2'),
(10, '2022-04-16', '10:30:00', 'Booked', 'phy621512b49d6a2'),
(11, '2022-03-18', '10:30:00', 'Booked', 'phy621512b49d6a2'),
(12, '2022-03-25', '12:30:00', 'Booked', 'phy621512b49d6a2'),
(13, '2022-03-26', '21:30:00', 'Booked', 'phy621512b49d6a2'),
(14, '2022-04-09', '09:00:00', 'Booked', 'phy621512b49d6a2'),
(15, '2022-04-16', '10:30:00', 'Booked', 'phy621512b49d6a2'),
(16, '2022-04-15', '09:30:00', 'Booked', 'phy621512b49d6a2'),
(17, '2022-04-26', '11:30:00', 'Booked', 'phy621512b49d6a2'),
(18, '2022-04-02', '10:30:00', 'Booked', 'phy621512b49d6a2'),
(19, '2022-04-09', '10:30:00', 'Booked', 'phy621512b49d6a2');

-- --------------------------------------------------------

--
-- Table structure for table `physicianprofile`
--

CREATE TABLE `physicianprofile` (
  `physicianID` varchar(25) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `phone` varchar(25) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `yearsOfExperience` int(11) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `physicianprofile`
--

INSERT INTO `physicianprofile` (`physicianID`, `description`, `phone`, `image`, `yearsOfExperience`, `education`, `language`, `location`) VALUES
('phy621512b49d6a2', 'The introductory paragraph of your therapist job description is a great place to briefly describe your company and working environment. Take a few sentences to sell your company to your ideal therapist candidate, explaining what sets you apart from other practices and organizations.', '+1(848) 454-5415', 'uploads/1647406286.jpg', 5, 'UWI', 'English, Spanish', 'Kingston, Jamaica');

-- --------------------------------------------------------

--
-- Table structure for table `resetaccount`
--

CREATE TABLE `resetaccount` (
  `AccountID` varchar(25) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `resetKey` varchar(100) DEFAULT NULL,
  `createdAt` varchar(50) DEFAULT NULL,
  `expiresAt` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `resetaccount`
--

INSERT INTO `resetaccount` (`AccountID`, `token`, `resetKey`, `createdAt`, `expiresAt`) VALUES
('phy621512b48f885', '24b7c22926f67c83', 'e6dc89965488b526', '1647406943', '1647410543');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`) VALUES
('TestRoom'),
('9098-aeb8-74e7'),
('e9e3-12c5-c057'),
('9068-c4e7-92f8'),
('accb-181d-b7cb'),
('4256-f3e4-1cce'),
('764b-2328-6b7f'),
('12d0-67b2-b6ea'),
('65c1-936c-c5bc'),
('7d29-7050-05a3');

-- --------------------------------------------------------

--
-- Table structure for table `sysadmin`
--

CREATE TABLE `sysadmin` (
  `sysAdminID` varchar(25) NOT NULL,
  `FName` varchar(255) DEFAULT NULL,
  `LName` varchar(255) DEFAULT NULL,
  `AccountID` varchar(25) NOT NULL,
  `Gender` varchar(50) DEFAULT NULL,
  `Phone` varchar(50) DEFAULT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sysadmin`
--

INSERT INTO `sysadmin` (`sysAdminID`, `FName`, `LName`, `AccountID`, `Gender`, `Phone`, `image`) VALUES
('phy6215106f9c880', 'John', 'Doe', 'phr6215106f8b8c9', 'Male', '+1(848) 454-5415', 'https://www.pngitem.com/pimgs/m/146-1468479_my-profile-icon-blank-profile-picture-circle-hd.png');

-- --------------------------------------------------------

--
-- Table structure for table `upcomingappointments`
--

CREATE TABLE `upcomingappointments` (
  `id` int(11) NOT NULL,
  `physicianID` varchar(25) NOT NULL,
  `clientID` varchar(25) NOT NULL,
  `aptDate` date NOT NULL,
  `aptTime` time NOT NULL,
  `room` varchar(25) DEFAULT NULL,
  `roomGenerated` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `upcomingappointments`
--

INSERT INTO `upcomingappointments` (`id`, `physicianID`, `clientID`, `aptDate`, `aptTime`, `room`, `roomGenerated`, `status`) VALUES
(1, 'phy621512b49d6a2', '6213fa8db398a', '2022-02-23', '16:00:00', '555c-ac68-70f4', 1, 'Completed'),
(3, 'phy621512b49d6a2', '6213fa8db398a', '2022-02-28', '12:30:00', '9068-c4e7-92f8', 1, 'Pending'),
(4, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-12', '18:30:00', '4256-f3e4-1cce', 1, 'Pending'),
(5, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-31', '13:30:00', '65c1-936c-c5bc', 1, 'Pending'),
(6, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-01', '15:45:00', 'accb-181d-b7cb', 1, 'Pending'),
(7, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-16', '10:30:00', NULL, 0, 'Pending'),
(8, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-18', '10:30:00', '764b-2328-6b7f', 1, 'Pending'),
(9, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-26', '10:30:00', NULL, 0, 'Pending'),
(17, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-25', '12:30:00', '12d0-67b2-b6ea', 1, 'Pending'),
(18, 'phy621512b49d6a2', '6213fa8db398a', '2022-03-26', '21:30:00', '7d29-7050-05a3', 1, 'Pending'),
(19, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-09', '09:00:00', NULL, 0, 'Pending'),
(23, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-15', '09:30:00', NULL, 0, 'Pending'),
(24, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-26', '11:30:00', NULL, 0, 'Pending'),
(25, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-02', '10:30:00', NULL, 0, 'Pending'),
(26, 'phy621512b49d6a2', '6213fa8db398a', '2022-04-09', '10:30:00', NULL, 0, 'Pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `activateaccount`
--
ALTER TABLE `activateaccount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`apptID`),
  ADD KEY `clientID` (`clientID`),
  ADD KEY `physicianID` (`physicianID`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`clientID`),
  ADD KEY `physicianID` (`physicianID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `clientID` (`clientID`),
  ADD KEY `itemID` (`itemID`),
  ADD KEY `pharmacistID` (`pharmacistID`),
  ADD KEY `pymtID` (`pymtID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pymtID`);

--
-- Indexes for table `pharmacist`
--
ALTER TABLE `pharmacist`
  ADD PRIMARY KEY (`pharmacistID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `physician`
--
ALTER TABLE `physician`
  ADD PRIMARY KEY (`physicianID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `physicianavailability`
--
ALTER TABLE `physicianavailability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `physicianID` (`physicianID`);

--
-- Indexes for table `physicianprofile`
--
ALTER TABLE `physicianprofile`
  ADD PRIMARY KEY (`physicianID`);

--
-- Indexes for table `resetaccount`
--
ALTER TABLE `resetaccount`
  ADD PRIMARY KEY (`AccountID`);

--
-- Indexes for table `sysadmin`
--
ALTER TABLE `sysadmin`
  ADD PRIMARY KEY (`sysAdminID`),
  ADD KEY `AccountID` (`AccountID`);

--
-- Indexes for table `upcomingappointments`
--
ALTER TABLE `upcomingappointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `physicianID` (`physicianID`),
  ADD KEY `clientID` (`clientID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activateaccount`
--
ALTER TABLE `activateaccount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `physicianavailability`
--
ALTER TABLE `physicianavailability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `upcomingappointments`
--
ALTER TABLE `upcomingappointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `Appointment_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`),
  ADD CONSTRAINT `Appointment_ibfk_2` FOREIGN KEY (`physicianID`) REFERENCES `physician` (`physicianID`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `Client_ibfk_1` FOREIGN KEY (`physicianID`) REFERENCES `physician` (`physicianID`),
  ADD CONSTRAINT `Client_ibfk_2` FOREIGN KEY (`AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `Orders_ibfk_1` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`),
  ADD CONSTRAINT `Orders_ibfk_2` FOREIGN KEY (`itemID`) REFERENCES `item` (`itemID`),
  ADD CONSTRAINT `Orders_ibfk_3` FOREIGN KEY (`pharmacistID`) REFERENCES `pharmacist` (`pharmacistID`),
  ADD CONSTRAINT `Orders_ibfk_4` FOREIGN KEY (`pymtID`) REFERENCES `payment` (`pymtID`);

--
-- Constraints for table `pharmacist`
--
ALTER TABLE `pharmacist`
  ADD CONSTRAINT `Pharmacist_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `physician`
--
ALTER TABLE `physician`
  ADD CONSTRAINT `Physician_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `physicianavailability`
--
ALTER TABLE `physicianavailability`
  ADD CONSTRAINT `physicianavailability_ibfk_1` FOREIGN KEY (`physicianID`) REFERENCES `physician` (`physicianID`);

--
-- Constraints for table `physicianprofile`
--
ALTER TABLE `physicianprofile`
  ADD CONSTRAINT `physicianprofile_ibfk_1` FOREIGN KEY (`physicianID`) REFERENCES `physician` (`physicianID`);

--
-- Constraints for table `resetaccount`
--
ALTER TABLE `resetaccount`
  ADD CONSTRAINT `resetaccount_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `sysadmin`
--
ALTER TABLE `sysadmin`
  ADD CONSTRAINT `sysAdmin_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `account` (`AccountID`);

--
-- Constraints for table `upcomingappointments`
--
ALTER TABLE `upcomingappointments`
  ADD CONSTRAINT `upcomingappointments_ibfk_1` FOREIGN KEY (`physicianID`) REFERENCES `physician` (`physicianID`),
  ADD CONSTRAINT `upcomingappointments_ibfk_2` FOREIGN KEY (`clientID`) REFERENCES `client` (`clientID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
