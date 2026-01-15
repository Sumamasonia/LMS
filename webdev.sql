-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2025 at 03:11 AM
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
-- Database: `webdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `booking_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `num_persons` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `user_id`, `department`, `booking_date`, `time_slot`, `num_persons`, `status`, `created_at`) VALUES
(1, 2, 3, 'Artificial Intelligence', '2025-12-22', '11:00 - 12:00', 200, 'approved', '2025-12-19 02:49:34'),
(2, 1, 3, 'Artificial Intelligence', '2025-12-25', '08:00 - 09:00', 500, 'approved', '2025-12-19 10:46:34'),
(3, 3, 3, 'Artificial Intelligence', '2025-12-30', '08:00 - 09:00', 50, 'rejected', '2025-12-19 11:03:01'),
(4, 2, 6, 'Computer Science', '2025-12-26', '08:00 - 09:00', 600, 'approved', '2025-12-23 15:31:05');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`) VALUES
(1, 'Computer Science', 'Focuses on AI, Software Engineering, and Data Science.'),
(3, 'Artificial Intelligence', 'Focuses on building intelligent systems through machine learning, data science, and advanced computing.');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` text NOT NULL,
  `type` enum('news','event','notification') DEFAULT 'notification',
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `content`, `type`, `department`, `created_at`) VALUES
(1, 'Official Department Noticeboard', 'Welcome to the Department Noticeboard.\r\nStay updated with official announcements, academic schedules, departmental activities, and important student information.\r\nStudents are advised to check the noticeboard regularly for timely updates related to courses, exams, events, and administrative notifications.', 'notification', NULL, '2025-12-04 02:45:20'),
(3, 'Fall Semester Events', 'Workshops, seminars, competitions, and cultural activities will be held throughout the semester.\r\nEvent schedule and registration details will be posted soon. Stay updated!', 'event', NULL, '2025-12-04 02:54:55'),
(4, 'for cs students', 'sports event', 'notification', NULL, '2025-12-05 12:02:20'),
(6, '📢 News for BS Artificial Intelligence (AI) Students', 'All AI students are informed that upcoming academic activities, including classes, assignments, and assessments, will proceed as per the official timetable. Students are advised to regularly check the LMS for updates, announcements, and important learning resources related to their courses.', 'news', 'Artificial Intelligence', '2025-12-23 16:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `capacity`, `created_at`) VALUES
(1, 'Iqbal Auditorium', 10000, '2025-12-19 02:45:53'),
(2, 'Conference Room', 500, '2025-12-19 02:47:23'),
(3, 'Exibition Centre', 100, '2025-12-19 02:48:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `roll_number` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','faculty') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `faculty_type` enum('instructor','focal_person') DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `roll_number`, `password`, `role`, `department`, `faculty_type`, `profile_pic`, `created_at`) VALUES
(1, 'Sumama Sonia', 'sumama@gmail.com', NULL, 'sumama123', 'admin', NULL, NULL, 'default.png', '2025-12-04 02:14:12'),
(2, 'Maheer Rizwan', NULL, '101', 'maheer123', 'student', 'Computer Science', NULL, 'profile1.jpg', '2025-12-04 02:15:15'),
(3, 'Ajwa ', 'ajwa@gmail.com', NULL, 'ajwa123', 'faculty', 'Artificial Intelligence', 'focal_person', 'default.png', '2025-12-04 02:15:47'),
(6, 'Zoha', 'zoha@gmail.com', NULL, 'zoha123', 'faculty', 'Computer Science', 'focal_person', 'default.png', '2025-12-19 11:08:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `roll_number` (`roll_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
