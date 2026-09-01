-- ========================================================
-- College Management System (CMS) Database Dump for MySQL
-- Database Name: cms
-- Exported on: 2026-08-27 10:00:18
-- ========================================================

CREATE DATABASE IF NOT EXISTS `cms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cms`;

SET FOREIGN_KEY_CHECKS = 0;

--
-- Table structure for table `users` 
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'student',
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users` 
--
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', 'Dr. Robert Vance (Director)', 'admin@cms.edu', NULL, '$2y$12$0EkzGebJLdnt0l8QMeWp2uMN9QrmME6.T6hRvW.STfcJ0z6uB7e.a', 'management', '+91 98765 43210', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150', '0', NULL, '2026-08-27 07:55:35', '2026-08-27 07:55:35');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', 'Prof. Sarah Jenkins', 'faculty@cms.edu', NULL, '$2y$12$mMJsE0ge6zr3J6HbhKHK6e617k56kMzXflIyLiJkHc8HVLS72aE0y', 'staff', '+91 98765 43211', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150', '0', NULL, '2026-08-27 07:55:35', '2026-08-27 07:55:35');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('3', 'Alex Morgan', 'student@cms.edu', NULL, '$2y$12$3wWTX71ufmXA65Og8A/7tuzQoub8GFjHfJrdPooBTNnuDxRXqXDFy', 'student', '+91 98765 43212', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150', '0', NULL, '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('4', 'Rahul Sharma', 'rahul@cms.edu', NULL, '$2y$12$edTpxOpoCBpz/oLsKYLNLu2bH2O5KlORBnsTdWVe.FQKGzq03v47.', 'student', '+91 98765 43213', NULL, '0', NULL, '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('5', 'Rohan Mehta', 'rohan@cms.edu', NULL, '$2y$12$tq.NslNAS3KnGOKReDUCQ.nz4E.vOzbV0APQi2213h2tftIfYepfO', 'student', '+91 98765 43214', NULL, '0', NULL, '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('6', 'Vikram Roy', 'vikram@cms.edu', NULL, '$2y$12$c0cvjQFdM/E4o2z34cK3ceP4PCEipwCR89PvE2b5FK8VF05JQlAsy', 'student', '+91 98765 43299', NULL, '1', NULL, '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `must_change_password`, `remember_token`, `created_at`, `updated_at`) VALUES ('7', 'Ananya Sen', 'ananya@cms.edu', NULL, '$2y$12$yyQ4KmvfnVD.nGsplMXnku0WhubsZx1.x/pWjudT56FGssq4DTyMK', 'student', '+91 98765 43215', NULL, '0', NULL, '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `password_reset_tokens` 
--
DROP TABLE IF EXISTS `password_reset_tokens`;
--
-- Table structure for table `sessions` 
--
DROP TABLE IF EXISTS `sessions`;
--
-- Table structure for table `cache` 
--
DROP TABLE IF EXISTS `cache`;
--
-- Table structure for table `cache_locks` 
--
DROP TABLE IF EXISTS `cache_locks`;
--
-- Table structure for table `jobs` 
--
DROP TABLE IF EXISTS `jobs`;
--
-- Table structure for table `job_batches` 
--
DROP TABLE IF EXISTS `job_batches`;
--
-- Table structure for table `failed_jobs` 
--
DROP TABLE IF EXISTS `failed_jobs`;
--
-- Table structure for table `departments` 
--
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `head_of_department` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments` 
--
INSERT INTO `departments` (`id`, `name`, `code`, `head_of_department`, `created_at`, `updated_at`) VALUES ('1', 'Master of Computer Applications', 'MCA', 'Dr. Alan Turing', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `departments` (`id`, `name`, `code`, `head_of_department`, `created_at`, `updated_at`) VALUES ('2', 'Computer Science & Engineering', 'CSE', 'Dr. Grace Hopper', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `departments` (`id`, `name`, `code`, `head_of_department`, `created_at`, `updated_at`) VALUES ('3', 'Department of Commerce & Finance', 'DOC', 'Dr. Warren Buffett', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `departments` (`id`, `name`, `code`, `head_of_department`, `created_at`, `updated_at`) VALUES ('4', 'Master of Business Administration', 'MBA', 'Dr. Peter Drucker', '2026-08-27 07:55:36', '2026-08-27 07:55:36');

--
-- Table structure for table `courses` 
--
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `duration_years` int(11) NOT NULL DEFAULT 2,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_code_unique` (`code`),
  KEY `courses_department_id_foreign` (`department_id`),
  CONSTRAINT `courses_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses` 
--
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `duration_years`, `created_at`, `updated_at`) VALUES ('1', '1', 'Master of Computer Applications (MCA)', 'MCA', '2', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `duration_years`, `created_at`, `updated_at`) VALUES ('2', '1', 'Bachelor of Computer Applications (BCA)', 'BCA', '3', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `duration_years`, `created_at`, `updated_at`) VALUES ('3', '3', 'Bachelor of Commerce (B.Com Hons)', 'BCOM', '3', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `duration_years`, `created_at`, `updated_at`) VALUES ('4', '4', 'Master of Business Administration (MBA)', 'MBA', '2', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `courses` (`id`, `department_id`, `name`, `code`, `duration_years`, `created_at`, `updated_at`) VALUES ('5', '2', 'B.Tech Computer Science & Engineering', 'BTECH-CSE', '4', '2026-08-27 07:55:36', '2026-08-27 07:55:36');

--
-- Table structure for table `enquiries` 
--
DROP TABLE IF EXISTS `enquiries`;
CREATE TABLE `enquiries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source` varchar(255) NOT NULL DEFAULT 'Website',
  `status` varchar(255) NOT NULL DEFAULT 'New',
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `enquiries_course_id_foreign` (`course_id`),
  CONSTRAINT `enquiries_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enquiries` 
--
INSERT INTO `enquiries` (`id`, `name`, `email`, `phone`, `course_id`, `source`, `status`, `notes`, `follow_up_date`, `created_at`, `updated_at`) VALUES ('1', 'David Miller', 'david@gmail.com', '+91 98111 22233', '1', 'Website', 'New', 'Interested in MCA 2026 session. Requested fee structure details.', '2026-08-29', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `enquiries` (`id`, `name`, `email`, `phone`, `course_id`, `source`, `status`, `notes`, `follow_up_date`, `created_at`, `updated_at`) VALUES ('2', 'Priya Nair', 'priya@nair.com', '+91 98222 33344', '5', 'Walk-in', 'In Progress', 'Visited campus on Monday. Counseling done. Awaiting documents.', '2026-08-28', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `enquiries` (`id`, `name`, `email`, `phone`, `course_id`, `source`, `status`, `notes`, `follow_up_date`, `created_at`, `updated_at`) VALUES ('3', 'Kevin Peterson', 'kevin@yahoo.com', '+91 98333 44455', '1', 'WhatsApp Marketing', 'Converted', 'Admission confirmed. Fee paid via online portal.', NULL, '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `marketing_templates` 
--
DROP TABLE IF EXISTS `marketing_templates`;
CREATE TABLE `marketing_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'whatsapp',
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_templates` 
--
INSERT INTO `marketing_templates` (`id`, `title`, `type`, `content`, `created_at`, `updated_at`) VALUES ('1', 'Admission Open 2026 - MCA & CSE', 'whatsapp', 'Hello {name}, Admissions for Session 2026 are now OPEN at CMS College! Apply now for {course} and get early-bird scholarships. Contact us at 9876543210 or reply YES.', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `marketing_templates` (`id`, `title`, `type`, `content`, `created_at`, `updated_at`) VALUES ('2', 'Campus Visit Invitation SMS', 'sms', 'Dear {name}, join our Grand Open Campus Tour on Saturday at CMS College. Experience state-of-the-art labs & meet faculty. Call 9876543210 to book spot.', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `marketing_campaigns` 
--
DROP TABLE IF EXISTS `marketing_campaigns`;
CREATE TABLE `marketing_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `recipients_count` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Completed',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_campaigns_template_id_foreign` (`template_id`),
  CONSTRAINT `marketing_campaigns_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `marketing_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_campaigns` 
--
INSERT INTO `marketing_campaigns` (`id`, `name`, `template_id`, `recipients_count`, `status`, `sent_at`, `created_at`, `updated_at`) VALUES ('1', 'August MCA Outreach Blitz', '1', '125', 'Completed', '2026-08-27 03:55:37', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `marketing_logs` 
--
DROP TABLE IF EXISTS `marketing_logs`;
CREATE TABLE `marketing_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `recipient_phone` varchar(255) NOT NULL,
  `channel` varchar(255) NOT NULL DEFAULT 'WhatsApp',
  `message` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Delivered',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_logs_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `marketing_logs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `marketing_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_logs` 
--
INSERT INTO `marketing_logs` (`id`, `campaign_id`, `recipient_phone`, `channel`, `message`, `status`, `created_at`, `updated_at`) VALUES ('1', '1', '+91 98111 22233', 'WhatsApp', 'Hello David Miller, Admissions for Session 2026 are now OPEN at CMS College! Apply now for MCA and get early-bird scholarships. Contact us at 9876543210 or reply YES.', 'Delivered', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `marketing_logs` (`id`, `campaign_id`, `recipient_phone`, `channel`, `message`, `status`, `created_at`, `updated_at`) VALUES ('2', '1', '+91 98222 33344', 'WhatsApp', 'Hello Priya Nair, Admissions for Session 2026 are now OPEN at CMS College! Apply now for B.Tech CSE and get early-bird scholarships. Contact us at 9876543210 or reply YES.', 'Read', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `students` 
--
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_no` varchar(255) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `batch_year` int(11) NOT NULL DEFAULT 2026,
  `current_semester` int(11) NOT NULL DEFAULT 1,
  `gender` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_enrollment_no_unique` (`enrollment_no`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_department_id_foreign` (`department_id`),
  KEY `students_course_id_foreign` (`course_id`),
  CONSTRAINT `students_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students` 
--
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `department_id`, `course_id`, `batch_year`, `current_semester`, `gender`, `dob`, `address`, `guardian_name`, `guardian_phone`, `created_at`, `updated_at`) VALUES ('1', '3', 'CMS-2026-MCA-00101', '1', '1', '2026', '3', 'Female', '2001-05-14', '42 College Road, Tech City', 'Michael Morgan', '+91 98765 00001', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `department_id`, `course_id`, `batch_year`, `current_semester`, `gender`, `dob`, `address`, `guardian_name`, `guardian_phone`, `created_at`, `updated_at`) VALUES ('2', '4', 'CMS-2026-MCA-00102', '1', '1', '2026', '3', 'Male', '2000-11-20', '88 Innovation Boulevard', 'Sunil Sharma', '+91 98765 00002', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `department_id`, `course_id`, `batch_year`, `current_semester`, `gender`, `dob`, `address`, `guardian_name`, `guardian_phone`, `created_at`, `updated_at`) VALUES ('3', '5', 'CMS-2026-MCA-00103', '1', '2', '2026', '1', 'Male', '2003-02-10', '12 Park Street', 'Rajesh Mehta', '+91 98765 00003', '2026-08-27 07:55:36', '2026-08-27 07:55:36');
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `department_id`, `course_id`, `batch_year`, `current_semester`, `gender`, `dob`, `address`, `guardian_name`, `guardian_phone`, `created_at`, `updated_at`) VALUES ('4', '6', 'CMS-2026-MCA-00104', '1', '1', '2026', '1', 'Male', '2002-05-12', '99 Innovation Hub', 'Dipak Roy', '+91 98765 00005', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `department_id`, `course_id`, `batch_year`, `current_semester`, `gender`, `dob`, `address`, `guardian_name`, `guardian_phone`, `created_at`, `updated_at`) VALUES ('5', '7', 'CMS-2026-DOC-00101', '3', '3', '2026', '2', 'Female', '2002-08-25', '55 Finance Avenue', 'Subhash Sen', '+91 98765 00004', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `staff_profiles` 
--
DROP TABLE IF EXISTS `staff_profiles`;
CREATE TABLE `staff_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL DEFAULT 'Assistant Professor',
  `qualification` varchar(255) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_profiles_employee_id_unique` (`employee_id`),
  KEY `staff_profiles_user_id_foreign` (`user_id`),
  KEY `staff_profiles_department_id_foreign` (`department_id`),
  CONSTRAINT `staff_profiles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_profiles` 
--
INSERT INTO `staff_profiles` (`id`, `user_id`, `department_id`, `employee_id`, `designation`, `qualification`, `joining_date`, `created_at`, `updated_at`) VALUES ('1', '2', '1', 'EMP-MCA-001', 'Associate Professor', 'Ph.D. in Computer Science', '2020-08-15', '2026-08-27 07:55:36', '2026-08-27 07:55:36');

--
-- Table structure for table `subjects` 
--
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 4,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`),
  KEY `subjects_course_id_foreign` (`course_id`),
  CONSTRAINT `subjects_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects` 
--
INSERT INTO `subjects` (`id`, `course_id`, `semester`, `name`, `code`, `credits`, `created_at`, `updated_at`) VALUES ('1', '1', '3', 'Web Application Development with Laravel', 'MCA301', '4', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `subjects` (`id`, `course_id`, `semester`, `name`, `code`, `credits`, `created_at`, `updated_at`) VALUES ('2', '1', '3', 'Advanced Database Management Systems', 'MCA302', '4', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `subjects` (`id`, `course_id`, `semester`, `name`, `code`, `credits`, `created_at`, `updated_at`) VALUES ('3', '1', '3', 'Cloud Computing & DevOps', 'MCA303', '3', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `attendances` 
--
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'present',
  `marked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_student_id_foreign` (`student_id`),
  KEY `attendances_subject_id_foreign` (`subject_id`),
  KEY `attendances_marked_by_foreign` (`marked_by`),
  CONSTRAINT `attendances_marked_by_foreign` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances` 
--
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '2026-08-23', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('2', '2', '1', '2026-08-23', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('3', '1', '1', '2026-08-24', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('4', '2', '1', '2026-08-24', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('5', '1', '1', '2026-08-25', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('6', '2', '1', '2026-08-25', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('7', '1', '1', '2026-08-26', 'present', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `attendances` (`id`, `student_id`, `subject_id`, `date`, `status`, `marked_by`, `created_at`, `updated_at`) VALUES ('8', '2', '1', '2026-08-26', 'absent', '2', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `fee_structures` 
--
DROP TABLE IF EXISTS `fee_structures`;
CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) NOT NULL DEFAULT '2026-2027',
  `tuition_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `exam_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `library_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_course_id_foreign` (`course_id`),
  CONSTRAINT `fee_structures_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_structures` 
--
INSERT INTO `fee_structures` (`id`, `course_id`, `academic_year`, `tuition_fee`, `exam_fee`, `library_fee`, `other_fee`, `total_amount`, `created_at`, `updated_at`) VALUES ('1', '1', '2026-2027', '60000', '5000', '3000', '2000', '70000', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `fee_payments` 
--
DROP TABLE IF EXISTS `fee_payments`;
CREATE TABLE `fee_payments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_no` varchar(255) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_mode` varchar(255) NOT NULL DEFAULT 'Cash',
  `payment_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_payments_receipt_no_unique` (`receipt_no`),
  KEY `fee_payments_student_id_foreign` (`student_id`),
  CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_payments` 
--
INSERT INTO `fee_payments` (`id`, `student_id`, `receipt_no`, `amount_paid`, `payment_mode`, `payment_date`, `remarks`, `created_at`, `updated_at`) VALUES ('1', '1', 'REC-2026-00101', '45000', 'Online UPI', '2026-08-17', 'First installment semester 3 fee received.', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `fee_payments` (`id`, `student_id`, `receipt_no`, `amount_paid`, `payment_mode`, `payment_date`, `remarks`, `created_at`, `updated_at`) VALUES ('2', '2', 'REC-2026-00102', '70000', 'Bank Transfer', '2026-08-12', 'Full annual fee clear.', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `exam_marks` 
--
DROP TABLE IF EXISTS `exam_marks`;
CREATE TABLE `exam_marks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `internal_marks` decimal(5,2) NOT NULL DEFAULT 0.00,
  `external_marks` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_marks` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grade` varchar(255) NOT NULL DEFAULT 'F',
  `grade_point` decimal(3,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_marks_student_id_foreign` (`student_id`),
  KEY `exam_marks_subject_id_foreign` (`subject_id`),
  CONSTRAINT `exam_marks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exam_marks_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_marks` 
--
INSERT INTO `exam_marks` (`id`, `student_id`, `subject_id`, `semester`, `internal_marks`, `external_marks`, `total_marks`, `grade`, `grade_point`, `created_at`, `updated_at`) VALUES ('1', '1', '1', '3', '28', '65', '93', 'A+', '4', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `exam_marks` (`id`, `student_id`, `subject_id`, `semester`, `internal_marks`, `external_marks`, `total_marks`, `grade`, `grade_point`, `created_at`, `updated_at`) VALUES ('2', '1', '2', '3', '25', '60', '85', 'A', '3.75', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `timetables` 
--
DROP TABLE IF EXISTS `timetables`;
CREATE TABLE `timetables` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `classroom` varchar(255) NOT NULL,
  `day_of_week` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timetables_course_id_foreign` (`course_id`),
  KEY `timetables_subject_id_foreign` (`subject_id`),
  KEY `timetables_staff_id_foreign` (`staff_id`),
  CONSTRAINT `timetables_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `timetables_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `timetables_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timetables` 
--
INSERT INTO `timetables` (`id`, `course_id`, `semester`, `subject_id`, `staff_id`, `classroom`, `day_of_week`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES ('1', '1', '3', '1', '2', 'Lab 102', 'Monday', '09:30:00', '10:30:00', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `timetables` (`id`, `course_id`, `semester`, `subject_id`, `staff_id`, `classroom`, `day_of_week`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES ('2', '1', '3', '2', '2', 'Room 204', 'Tuesday', '10:30:00', '11:30:00', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `study_materials` 
--
DROP TABLE IF EXISTS `study_materials`;
CREATE TABLE `study_materials` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'notes',
  `file_path` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `study_materials_subject_id_foreign` (`subject_id`),
  KEY `study_materials_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `study_materials_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `study_materials_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `study_materials` 
--
INSERT INTO `study_materials` (`id`, `subject_id`, `uploaded_by`, `title`, `description`, `type`, `file_path`, `due_date`, `created_at`, `updated_at`) VALUES ('1', '1', '2', 'Laravel MVC Architecture & Eloquent ORM Master Class', 'Complete slide deck and code snippets covering Blade templates, Eloquent relations, and middleware security.', 'notes', NULL, NULL, '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `study_materials` (`id`, `subject_id`, `uploaded_by`, `title`, `description`, `type`, `file_path`, `due_date`, `created_at`, `updated_at`) VALUES ('2', '1', '2', 'Assignment 1: Build a College Management Subsystem', 'Implement role-based middleware and CRUD controllers for student admission.', 'assignment', NULL, '2026-09-01', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `assignment_submissions` 
--
DROP TABLE IF EXISTS `assignment_submissions`;
CREATE TABLE `assignment_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `study_material_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'submitted',
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_submissions_study_material_id_foreign` (`study_material_id`),
  KEY `assignment_submissions_student_id_foreign` (`student_id`),
  CONSTRAINT `assignment_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignment_submissions_study_material_id_foreign` FOREIGN KEY (`study_material_id`) REFERENCES `study_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignment_submissions` 
--
INSERT INTO `assignment_submissions` (`id`, `study_material_id`, `student_id`, `file_path`, `comments`, `status`, `marks_obtained`, `created_at`, `updated_at`) VALUES ('1', '2', '1', NULL, 'Submitted assignment zip with test cases.', 'graded', '95', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

--
-- Table structure for table `notices` 
--
DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `target_audience` varchar(255) NOT NULL DEFAULT 'all',
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `attachment_path` varchar(255) DEFAULT NULL,
  `posted_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notices_posted_by_foreign` (`posted_by`),
  CONSTRAINT `notices_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices` 
--
INSERT INTO `notices` (`id`, `title`, `content`, `target_audience`, `priority`, `attachment_path`, `posted_by`, `created_at`, `updated_at`) VALUES ('1', 'Mid-Semester Examination Schedule Announced', 'The Mid-Semester examinations for MCA and B.Tech CSE will commence from 15th September. Detailed timetable is available on the portal.', 'all', 'urgent', NULL, '1', '2026-08-27 07:55:37', '2026-08-27 07:55:37');
INSERT INTO `notices` (`id`, `title`, `content`, `target_audience`, `priority`, `attachment_path`, `posted_by`, `created_at`, `updated_at`) VALUES ('2', 'Faculty Meeting: NAAC Accreditation Review', 'All Department Heads and Assistant Professors are requested to attend the NAAC self-study report briefing in Conference Room A.', 'staff', 'normal', NULL, '1', '2026-08-27 07:55:37', '2026-08-27 07:55:37');

SET FOREIGN_KEY_CHECKS = 1;
