-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2023 at 06:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `isbn` varchar(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publish_year` int(11) DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `image_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`isbn`, `title`, `author`, `publish_year`, `available`, `image_url`) VALUES
('978-0-13-2350', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 2008, 1, 'Images/Books/Clean Code A Handbook of Agile Software Craftsmanship.png'),
('978-0-13-7081', 'Domain-Driven Design: Tackling Complexity in the Heart of Software', 'Eric Evans', 2003, 1, 'Images/Books/Domain-Driven Design Tackling Complexity in the Heart of Software.png'),
('978-0-201-889', 'Introduction to Algorithms', 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, and Clifford Stein', 1990, 1, 'Images/Books/Introduction to Algorithms.png'),
('978-0-262-033', 'Structure and Interpretation of Computer Programs', 'Harold Abelson, Gerald Jay Sussman, Julie Sussman', 1996, 1, 'Images/Books/Structure and Interpretation of Computer Programs.png'),
('978-0-262-162', 'Artificial Intelligence: A Modern Approach', 'Stuart Russell and Peter Norvig', 1995, 1, 'Images/Books/Artificial Intelligence A Modern Approach.png'),
('978-0-321-635', 'Design Patterns: Elements of Reusable Object-Oriented Software', 'Erich Gamma, Richard Helm, Ralph Johnson, and John Vlissides', 1994, 1, 'Images/Books/Design Patterns Elements of Reusable Object-Oriented Software.png'),
('978-0-596-520', 'Learning Python', 'Mark Lutz', 2003, 1, 'Images/Books/Learning Python.png'),
('978-1-59327-5', 'Automate the Boring Stuff with Python', 'Al Sweigart', 2015, 1, 'Images/Books/Automate the Boring Stuff with Python.png');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `borrow_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `period` enum('6 months','1 month','1 week') NOT NULL,
  `is_returned` tinyint(1) NOT NULL DEFAULT 0,
  `fine` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `cpr` varchar(9) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `register_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `cpr`, `email`, `password`, `is_admin`, `register_date`) VALUES
(1, '123456789', 'admin@library.com', 'e3274be5c857fb42ab72d786e281b4b8', 1, '2023-12-15 10:53:06'),
(2, '030910609', 'ahasson2003@gmail.com', '$2y$10$fAz7GIzghNwAkYbjKsb4ueTsGn5a7uoKp0PdwUcWy1TJETT/bvcSO', 0, '2023-12-14 21:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `isbn` (`isbn`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpr` (`cpr`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrow_records_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
