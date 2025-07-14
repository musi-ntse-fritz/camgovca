-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 02:46 PM
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
-- Database: `camgovca_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `details`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, NULL, NULL, '127.0.0.1', NULL, NULL, '2025-07-05 15:27:38'),
(2, 1, 'certificate_approved', 'certificates', 1, NULL, NULL, '127.0.0.1', NULL, NULL, '2025-07-05 15:27:38'),
(3, 1, 'user_created', 'users', 2, NULL, NULL, '127.0.0.1', NULL, NULL, '2025-07-05 15:27:38'),
(4, NULL, 'test_action', 'test_table', 123, NULL, '{\"test\":\"data\"}', 'unknown', 'unknown', NULL, '2025-07-05 22:21:40'),
(5, NULL, 'file_read', 'files', NULL, NULL, '{\"file_path\":\"test_file.txt\"}', 'unknown', 'unknown', NULL, '2025-07-05 22:22:34'),
(6, NULL, 'db_test_insert', 'test_table', 123, NULL, '{\"name\":\"test\"}', 'unknown', 'unknown', NULL, '2025-07-05 22:22:34'),
(7, NULL, 'cert_request', 'certificates', 456, NULL, '{\"cert_type\":\"ssl\",\"subject_dn\":\"test.example.com\"}', 'unknown', 'unknown', NULL, '2025-07-05 22:22:34'),
(8, NULL, 'admin_test_action', 'test_target', NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-05 22:22:34'),
(9, 1, 'admin_certificate_rejection', 'certificate_requests', 23, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:23:29'),
(10, 1, 'cert_request', 'certificates', 26, NULL, '{\"cert_type\":\"code_signing\",\"subject_dn\":\"Fritz@wanamaker\",\"ref_code\":\"REF-20250706-9B3E99\",\"organization\":\"Zero-Trust01\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:25:11'),
(11, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-05 22:50:38'),
(12, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:55:12'),
(13, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:55:35'),
(14, NULL, 'security_2fa_otp_failed', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:55:36'),
(15, 1, 'auth_login', 'users', NULL, NULL, '{\"username\":\"admin\",\"success\":true,\"ip_address\":\"::1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:56:59'),
(16, 1, 'security_2fa_enabled', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:57:45'),
(17, 1, 'security_2fa_enabled', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:57:52'),
(18, 1, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 22:58:18'),
(19, 1, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 23:09:28'),
(20, 1, 'security_2fa_otp_validated', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 23:10:05'),
(21, 1, 'security_2fa_enabled', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 23:10:26'),
(22, 1, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 23:12:25'),
(23, 1, 'admin_test_otp_sent', 'users', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-05 23:12:27'),
(24, 1, 'security_2fa_code_sent', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:28:31'),
(25, 1, 'security_2fa_code_sent', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:33:57'),
(26, 1, 'security_2fa_code_mismatch', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:34:08'),
(27, 1, 'security_2fa_code_sent', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:34:23'),
(28, 1, 'security_2fa_code_sent', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:34:27'),
(29, 1, 'security_2fa_verification_success', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:35:34'),
(30, 1, 'user_deleted', 'users', 2, NULL, NULL, '127.0.0.1', NULL, 'User soft deleted by admin', '2025-07-06 00:35:34'),
(32, 1, 'security_certificate_creation_failed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:39:08'),
(34, 1, 'security_certificate_creation_failed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:39:24'),
(35, 1, 'cert_request', 'certificates', 27, NULL, '{\"cert_type\":\"code_signing\",\"subject_dn\":\"FRITZ\",\"ref_code\":\"REF-20250706-ACA48B\",\"organization\":\"University of Bamenda\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:41:19'),
(37, 1, 'security_certificate_creation_failed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 00:41:39'),
(38, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:26:13'),
(39, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:27:10'),
(40, NULL, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:27:19'),
(41, NULL, 'security_2fa_otp_validated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:27:19'),
(42, NULL, 'security_2fa_enabled', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(43, NULL, 'security_2fa_enabled_bulk', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(44, NULL, 'security_2fa_enabled', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(45, NULL, 'security_2fa_enabled_bulk', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(46, NULL, 'security_2fa_enabled', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(47, NULL, 'security_2fa_enabled_bulk', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:36'),
(48, NULL, 'security_2fa_enabled', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:37'),
(49, NULL, 'security_2fa_enabled_bulk', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:37:37'),
(50, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:38:44'),
(51, NULL, 'security_2fa_otp_validated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:38:44'),
(52, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:40:15'),
(53, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:40:55'),
(54, NULL, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-06 07:40:59'),
(55, 1, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:03:56'),
(56, 7, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:10:52'),
(57, 7, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:10:56'),
(58, 7, 'security_2fa_otp_resent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:10:56'),
(59, 7, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:19:09'),
(60, 7, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:19:14'),
(61, 7, 'security_2fa_otp_resent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:19:14'),
(62, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:21:44'),
(63, NULL, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:21:48'),
(64, NULL, 'security_2fa_otp_generated', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:22:32'),
(65, NULL, 'security_2fa_email_sent', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:22:36'),
(66, 7, 'security_admin_logout', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-06 08:35:41'),
(67, NULL, 'security_admin_password_reset_requested', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:05:42'),
(68, NULL, 'security_password_reset_completed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:17:27'),
(69, NULL, 'security_admin_password_reset_requested', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:19:27'),
(70, NULL, 'security_login_failed_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:26:58'),
(71, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:27:19'),
(72, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:27:53'),
(73, 7, 'security_password_reset_requested', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:45:51'),
(74, 7, 'security_password_reset_requested', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 16:49:05'),
(75, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:13:40'),
(76, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:13:46'),
(77, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:14:43'),
(78, NULL, 'security_login_failed_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:17:49'),
(79, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:18:08'),
(80, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:20:50'),
(81, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:20:57'),
(82, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:28:44'),
(83, NULL, 'security_otp_resent_test', NULL, NULL, NULL, NULL, 'unknown', 'unknown', NULL, '2025-07-08 17:29:34'),
(84, NULL, 'security_otp_resent_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:30:32'),
(85, NULL, 'security_otp_resent_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:32:09'),
(86, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:38:13'),
(87, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:39:49'),
(88, 7, 'security_2fa_code_sent', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:46:56'),
(89, 7, 'security_2fa_verification_success', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:47:23'),
(90, 7, 'user_deleted', 'users', 6, NULL, NULL, '127.0.0.1', NULL, 'User soft deleted by admin', '2025-07-08 17:47:23'),
(91, 7, 'security_admin_logout', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:48:24'),
(92, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:48:44'),
(93, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:50:31'),
(94, 2, 'security_admin_logout', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:52:36'),
(95, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:53:09'),
(96, NULL, 'cert_request', 'certificates', 28, NULL, '{\"cert_type\":\"ssl\",\"subject_dn\":\"FRITZ\",\"ref_code\":\"REF-20250708-6F11E5\",\"organization\":\"University of Bamenda\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:56:29'),
(97, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:57:01'),
(98, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:57:29'),
(100, 2, 'security_certificate_creation_failed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 17:58:20'),
(102, 2, 'security_certificate_creation_failed', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 18:01:43'),
(103, 2, 'admin_certificate_approval', 'certificate_requests', 28, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 18:04:15'),
(104, 2, 'cert_issue', 'certificates', 24, NULL, '{\"serial_number\":\"CERT-20250708-76EEB64B\",\"subject_dn\":\"FRITZ\",\"cert_type\":\"ssl\",\"request_id\":\"28\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 18:04:15'),
(105, 2, 'admin_certificate_approval', 'certificate_requests', 27, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 18:07:41'),
(106, 2, 'cert_issue', 'certificates', 25, NULL, '{\"serial_number\":\"CERT-20250708-D3E228B3\",\"subject_dn\":\"FRITZ\",\"cert_type\":\"code_signing\",\"request_id\":\"27\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-08 18:07:41'),
(107, 2, 'REVOKE', 'certificates', 25, NULL, '{\"revocation_reason\":\"using to scam\"}', '127.0.0.1', NULL, NULL, '2025-07-08 18:12:42'),
(108, 1, 'certificate_revoked', 'certificates', 1, NULL, NULL, '127.0.0.1', NULL, 'Certificate revoked. Reason: Key compromise', '2025-07-08 22:05:18'),
(109, 2, 'cert_request', 'certificates', 29, NULL, '{\"cert_type\":\"organization\",\"subject_dn\":\"FRITZ\",\"ref_code\":\"REF-20250709-364CAA\",\"organization\":\"University of Bamenda\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-09 05:33:28'),
(110, 2, 'security_admin_logout', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-09 06:26:44'),
(111, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-09 06:30:13'),
(112, NULL, 'security_password_reset_requested', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-09 06:32:06'),
(113, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', NULL, '2025-07-09 06:32:31'),
(114, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 14:47:59'),
(115, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 14:55:15'),
(116, NULL, 'security_otp_verification_failed_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 14:59:23'),
(117, NULL, 'security_otp_verification_failed_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 14:59:42'),
(118, NULL, 'security_otp_verification_failed_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 14:59:51'),
(119, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:00:37'),
(120, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:01:18'),
(121, 2, 'admin_certificate_approval', 'certificate_requests', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:02:12'),
(122, 2, 'cert_issue', 'certificates', 26, NULL, '{\"serial_number\":\"CERT-20250710-D66931FF\",\"subject_dn\":\"CN=FRITZ NTSE MUSI, O=Individual, C=CM\",\"cert_type\":\"individual\",\"request_id\":\"2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:02:12'),
(123, 2, 'admin_certificate_rejection', 'certificate_requests', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:02:37'),
(124, 2, 'REVOKE', 'certificates', 3, NULL, '{\"revocation_reason\":\"Administrative revocation\"}', '127.0.0.1', NULL, NULL, '2025-07-10 15:04:40'),
(125, 2, 'security_admin_logout', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:18:46'),
(126, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:18:54'),
(127, NULL, 'security_otp_generated_step1', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:20:13'),
(128, NULL, 'security_login_success_step2', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', NULL, '2025-07-10 15:20:28');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `cert_id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `subject_dn` varchar(500) NOT NULL,
  `issuer_dn` varchar(500) NOT NULL,
  `public_key` text NOT NULL,
  `key_size` int(11) NOT NULL,
  `signature_algorithm` varchar(50) NOT NULL,
  `certificate_pem` text NOT NULL,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valid_to` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','revoked','expired','suspended') DEFAULT 'active',
  `cert_type` enum('individual','organization','ssl','code_signing','email') NOT NULL,
  `user_id` int(11) NOT NULL,
  `ca_id` int(11) NOT NULL,
  `auth_code` varchar(50) DEFAULT NULL,
  `ref_code` varchar(50) DEFAULT NULL,
  `revocation_reason` varchar(200) DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `revoked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `certificate_password` varchar(255) DEFAULT '',
  `password_hash` varchar(255) DEFAULT '',
  `password_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password_attempts` int(11) DEFAULT 0,
  `password_locked_until` timestamp NULL DEFAULT NULL,
  `org_id` int(11) DEFAULT NULL,
  `private_key` text DEFAULT NULL,
  `expiry_date` timestamp NULL DEFAULT NULL,
  `validity_period` int(11) DEFAULT 1,
  `subject_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `subject_alt_names` text DEFAULT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspension_reason` varchar(500) DEFAULT NULL,
  `suspension_end_date` timestamp NULL DEFAULT NULL,
  `resumed_at` timestamp NULL DEFAULT NULL,
  `resume_reason` varchar(500) DEFAULT NULL,
  `resumed_by` int(11) DEFAULT NULL,
  `suspended_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`cert_id`, `serial_number`, `subject_dn`, `issuer_dn`, `public_key`, `key_size`, `signature_algorithm`, `certificate_pem`, `valid_from`, `valid_to`, `status`, `cert_type`, `user_id`, `ca_id`, `auth_code`, `ref_code`, `revocation_reason`, `revoked_at`, `revoked_by`, `created_at`, `expires_at`, `updated_at`, `certificate_password`, `password_hash`, `password_created_at`, `password_updated_at`, `password_attempts`, `password_locked_until`, `org_id`, `private_key`, `expiry_date`, `validity_period`, `subject_name`, `email`, `country`, `organization`, `subject_alt_names`, `issue_date`, `notes`, `suspended_at`, `suspension_reason`, `suspension_end_date`, `resumed_at`, `resume_reason`, `resumed_by`, `suspended_by`) VALUES
(1, 'CERT20250101001', 'CN=John Doe,OU=Individual,O=CamGovCA,C=CM', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', '-----BEGIN CERTIFICATE-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END CERTIFICATE-----', '2025-07-08 22:05:18', '2025-12-31 23:00:00', 'revoked', 'individual', 1, 2, 'AUTH123', 'REF456', 'Key compromise', '2025-07-08 22:05:18', NULL, '2025-06-29 22:11:03', '2026-06-29 22:11:03', '2025-07-08 22:05:18', 'CERT0000013e11', 'c827ad58b243b88026ced0195a4baf3a6ffdca089634ee18c0bb2319032c461f', '2025-07-05 08:32:31', '2025-07-08 22:05:18', 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-07-05 08:42:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'CERT-20250705-E8205D3A', 'CN=momo,O=CamGovCA,C=CM', 'CN=CamRootCA,OU=Cameroon Root Certification Authority,O=ANTIC,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256', '-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----', '2025-07-10 15:04:40', '2025-07-05 08:48:34', 'revoked', 'ssl', 2, 1, NULL, NULL, NULL, '2025-07-10 15:04:40', NULL, '2025-07-05 08:48:34', '2026-07-05 08:48:34', '2025-07-10 15:04:40', '1Nx3NBjZlbS]75^o', 'a71ce5807c58b4caa20a56571f873c9090ad81ce38f84ac13037f571aab83749', '2025-07-05 08:48:34', '2025-07-10 15:04:40', 3, NULL, 2, NULL, '2025-08-04 09:48:34', 30, 'momo', NULL, NULL, NULL, '', '2025-07-05 09:48:34', 'momo transactions', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'CERT-20250708-76EEB64B', 'FRITZ', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', '-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----', '2025-07-10 10:12:24', '2026-07-08 19:04:15', 'active', 'ssl', 2, 2, 'AUTH-4A25D7FE', 'REF-20250708-6F11E5', NULL, NULL, NULL, '2025-07-08 18:04:15', '2026-07-08 18:04:15', '2025-07-10 10:12:24', '', '', '2025-07-08 18:04:15', '2025-07-10 10:12:24', 2, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-07-08 18:04:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'CERT-20250708-D3E228B3', 'FRITZ', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', '-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----', '2025-07-08 21:44:02', '2026-07-08 19:07:41', 'revoked', 'code_signing', 2, 2, 'AUTH-EB59662A', 'REF-20250706-ACA48B', NULL, '2025-07-08 18:12:42', NULL, '2025-07-08 18:07:41', '2026-07-08 18:07:41', '2025-07-08 21:44:02', '', '', '2025-07-08 18:07:41', '2025-07-08 21:44:02', 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-07-08 18:07:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'CERT-20250710-D66931FF', 'CN=FRITZ NTSE MUSI, O=Individual, C=CM', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', '-----BEGIN CERTIFICATE-----\nMIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw\n...\n-----END CERTIFICATE-----', '2025-07-10 16:02:12', '2026-07-10 16:02:12', 'active', 'individual', 2, 2, 'AUTH93BA5333', 'REFD9A14009', NULL, NULL, NULL, '2025-07-10 15:02:12', NULL, '2025-07-10 15:02:12', '', '', '2025-07-10 15:02:12', '2025-07-10 15:02:12', 0, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2025-07-10 15:02:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificate_authorities`
--

CREATE TABLE `certificate_authorities` (
  `ca_id` int(11) NOT NULL,
  `ca_name` varchar(200) NOT NULL,
  `ca_type` enum('root','intermediate','subordinate') NOT NULL,
  `ca_dn` varchar(500) NOT NULL,
  `ca_serial` varchar(100) NOT NULL,
  `public_key` text NOT NULL,
  `private_key_path` varchar(255) DEFAULT NULL,
  `certificate_pem` text DEFAULT NULL,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valid_to` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive','revoked','expired') DEFAULT 'active',
  `parent_ca_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_authorities`
--

INSERT INTO `certificate_authorities` (`ca_id`, `ca_name`, `ca_type`, `ca_dn`, `ca_serial`, `public_key`, `private_key_path`, `certificate_pem`, `valid_from`, `valid_to`, `status`, `parent_ca_id`, `created_at`, `updated_at`) VALUES
(1, 'CamRootCA', 'root', 'CN=CamRootCA,OU=Cameroon Root Certification Authority,O=ANTIC,C=CM', 'ROOT001', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', NULL, NULL, '2014-12-31 23:00:00', '2034-12-31 23:00:00', 'active', NULL, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'CamGovCA', 'intermediate', 'CN=CamGovCA,OU=Cameroon Government Certification Authority,O=ANTIC CA,C=CM', 'INT001', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', NULL, NULL, '2014-12-31 23:00:00', '2029-12-31 23:00:00', 'active', NULL, '2025-06-29 22:11:03', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_copies`
--

CREATE TABLE `certificate_copies` (
  `id` int(11) NOT NULL,
  `original_certificate_id` int(11) NOT NULL,
  `copy_id` varchar(50) NOT NULL,
  `copy_format` varchar(10) NOT NULL,
  `copy_purpose` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `status` enum('active','revoked','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_copies`
--

INSERT INTO `certificate_copies` (`id`, `original_certificate_id`, `copy_id`, `copy_format`, `copy_purpose`, `created_at`, `created_by`, `status`) VALUES
(1, 1, 'COPY-20241201-TEST123', 'p12', 'backup', '2025-07-08 21:10:02', 1, 'active'),
(3, 24, 'COPY-20250708-48E768BE', 'pfx', 'mobile', '2025-07-08 21:35:00', NULL, 'active'),
(5, 24, 'COPY-20250708-B8B58A50', 'pfx', 'mobile', '2025-07-08 21:39:01', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_operations`
--

CREATE TABLE `certificate_operations` (
  `operation_id` int(11) NOT NULL,
  `cert_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `operation_type` enum('issue','renew','reissue','revoke','suspend','copy','verify','password_change') NOT NULL,
  `operation_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`operation_details`)),
  `status` enum('success','failed','pending') NOT NULL,
  `error_message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_operations`
--

INSERT INTO `certificate_operations` (`operation_id`, `cert_id`, `user_id`, `operation_type`, `operation_details`, `status`, `error_message`, `ip_address`, `created_at`) VALUES
(1, 1, 1, 'issue', '{\"cert_type\": \"individual\", \"auth_code\": \"AUTH123\", \"ref_code\": \"REF456\"}', 'success', NULL, '192.168.1.100', '2025-06-29 22:11:03'),
(2, 1, 1, 'verify', '{\"verification_method\": \"online\"}', 'success', NULL, '192.168.1.100', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_password_attempts`
--

CREATE TABLE `certificate_password_attempts` (
  `attempt_id` int(11) NOT NULL,
  `cert_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 0,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_password_attempts`
--

INSERT INTO `certificate_password_attempts` (`attempt_id`, `cert_id`, `ip_address`, `attempt_time`, `success`, `user_agent`) VALUES
(1, 24, '127.0.0.1', '2025-07-08 18:05:45', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36'),
(2, 3, '127.0.0.1', '2025-07-08 21:50:17', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36'),
(3, 3, '127.0.0.1', '2025-07-09 06:20:46', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36'),
(4, 3, '127.0.0.1', '2025-07-10 10:11:53', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36'),
(5, 24, '127.0.0.1', '2025-07-10 10:12:24', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_password_history`
--

CREATE TABLE `certificate_password_history` (
  `history_id` int(11) NOT NULL,
  `cert_id` int(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `change_reason` enum('initial','reset','change','admin_reset') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_policies`
--

CREATE TABLE `certificate_policies` (
  `policy_id` int(11) NOT NULL,
  `policy_name` varchar(200) NOT NULL,
  `policy_oid` varchar(100) DEFAULT NULL,
  `policy_description` text DEFAULT NULL,
  `cert_type` enum('individual','organization','ssl','code_signing','email') NOT NULL,
  `key_size_min` int(11) NOT NULL,
  `key_size_max` int(11) NOT NULL,
  `validity_period_days` int(11) NOT NULL,
  `signature_algorithms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signature_algorithms`)),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_policies`
--

INSERT INTO `certificate_policies` (`policy_id`, `policy_name`, `policy_oid`, `policy_description`, `cert_type`, `key_size_min`, `key_size_max`, `validity_period_days`, `signature_algorithms`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Individual Certificate Policy', '1.3.6.1.4.1.12345.1.1', 'Policy for individual digital certificates', 'individual', 2048, 4096, 365, '[\"sha256WithRSAEncryption\", \"sha384WithRSAEncryption\"]', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'Organization Certificate Policy', '1.3.6.1.4.1.12345.1.2', 'Policy for organizational digital certificates', 'organization', 2048, 4096, 365, '[\"sha256WithRSAEncryption\", \"sha384WithRSAEncryption\"]', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'SSL Certificate Policy', '1.3.6.1.4.1.12345.1.3', 'Policy for SSL/TLS certificates', 'ssl', 2048, 4096, 365, '[\"sha256WithRSAEncryption\", \"sha384WithRSAEncryption\"]', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'Code Signing Certificate Policy', '1.3.6.1.4.1.12345.1.4', 'Policy for code signing certificates', 'code_signing', 2048, 4096, 365, '[\"sha256WithRSAEncryption\", \"sha384WithRSAEncryption\"]', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(5, 'Email Certificate Policy', '1.3.6.1.4.1.12345.1.5', 'Policy for email certificates', 'email', 2048, 4096, 365, '[\"sha256WithRSAEncryption\", \"sha384WithRSAEncryption\"]', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_requests`
--

CREATE TABLE `certificate_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` enum('new','renewal','reissue','revocation') NOT NULL,
  `cert_type` enum('individual','organization','ssl','code_signing','email') NOT NULL,
  `subject_dn` varchar(500) NOT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `public_key` text NOT NULL,
  `key_size` int(11) NOT NULL,
  `signature_algorithm` varchar(50) NOT NULL,
  `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending',
  `auth_code` varchar(50) DEFAULT NULL,
  `ref_code` varchar(50) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `requested_password` varchar(255) DEFAULT NULL,
  `password_generated` tinyint(1) DEFAULT 0,
  `password_generated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_requests`
--

INSERT INTO `certificate_requests` (`request_id`, `user_id`, `request_type`, `cert_type`, `subject_dn`, `organization`, `email`, `phone`, `address`, `country`, `public_key`, `key_size`, `signature_algorithm`, `status`, `auth_code`, `ref_code`, `submitted_at`, `processed_at`, `processed_by`, `rejection_reason`, `created_at`, `updated_at`, `requested_password`, `password_generated`, `password_generated_at`) VALUES
(1, 1, 'new', 'individual', 'CN=John Doe,OU=Individual,O=CamGovCA,C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'sha256WithRSAEncryption', 'pending', 'AUTH123', 'REF456', '2025-06-29 22:11:03', NULL, NULL, NULL, '2025-06-29 22:11:03', '2025-06-29 22:11:03', NULL, 0, NULL),
(2, 2, 'new', 'individual', 'CN=FRITZ NTSE MUSI, O=Individual, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'approved', 'AUTH93BA5333', 'REFD9A14009', '2025-07-01 03:04:43', '2025-07-10 15:02:12', 2, NULL, '2025-07-01 03:04:43', '2025-07-10 15:02:12', NULL, 0, NULL),
(3, 1, 'new', 'individual', 'CN=FRITZ NTSE MUSI, O=Individual, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'approved', 'AUTH2749A52A', 'REFA8DF78DD', '2025-07-01 03:10:38', '2025-07-01 03:14:25', 1, NULL, '2025-07-01 03:10:38', '2025-07-01 03:14:25', NULL, 0, NULL),
(4, 1, 'new', 'code_signing', 'CN=ocatagon, O=octagon, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH871A15E0', 'REF1F25B912', '2025-07-01 03:38:35', NULL, NULL, NULL, '2025-07-01 03:38:35', '2025-07-01 03:38:35', NULL, 0, NULL),
(5, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'rejected', 'AUTH5725A6E8', 'REFD74AE663', '2025-07-02 19:42:01', '2025-07-10 15:02:37', 2, 'fake', '2025-07-02 19:42:01', '2025-07-10 15:02:37', NULL, 0, NULL),
(6, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTHA4A41A3A', 'REFECB511E1', '2025-07-02 19:42:30', NULL, NULL, NULL, '2025-07-02 19:42:30', '2025-07-02 19:42:30', NULL, 0, NULL),
(7, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH0B3609B1', 'REFAACA0E6D', '2025-07-04 07:13:10', NULL, NULL, NULL, '2025-07-04 07:13:10', '2025-07-04 07:13:10', NULL, 0, NULL),
(8, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH8BA012EA', 'REF213E8B30', '2025-07-04 07:18:18', NULL, NULL, NULL, '2025-07-04 07:18:18', '2025-07-04 07:18:18', NULL, 0, NULL),
(9, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTHC49E9742', 'REF69D31634', '2025-07-04 07:27:30', NULL, NULL, NULL, '2025-07-04 07:27:30', '2025-07-04 07:27:30', NULL, 0, NULL),
(10, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTHA9DCA8B0', 'REFA31BCD54', '2025-07-04 07:35:37', NULL, NULL, NULL, '2025-07-04 07:35:37', '2025-07-04 07:35:37', NULL, 0, NULL),
(11, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH07F80F43', 'REFFA65F966', '2025-07-04 07:49:07', NULL, NULL, NULL, '2025-07-04 07:49:07', '2025-07-04 07:49:07', NULL, 0, NULL),
(12, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH02E35D71', 'REFD5CBEF41', '2025-07-04 07:52:26', NULL, NULL, NULL, '2025-07-04 07:52:26', '2025-07-04 07:52:26', NULL, 0, NULL),
(13, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH6187CE88', 'REFE8901557', '2025-07-04 07:54:25', NULL, NULL, NULL, '2025-07-04 07:54:25', '2025-07-04 07:54:25', NULL, 0, NULL),
(14, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTHDE41C97A', 'REF4E88106C', '2025-07-04 07:58:06', NULL, NULL, NULL, '2025-07-04 07:58:06', '2025-07-04 07:58:06', NULL, 0, NULL),
(15, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH1D71D525', 'REF39CB2CC4', '2025-07-04 08:10:15', NULL, NULL, NULL, '2025-07-04 08:10:15', '2025-07-04 08:10:15', NULL, 0, NULL),
(16, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH1E726785', 'REF0F71D491', '2025-07-04 08:28:35', NULL, NULL, NULL, '2025-07-04 08:28:35', '2025-07-04 08:28:35', NULL, 0, NULL),
(17, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH9F00507A', 'REF8403D9C5', '2025-07-04 08:29:07', NULL, NULL, NULL, '2025-07-04 08:29:07', '2025-07-04 08:29:07', NULL, 0, NULL),
(18, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH7F8F2914', 'REFC9943224', '2025-07-04 08:29:27', NULL, NULL, NULL, '2025-07-04 08:29:27', '2025-07-04 08:29:27', NULL, 0, NULL),
(19, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTHF3FB07B5', 'REFB0D3EEAB', '2025-07-04 08:53:07', NULL, NULL, NULL, '2025-07-04 08:53:07', '2025-07-04 08:53:07', NULL, 0, NULL),
(20, 1, 'new', 'individual', 'CN=ZAP, O=ZAP, C=CM', NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...\n-----END PUBLIC KEY-----', 2048, 'SHA256', 'pending', 'AUTH452EA655', 'REF187DBD47', '2025-07-04 08:53:37', NULL, NULL, NULL, '2025-07-04 08:53:37', '2025-07-04 08:53:37', NULL, 0, NULL),
(21, 3, '', 'individual', 'MK MELCHI', 'MELCHITECH', 'melchitech@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'pending', 'AUTH-01E12F8A', 'REF-20250705-C0BB12', '2025-07-05 09:13:32', NULL, NULL, NULL, '2025-07-05 09:13:32', '2025-07-05 09:13:32', 'Melchitech@123', 0, '2025-07-05 09:13:32'),
(22, 5, '', 'email', 'Fritzwanamaker', 'Zero-Trust', 'wanamaker@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'pending', 'AUTH-E6B87A4D', 'REF-20250705-87126F', '2025-07-05 18:31:41', NULL, NULL, NULL, '2025-07-05 18:31:41', '2025-07-05 18:31:41', '@Wanamaker674', 0, '2025-07-05 18:31:41'),
(23, 5, '', 'email', 'Fritzwanamaker', 'Zero-Trust', 'wanamaker@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'rejected', 'AUTH-5143857F', 'REF-20250705-036158', '2025-07-05 18:31:52', '2025-07-05 22:23:29', 1, 'many requests\r\n', '2025-07-05 18:31:52', '2025-07-05 22:23:29', '@Wanamaker674', 0, '2025-07-05 18:31:52'),
(24, 5, '', 'email', 'Fritzwanamaker', 'Zero-Trust', 'wanamaker@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'rejected', 'AUTH-CA8FC6C3', 'REF-20250705-4DDC51', '2025-07-05 18:32:58', '2025-07-05 22:12:27', 1, 'duplicate', '2025-07-05 18:32:58', '2025-07-05 22:12:27', '@Wanamaker674', 0, '2025-07-05 18:32:58'),
(25, 5, '', 'email', 'Fritzwanamaker', 'Zero-Trust', 'wanamaker@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'rejected', 'AUTH-C15DEF2C', 'REF-20250705-0CFFB8', '2025-07-05 18:33:29', '2025-07-05 18:45:50', 1, 'not qualified\r\n', '2025-07-05 18:33:29', '2025-07-05 18:45:50', '@Wanamaker674', 0, '2025-07-05 18:33:29'),
(26, 5, '', 'code_signing', 'Fritz@wanamaker', 'Zero-Trust01', 'wanamaker@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'pending', 'AUTH-13C19171', 'REF-20250706-9B3E99', '2025-07-05 22:25:11', NULL, NULL, NULL, '2025-07-05 22:25:11', '2025-07-05 22:25:11', '@Wanamaker674', 0, '2025-07-05 22:25:11'),
(27, 2, '', 'code_signing', 'FRITZ', 'University of Bamenda', 'fritzntse@gmail.com', '674681899', 'Somatel Biyem-Assi', 'Cameroon', '', 0, '', 'approved', 'AUTH-EB59662A', 'REF-20250706-ACA48B', '2025-07-06 00:41:19', '2025-07-08 18:07:41', 2, NULL, '2025-07-06 00:41:19', '2025-07-08 18:07:41', 'Ff@123456789', 0, '2025-07-06 00:41:19'),
(28, 2, '', 'ssl', 'FRITZ', 'University of Bamenda', 'fritzntse@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'approved', 'AUTH-4A25D7FE', 'REF-20250708-6F11E5', '2025-07-08 17:56:29', '2025-07-08 18:04:15', 2, NULL, '2025-07-08 17:56:29', '2025-07-08 18:04:15', 'MKmk@1234', 0, '2025-07-08 17:56:29'),
(29, 2, '', 'organization', 'FRITZ', 'University of Bamenda', 'fritzntse@gmail.com', '674681899', 'Bambili Market', 'Cameroon', '', 0, '', 'pending', 'AUTH-C059BD0C', 'REF-20250709-364CAA', '2025-07-09 05:33:28', NULL, NULL, NULL, '2025-07-09 05:33:28', '2025-07-09 05:33:28', 'IAMFritz@Ntse674', 0, '2025-07-09 05:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_revocation_lists`
--

CREATE TABLE `certificate_revocation_lists` (
  `crl_id` int(11) NOT NULL,
  `ca_id` int(11) NOT NULL,
  `crl_number` int(11) NOT NULL,
  `this_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `next_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `crl_pem` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_notifications`
--

CREATE TABLE `email_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `recipient_email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('pending','sent','failed','cancelled') DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body_template` longtext NOT NULL,
  `language` enum('fr','en') DEFAULT 'fr',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`template_id`, `template_name`, `subject`, `body_template`, `language`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'certificate_issued', 'Votre certificat numérique a été émis', 'Bonjour {first_name},\n\nVotre certificat numérique a été émis avec succès.\n\nNuméro de série: {serial_number}\nDate de validité: {valid_from} à {valid_to}\n\nCordialement,\nL\'équipe CamGovCA', 'fr', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'certificate_renewed', 'Votre certificat numérique a été renouvelé', 'Bonjour {first_name},\n\nVotre certificat numérique a été renouvelé avec succès.\n\nNuméro de série: {serial_number}\nNouvelle date de validité: {valid_from} à {valid_to}\n\nCordialement,\nL\'équipe CamGovCA', 'fr', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'certificate_revoked', 'Votre certificat numérique a été révoqué', 'Bonjour {first_name},\n\nVotre certificat numérique a été révoqué.\n\nNuméro de série: {serial_number}\nRaison: {revocation_reason}\n\nCordialement,\nL\'équipe CamGovCA', 'fr', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'password_reset', 'Réinitialisation de votre mot de passe', 'Bonjour {first_name},\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nLien de réinitialisation: {reset_link}\n\nCe lien expire dans 24 heures.\n\nCordialement,\nL\'équipe CamGovCA', 'fr', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `faq_entries`
--

CREATE TABLE `faq_entries` (
  `faq_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` longtext NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `language` enum('fr','en') DEFAULT 'fr',
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_entries`
--

INSERT INTO `faq_entries` (`faq_id`, `question`, `answer`, `category`, `language`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Qu\'est-ce qu\'un certificat numérique ?', 'Un certificat numérique est un document électronique qui atteste l\'identité d\'une personne ou d\'une organisation sur Internet.', 'general', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'Comment obtenir un certificat numérique ?', 'Pour obtenir un certificat numérique, vous devez vous adresser à une Autorité d\'Enregistrement (AE) agréée par l\'ANTIC.', 'certificates', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'Quels sont les types de certificats disponibles ?', 'Les types de certificats disponibles sont: certificat individuel, certificat organisation, certificat SSL, certificat de signature de code, et certificat email.', 'certificates', 'fr', 2, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'Combien coûte un certificat numérique ?', 'Les tarifs varient selon le type de certificat et l\'Autorité d\'Enregistrement. Contactez une AE pour connaître les tarifs exacts.', 'pricing', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(5, 'Comment renouveler mon certificat ?', 'Vous pouvez renouveler votre certificat en ligne via notre plateforme ou en vous adressant à votre Autorité d\'Enregistrement.', 'certificates', 'fr', 3, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(6, 'What is a digital certificate?', 'A digital certificate is an electronic document that attests to the identity of a person or organization on the Internet.', 'general', 'en', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(7, 'How to obtain a digital certificate?', 'To obtain a digital certificate, you must contact a Registration Authority (RA) approved by ANTIC.', 'certificates', 'en', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(8, 'What types of certificates are available?', 'Available certificate types are: individual certificate, organization certificate, SSL certificate, code signing certificate, and email certificate.', 'certificates', 'en', 2, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(9, 'Comment installer mon certificat ?', 'Suivez les instructions d\'installation fournies avec votre certificat. Vous pouvez également consulter notre guide d\'installation.', 'installation', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(10, 'Mon certificat a expiré, que faire ?', 'Vous devez renouveler votre certificat avant expiration. Contactez votre Autorité d\'Enregistrement pour le renouvellement.', 'renewal', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(11, 'Comment révoquer un certificat ?', 'Pour révoquer un certificat, contactez l\'Autorité d\'Enregistrement Centrale de l\'ANTIC.', 'revocation', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(12, 'Quels sont les algorithmes de sécurité supportés ?', 'Nous supportons les algorithmes RSA avec SHA-256 et SHA-384.', 'security', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(13, 'Comment vérifier la validité d\'un certificat ?', 'Utilisez notre service de vérification en temps réel ou consultez les listes de révocation (CRL).', 'verification', 'fr', 1, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(14, 'What is a digital certificate?', 'A digital certificate is an electronic document that uses a digital signature to bind a public key with an identity.', 'General', 'en', 1, 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(15, 'How do I apply for a certificate?', 'You can apply for a certificate through our online portal by filling out the application form and providing the required documentation.', 'Application', 'en', 2, 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(16, 'What is the validity period of certificates?', 'Certificate validity periods vary depending on the type. Individual certificates are typically valid for 1 year, while organizational certificates can be valid for up to 3 years.', 'General', 'en', 3, 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(17, 'Comment obtenir un certificat numérique?', 'Vous pouvez obtenir un certificat numérique en soumettant une demande via notre portail en ligne.', 'Application', 'fr', 4, 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(18, 'Quelle est la durée de validité des certificats?', 'La durée de validité des certificats varie selon le type. Les certificats individuels sont généralement valides 1 an.', 'General', 'fr', 5, 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(19, 'What is a digital certificate?', 'A digital certificate is an electronic document that uses a digital signature to bind a public key with an identity.', 'General', 'en', 1, 'active', '2025-06-30 16:20:20', '2025-06-30 16:20:20'),
(20, 'How do I apply for a certificate?', 'You can apply for a certificate through our online portal by filling out the application form and providing the required documentation.', 'Application', 'en', 2, 'active', '2025-06-30 16:20:20', '2025-06-30 16:20:20'),
(21, 'What is the validity period of certificates?', 'Certificate validity periods vary depending on the type. Individual certificates are typically valid for 1 year, while organizational certificates can be valid for up to 3 years.', 'General', 'en', 3, 'active', '2025-06-30 16:20:20', '2025-06-30 16:20:20'),
(22, 'Comment obtenir un certificat numérique?', 'Vous pouvez obtenir un certificat numérique en soumettant une demande via notre portail en ligne.', 'Application', 'fr', 4, 'active', '2025-06-30 16:20:21', '2025-06-30 16:20:21'),
(23, 'Quelle est la durée de validité des certificats?', 'La durée de validité des certificats varie selon le type. Les certificats individuels sont généralement valides 1 an.', 'General', 'fr', 5, 'active', '2025-06-30 16:20:21', '2025-06-30 16:20:21'),
(24, 'What is a digital certificate?', 'A digital certificate is an electronic document that uses a digital signature to bind a public key with an identity.', 'General', 'en', 1, 'active', '2025-06-30 16:21:09', '2025-06-30 16:21:09'),
(25, 'How do I apply for a certificate?', 'You can apply for a certificate through our online portal by filling out the application form and providing the required documentation.', 'Application', 'en', 2, 'active', '2025-06-30 16:21:09', '2025-06-30 16:21:09'),
(26, 'What is the validity period of certificates?', 'Certificate validity periods vary depending on the type. Individual certificates are typically valid for 1 year, while organizational certificates can be valid for up to 3 years.', 'General', 'en', 3, 'active', '2025-06-30 16:21:09', '2025-06-30 16:21:09'),
(27, 'Comment obtenir un certificat numérique?', 'Vous pouvez obtenir un certificat numérique en soumettant une demande via notre portail en ligne.', 'Application', 'fr', 4, 'active', '2025-06-30 16:21:09', '2025-06-30 16:21:09'),
(28, 'Quelle est la durée de validité des certificats?', 'La durée de validité des certificats varie selon le type. Les certificats individuels sont généralement valides 1 an.', 'General', 'fr', 5, 'active', '2025-06-30 16:21:09', '2025-06-30 16:21:09');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `org_id` int(11) NOT NULL,
  `org_name` varchar(200) NOT NULL,
  `org_type` enum('government','private','ngo','foreign') NOT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Cameroon',
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`org_id`, `org_name`, `org_type`, `registration_number`, `tax_id`, `address`, `city`, `country`, `phone`, `email`, `website`, `contact_person`, `contact_phone`, `contact_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ministère des Finances', 'government', 'MINFIN001', NULL, 'Yaoundé, Cameroun', 'Yaoundé', 'Cameroon', '+237 222 23 45 67', 'contact@minfin.cm', NULL, 'Directeur des Systèmes d\'Information', NULL, NULL, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'Banque des États de l\'Afrique Centrale', 'government', 'BEAC001', NULL, 'Yaoundé, Cameroun', 'Yaoundé', 'Cameroon', '+237 222 20 12 34', 'contact@beac.cm', NULL, 'Directeur Technique', NULL, NULL, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'Société Nationale des Hydrocarbures', 'government', 'SNH001', NULL, 'Douala, Cameroun', 'Douala', 'Cameroon', '+237 233 42 15 67', 'contact@snh.cm', NULL, 'Directeur IT', NULL, NULL, 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'Ministry of Digital Economy', 'government', 'MINDE-001', 'MINDE-TAX-001', 'Yaounde, Cameroon', 'Yaounde', 'Cameroon', '+237 222 123 456', 'contact@minde.cm', 'www.minde.cm', 'John Doe', '+237 222 123 457', 'john.doe@minde.cm', 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(5, 'TechCorp Solutions', 'private', 'TECH-002', 'TECH-TAX-002', 'Douala, Cameroon', 'Douala', 'Cameroon', '+237 233 456 789', 'info@techcorp.cm', 'www.techcorp.cm', 'Jane Smith', '+237 233 456 790', 'jane.smith@techcorp.cm', 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(6, 'Digital Innovation NGO', 'ngo', 'DIGI-003', 'DIGI-TAX-003', 'Bamenda, Cameroon', 'Bamenda', 'Cameroon', '+237 233 789 123', 'contact@digitalinnovation.cm', 'www.digitalinnovation.cm', 'Bob Johnson', '+237 233 789 124', 'bob.johnson@digitalinnovation.cm', 'active', '2025-06-30 16:20:14', '2025-06-30 16:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `organization_users`
--

CREATE TABLE `organization_users` (
  `org_user_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `is_primary_contact` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `page_title` varchar(200) NOT NULL,
  `page_slug` varchar(200) NOT NULL,
  `page_content` longtext DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `language` enum('fr','en') DEFAULT 'fr',
  `status` enum('published','draft','archived') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page_title`, `page_slug`, `page_content`, `meta_description`, `meta_keywords`, `language`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Accueil', 'accueil', '<h1>Bienvenue sur CamGovCA</h1><p>Autorité de Certification Gouvernementale du Cameroun</p>', 'CamGovCA - Autorité de Certification Gouvernementale du Cameroun', NULL, 'fr', 'published', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'À propos de l\'ANTIC', 'a-propos-de-l-antic', '<h1>À propos de l\'ANTIC</h1><p>L\'Agence Nationale des Technologies de l\'Information et de la Communication...</p>', 'Informations sur l\'ANTIC', NULL, 'fr', 'published', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'Guide du Certificat', 'guide-du-certificat', '<h1>Guide du Certificat</h1><p>Guide complet sur les certificats numériques...</p>', 'Guide des certificats numériques', NULL, 'fr', 'published', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'Services et Produits', 'services-et-produits', '<h1>Services et Produits</h1><p>Nos services de certification...</p>', 'Services de certification numérique', NULL, 'fr', 'published', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(5, 'Contactez-nous', 'contactez-nous', '<h1>Contactez-nous</h1><p>Pour toute question, contactez-nous...</p>', 'Contact CamGovCA', NULL, 'fr', 'published', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`token_id`, `user_id`, `token`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 7, '9948f8df6664894e0f4106171c202b3d15114206bb46ac6ebf39cb5dec1b8a17', '2025-07-08 16:17:27', '2025-07-08 16:17:27', '2025-07-08 16:05:38'),
(2, 7, '74594e5b39ffaa8cc111c442c77f4ec1c5e7f1666092a33da4db2ed99719f9e9', '2025-07-09 17:19:27', NULL, '2025-07-08 16:19:27'),
(3, 7, '8fcec0a210d05fb63ecbf79a50e351f1f84f0282e70bfc0c9862517c6e23b49b', '2025-07-09 17:45:46', NULL, '2025-07-08 16:45:46'),
(4, 7, '23c2b889d990b98a5bbdd8916cf94b753164014f7f64fddc73aca411cf57b2ad', '2025-07-09 17:48:59', NULL, '2025-07-08 16:48:59'),
(5, 2, '7467020ec43bbf9c1cc5c476ea785da4304f15dceee4eac0c3575f65904b561d', '2025-07-10 07:32:04', NULL, '2025-07-09 06:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `ra_operators`
--

CREATE TABLE `ra_operators` (
  `ra_operator_id` int(11) NOT NULL,
  `ra_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ra_operators`
--

INSERT INTO `ra_operators` (`ra_operator_id`, `ra_id`, `user_id`, `role`, `permissions`, `created_at`) VALUES
(1, 3, 3, 'operator', NULL, '2025-07-08 18:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `registration_authorities`
--

CREATE TABLE `registration_authorities` (
  `ra_id` int(11) NOT NULL,
  `ra_name` varchar(200) NOT NULL,
  `ra_code` varchar(50) NOT NULL,
  `ra_type` enum('central','regional','sectoral') NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_authorities`
--

INSERT INTO `registration_authorities` (`ra_id`, `ra_name`, `ra_code`, `ra_type`, `address`, `city`, `region`, `phone`, `email`, `contact_person`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ANTIC Central RA', 'RA001', 'central', 'Yaoundé, Cameroun', 'Yaoundé', 'Centre', '+237 242 08 64 97', 'ra@antic.cm', 'Directeur Général', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'Douala Regional RA', 'RA002', 'regional', 'Douala, Cameroun', 'Douala', 'Littoral', '+237 233 42 15 67', 'ra.douala@antic.cm', 'Responsable Régional', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'Garoua Regional RA', 'RA003', 'regional', 'Garoua, Cameroun', 'Garoua', 'Nord', '+237 222 27 14 32', 'ra.garoua@antic.cm', 'Responsable Régional', 'active', '2025-06-29 22:11:03', '2025-06-29 22:11:03');

-- --------------------------------------------------------

--
-- Table structure for table `revoked_certificates`
--

CREATE TABLE `revoked_certificates` (
  `revocation_id` int(11) NOT NULL,
  `cert_id` int(11) NOT NULL,
  `revocation_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `revocation_reason` enum('unspecified','key_compromise','ca_compromise','affiliation_changed','superseded','cessation_of_operation','certificate_hold','remove_from_crl','privilege_withdrawn','aa_compromise') NOT NULL,
  `revoked_by` int(11) NOT NULL,
  `crl_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'CamGovCA - Autorité de Certification Gouvernementale du Cameroun', 'string', 'Website name', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(2, 'site_description', 'Public Key Infrastructure Center - ANTIC - CAMEROON', 'string', 'Website description', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(3, 'contact_email', 'pki@antic.cm', 'string', 'Contact email address', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(4, 'contact_phone', '+237 242 08 64 97', 'string', 'Contact phone number', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(5, 'contact_fax', '222 20 39 31', 'string', 'Contact fax number', 1, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(6, 'default_certificate_validity_days', '365', 'integer', 'Default certificate validity period in days', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(7, 'max_certificate_validity_days', '1095', 'integer', 'Maximum certificate validity period in days', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(8, 'min_key_size', '2048', 'integer', 'Minimum RSA key size', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(9, 'max_key_size', '4096', 'integer', 'Maximum RSA key size', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(10, 'crl_update_interval_hours', '24', 'integer', 'CRL update interval in hours', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(11, 'session_timeout_minutes', '30', 'integer', 'User session timeout in minutes', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(12, 'max_login_attempts', '5', 'integer', 'Maximum failed login attempts before account lock', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(13, 'account_lockout_duration_minutes', '30', 'integer', 'Account lockout duration in minutes', 0, '2025-06-29 22:11:03', '2025-06-29 22:11:03'),
(18, 'default_cert_validity', '365', 'integer', 'Setting for default cert validity', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(19, 'max_cert_validity', '1095', 'integer', 'Setting for max cert validity', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(20, 'ca_organization', 'Cameroon Government Certification Authority', 'string', 'Setting for ca organization', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(21, 'password_min_length', '8', 'integer', 'Setting for password min length', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(22, 'session_timeout', '30', 'integer', 'Setting for session timeout', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(23, 'maintenance_mode', '0', 'boolean', 'Setting for maintenance mode', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(24, 'smtp_host', 'localhost', 'string', 'Setting for smtp host', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(25, 'smtp_port', '587', 'integer', 'Setting for smtp port', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(26, 'smtp_username', '', 'string', 'Setting for smtp username', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(27, 'smtp_password', '', 'string', 'Setting for smtp password', 0, '2025-06-30 16:20:14', '2025-06-30 16:20:14'),
(54, 'certificate_password_min_length', '8', 'integer', 'Minimum length for certificate passwords', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(55, 'certificate_password_require_uppercase', 'true', 'boolean', 'Require uppercase letters in certificate passwords', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(56, 'certificate_password_require_lowercase', 'true', 'boolean', 'Require lowercase letters in certificate passwords', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(57, 'certificate_password_require_numbers', 'true', 'boolean', 'Require numbers in certificate passwords', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(58, 'certificate_password_require_special', 'true', 'boolean', 'Require special characters in certificate passwords', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(59, 'certificate_password_max_attempts', '5', 'integer', 'Maximum password attempts before lockout', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(60, 'certificate_password_lockout_duration', '1800', 'integer', 'Password lockout duration in seconds', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(61, 'certificate_password_expiry_days', '365', 'integer', 'Certificate password expiry in days', 1, '2025-07-05 08:33:50', '2025-07-05 08:33:50'),
(64, 'max_certificate_duration', '365', '', 'Durée maximale des certificats en jours', 0, '2025-07-08 21:08:18', '2025-07-08 21:08:18'),
(65, 'enable_2fa', 'true', 'boolean', 'Activer l\'authentification à deux facteurs', 0, '2025-07-08 21:08:18', '2025-07-08 21:08:18'),
(66, 'email_notifications', 'true', 'boolean', 'Activer les notifications par email', 0, '2025-07-08 21:08:18', '2025-07-08 21:08:18'),
(67, 'certificate_suspension_max_duration_days', '30', 'integer', 'Maximum suspension duration in days', 1, '2025-07-10 10:10:30', '2025-07-10 10:10:30'),
(68, 'certificate_suspension_require_reason', 'true', 'boolean', 'Require reason for certificate suspension', 1, '2025-07-10 10:10:30', '2025-07-10 10:10:30'),
(69, 'certificate_resume_require_reason', 'true', 'boolean', 'Require reason for certificate resume', 1, '2025-07-10 10:10:30', '2025-07-10 10:10:30'),
(70, 'certificate_suspension_auto_notify', 'true', 'boolean', 'Automatically notify users of certificate suspension', 1, '2025-07-10 10:10:30', '2025-07-10 10:10:30'),
(71, 'certificate_resume_auto_notify', 'true', 'boolean', 'Automatically notify users of certificate resume', 1, '2025-07-10 10:10:30', '2025-07-10 10:10:30');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_backup_codes`
--

CREATE TABLE `two_factor_backup_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `backup_code` varchar(255) NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `operation_type` varchar(50) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `two_factor_codes`
--

INSERT INTO `two_factor_codes` (`id`, `user_id`, `code`, `operation_type`, `expires_at`, `used`, `used_at`, `created_at`) VALUES
(1, 1, '670499', 'user_delete', '2025-07-06 01:30:20', 0, NULL, '2025-07-06 00:20:20'),
(2, 1, '424240', 'user_delete', '2025-07-06 01:30:56', 0, NULL, '2025-07-06 00:20:56'),
(3, 1, '857851', 'user_delete', '2025-07-06 01:35:31', 0, NULL, '2025-07-06 00:25:31'),
(4, 1, '554346', 'user_delete', '2025-07-06 01:38:27', 0, NULL, '2025-07-06 00:28:27'),
(5, 1, '047523', 'user_delete', '2025-07-06 01:43:52', 0, NULL, '2025-07-06 00:33:52'),
(6, 1, '330814', 'user_delete', '2025-07-06 01:44:19', 0, NULL, '2025-07-06 00:34:19'),
(7, 1, '671460', 'user_delete', '2025-07-06 00:35:34', 1, '2025-07-06 00:35:34', '2025-07-06 00:34:23'),
(8, 7, '752181', 'user_delete', '2025-07-08 17:47:23', 1, '2025-07-08 17:47:23', '2025-07-08 17:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_lockouts`
--

CREATE TABLE `two_factor_lockouts` (
  `lockout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `failed_attempts` int(11) DEFAULT 1,
  `locked_until` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_otps`
--

CREATE TABLE `two_factor_otps` (
  `otp_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `purpose` varchar(50) NOT NULL DEFAULT 'login',
  `expires_at` datetime NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `two_factor_otps`
--

INSERT INTO `two_factor_otps` (`otp_id`, `user_id`, `otp_hash`, `purpose`, `expires_at`, `attempts`, `used_at`, `created_at`) VALUES
(1, 1, '$2y$10$Y843srXRJYNCQtUmf6/oKuleMTR1NAvtPT1HRkQM49DmQQuUylGxu', 'test', '2025-07-06 00:55:38', 0, NULL, '2025-07-05 23:50:38'),
(2, 1, '$2y$10$L1gnZEI6r8B00ajfu1oLi.3EWUcnl3XAxC49b/7BQcnmyqQ2Lduwi', 'test', '2025-07-06 01:00:12', 0, NULL, '2025-07-05 23:55:12'),
(3, 1, '$2y$10$BxFs2Qx.86V/oMCJmQqIDu1oyjcWwowN/SAQSoy7EFOA6nm8qjhF6', 'test', '2025-07-06 01:00:35', 1, NULL, '2025-07-05 23:55:35'),
(4, 2, '$2y$10$mq.t4wBPfHqjnJ8WSyHS6OMYbZtpiMpjDDN1dGgh7kNQtYcy14JJ6', 'test', '2025-07-06 01:03:18', 0, NULL, '2025-07-05 23:58:18'),
(5, 2, '$2y$10$tn9V/mT4Z27u9lkCfGWoTeU1GwhzAuwD3UaZDmTteBrjMSunJmCiO', 'login', '2025-07-06 01:14:28', 1, '2025-07-06 00:10:05', '2025-07-06 00:09:28'),
(6, 1, '$2y$10$3vxHVRBvrF20GMomJtODf.Q.0CmBixOs8bbxuXaJpyAJAVIaYFxeO', 'test', '2025-07-06 01:17:25', 0, NULL, '2025-07-06 00:12:25'),
(7, 1, '$2y$10$yRcdWbGTy/Xy/pvvbvNOze71nR6nspg7KuFrsGKtOoyah238jioTO', 'login', '2025-07-06 09:31:13', 0, NULL, '2025-07-06 08:26:13'),
(8, 1, '$2y$10$CmM75N7jEXD2RotfYelFjeTYGrVwYohhEGre5ds0IppIS8.eZq.RO', 'login', '2025-07-06 09:32:10', 1, '2025-07-06 08:27:19', '2025-07-06 08:27:10'),
(9, 7, '$2y$10$jdqXa9jdxmcaXB3R4fRFR.Z4RKxOd4NVmY/g3Byl8RgL.9sMfXdvK', 'login', '2025-07-06 09:43:44', 1, '2025-07-06 08:38:44', '2025-07-06 08:38:44'),
(10, 7, '$2y$10$1rcKNtS6u6Y0k63gWjmWjuAV2hhFPwEhZtdkohI/3ewl/TCDYqSRe', 'login', '2025-07-06 09:45:15', 0, NULL, '2025-07-06 08:40:15'),
(11, 7, '$2y$10$eIjy179mBiuGyr6/etsBH.V7JGq1e9/Ld.9HOyNNiu9EtapY1Mn6i', 'login', '2025-07-06 09:45:55', 0, NULL, '2025-07-06 08:40:55'),
(12, 1, '$2y$10$uq97e463F5AyGilitgQ1xOMGP6K3Gd79A/NfIctlCr6r2eK6.9Ts6', 'test', '2025-07-06 10:08:56', 0, NULL, '2025-07-06 09:03:56'),
(13, 7, '$2y$10$RMa4wien/eQwy9SINoMO1OELKfwG4CV9dW4IOT9PkgRQ/4TE0eH0O', 'login', '2025-07-06 10:15:52', 0, NULL, '2025-07-06 09:10:52'),
(14, 7, '$2y$10$nisE5YrbOjJJC5HGvy9mgegiFiziR5rPsCsickiiG6sLmKTyRnv06', 'login', '2025-07-06 10:24:09', 0, NULL, '2025-07-06 09:19:09'),
(15, 7, '$2y$10$cUkIp5RzDrdPmoaQ7j.W1OXm/I6Yt4HrDvN5qckijvySKslxQwMi6', 'login', '2025-07-06 10:26:44', 0, NULL, '2025-07-06 09:21:44'),
(16, 7, '$2y$10$tnpLJ6FltbXs/f5ozhIw8ucRnxZopgEGAorljoAqfT4MUv0UG//DW', 'login', '2025-07-06 10:27:32', 0, NULL, '2025-07-06 09:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_settings`
--

CREATE TABLE `two_factor_settings` (
  `id` int(11) NOT NULL,
  `enabled` tinyint(1) DEFAULT 1,
  `email_enabled` tinyint(1) DEFAULT 1,
  `sms_enabled` tinyint(1) DEFAULT 0,
  `backup_codes_enabled` tinyint(1) DEFAULT 1,
  `max_attempts` int(11) DEFAULT 3,
  `lockout_duration` int(11) DEFAULT 15,
  `code_expiry_minutes` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `two_factor_settings`
--

INSERT INTO `two_factor_settings` (`id`, `enabled`, `email_enabled`, `sms_enabled`, `backup_codes_enabled`, `max_attempts`, `lockout_duration`, `code_expiry_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 1, 3, 15, 10, '2025-07-06 00:13:16', '2025-07-06 00:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('admin','operator','client','ra_operator') NOT NULL,
  `status` enum('active','inactive','suspended','pending') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `account_locked` tinyint(1) DEFAULT 0,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_method` varchar(20) DEFAULT 'email',
  `backup_codes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `user_type`, `status`, `created_at`, `updated_at`, `last_login`, `failed_login_attempts`, `account_locked`, `two_factor_enabled`, `two_factor_method`, `backup_codes`, `deleted_at`, `deleted_by`) VALUES
(1, 'admin', 'admin@camgovca.cm', '$2y$10$FYScCLRijsUX1By5E2fs..zLn16boNAEr3yqySDmeV0FwfYXl3cyC', 'Administrateur', 'Système', NULL, 'admin', 'active', '2025-06-29 22:11:03', '2025-07-05 23:10:26', NULL, 0, 0, 1, 'email', '[\"87BE821A\",\"F964C0EE\",\"4D7C44B6\",\"07E29174\",\"6F212081\",\"34A90AC3\",\"E21BDE9D\",\"DA7A9E07\",\"336FD29B\",\"1AC95F15\"]', NULL, NULL),
(2, 'fritz_admin', 'fritzntse@gmail.com', '$2y$10$mHB9c3pDpr5dwkXIizuvTOtKvBsCz/hF6XJQd.swg9uSxG8z4SnOK', 'FRITZ', 'NTSE MUSI', '674681899', 'admin', 'active', '2025-07-01 03:04:43', '2025-07-10 15:20:08', '2025-07-10 15:20:08', 0, 0, 1, 'email', '[\"A1DD3127\",\"C7420101\",\"35A9BF9C\",\"46D2C3BE\",\"DE1D00D8\",\"B85CDE3D\",\"7298E16C\",\"00F2E62C\",\"EB97DD2F\",\"51DF44E9\"]', NULL, 1),
(3, 'melchitech@gmail.com', 'melchitech@gmail.com', '', 'MK MELCHI', 'MELCHITECH', '674681899', 'client', 'pending', '2025-07-05 09:11:30', '2025-07-06 07:37:36', NULL, 0, 0, 1, 'email', '[\"F2DFC1AA\",\"FCB105C7\",\"9EFE753E\",\"BAF8B726\",\"23BD6A24\",\"DAFAF2D7\",\"70FE4C50\",\"83CCE52B\",\"5818F360\",\"516C5E50\"]', NULL, NULL),
(5, 'wanamaker@gmail.com', 'wanamaker@gmail.com', '', 'Fritzwanamaker', 'Zero-Trust', '674681899', 'client', 'suspended', '2025-07-05 18:31:41', '2025-07-10 15:03:00', NULL, 0, 0, 1, 'email', '[\"3CD885B8\",\"EBADB005\",\"C7A84141\",\"6E0655BF\",\"AEF25AE7\",\"29CCC7CB\",\"135BAED4\",\"E0B1E97C\",\"B87DEF72\",\"CD91B4C1\"]', NULL, NULL),
(6, 'demo_user', 'demo@camgovca.cm', '$2y$10$KTCx9VlUyek.C/BcdwZ8Ku5dCLqHQArScGcZDJJ/.oa8q.Nk1oNo.', 'Demo', 'User', NULL, 'admin', 'active', '2025-07-05 23:08:48', '2025-07-08 17:47:23', NULL, 0, 0, 1, 'email', '[\"51DEFAAE\",\"13CC1519\",\"8EBFC067\",\"91052956\",\"C116D01B\",\"040604CF\",\"5818CCDC\",\"4180D0FB\",\"6A7E60F1\",\"AA3483FC\"]', '2025-07-08 17:47:23', 7),
(7, 'melchi', 'melchimk6@gmail.com', '$2y$10$KrDjGT5KIuHGpopOueOczeGW9d81Sg939qUb8KwFTMT1zAGUgba8W', 'mk', 'melchi', '679936323', 'ra_operator', 'active', '2025-07-06 07:08:30', '2025-07-10 15:03:19', '2025-07-08 17:53:07', 0, 0, 1, 'email', '[\"93194742\",\"4BCC2F1F\",\"942E6FAB\",\"61D0D7F8\",\"69758FAC\",\"09252519\",\"C2486E7A\",\"CE3497DC\",\"F7A0B4D9\",\"5DF1330B\"]', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Cameroon',
  `organization` varchar(200) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`cert_id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ca_id` (`ca_id`),
  ADD KEY `revoked_by` (`revoked_by`),
  ADD KEY `idx_serial_number` (`serial_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_cert_type` (`cert_type`),
  ADD KEY `idx_valid_to` (`valid_to`),
  ADD KEY `idx_auth_code` (`auth_code`),
  ADD KEY `idx_ref_code` (`ref_code`),
  ADD KEY `idx_suspended_at` (`suspended_at`),
  ADD KEY `idx_suspension_end_date` (`suspension_end_date`),
  ADD KEY `idx_resumed_at` (`resumed_at`),
  ADD KEY `idx_suspended_by` (`suspended_by`),
  ADD KEY `idx_resumed_by` (`resumed_by`);

--
-- Indexes for table `certificate_authorities`
--
ALTER TABLE `certificate_authorities`
  ADD PRIMARY KEY (`ca_id`),
  ADD UNIQUE KEY `ca_serial` (`ca_serial`),
  ADD KEY `parent_ca_id` (`parent_ca_id`),
  ADD KEY `idx_ca_type` (`ca_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_valid_to` (`valid_to`);

--
-- Indexes for table `certificate_copies`
--
ALTER TABLE `certificate_copies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `copy_id` (`copy_id`),
  ADD KEY `idx_copy_id` (`copy_id`),
  ADD KEY `idx_original_cert` (`original_certificate_id`),
  ADD KEY `idx_copy_status` (`status`);

--
-- Indexes for table `certificate_operations`
--
ALTER TABLE `certificate_operations`
  ADD PRIMARY KEY (`operation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_cert_id` (`cert_id`),
  ADD KEY `idx_operation_type` (`operation_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `certificate_password_attempts`
--
ALTER TABLE `certificate_password_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `idx_cert_id` (`cert_id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_attempt_time` (`attempt_time`);

--
-- Indexes for table `certificate_password_history`
--
ALTER TABLE `certificate_password_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_cert_id` (`cert_id`),
  ADD KEY `idx_changed_by` (`changed_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `certificate_policies`
--
ALTER TABLE `certificate_policies`
  ADD PRIMARY KEY (`policy_id`),
  ADD UNIQUE KEY `policy_oid` (`policy_oid`),
  ADD KEY `idx_policy_oid` (`policy_oid`),
  ADD KEY `idx_cert_type` (`cert_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_request_type` (`request_type`),
  ADD KEY `idx_auth_code` (`auth_code`),
  ADD KEY `idx_ref_code` (`ref_code`);

--
-- Indexes for table `certificate_revocation_lists`
--
ALTER TABLE `certificate_revocation_lists`
  ADD PRIMARY KEY (`crl_id`),
  ADD UNIQUE KEY `unique_ca_crl` (`ca_id`,`crl_number`),
  ADD KEY `idx_ca_id` (`ca_id`),
  ADD KEY `idx_next_update` (`next_update`);

--
-- Indexes for table `email_notifications`
--
ALTER TABLE `email_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`template_id`),
  ADD UNIQUE KEY `template_name` (`template_name`),
  ADD KEY `idx_template_name` (`template_name`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `faq_entries`
--
ALTER TABLE `faq_entries`
  ADD PRIMARY KEY (`faq_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sort_order` (`sort_order`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD KEY `idx_org_type` (`org_type`),
  ADD KEY `idx_registration_number` (`registration_number`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `organization_users`
--
ALTER TABLE `organization_users`
  ADD PRIMARY KEY (`org_user_id`),
  ADD UNIQUE KEY `unique_org_user` (`org_id`,`user_id`),
  ADD KEY `idx_org_id` (`org_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_page_slug` (`page_slug`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `ra_operators`
--
ALTER TABLE `ra_operators`
  ADD PRIMARY KEY (`ra_operator_id`),
  ADD UNIQUE KEY `unique_ra_user` (`ra_id`,`user_id`),
  ADD KEY `idx_ra_id` (`ra_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `registration_authorities`
--
ALTER TABLE `registration_authorities`
  ADD PRIMARY KEY (`ra_id`),
  ADD UNIQUE KEY `ra_code` (`ra_code`),
  ADD KEY `idx_ra_code` (`ra_code`),
  ADD KEY `idx_ra_type` (`ra_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `revoked_certificates`
--
ALTER TABLE `revoked_certificates`
  ADD PRIMARY KEY (`revocation_id`),
  ADD KEY `revoked_by` (`revoked_by`),
  ADD KEY `crl_id` (`crl_id`),
  ADD KEY `idx_cert_id` (`cert_id`),
  ADD KEY `idx_revocation_date` (`revocation_date`),
  ADD KEY `idx_revocation_reason` (`revocation_reason`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_is_public` (`is_public`),
  ADD KEY `idx_setting_type` (`setting_type`);

--
-- Indexes for table `two_factor_backup_codes`
--
ALTER TABLE `two_factor_backup_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_backup_code` (`backup_code`),
  ADD KEY `idx_used` (`used`);

--
-- Indexes for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_operation` (`user_id`,`operation_type`),
  ADD KEY `idx_expires` (`expires_at`),
  ADD KEY `idx_used` (`used`);

--
-- Indexes for table `two_factor_lockouts`
--
ALTER TABLE `two_factor_lockouts`
  ADD PRIMARY KEY (`lockout_id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- Indexes for table `two_factor_otps`
--
ALTER TABLE `two_factor_otps`
  ADD PRIMARY KEY (`otp_id`),
  ADD KEY `idx_user_purpose` (`user_id`,`purpose`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `two_factor_settings`
--
ALTER TABLE `two_factor_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_users_2fa_enabled` (`two_factor_enabled`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_national_id` (`national_id`),
  ADD KEY `idx_organization` (`organization`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `cert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `certificate_authorities`
--
ALTER TABLE `certificate_authorities`
  MODIFY `ca_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate_copies`
--
ALTER TABLE `certificate_copies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `certificate_operations`
--
ALTER TABLE `certificate_operations`
  MODIFY `operation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate_password_attempts`
--
ALTER TABLE `certificate_password_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `certificate_password_history`
--
ALTER TABLE `certificate_password_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_policies`
--
ALTER TABLE `certificate_policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `certificate_revocation_lists`
--
ALTER TABLE `certificate_revocation_lists`
  MODIFY `crl_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_notifications`
--
ALTER TABLE `email_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faq_entries`
--
ALTER TABLE `faq_entries`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `organization_users`
--
ALTER TABLE `organization_users`
  MODIFY `org_user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ra_operators`
--
ALTER TABLE `ra_operators`
  MODIFY `ra_operator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registration_authorities`
--
ALTER TABLE `registration_authorities`
  MODIFY `ra_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `revoked_certificates`
--
ALTER TABLE `revoked_certificates`
  MODIFY `revocation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `two_factor_backup_codes`
--
ALTER TABLE `two_factor_backup_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `two_factor_lockouts`
--
ALTER TABLE `two_factor_lockouts`
  MODIFY `lockout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `two_factor_otps`
--
ALTER TABLE `two_factor_otps`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `two_factor_settings`
--
ALTER TABLE `two_factor_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`ca_id`) REFERENCES `certificate_authorities` (`ca_id`),
  ADD CONSTRAINT `certificates_ibfk_3` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `certificates_ibfk_4` FOREIGN KEY (`suspended_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `certificates_ibfk_5` FOREIGN KEY (`resumed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `certificate_authorities`
--
ALTER TABLE `certificate_authorities`
  ADD CONSTRAINT `certificate_authorities_ibfk_1` FOREIGN KEY (`parent_ca_id`) REFERENCES `certificate_authorities` (`ca_id`);

--
-- Constraints for table `certificate_operations`
--
ALTER TABLE `certificate_operations`
  ADD CONSTRAINT `certificate_operations_ibfk_1` FOREIGN KEY (`cert_id`) REFERENCES `certificates` (`cert_id`),
  ADD CONSTRAINT `certificate_operations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD CONSTRAINT `certificate_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `certificate_requests_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `certificate_revocation_lists`
--
ALTER TABLE `certificate_revocation_lists`
  ADD CONSTRAINT `certificate_revocation_lists_ibfk_1` FOREIGN KEY (`ca_id`) REFERENCES `certificate_authorities` (`ca_id`);

--
-- Constraints for table `email_notifications`
--
ALTER TABLE `email_notifications`
  ADD CONSTRAINT `email_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `email_notifications_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`template_id`);

--
-- Constraints for table `organization_users`
--
ALTER TABLE `organization_users`
  ADD CONSTRAINT `organization_users_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`),
  ADD CONSTRAINT `organization_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ra_operators`
--
ALTER TABLE `ra_operators`
  ADD CONSTRAINT `ra_operators_ibfk_1` FOREIGN KEY (`ra_id`) REFERENCES `registration_authorities` (`ra_id`),
  ADD CONSTRAINT `ra_operators_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `revoked_certificates`
--
ALTER TABLE `revoked_certificates`
  ADD CONSTRAINT `revoked_certificates_ibfk_1` FOREIGN KEY (`cert_id`) REFERENCES `certificates` (`cert_id`),
  ADD CONSTRAINT `revoked_certificates_ibfk_2` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `revoked_certificates_ibfk_3` FOREIGN KEY (`crl_id`) REFERENCES `certificate_revocation_lists` (`crl_id`);

--
-- Constraints for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `two_factor_lockouts`
--
ALTER TABLE `two_factor_lockouts`
  ADD CONSTRAINT `two_factor_lockouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `two_factor_otps`
--
ALTER TABLE `two_factor_otps`
  ADD CONSTRAINT `two_factor_otps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
