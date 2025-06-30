-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2025 at 08:36 PM
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
-- Database: `user_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `valid_id_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `reservation_time_and_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_questions`
--

CREATE TABLE `security_questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_questions`
--

INSERT INTO `security_questions` (`id`, `question`, `is_active`, `created_at`) VALUES
(1, 'What was the name of your first pet?', 1, '2025-06-30 15:23:18'),
(2, 'What is your mother\'s maiden name?', 1, '2025-06-30 15:23:18'),
(3, 'What city were you born in?', 1, '2025-06-30 15:23:18'),
(4, 'What was the name of your elementary school?', 1, '2025-06-30 15:23:18'),
(5, 'What is your favorite food?', 1, '2025-06-30 15:23:18'),
(6, 'What was your childhood nickname?', 1, '2025-06-30 15:23:18'),
(7, 'What is the name of your best friend?', 1, '2025-06-30 15:23:18'),
(8, 'What was your first job?', 1, '2025-06-30 15:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_reserved` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_type`, `price`, `is_reserved`, `description`, `photo_path`, `size`) VALUES
(1, 'Single Room', 5000.00, 1, 'Cozy room with private bathroom', 'images/Dr1.jpg', '40'),
(2, 'Double Room', 3500.00, 1, 'Shared room with two beds and common bath', 'images/Dr2.jpg', '70'),
(3, 'Studio Unit', 7300.00, 0, 'Studio type with kitchenette and own bath', 'images/Dr3.jpg', '100'),
(4, 'Single Room', 4000.00, 0, 'Simple unfurnished room', 'images/Dr4.jpg', '30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `email`, `contact_number`, `password`, `role`, `created_at`, `birthday`) VALUES
(2, 'Patricia Therese', 'Damaso', 'Pagayoya', 'damasopatricia12@gmail.com', '09199559795', '$2y$10$CglYNCkA4QPjYk0ocyQu2usWBj.K0fzRJyHl6dEPxqF4hehXrSxcO', 'user', '2025-06-29 09:14:38', '2004-12-10'),
(3, 'Marthel', 'Cestbon', 'Quinn', 'MarthelCestbon@gmail.com', '09198113353', '$2y$10$WrJB5cZQC8ZTtnkkSYf0Ce3AK0eWzw5oh6ChFIQhMdsj0R89wiUOm', 'user', '2025-06-29 09:39:20', '2004-10-01'),
(4, 'Joe', 'James', 'John', 'JohnJames@gmail.com', '09199442562', '$2y$10$kzmACU.UPzmmLm546pAu7uk0KFleob.yMFhRbAqUPUtXqTpIWq3k6', 'user', '2025-06-30 03:03:58', '2997-03-20'),
(5, 'System', 'Administrator', NULL, 'admin@dormmate.com', NULL, '$2y$10$yYsTOlOwxTnqWchE5R43rOgfjtsNzo8z/iFzjoJ5KwylC0w.mdT1y', 'admin', '2025-06-30 16:02:49', '1990-01-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `security_questions`
--
ALTER TABLE `security_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_questions`
--
ALTER TABLE `security_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
