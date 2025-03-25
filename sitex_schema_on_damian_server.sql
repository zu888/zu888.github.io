-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2023 at 09:34 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `3048`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE `checkins` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `checkin_datetime` datetime NOT NULL,
  `checkout_datetime` datetime DEFAULT NULL,
  `email_sent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `company_type` enum('Builder','Contractor','Subcontractor','Supplier') DEFAULT NULL,
  `abn` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address_no` varchar(10) NOT NULL,
  `address_street` varchar(50) NOT NULL,
  `address_suburb` varchar(50) NOT NULL,
  `address_state` varchar(50) NOT NULL,
  `address_postcode` varchar(20) NOT NULL,
  `address_country` varchar(50) NOT NULL,
  `contact_name` varchar(50) NOT NULL,
  `contact_email` varchar(320) NOT NULL,
  `contact_phone` varchar(15) NOT NULL,
  `passcode` varchar(10) NOT NULL COMMENT 'company passcode'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `admin_id`, `company_type`, `abn`, `name`, `address_no`, `address_street`, `address_suburb`, `address_state`, `address_postcode`, `address_country`, `contact_name`, `contact_email`, `contact_phone`, `passcode`) VALUES
(1, 2, 'Builder', 12345678910, 'Building Company', '4', 'Building Court', 'Clayton', '4', '3168', 'Australia', 'Martin Example', 'martin@example.com', '1234567890', 'OifDafQ8t1'),
(2, 3, 'Contractor', 12345678910, 'Contractor Company', '99', 'Contractor Avenue', 'Clayton', '4', '3168', 'Australia', 'Contractor Example', 'Contractor42@gmail.com', '0412345678', 'l#r2d(8e');

-- --------------------------------------------------------

--
-- Table structure for table `companies_projects`
--

CREATE TABLE `companies_projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `status` enum('Engaged','Disengaged') NOT NULL DEFAULT 'Engaged' COMMENT 'if a company is engaged with a project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies_projects`
--

INSERT INTO `companies_projects` (`id`, `company_id`, `project_id`, `status`) VALUES
(1, 2, 1, 'Engaged');

-- --------------------------------------------------------

--
-- Table structure for table `companies_users`
--

CREATE TABLE `companies_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `is_company_admin` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `inducted` datetime DEFAULT NULL,
  `status` enum('Engaged','Disengaged','Owner') NOT NULL DEFAULT 'Engaged' COMMENT 'if a user is engaged with a company'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies_users`
--

INSERT INTO `companies_users` (`id`, `company_id`, `user_id`, `is_company_admin`, `confirmed`, `inducted`, `status`) VALUES
(1, 1, 2, 1, 1, NULL, 'Owner');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `details` varchar(250) DEFAULT NULL,
  `document_no` varchar(50) DEFAULT NULL,
  `document_type` enum('SWMS','Induction','Site Policy','High Risk Work License','Insurance','Card','Other','Logbook') NOT NULL,
  `worker_accessible` tinyint(1) DEFAULT 1,
  `class` varchar(50) DEFAULT NULL,
  `issuer` varchar(50) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `requires_signature` tinyint(1) DEFAULT 0,
  `declaration_text` varchar(500) DEFAULT 'I acknowledge and agree to all terms established in the document.',
  `related_project_id` int(10) UNSIGNED DEFAULT NULL,
  `related_user_id` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_user_id` int(10) UNSIGNED DEFAULT NULL,
  `related_company_id` int(10) UNSIGNED DEFAULT NULL,
  `auth_type` int(11) NOT NULL DEFAULT 0,
  `auth_value` varchar(200) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `comment` text DEFAULT NULL COMMENT 'reason why you rejected documents',
  `extension` varchar(10) NOT NULL COMMENT 'file extension type',
  `archived` tinyint(1) NOT NULL COMMENT 'Archive document',
  `document_relation` varchar(20) NOT NULL COMMENT 'Tells whether the upload is for personal or project-specific induction document.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `name`, `details`, `document_no`, `document_type`, `worker_accessible`, `class`, `issuer`, `issue_date`, `expiry_date`, `requires_signature`, `declaration_text`, `related_project_id`, `related_user_id`, `uploaded_user_id`, `related_company_id`, `auth_type`, `auth_value`, `status`, `comment`, `extension`, `archived`, `document_relation`) VALUES
(1, 'SWMS', 'Building Company SWMS', 'SWMS00001', 'Induction', 1, NULL, 'Builder Example', '2024-11-12', '2024-05-22', 0, 'I acknowledge and agree to all terms established in the document.', 1, NULL, 2, NULL, 5, 'Induction Document', 'Approved', NULL, 'pdf', 0, 'project'),
(2, 'SWMS Contractor', 'SWMS for Contractor Company Example', 'SWMS00001', 'SWMS', 0, NULL, 'Contractor Example', '2023-10-12', '2024-11-14', 0, 'I acknowledge and agree to all terms established in the document.', NULL, NULL, 3, 2, 0, NULL, 'Approved', NULL, 'pdf', 0, 'company');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `equipment_type` enum('Other','Induction') DEFAULT NULL,
  `is_licensed` tinyint(1) NOT NULL DEFAULT 0,
  `hired_from_date` date DEFAULT NULL,
  `hired_until_date` date DEFAULT NULL,
  `worker_accessible` tinyint(4) DEFAULT 1,
  `related_project_id` int(10) UNSIGNED DEFAULT NULL,
  `related_company_id` int(10) UNSIGNED DEFAULT NULL,
  `related_user_id` int(10) UNSIGNED DEFAULT NULL,
  `auth_type` int(11) NOT NULL,
  `auth_value` varchar(50) DEFAULT NULL,
  `image` varchar(300) DEFAULT NULL COMMENT 'Location of image of equipment',
  `image_date` date DEFAULT NULL COMMENT 'Date of image upload',
  `review_status` enum('Pending','Accepted','Rejected') NOT NULL,
  `review_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inductions`
--

CREATE TABLE `inductions` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `inducted_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_type` enum('Construction','Renovation','De-fit','Other') NOT NULL DEFAULT 'Construction',
  `name` varchar(50) NOT NULL,
  `permit_no` varchar(50) NOT NULL,
  `builder_id` int(10) UNSIGNED DEFAULT NULL,
  `client_name` varchar(50) NOT NULL,
  `client_email` varchar(250) NOT NULL,
  `client_phone` varchar(15) NOT NULL,
  `surveyor_name` varchar(50) NOT NULL,
  `surveyor_email` varchar(250) NOT NULL,
  `surveyor_phone` varchar(15) NOT NULL,
  `start_date` date DEFAULT NULL,
  `est_completion_date` date DEFAULT NULL,
  `status` enum('Pending','Active','Cancelled','Complete','Archived') NOT NULL DEFAULT 'Active',
  `completion_date` date DEFAULT NULL,
  `address_no` varchar(10) NOT NULL,
  `address_street` varchar(50) NOT NULL,
  `address_suburb` varchar(50) NOT NULL,
  `address_state` varchar(50) NOT NULL,
  `address_postcode` varchar(20) NOT NULL,
  `address_country` varchar(50) NOT NULL,
  `passcode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_type`, `name`, `permit_no`, `builder_id`, `client_name`, `client_email`, `client_phone`, `surveyor_name`, `surveyor_email`, `surveyor_phone`, `start_date`, `est_completion_date`, `status`, `completion_date`, `address_no`, `address_street`, `address_suburb`, `address_state`, `address_postcode`, `address_country`, `passcode`) VALUES
(1, 'Construction', 'Roadworks', 'COS123456', 2, 'Danny Pearson', 'danny.pearson@parliament.vic.gov.au', '0412345678', 'Steven Surveyor', 'steven@surveyor.com', '0412345679', '2023-10-12', '2023-11-27', 'Active', NULL, '6', 'Surveyor Street', 'Clayton', '4', '3168', 'Australia', 'Z0Zf1HL6fm');

-- --------------------------------------------------------

--
-- Table structure for table `projects_documents`
--

CREATE TABLE `projects_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL COMMENT 'project id',
  `document_id` int(10) UNSIGNED NOT NULL COMMENT 'document id',
  `company_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'company id',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'user id',
  `status` enum('Pending','Reviewed','Rejected','') NOT NULL DEFAULT 'Pending' COMMENT 'document status',
  `auth_type` int(11) DEFAULT NULL COMMENT 'authentication type for add document checkbox',
  `auth_value` varchar(200) DEFAULT NULL COMMENT 'authentication types for user roles',
  `comment` text DEFAULT NULL COMMENT 'reason why a doucment is rejected'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Project based documentation approval';

--
-- Dumping data for table `projects_documents`
--

INSERT INTO `projects_documents` (`id`, `project_id`, `document_id`, `company_id`, `user_id`, `status`, `auth_type`, `auth_value`, `comment`) VALUES
(1, 1, 1, NULL, 2, 'Pending', 5, 'Induction Document', NULL),
(2, 1, 2, 2, 3, 'Pending', 3, 'Admin', NULL),
(3, 1, 2, 2, 7, 'Reviewed', 1, 'Worker Acknowledgement', NULL),
(4, 1, 1, NULL, 7, 'Reviewed', 1, 'Worker Acknowledgement', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects_users`
--

CREATE TABLE `projects_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` enum('Engaged','Disengaged','Co-Manager') NOT NULL DEFAULT 'Engaged' COMMENT 'if a user is engaged with a project',
  `company_id` int(10) NOT NULL,
  `inducted_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects_users`
--

INSERT INTO `projects_users` (`id`, `project_id`, `user_id`, `status`, `company_id`, `inducted_date`) VALUES
(1, 1, 4, 'Engaged', 2, NULL),
(2, 1, 5, 'Engaged', 2, NULL),
(3, 1, 6, 'Engaged', 2, NULL),
(4, 1, 7, 'Engaged', 2, NULL),
(5, 1, 8, 'Engaged', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED DEFAULT NULL,
  `company_id` int(10) UNSIGNED DEFAULT NULL,
  `request_type` varchar(50) NOT NULL,
  `request_text` varchar(250) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `removal_status` int(10) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL COMMENT 'Rejection reason',
  `company_id_worker` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE `signatures` (
  `id` int(10) UNSIGNED NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `signed_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcontracts`
--

CREATE TABLE `subcontracts` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'item id',
  `project_id` int(10) UNSIGNED NOT NULL COMMENT 'project associated with subcontract',
  `parent_company_id` int(10) UNSIGNED NOT NULL COMMENT 'parent id',
  `child_worker_id` int(10) UNSIGNED NOT NULL COMMENT 'child worker id',
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('Admin','Builder','Client','On-site Worker','Contractor','Subcontractor','Consultant','Visitor') NOT NULL,
  `status` enum('Pending','Verified','Deactivated') NOT NULL DEFAULT 'Pending',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address_no` varchar(10) NOT NULL,
  `address_street` varchar(50) NOT NULL,
  `address_suburb` varchar(50) NOT NULL,
  `address_state` varchar(50) NOT NULL,
  `address_postcode` varchar(20) NOT NULL,
  `address_country` varchar(50) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(128) NOT NULL,
  `phone_mobile` varchar(15) DEFAULT NULL,
  `phone_office` varchar(15) DEFAULT NULL,
  `emergency_name` varchar(100) NOT NULL,
  `emergency_relationship` varchar(50) NOT NULL,
  `emergency_phone` varchar(15) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Profile picture'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `is_admin`, `role`, `status`, `first_name`, `last_name`, `address_no`, `address_street`, `address_suburb`, `address_state`, `address_postcode`, `address_country`, `email`, `password`, `phone_mobile`, `phone_office`, `emergency_name`, `emergency_relationship`, `emergency_phone`, `image`) VALUES
(1, 1, 'Admin', 'Verified', 'Damian', 'Marchese', '1', 'Admin Street', 'Templestowe', '4', '3106', 'Australia', 'sitex@u22s1010.monash-ie.me', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Test', 'Friend', '0412222223', NULL),
(2, 0, 'Builder', 'Verified', 'Builder', 'Example', '2', 'Builder Street', 'Templestowe', '4', '3106', 'Australia', 'Builder42@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Test', 'Friend', '0412222223', NULL),
(3, 0, 'Contractor', 'Verified', 'Contractor', 'Example', '3', 'Contractor Street', 'Templestowe', '4', '3106', 'Australia', 'Contractor42@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Test', 'Friend', '0412222223', NULL),
(4, 0, 'On-site Worker', 'Verified', 'Worker', 'Example', '4', 'Worker Street', 'Templestowe', '4', '3106', 'Australia', 'Worker42@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL),
(5, 0, 'On-site Worker', 'Verified', 'WorkerFive', 'ExampleFive', '5', 'Worker Street', 'Templestowe', '5', '3106', 'Australia', 'Worker42.1@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL),
(6, 0, 'On-site Worker', 'Verified', 'WorkerSix', 'ExampleSix', '6', 'Worker Street', 'Templestowe', '6', '3106', 'Australia', 'Worker42.2@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL),
(7, 0, 'On-site Worker', 'Verified', 'WorkerSeven', 'ExampleSeven', '7', 'Worker Street', 'Templestowe', '7', '3106', 'Australia', 'Worker42.3@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL),
(8, 0, 'On-site Worker', 'Verified', 'WorkerEight', 'ExampleEight', '8', 'Worker Street', 'Templestowe', '8', '3106', 'Australia', 'Worker42.4@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL),
(9, 0, 'On-site Worker', 'Verified', 'WorkerNine', 'ExampleNine', '9', 'Worker Street', 'Templestowe', '9', '3106', 'Australia', 'Worker42.5@gmail.com', '$2y$10$G8N8D4Vc/LdAgLpuAsvmI.rj6YXxqtGl/Sy3A9PrTBzHg9goL0gIu', '0412222222', '0412222222', 'Martin Example', 'Friend', '0412222222', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_agreements`
--

CREATE TABLE `users_agreements` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `project_id` int(10) UNSIGNED NOT NULL,
  `document_id` int(10) UNSIGNED NOT NULL,
  `agreed_at` timestamp NULL DEFAULT current_timestamp(),
  `agreement_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkins`
--
ALTER TABLE `checkins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checkins_projects_fk` (`project_id`),
  ADD KEY `checkins_users_fk` (`user_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_fk` (`admin_id`);

--
-- Indexes for table `companies_projects`
--
ALTER TABLE `companies_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_p_companies_fk` (`company_id`),
  ADD KEY `c_p_projects_fk` (`project_id`);

--
-- Indexes for table `companies_users`
--
ALTER TABLE `companies_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_u_companies_fk` (`company_id`),
  ADD KEY `c_u_users_fk` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_project_fk` (`related_project_id`),
  ADD KEY `documents_user_fk` (`related_user_id`),
  ADD KEY `documents_company_fk` (`related_company_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `related_company_id` (`related_company_id`),
  ADD KEY `related_project_id` (`related_project_id`),
  ADD KEY `related_user_id` (`related_user_id`);

--
-- Indexes for table `inductions`
--
ALTER TABLE `inductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inductions_project_fk` (`project_id`),
  ADD KEY `inductions_user_fk` (`user_id`),
  ADD KEY `inductions_company_fk` (`company_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_client_fk` (`builder_id`);

--
-- Indexes for table `projects_documents`
--
ALTER TABLE `projects_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_documents_fk` (`project_id`),
  ADD KEY `document_project_fk` (`document_id`),
  ADD KEY `company_document_fk` (`company_id`),
  ADD KEY `user_document_fk` (`user_id`);

--
-- Indexes for table `projects_users`
--
ALTER TABLE `projects_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_users_project_fk` (`project_id`),
  ADD KEY `projects_users_user_fk` (`user_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requests_user_fk` (`user_id`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `d_u_documents_fk` (`document_id`),
  ADD KEY `d_u_users_fk` (`user_id`);

--
-- Indexes for table `subcontracts`
--
ALTER TABLE `subcontracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_company_fk` (`parent_company_id`),
  ADD KEY `child_worker_fk` (`child_worker_id`),
  ADD KEY `project_sub_fk` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users_agreements`
--
ALTER TABLE `users_agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_agreements_ibfk_1` (`user_id`),
  ADD KEY `users_agreements_ibfk_2` (`project_id`),
  ADD KEY `users_agreements_ibfk_3` (`document_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkins`
--
ALTER TABLE `checkins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies_projects`
--
ALTER TABLE `companies_projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companies_users`
--
ALTER TABLE `companies_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inductions`
--
ALTER TABLE `inductions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects_documents`
--
ALTER TABLE `projects_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `projects_users`
--
ALTER TABLE `projects_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcontracts`
--
ALTER TABLE `subcontracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'item id';

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users_agreements`
--
ALTER TABLE `users_agreements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkins`
--
ALTER TABLE `checkins`
  ADD CONSTRAINT `checkins_projects_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkins_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies_projects`
--
ALTER TABLE `companies_projects`
  ADD CONSTRAINT `c_p_companies_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `c_p_projects_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies_users`
--
ALTER TABLE `companies_users`
  ADD CONSTRAINT `c_u_companies_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `c_u_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_company_fk` FOREIGN KEY (`related_company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_project_fk` FOREIGN KEY (`related_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_user_fk` FOREIGN KEY (`related_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`related_company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`related_project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `equipment_ibfk_3` FOREIGN KEY (`related_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inductions`
--
ALTER TABLE `inductions`
  ADD CONSTRAINT `inductions_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inductions_project_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inductions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `project_client_fk` FOREIGN KEY (`builder_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects_documents`
--
ALTER TABLE `projects_documents`
  ADD CONSTRAINT `company_document_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_project_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_documents_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_document_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `projects_users`
--
ALTER TABLE `projects_users`
  ADD CONSTRAINT `projects_users_project_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `projects_users_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `d_u_documents_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `d_u_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcontracts`
--
ALTER TABLE `subcontracts`
  ADD CONSTRAINT `child_worker_fk` FOREIGN KEY (`child_worker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `parent_company_fk` FOREIGN KEY (`parent_company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `project_sub_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `users_agreements`
--
ALTER TABLE `users_agreements`
  ADD CONSTRAINT `users_agreements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_agreements_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_agreements_ibfk_3` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
