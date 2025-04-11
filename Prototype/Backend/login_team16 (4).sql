-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 11:07 AM
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
-- Database: `login_team16`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `firstName`, `lastName`, `email`, `password`) VALUES
(1, 'Test', 'admin', 'admin@gmail.com', 'admin'),
(2, 'Mohammed', 'Ali', 'mo@gmail.com', '22222222');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `Complaint_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `id` int(11) NOT NULL,
  `ComplaintDate` date NOT NULL,
  `TypeOfcomplaint` varchar(20) NOT NULL,
  `Resolved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `EnquiryID` int(11) NOT NULL,
  `Enquiry` text DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Resolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_listings`
--

CREATE TABLE `job_listings` (
  `job_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(20) NOT NULL,
  `lister_id` int(11) DEFAULT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `admin_approval` tinyint(1) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `job_status` enum('pending','taken','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_listings`
--

INSERT INTO `job_listings` (`job_id`, `status`, `description`, `location`, `lister_id`, `applicant_id`, `admin_approval`, `title`, `salary`, `requirements`, `job_status`) VALUES
(1, 1, 'I am looking for a patient dog walker that can handle my energetic dog. We have a 3-year-old Siberian Husky who needs daily walks. The ideal candidate will have experience with high-energy breeds and be able to provide consistent exercise and companionship. The schedule is flexible but must include weekday afternoons.', 'London', 2, NULL, 1, 'Dog Walker', '£15 per hour', '[\"Experience with energetic dogs\", \"Able to walk dogs in all weather conditions\", \"Reliable and punctual\", \"Lives in London or surrounding areas\"]', 'pending'),
(2, 0, 'Looking for a reliable babysitter for two children (5 and 8 years old) for evenings and occasional weekends. Our family is looking for a responsible and caring individual to look after our two children. Duties include preparing meals, helping with homework, bedtime routines, and engaging in age-appropriate activities. References required.', 'Manchester', 2, 5, 1, 'Babysitter', '£12 per hour', '[\"Previous childcare experience\", \"First aid certification preferred\", \"Energetic and patient\", \"Able to help with homework\"]', 'pending'),
(3, 0, 'Need someone to maintain our lawn and garden weekly during the growing season. We need help maintaining our medium-sized garden including lawn mowing, weeding, and basic pruning. The job requires approximately 2 hours per week, typically on weekends. We can provide some equipment but prefer if you have your own lawn mower.', 'Birmingham', 2, 5, 1, 'Lawn Mowing & Garden Maintenance', '£14 per hour or £25 per visit', '[\"Own gardening equipment preferred\", \"Knowledge of basic garden maintenance\", \"Reliable and consistent\", \"Available on weekends\"]', 'completed'),
(4, 1, 'Looking for a pet sitter for our cat and two small dogs while we are on vacation. We need someone to feed, walk, and spend time with our pets while we are away on vacation. The job involves visiting our home twice daily (morning and evening) to feed our animals, walk the dogs, and provide some companionship. Must be comfortable with administering medication to one of our dogs.', 'Leeds', 2, NULL, 1, 'Pet Sitting', '£20 per day', '[\"Experience with cats and small dogs\", \"Responsible and trustworthy\", \"Lives nearby\", \"Available for the last two weeks of August\"]', 'pending'),
(5, 1, 'Seeking help with weekly grocery shopping for an elderly couple. An elderly couple needs assistance with their weekly grocery shopping. The job involves picking up a shopping list from their home, going to the supermarket, purchasing the items, and delivering them back to their home. Candidate must be patient, respectful, and have their own transportation.', 'Sheffield', 2, NULL, 1, 'Grocery Shopping Assistant', '£12 per hour plus travel expenses', '[\"Driver\'s license and own vehicle\", \"Patient and respectful\", \"Attention to detail\", \"Available on weekday mornings\"]', 'pending'),
(6, 1, 'Looking for a tutor for GCSE level Math and Science for my 15-year-old son. We are looking for a knowledgeable and patient tutor to help our son improve his Math and Science grades for his upcoming GCSE exams. The ideal candidate will have experience with the current curriculum and be able to explain concepts clearly. Sessions would be held at our home.', 'London', 2, NULL, 1, 'Tutoring - Math & Science', '£20 per hour', '[\"Strong knowledge of GCSE Math and Science curriculum\", \"Previous tutoring experience preferred\", \"Patient and encouraging teaching style\", \"Available weekday evenings\"]', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `resolved`
--

CREATE TABLE `resolved` (
  `id` int(11) NOT NULL,
  `type` enum('enquiry','complaint') NOT NULL,
  `original_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `resolved_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `firstLineAddress` varchar(255) DEFAULT NULL,
  `postalCode` varchar(20) DEFAULT NULL,
  `password` varchar(50) NOT NULL,
  `registrationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `phoneNumber`, `firstLineAddress`, `postalCode`, `password`, `registrationDate`) VALUES
(2, 'TestFirstName', 'TestLastName', 'test@example.com', '1234567890', 'Test Address', '12345', '11111111', '2025-02-26 19:15:58'),
(4, 'asddsa', 'dassasda', 'm@gmail.comssda', '07767485944', '33 random street', 're149mk', '11111111', '2025-03-02 21:34:52'),
(5, 'Test', 'Name2', 'm@gmail.com', '07767485944', '33 random street', 'R45 GTH', '111111111', '2025-04-01 16:45:41'),
(6, 'Mohammed', 'ali', 'A@gmail.com', '011111111111', '1 bradford street', 'bd5 567', '11111111', '2025-04-05 17:50:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`Complaint_id`),
  ADD KEY `fk_user_complaints` (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`EnquiryID`);

--
-- Indexes for table `job_listings`
--
ALTER TABLE `job_listings`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `lister_id` (`lister_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `resolved`
--
ALTER TABLE `resolved`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resolved_admin_id` (`admin_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `Complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `EnquiryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `job_listings`
--
ALTER TABLE `job_listings`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `resolved`
--
ALTER TABLE `resolved`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `fk_user_complaints` FOREIGN KEY (`id`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_listings`
--
ALTER TABLE `job_listings`
  ADD CONSTRAINT `job_listings_ibfk_1` FOREIGN KEY (`lister_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `job_listings_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `resolved`
--
ALTER TABLE `resolved`
  ADD CONSTRAINT `fk_resolved_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
