-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2018 at 09:24 AM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bcd`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dvht` int(11) DEFAULT NULL,
  `tong_tiet` int(11) DEFAULT NULL,
  `lt` int(11) DEFAULT NULL,
  `bt` int(11) DEFAULT NULL,
  `th` int(11) DEFAULT NULL,
  `hk` int(11) NOT NULL,
  `grade_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `name`, `dvht`, `tong_tiet`, `lt`, `bt`, `th`, `hk`, `grade_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Software Process & Quality Management', 6, 90, 60, NULL, 30, 1, 1, '2018-07-14 00:18:49', '2018-07-14 00:18:49'),
(2, NULL, 'Chuyên đề: Lập trình Mobile', 6, 90, 60, NULL, 30, 2, 1, '2018-07-14 00:18:49', '2018-07-14 00:18:49'),
(3, NULL, 'Anh văn 7', 5, 75, 60, 15, NULL, 3, 1, '2018-07-14 00:18:49', '2018-07-14 00:18:49'),
(4, NULL, 'Capstone Project', 20, 300, NULL, NULL, NULL, 4, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(5, NULL, 'Phân tích thiết kế hệ thống theo hướng đối tượng', 5, 90, 60, NULL, 30, 5, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(6, NULL, 'Computer Science for Practicing Engineers', 6, 90, 60, NULL, 30, 6, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(7, NULL, 'Software Testing', 6, 90, 60, NULL, 30, 7, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(8, NULL, 'Anh văn chuyên ngành', 5, 75, 75, NULL, NULL, 1, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(9, NULL, 'Software Project Management', 6, 90, 60, 30, NULL, 2, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(10, NULL, 'Application Development Practices', 4, 90, 30, NULL, 60, 3, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(11, NULL, 'Information System Application - Databases', 4, 90, 30, NULL, 60, 4, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(12, NULL, 'Anh văn 3', 3, 45, 45, NULL, NULL, 5, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(13, NULL, 'Toán rời rạc', 3, 45, 45, NULL, NULL, 6, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(14, NULL, 'Object Oriented Programming C#', 4, 90, 30, NULL, 60, 7, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(15, NULL, 'Đại số', 3, 45, 45, NULL, NULL, 1, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(16, NULL, 'Vật lý 1', 2, 30, 30, NULL, NULL, 2, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(17, NULL, 'Introduction to Software Engineering', 3, 60, 30, NULL, 30, 3, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(18, NULL, 'Fundamentals of Computing 1', 4, 75, 45, NULL, 30, 4, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(19, NULL, 'Những NLCB của CN ML 1', 3, 45, 45, NULL, NULL, 5, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50'),
(20, NULL, 'Anh văn 1', 4, 60, 60, NULL, NULL, 6, 1, '2018-07-14 00:18:50', '2018-07-14 00:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `courses_plan`
--

CREATE TABLE `courses_plan` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `hk` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'K20T', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grades_plans`
--

CREATE TABLE `grades_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2018_05_26_170912_create_r2', 1),
(4, '2018_06_25_152501_create_course_managemment_table', 1),
(5, '2018_07_13_144246_create_educationplan_management', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `studen_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `gender` int(11) NOT NULL,
  `class_id` int(10) UNSIGNED NOT NULL,
  `grade_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `staffid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otheremail` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `staffid`, `name`, `email`, `otheremail`, `password`, `phone1`, `phone2`, `status`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'T139402', 'Nguyễn Trung Tín', 'admin@admin.com', 'abc@abc.com', '$2y$10$hHktNa3MQp0GYxQ641VtcuFuboNVJ2X8pUvXrQ4wdwYv.ouZAwknC', '12039101', '15254', 1, 'admin', NULL, NULL, NULL),
(2, 'T139401', 'Nguyễn Trung Tín Assistant', 'assistant@admin.com', 'abc@abc.com', '$2y$10$hHktNa3MQp0GYxQ641VtcuFuboNVJ2X8pUvXrQ4wdwYv.ouZAwknC', '12039101', '15254', 1, 'assistant', NULL, NULL, NULL),
(3, 't000000', 'nguyễn dương vũ hà bê đê', 'habede@gmail.com', 'haofyen@gmail.com', '$2y$10$I85MS8cdodFfRuJ8RLmV8uAT5Zgy2iKaROgOwfM.oIHcFoI75j9OW', '1234567', '7654321', 0, 'admin', NULL, '2018-06-03 12:52:18', '2018-06-03 12:53:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classes_name_unique` (`name`),
  ADD KEY `classes_grade_id_foreign` (`grade_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_code_name_hk_grade_id_unique` (`code`,`name`,`hk`,`grade_id`),
  ADD KEY `courses_grade_id_foreign` (`grade_id`);

--
-- Indexes for table `courses_plan`
--
ALTER TABLE `courses_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_plan_course_id_foreign` (`course_id`),
  ADD KEY `courses_plan_plan_id_foreign` (`plan_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grades_name_unique` (`name`);

--
-- Indexes for table `grades_plans`
--
ALTER TABLE `grades_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grades_plans_name_unique` (`name`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_studen_id_unique` (`studen_id`),
  ADD KEY `students_class_id_foreign` (`class_id`),
  ADD KEY `students_grade_id_foreign` (`grade_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_staffid_unique` (`staffid`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `courses_plan`
--
ALTER TABLE `courses_plan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grades_plans`
--
ALTER TABLE `grades_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`);

--
-- Constraints for table `courses_plan`
--
ALTER TABLE `courses_plan`
  ADD CONSTRAINT `courses_plan_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `courses_plan_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `grades_plans` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `students_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
