-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2023 at 06:19 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secprogkelas`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_config`
--

CREATE TABLE `app_config` (
  `key` varchar(15) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `app_config`
--

INSERT INTO `app_config` (`key`, `value`, `created_at`) VALUES
('initialized', 1, '2019-04-30 07:13:37');

-- --------------------------------------------------------

--
-- Table structure for table `communications`
--

CREATE TABLE `communications` (
  `id` int(11) NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `recipient_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `send_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `files` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `communications`
--

INSERT INTO `communications` (`id`, `sender_id`, `recipient_id`, `title`, `message`, `send_at`, `files`) VALUES
(2, 1, 1, 'Important Request', 'Dear admin,\r\nwe need backup now!\r\nA virus is attacking and we are low on supply! SOS!', '2019-05-03 06:02:33', ''),
(3, 1, 4, 'Score Recap Request', 'Dude, please send me score recap from last night. Need it for the boss. Thanks!', '2019-05-03 06:13:10', ''),
(4, 2, 3, 'superbum', '123123', '2019-08-02 05:22:25', ''),
(5, 2, 2, 'qweqwe', 'dasdadasd', '2019-08-02 05:23:36', ''),
(6, 1, 1, 'twad', 'dwadadw', '2023-11-14 15:23:34', '655390f66fa73_IMG_6159.JPG'),
(7, 1, 1, 'Shibal', 'Shibal Sekiyaaa!', '2023-11-14 15:26:03', '6553918bcee5c_ERD.png'),
(8, 1, 4, 'Testing', 'Test', '2023-12-06 09:40:09', '657041793dd49_FortiClientOnlineInstaller.exe');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(16) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'dummy1', 'dummy1', 'dummy1@dummy.com', 'user', 'dummy1dummy1', '2019-04-30 07:13:37', '2019-08-02 04:49:24'),
(2, 'dummy2', 'dummy2', 'dummy2@dummy.com', 'user', 'dummy2dummy2', '2019-04-30 07:13:37', '2019-08-02 04:49:24'),
(3, 'dummy3', 'dummy3', 'dummy3@dummy.com', 'user', 'dummy3dummy3', '2019-04-30 07:13:37', '2019-08-02 04:49:25'),
(5, 'admin', 'ando123', 'admin@gmail.com', 'admin', 'supers3cretp4sswordOyeah123', '2019-04-30 07:13:37', '2019-08-02 04:49:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_config`
--
ALTER TABLE `app_config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `communications`
--
ALTER TABLE `communications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foreign_constraint_recipient` (`recipient_id`),
  ADD KEY `foreign_constraint_sender` (`sender_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `communications`
--
ALTER TABLE `communications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `communications`
--
ALTER TABLE `communications`
  ADD CONSTRAINT `foreign_constraint_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
