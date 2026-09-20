-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2026 at 07:06 PM
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
-- Database: `sn project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `access_level` varchar(20) DEFAULT 'STANDARD',
  `assigned_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `amenity_bookings`
--

CREATE TABLE `amenity_bookings` (
  `booking_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booking_status` varchar(20) DEFAULT 'PENDING',
  `lock_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `target_table` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT 0,
  `ip_address` varchar(45) NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_ledgers`
--

CREATE TABLE `expense_ledgers` (
  `expense_id` int(11) NOT NULL,
  `fund_id` int(11) NOT NULL,
  `created_by_treasurer` int(11) NOT NULL,
  `approved_by_president` int(11) DEFAULT NULL,
  `description` varchar(200) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `status` varchar(20) DEFAULT 'PENDING_APPROVAL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flats`
--

CREATE TABLE `flats` (
  `flat_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `building_block` varchar(10) NOT NULL,
  `flat_number` varchar(20) NOT NULL,
  `square_feet` int(11) NOT NULL,
  `listing_type` varchar(10) NOT NULL,
  `base_price` decimal(14,2) NOT NULL,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'AVAILABLE',
  `version_id` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flats`
--

INSERT INTO `flats` (`flat_id`, `owner_id`, `building_block`, `flat_number`, `square_feet`, `listing_type`, `base_price`, `service_charge`, `status`, `version_id`, `created_at`) VALUES
(1, 2, 'A', '401', 1200, 'RENT', 28000.00, 4000.00, 'AVAILABLE', 1, '2026-09-20 14:24:58'),
(2, 2, 'A', '402', 1300, 'RENT', 30000.00, 4000.00, 'OCCUPIED', 1, '2026-09-20 14:24:58'),
(3, 2, 'B', '501', 1500, 'RENT', 35000.00, 5000.00, 'AVAILABLE', 1, '2026-09-20 14:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `flat_applications`
--

CREATE TABLE `flat_applications` (
  `application_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'PENDING_OWNER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flat_owner_users`
--

CREATE TABLE `flat_owner_users` (
  `owner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `verification_status` varchar(20) DEFAULT 'PENDING',
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gate_visitors`
--

CREATE TABLE `gate_visitors` (
  `visitor_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `visitor_name` varchar(100) NOT NULL,
  `visitor_phone` varchar(20) DEFAULT NULL,
  `gate_pass_otp` varchar(6) DEFAULT NULL,
  `entry_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `exit_time` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_users`
--

CREATE TABLE `guest_users` (
  `guest_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preferred_location` varchar(100) DEFAULT NULL,
  `budget_min` decimal(14,2) DEFAULT NULL,
  `budget_max` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `agreement_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `invoice_type` varchar(20) NOT NULL,
  `billing_period` varchar(30) NOT NULL,
  `rent_portion` decimal(14,2) NOT NULL,
  `society_portion` decimal(10,2) DEFAULT 0.00,
  `utility_portion` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'UNPAID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_complaints`
--

CREATE TABLE `maintenance_complaints` (
  `complaint_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `complaint_type` varchar(50) NOT NULL,
  `scope` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `notice_id` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `target_role` varchar(20) DEFAULT 'ALL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owner_verifications`
--

CREATE TABLE `owner_verifications` (
  `verification_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `deed_path` varchar(255) DEFAULT NULL,
  `tax_id_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING',
  `approved_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount_paid` decimal(14,2) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `transaction_reference` varchar(100) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `society_amenities`
--

CREATE TABLE `society_amenities` (
  `amenity_id` int(11) NOT NULL,
  `amenity_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `opening_hour` time DEFAULT '06:00:00',
  `closing_hour` time DEFAULT '22:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `society_funds`
--

CREATE TABLE `society_funds` (
  `fund_id` int(11) NOT NULL,
  `total_balance` decimal(14,2) DEFAULT 0.00,
  `reserve_sinking_balance` decimal(14,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `society_funds`
--

INSERT INTO `society_funds` (`fund_id`, `total_balance`, `reserve_sinking_balance`, `last_updated`) VALUES
(1, 0.00, 0.00, '2026-09-20 14:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance`
--

CREATE TABLE `staff_attendance` (
  `attendance_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `check_in` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `check_out` timestamp NULL DEFAULT NULL,
  `attendance_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_backups`
--

CREATE TABLE `system_backups` (
  `backup_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size_bytes` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'COMPLETED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenancy_agreements`
--

CREATE TABLE `tenancy_agreements` (
  `agreement_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `lease_start` date NOT NULL,
  `lease_end` date NOT NULL,
  `monthly_rent` decimal(14,2) NOT NULL,
  `security_deposit` decimal(14,2) NOT NULL,
  `agreement_status` varchar(20) DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `full_name`, `email`, `phone_number`, `password_hash`, `is_verified`, `created_at`, `last_login`) VALUES
(1, 6, 'Admin User', 'admin@example.com', '9876543210', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 1, '2026-09-20 14:24:58', NULL),
(2, 3, 'Owner Ram', 'owner@example.com', '9876543211', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 1, '2026-09-20 14:24:58', NULL),
(3, 2, 'Resident Hari', 'resident@example.com', '9876543212', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 1, '2026-09-20 14:24:58', NULL),
(4, 1, 'Guest Priya', 'guest@example.com', '9876543213', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', 1, '2026-09-20 14:24:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '[]' CHECK (json_valid(`permissions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `permissions`, `created_at`) VALUES
(1, 'GUEST', '[]', '2026-09-20 14:24:57'),
(2, 'RESIDENT', '[]', '2026-09-20 14:24:57'),
(3, 'FLAT_OWNER', '[]', '2026-09-20 14:24:57'),
(4, 'COMMITTEE', '[]', '2026-09-20 14:24:57'),
(5, 'STAFF', '[]', '2026-09-20 14:24:57'),
(6, 'ADMIN', '[]', '2026-09-20 14:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_quotes`
--

CREATE TABLE `vendor_quotes` (
  `quote_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `vendor_name` varchar(100) NOT NULL,
  `quote_amount` decimal(14,2) NOT NULL,
  `quote_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `viewing_bookings`
--

CREATE TABLE `viewing_bookings` (
  `booking_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `gate_pass_otp` varchar(6) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'CONFIRMED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `amenity_bookings`
--
ALTER TABLE `amenity_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `amenity_id` (`amenity_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `idx_date` (`booking_date`),
  ADD KEY `idx_status` (`booking_status`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_timestamp` (`logged_at`);

--
-- Indexes for table `expense_ledgers`
--
ALTER TABLE `expense_ledgers`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `fund_id` (`fund_id`),
  ADD KEY `created_by_treasurer` (`created_by_treasurer`),
  ADD KEY `approved_by_president` (`approved_by_president`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`created_at`);

--
-- Indexes for table `flats`
--
ALTER TABLE `flats`
  ADD PRIMARY KEY (`flat_id`),
  ADD UNIQUE KEY `unique_flat` (`building_block`,`flat_number`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `flat_applications`
--
ALTER TABLE `flat_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `idx_flat` (`flat_id`),
  ADD KEY `idx_guest` (`guest_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `flat_owner_users`
--
ALTER TABLE `flat_owner_users`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `gate_visitors`
--
ALTER TABLE `gate_visitors`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `flat_id` (`flat_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `idx_otp` (`gate_pass_otp`),
  ADD KEY `idx_entry` (`entry_time`);

--
-- Indexes for table `guest_users`
--
ALTER TABLE `guest_users`
  ADD PRIMARY KEY (`guest_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `agreement_id` (`agreement_id`),
  ADD KEY `flat_id` (`flat_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `maintenance_complaints`
--
ALTER TABLE `maintenance_complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_flat` (`flat_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`notice_id`),
  ADD KEY `posted_by` (`posted_by`),
  ADD KEY `idx_expiry` (`expiry_date`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `owner_verifications`
--
ALTER TABLE `owner_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD UNIQUE KEY `owner_id` (`owner_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_reference` (`transaction_reference`),
  ADD KEY `idx_invoice` (`invoice_id`),
  ADD KEY `idx_date` (`payment_date`);

--
-- Indexes for table `society_amenities`
--
ALTER TABLE `society_amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `society_funds`
--
ALTER TABLE `society_funds`
  ADD PRIMARY KEY (`fund_id`),
  ADD KEY `idx_updated` (`last_updated`);

--
-- Indexes for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `idx_staff` (`staff_id`),
  ADD KEY `idx_date` (`attendance_date`);

--
-- Indexes for table `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`backup_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tenancy_agreements`
--
ALTER TABLE `tenancy_agreements`
  ADD PRIMARY KEY (`agreement_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `idx_tenant` (`tenant_id`),
  ADD KEY `idx_flat` (`flat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  ADD PRIMARY KEY (`quote_id`),
  ADD KEY `expense_id` (`expense_id`);

--
-- Indexes for table `viewing_bookings`
--
ALTER TABLE `viewing_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `idx_flat` (`flat_id`),
  ADD KEY `idx_date` (`visit_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenity_bookings`
--
ALTER TABLE `amenity_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_ledgers`
--
ALTER TABLE `expense_ledgers`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flats`
--
ALTER TABLE `flats`
  MODIFY `flat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flat_applications`
--
ALTER TABLE `flat_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flat_owner_users`
--
ALTER TABLE `flat_owner_users`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gate_visitors`
--
ALTER TABLE `gate_visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_users`
--
ALTER TABLE `guest_users`
  MODIFY `guest_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_complaints`
--
ALTER TABLE `maintenance_complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owner_verifications`
--
ALTER TABLE `owner_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `society_amenities`
--
ALTER TABLE `society_amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `society_funds`
--
ALTER TABLE `society_funds`
  MODIFY `fund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenancy_agreements`
--
ALTER TABLE `tenancy_agreements`
  MODIFY `agreement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `viewing_bookings`
--
ALTER TABLE `viewing_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_users_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `admin_users` (`admin_id`);

--
-- Constraints for table `amenity_bookings`
--
ALTER TABLE `amenity_bookings`
  ADD CONSTRAINT `amenity_bookings_ibfk_1` FOREIGN KEY (`amenity_id`) REFERENCES `society_amenities` (`amenity_id`),
  ADD CONSTRAINT `amenity_bookings_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `expense_ledgers`
--
ALTER TABLE `expense_ledgers`
  ADD CONSTRAINT `expense_ledgers_ibfk_1` FOREIGN KEY (`fund_id`) REFERENCES `society_funds` (`fund_id`),
  ADD CONSTRAINT `expense_ledgers_ibfk_2` FOREIGN KEY (`created_by_treasurer`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `expense_ledgers_ibfk_3` FOREIGN KEY (`approved_by_president`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `flats`
--
ALTER TABLE `flats`
  ADD CONSTRAINT `flats_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `flat_applications`
--
ALTER TABLE `flat_applications`
  ADD CONSTRAINT `flat_applications_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `flat_applications_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `flat_owner_users`
--
ALTER TABLE `flat_owner_users`
  ADD CONSTRAINT `flat_owner_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `gate_visitors`
--
ALTER TABLE `gate_visitors`
  ADD CONSTRAINT `gate_visitors_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `gate_visitors_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `guest_users`
--
ALTER TABLE `guest_users`
  ADD CONSTRAINT `guest_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`agreement_id`) REFERENCES `tenancy_agreements` (`agreement_id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `maintenance_complaints`
--
ALTER TABLE `maintenance_complaints`
  ADD CONSTRAINT `maintenance_complaints_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `maintenance_complaints_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `owner_verifications`
--
ALTER TABLE `owner_verifications`
  ADD CONSTRAINT `owner_verifications_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `owner_verifications_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`);

--
-- Constraints for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD CONSTRAINT `staff_attendance_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `system_backups`
--
ALTER TABLE `system_backups`
  ADD CONSTRAINT `system_backups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tenancy_agreements`
--
ALTER TABLE `tenancy_agreements`
  ADD CONSTRAINT `tenancy_agreements_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `tenancy_agreements_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tenancy_agreements_ibfk_3` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`role_id`);

--
-- Constraints for table `vendor_quotes`
--
ALTER TABLE `vendor_quotes`
  ADD CONSTRAINT `vendor_quotes_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expense_ledgers` (`expense_id`);

--
-- Constraints for table `viewing_bookings`
--
ALTER TABLE `viewing_bookings`
  ADD CONSTRAINT `viewing_bookings_ibfk_1` FOREIGN KEY (`flat_id`) REFERENCES `flats` (`flat_id`),
  ADD CONSTRAINT `viewing_bookings_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
