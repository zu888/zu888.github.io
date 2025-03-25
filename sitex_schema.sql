-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2023 at 02:38 PM
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
-- Database: `fit3048`
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

--
-- Dumping data for table `checkins`
--

INSERT INTO `checkins` (`id`, `project_id`, `user_id`, `checkin_datetime`, `checkout_datetime`, `email_sent`) VALUES
(1, 1, 3, '2022-07-18 14:30:17', NULL, 0),
(2, 1, 3, '2022-07-18 12:54:02', '2022-07-18 18:42:44', 0),
(3, 1, 3, '2022-08-11 14:54:02', NULL, 0),
(4, 1, 3, '2022-08-14 09:54:02', NULL, 0);

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
(1, 1, 'Builder', 404, 'Cosmic Property', '19', 'Placeholder Street', 'Mount Waverley', 'Victoria', '3149', 'Australia', 'Receptionist Joe', 'joe@fake.com', '0422999999', ''),
(2, 30, 'Contractor', 40401, 'Joe Outsourcing', '2', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'Receptionist Jane', 'jane@fake.com', '0422999998', ''),
(3, 5, 'Builder', 4013230, 'DanConstructions', '222', 'Placeholder Street', 'Mount Waverley', 'Victoria', '3149', 'Australia', 'Receptionist Jack', 'jack@fake.com', '0422999978', ''),
(10, 24, 'Contractor', 11111111111, 'TestCompany', '145', 'sadasd', 'ad', '6', '3168', 'Australia', 'Alice', '214rewqrw@gmail.com', '231241324123', '7IUwU616ip'),
(11, 25, 'Contractor', 12345678902, 'BuilderCompany', '134', 'dasd', 'sdf', '2', '1234', 'adfa', 'dfa', 'sdfa@gmail.com', '123456789098', ''),
(12, 37, 'Contractor', 12345678987, 'Renovate Construction', '304', 'Britons road', 'Frankston ', '4', '2908', 'Australia', 'Tom', 'Tom@gmail.com', '1234253647', 's7yVrF5wSz'),
(13, 38, 'Contractor', 32141234213, 'Magic Hammer', '300', 'innovation street', 'Caulfield ', '4', '3092', 'Australia', 'Alice', 'alice@gmail.com', '0438201432', 'WIFVdQ6pOw'),
(14, 39, 'Contractor', 11111111111, 'Rock Foundation', '63', 'Thomas Lane', 'COLLINGWOOD', '4', '3066', 'Australia', 'Ryan Rutledge', 'Ryan@gmail.com', '0489732425', 'M4unhtxeFa'),
(15, 21, 'Contractor', 11111111111, 'company_test', 'Unit 1', 'something road', 'suburb', '3', '1234', 'Australia', 'contractor', 'contractor@mail.com', '0412345678', 'yMZweylDyV'),
(16, 42, 'Builder', 12335345234, 'Dream construction ', '123', 'north road', 'Elsternwick', '4', '3158', 'Australia', 'Alice', 'Alice@gmail.com', '0423423234', 'FTyRcmZ9ML'),
(17, 44, 'Builder', 85634200598, 'ProBuild Solutions', '89', 'Millicent Drive', 'Bandon Grove', '0', '2420', 'Australia', 'Sophia Collins', 'Sophia@gmail.com', '0420945934', 'cUmcmlAd1l'),
(18, 45, 'Contractor', 44891200231, 'Golden Hammer', '83', 'Flinstone Drive', 'Bagdad North', '3', '7030', 'Australia', 'William Carter', 'William@gmail.com', '0402347321', 'SsyHOt3Y3Z');

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
(1, 1, 1, 'Engaged'),
(2, 1, 2, 'Engaged'),
(3, 2, 19, 'Engaged'),
(4, 10, 20, 'Engaged'),
(5, 15, 21, 'Engaged'),
(6, 12, 22, 'Engaged'),
(7, 13, 22, 'Engaged'),
(8, 14, 22, 'Engaged'),
(9, 18, 23, 'Engaged'),
(10, 12, 23, 'Engaged'),
(11, 13, 23, 'Engaged'),
(12, 14, 23, 'Engaged'),
(13, 2, 23, 'Engaged'),
(14, 18, 24, 'Engaged'),
(15, 12, 24, 'Engaged');

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
(1, 1, 1, 1, 1, '2022-07-18 18:42:44', 'Owner'),
(2, 2, 2, 1, 1, '2022-07-18 18:42:44', 'Engaged'),
(3, 1, 3, 0, 1, '2022-07-18 18:42:44', 'Engaged'),
(4, 1, 4, 0, 0, NULL, 'Engaged'),
(5, 3, 5, 1, 1, NULL, 'Owner'),
(6, 2, 6, 0, 1, NULL, 'Engaged'),
(7, 3, 7, 1, 1, NULL, 'Engaged'),
(11, 10, 24, 1, 1, NULL, 'Owner'),
(18, 11, 25, 1, 1, NULL, 'Owner'),
(20, 10, 23, 0, 1, NULL, 'Engaged'),
(21, 12, 37, 1, 1, NULL, 'Owner'),
(22, 13, 38, 1, 1, NULL, 'Owner'),
(23, 14, 39, 1, 1, NULL, 'Owner'),
(24, 15, 21, 1, 1, NULL, 'Owner'),
(25, 16, 42, 1, 1, NULL, 'Owner'),
(26, 17, 44, 1, 1, NULL, 'Owner'),
(27, 18, 45, 1, 1, NULL, 'Owner');

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
(1, 'CorpOffice 142 Induction doc', NULL, 'ABC123', 'Induction', 1, NULL, NULL, '2022-05-03', '2023-05-03', 1, 'I acknowledge and agree to all terms established in the document.', 1, NULL, NULL, NULL, 0, NULL, 'Pending', NULL, '', 0, ''),
(2, 'CorpOffice 142 2nd induction doc', NULL, 'ABC1234', 'Induction', 1, NULL, NULL, '2022-05-04', '2023-05-04', 1, 'I acknowledge and agree to all terms established in the document.', 1, NULL, NULL, NULL, 0, NULL, 'Pending', NULL, '', 0, ''),
(3, 'Working with Children Check', NULL, 'AB4', 'Other', 1, NULL, NULL, '2022-05-07', '2023-05-08', 0, 'I acknowledge and agree to all terms established in the document.', NULL, 1, NULL, NULL, 0, NULL, 'Pending', NULL, '', 0, ''),
(4, '32423', '234234', '23423', 'Induction', 1, NULL, '2423', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', 1, NULL, NULL, NULL, 0, NULL, 'Pending', NULL, '', 0, ''),
(5, '3424', '234234', '234234', 'Induction', 1, NULL, '324234', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', 1, NULL, NULL, NULL, 2, 'tanxin@163.com;tanxin@164.com', 'Pending', NULL, '', 0, ''),
(6, '3333', '444', 'dfasdf', 'Induction', 1, NULL, '3434', '2023-04-12', '2023-04-20', 0, 'I acknowledge and agree to all terms established in the document.', 1, NULL, NULL, NULL, 1, NULL, 'Pending', NULL, '', 0, ''),
(7, '333', '333', '333', 'Induction', 1, NULL, '333', '2023-04-19', '2023-04-28', 0, 'I acknowledge and agree to all terms established in the document.', 2, NULL, NULL, NULL, 1, NULL, 'Pending', NULL, '', 0, ''),
(8, '微波炉', '333', '333', 'Induction', 1, NULL, '33', '2023-04-19', '2023-04-21', 0, 'I acknowledge and agree to all terms established in the document.', 2, NULL, NULL, NULL, 1, NULL, 'Pending', NULL, '', 0, ''),
(9, '33', '33', '33', 'Induction', 1, NULL, '33', '2023-04-19', '2023-04-20', 0, 'I acknowledge and agree to all terms established in the document.', 2, NULL, NULL, NULL, 1, NULL, 'Pending', NULL, '', 0, ''),
(10, '33', '33', '33', 'Induction', 1, NULL, '33', '2023-04-11', '2023-04-20', 0, 'I acknowledge and agree to all terms established in the document.', 2, NULL, NULL, NULL, 1, NULL, 'Pending', NULL, '', 0, ''),
(72, 'test', 'Induction_test', 'Induction_test', 'Other', 1, NULL, 'test', NULL, NULL, 1, 'I acknowledge and agree to all terms established in the document.', 6, NULL, 21, NULL, 1, NULL, 'Approved', NULL, '', 0, ''),
(73, 'test', 'insurance', 'insurance', 'Insurance', 1, NULL, 'insurance', NULL, '2023-08-31', 0, 'I acknowledge and agree to all terms established in the document.', NULL, 21, 21, NULL, 0, NULL, 'Pending', NULL, '', 0, ''),
(81, 'tttt', 'tttt', 'tttt', 'Card', 1, NULL, 'tttt', NULL, '2023-08-30', 0, 'I acknowledge and agree to all terms established in the document.', NULL, NULL, 30, 2, 3, NULL, 'Rejected', 'rejected', 'pdf', 0, ''),
(89, 'Renovate Construction SWMS', 'Renovate Construction SWMS', 'COS123', 'SWMS', 1, NULL, 'LEO ', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', NULL, NULL, 37, 12, 3, 'Builder,On-site Worker,Contractor', 'Approved', NULL, 'pdf', 0, 'company'),
(90, 'Magic Hammer SWMS', 'Magic Hammer SWMS', 'cos34453454', 'SWMS', 1, NULL, 'Leo', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', NULL, NULL, 38, 13, 3, 'Builder,On-site Worker,Contractor', 'Approved', NULL, 'pdf', 0, 'company'),
(91, 'Rock Foundation SWMS', 'Rock Foundation SWMS', 'cos34525', 'SWMS', 1, NULL, 'Leo', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', NULL, NULL, 39, 14, 3, 'Builder,On-site Worker,Contractor', 'Approved', NULL, 'pdf', 0, 'company'),
(92, 'Project Induction form', 'Project Induction form', 'cos1243', 'Induction', 1, NULL, 'Leo', NULL, NULL, 0, 'I acknowledge and agree to all terms established in the document.', 22, NULL, 42, NULL, 3, 'Builder,On-site Worker,Contractor', 'Pending', NULL, 'pdf', 0, 'project');

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

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `description`, `equipment_type`, `is_licensed`, `hired_from_date`, `hired_until_date`, `worker_accessible`, `related_project_id`, `related_company_id`, `related_user_id`, `auth_type`, `auth_value`, `image`, `image_date`, `review_status`, `review_reason`) VALUES
(6, '微波炉', NULL, 'Induction', 1, '2023-04-19', '2023-04-19', 1, 1, NULL, NULL, 2, 'tanxin@163.com;tanxin@164.com', NULL, '2023-09-08', 'Pending', ''),
(7, '333', NULL, 'Induction', 1, '2023-04-18', '2023-04-21', 1, 1, NULL, NULL, 1, NULL, NULL, '2023-09-08', 'Pending', ''),
(8, '234324', NULL, 'Induction', 1, '2023-04-04', '2023-04-27', 1, 1, NULL, NULL, 3, 'Admin,Builder,Client,On-site Worker,Contractor', NULL, '2023-09-08', 'Pending', ''),
(9, '3434343434', NULL, 'Induction', 1, '2023-04-04', '2023-04-14', 1, 1, NULL, NULL, 3, 'Admin,Builder,Client,On-site Worker', NULL, '2023-09-08', 'Pending', ''),
(10, '3334', NULL, 'Induction', 1, '2023-04-12', '2023-04-13', 1, 2, NULL, NULL, 1, NULL, NULL, '2023-09-08', 'Pending', ''),
(16, '', NULL, NULL, 0, NULL, NULL, 1, 19, NULL, NULL, 0, NULL, NULL, '2023-09-08', 'Pending', ''),
(17, '', NULL, NULL, 0, NULL, NULL, 1, 19, NULL, NULL, 0, NULL, NULL, '2023-09-08', 'Pending', '');

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

--
-- Dumping data for table `inductions`
--

INSERT INTO `inductions` (`id`, `project_id`, `user_id`, `company_id`, `inducted_date`) VALUES
(1, 1, 3, 1, NULL);

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
(1, 'Construction', 'Corporate Office 142', '10249', 1, 'DC Ward', 'd.c.ward@outlook.com', '0411222222', 'DC Ward', 'd.c.ward@outlook.com', '0411222222', '2022-08-09', '2023-05-04', 'Active', NULL, '123', 'Worksite Street', 'Melbourne', 'Victoria', '3000', 'Australia', ''),
(2, 'Construction', 'Rustic Cabin', '9494949', 1, 'George Foreman', 'george@mail.com', '0411222227', 'DC Ward', 'd.c.ward@outlook.com', '0411222222', '2020-05-02', '2022-01-04', 'Complete', NULL, '123', 'Nice Avenue', 'Melbourne', 'Victoria', '3000', 'Australia', ''),
(6, 'Construction', 'test', 'test', 20, 'Client1', 'Client1@mail.com', '0412345678', 'Surveyor1', 'Surveyor1@mail.com', '0412345678', '2023-10-10', NULL, 'Cancelled', NULL, '1', 'something road', 'suburb', '3', '1234', 'Australia', ''),
(7, 'Construction', '1 project', '45325', 25, 'gdsfg', 'dsfgs@gmail.com', '1234567890', 'fdgsd', 'dsafasd@gmail.com', '1234567890', '2023-08-22', '2023-08-30', 'Active', NULL, '123', 'clayton', 'clayton', '5', '1234', 'Australia', 'WXuNmcxKJq'),
(18, 'Construction', '11project', 'qeqweqw', 25, 'Alice', 'adfsa@gmail.com', '1234567890', 'will', 'rew@gmail.com', '0421231232', '2025-04-09', '2023-08-10', 'Active', NULL, '144', 'clayton', 'clayton', '5', '1234', 'Australia', 'GPYwpr1KvS'),
(19, 'Construction', 'Example project', '345678903', 29, 'Ex Ample', 'example@example.com', '3120939120', 'For Instance', 'forinstance@example.com', '3120931209', '2023-09-30', '2023-11-16', 'Active', NULL, '32', 'Test Street', 'Clayton', '4', '3168', 'Australia', 'a4Jnw3lMnC'),
(20, 'Construction', 'Ultra Tower', 'COS12342544', 29, 'Tom', 'Tom@gmail.com', '0429802342', 'Cody', 'cody@gmail.com', '0490823034', '2023-08-25', '2025-08-31', 'Active', NULL, '40', 'Bayview Close', 'DUMGREE', '1', '4715', 'Australia', 'bdmXz8cxMA'),
(21, 'Construction', 'jpeg', 'test', 20, 'Client1', 'Client1@mail.com', '0412345678', 'Surveyor1', 'Surveyor1@mail.com', '0412345678', NULL, NULL, 'Active', NULL, 'Unit 1', 'c road13', 'Clayton', '6', '1234', 'Australia', 'kVAUZz83dg'),
(22, 'Construction', 'SkyRise Gardens', 'cos1234', 42, 'Leo', 'Leo@gmail.com', '0428088812', 'Will', 'Will@gmail.com', '0424205438', '2023-09-21', '2025-09-25', 'Active', NULL, '144', 'East Street', 'South Yarra', '4', '3141', 'Australia', 'An8bQ9s32x'),
(23, 'Construction', 'Skyline Residences', 'PM034952', 44, 'Ava Parker', 'Ava@gmail.com', '0420983431', 'Ethan Walker', 'Ethan@gmail.com', '0409283742', '2023-09-26', NULL, 'Active', NULL, '19', 'Adavale Road', 'Towrang', '0', '2580', 'Australia', 'aXIFGzBeAn'),
(24, 'Construction', 'Urban Oasis', 'PM342323', 44, 'Aiden Johnson', 'Aiden@gmail.com', '0492342347', 'Harper White', 'Harper@gmail.com', '0409238234', '2023-08-17', '2024-11-16', 'Active', NULL, '30', 'McPherson Road', 'Koetong', '4', '3704', 'Australia', 'G1girlvBBR');

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
(5, 22, 89, 12, 37, 'Reviewed', 3, 'Builder,On-site Worker,Contractor', NULL),
(6, 22, 90, 13, 38, 'Reviewed', 3, 'Builder,On-site Worker,Contractor', NULL),
(7, 22, 91, 14, 39, 'Reviewed', 3, 'Builder,On-site Worker,Contractor', NULL),
(8, 22, 89, 12, 32, 'Reviewed', 1, 'Worker Acknowledgement', NULL),
(9, 22, 89, 12, 32, 'Reviewed', 1, 'Worker Acknowledgement', NULL),
(10, 22, 89, 12, 32, 'Reviewed', 1, 'Worker Acknowledgement', NULL);

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
(2, 1, 7, 'Engaged', 0, NULL),
(5, 6, 21, 'Disengaged', 0, NULL),
(6, 19, 3, 'Engaged', 0, NULL),
(7, 19, 7, 'Engaged', 0, NULL),
(8, 19, 30, 'Engaged', 0, NULL);

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

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `project_id`, `company_id`, `request_type`, `request_text`, `created_at`, `approved_at`, `removal_status`, `comment`, `company_id_worker`) VALUES
(1, 7, 1, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:44:09', NULL, 0, NULL, NULL),
(2, 7, 2, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:44:11', NULL, 0, NULL, NULL),
(3, 7, 2, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:44:46', NULL, 0, NULL, NULL),
(4, 7, 1, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:45:02', NULL, 0, NULL, NULL),
(5, 7, 2, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:45:08', NULL, 0, NULL, NULL),
(6, 7, 1, NULL, 'Project', 'User is requesting to join a Project ', '2023-04-19 22:48:16', NULL, 0, NULL, NULL),
(13, 21, 5, NULL, 'Project', 'User is requesting to join a Project ', '2023-08-10 13:28:50', NULL, 0, NULL, NULL),
(14, 21, 6, NULL, 'Project', 'User is requesting to join a Project ', '2023-08-10 13:30:01', '2023-08-10 13:30:15', 0, NULL, NULL),
(38, 23, NULL, 10, 'Company', 'User is requesting to join a Company ', '2023-08-12 00:30:45', '2023-08-12 10:33:42', 0, NULL, NULL),
(39, 25, NULL, NULL, 'Builder', 'I am requesting to become a builder', '2023-08-12 00:41:20', NULL, 0, NULL, NULL),
(42, 23, 7, NULL, 'Project', 'User is requesting to join a Project ', '2023-08-12 01:08:09', NULL, 0, NULL, NULL),
(43, 24, 7, NULL, 'Project', 'User is requesting to join a Project ', '2023-08-12 01:13:22', NULL, 0, NULL, NULL),
(44, 23, NULL, 7, 'Company', 'User is requesting to join a Company ', '2023-08-12 10:33:16', '2023-08-12 10:33:31', 0, NULL, NULL),
(45, 32, 19, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-08-19 01:19:38', NULL, 0, NULL, NULL),
(46, 33, 19, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-08-19 01:22:48', NULL, 0, NULL, NULL),
(47, 34, 19, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-08-19 01:24:53', NULL, 0, NULL, NULL),
(48, 35, 19, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-08-19 01:26:53', NULL, 0, NULL, NULL),
(49, 36, 19, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-08-19 01:29:07', NULL, 0, NULL, NULL),
(50, 37, 19, 12, 'Project_Company', 'Company is requesting to join a Project ', '2023-08-19 01:34:36', NULL, 0, NULL, NULL),
(51, 38, 19, 13, 'Project_Company', 'Company is requesting to join a Project ', '2023-08-19 01:38:58', NULL, 0, NULL, NULL),
(52, 39, 19, 14, 'Project_Company', 'Company is requesting to join a Project ', '2023-08-19 01:44:14', NULL, 0, NULL, NULL),
(54, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:02:18', NULL, 1, NULL, NULL),
(56, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:03:30', NULL, 1, NULL, NULL),
(57, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:04:25', NULL, 1, NULL, NULL),
(58, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:13:24', NULL, 1, NULL, NULL),
(59, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:15:17', NULL, 1, NULL, NULL),
(60, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:15:49', NULL, 1, NULL, NULL),
(61, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-10 23:16:31', NULL, 1, NULL, NULL),
(62, 24, 20, 10, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-11 08:04:00', '2023-09-11 08:05:15', 0, NULL, NULL),
(63, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-11 08:05:52', NULL, 1, NULL, 10),
(64, 23, 20, NULL, 'Project_Member', 'Worker is requesting to join a Project ', '2023-09-11 08:15:24', NULL, 0, NULL, 10),
(65, 21, 21, 15, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-12 21:24:28', '2023-09-12 21:24:34', 0, NULL, NULL),
(66, 42, NULL, NULL, 'Builder', '123134324234324234324', '2023-09-21 15:24:43', '2023-09-21 15:25:30', 0, NULL, NULL),
(67, 37, 22, 12, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-21 15:28:10', '2023-09-21 15:29:11', 0, NULL, NULL),
(68, 38, 22, 13, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-21 15:28:24', '2023-09-21 15:29:13', 0, NULL, NULL),
(69, 39, 22, 14, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-21 15:28:50', '2023-09-21 15:29:17', 0, NULL, NULL),
(70, 44, NULL, NULL, 'Builder', '12309324', '2023-09-25 14:47:03', '2023-09-25 14:47:32', 0, NULL, NULL),
(71, 45, 23, 18, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 15:45:04', '2023-09-25 15:55:09', 0, NULL, NULL),
(72, 37, 23, 12, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 15:53:03', '2023-09-25 15:55:11', 0, NULL, NULL),
(73, 38, 23, 13, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 15:53:36', '2023-09-25 15:55:13', 0, NULL, NULL),
(74, 39, 23, 14, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 15:54:34', '2023-09-25 15:55:18', 0, NULL, NULL),
(75, 30, 23, 2, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 16:05:13', '2023-09-25 16:07:10', 0, NULL, NULL),
(76, 45, 24, 18, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 16:18:29', '2023-09-25 16:22:59', 0, NULL, NULL),
(77, 37, 24, 12, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 16:21:22', '2023-09-25 16:23:05', 0, NULL, NULL),
(78, 38, 24, 13, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 16:21:38', NULL, 0, NULL, NULL),
(79, 39, 24, 14, 'Project_Company', 'Company/Contractor is requesting to join a Project', '2023-09-25 16:22:03', NULL, 0, NULL, NULL);

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

--
-- Dumping data for table `signatures`
--

INSERT INTO `signatures` (`id`, `document_id`, `user_id`, `signed_datetime`) VALUES
(1, 1, 3, NULL),
(2, 2, 3, NULL);

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

--
-- Dumping data for table `subcontracts`
--

INSERT INTO `subcontracts` (`id`, `project_id`, `parent_company_id`, `child_worker_id`, `description`) VALUES
(3, 19, 2, 3, 'Concreting:\r\n\r\nJoe Outsourcing has subcontracted this job to Rob\'s Insourcing, who in turn subcontracted the job to Lisa\'s Contractors, who further subcontracted the job to Mark\'s Construction Services, who outsourced the job to Carla\'s Builders, who then subcontracted the job to Alex\'s Renovations, who outsourced the job to Steve\'s Developments, who subcontracted the job to Sarah\'s Builders, who in turn subcontracted the job to John\'s Renovations, who outsourced the job to Laura\'s Construction, who then subcontracted the job to Matt\'s Contracting, who outsourced the job to Rachel\'s Construction, who subcontracted the job to David\'s Builders, who then outsourced the job to Emily\'s Contracting, who subcontracted the job to Kevin\'s Construction Services, who outsourced the job to Maria\'s Builders, who in turn subcontracted the job to Patrick\'s Renovations, who further subcontracted the job to Lisa\'s Developments, who outsourced the job to James\'s Contractors, who then subcontracted the job to Walter Workson.');

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
(1, 1, 'Admin', 'Verified', 'Damian', 'Marchese', '1', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'sitex@u22s1010.monash-ie.me', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412222222', '98000000', 'Mark Smith', 'Friend', '0422222222', NULL),
(2, 0, 'Contractor', 'Verified', 'Joe', 'Contractson', '3', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'joe@mail.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412222223', '98000001', 'Mark Smith', 'Friend', '0422222222', NULL),
(3, 0, 'On-site Worker', 'Verified', 'Walter', 'Workson', '1', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'walter@mail.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412222224', '98000002', 'Mark Smith', 'Friend', '0422222222', NULL),
(4, 0, 'On-site Worker', 'Verified', 'William', 'Workson', '1', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'william@mail.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412222224', '98000003', 'Mark Smith', 'Friend', '0422222222', NULL),
(5, 0, 'Builder', 'Verified', 'Daniel', 'Ward', '12', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'dwar@student.monash.edu', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412222223', '98000000', 'Mark Smith', 'Friend', '0422222222', NULL),
(6, 0, 'On-site Worker', 'Pending', 'Joe Jr.', 'Contractson', '3', 'Placeholder Street', 'Mount Waverley', '4', '3149', 'Australia', 'joejr@mail.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412228223', '98000000', 'Mark Smith', 'Friend', '0422222222', NULL),
(7, 0, 'On-site Worker', 'Verified', 'tan', 'xin', '333', 'agc', '3', '4', '333', '6', 'tanxin@163.com', '$2y$10$RBli0wByOAFFa7v8XZynA.JuZnSSYBej5I3/r5d4Is4Vjy6B06ZrS', '1838044933', '1838044935', '7', '111', '1838044935', NULL),
(19, 0, 'Admin', 'Verified', 'Admin', 'Admin', '1', 'c road', 'suburb', '4', '1234', 'Australia', 'admin@mail.com', '$2y$10$TBkkG4zljWlCK.tJKy3zdOJSm6g1YWXi6uw40mlFQR9E.hzoqYSpO', '1234567890', '1234567890', 'Test', 'test', '1234567890', NULL),
(20, 0, 'Builder', 'Verified', 'Builder', 'Builder', '1', 'c road', 'suburb', '4', '1234', 'Australia', 'builder@mail.com', '$2y$10$d2/HnvklpADr27uXKxaxlufY1PXIvq23d62muGSwp3fcU6DPa1JBK', '0123456789', '0123456789', 'Test', 'test', '0123456789', NULL),
(21, 0, 'Contractor', 'Verified', 'contractor', 'contractor', '1', 'something road', 'suburb', '4', '1234', 'Australia', 'contractor@mail.com', '$2y$10$/BA0CEmYUt0.N6X9KFwqF.T4qvL4EwldWG0iszrb8Y0EMr5E6XsFi', '1234567890', '1234567890', 'Test', 'test', '1234567890', NULL),
(23, 0, 'On-site Worker', 'Verified', 'worker', 'worker', '111', 'clayton', 'clayton', '4', '1234', 'Saudi Arabia', 'worker@gmail.com', '$2y$10$/432GR5PpgIe9ukJmEatsOLF884/YEUHsfocW3s.BINn0lfxCWseG', '1234567890', '1234567890', 'akle', 'ala', '1234567890', '/uploads/Userimage/Miko (2).gif'),
(24, 0, 'Contractor', 'Verified', 'contractor', 'contractor', '111', 'clayton', 'clayton', '4', '1234', 'au', 'contractor@gmail.com', '$2y$10$FDvAl8d7nkdhHznuPUpVNewQBqkkwDtHi4zfUuplkLZ8SEWYa5hMm', '1234567890', '1234567890', 'kale', 'asd', '1234567890', '/uploads/Userimage/Suisei (2).gif'),
(25, 0, 'Builder', 'Verified', 'Builder', 'Builder', '111', 'clayton road', 'clayton', '4', '1321', 'au', 'Builder@gmail.com', '$2y$10$Xh1QmV0RN8t.ZlHaaMaEkOu84UQLLGP1.2/7xzQPSyPxsGV0rLPIS', '1234567890', '1234567890', 'kale', 'dfs', '1234567890', NULL),
(26, 0, 'Admin', 'Verified', 'admin', 'admin', '111', 'clayton', 'clayton', '4', '1234', 'aus', 'admin@gmail.com', '$2y$10$KsG345sSynbTAm6qhBfQ/.wOXn/LBNTmi3xOuh2rbnRtopUV/5nG.', '1234567890', '1234567890', 'kale', 'friend', '1234567890', NULL),
(28, 1, 'Admin', 'Verified', 'Admin', 'User', '123', 'Main St', 'Sydney', '4', '2000', 'Australia', 'admin@example.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0412345678', '0298765432', 'Emergency Contact 1', 'Spouse', '0412345678', NULL),
(29, 0, 'Builder', 'Verified', 'Builder', 'User', '456', 'Oak Ave', 'Melbourne', '4', '3000', 'Australia', 'builder@example.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0455555555', '0388888888', 'Emergency Contact 2', 'Parent', '0455555555', NULL),
(30, 0, 'Contractor', 'Verified', 'Contractor', 'User', '789', 'Maple Rd', 'Brisbane', '4', '4000', 'Australia', 'contractor@example.com', '$2y$10$fL.J4yYoVkXPxiRJYFVlhO4TIswzZvhKgpyMIWrQUOCb2f.2FBmRm', '0411122333', '0444455566', 'Emergency Contact 3', 'Friend', '0411122333', NULL),
(31, 0, 'On-site Worker', 'Verified', 'Worker', 'User', '17', 'Bent st', 'Bentleigh', '4', '3204', 'Australia', 'worker@example.com', '$2y$10$rzDMEmy3voMGyBaWQImmOuAcjHnqW6oBDo8ZFhVsZqBzS/iNzlGS.', '0428088098', '0492830701', 'Ben', 'Dad', '0428089379', NULL),
(32, 0, 'On-site Worker', 'Verified', 'Thomas K. ', 'Humphrey', ' 10 ', 'Grayson street', 'LAUREL HILL', '0', '2649', 'Australia', '0@gmail.com', '$2y$10$CBa2/oHBsJ7UAh/QhXBxE.EJ6SsajqUy3kWXmZWp5.zDg8I4hyrUm', '0428997072', '0423892792', 'Keogh', 'Dad', '0498239872', NULL),
(33, 0, 'On-site Worker', 'Verified', 'Anthony ', 'Flood', '18', 'Elizabeth Street ', 'SEXTON', '1', '4570', 'Australia', '00@gmail.com', '$2y$10$qBTRaep/Lz5/Zy1TlBZ93elKXLr4i1RrNRsD/TAetnXg5jkv.yJj2', '0492479732', '0498023372', 'Sands', 'Friend', '0498382749', NULL),
(34, 0, 'On-site Worker', 'Verified', 'Lilian ', 'Stonham', '6', 'Parkes Road', 'GLENGALA', '4', '3020', 'Australia', '000@gmail.com', '$2y$10$F.MaT5Is2zYk/FkF14hl0.gWiIURXw6KyqDOudV2mYTuebnurHwua', '0498230380', '0423480238', 'Titheradge', 'Friends', '0498042472', NULL),
(35, 0, 'On-site Worker', 'Verified', 'Christian', 'Loader', '74 ', 'Norton Street', 'WEST PYMBLE', '0', '2073', 'Australia', '0000@gmail.com', '$2y$10$/E0fCxKtvxqsIXppJhZ.p.hPdKO2Dj5BHO4ClLJaBMyJrzqFbvXau', '0402730247', '0493849247', 'Halfey', 'friend', '0474295234', NULL),
(36, 0, 'On-site Worker', 'Verified', 'Benjamin', ' Goldstein', '52', 'Glenpark Road', 'UPPER CORINDI', '0', '2456', 'Australia', '00000@gmail.com', '$2y$10$iTV/bpCPkb5TVUWDaHsw7.Hkzrjy2qqxB9yDiU2P.yWNsYcwznZRm', '0429804243', '0497243592', 'Kale', 'Ex', '0498427925', NULL),
(37, 0, 'Contractor', 'Verified', 'Ashley', 'Laura', '38', 'Kogil Street', 'GARAH', '0', '2405', 'Australia', '1@gmail.com', '$2y$10$RhoQ4RiQH37oaok3mLYnMulqX7.bMSvlcr4itHlEMfzb7mdgvv8Ny', '0494042742', '0489274593', 'tylor', 'friend', '0459376534', NULL),
(38, 0, 'Contractor', 'Verified', 'Madeline', ' Belz', '96', 'Hunter Street', 'MIDDLE RIDGE', '1', '4350', 'Australia', '11@gmail.com', '$2y$10$tlfnbeZ6FN34syVQBqkXueG9pvXhe2NCr8MPEUEIMBb1vs8RTJ5CS', '0490820934', '0474853275', 'Julia', 'friend', '0498473259', NULL),
(39, 0, 'Contractor', 'Verified', 'Molly', 'Townsend', '12', 'Saggers Road', 'BEENONG', '5', '6333', 'Australia', '111@gmail.com', '$2y$10$J3FeMfGpMrj8cytqPQHXmuvE/nR.btbjaLZZafoaEpPJyr/qum6Gm', '0498722452', '0438749282', 'kan', 'friends', '0391042843', NULL),
(40, 0, 'Contractor', 'Verified', 'e', 'v', '3', 'e', 'e', '1', '3123', 'Australia', 'e@Eee.com', '$2y$10$5ehyyXNDvI4v2KINNIzvu.hhFllo1SJZa65az4ybLvJt772WPg/d.', '3213123123', '2312312312', 'e', 'e', '3123123123', '/uploads/userimage/default.jpg'),
(41, 0, 'On-site Worker', 'Verified', 'worker', 'worker', 'Unit 1', 'something road', 'Clayton', '5', '1234', 'Australia', 'worker@mail.com', '$2y$10$TaqdwJ/PcqHvSzUKQMFvVOi3FinUrbq0bai15dk4E1UD3XJ.G2v5C', '0412345678', '0412345678', 'Test', 'test', '0412345678', NULL),
(42, 0, 'Builder', 'Verified', 'Eleanor', ' Bennett', '144', 'West Road', 'Clayton', '4', '3000', 'Australia', 'builder2@gmail.com', '$2y$10$smlB/IPDUzmF1dXurCotw.aMvl0P.pKWnYBUoYDHHNhF32dr6dwyy', '0424324892', '', 'Leo', 'Friend', '0428942342', NULL),
(43, 0, 'Admin', 'Verified', 'Aria', 'Malone', '31', 'Fairview Street', 'Gnotuk', '4', '3260', 'Australia', 'GlobalAdmin42@gmail.com', '$2y$10$npgR7IDCyWaKvSDbk.dmmeId6rhxuq94mLKLhQdP7.OfAvlzAAL6W', '0493240723', '', 'Olivia Bennett', 'Parent', '0428032478', NULL),
(44, 0, 'Builder', 'Verified', 'Noah', 'Mitchell', '95', 'Tooraweenah Road', 'Cumbijowa', '0', '2871', 'Australia', 'Builder42@gmail.com', '$2y$10$DxY7JQWFXMPik.4eAbJAfeQjN2eW8fpDQldPig5XKnQ5HwkKxu1NW', '0402348975', '', 'Liam Anderson', 'Friend', '0409234829', NULL),
(45, 0, 'Contractor', 'Verified', 'Mason', 'Hall', '54', 'Springhill Bottom Road', 'Railton', '3', '7305', 'Australia', 'Contractor42@gmail.com', '$2y$10$CbO/pw8vUuSFykkaur1TYen1e4LEnp85ohqmWhzQRuhO26CQ0GStS', '0429072342', '', 'Harper Davis', 'Parent', '0409234234', NULL),
(46, 0, 'On-site Worker', 'Verified', 'Jackson', 'Wilson', '12', 'Cornish Street', 'Werribee', '4', '3030', 'Australia', 'Worker42@gmail.com', '$2y$10$Z9MJ/T56kFUjhcTDDaSzeuCpJyciF3O9Tq8wIb6wL2jU80FsrTxRi', '0492304234', '', 'Grace Thompson', 'Friend', '0402394283', NULL),
(47, 0, 'On-site Worker', 'Verified', 'Chloe', 'Nelson', '22', 'Peterho Boulevard', 'Houghton', '2', '5131', 'Australia', 'Worker42.1@gmail.com', '$2y$10$fttLq3j7IeDdlllC5VDYYu.5yDUG.qz9Y/v0hu.PEPFJA2C4UYW1K', '0430242343', '', 'Olivia Foster', 'Friend', '0402934823', NULL),
(48, 0, 'On-site Worker', 'Verified', 'Benjamin', ' Wright', '22', 'Edgecliff Road', 'Beaconsfield', '0', '2015', 'Australia', 'Worker42.2@gmail.com', '$2y$10$mbxjp0kNo9OJjdIMsmVV1uoWNnYFyCaemQNzKTM7ORm2ouYsXmNeu', '0400293424', '', 'Ethan Parker', 'Friend', '0432092434', NULL),
(49, 0, 'On-site Worker', 'Verified', 'Samuel', 'Foster', '72', 'McLeans Road', 'Coominglah Forest', '1', '4630', 'Australia', 'Worker42.3@gmail.com', '$2y$10$7e.IBmaqz9tzz3zUdV7PWO29UHK5ubgarnRjJGovwLTrB.k/UnigW', '0402389271', '', 'Harper Wright', 'Friend', '0492384234', NULL),
(50, 0, 'On-site Worker', 'Verified', 'Benjamin', 'Parker', '21', 'Link Road', 'Eddystone', '3', '7264', 'Australia', 'Worker42.4@gmail.com', '$2y$10$rSPZpStGviNrloJNaoHltunk18trwwJ6Wkudd.0kJ6DeceD2PEoKC', '0492098172', '', 'Liam Turner', 'Friend', '0489273492', NULL),
(51, 0, 'On-site Worker', 'Verified', 'James', 'Clark', '91', 'Fitzroy Street', 'Sebastopol', '4', '3356', 'Australia', 'Worker42.5@gmail.com', '$2y$10$n7ANUZm0ItRprD8eVooJV.VOwnubiLe1WDiVqXT5YIZyebowjf7aa', '0423037434', '', 'Isabella Taylor', 'Isabella@gmail.com', '0492389324', NULL);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `companies_projects`
--
ALTER TABLE `companies_projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `companies_users`
--
ALTER TABLE `companies_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `inductions`
--
ALTER TABLE `inductions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `projects_documents`
--
ALTER TABLE `projects_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects_users`
--
ALTER TABLE `projects_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subcontracts`
--
ALTER TABLE `subcontracts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'item id', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
