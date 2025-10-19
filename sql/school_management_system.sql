-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 02:34 PM
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
-- Database: `academic_portal`
--
CREATE DATABASE IF NOT EXISTS `academic_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `academic_portal`;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `teacher_id`, `title`, `content`, `created_at`) VALUES
(1, 1, 'Holiday Schedule', 'Please note the changes to the holiday schedule for the winter break.', '2025-09-09 22:25:26'),
(2, 1, 'Final Exam Guidelines', 'Important guidelines for the upcoming final examinations.', '2025-09-09 22:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `instructions` text DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `course_id`, `title`, `instructions`, `deadline`, `file_path`, `created_at`) VALUES
(1, 1, 'Calculus Problems', 'Solve the following calculus problems and show your work.', '2023-12-15 23:59:59', NULL, '2025-09-09 22:25:26'),
(2, 1, 'Algebra Quiz', 'Complete the algebra quiz within the time limit.', '2023-12-20 23:59:59', NULL, '2025-09-09 22:25:26'),
(3, 2, 'Essay Writing', 'Write a 1000-word essay on the provided topic.', '2023-12-18 23:59:59', NULL, '2025-09-09 22:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `submitted_text` text DEFAULT NULL,
  `submitted_file` varchar(255) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`schedule_id`, `course_id`, `day_of_week`, `start_time`, `end_time`, `venue`) VALUES
(1, 1, 'Monday', '09:00:00', '10:30:00', 'Room 101'),
(2, 1, 'Wednesday', '09:00:00', '10:30:00', 'Room 101'),
(3, 2, 'Tuesday', '11:00:00', '12:30:00', 'Room 202'),
(4, 3, 'Thursday', '14:00:00', '15:30:00', 'Lab 1');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `materials` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `teacher_id`, `subject_id`, `title`, `description`, `materials`, `created_at`) VALUES
(1, 1, 1, 'Advanced Mathematics', 'This course covers advanced mathematical concepts including calculus, algebra, and geometry.', NULL, '2025-09-09 22:25:26'),
(2, 1, 2, 'English Literature', 'Study of literary texts from various periods and cultures.', NULL, '2025-09-09 22:25:26'),
(3, 2, 3, 'Physics Fundamentals', 'Introduction to the fundamental principles of physics.', NULL, '2025-09-09 22:25:26');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `grade` varchar(31) NOT NULL,
  `grade_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `grade`, `grade_code`) VALUES
(1, '1', 'G'),
(2, '2', 'G'),
(3, '3', 'G'),
(4, '4', 'G'),
(5, '5', 'G'),
(6, '6', 'G'),
(7, '7', 'G'),
(8, '8', 'G'),
(9, '9', 'G'),
(10, '10', 'G');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `sender_role` enum('Teacher','Student') DEFAULT NULL,
  `receiver_role` enum('Teacher','Student') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `type` enum('Assignment','Grade','Announcement','Message','Schedule') DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `section` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `section`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `date_of_joined` date DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `parent_fname` varchar(50) DEFAULT NULL,
  `parent_lname` varchar(50) DEFAULT NULL,
  `parent_phone_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `fname`, `lname`, `address`, `date_of_birth`, `email_address`, `gender`, `date_of_joined`, `grade`, `section`, `parent_fname`, `parent_lname`, `parent_phone_number`) VALUES
(1, 4, 'John', 'Doe', 'California, Los angeles', '2005-09-12', 'john@school.edu', 'Male', '2022-09-01', 10, 1, 'Michael', 'Doe', '0934567890'),
(2, 5, 'Jane', 'Smith', 'New York, NY', '2006-03-25', 'jane@school.edu', 'Female', '2022-09-01', 10, 1, 'Robert', 'Smith', '0945678901');

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_courses`
--

INSERT INTO `student_courses` (`id`, `student_id`, `course_id`, `enrolled_at`) VALUES
(1, 1, 1, '2025-09-09 22:25:27'),
(2, 1, 2, '2025-09-09 22:25:27'),
(3, 2, 1, '2025-09-09 22:25:27'),
(4, 2, 3, '2025-09-09 22:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject` varchar(31) NOT NULL,
  `subject_code` varchar(31) NOT NULL,
  `grade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject`, `subject_code`, `grade_id`) VALUES
(1, 'Mathematics', 'MATH', 10),
(2, 'English Literature', 'ENG', 10),
(3, 'Physics', 'PHY', 10),
(4, 'Chemistry', 'CHEM', 10),
(5, 'Biology', 'BIO', 10);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `date_of_joined` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `fname`, `lname`, `employee_number`, `address`, `date_of_birth`, `phone_number`, `qualification`, `email_address`, `gender`, `date_of_joined`) VALUES
(1, 2, 'Oliver', 'Noah', 'T001', 'California, Los angeles', '1980-05-15', '09457396789', 'M.Sc in Mathematics', 'oliver@school.edu', 'Male', '2020-01-10'),
(2, 3, 'Sarah', 'Smith', 'T002', 'New York, NY', '1985-08-22', '09451234567', 'Ph.D in English Literature', 'sarah@school.edu', 'Female', '2019-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Teacher','Student','Admin') NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$H7obJEdmLzqqcPy7wQWhsOLUvrgzC8f1Y1or2Gxaza5z1PT0tvLy6', 'Admin', 'admin@school.edu', '2025-09-09 22:25:26'),
(2, 'teacher1', '$2y$10$JruTW/rNZ6CVO4nxYWCrn.GJpiIKMACEPYrK00S7Dk/fkbJIdYau2', 'Teacher', 'teacher1@school.edu', '2025-09-09 22:25:26'),
(3, 'teacher2', '$2y$10$cMSKcHEJcg3K6wbVcxcXGuksgU39i70aEQVKN7ZHrzqTH9oAc3y5m', 'Teacher', 'teacher2@school.edu', '2025-09-09 22:25:26'),
(4, 'student1', '$2y$10$xmtROY8efWeORYiuQDE3SO.eZwscao20QNuLky1Qlr88zDzNNq4gm', 'Student', 'student1@school.edu', '2025-09-09 22:25:26'),
(5, 'student2', '$2y$10$KLFheMWgpLfoiqMuW2LQxOPficlBiSIJ9.wE2qr5yJUbAQ.5VURoO', 'Student', 'student2@school.edu', '2025-09-09 22:25:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `idx_announcements_teacher` (`teacher_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_assignments_course` (`course_id`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `idx_submissions_assignment` (`assignment_id`),
  ADD KEY `idx_submissions_student` (`student_id`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_schedules_course` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_messages_sender` (`sender_id`),
  ADD KEY `idx_messages_receiver` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notifications_user` (`user_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_course` (`student_id`,`course_id`),
  ADD KEY `idx_student_courses_student` (`student_id`),
  ADD KEY `idx_student_courses_course` (`course_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `grade_id` (`grade_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
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
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `class_schedules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`grade_id`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;
--
-- Database: `booking_system`
--
CREATE DATABASE IF NOT EXISTS `booking_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `booking_system`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_reference` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `number_of_people` int(11) DEFAULT 1,
  `special_requests` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `calendar_event_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('email','sms','app') DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_sent` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('mtn_momo','cash','card') DEFAULT 'mtn_momo',
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `price` decimal(10,2) DEFAULT NULL,
  `business_type` enum('hotel','restaurant','clinic','salon') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `duration_minutes`, `price`, `business_type`, `is_active`, `created_at`) VALUES
(1, 'Standard Room', 'Comfortable standard room with basic amenities', 60, 100.00, 'hotel', 1, '2025-09-25 22:08:38'),
(2, 'Dinner Reservation', 'Table for 2-4 people', 60, 0.00, 'restaurant', 1, '2025-09-25 22:08:38'),
(3, 'Medical Checkup', 'General health examination', 60, 50.00, 'clinic', 1, '2025-09-25 22:08:38'),
(4, 'Haircut', 'Professional haircut and styling', 60, 25.00, 'salon', 1, '2025-09-25 22:08:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('customer','admin','staff') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `user_type`, `created_at`, `updated_at`) VALUES
(2, 'System Admin', 'admin@bookingsystem.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'admin', '2025-09-25 22:14:41', '2025-09-25 22:14:41'),
(3, 'james', 'james@gmail.com', '$2y$10$O4bkHUwRZEnKvySZ7vgwdOs6ZcFGh113Q/ybUGsLz78H8p2e1UJmK', '0201952402', 'customer', '2025-09-26 08:00:30', '2025-09-26 08:00:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
--
-- Database: `dhltu`
--
CREATE DATABASE IF NOT EXISTS `dhltu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dhltu`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'f865b53623b121fd34ee5426c792e5c33af8c227');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(100) NOT NULL,
  `year` year(4) NOT NULL,
  `file_path` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `program` varchar(100) NOT NULL,
  `admission_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Database: `dhltu_db`
--
CREATE DATABASE IF NOT EXISTS `dhltu_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dhltu_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-09-03 17:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(200) NOT NULL,
  `year` year(4) NOT NULL,
  `file_path` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `program` varchar(100) NOT NULL,
  `admission_year` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Database: `finance`
--
CREATE DATABASE IF NOT EXISTS `finance` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `finance`;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month_year` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `user_id`, `category_id`, `amount`, `month_year`, `created_at`) VALUES
(1, 2, 5, 500.00, '2024-02-01', '2025-10-06 14:00:21'),
(2, 2, 6, 200.00, '2024-02-01', '2025-10-06 14:00:21'),
(3, 2, 8, 150.00, '2024-02-01', '2025-10-06 14:00:21'),
(4, 3, 5, 300.00, '2024-02-01', '2025-10-06 14:00:21'),
(5, 3, 7, 100.00, '2024-02-01', '2025-10-06 14:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `color`, `user_id`, `created_at`) VALUES
(1, 'Salary', 'income', '#28a745', NULL, '2025-10-06 14:00:20'),
(2, 'Freelance', 'income', '#20c997', NULL, '2025-10-06 14:00:20'),
(3, 'Investment', 'income', '#17a2b8', NULL, '2025-10-06 14:00:20'),
(4, 'Gift', 'income', '#6f42c1', NULL, '2025-10-06 14:00:20'),
(5, 'Food', 'expense', '#dc3545', NULL, '2025-10-06 14:00:20'),
(6, 'Transportation', 'expense', '#fd7e14', NULL, '2025-10-06 14:00:20'),
(7, 'Entertainment', 'expense', '#e83e8c', NULL, '2025-10-06 14:00:20'),
(8, 'Utilities', 'expense', '#6c757d', NULL, '2025-10-06 14:00:20'),
(9, 'Rent', 'expense', '#343a40', NULL, '2025-10-06 14:00:20'),
(10, 'Healthcare', 'expense', '#007bff', NULL, '2025-10-06 14:00:20');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `category_id`, `amount`, `description`, `expense_date`, `created_at`) VALUES
(1, 2, 5, 150.00, 'Groceries', '2024-02-01', '2025-10-06 14:00:21'),
(2, 2, 5, 200.00, 'Restaurant Dinner', '2024-02-05', '2025-10-06 14:00:21'),
(3, 2, 6, 80.00, 'Gas', '2024-02-10', '2025-10-06 14:00:21'),
(4, 2, 8, 120.00, 'Electricity Bill', '2024-02-12', '2025-10-06 14:00:21'),
(5, 2, 9, 1200.00, 'Monthly Rent', '2024-02-01', '2025-10-06 14:00:21'),
(6, 3, 5, 100.00, 'Supermarket', '2024-02-03', '2025-10-06 14:00:21'),
(7, 3, 7, 50.00, 'Movie Tickets', '2024-02-08', '2025-10-06 14:00:21'),
(8, 3, 10, 75.00, 'Doctor Visit', '2024-02-15', '2025-10-06 14:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `income_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `user_id`, `category_id`, `amount`, `description`, `income_date`, `created_at`) VALUES
(1, 2, 1, 3000.00, 'Monthly Salary', '2024-01-15', '2025-10-06 14:00:21'),
(2, 2, 1, 3000.00, 'Monthly Salary', '2024-02-15', '2025-10-06 14:00:21'),
(3, 2, 2, 500.00, 'Freelance Project', '2024-02-20', '2025-10-06 14:00:21'),
(4, 3, 1, 4000.00, 'Monthly Salary', '2024-01-20', '2025-10-06 14:00:21'),
(5, 3, 3, 250.00, 'Stock Dividends', '2024-02-01', '2025-10-06 14:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 2, 'Welcome to Finance Manager! Start tracking your income and expenses.', 'info', 0, '2025-10-06 14:00:21'),
(2, 2, 'Budget alert: You have exceeded your Food budget for this month.', 'warning', 0, '2025-10-06 14:00:21'),
(3, 3, 'Your expense report for January is ready to view.', 'success', 0, '2025-10-06 14:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `is_active`) VALUES
(1, 'admin', 'admin@finance.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-10-06 14:00:20', 1),
(2, 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2025-10-06 14:00:21', 1),
(3, 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2025-10-06 14:00:21', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"summit_academy\",\"table\":\"applications\"},{\"db\":\"university_db\",\"table\":\"programs\"},{\"db\":\"university_db\",\"table\":\"users\"},{\"db\":\"university_db\",\"table\":\"applications\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-09-01 11:35:33', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `schooligniter`
--
CREATE DATABASE IF NOT EXISTS `schooligniter` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `schooligniter`;

-- --------------------------------------------------------

--
-- Table structure for table `acd_history`
--

CREATE TABLE `acd_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `examtype` longtext NOT NULL,
  `group` longtext NOT NULL,
  `board` longtext NOT NULL,
  `passing_yr` longtext NOT NULL,
  `special_mark` longtext NOT NULL,
  `ttl_mark` longtext NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acd_session`
--

CREATE TABLE `acd_session` (
  `id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `is_dt` longtext DEFAULT NULL,
  `is_open` int(1) NOT NULL,
  `strt_dt` date DEFAULT NULL,
  `end_dt` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `acd_session`
--

INSERT INTO `acd_session` (`id`, `name`, `is_dt`, `is_open`, `strt_dt`, `end_dt`) VALUES
(8, '2021-23', NULL, 1, '2021-09-16', '2023-01-18');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `level` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `level`) VALUES
(1, 'School Admin', 'admin@mail.com', 'Password@123', '1');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 undefined , 1 present , 2  absent',
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `status`, `student_id`, `date`) VALUES
(1, 1, 1, '2015-12-10'),
(2, 0, 1, '2015-12-11'),
(3, 0, 1, '2016-01-23'),
(4, 0, 1, '2016-01-30'),
(5, 0, 1, '2020-03-12'),
(6, 1, 1, '2022-03-12'),
(7, 1, 1, '2021-11-20'),
(8, 2, 3, '2022-03-12'),
(9, 2, 4, '2022-03-12'),
(10, 2, 5, '2022-03-12'),
(11, 2, 6, '2022-03-12'),
(12, 2, 7, '2022-03-12'),
(13, 2, 8, '2022-03-12'),
(14, 2, 9, '2022-03-12'),
(15, 1, 1, '2022-03-13'),
(16, 2, 2, '2022-03-13'),
(17, 1, 12, '2022-03-13'),
(18, 1, 10, '2022-03-13'),
(19, 1, 11, '2022-03-13'),
(20, 1, 14, '2022-03-13'),
(21, 1, 3, '2022-03-13'),
(22, 1, 4, '2022-03-13'),
(23, 1, 5, '2022-03-13'),
(24, 1, 6, '2022-03-13'),
(25, 2, 7, '2022-03-13'),
(26, 1, 8, '2022-03-13'),
(27, 1, 9, '2022-03-13'),
(28, 2, 13, '2022-03-13'),
(29, 0, 3, '2020-03-13'),
(30, 0, 4, '2020-03-13'),
(31, 0, 5, '2020-03-13'),
(32, 0, 6, '2020-03-13'),
(33, 0, 7, '2020-03-13'),
(34, 0, 8, '2020-03-13'),
(35, 0, 9, '2020-03-13'),
(36, 0, 13, '2020-03-13');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `description` longtext NOT NULL,
  `author` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `status` longtext NOT NULL,
  `price` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `name`, `description`, `author`, `class_id`, `status`, `price`) VALUES
(1, 'LBook One', 'Desc-0078', 'AuthorOne', '4', 'available', '11'),
(2, 'LBook Two', 'Desc-0089', 'AuthorTwo', '4', 'available', '13'),
(3, 'LBook Three', 'Desc-0118', 'AuthorThree', '5', 'available', '19');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('015a447db4fe344d43edf6564aae2d2593dd256a', '::1', 1647185186, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138343838363b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b666c6173685f6d6573736167657c733a31323a22446174612055706461746564223b5f5f63695f766172737c613a313a7b733a31333a22666c6173685f6d657373616765223b733a333a226f6c64223b7d),
('069e633497eee7c923270723f19db79465c067fa', '::1', 1647185342, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138353139303b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('151ab4a89340bbd00fb529eaa5d2bc389c9b0c10', '::1', 1647187510, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138373531303b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('175357b91aa5c3204100dd81a8bd6368264262de', '::1', 1647190226, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139303231383b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('23520caef99625591f10f7666b851d0ad54c3ef7', '::1', 1647192530, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139323531393b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('2c40f7068539d979e160afcdcdb00ff75d54fe14', '::1', 1647192169, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139323136393b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('38a5d9b737daa7c28de7d700ee1198f72485db04', '::1', 1647185870, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138353837303b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('4a90461d244f1154968e1b5bcc32e47ce151a99e', '::1', 1647186182, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138363138323b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('52905d700a8471a35d7895c0cd2f6b3d7308e6db', '::1', 1647188235, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138383136373b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('5cff6777d93669a43ac96d84da8890ac4a075c80', '::1', 1647185509, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138353530393b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('679d5e08bbaa49ddade499b2eeab617df29a97ac', '::1', 1647191205, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139303932393b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('718ba56c5d55fda016c965f2e9a2e9707aaab9bd', '::1', 1647187201, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138373230313b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('72e4408f94dc6f73b479366c690c2e63d81030c5', '::1', 1647186886, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138363838363b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('8eb4ab20034c4a321bc58e7f449544bac3ab5d87', '::1', 1647190212, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139303231323b),
('96f3b6c494acd5ae18899787b6ed15a4bacceb12', '::1', 1647193514, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139333233383b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('9e3709b39226d15cccb0fe48db1dcb9b7b553a37', '::1', 1647191875, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139313836343b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('b89e0ab65aec725ea8dc940e6e192763dfca6abc', '::1', 1647191294, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139313235363b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('cda5d9f2bfd783de391975c2db21753fcfa3f1c1', '::1', 1647190853, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139303536313b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('d0d5647e0b256d126adab84cb6d5ccdac26c885b', '::1', 1647188122, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138373833323b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('d52ebb202d44fa445af5213a663fe933264f4464', '::1', 1647191686, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139313536303b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('d8aeefa4c13b11b8109dfd92696c9b3f0461c1ac', '::1', 1647186567, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373138363536373b61646d696e5f6c6f67696e7c733a313a2231223b61646d696e5f69647c733a313a2231223b6c6f67696e5f757365725f69647c733a313a2231223b6e616d657c733a31323a225363686f6f6c2041646d696e223b6c6f67696e5f747970657c733a353a2261646d696e223b),
('e00b325a73b5296971ecd69beb21e6945a3af4d4', '::1', 1647194267, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139343236373b),
('e5048a7148c52caa5ab5600047a36668a660faab', '::1', 1647192926, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139323932363b),
('ec2e698310a2203b3880f86c1e008a67939ab6e2', '::1', 1647192921, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634373139323932313b);

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `name_numeric` longtext NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `name`, `name_numeric`, `teacher_id`) VALUES
(2, 'Class-9', '09', 2),
(3, 'Class-8', '08', 3),
(4, 'Class-7', '07', 3),
(5, 'Class 10', '10', 8);

-- --------------------------------------------------------

--
-- Table structure for table `class_routine`
--

CREATE TABLE `class_routine` (
  `class_routine_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `time_start` int(11) NOT NULL,
  `time_end` int(11) NOT NULL,
  `day` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `class_routine`
--

INSERT INTO `class_routine` (`class_routine_id`, `class_id`, `subject_id`, `time_start`, `time_end`, `day`) VALUES
(1, 2, 3, 10, 11, 'Sunday'),
(2, 2, 4, 11, 12, 'Sunday'),
(3, 2, 3, 10, 11, 'monday'),
(4, 5, 7, 10, 11, 'sunday'),
(5, 5, 8, 11, 24, 'sunday'),
(6, 5, 9, 13, 14, 'sunday'),
(7, 5, 10, 10, 11, 'monday');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `file_name` longtext NOT NULL,
  `file_type` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `timestamp` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dormitory`
--

CREATE TABLE `dormitory` (
  `dormitory_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `number_of_room` longtext NOT NULL,
  `description` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `dormitory`
--

INSERT INTO `dormitory` (`dormitory_id`, `name`, `number_of_room`, `description`) VALUES
(1, 'Boys Hostel B', '16', 'Hostel for Boys - B'),
(2, 'Boys Hostel A', '31', 'Hostel for Boys - A'),
(3, 'Girls Hostel A', '25', 'Hostel for Girls - A');

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `date` longtext NOT NULL,
  `comment` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`exam_id`, `name`, `date`, `comment`) VALUES
(1, 'First Terminal Examination', '11/18/2021', 'none'),
(2, 'Second Terminal Examination', '03/15/2022', 'none'),
(3, 'Third Terminal Examination', '07/30/2022', 'none'),
(4, 'Final Examination', '12/16/2022', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `expense_category`
--

CREATE TABLE `expense_category` (
  `expense_category_id` int(11) NOT NULL,
  `name` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `expense_category`
--

INSERT INTO `expense_category` (`expense_category_id`, `name`) VALUES
(1, 'Teacher Salary'),
(2, 'Classroom Equipments'),
(3, 'Classroom Decorations'),
(4, 'Inventory Purchase'),
(5, 'Exam Accessories');

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE `grade` (
  `grade_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `grade_point` longtext NOT NULL,
  `mark_from` int(11) NOT NULL,
  `mark_upto` int(11) NOT NULL,
  `comment` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`grade_id`, `name`, `grade_point`, `mark_from`, `mark_upto`, `comment`) VALUES
(1, 'A+', '5', 91, 100, 'Excellent'),
(2, 'A', '4', 81, 90, 'Very Good'),
(3, 'A-', '3', 71, 80, 'Good'),
(4, 'B', '2', 61, 70, 'Okay'),
(5, 'C', '1', 51, 60, 'Need Improvement'),
(6, 'F', '0', 0, 50, 'Fail');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `description` longtext NOT NULL,
  `amount` int(11) NOT NULL,
  `amount_paid` longtext NOT NULL,
  `due` longtext NOT NULL,
  `creation_timestamp` int(11) NOT NULL,
  `payment_timestamp` longtext NOT NULL,
  `payment_method` longtext NOT NULL,
  `payment_details` longtext NOT NULL,
  `status` longtext NOT NULL COMMENT 'paid or unpaid'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `student_id`, `title`, `description`, `amount`, `amount_paid`, `due`, `creation_timestamp`, `payment_timestamp`, `payment_method`, `payment_details`, `status`) VALUES
(1, 1, 'Monthly Fee', 'none', 850, '850', '0', 1449702000, '', '', '', 'paid'),
(2, 3, 'Monthly Payment', 'Payment for the month - Feb', 990, '0', '990', 1646089200, '', '', '', 'unpaid'),
(3, 10, 'Monthly Fees - Feb', 'Fees collection for the month of February', 770, '770', '0', 1646002800, '', '', '', 'paid'),
(4, 5, 'Monthly Fees', 'Fees collection for the month February', 990, '990', '0', 1646002800, '', '', '', 'paid'),
(5, 12, 'Monthly Fees - Feb', 'Fees Collection for the month February', 850, '0', '850', 1646002800, '', '', '', 'unpaid'),
(6, 9, 'Monthly Fees', 'none', 990, '990', '0', 1646002800, '', '', '', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `phrase_id` int(11) NOT NULL,
  `phrase` longtext NOT NULL,
  `english` longtext NOT NULL,
  `bengali` longtext NOT NULL,
  `spanish` longtext NOT NULL,
  `arabic` longtext NOT NULL,
  `dutch` longtext NOT NULL,
  `russian` longtext NOT NULL,
  `chinese` longtext NOT NULL,
  `turkish` longtext NOT NULL,
  `portuguese` longtext NOT NULL,
  `hungarian` longtext NOT NULL,
  `french` longtext NOT NULL,
  `greek` longtext NOT NULL,
  `german` longtext NOT NULL,
  `italian` longtext NOT NULL,
  `thai` longtext NOT NULL,
  `urdu` longtext NOT NULL,
  `hindi` longtext NOT NULL,
  `latin` longtext NOT NULL,
  `indonesian` longtext NOT NULL,
  `japanese` longtext NOT NULL,
  `korean` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`phrase_id`, `phrase`, `english`, `bengali`, `spanish`, `arabic`, `dutch`, `russian`, `chinese`, `turkish`, `portuguese`, `hungarian`, `french`, `greek`, `german`, `italian`, `thai`, `urdu`, `hindi`, `latin`, `indonesian`, `japanese`, `korean`) VALUES
(1, 'login', 'login', 'লগইন', 'login', 'دخول', 'login', 'Войти', '注册', 'giriş', 'login', 'bejelentkezés', 'Connexion', 'σύνδεση', 'Login', 'login', 'เข้าสู่ระบบ', 'لاگ ان', 'लॉगिन', 'login', 'login', 'ログイン', '로그인'),
(2, 'account_type', 'account type', 'অ্যাকাউন্ট টাইপ', 'tipo de cuenta', 'نوع الحساب', 'type account', 'тип счета', '账户类型', 'hesap türü', 'tipo de conta', 'fiók típusát', 'Type de compte', 'τον τύπο του λογαριασμού', 'Kontotyp', 'tipo di account', 'ประเภทบัญชี', 'اکاؤنٹ کی قسم', 'खाता प्रकार', 'propter speciem', 'Jenis akun', '口座の種類', '계정 유형'),
(3, 'admin', 'admin', 'অ্যাডমিন', 'administración', 'مشرف', 'admin', 'админ', '管理', 'yönetim', 'administrador', 'admin', 'administrateur', 'το admin', 'Admin', 'Admin', 'ผู้ดูแลระบบ', 'منتظم', 'प्रशासन', 'Lorem ipsum dolor sit', 'admin', '管理者', '관리자'),
(4, 'teacher', 'teacher', 'শিক্ষক', 'profesor', 'معلم', 'leraar', 'учитель', '老师', 'öğretmen', 'professor', 'tanár', 'professeur', 'δάσκαλος', 'Lehrer', 'insegnante', 'ครู', 'استاد', 'शिक्षक', 'Magister', 'guru', '教師', '선생'),
(5, 'student', 'student', 'ছাত্র', 'estudiante', 'طالب', 'student', 'студент', '学生', 'öğrenci', 'estudante', 'diák', 'étudiant', 'φοιτητής', 'Schüler', 'studente', 'นักเรียน', 'طالب علم', 'छात्र', 'discipulo', 'mahasiswa', '学生', '학생'),
(6, 'parent', 'parent', 'পিতা বা মাতা', 'padre', 'أصل', 'ouder', 'родитель', '亲', 'ebeveyn', 'parente', 'szülő', 'mère', 'μητρική εταιρεία', 'Elternteil', 'genitore', 'ผู้ปกครอง', 'والدین', 'माता - पिता', 'parente', 'induk', '親', '부모의'),
(7, 'email', 'email', 'ইমেইল', 'email', 'البريد الإلكتروني', 'e-mail', 'по электронной почте', '电子邮件', 'E-posta', 'e-mail', 'E-mail', 'email', 'e-mail', 'E-Mail-', 'e-mail', 'อีเมล์', 'ای میل', 'ईमेल', 'email', 'email', 'メール', '이메일'),
(8, 'password', 'password', 'পাসওয়ার্ড', 'contraseña', 'كلمة السر', 'wachtwoord', 'пароль', '密码', 'şifre', 'senha', 'jelszó', 'mot de passe', 'τον κωδικό', 'Passwort', 'password', 'รหัสผ่าน', 'پاس', 'पासवर्ड', 'Signum', 'kata sandi', 'パスワード', '암호'),
(9, 'forgot_password ?', 'forgot password ?', 'পাসওয়ার্ড ভুলে গেছেন?', '¿Olvidó su contraseña?', 'نسيت كلمة المرور؟', 'wachtwoord vergeten?', 'забыли пароль?', '忘记密码？', 'Şifremi unuttum?', 'Esqueceu a senha?', 'Elfelejtett jelszó?', 'Mot de passe oublié?', 'Ξεχάσατε τον κωδικό;', 'Passwort vergessen?', 'dimenticato la password?', 'ลืมรหัสผ่าน', 'پاس ورڈ بھول گیا؟', 'क्या संभावनाएं हैं?', 'oblitus esne verbi?', 'lupa password?', 'パスワードを忘れた？', '비밀번호를 잊으 셨나요?'),
(10, 'reset_password', 'reset password', 'পাসওয়ার্ড রিসেট', 'restablecer la contraseña', 'إعادة تعيين كلمة المرور', 'reset wachtwoord', 'сбросить пароль', '重设密码', 'şifrenizi sıfırlamak', 'redefinir a senha', 'Jelszó visszaállítása', 'réinitialiser le mot de passe', 'επαναφέρετε τον κωδικό πρόσβασης', 'Kennwort zurücksetzen', 'reimpostare la password', 'ตั้งค่ารหัสผ่าน', 'پاس ورڈ ری سیٹ', 'पासवर्ड रीसेट', 'Duis adipiscing', 'reset password', 'パスワードを再設定する', '암호를 재설정'),
(11, 'reset', 'reset', 'রিসেট করুন', 'reajustar', 'إعادة تعيين', 'reset', 'сброс', '重置', 'ayarlamak', 'restabelecer', 'vissza', 'remettre', 'επαναφορά', 'rücksetzen', 'reset', 'ตั้งใหม่', 'ری سیٹ', 'रीसेट करें', 'Duis', 'ulang', 'リセット', '재설정'),
(12, 'admin_dashboard', 'admin dashboard', 'অ্যাডমিন ড্যাশবোর্ড', 'administrador salpicadero', 'المشرف وحة القيادة', 'admin dashboard', 'админ панель', '管理面板', 'Admin paneli', 'Admin Dashboard', 'admin műszerfal', 'administrateur tableau de bord', 'πίνακα ελέγχου του διαχειριστή', 'Admin-Dashboard', 'Admin Dashboard', 'แผงควบคุมของผู้ดูแลระบบ', 'ایڈمن ڈیش بورڈ', 'व्यवस्थापक डैशबोर्ड', 'Lorem ipsum dolor sit Dashboard', 'admin dashboard', '管理ダッシュボード', '관리자 대시 보드'),
(13, 'account', 'account', 'হিসাব', 'cuenta', 'حساب', 'rekening', 'счет', '帐户', 'hesap', 'conta', 'számla', 'compte', 'λογαριασμός', 'Konto', 'conto', 'บัญชี', 'اکاؤنٹ', 'खाता', 'propter', 'rekening', 'アカウント', '계정'),
(14, 'profile', 'profile', 'পরিলেখ', 'perfil', 'ملف', 'profiel', 'профиль', '轮廓', 'profil', 'perfil', 'profil', 'profil', 'προφίλ', 'Profil', 'profilo', 'โปรไฟล์', 'پروفائل', 'रूपरेखा', 'profile', 'profil', 'プロフィール', '프로필'),
(15, 'change_password', 'change password', 'পাসওয়ার্ড পরিবর্তন', 'cambiar la contraseña', 'تغيير كلمة المرور', 'wachtwoord wijzigen', 'изменить пароль', '更改密码', 'şifresini değiştirmek', 'alterar a senha', 'jelszó megváltoztatása', 'changer le mot de passe', 'αλλάξετε τον κωδικό πρόσβασης', 'Kennwort ändern', 'cambiare la password', 'เปลี่ยนรหัสผ่าน', 'پاس ورڈ تبدیل', 'पासवर्ड परिवर्तित', 'mutare password', 'mengubah password', 'パスワードを変更する', '암호를 변경'),
(16, 'logout', 'logout', 'লগ আউট', 'logout', 'تسجيل الخروج', 'logout', 'выход', '注销', 'logout', 'Sair', 'logout', 'Déconnexion', 'αποσύνδεση', 'logout', 'Esci', 'ออกจากระบบ', 'لاگ آؤٹ کریں', 'लॉगआउट', 'logout', 'logout', 'ログアウト', '로그 아웃'),
(17, 'panel', 'panel', 'প্যানেল', 'panel', 'لوحة', 'paneel', 'панель', '面板', 'panel', 'painel', 'bizottság', 'panneau', 'πίνακας', 'Platte', 'pannello', 'แผงหน้าปัด', 'پینل', 'पैनल', 'panel', 'panel', 'パネル', '패널'),
(18, 'dashboard_help', 'dashboard help', 'ড্যাশবোর্ড সহায়তা', 'salpicadero ayuda', 'لوحة القيادة مساعدة', 'dashboard hulp', 'Приборная панель помощь', '仪表板帮助', 'pano yardım', 'dashboard ajuda', 'műszerfal help', 'tableau de bord aide', 'ταμπλό βοήθεια', 'Dashboard-Hilfe', 'dashboard aiuto', 'แผงควบคุมความช่วยเหลือ', 'ڈیش بورڈ مدد', 'डैशबोर्ड मदद', 'Dashboard auxilium', 'dashboard bantuan', 'ダッシュボードヘルプ', '대시 보드 도움말'),
(19, 'dashboard', 'dashboard', 'ড্যাশবোর্ড', 'salpicadero', 'لوحة القيادة', 'dashboard', 'приборная панель', '仪表盘', 'gösterge paneli', 'painel de instrumentos', 'műszerfal', 'tableau de bord', 'ταμπλό', 'Armaturenbrett', 'cruscotto', 'หน้าปัด', 'ڈیش بورڈ', 'डैशबोर्ड', 'Dashboard', 'dasbor', 'ダッシュボード', '계기판'),
(20, 'student_help', 'student help', 'শিক্ষার্থীর সাহায্য', 'ayuda estudiantil', 'مساعدة الطالب', 'student hulp', 'студент помощь', '学生的帮助', 'Öğrenci yardım', 'ajuda estudante', 'diák segítségével', 'aide aux étudiants', 'φοιτητής βοήθεια', 'Schüler-Hilfe', 'help studente', 'ช่วยเหลือนักเรียน', 'طالب علم کی مدد', 'छात्र मदद', 'Discipulus auxilium', 'membantu siswa', '学生のヘルプ', '학생 도움말'),
(21, 'teacher_help', 'teacher help', 'শিক্ষক সাহায্য', 'ayuda del maestro', 'مساعدة المعلم', 'leraar hulp', 'Учитель помощь', '老师的帮助', 'öğretmen yardım', 'ajuda de professores', 'tanár segítségével', 'aide de l\'enseignant', 'βοήθεια των εκπαιδευτικών', 'Lehrer-Hilfe', 'aiuto dell\'insegnante', 'ครูช่วยเหลือ', 'استاد کی مدد', 'शिक्षक मदद', 'doctor auxilium', 'bantuan guru', '教師のヘルプ', '교사의 도움'),
(22, 'subject_help', 'subject help', 'বিষয় সাহায্য', 'ayuda sujeto', 'مساعدة الموضوع', 'Onderwerp hulp', 'Заголовок помощь', '主题帮助', 'konusu yardım', 'ajuda assunto', 'tárgy segítségével', 'l\'objet de l\'aide', 'υπόκεινται βοήθεια', 'Thema Hilfe', 'Aiuto Subject', 'ความช่วยเหลือเรื่อง', 'موضوع مدد', 'विषय मदद', 'agitur salus', 'bantuan subjek', '件名ヘルプ', '주제 도움'),
(23, 'subject', 'subject', 'বিষয়', 'sujeto', 'موضوع', 'onderwerp', 'тема', '主题', 'konu', 'assunto', 'tárgy', 'sujet', 'θέμα', 'Thema', 'soggetto', 'เรื่อง', 'موضوع', 'विषय', 'agitur', 'subyek', 'テーマ', '제목'),
(24, 'class_help', 'class help', 'বর্গ সাহায্য', 'clase de ayuda', 'الطبقة مساعدة', 'klasse hulp', 'Класс помощь', '类的帮助', 'sınıf yardım', 'classe ajuda', 'osztály segítségével', 'aide de la classe', 'Κατηγορία βοήθεια', 'Klasse Hilfe', 'help classe', 'ความช่วยเหลือในชั้นเรียน', 'کلاس مدد', 'कक्षा मदद', 'genus auxilii', 'kelas bantuan', 'クラスのヘルプ', '클래스 도움'),
(25, 'class', 'class', 'বর্গ', 'clase', 'فئة', 'klasse', 'класс', '类', 'sınıf', 'classe', 'osztály', 'classe', 'κατηγορία', 'Klasse', 'classe', 'ชั้น', 'کلاس', 'वर्ग', 'class', 'kelas', 'クラス', '클래스'),
(26, 'exam_help', 'exam help', 'পরীক্ষায় সাহায্য', 'ayuda examen', 'امتحان مساعدة', 'examen hulp', 'Экзамен помощь', '考试帮助', 'sınav yardım', 'exame ajuda', 'vizsga help', 'aide à l\'examen', 'εξετάσεις βοήθεια', 'Prüfung Hilfe', 'esame di guida', 'การสอบความช่วยเหลือ', 'امتحان مدد', 'परीक्षा मदद', 'ipsum Auxilium', 'ujian bantuan', '試験ヘルプ', '시험에 도움'),
(27, 'exam', 'exam', 'পরীক্ষা', 'examen', 'امتحان', 'tentamen', 'экзамен', '考试', 'sınav', 'exame', 'vizsgálat', 'exam', 'εξέταση', 'Prüfung', 'esame', 'การสอบ', 'امتحان', 'परीक्षा', 'Lorem ipsum', 'ujian', '試験', '시험'),
(28, 'marks_help', 'marks help', 'চিহ্ন সাহায্য', 'marcas ayudan', 'علامات مساعدة', 'markeringen helpen', 'метки помогают', '标记帮助', 'işaretleri yardım', 'marcas ajudar', 'jelek segítenek', 'marques aident', 'σήματα βοηθήσει', 'Markierungen helfen', 'segni aiutano', 'เครื่องหมายช่วย', 'نمبر مدد', 'निशान मदद', 'notas auxilio', 'tanda membantu', 'マークのヘルプ', '마크는 데 도움이'),
(29, 'marks-attendance', 'marks-attendance', 'চিহ্ন-উপস্থিতির', 'marcas-asistencia', 'علامات-الحضور', 'merken-deelname', 'знаки-посещаемости', '标记缺席', 'işaretleri-katılım', 'marcas de comparecimento', 'jelek-ellátás', 'marques-participation', 'σήματα προσέλευση', 'Marken-Teilnahme', 'marchi-presenze', 'เครื่องหมายการเข้าร่วม', 'نمبر حاضری', 'निशान उपस्थिति', 'signa eius ministrabant,', 'tanda-pertemuan', 'マーク·出席', '마크 출석'),
(30, 'grade_help', 'grade help', 'গ্রেড সাহায্য', 'ayuda de grado', 'مساعدة الصف', 'leerjaar hulp', 'оценка помощь', '级帮助', 'sınıf yardım', 'ajuda grau', 'fokozat help', 'aide de qualité', 'βαθμού βοήθεια', 'Grade-Hilfe', 'aiuto grade', 'ช่วยเหลือเกรด', 'گریڈ مدد', 'ग्रेड मदद', 'gradus ope', 'kelas bantuan', 'グレードのヘルプ', '급 도움'),
(31, 'exam-grade', 'exam-grade', 'পরীক্ষার শ্রেণী', 'examen de grado', 'امتحان الصف', 'examen-grade', 'экзамен класса', '考试级别', 'sınav notu', 'exame de grau', 'vizsga-grade', 'examen de qualité', 'εξετάσεις ποιότητας', 'Prüfung-Grade', 'esami-grade', 'สอบเกรด', 'امتحان گریڈ', 'परीक्षा ग्रेड', 'ipsum turpis,', 'ujian-grade', '試験グレード', '시험 등급'),
(32, 'class_routine_help', 'class routine help', 'ক্লাসের রুটিন সাহায্য', 'clase ayuda rutina', 'الطبقة مساعدة روتينية', 'klasroutine hulp', 'класс рутина помощь', '类常规帮助', 'sınıf rutin yardım', 'classe ajuda rotina', 'osztály rutin segít', 'classe aide routine', 'κατηγορία ρουτίνας βοήθεια', 'Klasse Routine Hilfe', 'Classe aiuto di routine', 'ระดับความช่วยเหลือตามปกติ', 'کلاس معمول مدد', 'वर्ग दिनचर्या मदद', 'uno genere auxilium', 'kelas bantuan rutin', 'クラスルーチンのヘルプ', '클래스 루틴 도움'),
(33, 'class_routine', 'class routine', 'ক্লাসের রুটিন', 'rutina de la clase', 'فئة الروتينية', 'klasroutine', 'класс подпрограмм', '常规类', 'sınıf rutin', 'rotina classe', 'osztály rutin', 'routine de classe', 'Κατηγορία ρουτίνα', 'Klasse Routine', 'classe di routine', 'ประจำชั้น', 'کلاس معمول', 'वर्ग दिनचर्या', 'in genere uno,', 'rutin kelas', 'クラス·ルーチン', '클래스 루틴'),
(34, 'invoice_help', 'invoice help', 'চালান সাহায্য', 'ayuda factura', 'مساعدة الفاتورة', 'factuur hulp', 'счет-фактура помощь', '发票帮助', 'fatura yardım', 'ajuda factura', 'számla segítségével', 'aide facture', 'τιμολόγιο βοήθεια', 'Rechnungs Hilfe', 'help fattura', 'ช่วยเหลือใบแจ้งหนี้', 'انوائس مدد', 'चालान सहायता', 'auxilium cautionem', 'bantuan faktur', '送り状ヘルプ', '송장 도움'),
(35, 'payment', 'payment', 'প্রদান', 'pago', 'دفع', 'betaling', 'оплата', '付款', 'ödeme', 'pagamento', 'fizetés', 'paiement', 'πληρωμή', 'Zahlung', 'pagamento', 'การชำระเงิน', 'ادائیگی', 'भुगतान', 'pecunia', 'pembayaran', '支払い', '지불'),
(36, 'book_help', 'book help', 'বইয়ের সাহায্য', 'libro de ayuda', 'كتاب المساعدة', 'boek hulp', 'Книга помощь', '本书帮助', 'kitap yardımı', 'livro ajuda', 'könyv segít', 'livre aide', 'βοήθεια του βιβλίου', 'Buch-Hilfe', 'della guida', 'ช่วยเหลือหนังสือ', 'کتاب مدد', 'पुस्तक मदद', 'auxilium libro,', 'Buku bantuan', 'ブックのヘルプ', '책 도움말'),
(37, 'library', 'library', 'লাইব্রেরি', 'biblioteca', 'مكتبة', 'bibliotheek', 'библиотека', '文库', 'kütüphane', 'biblioteca', 'könyvtár', 'bibliothèque', 'βιβλιοθήκη', 'Bibliothek', 'biblioteca', 'ห้องสมุด', 'لائبریری', 'पुस्तकालय', 'library', 'perpustakaan', '図書館', '도서관'),
(38, 'transport_help', 'transport help', 'যানবাহনের সাহায্য', 'ayuda de transporte', 'مساعدة النقل', 'vervoer help', 'транспорт помощь', '运输帮助', 'ulaşım yardım', 'ajuda de transporte', 'szállítás Súgó', 'le transport de l\'aide', 'βοηθούν τη μεταφορά', 'Transport Hilfe', 'help trasporti', 'ช่วยเหลือการขนส่ง', 'نقل و حمل مدد', 'परिवहन मदद', 'auxilium onerariis', 'transportasi bantuan', '輸送のヘルプ', '전송 도움'),
(39, 'transport', 'transport', 'পরিবহন', 'transporte', 'نقل', 'vervoer', 'транспорт', '运输', 'taşıma', 'transporte', 'szállítás', 'transport', 'μεταφορά', 'Transport', 'trasporto', 'การขนส่ง', 'نقل و حمل', 'परिवहन', 'onerariis', 'angkutan', '輸送', '수송'),
(40, 'dormitory_help', 'dormitory help', 'আস্তানা সাহায্য', 'dormitorio de ayuda', 'عنبر المساعدة', 'slaapzaal hulp', 'общежитие помощь', '宿舍帮助', 'yatakhane yardım', 'dormitório ajuda', 'kollégiumi help', 'dortoir aide', 'κοιτώνα βοήθεια', 'Wohnheim Hilfe', 'dormitorio aiuto', 'หอพักช่วยเหลือ', 'شیناگار مدد', 'छात्रावास मदद', 'dormitorium auxilium', 'asrama bantuan', '寮のヘルプ', '기숙사 도움말'),
(41, 'dormitory', 'dormitory', 'শ্রমিক - আস্তানা', 'dormitorio', 'المهجع', 'slaapzaal', 'спальня', '宿舍', 'yatakhane', 'dormitório', 'hálóterem', 'dortoir', 'κοιτώνα', 'Wohnheim', 'dormitorio', 'หอพัก', 'شیناگار', 'छात्रावास', 'dormitorium', 'asrama mahasiswa', '寮', '기숙사'),
(42, 'noticeboard_help', 'noticeboard help', 'নোটিশবোর্ড সাহায্য', 'tablón de anuncios de la ayuda', 'اللافتة مساعدة', 'prikbord hulp', 'доска для объявлений помощь', '布告帮助', 'noticeboard yardım', 'avisos ajuda', 'üzenőfalán help', 'panneau d\'aide', 'ανακοινώσεων βοήθεια', 'Brett-Hilfe', 'bacheca aiuto', 'ป้ายประกาศความช่วยเหลือ', 'noticeboard مدد', 'Noticeboard मदद', 'auxilium noticeboard', 'pengumuman bantuan', '伝言板のヘルプ', '의 noticeboard 도움말'),
(43, 'noticeboard-event', 'noticeboard-event', 'নোটিশবোর্ড-ইভেন্ট', 'tablón de anuncios de eventos', 'اللافتة الحدث', 'prikbord-event', 'доска для объявлений-событие', '布告牌事件', 'noticeboard olay', 'avisos de eventos', 'üzenőfalán esemény', 'panneau d\'événement', 'ανακοινώσεων εκδήλωση', 'Brett-Ereignis', 'bacheca-evento', 'ป้ายประกาศของเหตุการณ์', 'noticeboard ایونٹ', 'Noticeboard घटना', 'noticeboard eventus,', 'pengumuman-acara', '伝言板イベント', '의 noticeboard 이벤트'),
(44, 'bed_ward_help', 'bed ward help', 'বিছানা ওয়ার্ড সাহায্য', 'cama ward ayuda', 'جناح سرير المساعدة', 'bed ward hulp', 'кровать подопечный помощь', '床病房的帮助', 'yatak koğuş yardım', 'ajuda cama enfermaria', 'ágy Ward help', 'lit salle de l\'aide', 'κρεβάτι πτέρυγα βοήθεια', 'Betten-Station Hilfe', 'Letto reparto aiuto', 'วอร์ดเตียงช่วยเหลือ', 'بستر وارڈ مدد', 'बिस्तर वार्ड मदद', 'lectum stans auxilium', 'tidur bangsal bantuan', 'ベッド病棟のヘルプ', '침대 병동 도움'),
(45, 'settings', 'settings', 'সেটিংস', 'configuración', 'إعدادات', 'instellingen', 'настройки', '设置', 'ayarları', 'definições', 'beállítások', 'paramètres', 'Ρυθμίσεις', 'Einstellungen', 'Impostazioni', 'การตั้งค่า', 'ترتیبات', 'सेटिंग्स', 'occasus', 'Pengaturan', '設定', '설정'),
(46, 'system_settings', 'system settings', 'সিস্টেম সেটিংস', 'configuración del sistema', 'إعدادات النظام', 'systeeminstellingen', 'настройки системы', '系统设置', 'sistem ayarları', 'configurações do sistema', 'rendszerbeállításokat', 'les paramètres du système', 'ρυθμίσεις του συστήματος', 'Systemeinstellungen', 'impostazioni di sistema', 'การตั้งค่าระบบ', 'نظام کی ترتیبات', 'प्रणाली सेटिंग्स', 'ratio occasus', 'pengaturan sistem', 'システム設定', '시스템 설정'),
(47, 'manage_language', 'manage language', 'ভাষা ও পরিচালনা', 'gestionar idioma', 'إدارة اللغة', 'beheren taal', 'управлять язык', '管理语言', 'dil yönetmek', 'gerenciar língua', 'kezelni nyelv', 'gérer langue', 'διαχείριση γλώσσα', 'verwalten Sprache', 'gestire lingua', 'จัดการภาษา', 'زبان کا انتظام', 'भाषा का प्रबंधन', 'moderari linguam,', 'mengelola bahasa', '言語を管理', '언어를 관리'),
(48, 'backup_restore', 'backup restore', 'ব্যাকআপ পুনঃস্থাপন', 'copia de seguridad a restaurar', 'استعادة النسخ الاحتياطي', 'backup terugzetten', 'восстановить резервного копирования', '备份还原', 'yedekleme geri', 'de backup restaurar', 'Backup Restore', 'restauration de sauvegarde', 'επαναφοράς αντιγράφων ασφαλείας', 'Backup wiederherstellen', 'ripristino di backup', 'การสำรองข้อมูลเรียกคืน', 'بیک اپ بحال', 'बैकअप बहाल', 'tergum restituunt', 'backup restore', 'バックアップは、リストア', '백업 복원'),
(49, 'profile_help', 'profile help', 'সাহায্য প্রোফাইল', 'Perfil Ayuda', 'ملف المساعدة', 'profile hulp', 'анкета помощь', '简介帮助', 'yardım profile', 'Perfil ajuda', 'profile help', 'profil aide', 'προφίλ βοήθεια', 'Profil Hilfe', 'profilo di aiuto', 'โปรไฟล์ความช่วยเหลือ', 'مدد پروفائل', 'प्रोफाइल में', 'Auctor nullam opem', 'Profil bantuan', 'プロフィールヘルプ', '도움 프로필'),
(50, 'manage_student', 'manage student', 'শিক্ষার্থী ও পরিচালনা', 'gestionar estudiante', 'إدارة الطلبة', 'beheren student', 'управлять студента', '管理学生', 'öğrenci yönetmek', 'gerenciar estudante', 'kezelni diák', 'gérer étudiant', 'διαχείριση των φοιτητών', 'Schüler verwalten', 'gestire studente', 'การจัดการศึกษา', 'طالب علم کا انتظام', 'छात्र का प्रबंधन', 'curo alumnorum', 'mengelola siswa', '生徒を管理', '학생 관리'),
(51, 'manage_teacher', 'manage teacher', 'শিক্ষক ও পরিচালনা', 'gestionar maestro', 'إدارة المعلم', 'beheren leraar', 'управлять учителя', '管理老师', 'öğretmen yönetmek', 'gerenciar professor', 'kezelni tanár', 'gérer enseignant', 'διαχείριση των εκπαιδευτικών', 'Lehrer verwalten', 'gestire insegnante', 'จัดการครู', 'ٹیچر کا انتظام', 'शिक्षक का प्रबंधन', 'magister curo', 'mengelola guru', '教師を管理', '교사 관리'),
(52, 'noticeboard', 'noticeboard', 'নোটিশবোর্ড', 'tablón de anuncios', 'اللافتة', 'prikbord', 'доска для объявлений', '布告', 'noticeboard', 'quadro de avisos', 'üzenőfalán', 'panneau d\'affichage', 'ανακοινώσεων', 'Brett', 'bacheca', 'ป้ายประกาศ', 'noticeboard', 'Noticeboard', 'noticeboard', 'pengumuman', '伝言板', '의 noticeboard'),
(53, 'language', 'language', 'ভাষা', 'idioma', 'لغة', 'taal', 'язык', '语', 'dil', 'língua', 'nyelv', 'langue', 'γλώσσα', 'Sprache', 'lingua', 'ภาษา', 'زبان', 'भाषा', 'Lingua', 'bahasa', '言語', '언어'),
(54, 'backup', 'backup', 'ব্যাকআপ', 'reserva', 'دعم', 'reservekopie', 'резервный', '备用', 'yedek', 'backup', 'mentés', 'sauvegarde', 'εφεδρικός', 'Sicherungskopie', 'di riserva', 'การสำรองข้อมูล', 'بیک اپ', 'बैकअप', 'tergum', 'backup', 'バックアップ', '지원'),
(55, 'calendar_schedule', 'calendar schedule', 'ক্যালেন্ডার সময়সূচী', 'horario de calendario', 'الجدول الزمني', 'kalender schema', 'Календарь Расписание', '日历日程', 'takvim programı', 'agenda calendário', 'naptári ütemezés', 'calendrier calendrier', 'χρονοδιαγράμματος του ημερολογίου', 'Kalender Zeitplan', 'programma di calendario', 'ปฏิทินตารางนัดหมาย', 'کیلنڈر شیڈول', 'कैलेंडर अनुसूची', 'kalendarium ipsum', 'jadwal kalender', 'カレンダーのスケジュール', '캘린더 일정'),
(56, 'select_a_class', 'select a class', 'একটি শ্রেণী নির্বাচন', 'seleccionar una clase', 'حدد فئة', 'selecteer een class', 'выберите класс', '选择一个类', 'bir sınıf seçin', 'selecionar uma classe', 'válasszon ki egy osztályt', 'sélectionner une classe', 'επιλέξτε μια κατηγορία', 'Wählen Sie eine Klasse', 'selezionare una classe', 'เลือกชั้น', 'ایک کلاس منتخب کریں', 'एक वर्ग का चयन करें', 'eligere genus', 'pilih kelas', 'クラスを選択', '클래스를 선택'),
(57, 'student_list', 'student list', 'শিক্ষার্থীর তালিকা', 'lista de alumnos', 'قائمة الطلاب', 'student lijst', 'Список студент', '学生名单', 'öğrenci listesi', 'lista de alunos', 'diák lista', 'liste des étudiants', 'κατάλογο των φοιτητών', 'Schülerliste', 'elenco degli studenti', 'รายชื่อนักเรียน', 'طالب علم کی فہرست', 'छात्र सूची', 'Discipulus album', 'daftar mahasiswa', '学生のリスト', '학생 목록'),
(58, 'add_student', 'add student', 'ছাত্র যোগ', 'añadir estudiante', 'إضافة طالب', 'voeg student', 'добавить студента', '新增学生', 'öğrenci eklemek', 'adicionar estudante', 'hozzá hallgató', 'ajouter étudiant', 'προσθέστε φοιτητής', 'Student hinzufügen', 'aggiungere studente', 'เพิ่มนักเรียน', 'طالب علم شامل', 'छात्र जोड़', 'adde elit', 'menambahkan mahasiswa', '学生を追加', '학생을 추가'),
(59, 'roll', 'roll', 'রোল', 'rollo', 'لفة', 'broodje', 'рулон', '滚', 'rulo', 'rolo', 'tekercs', 'rouleau', 'ρολό', 'Rolle', 'rotolo', 'ม้วน', 'رول', 'रोल', 'volumen', 'gulungan', 'ロール', '롤'),
(60, 'photo', 'photo', 'ছবি', 'foto', 'صور', 'foto', 'фото', '照片', 'fotoğraf', 'foto', 'fénykép', 'photo', 'φωτογραφία', 'Foto', 'foto', 'ภาพถ่าย', 'تصویر', 'फ़ोटो', 'Lorem ipsum', 'foto', '写真', '사진'),
(61, 'student_name', 'student name', 'শিক্ষার্থীর নাম', 'Nombre del estudiante', 'اسم الطالب', 'naam van de leerling', 'Имя студента', '学生姓名', 'Öğrenci adı', 'nome do aluno', 'tanuló nevét', 'nom de l\'étudiant', 'το όνομα του μαθητή', 'Studentennamen', 'nome dello studente', 'ชื่อนักเรียน', 'طالب علم کے نام', 'छात्र का नाम', 'ipsum est nomen', 'nama siswa', '学生の名前', '학생의 이름'),
(62, 'address', 'address', 'ঠিকানা', 'dirección', 'عنوان', 'adres', 'адрес', '地址', 'adres', 'endereço', 'cím', 'adresse', 'διεύθυνση', 'Adresse', 'indirizzo', 'ที่อยู่', 'ایڈریس', 'पता', 'Oratio', 'alamat', 'アドレス', '주소'),
(63, 'options', 'options', 'অপশন', 'Opciones', 'خيارات', 'opties', 'опции', '选项', 'seçenekleri', 'opções', 'lehetőségek', 'les options', 'Επιλογές', 'Optionen', 'Opzioni', 'ตัวเลือก', 'اختیارات', 'विकल्प', 'options', 'Pilihan', 'オプション', '옵션'),
(64, 'marksheet', 'marksheet', 'marksheet', 'marksheet', 'marksheet', 'Marksheet', 'marksheet', 'marksheet', 'Marksheet', 'marksheet', 'Marksheet', 'relevé de notes', 'Marksheet', 'marksheet', 'Marksheet', 'marksheet', 'marksheet', 'अंकपत्र', 'marksheet', 'marksheet', 'marksheet', 'marksheet'),
(65, 'id_card', 'id card', 'আইডি কার্ড', 'carnet de identidad', 'بطاقة الهوية', 'id-kaart', 'удостоверение личности', '身份证', 'kimlik kartı', 'carteira de identidade', 'személyi igazolvány', 'carte d\'identité', 'id κάρτα', 'Ausweis', 'carta d\'identità', 'บัตรประชาชน', 'شناختی کارڈ', 'औ डी कार्ड', 'id ipsum', 'id card', 'IDカード', '신분증'),
(66, 'edit', 'edit', 'সম্পাদন করা', 'editar', 'تحرير', 'uitgeven', 'редактировать', '编辑', 'düzenleme', 'editar', 'szerkeszt', 'modifier', 'edit', 'bearbeiten', 'modifica', 'แก้ไข', 'میں ترمیم کریں', 'संपादित करें', 'edit', 'mengedit', '編集', '편집'),
(67, 'delete', 'delete', 'মুছে ফেলা', 'borrar', 'حذف', 'verwijderen', 'удалять', '删除', 'silmek', 'excluir', 'töröl', 'effacer', 'διαγραφή', 'löschen', 'cancellare', 'ลบ', 'خارج', 'हटाना', 'vel deleri,', 'menghapus', '削除する', '삭제'),
(68, 'personal_profile', 'personal profile', 'ব্যক্তিগত প্রোফাইল', 'perfil personal', 'ملف شخصي', 'persoonlijk profiel', 'личный профиль', '个人简介', 'kişisel profil', 'perfil pessoal', 'személyes profil', 'profil personnel', 'προσωπικό προφίλ', 'persönliches Profil', 'profilo personale', 'รายละเอียดข้อมูลส่วนตัว', 'ذاتی پروفائل', 'व्यक्तिगत प्रोफाइल', 'personal profile', 'profil pribadi', '人物点描', '개인 프로필'),
(69, 'academic_result', 'academic result', 'একাডেমিক ফলাফল', 'resultado académico', 'نتيجة الأكاديمية', 'academische resultaat', 'академический результат', '学术成果', 'akademik sonuç', 'resultado acadêmico', 'tudományos eredmény', 'résultat académique', 'ακαδημαϊκή αποτέλεσμα', 'Studienergebnis', 'risultato accademico', 'ผลการศึกษา', 'تعلیمی نتیجہ', 'शैक्षिक परिणाम', 'Ex academicis', 'Hasil akademik', '学術結果', '학습 결​​과'),
(70, 'name', 'name', 'নাম', 'nombre', 'اسم', 'naam', 'название', '名称', 'isim', 'nome', 'név', 'nom', 'όνομα', 'Name', 'nome', 'ชื่อ', 'نام', 'नाम', 'nomen,', 'nama', '名前', '이름'),
(71, 'birthday', 'birthday', 'জন্মদিন', 'cumpleaños', 'عيد ميلاد', 'verjaardag', 'день рождения', '生日', 'doğum günü', 'aniversário', 'születésnap', 'anniversaire', 'γενέθλια', 'Geburtstag', 'compleanno', 'วันเกิด', 'سالگرہ', 'जन्मदिन', 'natalis', 'ulang tahun', '誕生日', '생일'),
(72, 'sex', 'sex', 'লিঙ্গ', 'sexo', 'جنس', 'seks', 'секс', '性别', 'seks', 'sexo', 'szex', 'sexe', 'φύλο', 'Sex', 'sesso', 'เพศ', 'جنسی', 'लिंग', 'sex', 'seks', 'セックス', '섹스'),
(73, 'male', 'male', 'পুরুষ', 'masculino', 'ذكر', 'mannelijk', 'мужской', '男性', 'erkek', 'masculino', 'férfi', 'mâle', 'αρσενικός', 'männlich', 'maschio', 'เพศชาย', 'پروفائل', 'नर', 'masculus', 'laki-laki', '男性', '남성'),
(74, 'female', 'female', 'মহিলা', 'femenino', 'أنثى', 'vrouw', 'женский', '女', 'kadın', 'feminino', 'női', 'femelle', 'θηλυκός', 'weiblich', 'femminile', 'เพศหญิง', 'خواتین', 'महिला', 'femina,', 'perempuan', '女性', '여성'),
(75, 'religion', 'religion', 'ধর্ম', 'religión', 'دين', 'religie', 'религия', '宗教', 'din', 'religião', 'vallás', 'religion', 'θρησκεία', 'Religion', 'religione', 'ศาสนา', 'مذہب', 'धर्म', 'religionis,', 'agama', '宗教', '종교'),
(76, 'blood_group', 'blood group', 'রক্তের বিভাগ', 'grupo sanguíneo', 'فصيلة الدم', 'bloedgroep', 'группа крови', '血型', 'kan grubu', 'grupo sanguíneo', 'vércsoport', 'groupe sanguin', 'ομάδα αίματος', 'Blutgruppe', 'gruppo sanguigno', 'กรุ๊ปเลือด', 'خون کے گروپ', 'रक्त वर्ग', 'sanguine coetus', 'golongan darah', '血液型', '혈액형'),
(77, 'phone', 'phone', 'ফোন', 'teléfono', 'هاتف', 'telefoon', 'телефон', '电话', 'telefon', 'telefone', 'telefon', 'téléphone', 'τηλέφωνο', 'Telefon', 'telefono', 'โทรศัพท์', 'فون', 'फ़ोन', 'Praesent', 'telepon', '電話', '전화'),
(78, 'father_name', 'father name', 'পিতার নাম', 'Nombre del padre', 'اسم الأب', 'naam van de vader', 'отчество', '父亲姓名', 'baba adı', 'nome pai', 'apa név', 'nom de père', 'Το όνομα του πατέρα', 'Der Name des Vaters', 'nome del padre', 'ชื่อพ่อ', 'والد کا نام', 'पिता का नाम', 'nomine Patris,', 'Nama ayah', '父親の名前', '아버지의 이름'),
(79, 'mother_name', 'mother name', 'মায়ের নাম', 'Nombre de la madre', 'اسم الأم', 'moeder naam', 'Имя матери', '母亲的名字', 'anne adı', 'Nome mãe', 'anyja név', 'nom de la mère', 'το όνομα της μητέρας', 'Name der Mutter', 'Nome madre', 'ชื่อแม่', 'ماں کا نام', 'माता का नाम', 'matris nomen,', 'Nama ibu', '母の名前', '어머니 이름'),
(80, 'edit_student', 'edit student', 'সম্পাদনা ছাত্র', 'edit estudiante', 'تحرير الطالب', 'bewerk student', 'редактирования студент', '编辑学生', 'edit öğrenci', 'edição aluno', 'szerkesztés diák', 'modifier étudiant', 'επεξεργασία των φοιτητών', 'Schüler bearbeiten', 'modifica dello studente', 'แก้ไขนักเรียน', 'ترمیم کے طالب علم', 'संपादित छात्र', 'edit studiosum', 'mengedit siswa', '編集学生', '편집 학생'),
(81, 'teacher_list', 'teacher list', 'শিক্ষক তালিকা', 'lista maestra', 'قائمة المعلم', 'leraar lijst', 'Список учителей', '老师名单', 'öğretmen listesi', 'lista de professores', 'tanár lista', 'Liste des enseignants', 'Λίστα των εκπαιδευτικών', 'Lehrer-Liste', 'elenco degli insegnanti', 'รายชื่อครู', 'استاد فہرست', 'शिक्षक सूची', 'magister album', 'daftar guru', '教員リスト', '교사의 목록'),
(82, 'add_teacher', 'add teacher', 'শিক্ষক যোগ', 'añadir profesor', 'إضافة المعلم', 'voeg leraar', 'добавить учителя', '加上老师', 'öğretmen ekle', 'adicionar professor', 'hozzá tanár', 'ajouter enseignant', 'προσθέστε δάσκαλος', 'Lehrer hinzufügen', 'aggiungere insegnante', 'เพิ่มครู', 'استاد شامل', 'शिक्षक जोड़', 'Magister addit', 'menambah guru', '先生を追加', '교사를 추가'),
(83, 'teacher_name', 'teacher name', 'শিক্ষক নাম', 'Nombre del profesor', 'اسم المعلم', 'leraarsnaam', 'Имя учителя', '老师姓名', 'öğretmen adı', 'nome professor', 'tanár név', 'nom des enseignants', 'όνομα των εκπαιδευτικών', 'Lehrer Name', 'Nome del docente', 'ชื่อครู', 'استاد کا نام', 'शिक्षक का नाम', 'magister nomine', 'nama guru', '教員名', '교사 이름'),
(84, 'edit_teacher', 'edit teacher', 'সম্পাদনা শিক্ষক', 'edit maestro', 'تحرير المعلم', 'leraar bewerken', 'править учитель', '编辑老师', 'edit öğretmen', 'editar professor', 'szerkesztés tanár', 'modifier enseignant', 'edit εκπαιδευτικών', 'edit Lehrer', 'modifica insegnante', 'แก้ไขครู', 'ترمیم استاد', 'संपादित करें शिक्षक', 'edit magister', 'mengedit guru', '編集の先生', '편집 교사'),
(85, 'manage_parent', 'manage parent', 'অভিভাবক ও পরিচালনা', 'gestionar los padres', 'إدارة الأم', 'beheren ouder', 'управлять родителей', '母公司管理', 'ebeveyn yönetmek', 'gerenciar pai', 'kezelni szülő', 'gérer parent', 'διαχείριση μητρική', 'verwalten Mutter', 'gestione genitore', 'จัดการปกครอง', 'والدین کا انتظام', 'माता - पिता का प्रबंधन', 'curo parent', 'mengelola orang tua', '親を管理', '부모 관리'),
(86, 'parent_list', 'parent list', 'মূল তালিকা', 'lista primaria', 'قائمة الوالد', 'ouder lijst', 'родительского списка', '父列表', 'ebeveyn listesi', 'lista pai', 'szülő lista', 'liste parent', 'μητρική λίστα', 'geordneten Liste', 'elenco padre', 'รายชื่อผู้ปกครอง', 'والدین کی فہرست', 'माता - पिता सूची', 'parente album', 'daftar induk', '親リスト', '상위 목록'),
(87, 'parent_name', 'parent name', 'মূল নাম', 'Nombre del padre', 'اسم الوالد', 'oudernaam', 'родитель название', '父名', 'ebeveyn isim', 'nome do pai', 'szülő név', 'nom du parent', 'μητρικό όνομα', 'Mutternamen', 'nome del padre', 'ชื่อผู้ปกครอง', 'والدین کے نام', 'माता - पिता का नाम', 'Nomen parentis,', 'nama orang tua', '親の名前', '부모 이름'),
(88, 'relation_with_student', 'relation with student', 'ছাত্রদের সঙ্গে সম্পর্ক', 'relación con el estudiante', 'العلاقة مع الطالب', 'relatie met student', 'отношения с учеником', '与学生关系', 'öğrenci ile ilişkisi', 'relação com o aluno', 'kapcsolatban diák', 'relation avec l\'élève', 'σχέση με τον μαθητή', 'Zusammenhang mit Studenten', 'rapporto con lo studente', 'ความสัมพันธ์กับนักเรียน', 'طالب علم کے ساتھ تعلق', 'छात्रा के साथ संबंध', 'cum inter ipsum', 'hubungan dengan siswa', '学生との関係', '학생과의 관계'),
(89, 'parent_email', 'parent email', 'মূল ইমেইল', 'correo electrónico de los padres', 'البريد الإلكتروني الأم', 'ouder email', 'родитель письмо', '父母的电子邮件', 'ebeveyn email', 'e-mail dos pais', 'szülő e-mail', 'parent email', 'email του γονέα', 'Eltern per E-Mail', 'email genitore', 'อีเมล์ผู้ปกครอง', 'والدین کا ای میل', 'माता - पिता ईमेल', 'parente email', 'email induk', '親電子メール', '부모의 이메일'),
(90, 'parent_phone', 'parent phone', 'ঊর্ধ্বতন ফোন', 'teléfono de los padres', 'الهاتف الوالدين', 'ouder telefoon', 'родитель телефон', '家长电话', 'ebeveyn telefon', 'telefone dos pais', 'szülő telefon', 'mère de téléphone', 'μητρική τηλέφωνο', 'Elterntelefon', 'telefono genitore', 'โทรศัพท์ของผู้ปกครอง', 'والدین فون', 'माता - पिता को फोन', 'parentis phone', 'telepon orang tua', '親の携帯電話', '부모 전화'),
(91, 'parrent_address', 'parrent address', 'parrent ঠিকানা', 'Dirección Parrent', 'عنوان parrent', 'parrent adres', 'Parrent адрес', 'parrent地址', 'parrent adresi', 'endereço Parrent', 'parrent cím', 'adresse Parrent', 'parrent διεύθυνση', 'parrent Adresse', 'Indirizzo parrent', 'ที่อยู่ parrent', 'parrent ایڈریس', 'parrent पता', 'oratio parrent', 'alamat parrent', 'parrentアドレス', 'parrent 주소'),
(92, 'parrent_occupation', 'parrent occupation', 'parrent বৃত্তি', 'ocupación Parrent', 'الاحتلال parrent', 'parrent bezetting', 'Parrent оккупация', 'parrent职业', 'parrent işgal', 'ocupação Parrent', 'parrent Foglalkozás', 'occupation Parrent', 'parrent επάγγελμα', 'parrent Beruf', 'occupazione parrent', 'อาชีพ parrent', 'parrent قبضے', 'parrent कब्जे', 'opus parrent', 'pendudukan parrent', 'parrent職業', 'parrent 직업'),
(93, 'add', 'add', 'যোগ করা', 'añadir', 'إضافة', 'toevoegen', 'добавлять', '加', 'eklemek', 'adicionar', 'hozzáad', 'ajouter', 'προσθήκη', 'hinzufügen', 'aggiungere', 'เพิ่ม', 'شامل', 'जोड़ना', 'Adde', 'menambahkan', '加える', '추가'),
(94, 'parent_of', 'parent of', 'অভিভাবক', 'matriz de', 'الأم ل', 'ouder van', 'родитель', '父', 'ebeveyn', 'pai', 'szülő', 'parent d\'', 'γονέας', 'Muttergesellschaft der', 'madre di', 'ผู้ปกครองของ', 'والدین', 'के माता - पिता', 'parentem,', 'induk dari', 'の親', '의 부모'),
(95, 'profession', 'profession', 'পেশা', 'profesión', 'مهنة', 'beroep', 'профессия', '职业', 'meslek', 'profissão', 'szakma', 'profession', 'επάγγελμα', 'Beruf', 'professione', 'อาชีพ', 'پیشہ', 'व्यवसाय', 'professio', 'profesi', '職業', '직업'),
(96, 'edit_parent', 'edit parent', 'সম্পাদনা ঊর্ধ্বতন', 'edit padres', 'تحرير الوالدين', 'bewerk ouder', 'править родитель', '编辑父', 'edit ebeveyn', 'edição pai', 'szerkesztés szülő', 'modifier parent', 'edit γονέα', 'edit Mutter', 'modifica genitore', 'แก้ไขผู้ปกครอง', 'میں ترمیم کریں والدین', 'संपादित जनक', 'edit parent', 'mengedit induk', '編集親', '편집 부모'),
(97, 'add_parent', 'add parent', 'ঊর্ধ্বতন যোগ', 'añadir los padres', 'إضافة الوالد', 'Voeg een ouder', 'добавить родителя', '添加父', 'ebeveyn ekle', 'adicionar pai', 'hozzá szülő', 'ajouter parent', 'προσθέστε μητρική', 'Mutter hinzufügen', 'aggiungere genitore', 'เพิ่มผู้ปกครอง', 'والدین شامل', 'माता - पिता जोड़', 'adde parent', 'menambahkan orang tua', '親を追加', '부모를 추가'),
(98, 'manage_subject', 'manage subject', 'বিষয় ও পরিচালনা', 'gestionar sujeto', 'إدارة الموضوع', 'beheren onderwerp', 'управлять тему', '管理主题', 'konuyu yönetmek', 'gerenciar assunto', 'kezelni tárgy', 'gérer sujet', 'διαχείριση υπόκειται', 'Thema verwalten', 'gestire i soggetti', 'การจัดการเรื่อง', 'موضوع کا انتظام', 'विषय का प्रबंधन', 'subiectum disponat', 'mengelola subjek', '対象を管理', '대상 관리'),
(99, 'subject_list', 'subject list', 'বিষয় তালিকা', 'lista por materia', 'قائمة الموضوع', 'Onderwerp lijst', 'Список подлежит', '主题列表', 'konu listesi', 'lista por assunto', 'téma lista', 'liste de sujets', 'υπόκεινται λίστα', 'Themenliste', 'lista soggetto', 'รายการเรื่อง', 'موضوع کی فہرست', 'विषय सूची', 'subiectum album', 'daftar subjek', 'サブジェクトリスト', '주제 목록'),
(100, 'add_subject', 'add subject', 'বিষয় যোগ', 'Añadir asunto', 'إضافة الموضوع', 'Onderwerp toevoegen', 'добавить тему', '新增主题', 'konu ekle', 'adicionar assunto', 'Tárgy hozzáadása', 'ajouter l\'objet', 'Προσθήκη θέματος', 'Thema hinzufügen', 'aggiungere soggetto', 'เพิ่มเรื่อง', 'موضوع', 'जोड़ें विषय', 're addere', 'menambahkan subjek', '件名を追加', '제목을 추가'),
(101, 'subject_name', 'subject name', 'বিষয় নাম', 'nombre del sujeto', 'اسم الموضوع', 'Onderwerp naam', 'имя субъекта', '主题名称', 'konu adı', 'nome do assunto', 'tárgy megnevezése', 'nom du sujet', 'υπόκεινται όνομα', 'Thema Namen', 'nome del soggetto', 'ชื่อเรื่อง', 'موضوع کے نام', 'विषय का नाम', 'agitur nomine', 'nama subjek', 'サブジェクト名', '주체 이름'),
(102, 'edit_subject', 'edit subject', 'সম্পাদনা বিষয়', 'Editar asunto', 'تحرير الموضوع', 'Onderwerp bewerken', 'Изменить тему', '编辑主题', 'düzenleme konusu', 'Editar assunto', 'Tárgy szerkesztése', 'modifier l\'objet', 'edit θέμα', 'Betreff bearbeiten', 'Modifica oggetto', 'แก้ไขเรื่อง', 'موضوع میں ترمیم کریں', 'विषय संपादित करें', 'edit subiecto', 'mengedit subjek', '編集対象', '제목 수정'),
(103, 'manage_class', 'manage class', 'ক্লাস ও পরিচালনা', 'gestionar clase', 'إدارة الصف', 'beheren klasse', 'управлять класс', '管理类', 'sınıf yönetmek', 'gerenciar classe', 'kezelni osztály', 'gérer classe', 'διαχείριση τάξης', 'Klasse verwalten', 'gestione della classe', 'การจัดการชั้นเรียน', 'کلاس کا انتظام', 'वर्ग का प्रबंधन', 'genus regendi', 'mengelola kelas', 'クラスを管理', '클래스에게 관리'),
(104, 'class_list', 'class list', 'বর্গ তালিকা', 'lista de la clase', 'قائمة فئة', 'klasse lijst', 'Список класс', '类列表', 'sınıf listesi', 'lista de classe', 'class lista', 'liste de classe', 'πίνακας αποτελεσμάτων', 'Klassenliste', 'elenco di classe', 'รายการชั้น', 'کلاس فہرست', 'कक्षा सूची', 'genus album', 'daftar kelas', 'クラスリスト', '클래스 목록'),
(105, 'add_class', 'add class', 'ক্লাসে যোগ', 'agregar la clase', 'إضافة فئة', 'voeg klasse', 'добавить класс', '添加类', 'sınıf eklemek', 'adicionar classe', 'hozzá osztály', 'ajouter la classe', 'προσθέσετε τάξη', 'Klasse hinzufügen', 'aggiungere classe', 'เพิ่มระดับ', 'کلاس شامل کریں', 'वर्ग जोड़', 'adde genus', 'menambahkan kelas', 'クラスを追加', '클래스를 추가'),
(106, 'class_name', 'class name', 'শ্রেণীর নাম', 'nombre de la clase', 'اسم الفئة', 'class naam', 'Имя класса', '类名', 'sınıf adı', 'nome da classe', 'osztály neve', 'nom de la classe', 'όνομα της κλάσης', 'Klassennamen', 'nome della classe', 'ชื่อชั้น', 'کلاس نام', 'वर्ग के नाम', 'Classis nomine', 'nama kelas', 'クラス名', '클래스 이름'),
(107, 'numeric_name', 'numeric name', 'সাংখ্যিক নাম', 'nombre numérico', 'اسم رقمية', 'numerieke naam', 'числовое имя', '数字名称', 'Sayısal isim', 'nome numérico', 'numerikus név', 'nom numérique', 'αριθμητικό όνομα', 'numerischen Namen', 'nome numerico', 'ชื่อตัวเลข', 'عددی نام', 'सांख्यिक नाम', 'secundum numerum est secundum nomen,', 'Nama numerik', '数値の名前', '숫자 이름'),
(108, 'name_numeric', 'name numeric', 'সাংখ্যিক নাম দিন', 'nombre numérico', 'تسمية رقمية', 'naam numerieke', 'назвать числовой', '数字命名', 'sayısal isim', 'nome numérico', 'név numerikus', 'nommer numérique', 'όνομα αριθμητικό', 'nennen numerischen', 'nome numerico', 'ชื่อตัวเลข', 'عددی نام', 'सांख्यिक का नाम', 'secundum numerum est secundum nomen,', 'nama numerik', '数値に名前を付ける', '숫자 이름을'),
(109, 'edit_class', 'edit class', 'সম্পাদনা বর্গ', 'clase de edición', 'الطبقة تحرير', 'bewerken klasse', 'править класс', '编辑类', 'sınıf düzenle', 'edição classe', 'szerkesztés osztály', 'modifier la classe', 'edit κατηγορία', 'Klasse bearbeiten', 'modifica della classe', 'แก้ไขระดับ', 'ترمیم کلاس', 'संपादित वर्ग', 'edit genere', 'mengedit kelas', '編集クラス', '편집 클래스'),
(110, 'manage_exam', 'manage exam', 'পরীক্ষা পরিচালনা', 'gestionar examen', 'إدارة الامتحان', 'beheren examen', 'управлять экзамен', '考试管理', 'sınavı yönetmek', 'gerenciar exame', 'kezelni vizsga', 'gérer examen', 'διαχείριση εξετάσεις', 'Prüfung verwalten', 'gestire esame', 'การจัดการสอบ', 'امتحان کا انتظام', 'परीक्षा का प्रबंधन', 'curo ipsum', 'mengelola ujian', '試験を管理', '시험 관리'),
(111, 'exam_list', 'exam list', 'পরীক্ষার তালিকা', 'lista de exámenes', 'قائمة الامتحان', 'examen lijst', 'Список экзамен', '考试名单', 'sınav listesi', 'lista de exames', 'vizsga lista', 'liste d\'examen', 'Λίστα εξετάσεις', 'Prüfungsliste', 'elenco esami', 'รายการสอบ', 'امتحان فہرست', 'परीक्षा सूची', 'Lorem ipsum album', 'daftar ujian', '試験のリスト', '시험 목록'),
(112, 'add_exam', 'add exam', 'পরীক্ষার যোগ', 'agregar examen', 'إضافة امتحان', 'voeg examen', 'добавить экзамен', '新增考试', 'sınav eklemek', 'adicionar exame', 'hozzá vizsga', 'ajouter examen', 'προσθέσετε εξετάσεις', 'Prüfung hinzufügen', 'aggiungere esame', 'เพิ่มการสอบ', 'امتحان میں شامل کریں', 'परीक्षा जोड़', 'adde ipsum', 'menambahkan ujian', '試験を追加', '시험에 추가'),
(113, 'exam_name', 'exam name', 'পরীক্ষার নাম', 'nombre del examen', 'اسم الامتحان', 'examen naam', 'Название экзамен', '考试名称', 'sınav adı', 'nome do exame', 'Vizsga neve', 'nom de l\'examen', 'Το όνομά εξετάσεις', 'Prüfungsnamen', 'nome dell\'esame', 'ชื่อสอบ', 'امتحان کے نام', 'परीक्षा का नाम', 'ipsum nomen,', 'Nama ujian', '試験名', '시험 이름'),
(114, 'date', 'date', 'তারিখ', 'fecha', 'تاريخ', 'datum', 'дата', '日期', 'tarih', 'data', 'dátum', 'date', 'ημερομηνία', 'Datum', 'data', 'วันที่', 'تاریخ', 'तारीख', 'date', 'tanggal', '日付', '날짜'),
(115, 'comment', 'comment', 'মন্তব্য', 'comentario', 'تعليق', 'commentaar', 'комментарий', '评论', 'yorum', 'comentário', 'megjegyzés', 'commentaire', 'σχόλιο', 'Kommentar', 'commento', 'ความเห็น', 'تبصرہ', 'टिप्पणी', 'comment', 'komentar', 'コメント', '논평'),
(116, 'edit_exam', 'edit exam', 'সম্পাদনা পরীক্ষা', 'examen de edición', 'امتحان تحرير', 'bewerk examen', 'править экзамен', '编辑考试', 'edit sınavı', 'edição do exame', 'szerkesztés vizsga', 'modifier examen', 'edit εξετάσεις', 'edit Prüfung', 'modifica esame', 'แก้ไขการสอบ', 'ترمیم امتحان', 'संपादित परीक्षा', 'edit ipsum', 'mengedit ujian', '編集試験', '편집 시험'),
(117, 'manage_exam_marks', 'manage exam marks', 'পরীক্ষা চিহ্ন ও পরিচালনা', 'gestionar marcas de examen', 'إدارة علامات الامتحان', 'beheren examencijfers', 'управлять экзаменационные отметки', '管理考试痕', 'sınav işaretleri yönetmek', 'gerenciar marcas exame', 'kezelni vizsga jelek', 'gérer les marques d\'examen', 'διαχείριση των σημάτων εξετάσεις', 'Prüfungsnoten verwalten', 'gestire i voti degli esami', 'จัดการสอบเครื่องหมาย', 'امتحان کے نشانات کا انتظام', 'परीक्षा के निशान का प्रबंधन', 'ipsum curo indicia', 'mengelola nilai ujian', '試験マークを管理', '시험 점수를 관리'),
(118, 'manage_marks', 'manage marks', 'চিহ্ন ও পরিচালনা', 'gestionar marcas', 'إدارة علامات', 'beheren merken', 'управлять знаки', '商标管理', 'işaretleri yönetmek', 'gerenciar marcas', 'kezelni jelek', 'gérer les marques', 'διαχείριση των σημάτων', 'Markierungen verwalten', 'gestire i marchi', 'จัดการเครื่องหมาย', 'نمبروں کا انتظام', 'निशान का प्रबंधन', 'curo indicia', 'mengelola tanda', 'マークを管理', '마크를 관리'),
(119, 'select_exam', 'select exam', 'পরীক্ষার নির্বাচন', 'seleccione examen', 'حدد الامتحان', 'selecteer examen', 'выбрать экзамен', '选择考试', 'sınavı seçin', 'selecionar exame', 'válassza ki a vizsga', 'sélectionnez examen', 'επιλέξτε εξετάσεις', 'Prüfung wählen', 'seleziona esame', 'เลือกสอบ', 'امتحان منتخب کریں', 'परीक्षा का चयन', 'velit ipsum', 'pilih ujian', '受験を選択', '시험을 선택'),
(120, 'select_class', 'select class', 'বর্গ নির্বাচন', 'seleccione clase', 'حدد فئة', 'selecteren klasse', 'выбрать класс', '选择产品类别', 'sınıf seçin', 'selecionar classe', 'válassza osztály', 'sélectionnez classe', 'Επιλέξτε κατηγορία', 'Klasse wählen', 'seleziona classe', 'เลือกชั้น', 'کلاس منتخب کریں', 'वर्ग का चयन करें', 'genus eligere,', 'pilih kelas', 'クラスを選択', '클래스를 선택'),
(121, 'select_subject', 'select subject', 'বিষয় নির্বাচন করুন', 'seleccione tema', 'حدد الموضوع', 'Selecteer onderwerp', 'выберите тему', '选择主题', 'konu seçin', 'selecionar assunto', 'Válassza a Tárgy', 'sélectionner le sujet', 'επιλέξτε θέμα', 'Thema wählen', 'seleziona argomento', 'เลือกเรื่อง', 'موضوع منتخب کریں', 'विषय का चयन', 'eligere subditos', 'pilih subjek', '件名を選択', '주제를 선택'),
(122, 'select_an_exam', 'select an exam', 'একটি পরীক্ষা নির্বাচন', 'seleccione un examen', 'حدد الامتحان', 'selecteer een examen', 'выбрать экзамен', '选择考试', 'Bir sınav seçin', 'selecionar um exame', 'válasszon ki egy vizsga', 'sélectionner un examen', 'επιλέξτε μια εξέταση', 'Wählen Sie eine Prüfung', 'selezionare un esame', 'เลือกสอบ', 'امتحان منتخب کریں', 'एक परीक्षा का चयन', 'Eligebatur autem ipsum', 'pilih ujian', '受験を選択', '시험을 선택'),
(123, 'mark_obtained', 'mark obtained', 'চিহ্নিত প্রাপ্ত', 'calificación obtenida', 'بمناسبة الحصول على', 'markeren verkregen', 'отметить получены', '获得标', 'işaretlemek elde', 'marca obtida', 'jelölje kapott', 'marquer obtenu', 'σήμα που λαμβάνεται', 'Markieren Sie erhalten', 'contrassegnare ottenuto', 'ทำเครื่องหมายที่ได้รับ', 'نشان زد حاصل', 'अंक प्राप्त', 'attende obtinuit', 'menandai diperoleh', 'マークが得', '마크 획득'),
(124, 'attendance', 'attendance', 'উপস্থিতি', 'asistencia', 'الحضور', 'opkomst', 'посещаемость', '护理', 'katılım', 'comparecimento', 'részvétel', 'présence', 'παρουσία', 'Teilnahme', 'partecipazione', 'การดูแลรักษา', 'حاضری', 'उपस्थिति', 'auscultant', 'kehadiran', '出席', '출석'),
(125, 'manage_grade', 'manage grade', 'গ্রেড পরিচালনা', 'gestión de calidad', 'إدارة الصف', 'beheren leerjaar', 'управлять класс', '管理级', 'notu yönetmek', 'gerenciar grau', 'kezelni fokozat', 'gérer de qualité', 'διαχείριση ποιότητας', 'Klasse verwalten', 'gestione grade', 'จัดการเกรด', 'گریڈ کا انتظام', 'ग्रेड का प्रबंधन', 'moderari gradu', 'mengelola kelas', 'グレードを管理', '등급 관리'),
(126, 'grade_list', 'grade list', 'গ্রেড তালিকা', 'Lista de grado', 'قائمة الصف', 'cijferlijst', 'Список класса', '等级列表', 'sınıf listesi', 'lista grau', 'fokozat lista', 'liste de qualité', 'Λίστα βαθμού', 'Notenliste', 'elenco grade', 'รายการเกรด', 'گریڈ فہرست', 'ग्रेड की सूची', 'gradus album', 'daftar kelas', 'グレード一覧', '등급 목록'),
(127, 'add_grade', 'add grade', 'গ্রেড যোগ করুন', 'añadir grado', 'إضافة الصف', 'voeg leerjaar', 'добавить класс', '添加级', 'not eklemek', 'adicionar grau', 'hozzá fokozat', 'ajouter note', 'προσθήκη βαθμού', 'Klasse hinzufügen', 'aggiungere grade', 'เพิ่มเกรด', 'گریڈ میں شامل کریں', 'ग्रेड जोड़', 'adde gradum,', 'menambahkan kelas', 'グレードを追加', '등급을 추가'),
(128, 'grade_name', 'grade name', 'গ্রেড নাম', 'Nombre de grado', 'اسم الصف', 'rangnaam', 'Название сорта', '等级名称', 'sınıf adı', 'nome da classe', 'fokozat név', 'nom de la catégorie', 'Όνομα βαθμού', 'Klasse Name', 'nome del grado', 'ชื่อชั้น', 'گریڈ نام', 'ग्रेड का नाम', 'nomen, gradus,', 'nama kelas', 'グレード名', '등급 이름'),
(129, 'grade_point', 'grade point', 'গ্রেড পয়েন্ট', 'de calificaciones', 'تراكمي', 'rangpunt', 'балл', '成绩', 'not', 'ponto de classe', 'fokozatú pont', 'cumulative', 'βαθμών', 'Noten', 'punto di grado', 'จุดเกรด', 'گریڈ پوائنٹ', 'ग्रेड बिंदु', 'gradus punctum', 'indeks prestasi', '成績評価点', '학점'),
(130, 'mark_from', 'mark from', 'চিহ্ন থেকে', 'marca de', 'علامة من', 'mark uit', 'знак от', '从商标', 'mark dan', 'marca de', 'jelölést', 'marque de', 'σήμα από', 'Marke aus', 'segno da', 'เครื่องหมายจาก', 'نشان سے', 'मार्क से', 'marcam', 'mark dari', 'マークから', '표에서'),
(131, 'mark_upto', 'mark upto', 'পর্যন্ত চিহ্নিত', 'marcar hasta', 'بمناسبة تصل', 'mark tot', 'отметить ДО', '高达标记', 'kadar işaretlemek', 'marcar até', 'jelölje upto', 'marquer jusqu\'à', 'σήμα μέχρι', 'Markieren Sie bis zu', 'contrassegnare fino a', 'ทำเครื่องหมายเกิน', 'تک کے موقع', 'तक चिह्नित', 'Genitus est notare', 'menandai upto', '点で最大マーク', '표까지'),
(132, 'edit_grade', 'edit grade', 'সম্পাদনা গ্রেড', 'edit grado', 'تحرير الصف', 'Cijfer bewerken', 'править класса', '编辑等级', 'edit notu', 'edição grau', 'szerkesztés fokozat', 'edit qualité', 'edit βαθμού', 'edit Grad', 'modifica grade', 'แก้ไขเกรด', 'ترمیم گریڈ', 'संपादित ग्रेड', 'edit gradu', 'mengedit kelas', '編集グレード', '편집 등급'),
(133, 'manage_class_routine', 'manage class routine', 'ক্লাসের রুটিন পরিচালনা', 'gestionar rutina de la clase', 'إدارة الطبقة الروتينية', 'beheren klasroutine', 'управлять рутину класса', '管理类常规', 'sınıf rutin yönetmek', 'gerenciar rotina classe', 'kezelni class rutin', 'gérer la routine de classe', 'διαχειρίζονται τάξη ρουτίνα', 'verwalten Klasse Routine', 'gestione classe di routine', 'การจัดการชั้นเรียนตามปกติ', 'کلاس معمول کا انتظام', 'वर्ग दिनचर्या का प्रबंधन', 'uno in genere tractare', 'mengelola rutinitas kelas', 'クラスルーチンを管理', '수준의 일상적인 관리'),
(134, 'class_routine_list', 'class routine list', 'ক্লাসের রুটিন তালিকা', 'clase de lista de rutina', 'قائمة الروتينية الطبقة', 'klasroutine lijst', 'класс рутина список', '班级常规列表', 'sınıf rutin listesi', 'classe de lista de rotina', 'osztály rutin lista', 'classe liste routine', 'κλάση list ρουτίνας', 'Klasse Routine Liste', 'classe lista di routine', 'รายการประจำชั้น', 'کلاس معمول کے مطابق فہرست', 'वर्ग दिनचर्या सूची', 'uno genere album', 'Daftar rutin kelas', 'クラスルーチン一覧', '클래스 루틴 목록'),
(135, 'add_class_routine', 'add class routine', 'ক্লাসের রুটিন যুক্ত', 'añadir rutina de la clase', 'إضافة فئة الروتينية', 'voeg klasroutine', 'добавить подпрограмму класса', '添加类常规', 'sınıf rutin eklemek', 'adicionar rotina classe', 'hozzá class rutin', 'ajouter routine de classe', 'προσθέσετε τάξη ρουτίνα', 'Klasse hinzufügen Routine', 'aggiungere classe di routine', 'เพิ่มระดับตามปกติ', 'کلاس معمول میں شامل کریں', 'वर्ग दिनचर्या जोड़', 'adde genus moris', 'menambahkan rutin kelas', 'クラス·ルーチンを追加', '클래스 루틴을 추가'),
(136, 'day', 'day', 'দিন', 'día', 'يوم', 'dag', 'день', '日', 'gün', 'dia', 'nap', 'jour', 'ημέρα', 'Tag', 'giorno', 'วัน', 'دن', 'दिन', 'die,', 'hari', '日', '일'),
(137, 'starting_time', 'starting time', 'সময়ের শুরু', 'tiempo de inicio', 'بدءا الوقت', 'starttijd', 'время начала', '开始时间', 'başlangıç ​​zamanı', 'tempo começando', 'indítási idő', 'temps de démarrage', 'ώρα έναρξης', 'Startzeit', 'tempo di avviamento', 'เวลาเริ่มต้น', 'وقت شروع ہونے', 'समय की शुरुआत के', 'tum satus', 'waktu mulai', '起動時間', '시작 시간'),
(138, 'ending_time', 'ending time', 'সময় শেষ', 'hora de finalización', 'تنتهي الساعة', 'eindtijd', 'время окончания', '结束时间', 'bitiş zamanını', 'tempo final', 'befejezési időpont', 'heure de fin', 'ώρα λήξης', 'Endzeit', 'ora finale', 'สิ้นสุดเวลา', 'وقت ختم', 'समय समाप्त होने के', 'et finis temporis,', 'akhir waktu', '終了時刻', '종료 시간'),
(139, 'edit_class_routine', 'edit class routine', 'সম্পাদনা ক্লাস রুটিন', 'rutina de la clase de edición', 'الطبقة تحرير الروتينية', 'bewerk klasroutine', 'Процедура редактирования класс', '编辑类常规', 'sınıf düzenle rutin', 'rotina de edição de classe', 'szerkesztés osztály rutin', 'routine modifier de classe', 'edit τάξη ρουτίνα', 'edit Klasse Routine', 'modifica della classe di routine', 'แก้ไขชั้นเรียนตามปกติ', 'ترمیم کلاس معمول', 'संपादित वर्ग दिनचर्या', 'edit uno genere', 'rutin mengedit kelas', '編集クラスのルーチン', '편집 클래스 루틴'),
(140, 'manage_invoice/payment', 'manage invoice/payment', 'চালান / পেমেন্ট পরিচালনা', 'gestionar factura / pago', 'إدارة فاتورة / دفع', 'beheren factuur / betaling', 'управлять счета / оплата', '管理发票/付款', 'fatura / ödeme yönetmek', 'gerenciar fatura / pagamento', 'kezelni számla / fizetési', 'gérer facture / paiement', 'διαχείριση τιμολογίου / πληρωμής', 'Verwaltung Rechnung / Zahlung', 'gestione fattura / pagamento', 'จัดการใบแจ้งหนี้ / การชำระเงิน', 'انوائس / ادائیگی کا انتظام', 'चालान / भुगतान का प्रबंधन', 'curo cautionem / solutionem', 'mengelola tagihan / pembayaran', '請求書/支払管理', '인보이스 / 결제 관리'),
(141, 'invoice/payment_list', 'invoice/payment list', 'চালান / পেমেন্ট তালিকা', 'lista de facturas / pagos', 'قائمة فاتورة / دفع', 'factuur / betaling lijst', 'Список счета / оплата', '发票/付款清单', 'fatura / ödeme listesi', 'lista de fatura / pagamento', 'számla / fizetési lista', 'liste facture / paiement', 'Λίστα τιμολογίου / πληρωμής', 'Rechnung / Zahlungsliste', 'elenco fattura / pagamento', 'รายการใบแจ้งหนี้ / การชำระเงิน', 'انوائس / ادائیگی کی فہرست', 'चालान / भुगतान सूची', 'cautionem / list pretium', 'daftar tagihan / pembayaran', '請求書/支払一覧', '인보이스 / 결제리스트'),
(142, 'add_invoice/payment', 'add invoice/payment', 'চালান / পেমেন্ট যোগ', 'añadir factura / pago', 'إضافة فاتورة / دفع', 'voeg factuur / betaling', 'добавить счета / оплата', '添加发票/付款', 'fatura / ödeme eklemek', 'adicionar factura / pagamento', 'hozzá számla / fizetési', 'ajouter facture / paiement', 'προσθήκη τιμολογίου / πληρωμής', 'hinzufügen Rechnung / Zahlung', 'aggiungere fatturazione / pagamento', 'เพิ่มใบแจ้งหนี้ / การชำระเงิน', 'انوائس / ادائیگی شامل', 'चालान / भुगतान जोड़ें', 'add cautionem / solutionem', 'menambahkan tagihan / pembayaran', '請求書/支払を追加', '송장 / 지불을 추가'),
(143, 'title', 'title', 'খেতাব', 'título', 'لقب', 'titel', 'название', '标题', 'başlık', 'título', 'cím', 'titre', 'τίτλος', 'Titel', 'titolo', 'ชื่อเรื่อง', 'عنوان', 'शीर्षक', 'title', 'judul', 'タイトル', '표제'),
(144, 'description', 'description', 'বিবরণ', 'descripción', 'وصف', 'beschrijving', 'описание', '描述', 'tanım', 'descrição', 'leírás', 'description', 'περιγραφή', 'Beschreibung', 'descrizione', 'ลักษณะ', 'تفصیل', 'विवरण', 'description', 'deskripsi', '説明', '기술');
INSERT INTO `language` (`phrase_id`, `phrase`, `english`, `bengali`, `spanish`, `arabic`, `dutch`, `russian`, `chinese`, `turkish`, `portuguese`, `hungarian`, `french`, `greek`, `german`, `italian`, `thai`, `urdu`, `hindi`, `latin`, `indonesian`, `japanese`, `korean`) VALUES
(145, 'amount', 'amount', 'পরিমাণ', 'cantidad', 'مبلغ', 'bedrag', 'количество', '量', 'miktar', 'quantidade', 'mennyiség', 'montant', 'ποσό', 'Menge', 'importo', 'จำนวน', 'رقم', 'राशि', 'tantum', 'jumlah', '額', '양'),
(146, 'status', 'status', 'অবস্থা', 'estado', 'حالة', 'toestand', 'статус', '状态', 'durum', 'estado', 'állapot', 'statut', 'κατάσταση', 'Status', 'stato', 'สถานะ', 'درجہ', 'हैसियत', 'status', 'status', 'ステータス', '지위'),
(147, 'view_invoice', 'view invoice', 'দেখুন চালান', 'vista de la factura', 'عرض الفاتورة', 'view factuur', 'вид счета-фактуры', '查看发票', 'view fatura', 'vista da fatura', 'view számla', 'vue facture', 'προβολή τιμολόγιο', 'Ansicht Rechnung', 'vista fattura', 'ดูใบแจ้งหนี้', 'دیکھیں انوائس', 'देखें चालान', 'propter cautionem', 'lihat faktur', 'ビュー請求書', '보기 송장'),
(148, 'paid', 'paid', 'পরিশোধ', 'pagado', 'مدفوع', 'betaald', 'оплаченный', '支付', 'ücretli', 'pago', 'fizetett', 'payé', 'καταβληθεί', 'bezahlt', 'pagato', 'ต้องจ่าย', 'ادا کیا', 'प्रदत्त', 'solutis', 'dibayar', '支払われた', '지급'),
(149, 'unpaid', 'unpaid', 'অবৈতনিক', 'no pagado', 'غير مدفوع', 'onbetaald', 'неоплаченный', '未付', 'ödenmemiş', 'não remunerado', 'kifizetetlen', 'non rémunéré', 'απλήρωτη', 'unbezahlt', 'non pagato', 'ยังไม่ได้ชำระ', 'بلا معاوضہ', 'अवैतनिक', 'non est constitutus,', 'dibayar', '未払い', '지불하지 않은'),
(150, 'add_invoice', 'add invoice', 'চালান যোগ', 'añadir factura', 'إضافة الفاتورة', 'voeg factuur', 'добавить счет', '添加发票', 'faturayı eklemek', 'adicionar fatura', 'hozzá számla', 'ajouter facture', 'προσθέστε τιμολόγιο', 'Rechnung hinzufügen', 'aggiungere fattura', 'เพิ่มใบแจ้งหนี้', 'انوائس میں شامل', 'चालान जोड़', 'add cautionem', 'menambahkan faktur', '請求書を追加', '송장을 추가'),
(151, 'payment_to', 'payment to', 'পেমেন্ট', 'pago a', 'دفع ل', 'betaling aan', 'оплата', '支付', 'için ödeme', 'pagamento', 'fizetés', 'paiement', 'πληρωμή', 'Zahlung an', 'pagamento', 'ชำระเงินให้กับ', 'ادائیگی', 'को भुगतान', 'pecunia', 'pembayaran kepada', 'への支払い', '에 지불'),
(152, 'bill_to', 'bill to', 'বিল', 'proyecto de ley para', 'مشروع قانون ل', 'wetsvoorstel om', 'Законопроект о', '法案', 'bill', 'projeto de lei para', 'törvényjavaslat', 'projet de loi', 'νομοσχέδιο για την', 'Gesetzentwurf zur', 'disegno di legge per', 'บิล', 'بل', 'बिल के लिए', 'latumque', 'RUU untuk', '請求する', '법안'),
(153, 'invoice_title', 'invoice title', 'চালান শিরোনাম', 'Título de la factura', 'عنوان الفاتورة', 'factuur titel', 'Название счета', '发票抬头', 'fatura başlık', 'título fatura', 'számla cím', 'titre de la facture', 'Τίτλος τιμολόγιο', 'Rechnungs Titel', 'title fattura', 'ชื่อใบแจ้งหนี้', 'انوائس عنوان', 'चालान शीर्षक', 'title cautionem', 'judul faktur', '請求書のタイトル', '송장 제목'),
(154, 'invoice_id', 'invoice id', 'চালান আইডি', 'Identificación de la factura', 'فاتورة معرف', 'factuur id', 'счет-фактура ID', '发票编号', 'fatura id', 'id fatura', 'számla id', 'Identifiant facture', 'id τιμολόγιο', 'Rechnung-ID', 'fattura id', 'ใบแจ้งหนี้หมายเลข', 'انوائس ID', 'चालान आईडी', 'id cautionem', 'faktur id', '請求書ID', '송장 ID'),
(155, 'edit_invoice', 'edit invoice', 'সম্পাদনা চালান', 'edit factura', 'تحرير الفاتورة', 'bewerk factuur', 'редактирования счета-фактуры', '编辑发票', 'edit fatura', 'edição fatura', 'szerkesztés számla', 'modifier la facture', 'edit τιμολόγιο', 'edit Rechnung', 'modifica fattura', 'แก้ไขใบแจ้งหนี้', 'ترمیم انوائس', 'संपादित चालान', 'edit cautionem', 'mengedit faktur', '編集送り状', '편집 송장'),
(156, 'manage_library_books', 'manage library books', 'লাইব্রেরির বই ও পরিচালনা', 'gestionar libros de la biblioteca', 'إدارة مكتبة الكتب', 'beheren bibliotheekboeken', 'управлять библиотечные книги', '管理图书', 'kitapları kütüphane yönetmek', 'gerenciar os livros da biblioteca', 'kezelni könyvtári könyvek', 'gérer des livres de bibliothèque', 'διαχειριστείτε τα βιβλία της βιβλιοθήκης', 'Bücher aus der Bibliothek verwalten', 'gestire i libri della biblioteca', 'จัดการหนั​​งสือห้องสมุด', 'کتب خانے کی کتابیں منظم', 'पुस्तकालय की पुस्तकों का प्रबंधन', 'curo bibliotheca librorum,', 'mengelola buku perpustakaan', '図書館の本を管理', '도서관 책 관리'),
(157, 'book_list', 'book list', 'পাঠ্যতালিকা', 'lista de libros', 'قائمة الكتب', 'boekenlijst', 'Список книг', '书单', 'kitap listesi', 'lista de livros', 'book lista', 'liste de livres', 'λίστα βιβλίων', 'Buchliste', 'elenco libri', 'รายการหนั​​งสือ', 'کتاب کی فہرست', 'पुस्तक सूची', 'album', 'daftar buku', 'ブックリスト', '도서 목록'),
(158, 'add_book', 'add book', 'বই যোগ', 'Añadir libro', 'إضافة كتاب', 'boek toevoegen', 'добавить книгу', '加入书', 'kitap eklemek', 'adicionar livro', 'Könyv hozzáadása', 'ajouter livre', 'προσθέστε το βιβλίο', 'Buch hinzufügen', 'aggiungere il libro', 'เพิ่มหนังสือ', 'کتاب شامل', 'पुस्तक जोड़', 'adde libri', 'menambahkan buku', '本を追加', '책을 추가'),
(159, 'book_name', 'book name', 'বইয়ের নাম', 'Nombre del libro', 'اسم الكتاب', 'boeknaam', 'Название книги', '书名', 'kitap adı', 'nome livro', 'book név', 'nom de livre', 'το όνομα του βιβλίου', 'Buchnamen', 'nome del libro', 'ชื่อหนังสือ', 'کتاب کا نام', 'किताब का नाम', 'librum nomine', 'nama buku', 'ブック名', '책 이름'),
(160, 'author', 'author', 'লেখক', 'autor', 'الكاتب', 'auteur', 'автор', '作者', 'yazar', 'autor', 'szerző', 'auteur', 'συγγραφέας', 'Autor', 'autore', 'ผู้เขียน', 'مصنف', 'लेखक', 'auctor', 'penulis', '著者', '저자'),
(161, 'price', 'price', 'দাম', 'precio', 'السعر', 'prijs', 'цена', '价格', 'fiyat', 'preço', 'ár', 'prix', 'τιμή', 'Preis', 'prezzo', 'ราคา', 'قیمت', 'कीमत', 'price', 'harga', '価格', '가격'),
(162, 'available', 'available', 'উপলব্ধ', 'disponible', 'متاح', 'beschikbaar', 'доступный', '可用的', 'mevcut', 'disponível', 'rendelkezésre álló', 'disponible', 'διαθέσιμος', 'verfügbar', 'disponibile', 'สามารถใช้ได้', 'دستیاب', 'उपलब्ध', 'available', 'tersedia', '利用できる', '유효한'),
(163, 'unavailable', 'unavailable', 'অপ্রাপ্য', 'indisponible', 'غير متاح', 'niet beschikbaar', 'недоступен', '不可用', 'yok', 'indisponível', 'érhető el', 'indisponible', 'διαθέσιμο', 'nicht verfügbar', 'non disponibile', 'ไม่มี', 'دستیاب نہیں', 'अनुपलब्ध', 'unavailable', 'tidak tersedia', '利用できない', '없는'),
(164, 'edit_book', 'edit book', 'সম্পাদনা বই', 'libro de edición', 'كتاب تحرير', 'bewerk boek', 'править книга', '编辑本书', 'edit kitap', 'edição do livro', 'edit könyv', 'edit livre', 'επεξεργαστείτε το βιβλίο', 'edit Buch', 'modifica book', 'แก้ไขหนังสือ', 'ترمیم کتاب', 'संपादित पुस्तक', 'edit Liber', 'mengedit buku', '編集の本', '편집 책'),
(165, 'manage_transport', 'manage transport', 'পরিবহন ও পরিচালনা', 'gestionar el transporte', 'إدارة النقل', 'beheren van vervoerssystemen', 'управлять транспортом', '运输管理', 'ulaşım yönetmek', 'gerenciar o transporte', 'kezelni a közlekedés', 'la gestion du transport', 'διαχείριση των μεταφορών', 'Transport verwalten', 'gestire i trasporti', 'การจัดการการขนส่ง', 'نقل و حمل کے انتظام', 'परिवहन का प्रबंधन', 'curo onerariis', 'mengelola transportasi', '輸送を管理', '교통 관리'),
(166, 'transport_list', 'transport list', 'পরিবহন তালিকা', 'Lista de transportes', 'قائمة النقل', 'lijst vervoer', 'лист транспорт', '运输名单', 'taşıma listesi', 'Lista de transportes', 'szállítás lista', 'liste de transport', 'Λίστα των μεταφορών', 'Transportliste', 'elenco trasporti', 'รายการการขนส่ง', 'نقل و حمل کی فہرست', 'परिवहन सूची', 'turpis album', 'daftar transport', '輸送一覧', '전송 목록'),
(167, 'add_transport', 'add transport', 'পরিবহন যোগ করুন', 'añadir el transporte', 'إضافة النقل', 'voeg vervoer', 'добавить транспорт', '加上运输', 'taşıma ekle', 'adicionar transporte', 'hozzá a közlekedés', 'ajouter transports', 'προσθέστε μεταφορών', 'add-Transport', 'aggiungere il trasporto', 'เพิ่มการขนส่ง', 'نقل و حمل شامل', 'परिवहन जोड़', 'adde onerariis', 'tambahkan transportasi', 'トランスポートを追加', '전송을 추가'),
(168, 'route_name', 'route name', 'রুট নাম', 'nombre de la ruta', 'اسم توجيه', 'naam van de route', 'Имя маршрут', '路由名称', 'rota ismi', 'nome da rota', 'útvonal nevét', 'nom de la route', 'Όνομα διαδρομής', 'Routennamen', 'nome del percorso', 'ชื่อเส้นทาง', 'راستے نام', 'मार्ग का नाम', 'iter nomine', 'Nama rute', 'ルートの名前', '경로 이름'),
(169, 'number_of_vehicle', 'number of vehicle', 'গাড়ীর সংখ্যা', 'número de vehículo', 'عدد من المركبات', 'aantal voertuigkilometers', 'количество автомобиля', '车辆的数量', 'Aracın sayısı', 'número de veículo', 'számú gépjármű', 'nombre de véhicules', 'αριθμός των οχημάτων', 'Anzahl der Fahrzeug', 'numero di veicolo', 'จำนวนของยานพาหนะ', 'گاڑی کی تعداد', 'वाहन की संख्या', 'de numero scilicet vehiculum', 'jumlah kendaraan', '車両の数', '차량의 수'),
(170, 'route_fare', 'route fare', 'রুট করবেন', 'ruta hacer', 'المسار تفعل', 'route doen', 'маршрут делать', '路线做', 'yol yapmak', 'rota fazer', 'útvonal do', 'itinéraire faire', 'διαδρομή κάνει', 'Route zu tun', 'r', 'เส้นทางทำ', 'راستے کرتے', 'मार्ग करना', 'iter faciunt,', 'rute lakukan', 'ルートか', '경로는 할'),
(171, 'edit_transport', 'edit transport', 'সম্পাদনা পরিবহন', 'transporte de edición', 'النقل تحرير', 'vervoer bewerken', 'править транспорт', '编辑运输', 'edit ulaşım', 'edição transporte', 'szerkesztés szállítás', 'transport modifier', 'edit μεταφορών', 'edit Transport', 'modifica dei trasporti', 'แก้ไขการขนส่ง', 'ترمیم نقل و حمل', 'संपादित परिवहन', 'edit onerariis', 'mengedit transportasi', '編集輸送', '편집 전송'),
(172, 'manage_dormitory', 'manage dormitory', 'আস্তানা ও পরিচালনা', 'gestionar dormitorio', 'إدارة مهجع', 'beheren slaapzaal', 'управлять общежитие', '宿舍管理', 'yurt yönetmek', 'gerenciar dormitório', 'kezelni kollégiumi', 'gérer dortoir', 'διαχείριση κοιτώνα', 'Schlafsaal verwalten', 'gestione dormitorio', 'จัดการหอพัก', 'شیناگار کا انتظام', 'छात्रावास का प्रबंधन', 'curo dormitorio', 'mengelola asrama', '寮を管理', '기숙사를 관리'),
(173, 'dormitory_list', 'dormitory list', 'আস্তানা তালিকা', 'lista dormitorio', 'قائمة مهجع', 'slaapzaal lijst', 'Список общежитие', '宿舍名单', 'yurt listesi', 'lista dormitório', 'kollégiumi lista', 'liste de dortoir', 'Λίστα κοιτώνα', 'Schlafsaal Liste', 'elenco dormitorio', 'รายชื่อหอพัก', 'شیناگار فہرست', 'छात्रावास सूची', 'dormitorium album', 'daftar asrama', '寮のリスト', '기숙사 목록'),
(174, 'add_dormitory', 'add dormitory', 'আস্তানা যোগ', 'añadir dormitorio', 'إضافة مهجع', 'voeg slaapzaal', 'добавить общежитие', '添加宿舍', 'yurt ekle', 'adicionar dormitório', 'hozzá kollégiumi', 'ajouter dortoir', 'προσθήκη κοιτώνα', 'Schlaf hinzufügen', 'aggiungere dormitorio', 'เพิ่มหอพัก', 'شیناگار شامل', 'छात्रावास जोड़', 'adde dormitorio', 'menambahkan asrama', '寮を追加', '기숙사를 추가'),
(175, 'dormitory_name', 'dormitory name', 'আস্তানা নাম', 'Nombre del dormitorio', 'اسم المهجع', 'slaapzaal naam', 'Имя общежитие', '宿舍名', 'yurt adı', 'nome dormitório', 'kollégiumi név', 'nom de dortoir', 'Όνομα κοιτώνα', 'Schlaf Namen', 'Nome dormitorio', 'ชื่อหอพัก', 'شیناگار نام', 'छात्रावास नाम', 'dormitorium nomine', 'Nama asrama', '寮名', '기숙사 이름'),
(176, 'number_of_room', 'number of room', 'ঘরের সংখ্যা', 'número de habitación', 'عدد الغرف', 'aantal kamer', 'число комнате', '房间数量', 'oda sayısı', 'número de quarto', 'száma szobában', 'nombre de salle', 'τον αριθμό των δωματίων', 'Anzahl der Zimmer', 'numero delle camera', 'จำนวนห้องพัก', 'کمرے کی تعداد', 'कमरे की संख्या', 'numerus locus', 'Jumlah kamar', 'お部屋数', '객실 수'),
(177, 'manage_noticeboard', 'manage noticeboard', 'নোটিশবোর্ড পরিচালনা', 'gestionar tablón de anuncios', 'إدارة اللافتة', 'beheren prikbord', 'управлять доске объявлений', '管理布告', 'Noticeboard yönetmek', 'gerenciar avisos', 'kezelni üzenőfalán', 'gérer panneau d\'affichage', 'διαχείριση ανακοινώσεων', 'Brett verwalten', 'gestione bacheca', 'จัดการป้ายประกาศ', 'noticeboard کا انتظام', 'Noticeboard का प्रबंधन', 'curo noticeboard', 'mengelola pengumuman', '伝言板を管理', '의 noticeboard 관리'),
(178, 'noticeboard_list', 'noticeboard list', 'নোটিশবোর্ড তালিকা', 'tablón de anuncios de la lista', 'قائمة اللافتة', 'prikbord lijst', 'Список доска для объявлений', '布告名单', 'noticeboard listesi', 'lista de avisos', 'üzenőfalán lista', 'liste de panneau d\'affichage', 'λίστα ανακοινώσεων', 'Brett-Liste', 'elenco bacheca', 'รายการป้ายประกาศ', 'noticeboard فہرست', 'Noticeboard सूची', 'noticeboard album', 'daftar pengumuman', '伝言板一覧', '의 noticeboard 목록'),
(179, 'add_noticeboard', 'add noticeboard', 'নোটিশবোর্ড যোগ', 'añadir tablón de anuncios', 'إضافة اللافتة', 'voeg prikbord', 'добавить доске объявлений', '添加布告', 'Noticeboard ekle', 'adicionar avisos', 'hozzá üzenőfalán', 'ajouter panneau d\'affichage', 'προσθήκη ανακοινώσεων', 'Brett hinzufügen', 'aggiungere bacheca', 'เพิ่มป้ายประกาศ', 'noticeboard شامل', 'Noticeboard जोड़', 'adde noticeboard', 'menambahkan pengumuman', '伝言板を追加', '의 noticeboard 추가'),
(180, 'notice', 'notice', 'বিজ্ঞপ্তি', 'aviso', 'إشعار', 'kennisgeving', 'уведомление', '通知', 'uyarı', 'aviso', 'értesítés', 'délai', 'ειδοποίηση', 'Bekanntmachung', 'avviso', 'แจ้งให้ทราบ', 'نوٹس', 'नोटिस', 'Observa', 'pemberitahuan', '予告', '통지'),
(181, 'add_notice', 'add notice', 'নোটিশ যোগ করুন', 'añadir aviso', 'إضافة إشعار', 'voeg bericht', 'добавить уведомление', '添加通知', 'haber ekle', 'adicionar aviso', 'hozzá értesítés', 'ajouter un avis', 'προσθέστε ανακοίνωση', 'Hinweis hinzufügen', 'aggiungere preavviso', 'เพิ่มแจ้งให้ทราบล่วงหน้า', 'نوٹس کا اضافہ کریں', 'नोटिस जोड़', 'addunt et titulum', 'tambahkan pemberitahuan', '通知を追加', '통지를 추가'),
(182, 'edit_noticeboard', 'edit noticeboard', 'সম্পাদনা নোটিশবোর্ড', 'edit tablón de anuncios', 'تحرير اللافتة', 'bewerk prikbord', 'править доска для объявлений', '编辑布告', 'edit noticeboard', 'edição de avisos', 'szerkesztés üzenőfalán', 'modifier panneau d\'affichage', 'edit ανακοινώσεων', 'Brett bearbeiten', 'modifica bacheca', 'แก้ไขป้ายประกาศ', 'میں ترمیم کریں noticeboard', 'संपादित Noticeboard', 'edit noticeboard', 'mengedit pengumuman', '編集伝言板', '편집의 noticeboard'),
(183, 'system_name', 'system name', 'সিস্টেমের নাম', 'Nombre del sistema', 'اسم النظام', 'Name System', 'Имя системы', '系统名称', 'sistemi adı', 'nome do sistema', 'rendszer neve', 'nom du système', 'όνομα του συστήματος', 'Systemnamen', 'nome del sistema', 'ชื่อระบบ', 'نظام کا نام', 'सिस्टम नाम', 'ratio nominis', 'Nama sistem', 'システム名', '시스템 이름'),
(184, 'save', 'save', 'রক্ষা', 'guardar', 'حفظ', 'besparen', 'экономить', '节省', 'kurtarmak', 'salvar', 'kivéve', 'sauver', 'εκτός', 'sparen', 'salvare', 'ประหยัด', 'کو بچانے کے', 'बचाना', 'salvum', 'menyimpan', '保存', '저장'),
(185, 'system_title', 'system title', 'সিস্টেম শিরোনাম', 'Título de sistema', 'عنوان النظام', 'systeem titel', 'Название системы', '系统标题', 'Sistem başlık', 'título sistema', 'rendszer cím', 'titre du système', 'Τίτλος του συστήματος', 'System-Titel', 'titolo di sistema', 'ชื่อระบบ', 'نظام عنوان', 'सिस्टम शीर्षक', 'ratio title', 'title sistem', 'システムのタイトル', '시스템 제목'),
(186, 'paypal_email', 'paypal email', 'PayPal ইমেইল', 'paypal email', 'باي بال البريد الإلكتروني', 'paypal e-mail', 'PayPal по электронной почте', 'PayPal电子邮件', 'paypal e-posta', 'paypal e-mail', 'paypal email', 'paypal email', 'paypal ηλεκτρονικό ταχυδρομείο', 'paypal E-Mail', 'paypal-mail', 'paypal อีเมล์', 'پے پال ای میل', 'पेपैल ईमेल', 'Paypal email', 'email paypal', 'Paypalのメール', '페이팔 이메일'),
(187, 'currency', 'currency', 'মুদ্রা', 'moneda', 'عملة', 'valuta', 'валюта', '货币', 'para', 'moeda', 'valuta', 'monnaie', 'νόμισμα', 'Währung', 'valuta', 'เงินตรา', 'کرنسی', 'मुद्रा', 'currency', 'mata uang', '通貨', '통화'),
(188, 'phrase_list', 'phrase list', 'ফ্রেজ তালিকা', 'lista de frases', 'قائمة جملة', 'zinnenlijst', 'Список фраза', '短语列表', 'ifade listesi', 'lista de frases', 'kifejezés lista', 'liste de phrase', 'Λίστα φράση', 'Phrasenliste', 'elenco frasi', 'รายการวลี', 'جملہ فہرست', 'वाक्यांश सूची', 'dicitur album', 'Daftar frase', 'フレーズリスト', '문구 목록'),
(189, 'add_phrase', 'add phrase', 'শব্দগুচ্ছ যুক্ত', 'añadir la frase', 'إضافة عبارة', 'voeg zin', 'добавить фразу', '添加词组', 'ifade eklemek', 'adicionar frase', 'adjunk kifejezést', 'ajouter la phrase', 'προσθέστε φράση', 'Begriff hinzufügen', 'aggiungere la frase', 'เพิ่มวลี', 'جملہ شامل', 'वाक्यांश जोड़ना', 'addere phrase', 'menambahkan frase', 'フレーズを追加', '문구를 추가'),
(190, 'add_language', 'add language', 'ভাষা যুক্ত', 'añadir idioma', 'إضافة لغة', 'add taal', 'добавить язык', '新增语言', 'dil ekle', 'adicionar língua', 'nyelv hozzáadása', 'ajouter la langue', 'προσθέστε γλώσσα', 'Sprache hinzufügen', 'aggiungere la lingua', 'เพิ่มภาษา', 'زبان کو شامل', 'भाषा जोड़ना', 'addere verbis', 'menambahkan bahasa', '言語を追加', '언어를 추가'),
(191, 'phrase', 'phrase', 'বাক্য', 'frase', 'العبارة', 'frase', 'фраза', '短语', 'ifade', 'frase', 'kifejezés', 'phrase', 'φράση', 'Ausdruck', 'frase', 'วลี', 'جملہ', 'वाक्यांश', 'phrase', 'frasa', 'フレーズ', '구'),
(192, 'manage_backup_restore', 'manage backup restore', 'ব্যাকআপ পুনঃস্থাপন ও পরিচালনা', 'gestionar copias de seguridad a restaurar', 'إدارة استعادة النسخ الاحتياطي', 'beheer van back-up herstellen', 'управлять восстановить резервного копирования', '管理备份恢复', 'yedekleme geri yönetmek', 'gerenciar o backup de restauração', 'kezelni a biztonsági mentés visszaállítása', 'gérer de restauration de sauvegarde', 'διαχείριση επαναφοράς αντιγράφων ασφαλείας', 'verwalten Backup wiederherstellen', 'gestire il ripristino di backup', 'จัดการการสำรองข้อมูลเรียกคืน', 'بیک اپ بحال انتظام', 'बैकअप बहाल का प्रबंधन', 'curo tergum restituunt', 'mengelola backup restore', 'バックアップ、リストアを管理', '백업 복원 관리'),
(193, 'restore', 'restore', 'প্রত্যর্পণ করা', 'restaurar', 'استعادة', 'herstellen', 'восстановление', '恢复', 'geri', 'restaurar', 'visszaad', 'restaurer', 'επαναφέρετε', 'wiederherstellen', 'ripristinare', 'ฟื้นฟู', 'بحال', 'बहाल', 'reddite', 'mengembalikan', '復元する', '복원'),
(194, 'mark', 'mark', 'ছাপ', 'marca', 'علامة', 'mark', 'знак', '标志', 'işaret', 'marca', 'jel', 'marque', 'σημάδι', 'Marke', 'marchio', 'เครื่องหมาย', 'نشان', 'निशान', 'Marcus', 'tanda', 'マーク', '표'),
(195, 'grade', 'grade', 'গ্রেড', 'grado', 'درجة', 'graad', 'класс', '等级', 'sınıf', 'grau', 'fokozat', 'grade', 'βαθμός', 'Klasse', 'grado', 'เกรด', 'گریڈ', 'ग्रेड', 'gradus,', 'kelas', 'グレード', '학년'),
(196, 'invoice', 'invoice', 'চালান', 'factura', 'فاتورة', 'factuur', 'счет-фактура', '发票', 'fatura', 'fatura', 'számla', 'facture', 'τιμολόγιο', 'Rechnung', 'fattura', 'ใบกำกับสินค้า', 'انوائس', 'बीजक', 'cautionem', 'faktur', 'インボイス', '송장'),
(197, 'book', 'book', 'বই', 'libro', 'كتاب', 'boek', 'книга', '书', 'kitap', 'livro', 'könyv', 'livre', 'βιβλίο', 'Buch', 'libro', 'หนังสือ', 'کتاب', 'किताब', 'Liber', 'buku', '本', '책'),
(198, 'all', 'all', 'সব', 'todo', 'كل', 'alle', 'все', '所有', 'tüm', 'tudo', 'minden', 'tout', 'όλα', 'alle', 'tutto', 'ทั้งหมด', 'تمام', 'सब', 'omnes', 'semua', 'すべて', '모든'),
(199, 'upload_&_restore_from_backup', 'upload & restore from backup', 'আপলোড &amp; ব্যাকআপ থেকে পুনঃস্থাপন', 'cargar y restaurar copia de seguridad', 'تحميل واستعادة من النسخة الاحتياطية', 'uploaden en terugzetten van een backup', 'загрузить и восстановить из резервной копии', '上传及从备份中恢复', 'yükleyebilir ve yedekten geri yükleme', 'fazer upload e restauração de backup', 'feltölteni és visszaállítani backup', 'télécharger et restauration de la sauvegarde', 'ανεβάσετε και επαναφορά από backup', 'Upload &amp; Wiederherstellung von Backups', 'caricare e ripristinare dal backup', 'อัปโหลดและเรียกคืนจากการสำรองข้อมูล', 'اپ لوڈ کریں اور بیک اپ سے بحال', 'अपलोड और बैकअप से बहाल', 'restituo ex tergum upload,', 'meng-upload &amp; restore dari backup', 'アップロード＆バックアップから復元', '업로드 및 백업에서 복원'),
(200, 'manage_profile', 'manage profile', 'প্রফাইলটি পরিচালনা', 'gestionar el perfil', 'إدارة الملف الشخصي', 'te beheren!', 'управлять профилем', '管理配置文件', 'profilini yönetmek', 'gerenciar o perfil', 'Profil kezelése', 'gérer le profil', 'διαχειριστείτε το προφίλ', 'Profil verwalten', 'gestire il profilo', 'จัดการรายละเอียด', 'پروفائل کا نظم کریں', 'प्रोफाइल का प्रबंधन', 'curo profile', 'mengelola profil', 'プロファイル（個人情報）の管理', '프로필 (내 정보) 관리'),
(201, 'update_profile', 'update profile', 'প্রোফাইল আপডেট', 'actualizar el perfil', 'تحديث الملف الشخصي', 'Profiel bijwerken', 'обновить профиль', '更新个人资料', 'profilinizi güncelleyin', 'atualizar o perfil', 'frissíteni profil', 'mettre à jour le profil', 'ενημερώσετε το προφίλ', 'Profil aktualisieren', 'aggiornare il profilo', 'อัปเดตโปรไฟล์', 'پروفائل کو اپ ڈیٹ', 'प्रोफ़ाइल अपडेट', 'magna eget ipsum', 'memperbarui profil', 'プロファイルを更新', '프로필을 업데이트'),
(202, 'new_password', 'new password', 'নতুন পাসওয়ার্ড', 'nueva contraseña', 'كلمة مرور جديدة', 'nieuw wachtwoord', 'новый пароль', '新密码', 'Yeni şifre', 'nova senha', 'Új jelszó', 'nouveau mot de passe', 'νέο κωδικό', 'Neues Passwort', 'nuova password', 'รหัสผ่านใหม่', 'نیا پاس ورڈ', 'नया पासवर्ड', 'novum password', 'kata sandi baru', '新しいパスワード', '새 암호'),
(203, 'confirm_new_password', 'confirm new password', 'নতুন পাসওয়ার্ড নিশ্চিত করুন', 'confirmar nueva contraseña', 'تأكيد كلمة المرور الجديدة', 'Bevestig nieuw wachtwoord', 'подтвердить новый пароль', '确认新密码', 'yeni parolayı onaylayın', 'confirmar nova senha', 'erősítse meg az új jelszót', 'confirmer le nouveau mot de passe', 'επιβεβαιώσετε το νέο κωδικό', 'Bestätigen eines neuen Kennwortes', 'conferma la nuova password', 'ยืนยันรหัสผ่านใหม่', 'نئے پاس ورڈ کی توثیق', 'नए पासवर्ड की पुष्टि', 'confirma novum password', 'konfirmasi password baru', '新しいパスワードを確認', '새 암호를 확인합니다'),
(204, 'update_password', 'update password', 'পাসওয়ার্ড আপডেট', 'actualizar la contraseña', 'تحديث كلمة السر', 'updaten wachtwoord', 'обновить пароль', '更新密码', 'Parolanızı güncellemek', 'atualizar senha', 'frissíti jelszó', 'mettre à jour le mot de passe', 'ενημερώσετε τον κωδικό πρόσβασης', 'Kennwort aktualisieren', 'aggiornare la password', 'ปรับปรุงรหัสผ่าน', 'پاس اپ ڈیٹ', 'पासवर्ड अद्यतन', 'scelerisque eget', 'memperbarui sandi', 'パスワードを更新', '암호를 업데이트'),
(205, 'teacher_dashboard', 'teacher dashboard', 'শিক্ষক ড্যাশবোর্ড', 'tablero maestro', 'لوحة أجهزة القياس المعلم', 'leraar dashboard', 'учитель приборной панели', '老师仪表板', 'öğretmen pano', 'dashboard professor', 'tanár műszerfal', 'enseignant tableau de bord', 'ταμπλό των εκπαιδευτικών', 'Lehrer-Dashboard', 'dashboard insegnante', 'กระดานครู', 'استاد ڈیش بورڈ', 'शिक्षक डैशबोर्ड', 'magister Dashboard', 'dashboard guru', '教師のダッシュボード', '교사 대시 보드'),
(206, 'backup_restore_help', 'backup restore help', 'ব্যাকআপ পুনঃস্থাপন সাহায্য', 'copia de seguridad restaurar ayuda', 'استعادة النسخ الاحتياطي المساعدة', 'backup helpen herstellen', 'восстановить резервную копию помощь', '备份恢复的帮助', 'yedekleme yardım geri', 'de backup restaurar ajuda', 'Backup Restore segítségével', 'restauration de sauvegarde de l\'aide', 'επαναφοράς αντιγράφων ασφαλείας βοήθεια', 'Backup wiederherstellen Hilfe', 'Backup Restore aiuto', 'การสำรองข้อมูลเรียกคืนความช่วยเหลือ', 'بیک اپ کی مدد بحال', 'बैकअप मदद बहाल', 'auxilium tergum restituunt', 'backup restore bantuan', 'バックアップヘルプを復元', '백업 도움을 복원'),
(207, 'student_dashboard', 'student dashboard', 'ছাত্র ড্যাশবোর্ড', 'salpicadero estudiante', 'لوحة القيادة الطلابية', 'student dashboard', 'студент приборной панели', '学生的仪表板', 'Öğrenci paneli', 'dashboard estudante', 'tanuló műszerfal', 'tableau de bord de l\'élève', 'ταμπλό των φοιτητών', 'Schüler Armaturenbrett', 'studente dashboard', 'แผงควบคุมนักเรียน', 'طالب علم کے ڈیش بورڈ', 'छात्र डैशबोर्ड', 'Discipulus Dashboard', 'dashboard mahasiswa', '学生のダッシュボード', '학생 대시 보드'),
(208, 'parent_dashboard', 'parent dashboard', 'অভিভাবক ড্যাশবোর্ড', 'salpicadero padres', 'لوحة أجهزة القياس الأم', 'ouder dashboard', 'родитель приборной панели', '家长仪表板', 'ebeveyn kontrol paneli', 'dashboard pai', 'szülő műszerfal', 'parent tableau de bord', 'μητρική ταμπλό', 'Mutter Armaturenbrett', 'dashboard genitore', 'แผงควบคุมของผู้ปกครอง', 'والدین کے ڈیش بورڈ', 'माता - पिता डैशबोर्ड', 'Dashboard parent', 'orangtua dashboard', '親ダッシュ', '부모 대시 보드'),
(209, 'view_marks', 'view marks', 'দেখুন চিহ্ন', 'Vista marcas', 'علامات رأي', 'view merken', 'вид знаки', '鉴于商标', 'görünümü işaretleri', 'vista marcas', 'view jelek', 'Vue marques', 'σήματα άποψη', 'Ansicht Marken', 'Vista marchi', 'เครื่องหมายมุมมอง', 'دیکھیں نشانات', 'देखने के निशान', 'propter signa', 'lihat tanda', 'ビューマーク', '보기 마크'),
(210, 'delete_language', 'delete language', 'ভাষা মুছতে', 'eliminar el lenguaje', 'حذف اللغة', 'verwijderen taal', 'удалить язык', '删除语言', 'dili silme', 'excluir língua', 'törlése nyelv', 'supprimer la langue', 'διαγραφή γλώσσα', 'Sprache löschen', 'eliminare lingua', 'ลบภาษา', 'زبان کو خارج کر دیں', 'भाषा को हटाना', 'linguam turpis', 'menghapus bahasa', '言語を削除する', '언어를 삭제'),
(211, 'settings_updated', 'settings updated', 'সেটিংস আপডেট', 'configuración actualizado', 'الإعدادات المحدثة', 'instellingen bijgewerkt', 'Настройки обновлены', '设置更新', 'ayarları güncellendi', 'definições atualizadas', 'beállítások frissítve', 'paramètres mis à jour', 'Ρυθμίσεις ενημέρωση', 'Einstellungen aktualisiert', 'impostazioni aggiornate', 'การตั้งค่าการปรับปรุง', 'ترتیبات کی تازہ کاری', 'सेटिंग्स अद्यतन', 'venenatis eu', 'pengaturan diperbarui', '設定が更新', '설정이 업데이트'),
(212, 'update_phrase', 'update phrase', 'আপডেট ফ্রেজ', 'actualización de la frase', 'تحديث العبارة', 'Update zin', 'обновление фраза', '更新短语', 'güncelleme ifade', 'atualização frase', 'frissítést kifejezés', 'mise à jour phrase', 'ενημέρωση φράση', 'Update Begriff', 'aggiornamento frase', 'ปรับปรุงวลี', 'اپ ڈیٹ جملہ', 'अद्यतन वाक्यांश', 'eget dictum', 'frase pembaruan', '更新フレーズ', '업데이트 구문'),
(213, 'login_failed', 'login failed', 'লগইন ব্যর্থ হয়েছে', 'Error de acceso', 'فشل تسجيل الدخول', 'inloggen is mislukt', 'Ошибка входа', '登录失败', 'giriş başarısız oldu', 'Falha no login', 'bejelentkezés sikertelen', 'Échec de la connexion', 'Είσοδος απέτυχε', 'Fehler bei der Anmeldung', 'Accesso non riuscito', 'เข้าสู่ระบบล้มเหลว', 'لاگ ان ناکام', 'लॉगिन विफल', 'tincidunt defecit', 'Login gagal', 'ログインに失敗しました', '로그인 실패'),
(214, 'live_chat', 'live chat', 'লাইভ চ্যাট', 'chat en vivo', 'الدردشة الحية', 'live chat', 'Онлайн-чат', '即时聊天', 'canlı sohbet', 'chat ao vivo', 'élő chat', 'chat en direct', 'live chat', 'Live-Chat', 'live chat', 'อยู่สนทนา', 'لائیو چیٹ', 'लाइव चैट', 'Vivamus nibh', 'live chat', 'ライブチャット', '라이브 채팅'),
(215, 'client 1', 'client 1', 'ক্লায়েন্টের 1', 'cliente 1', 'العميل 1', 'client 1', 'Клиент 1', '客户端1', 'istemcisi 1', 'cliente 1', 'ügyfél 1', 'client 1', 'πελάτη 1', 'Client 1', 'client 1', 'ลูกค้า 1', 'کلائنٹ 1', 'ग्राहक 1', 'I huius', 'client 1', 'クライアント1', '클라이언트 1'),
(216, 'buyer', 'buyer', 'ক্রেতা', 'comprador', 'مشتر', 'koper', 'покупатель', '买方', 'alıcı', 'comprador', 'vevő', 'acheteur', 'αγοραστής', 'Käufer', 'compratore', 'ผู้ซื้อ', 'خریدار', 'खरीददार', 'qui emit,', 'pembeli', 'バイヤー', '구매자'),
(217, 'purchase_code', 'purchase code', 'ক্রয় কোড', 'código de compra', 'كود الشراء', 'aankoop code', 'покупка код', '申购代码', 'satın alma kodu', 'código de compra', 'vásárlási kódot', 'code d\'achat', 'Κωδικός αγορά', 'Kauf-Code', 'codice di acquisto', 'รหัสการสั่งซื้อ', 'خریداری کے کوڈ', 'खरीद कोड', 'Mauris euismod', 'kode pembelian', '購入コード', '구매 코드'),
(218, 'system_email', 'system email', 'সিস্টেম ইমেইল', 'correo electrónico del sistema', 'نظام البريد الإلكتروني', 'systeem e-mail', 'система электронной почты', '邮件系统', 'sistem e-posta', 'e-mail do sistema', 'a rendszer az e-mail', 'email de système', 'e-mail συστήματος', 'E-Mail-System', 'email sistema', 'อีเมล์ระบบ', 'نظام کی ای میل', 'प्रणाली ईमेल', 'Praesent sit amet', 'email sistem', 'システムの電子メール', '시스템 전자 메일'),
(219, 'option', 'option', 'বিকল্প', 'opción', 'خيار', 'optie', 'вариант', '选项', 'seçenek', 'opção', 'opció', 'option', 'επιλογή', 'Option', 'opzione', 'ตัวเลือกที่', 'آپشن', 'विकल्प', 'optio', 'pilihan', 'オプション', '선택권'),
(220, 'edit_phrase', 'edit phrase', 'সম্পাদনা ফ্রেজ', 'edit frase', 'تحرير العبارة', 'bewerk zin', 'править фраза', '编辑语', 'edit ifade', 'edição frase', 'szerkesztés kifejezés', 'modifier phrase', 'edit φράση', 'edit Begriff', 'modifica frase', 'แก้ไขวลี', 'ترمیم کے جملہ', 'संपादित वाक्यांश', 'edit phrase', 'mengedit frase', '編集フレーズ', '편집 구'),
(221, 'forgot_your_password', 'Forgot Your Password', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(222, 'forgot_password', 'Forgot Password', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(223, 'back_to_login', 'Back To Login', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(224, 'return_to_login_page', 'Return to Login Page', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(225, 'admit_student', 'Admit Student', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(226, 'admit_bulk_student', 'Admit Bulk Student', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(227, 'student_information', 'Student Information', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(228, 'student_marksheet', 'Student Mark Sheet', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(229, 'daily_attendance', 'Daily Attendance', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(230, 'exam_grades', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(231, 'message', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(232, 'general_settings', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(233, 'language_settings', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(234, 'edit_profile', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(235, 'event_schedule', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(236, 'cancel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(237, 'addmission_form', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(238, 'value_required', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(239, 'select', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(240, 'gender', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(241, 'add_bulk_student', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(242, 'student_bulk_add_form', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(243, 'select_excel_file', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(244, 'upload_and_import', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(245, 'manage_classes', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(246, 'manage_sections', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(247, 'add_new_teacher', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(248, 'section_name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(249, 'nick_name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(250, 'add_new_section', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(251, 'add_section', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(252, 'update', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(253, 'section', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(254, 'select_class_first', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(255, 'parent_information', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(256, 'relation', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(257, 'add_form', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(258, 'all_parents', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(259, 'parents', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(260, 'add_new_parent', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(261, 'add_new_student', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(262, 'all_students', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(263, 'view_marksheet', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(264, 'text_align', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(265, 'clickatell_username', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(266, 'clickatell_password', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(267, 'clickatell_api_id', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(268, 'sms_settings', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(269, 'data_updated', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(270, 'data_added_successfully', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(271, 'edit_notice', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(272, 'private_messaging', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(273, 'messages', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(274, 'new_message', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(275, 'write_new_message', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(276, 'recipient', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(277, 'select_a_user', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(278, 'write_your_message', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(279, 'send', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(280, 'current_password', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(281, 'exam_marks', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(282, 'marks_obtained', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(283, 'total_marks', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(284, 'comments', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(285, 'theme_settings', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(286, 'select_theme', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(287, 'theme_selected', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(288, 'language_list', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(289, 'payment_cancelled', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(290, 'study_material', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(291, 'download', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(292, 'select_a_theme_to_make_changes', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(293, 'manage_daily_attendance', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(294, 'select_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(295, 'select_month', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(296, 'select_year', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(297, 'manage_attendance', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(298, 'twilio_account', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(299, 'authentication_token', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(300, 'registered_phone_number', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(301, 'select_a_service', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(302, 'active', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(303, 'disable_sms_service', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(304, 'not_selected', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(305, 'disabled', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(306, 'present', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(307, 'absent', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(308, 'accounting', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(309, 'income', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(310, 'expense', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(311, 'incomes', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(312, 'invoice_informations', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(313, 'payment_informations', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(314, 'total', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(315, 'enter_total_amount', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(316, 'enter_payment_amount', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(317, 'payment_status', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(318, 'method', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(319, 'cash', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(320, 'check', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(321, 'card', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(322, 'data_deleted', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(323, 'total_amount', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(324, 'take_payment', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(325, 'payment_history', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(326, 'amount_paid', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(327, 'due', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(328, 'payment_successfull', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(329, 'creation_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(330, 'invoice_entries', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(331, 'paid_amount', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(332, 'send_sms_to_all', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(333, 'yes', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(334, 'no', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(335, 'activated', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(336, 'sms_service_not_activated', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(337, 'add_study_material', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(338, 'file', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(339, 'file_type', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(340, 'select_file_type', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(341, 'image', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(342, 'doc', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(343, 'pdf', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(344, 'excel', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(345, 'other', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(346, 'expenses', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(347, 'add_new_expense', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(348, 'add_expense', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(349, 'edit_expense', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(350, 'total_mark', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(351, 'send_marks_by_sms', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(352, 'send_marks', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(353, 'select_receiver', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(354, 'students', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(355, 'marks_of', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(356, 'for', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(357, 'message_sent', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(358, 'expense_category', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(359, 'add_new_expense_category', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(360, 'add_expense_category', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(361, 'category', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(362, 'select_expense_category', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(363, 'message_sent!', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(364, 'reply_message', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(365, 'account_updated', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(366, 'upload_logo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(367, 'upload', 'Upload', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(368, 'study_material_info_saved_successfuly', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(369, 'edit_study_material', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(370, 'default_theme', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(371, 'default', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(372, 'acd_session', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(373, 'academic_session', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(374, 'online_admission', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(375, 'session_list', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(376, 'add_session', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(377, 'is_open', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(378, 'active_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(379, 'edit_session', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(380, 'student_application_list', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(381, 'add_student_application', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(382, 'pay_status', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(383, 'from_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(384, 'to_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(385, 'start_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(386, 'end_date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(387, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(388, 'name_bangla', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(389, 'name_english', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(390, 'fridom_fighter_son', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(391, 'gardian_name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(392, 'upojati', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(393, 'FFS', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(394, 'fridom_fighter', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(395, 'Nationality', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(396, 'islam', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(397, 'hindu', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(398, 'present_address', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(399, 'Parmanent_address', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(400, 'Current_address', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(401, 'technology', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(402, 'textile', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(403, 'electrical', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(404, 'Academic_details', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(405, 'SSC(General)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(406, 'SSC(Vocational)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(407, 'Trade(Two-Years)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(408, 'Dakhil(Vocational)', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(409, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(410, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(411, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(412, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(413, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(414, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(415, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(416, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(417, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(418, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(419, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(420, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(421, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(422, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(423, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(424, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(425, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(426, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(427, 'Actions', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(428, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(429, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(430, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
INSERT INTO `language` (`phrase_id`, `phrase`, `english`, `bengali`, `spanish`, `arabic`, `dutch`, `russian`, `chinese`, `turkish`, `portuguese`, `hungarian`, `french`, `greek`, `german`, `italian`, `thai`, `urdu`, `hindi`, `latin`, `indonesian`, `japanese`, `korean`) VALUES
(431, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(432, 'Apply_Date', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(433, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(434, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(435, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(436, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(437, 'Cell_No', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(438, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(439, 'Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(440, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(441, 'Full_Name', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(442, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(443, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(444, 'Report', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(445, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(446, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(447, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(448, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(449, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(450, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(451, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(452, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(453, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(454, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(455, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(456, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(457, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(458, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(459, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(460, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(461, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(462, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(463, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(464, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(465, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(466, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(467, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(468, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(469, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(470, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(471, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(472, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(473, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(474, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(475, 'Photo', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(476, 'on', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `mark`
--

CREATE TABLE `mark` (
  `mark_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `mark_obtained` int(11) NOT NULL DEFAULT 0,
  `mark_total` int(11) NOT NULL DEFAULT 100,
  `comment` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mark`
--

INSERT INTO `mark` (`mark_id`, `student_id`, `subject_id`, `class_id`, `exam_id`, `mark_obtained`, `mark_total`, `comment`) VALUES
(1, 3, 7, 5, 1, 55, 100, 'Need to improve'),
(2, 4, 7, 5, 1, 71, 100, 'Good, keep it up!'),
(3, 5, 7, 5, 1, 45, 100, 'Need to focus more, just passed!'),
(4, 6, 7, 5, 1, 80, 100, 'Well done!'),
(5, 7, 7, 5, 1, 50, 100, 'Need to improve'),
(6, 8, 7, 5, 1, 76, 100, 'Good, keep it up!'),
(7, 9, 7, 5, 1, 79, 100, 'Good, keep it up!'),
(8, 3, 8, 5, 1, 68, 100, 'none'),
(9, 4, 8, 5, 1, 40, 100, 'none'),
(10, 5, 8, 5, 1, 22, 100, 'none'),
(11, 6, 8, 5, 1, 0, 100, ''),
(12, 7, 8, 5, 1, 0, 100, ''),
(13, 8, 8, 5, 1, 0, 100, ''),
(14, 9, 8, 5, 1, 0, 100, ''),
(15, 13, 8, 5, 1, 0, 100, ''),
(16, 3, 9, 5, 1, 79, 100, 'none'),
(17, 4, 9, 5, 1, 0, 100, ''),
(18, 5, 9, 5, 1, 0, 100, ''),
(19, 6, 9, 5, 1, 0, 100, ''),
(20, 7, 9, 5, 1, 0, 100, ''),
(21, 8, 9, 5, 1, 0, 100, ''),
(22, 9, 9, 5, 1, 0, 100, ''),
(23, 13, 9, 5, 1, 0, 100, ''),
(24, 3, 10, 5, 1, 70, 100, 'none'),
(25, 4, 10, 5, 1, 0, 100, ''),
(26, 5, 10, 5, 1, 0, 100, ''),
(27, 6, 10, 5, 1, 0, 100, ''),
(28, 7, 10, 5, 1, 0, 100, ''),
(29, 8, 10, 5, 1, 0, 100, ''),
(30, 9, 10, 5, 1, 0, 100, ''),
(31, 13, 10, 5, 1, 0, 100, ''),
(32, 3, 11, 5, 1, 0, 100, ''),
(33, 4, 11, 5, 1, 0, 100, ''),
(34, 5, 11, 5, 1, 0, 100, ''),
(35, 6, 11, 5, 1, 0, 100, ''),
(36, 7, 11, 5, 1, 0, 100, ''),
(37, 8, 11, 5, 1, 0, 100, ''),
(38, 9, 11, 5, 1, 0, 100, ''),
(39, 13, 11, 5, 1, 0, 100, ''),
(40, 13, 7, 5, 1, 0, 100, ''),
(41, 3, 9, 5, 2, 0, 100, ''),
(42, 4, 9, 5, 2, 0, 100, ''),
(43, 5, 9, 5, 2, 0, 100, ''),
(44, 6, 9, 5, 2, 0, 100, ''),
(45, 7, 9, 5, 2, 0, 100, ''),
(46, 8, 9, 5, 2, 0, 100, ''),
(47, 9, 9, 5, 2, 0, 100, ''),
(48, 13, 9, 5, 2, 0, 100, ''),
(49, 10, 6, 4, 2, 0, 100, ''),
(50, 11, 6, 4, 2, 0, 100, ''),
(51, 14, 6, 4, 2, 0, 100, '');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `message_thread_code` longtext NOT NULL,
  `message` longtext NOT NULL,
  `sender` longtext NOT NULL,
  `timestamp` longtext NOT NULL,
  `read_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 unread 1 read'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `message_thread_code`, `message`, `sender`, `timestamp`, `read_status`) VALUES
(3, 'eb7157e52314645', 'This is a test message', 'admin-1', '1647099867', 1),
(2, '771e4077025c3f1', 'Hi There', 'admin-1', '1449767732', 0),
(4, 'eb7157e52314645', 'This is a test reply from Eleanor.', 'student-2', '1647099931', 1),
(5, '7053f3d0570a2d9', 'Dear student, this is to inform you that this is just a demo text message from the admin.', 'admin-1', '1647193784', 1),
(6, '7053f3d0570a2d9', 'demo reply', 'student-3', '1647193990', 0);

-- --------------------------------------------------------

--
-- Table structure for table `message_thread`
--

CREATE TABLE `message_thread` (
  `message_thread_id` int(11) NOT NULL,
  `message_thread_code` longtext NOT NULL,
  `sender` longtext NOT NULL,
  `reciever` longtext NOT NULL,
  `last_message_timestamp` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `message_thread`
--

INSERT INTO `message_thread` (`message_thread_id`, `message_thread_code`, `sender`, `reciever`, `last_message_timestamp`) VALUES
(2, '771e4077025c3f1', 'admin-1', 'student-1', ''),
(3, 'eb7157e52314645', 'admin-1', 'student-2', ''),
(4, '7053f3d0570a2d9', 'admin-1', 'student-3', '');

-- --------------------------------------------------------

--
-- Table structure for table `noticeboard`
--

CREATE TABLE `noticeboard` (
  `notice_id` int(11) NOT NULL,
  `notice_title` longtext NOT NULL,
  `notice` longtext NOT NULL,
  `create_timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `noticeboard`
--

INSERT INTO `noticeboard` (`notice_id`, `notice_title`, `notice`, `create_timestamp`) VALUES
(1, 'Test Notice One', 'This is a demo notice from the school administration to inform you that this is just a simple test message. Thank you!', 1637103600);

-- --------------------------------------------------------

--
-- Table structure for table `osad_acd_history`
--

CREATE TABLE `osad_acd_history` (
  `id` int(11) NOT NULL,
  `osad_student_id` int(11) NOT NULL,
  `examtype` longtext NOT NULL,
  `group` longtext NOT NULL,
  `board` longtext NOT NULL,
  `passing_yr` longtext NOT NULL,
  `special_mark` longtext NOT NULL,
  `ttl_mark` longtext NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `osad_acd_history`
--

INSERT INTO `osad_acd_history` (`id`, `osad_student_id`, `examtype`, `group`, `board`, `passing_yr`, `special_mark`, `ttl_mark`, `date`) VALUES
(1, 1, 'SSC-GENERAL', '3', 'asd', '2222', '2', '2', '2022-03-13');

-- --------------------------------------------------------

--
-- Table structure for table `osad_student`
--

CREATE TABLE `osad_student` (
  `id` int(11) NOT NULL,
  `acd_session_id` int(11) NOT NULL,
  `app_sno` int(11) NOT NULL,
  `name_en` longtext NOT NULL,
  `name_bn` longtext NOT NULL,
  `father_name` longtext NOT NULL,
  `mother_name` longtext NOT NULL,
  `gardian_name` longtext NOT NULL,
  `nationality` longtext NOT NULL,
  `technology` longtext NOT NULL,
  `ff_son` int(11) NOT NULL,
  `upjati` int(11) NOT NULL,
  `birthday` longtext NOT NULL,
  `sex` longtext NOT NULL,
  `religion` longtext NOT NULL,
  `blood_group` longtext NOT NULL,
  `pr_address` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `section_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `roll` longtext NOT NULL,
  `transport_id` int(11) NOT NULL,
  `dormitory_id` int(11) NOT NULL,
  `dormitory_room_number` longtext NOT NULL,
  `pay_no` longtext NOT NULL,
  `pay_date` date NOT NULL,
  `app_date` date NOT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `pay_status` int(1) DEFAULT NULL,
  `cur_address` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `osad_student`
--

INSERT INTO `osad_student` (`id`, `acd_session_id`, `app_sno`, `name_en`, `name_bn`, `father_name`, `mother_name`, `gardian_name`, `nationality`, `technology`, `ff_son`, `upjati`, `birthday`, `sex`, `religion`, `blood_group`, `pr_address`, `phone`, `email`, `password`, `class_id`, `section_id`, `parent_id`, `roll`, `transport_id`, `dormitory_id`, `dormitory_room_number`, `pay_no`, `pay_date`, `app_date`, `photo`, `pay_status`, `cur_address`) VALUES
(1, 8, 0, 'asdas', 'asdas', 'asdas', 'asdas', 'asdas', 'asdas', 'Textile', 0, 0, '07/15/2021', 'male', 'Buddhist', '', 'asdas', '2222222222', 'asdas@asd.com', '', '', 0, 0, '', 0, 0, '', '', '0000-00-00', '2022-03-13', NULL, NULL, 'asdasd');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parent_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `address` longtext NOT NULL,
  `profession` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `name`, `email`, `password`, `phone`, `address`, `profession`) VALUES
(1, 'Pedro S. Hathaway', 'pedro@mail.com', '123456', '3214569855', '309 Winifred Way', 'Designier'),
(2, 'Paulene D. Spain', 'pailene@gmail.com', '123456', '1450002020', '68 Morris Street', 'Nurse'),
(3, 'Ruby J. Sloan', 'ruby@mail.com', '123456', '7854444444', '806 Radford Street', 'Housewife'),
(4, 'James K. Jameson', 'james@mail.com', '123456', '7458960000', '15 Pike Street', 'Lawyer'),
(5, 'Lester O. Shelton', 'lester@mail.com', '123456', '7414440010', '10 Smith Street', 'Butcher'),
(6, 'Olga T. Decker', 'olga@mail.com', '123456', '7414741450', '56 Jarvis Street', 'Nurse'),
(7, 'James S. Scott', 'jamescott@mail.com', '123456', '7896547800', '80 Cherry Ridge Drive', 'Civil Engineer'),
(8, 'Samantha J. Wall', 'samantha@mail.com', '123456', '7890001258', '56 Harrison Street', 'Store Manager'),
(9, 'Dustin A. Chartier', 'dustin@mail.com', '123456', '7450025600', '80 Collins Avenue', 'Projectionist'),
(10, 'David L. Yanez', 'davidy@mail.com', '123456', '7950001450', '59 Rocky Road', 'Businessman'),
(11, 'Jose A. Bailey', 'josb@mail.com', '123456', '7896547801', '56 Centennial Farm Road', 'Businessman'),
(12, 'Judith J. Wilson', 'judith@mail.com', '123456', '4785478540', '65 Barrington Court', 'Sales Manager');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `payment_type` longtext NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `method` longtext NOT NULL,
  `description` longtext NOT NULL,
  `amount` longtext NOT NULL,
  `timestamp` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `expense_category_id`, `title`, `payment_type`, `invoice_id`, `student_id`, `method`, `description`, `amount`, `timestamp`) VALUES
(1, 0, 'Monthly Fee', 'income', 1, 1, '1', 'asd', '100', '1449702000'),
(2, 0, 'Monthly Payment', 'income', 2, 3, '1', 'Payment for the month - Feb', '0', '1646089200'),
(3, 0, 'Monthly Fees - Feb', 'income', 3, 10, '3', 'Fees collection for the month of February', '770', '1646002800'),
(4, 0, 'Monthly Fees', 'income', 4, 5, '1', 'Fees collection for the month February', '990', '1646002800'),
(5, 0, 'Monthly Fees - Feb', 'income', 5, 12, '1', 'Fees Collection for the month February', '0', '1646002800'),
(6, 0, 'Monthly Fees', 'income', 6, 9, '2', 'none', '990', '1646002800');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `nick_name` longtext NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `name`, `nick_name`, `class_id`, `teacher_id`) VALUES
(1, 'A', 'SectionA', 2, 2),
(2, 'B', 'SectionB', 2, 6),
(3, 'A', 'SectionA', 3, 3),
(4, 'B', 'SectionB', 3, 4),
(5, 'A', 'Section A', 4, 5),
(6, 'B', 'Section B', 4, 6),
(7, 'A', 'Section A', 5, 8),
(8, 'B', 'Section B', 5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `settings_id` int(11) NOT NULL,
  `type` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`settings_id`, `type`, `description`) VALUES
(1, 'system_name', 'School Management System'),
(2, 'system_title', 'School Management System'),
(3, 'address', '93 Elm Drive, Westbury'),
(4, 'phone', '7410000010'),
(5, 'paypal_email', 'demomail@school.com'),
(6, 'currency', '$'),
(7, 'system_email', 'smsys@mail.com'),
(20, 'active_sms_service', 'clickatell'),
(11, 'language', 'english'),
(12, 'text_align', 'left-to-right'),
(13, 'clickatell_user', ''),
(14, 'clickatell_password', ''),
(15, 'clickatell_api_id', ''),
(16, 'skin_colour', 'purple'),
(17, 'twilio_account_sid', ''),
(18, 'twilio_auth_token', ''),
(19, 'twilio_sender_phone_number', '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `birthday` longtext NOT NULL,
  `sex` longtext NOT NULL,
  `religion` longtext NOT NULL,
  `blood_group` longtext NOT NULL,
  `address` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL,
  `father_name` longtext NOT NULL,
  `mother_name` longtext NOT NULL,
  `class_id` longtext NOT NULL,
  `section_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `roll` longtext NOT NULL,
  `transport_id` int(11) NOT NULL,
  `dormitory_id` int(11) NOT NULL,
  `dormitory_room_number` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `name`, `birthday`, `sex`, `religion`, `blood_group`, `address`, `phone`, `email`, `password`, `father_name`, `mother_name`, `class_id`, `section_id`, `parent_id`, `roll`, `transport_id`, `dormitory_id`, `dormitory_room_number`) VALUES
(1, 'Madeline Collins', '12/07/2007', 'Female', '', '', '23 Tea Berry Lane', '1478547855', 'collines@mail.com', '123456', '', '', '2', 1, 2, '3', 0, 0, ''),
(2, 'Eleanor Hathaway', '02/16/2010', 'Female', '', '', '302 Adams Avenue', '7400012544', 'eleanor@mail.com', '123456', '', '', '3', 3, 1, '1', 0, 0, ''),
(3, 'Martin Shelton', '09/26/2006', 'Male', '', '', '407 Happy Hollow Road', '8541110101', 'marc@mail.com', '123456', '', '', '5', 7, 5, '1', 0, 0, ''),
(4, 'Ellen Decker', '02/28/2007', 'Female', '', '', '709 Anmoore Road', '4580001450', 'ellend@mail.com', '123456', '', '', '5', 7, 6, '2', 0, 0, ''),
(5, 'Arthur Scott', '01/21/2008', 'Male', '', '', '32 Goff Avenue', '7800024580', 'arthur@mail.com', '123456', '', '', '5', 7, 7, '3', 0, 0, ''),
(6, 'Mark Wall', '11/15/2006', 'Male', '', '', '602 Sharon Lane', '4587777700', 'markw@mail.com', '123456', '', '', '5', 7, 8, '4', 0, 0, ''),
(7, 'Doris Chartier', '01/30/2007', 'Female', '', '', '10 Grasselli Street', '4780145690', 'doris@mail.com', '123456', '', '', '5', 7, 9, '5', 0, 0, ''),
(8, 'Jamie Yanez', '11/29/2006', 'Male', '', '', '17 Woodrow Way', '4569874500', 'jamie@mail.com', '123456', '', '', '5', 8, 10, '1', 0, 0, ''),
(9, 'Ray Bailey', '12/27/2005', 'Male', '', '', '70 Jarvisville Road', '4701597530', 'rayb@mail.com', '123456', '', '', '5', 8, 11, '2', 0, 0, ''),
(10, 'Ruth Sloan', '07/16/2009', 'Female', '', '', '206 Tavern Place', '4785698740', 'ruths@mail.com', '123456', '', '', '4', 5, 3, '3', 0, 0, ''),
(11, 'Brent Scott', '12/31/2009', 'Male', '', '', '98 Loving Acres Road', '4780000020', 'brent@mail.com', '123456', '', '', '4', 5, 7, '4', 0, 0, ''),
(12, 'Martha Jameson', '02/14/2008', 'Female', '', '', '31 Woodrow Way', '4741458411', 'martha@mail.com', '123456', '', '', '3', 4, 4, '4', 0, 0, ''),
(13, 'Tony Wilson', '06/12/2007', 'Male', '', '', '58 Raccoon Run', '4789666666', 'tonyw@mail.com', '123456', '', '', '5', 8, 12, '4', 0, 0, ''),
(14, 'Gary Wilson', '07/08/2009', 'Male', '', '', '452 Lanef Street', '7458965000', 'garyw@mail.com', '123456', '', '', '4', 5, 12, '6', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `name`, `class_id`, `teacher_id`) VALUES
(3, 'Science', 2, 2),
(4, 'Mathematics', 2, 7),
(5, 'Science', 3, 2),
(6, 'Science', 4, 2),
(7, 'Health', 5, 8),
(8, 'Social Studies	', 5, 4),
(9, 'Science', 5, 2),
(10, 'Mathematics', 5, 7),
(11, 'Social Studies	', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `name` longtext NOT NULL,
  `birthday` longtext NOT NULL,
  `sex` longtext NOT NULL,
  `religion` longtext NOT NULL,
  `blood_group` longtext NOT NULL,
  `address` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `email` longtext NOT NULL,
  `password` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `name`, `birthday`, `sex`, `religion`, `blood_group`, `address`, `phone`, `email`, `password`) VALUES
(2, 'Maria T. Alexander', '06/07/1989', 'female', '', '', '36 Terra Street', '8555554540', 'maria@mail.com', '123456'),
(3, 'Frank E. Robinson', '03/25/1982', 'male', '', '', '460 Davis Avenue', '0147965454', 'franke@mail.com', '123456'),
(4, 'Laura R. Smyth', '09/09/1982', 'female', '', '', '16 Bond Street', '3698885555', 'smyth@mail.com', '123456'),
(5, 'Pam R. Wortman', '08/11/1988', 'female', '', '', '23 Foley Street', '0369999969', 'pam@mail.com', '123456'),
(6, 'Glenn S. Ford', '09/08/1988', 'male', '', '', '693 Kembery Drive', '7850002500', 'gford@mail.com', '123456'),
(7, 'Katherine C. Jefferson', '07/05/1987', 'female', '', '', '96 Public Works Drive', '7898789870', 'katherine@mail.com', '123456'),
(8, 'John E. Papenfuss', '02/04/1990', 'Male', '', '', '190 Haymond Rocks Road', '4545550000', 'john@mail.com', '123456'),
(9, 'Kevin J. Hubbard', '08/26/1984', 'Male', '', '', '68 Arlington Avenue', '4502500010', 'kevinh@mail.com', '123456'),
(10, 'Zachary A. Shea', '04/01/1980', 'Male', '', '', '29 Davis Place', '4578540010', 'zachary@mail.com', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

CREATE TABLE `transport` (
  `transport_id` int(11) NOT NULL,
  `route_name` longtext NOT NULL,
  `number_of_vehicle` longtext NOT NULL,
  `description` longtext NOT NULL,
  `route_fare` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `transport`
--

INSERT INTO `transport` (`transport_id`, `route_name`, `number_of_vehicle`, `description`, `route_fare`) VALUES
(1, 'Miami to Orlando', '27', '$27 per person', '27'),
(2, 'Orlando to Miami	', '19', '$29 per person', '29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acd_session`
--
ALTER TABLE `acd_session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_routine`
--
ALTER TABLE `class_routine`
  ADD PRIMARY KEY (`class_routine_id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `dormitory`
--
ALTER TABLE `dormitory`
  ADD PRIMARY KEY (`dormitory_id`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `expense_category`
--
ALTER TABLE `expense_category`
  ADD PRIMARY KEY (`expense_category_id`);

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`phrase_id`);

--
-- Indexes for table `mark`
--
ALTER TABLE `mark`
  ADD PRIMARY KEY (`mark_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `message_thread`
--
ALTER TABLE `message_thread`
  ADD PRIMARY KEY (`message_thread_id`);

--
-- Indexes for table `noticeboard`
--
ALTER TABLE `noticeboard`
  ADD PRIMARY KEY (`notice_id`);

--
-- Indexes for table `osad_acd_history`
--
ALTER TABLE `osad_acd_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `osad_student`
--
ALTER TABLE `osad_student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acd_session_id` (`acd_session_id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`settings_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acd_session`
--
ALTER TABLE `acd_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_routine`
--
ALTER TABLE `class_routine`
  MODIFY `class_routine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dormitory`
--
ALTER TABLE `dormitory`
  MODIFY `dormitory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expense_category`
--
ALTER TABLE `expense_category`
  MODIFY `expense_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `phrase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=477;

--
-- AUTO_INCREMENT for table `mark`
--
ALTER TABLE `mark`
  MODIFY `mark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `message_thread`
--
ALTER TABLE `message_thread`
  MODIFY `message_thread_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `noticeboard`
--
ALTER TABLE `noticeboard`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `osad_acd_history`
--
ALTER TABLE `osad_acd_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `osad_student`
--
ALTER TABLE `osad_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transport`
--
ALTER TABLE `transport`
  MODIFY `transport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `osad_student`
--
ALTER TABLE `osad_student`
  ADD CONSTRAINT `osad_student_ibfk_1` FOREIGN KEY (`acd_session_id`) REFERENCES `acd_session` (`id`);
--
-- Database: `school_management_system`
--
CREATE DATABASE IF NOT EXISTS `school_management_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school_management_system`;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `class_id`, `subject_id`, `date`, `status`, `created_at`) VALUES
(4, 5, 1, 2, '2025-08-20', 'present', '2025-09-25 14:19:40'),
(5, 5, 1, 2, '2025-10-04', 'late', '2025-10-03 13:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `room` varchar(20) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `status` varchar(10) DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `room`, `capacity`, `created_at`, `description`, `status`, `updated_at`) VALUES
(1, 'Grade 1A', 'Room 101', 25, '2025-09-17 21:31:55', NULL, 'active', '2025-10-06 15:13:26'),
(2, 'Grade 1B', 'Room 102', 25, '2025-09-17 21:31:55', NULL, 'active', '2025-10-06 15:13:26'),
(3, 'Grade 2A', 'Room 201', 25, '2025-09-17 21:31:55', NULL, 'active', '2025-10-06 15:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_subjects`
--

INSERT INTO `class_subjects` (`id`, `class_id`, `subject_id`, `teacher_id`, `created_at`) VALUES
(4, 1, 2, 7, '2025-09-25 11:55:37'),
(6, 1, 3, 9, '2025-10-07 09:34:24'),
(7, 2, 5, 9, '2025-10-07 09:34:40');

-- --------------------------------------------------------

--
-- Table structure for table `cleaners`
--

CREATE TABLE `cleaners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_area` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cleaners`
--

INSERT INTO `cleaners` (`id`, `user_id`, `assigned_area`, `contact`, `email`, `schedule`, `created_at`) VALUES
(3, 37, 'Classroom Wing A', '96969', NULL, 'Full Day (8AM-5PM)', '2025-10-07 09:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `cleaning_duties`
--

CREATE TABLE `cleaning_duties` (
  `id` int(11) NOT NULL,
  `cleaner_id` int(11) NOT NULL,
  `duty_area` varchar(100) NOT NULL,
  `duty_date` date NOT NULL,
  `duty_time` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `status` enum('assigned','completed','pending') DEFAULT 'assigned',
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleaning_schedule`
--

CREATE TABLE `cleaning_schedule` (
  `id` int(11) NOT NULL,
  `cleaner_id` int(11) NOT NULL,
  `area` varchar(100) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `driver_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `emergency_contact` varchar(20) DEFAULT NULL,
  `status` enum('active','on_leave','inactive') DEFAULT 'active',
  `hire_date` date DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `driver_name`, `address`, `contact`, `email`, `emergency_contact`, `status`, `hire_date`, `license_number`, `license_expiry`, `created_at`) VALUES
(1, NULL, 'John Smith', NULL, '555-1234', NULL, NULL, 'active', NULL, 'DL123456', '2025-12-31', '2025-09-17 21:31:56'),
(2, NULL, 'Maria Garcia', NULL, '555-5678', NULL, NULL, 'active', NULL, 'DL789012', '2024-06-30', '2025-09-17 21:31:56'),
(3, NULL, 'Robert Johnson', NULL, '555-9012', NULL, NULL, 'active', NULL, 'DL345678', '2025-03-15', '2025-09-17 21:31:56'),
(4, NULL, 'John', NULL, '0201952402', NULL, NULL, 'active', NULL, 'E15', '2027-01-01', '2025-09-19 16:11:00'),
(5, NULL, 'Johnn', 'POST OFFICE BOX 7', '55555333', 'myy@gmail.com', '77777777777777', 'active', '2025-09-26', 'E154', '2025-09-26', '2025-09-26 14:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `term` varchar(20) DEFAULT NULL,
  `academic_year` year(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','paid','overdue') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `amount`, `term`, `academic_year`, `due_date`, `status`, `created_at`) VALUES
(4, 5, 500.00, '1st Term', '2024', '2026-10-03', 'paid', '2025-10-03 13:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `finance`
--

CREATE TABLE `finance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `term` varchar(20) DEFAULT NULL,
  `academic_year` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `grade`, `term`, `academic_year`, `created_at`) VALUES
(6, 5, 2, 70.00, '1st Term', '2023', '2025-09-25 14:16:18'),
(7, 5, 2, 0.11, '1st Term', '2024', '2025-10-03 13:14:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_type` enum('all','specific') DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `sender_id`, `recipient_type`, `created_at`) VALUES
(12, 'System maintance', 'the be quation of the system because it under maintenance and wont be functioning properly so take note', 1, 'all', '2025-09-20 18:52:50'),
(13, 'Vac', 'tjhjekjjhkd', 1, 'all', '2025-09-29 11:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `notification_recipients`
--

CREATE TABLE `notification_recipients` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `fee_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `fee_id`, `amount_paid`, `payment_date`, `payment_method`, `transaction_id`, `recorded_by`, `created_at`) VALUES
(3, 4, 500.00, '2025-09-06', 'bank_transfer', '17711', 3, '2025-10-03 13:50:50'),
(4, 4, 500.00, '2025-09-06', 'bank_transfer', '17711', 3, '2025-10-03 14:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `registrar`
--

CREATE TABLE `registrar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_payments`
--

CREATE TABLE `salary_payments` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check','online') DEFAULT 'bank_transfer',
  `transaction_id` varchar(100) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_salaries`
--

CREATE TABLE `staff_salaries` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `monthly_salary` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_salaries`
--

INSERT INTO `staff_salaries` (`id`, `staff_id`, `monthly_salary`, `created_at`, `updated_at`) VALUES
(1, 1, 5000.00, '2025-10-04 12:29:20', '2025-10-04 12:29:20'),
(3, 29, 3000.00, '2025-10-04 12:29:20', '2025-10-04 12:29:20');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `first_name`, `last_name`, `dob`, `class_id`, `address`, `contact`, `email`, `guardian_name`, `guardian_contact`, `created_at`) VALUES
(4, 24, 'WELAGA', 'WESEH', '2018-09-24', 3, 'POST OFFICE BOX 7', '0201952402', 'welagacornelius@gmail.com', 'peter', '0201448185', '2025-09-24 11:18:16'),
(5, 27, 'Mighty ', 'Kaba', '2000-07-14', 1, 'POST OFFICE BOX 64', '05533333333', 'maviskaba30@gmail.com', 'Kaba james ', '055555555555', '2025-09-25 10:42:44'),
(6, 35, 'student', 'name', '2021-03-07', 3, 'psot af12', '1124', 'studentname@gmail.com', 'name ', '777', '2025-10-07 09:31:20');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignments`
--

CREATE TABLE `student_assignments` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_transport`
--

CREATE TABLE `student_transport` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `pickup_point` varchar(100) DEFAULT NULL,
  `dropoff_point` varchar(100) DEFAULT NULL,
  `fee_amount` decimal(10,2) DEFAULT NULL,
  `fee_status` enum('paid','pending','overdue') DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_transport`
--

INSERT INTO `student_transport` (`id`, `student_id`, `schedule_id`, `pickup_point`, `dropoff_point`, `fee_amount`, `fee_status`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 5, 1, 'police station junction', 'school', 5.00, 'pending', '2025-09-26', NULL, 'active', '2025-09-26 14:49:28');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `description`, `created_at`) VALUES
(1, 'Mathematics', 'MATH101', 'Basic mathematics course', '2025-09-17 21:31:55'),
(2, 'English', 'ENG101', 'English language and literature', '2025-09-17 21:31:55'),
(3, 'Science', 'SCI101', 'General science course', '2025-09-17 21:31:55'),
(4, 'History', 'HIST101', 'World history', '2025-09-17 21:31:55'),
(5, 'Physics', 'PHYS101', 'Physics fundamentals', '2025-09-17 21:31:55'),
(6, 'Chemistry', 'CHEM101', 'Chemistry fundamentals', '2025-09-17 21:31:55'),
(7, 'Biology', 'BIO101', 'Biology fundamentals', '2025-09-17 21:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('academic_year', '2023-2024', '2025-09-17 21:31:56'),
('current_term', '1st Term', '2025-09-17 21:31:56'),
('school_address', '123 Education Street, City, State 12345', '2025-09-17 21:31:56'),
('school_email', 'info@schoolsystem.edu', '2025-09-17 21:31:56'),
('school_name', 'Greenwood Academy', '2025-10-06 15:25:23'),
('school_phone', '(555) 123-4567', '2025-09-17 21:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `first_name`, `last_name`, `specialization`, `contact`, `email`, `created_at`) VALUES
(7, 29, 'Catherine', 'Naa', 'English Language', '055555333', 'my@gmail.com', '2025-09-25 11:55:09'),
(9, 36, 'teacher', 'name', 'ICT', '011102', 'techassistalliance@gmail.com', '2025-10-07 09:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_salary_payments`
--

CREATE TABLE `teacher_salary_payments` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check','online') DEFAULT 'cash',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_month` varchar(7) NOT NULL,
  `payment_date` date NOT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

CREATE TABLE `transport` (
  `id` int(11) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `driver_phone` varchar(20) DEFAULT NULL,
  `vehicle_number` varchar(50) NOT NULL,
  `route` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport`
--

INSERT INTO `transport` (`id`, `driver_name`, `driver_phone`, `vehicle_number`, `route`, `capacity`, `created_at`) VALUES
(1, 'John Doe', '0244000000', 'AS-1234-22', 'Walewale - School', 40, '2025-09-18 12:23:17'),
(2, 'Mary Smith', '0245000000', 'AS-5678-23', 'Nalerigu - School', 35, '2025-09-18 12:23:17');

-- --------------------------------------------------------

--
-- Table structure for table `transport_bookings`
--

CREATE TABLE `transport_bookings` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `booked_by` int(11) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `pickup_location` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `passenger_count` int(11) DEFAULT 1,
  `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_routes`
--

CREATE TABLE `transport_routes` (
  `id` int(11) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_point` varchar(100) NOT NULL,
  `end_point` varchar(100) NOT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `estimated_time_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_routes`
--

INSERT INTO `transport_routes` (`id`, `route_name`, `description`, `start_point`, `end_point`, `distance_km`, `estimated_time_minutes`, `created_at`) VALUES
(1, 'Route A - Main Road', 'Primary route covering main residential areas', 'City Center', 'School Campus', 15.50, 45, '2025-09-26 08:28:26'),
(2, 'Route B - Eastern Suburbs', 'Eastern suburbs route', 'Eastern Terminal', 'School Campus', 12.30, 35, '2025-09-26 08:28:26'),
(3, 'Route C - Western Hills', 'Western hills and rural areas', 'West Village', 'School Campus', 20.70, 55, '2025-09-26 08:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `transport_schedule`
--

CREATE TABLE `transport_schedule` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `route` varchar(100) NOT NULL,
  `stop_name` varchar(100) DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_schedule`
--

INSERT INTO `transport_schedule` (`id`, `vehicle_id`, `route`, `stop_name`, `arrival_time`, `departure_time`, `day_of_week`, `created_at`) VALUES
(4, 3, '1', 'Tamle', '17:00:00', '11:00:00', 'Monday', '2025-09-19 16:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `transport_schedules`
--

CREATE TABLE `transport_schedules` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `schedule_type` enum('morning','evening','both') DEFAULT 'both',
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `recurring` tinyint(1) DEFAULT 1,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_schedules`
--

INSERT INTO `transport_schedules` (`id`, `vehicle_id`, `route_id`, `driver_id`, `schedule_type`, `departure_time`, `arrival_time`, `day_of_week`, `recurring`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 4, 1, 5, 'morning', '06:00:00', '08:30:00', 'Monday', 1, '2025-09-26', '2025-09-26', 'active', '2025-09-26 14:48:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','registrar','finance','teacher','student','cleaner','transport') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, '2025-09-17 21:31:55'),
(2, 'registrar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'registrar', NULL, 1, '2025-09-17 21:31:55'),
(3, 'finance', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance', NULL, 1, '2025-09-17 21:31:55'),
(24, 'welaga.weseh', '$2y$10$KncYctRND2ZYGlKdaemIhu9hXCbnt3Ums3VpQjthBm0eWyt7G8M6K', 'student', 'welagacornelius@gmail.com', 1, '2025-09-24 11:18:16'),
(27, 'mighty', '$2y$10$AKb9zZ7M49Wa9uG9WHDSzeolAuz5ZLycH6csXQ3AEv8OThQi7YH2q', 'student', 'maviskaba30@gmail.com', 1, '2025-09-25 10:42:44'),
(29, 'Catherine', '$2y$10$RjPZs8SGjO5oQgziMiG5ZekxlvAvTvQnKCCvBfEEOJUYDxTTFtoli', 'teacher', 'my@gmail.com', 1, '2025-09-25 11:55:09'),
(30, 'transport', '$2y$10$gsndX5VvCu2/NOVw6w7TvORsKhQgIKWhfkrmK090LxgD0xENXyebO', 'transport', NULL, 1, '2025-09-25 14:26:07'),
(35, 'student', '$2y$10$ORiVLbCjmBKUZ0Sk7miAw.rVC7H0fCkNwpAEP46YNNglW77lZRw/2', 'student', 'studentname@gmail.com', 1, '2025-10-07 09:31:20'),
(36, 'teacher', '$2y$10$ExgNHUE823hgMiCkEDPyWeQSRHyCArRctq9CC4EZ6PdHbV7g6ifu2', 'teacher', 'techassistalliance@gmail.com', 1, '2025-10-07 09:33:34'),
(37, 'cleaner', '$2y$10$seREeLuqhKdXdIHhXTKBXeOJsNJERj4NXpU2bf7SN4Mo8Qp3zec6G', 'cleaner', 'cleaner@school.edu', 1, '2025-10-07 09:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `model` varchar(50) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `insurance_number` varchar(100) DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `status` enum('active','maintenance','inactive') DEFAULT 'active',
  `last_maintenance` date DEFAULT NULL,
  `next_maintenance` date DEFAULT NULL,
  `vehicle_type_id` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_number`, `model`, `year`, `color`, `insurance_number`, `insurance_expiry`, `status`, `last_maintenance`, `next_maintenance`, `vehicle_type_id`, `capacity`, `driver_id`, `route_id`, `created_at`) VALUES
(1, 'SCH-001', 'Ford Transit', NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 15, 1, NULL, '2025-09-17 21:31:56'),
(2, 'SCH-002', 'Chevy Van', NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 12, 2, NULL, '2025-09-17 21:31:56'),
(3, 'GHA-20-25', 'GLE', NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, 12, 4, NULL, '2025-09-19 16:12:19'),
(4, 'GHA-22-25', 'GLE', '2001', 'blue', '23445', '2025-09-26', 'active', NULL, NULL, 4, 4, 5, 1, '2025-09-26 14:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_maintenance`
--

CREATE TABLE `vehicle_maintenance` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `maintenance_date` date NOT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `service_provider` varchar(100) DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed') DEFAULT 'scheduled',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE `vehicle_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`id`, `type_name`, `description`, `capacity`, `created_at`) VALUES
(1, 'School Bus', 'Large capacity school bus', 40, '2025-09-26 08:28:25'),
(2, 'Minibus', 'Medium capacity minibus', 20, '2025-09-26 08:28:25'),
(3, 'Van', 'Small capacity van', 12, '2025-09-26 08:28:25'),
(4, 'Car', 'Sedan for staff transport', 4, '2025-09-26 08:28:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `assignments_ibfk_2` (`teacher_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `cleaners`
--
ALTER TABLE `cleaners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cleaning_duties`
--
ALTER TABLE `cleaning_duties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cleaner_id` (`cleaner_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `cleaning_schedule`
--
ALTER TABLE `cleaning_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cleaning_schedule_ibfk_1` (`cleaner_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drivers_ibfk_2` (`user_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `finance`
--
ALTER TABLE `finance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `payments_ibfk_1` (`fee_id`);

--
-- Indexes for table `registrar`
--
ALTER TABLE `registrar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `staff_salaries`
--
ALTER TABLE `staff_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_transport`
--
ALTER TABLE `student_transport`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_schedule` (`student_id`,`schedule_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_salary_payments`
--
ALTER TABLE `teacher_salary_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `booked_by` (`booked_by`);

--
-- Indexes for table `transport_routes`
--
ALTER TABLE `transport_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport_schedule`
--
ALTER TABLE `transport_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `transport_schedules`
--
ALTER TABLE `transport_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_number` (`vehicle_number`),
  ADD KEY `vehicles_ibfk_3` (`vehicle_type_id`),
  ADD KEY `vehicles_ibfk_4` (`route_id`);

--
-- Indexes for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_subjects`
--
ALTER TABLE `class_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cleaners`
--
ALTER TABLE `cleaners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cleaning_duties`
--
ALTER TABLE `cleaning_duties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cleaning_schedule`
--
ALTER TABLE `cleaning_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `finance`
--
ALTER TABLE `finance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `registrar`
--
ALTER TABLE `registrar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_payments`
--
ALTER TABLE `salary_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_salaries`
--
ALTER TABLE `staff_salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_assignments`
--
ALTER TABLE `student_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_transport`
--
ALTER TABLE `student_transport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `teacher_salary_payments`
--
ALTER TABLE `teacher_salary_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport`
--
ALTER TABLE `transport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_routes`
--
ALTER TABLE `transport_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transport_schedule`
--
ALTER TABLE `transport_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transport_schedules`
--
ALTER TABLE `transport_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD CONSTRAINT `class_subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `class_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `class_subjects_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `cleaners`
--
ALTER TABLE `cleaners`
  ADD CONSTRAINT `cleaners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cleaning_duties`
--
ALTER TABLE `cleaning_duties`
  ADD CONSTRAINT `cleaning_duties_ibfk_1` FOREIGN KEY (`cleaner_id`) REFERENCES `cleaners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cleaning_duties_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `cleaning_schedule`
--
ALTER TABLE `cleaning_schedule`
  ADD CONSTRAINT `cleaning_schedule_ibfk_1` FOREIGN KEY (`cleaner_id`) REFERENCES `cleaners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `finance`
--
ALTER TABLE `finance`
  ADD CONSTRAINT `finance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD CONSTRAINT `notification_recipients_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`),
  ADD CONSTRAINT `notification_recipients_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`fee_id`) REFERENCES `fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `registrar`
--
ALTER TABLE `registrar`
  ADD CONSTRAINT `registrar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD CONSTRAINT `salary_payments_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_payments_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `staff_salaries`
--
ALTER TABLE `staff_salaries`
  ADD CONSTRAINT `staff_salaries_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD CONSTRAINT `student_assignments_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`),
  ADD CONSTRAINT `student_assignments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `student_transport`
--
ALTER TABLE `student_transport`
  ADD CONSTRAINT `student_transport_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `student_transport_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `transport_schedules` (`id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD CONSTRAINT `transport_bookings_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  ADD CONSTRAINT `transport_bookings_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  ADD CONSTRAINT `transport_bookings_ibfk_3` FOREIGN KEY (`booked_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `transport_schedule`
--
ALTER TABLE `transport_schedule`
  ADD CONSTRAINT `transport_schedule_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `transport_schedules`
--
ALTER TABLE `transport_schedules`
  ADD CONSTRAINT `transport_schedules_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  ADD CONSTRAINT `transport_schedules_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `transport_routes` (`id`),
  ADD CONSTRAINT `transport_schedules_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_3` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_types` (`id`),
  ADD CONSTRAINT `vehicles_ibfk_4` FOREIGN KEY (`route_id`) REFERENCES `transport_routes` (`id`);

--
-- Constraints for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  ADD CONSTRAINT `vehicle_maintenance_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);
--
-- Database: `sms_db`
--
CREATE DATABASE IF NOT EXISTS `sms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sms_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(127) NOT NULL,
  `lname` varchar(127) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `fname`, `lname`) VALUES
(1, 'elias', '$2y$10$H7obJEdmLzqqcPy7wQWhsOLUvrgzC8f1Y1or2Gxaza5z1PT0tvLy6', 'Elias', 'Abdurrahman');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `section` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `grade`, `section`) VALUES
(1, 7, 2),
(2, 1, 1),
(3, 3, 3),
(4, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `grade` varchar(31) NOT NULL,
  `grade_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `grade`, `grade_code`) VALUES
(1, '1', 'G'),
(2, '2', 'G'),
(3, '1', 'KG'),
(4, '2', 'KG'),
(7, '3', 'G');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `sender_full_name` varchar(100) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_full_name`, `sender_email`, `message`, `date_time`) VALUES
(1, 'John doe', 'es@gmail.com', 'Hello, world', '2023-02-17 23:39:15'),
(2, 'John doe', 'es@gmail.com', 'Hi', '2023-02-17 23:49:19'),
(3, 'John doe', 'es@gmail.com', 'Hey, ', '2023-02-17 23:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `registrar_office`
--

CREATE TABLE `registrar_office` (
  `r_user_id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(31) NOT NULL,
  `lname` varchar(31) NOT NULL,
  `address` varchar(31) NOT NULL,
  `employee_number` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(31) NOT NULL,
  `qualification` varchar(31) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `date_of_joined` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrar_office`
--

INSERT INTO `registrar_office` (`r_user_id`, `username`, `password`, `fname`, `lname`, `address`, `employee_number`, `date_of_birth`, `phone_number`, `qualification`, `gender`, `email_address`, `date_of_joined`) VALUES
(1, 'james', '$2y$10$t0SCfeXNcyiO9hdzNTKKB.j2xlE2yt8Hm2.0AWJR5kSE469JIkHKG', 'James', 'William', 'West Virginia', 843583, '2022-10-04', '+12328324092', 'diploma', 'Male', 'james@j.com', '2022-10-23 01:03:25'),
(2, 'oliver2', '$2y$10$7XhzOu.3OgHPFv7hKjvfUu3waU.8j6xTASj4yIWMfo...k/p8yvvS', 'Oliver2', 'Noah', 'California,  Los angeles', 6546, '1999-06-11', '09457396789', 'BSc, BA', 'Male', 'ov@ab.com', '2022-11-12 23:06:18');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `section` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `section`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(6, 'D');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `current_year` int(11) NOT NULL,
  `current_semester` varchar(11) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `slogan` varchar(300) NOT NULL,
  `about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `current_year`, `current_semester`, `school_name`, `slogan`, `about`) VALUES
(1, 2023, 'II', 'Y School', 'Lux et Veritas Light and Truth', 'This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(127) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `grade` int(11) NOT NULL,
  `section` int(11) NOT NULL,
  `address` varchar(31) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_joined` timestamp NULL DEFAULT current_timestamp(),
  `parent_fname` varchar(127) NOT NULL,
  `parent_lname` varchar(127) NOT NULL,
  `parent_phone_number` varchar(31) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `username`, `password`, `fname`, `lname`, `grade`, `section`, `address`, `gender`, `email_address`, `date_of_birth`, `date_of_joined`, `parent_fname`, `parent_lname`, `parent_phone_number`) VALUES
(1, 'john', '$2y$10$xmtROY8efWeORYiuQDE3SO.eZwscao20QNuLky1Qlr88zDzNNq4gm', 'John', 'Doe', 1, 1, 'California,  Los angeles', 'Male', 'abas55@ab.com', '2012-09-12', '2019-12-11 14:16:44', 'Doe', 'Mark', '09393'),
(3, 'abas', '$2y$10$KLFheMWgpLfoiqMuW2LQxOPficlBiSIJ9.wE2qr5yJUbAQ.5VURoO', 'Abas', 'A.', 2, 1, 'Berlin', 'Male', 'abas@ab.com', '2002-12-03', '2021-12-01 14:16:51', 'dsf', 'dfds', '7979'),
(4, 'jo', '$2y$10$pYyVlWg9jxkT0u/4LrCMS.ztMaOvgyol1hgNt.jqcFEqUC7yZLIYe', 'John3', 'Doe', 1, 1, 'California,  Los angeles', 'Female', 'jo@jo.com', '2013-06-13', '2022-09-10 13:48:49', 'Doe', 'Mark', '074932040'),
(5, 'jo2', '$2y$10$lRQ58lbak05rW7.be8ok4OaWJcb9znRp9ra.qXqnQku.iDrA9N8vy', 'Jhon', 'Doe', 1, 1, 'UK', 'Male', 'jo@jo.com', '1990-02-15', '2023-02-12 18:11:26', 'Doe', 'Do', '0943568654'),
(6, 'KING', '$2y$10$11U8F96lcWmt3z0iU/6lBuPcGhoyXoqzagKVlw9tjvr.L94.5DFOS', 'WELAGA', 'WESEH', 1, 1, 'POST OFFICE BOX 7', 'Male', 'welagacornelius@gmail.com', '2003-07-07', '2025-09-16 20:40:45', 'WELAGA', 'WESEH', '0201952402');

-- --------------------------------------------------------

--
-- Table structure for table `student_score`
--

CREATE TABLE `student_score` (
  `id` int(11) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `results` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_score`
--

INSERT INTO `student_score` (`id`, `semester`, `year`, `student_id`, `teacher_id`, `subject_id`, `results`) VALUES
(1, 'II', 2021, 1, 1, 1, '10 15,15 20,10 10,10 20,30 35'),
(2, 'II', 2023, 1, 1, 4, '15 20,4 5'),
(3, 'I', 2022, 1, 1, 5, '10 20,50 50');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject` varchar(31) NOT NULL,
  `subject_code` varchar(31) NOT NULL,
  `grade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject`, `subject_code`, `grade`) VALUES
(1, 'English', 'En', 1),
(2, 'Physics', 'Phy', 2),
(3, 'Biology', 'Bio-01', 1),
(4, 'Math', 'Math-01', 1),
(5, 'Chemistry', 'ch-01', 1),
(6, 'Programming', 'pro-01', 1),
(7, 'Java', 'java-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(255) NOT NULL,
  `class` varchar(31) NOT NULL,
  `fname` varchar(127) NOT NULL,
  `lname` varchar(127) NOT NULL,
  `subjects` varchar(31) NOT NULL,
  `address` varchar(31) NOT NULL,
  `employee_number` int(11) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(31) NOT NULL,
  `qualification` varchar(127) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `date_of_joined` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `username`, `password`, `class`, `fname`, `lname`, `subjects`, `address`, `employee_number`, `date_of_birth`, `phone_number`, `qualification`, `gender`, `email_address`, `date_of_joined`) VALUES
(1, 'oliver', '$2y$10$JruTW/rNZ6CVO4nxYWCrn.GJpiIKMACEPYrK00S7Dk/fkbJIdYau2', '1234', 'Oliver', 'Noah', '1245', 'California,  Los angeles', 6546, '2022-09-12', '0945739', 'BSc', 'Male', 'ol@ab.com', '2022-09-09 05:23:45'),
(5, 'abas', '$2y$10$cMSKcHEJcg3K6wbVcxcXGuksgU39i70aEQVKN7ZHrzqTH9oAc3y5m', '123', 'Abas', 'A.', '12', 'Berlin', 1929, '2003-09-16', '09457396789', 'BSc,', 'Male', 'abas55@ab.com', '2022-09-09 06:42:31'),
(8, 'WELAGA', '$2y$10$/ENuxoMnqP18ihN3YpSvmu1EZ5iAdenL0WvffqCm8qu5.irO1IWkS', '12', 'WELAGA', 'WESEH', '127', 'POST OFFICE BOX 7', 1213, '2013-05-01', '0201952402', 'Degree in Computer Science', 'Male', 'welagacornelius@gmail.com', '2025-09-16 20:29:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `registrar_office`
--
ALTER TABLE `registrar_office`
  ADD PRIMARY KEY (`r_user_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `student_score`
--
ALTER TABLE `student_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registrar_office`
--
ALTER TABLE `registrar_office`
  MODIFY `r_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_score`
--
ALTER TABLE `student_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- Database: `university_db`
--
CREATE DATABASE IF NOT EXISTS `university_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `university_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$w9nYmGxR9R2DmHD8Jj1Nqum1b3fZlK9M1Zg7n3mB3g1v8mJxw2wVO', '2025-09-03 17:57:45');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` text NOT NULL,
  `audience` enum('all','students') NOT NULL DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `audience`, `created_at`) VALUES
(1, 'Welcome to the new semester', 'Classes begin on the first Monday of next month.', 'all', '2025-09-02 17:01:33'),
(2, 'Exam Registration', 'Students must register for exams by the deadline.', 'students', '2025-09-02 17:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(160) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `details` text NOT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `details`, `event_date`, `created_at`) VALUES
(1, 'Open Day', 'Meet faculty, tour facilities, and learn about programs.', '2025-09-16', '2025-09-02 17:01:32'),
(2, 'Hackathon', '24-hour student hackathon with prizes.', '2025-10-02', '2025-09-02 17:01:32'),
(3, 'Open Day', 'Meet faculty, tour facilities, and learn about programs.', '2025-09-16', '2025-09-02 17:01:32'),
(4, 'Hackathon', '24-hour student hackathon with prizes.', '2025-10-02', '2025-09-02 17:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `body`, `created_at`) VALUES
(1, 'New Research Lab Opens', 'Our state-of-the-art AI and Robotics lab is now open to students.', '2025-09-02 17:01:32'),
(2, 'Scholarship Opportunities', 'Merit-based scholarships are available for top-performing applicants.', '2025-09-02 17:01:32'),
(3, 'New Research Lab Opens', 'Our state-of-the-art AI and Robotics lab is now open to students.', '2025-09-02 17:01:32'),
(4, 'Scholarship Opportunities', 'Merit-based scholarships are available for top-performing applicants.', '2025-09-02 17:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `faculty`, `department`, `name`, `duration`, `description`) VALUES
(1, 'Engineering', 'Computer Science', 'BSc Computer Science', '4 years', 'Learn programming, algorithms, AI, and systems.'),
(2, 'Science', 'Biological Sciences', 'BSc Biology', '4 years', 'Covers genetics, ecology, and microbiology.'),
(3, 'Arts', 'Languages', 'BA English', '4 years', 'Focus on English literature, linguistics, and communication.');

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `year` year(4) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(160) NOT NULL,
  `grade` varchar(4) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `course_code`, `course_title`, `grade`, `semester`, `academic_year`, `created_at`) VALUES
(1, 2, 'CSC101', 'Introduction to Programming', 'A', 'Fall', '2024/2025', '2025-09-02 17:01:33'),
(2, 2, 'MAT102', 'Discrete Mathematics', 'B+', 'Fall', '2024/2025', '2025-09-02 17:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `program` varchar(120) NOT NULL,
  `admission_year` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Site Administrator', 'king', 'admin@university.test', '1234567', 'admin', '2025-09-02 17:01:32'),
(2, 'John Doe', 'jdoe', 'jdoe@university.test', '$2y$10$tlqU9y9C0zZ5fG8m2Z3ZZuJ6f9H45T1e9oJrF7r5bVY0q2x3o3c6S', 'student', '2025-09-02 17:01:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_results_student` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_results_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Database: `university_portal`
--
CREATE DATABASE IF NOT EXISTS `university_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `university_portal`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '<HASH_FROM_SCRIPT>');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `program` varchar(150) DEFAULT NULL,
  `institution` varchar(150) DEFAULT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `app_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `fullname`, `dob`, `phone`, `level`, `program`, `institution`, `qualification`, `file_path`, `file`, `submitted_at`, `status`, `app_id`) VALUES
(1, 1, 'WELAGA KING', '2003-07-07', '0201952402', 'Undergraduate', 'COMPUTER SCIENCE ', 'AWE SHS', 'WASSCE', NULL, NULL, '2025-09-04 22:56:28', 'Pending', NULL),
(2, 1, 'WELAGA KING', '2003-07-07', '0201952402', 'Undergraduate', 'COMPUTER SCIENCE ', 'AWE SHS', 'WASSCE', NULL, NULL, '2025-09-04 23:20:39', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'WELAGA', 'KABA', 'welage@gmail.com', '$2y$10$3lBQL2OJuEpXSDyZbK3G4Ou55ffnv7QmZs3449y0KFOts4WUnOlkq', '2025-09-04 22:42:28');

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
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_id` (`app_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
