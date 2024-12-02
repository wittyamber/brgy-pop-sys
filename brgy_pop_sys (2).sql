-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 04:31 PM
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
-- Database: `brgy_pop_sys`
--

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `household_id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `age` int(3) DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `tribe` enum('None','Ata Manobo','Badjao','Bagobo','Banwaon','B laan','Bukidnon','Dibabawan','Dulangan','Guiangga','Higaonon','Iranon','JamaMapun','Kaagan','Kalagan','Kulagan','Kalbugan','Magindanaon','Magguangan','Mamanwa','Mandaya','Mangguwangan','Manobo','Malbog','Marano','Mansaka','Matigsalog','Palawani','Sama','Sangil','Subanon','Tagakaolo','T boli','Talandig','Tao-sug','Teduary','Tiruray','Ubo','Yakan') DEFAULT NULL,
  `occupation` enum('None','Accountant','Assistant','Baker','Barber','Businessman/woman','Butcher','Carpenter','Cashier','Construction Worker','Civil Servant','Chef','Doctor','Dentist','Driver','Electrician','Farmer','Firefighter','Fisherman','Housekeeper','Lawyer','Manager','Nurse','Office Clerk','Overseas Filipino Worker (OFW)','Police Officer','Salesperson','Seaman/woman','Soldier','Teacher','Vendor') DEFAULT NULL,
  `address` enum('Purok Sto.Niño-Apo Beach','Purok Bayabas-Apo Beach','Purok Centro-Apo Beach','Purok Leytenians-Apo Beach','Purok Mahayahay-Apo Beach','Purok Kalubihan-Apo Beach','Purok Badjaoan-Apo Beach','Purok Bonggahan','Purok Madasigon','Purok Kapayapaan-Amlo Subd','Civil Servant','Chef','Doctor','Dentist','Purok Bougainvilla','Purok Federation President','Purok Miranda','Purok Kaunlaran-Sto.Niño Village','Purok Talisay-Ceboley Beach','Purok Dapsap-Ceboley Beach','Purok Sampaguita-Ceboley Beach','Purok Kagitingan-Kapihan','Purok Kaimito-Bagumbayan','Purok Pakigdait-Bagumbayan','Purok Pagkakaisa-Bagumbayan','Purok Pag-asa-Bagumbayan','Purok Bagong Silang-sitio Doring Bendigo') DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`household_id`, `last_name`, `first_name`, `middle_name`, `birthdate`, `age`, `civil_status`, `gender`, `tribe`, `occupation`, `address`, `contact_number`, `status`, `created_at`, `updated_at`, `archived`) VALUES
(1, 'Sabio', 'Juana', 'Bausing', '0000-00-00', NULL, 'Single', 'Female', 'None', NULL, 'Purok Sto.Niño-Apo Beach', '09123456789', 'Active', '2024-11-30 05:15:24', '2024-12-02 12:32:47', 0),
(2, 'Ongkas', 'Mariano', 'III', '2002-12-15', 21, 'Single', 'Male', 'Bagobo', 'Accountant', 'Purok Kapayapaan-Amlo Subd', '09234567891', 'Active', '2024-11-30 05:46:55', '2024-11-30 11:44:33', 0),
(3, 'Leonida', 'Fritzie', 'Cornel', '1999-03-24', 25, 'Married', 'Female', 'None', 'Civil Servant', 'Purok Kapayapaan-Amlo Subd', '09123123123', 'Active', '2024-11-30 11:47:02', '2024-12-02 12:30:51', 1),
(4, 'Bausing', 'Jellian', 'Sabio', '2009-02-25', 15, 'Single', 'Female', 'Badjao', 'Carpenter', 'Purok Talisay-Ceboley Beach', '09234567891', 'Active', '2024-11-30 12:27:11', '2024-12-02 12:31:31', 0);

--
-- Triggers `households`
--
DELIMITER $$
CREATE TRIGGER `update_age_before_insert` BEFORE INSERT ON `households` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.birthdate, CURDATE());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_age_before_update` BEFORE UPDATE ON `households` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.birthdate, CURDATE());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

CREATE TABLE `household_members` (
  `member_id` int(11) NOT NULL,
  `household_id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `age` int(3) DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `relationship_to_head` enum('Father','Mother','Husband','Wife','Daughter','Son','Sister','Brother','Grandmother','Grandfather','Uncle','Auntie','Cousin','In-law','Friend','None') DEFAULT NULL,
  `tribe` enum('None','Ata Manobo','Badjao','Bagobo','Banwaon','B laan','Bukidnon','Dibabawan','Dulangan','Guiangga','Higaonon','Iranon','JamaMapun','Kaagan','Kalagan','Kulagan','Kalbugan','Magindanaon','Magguangan','Mamanwa','Mandaya','Mangguwangan','Manobo','Malbog','Marano','Mansaka','Matigsalog','Palawani','Sama','Sangil','Subanon','Tagakaolo','T boli','Talandig','Tao-sug','Teduary','Tiruray','Ubo','Yakan') DEFAULT NULL,
  `occupation` enum('None','Accountant','Assistant','Baker','Barber','Businessman/woman','Butcher','Carpenter','Cashier','Construction Worker','Civil Servant','Chef','Doctor','Dentist','Driver','Electrician','Farmer','Firefighter','Fisherman','Housekeeper','Lawyer','Manager','Nurse','Office Clerk','Overseas Filipino Worker (OFW)','POlice Officer','Salesperson','Seaman/woman','Soldier','Teacher','Vendor') DEFAULT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `household_members`
--
DELIMITER $$
CREATE TRIGGER `update_member_age_before_insert` BEFORE INSERT ON `household_members` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.birthdate, CURDATE());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_member_age_before_update` BEFORE UPDATE ON `household_members` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.birthdate, CURDATE());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `birthdate` date DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `first_name`, `middle_name`, `last_name`, `age`, `gender`, `address`, `contact_number`, `registered_at`, `is_archived`, `birthdate`, `archived`) VALUES
(2, 'Fritzie', 'P.', 'Leonida', 4, 'Female', 'Purok 1', '09234567891', '2024-11-10 04:44:47', 0, '2020-05-07', 1),
(3, 'Mariano', 'O', 'Ongkas', 15, 'Male', 'Purok 2', '09345678912', '2024-11-10 04:45:59', 0, '2008-11-20', 1),
(4, 'Juan', 'P.', 'Dela Cruz', 77, 'Male', 'Purok 3', '09123123123', '2024-11-10 05:01:02', 0, '1947-02-11', 0),
(5, 'Juana', 'M', 'Sabio', 22, 'Female', 'Purok 1', '09123456789', '2024-11-10 09:54:24', 0, '2002-05-21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `status`, `created_at`) VALUES
(1, '', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', '', '2024-11-25 09:22:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`household_id`);

--
-- Indexes for table `household_members`
--
ALTER TABLE `household_members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `household_id` (`household_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `household_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `household_members`
--
ALTER TABLE `household_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `household_members`
--
ALTER TABLE `household_members`
  ADD CONSTRAINT `household_members_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`household_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
