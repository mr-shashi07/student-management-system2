-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 07:25 PM
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
-- Database: `student_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` varchar(36) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
('1', 'akshay123', 'akshay@example.com', 'Test@123', '2025-11-15 17:15:00'),
('2', 'superadmin', 'superadmin@example.com', '$2y$10$8reKjKBLA2cmQxCcGzPReO5cmdE6nL.iyHHhbc5VRexih3/kHVW.O', '2025-11-15 17:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` varchar(36) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `description`, `duration`, `created_at`) VALUES
('3830440e-98ce-4a36-b9ff-09fc41b0eba1', 'test', 'test101', 'this is testing', '1 year', '2025-11-20 17:17:03'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2001', 'Computer Science', 'CS101', 'Comprehensive computer science program covering programming, algorithms, and data structures', '4 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2002', 'Business Administration', 'BA201', 'Business management, marketing, finance, and entrepreneurship fundamentals', '3 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2003', 'Civil Engineering', 'CE301', 'Structural design, construction management, and infrastructure development', '4 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2004', 'Electronics Engineering', 'EE401', 'Circuit design, embedded systems, and telecommunications', '4 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2005', 'Mechanical Engineering', 'ME501', 'Thermodynamics, manufacturing processes, and machine design', '4 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2006', 'Information Technology', 'IT601', 'Software development, networking, and database management', '3 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2007', 'English Literature', 'EL701', 'Literary analysis, creative writing, and linguistic studies', '3 Years', '2025-11-14 11:57:45'),
('a1f2b8c2-0c10-4d8b-b408-5ae6a66d2008', 'Mathematics', 'MA801', 'Pure and applied mathematics including calculus, algebra, and statistics', '3 Years', '2025-11-14 11:57:45'),
('ed813387-7702-40be-8c5a-342bd78d5e5b', 'Hyper Testing', 'test301', 'this is one of the best course', '1 year', '2025-11-16 12:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` varchar(36) NOT NULL,
  `student_id` varchar(36) NOT NULL,
  `course_id` varchar(36) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `status`, `enrollment_date`) VALUES
('019b4892-4040-495a-b776-ace879f316d7', '429d0c6a-d625-46bb-87c8-a75fde7c389c', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2001', 'active', '2025-12-23 16:51:11'),
('0a48115a-f79a-4d07-ad82-d55c407fca2c', '429d0c6a-d625-46bb-87c8-a75fde7c389c', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2003', 'active', '2025-12-21 18:01:21'),
('0dd425c1-1822-4c25-8415-01ab354386e4', '429d0c6a-d625-46bb-87c8-a75fde7c389c', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2004', 'active', '2025-12-23 16:49:17'),
('2a1d327b-e9c4-4385-b05e-7fd1e2424b50', '429d0c6a-d625-46bb-87c8-a75fde7c389c', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2002', 'active', '2025-12-21 18:01:20'),
('5fcfe32f-f96d-4cc0-8cda-13ef3fee595b', 'd790c12e-1494-403f-9285-5c5a2ee9ade4', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2001', 'active', '2025-11-20 17:54:36'),
('7ee24c49-fd3a-4fc7-8c1d-27ae4d3473cf', '13067836-c5e8-49a2-8004-8c4667bb9c99', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2008', 'active', '2025-11-20 17:47:47'),
('f8b7fd04-c93d-4e46-b624-4a7543d8c1c5', '429d0c6a-d625-46bb-87c8-a75fde7c389c', 'a1f2b8c2-0c10-4d8b-b408-5ae6a66d2005', 'active', '2025-12-23 17:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` varchar(36) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `admin_id` varchar(151) NOT NULL,
  `created_by` varchar(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `admin_id`, `created_by`, `created_at`) VALUES
('21d96520-c151-11f0-85b3-3c95098e0316', 'Welcome to New Academic Year', 'We welcome all students to the new academic year 2024-25. Please check your course schedules and complete the enrollment process.', '', NULL, '2025-11-14 11:57:45'),
('21d97c4d-c151-11f0-85b3-3c95098e0316', 'Library Timing Update', 'The library will now remain open until 10 PM on weekdays. Students can utilize extended hours for their studies.', '', NULL, '2025-11-14 11:57:45'),
('21d97fbd-c151-11f0-85b3-3c95098e0316', 'Examination Schedule Released', 'The semester examination schedule has been released. Please check the notice board and prepare accordingly.', '', NULL, '2025-11-14 11:57:45');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(36) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `address`, `password_hash`, `created_at`) VALUES
('13067836-c5e8-49a2-8004-8c4667bb9c99', 'chandan', 'chandan@gmail.com', '5555544444', 'mmandla madhya pradesh', '$2y$10$5HP7ytjsKQk6SoX7R0S.eOANyUooofIbKwnnmuK7THI1UsmN0Mk6y', '2025-11-20 17:16:21'),
('429d0c6a-d625-46bb-87c8-a75fde7c389c', 'aadarsh singh', 'aadarsh115@gmail.com', '1221346570', 'xyzf, jabalpur madhya pradesh', '$2y$10$TUGAm7kLu/DcZoBSQx8foOQumFmKFVKpRMYiWVwcU0rjIus/lQyB6', '2025-12-21 17:16:58'),
('d790c12e-1494-403f-9285-5c5a2ee9ade4', 'akshay', 'akshay@gmail.com', '1234554321', 'mandla, madhya pradesh', '$2y$10$INW4R/t0RLc.FtPX.olt8OKIZsXAxYGkXbp5FN8eNfq0rbWzcxicm', '2025-11-20 17:06:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
