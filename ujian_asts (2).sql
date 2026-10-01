-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2026 at 07:50 AM
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
-- Database: `ujian_asts`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `ref` varchar(100) DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `ref`, `is_read`, `created_at`) VALUES
(1, 11, 'Data akunmu telah diubah oleh admin.', NULL, 1, '2026-10-01 01:34:13'),
(2, 0, 'Data faza diubah oleh admin.', NULL, 1, '2026-10-01 01:34:13'),
(3, 11, 'Admin mengubah datamu:\n• Email: neo9@gmail.com → faza9@gmail.com', NULL, 1, '2026-10-01 01:39:35'),
(4, 0, 'Data faza diubah oleh admin:\n• Email: neo9@gmail.com → faza9@gmail.com', NULL, 1, '2026-10-01 01:39:35'),
(6, 0, 'Data Rizzzneo diubah oleh admin:\n• Nama: neo → Rizzzneo', NULL, 1, '2026-10-01 01:49:07'),
(7, 0, 'Akun Rizzzneo telah dihapus.', NULL, 1, '2026-10-01 01:53:41'),
(8, -1, 'deleted', 'neo1@gmail.com', 0, '2026-10-01 01:53:41'),
(10, 0, 'Akun neo telah dihapus.', NULL, 1, '2026-10-01 01:58:45'),
(11, -1, 'deleted', 'neo@gmail.com', 0, '2026-10-01 01:58:45'),
(12, 31, 'Akunmu telah dibuat oleh admin:\n• Nama: neo gimang\n• NISN: 24324354\n• TTL: Ponorogo ,15 desember 2008\n• Gender: MALE\n• Email: fazaa1@gmail.com\n• No. HP: 085784691260\n• Alamat: jenanggan', NULL, 0, '2026-10-01 01:59:28'),
(13, 0, 'Pendaftaran akunmu berhasil. Datamu:\n• Nama: fasa\n• NISN: 12321321321\n• TTL: Ponorogo ,12 desember 2008\n• Gender: MALE\n• Email: fasa@gmail.com\n• No. HP: 088976546026\n• Alamat: jenanggan', NULL, 1, '2026-10-01 02:05:39'),
(14, 0, 'Pengguna baru mendaftar:\n• Nama: fasa\n• NISN: 12321321321\n• TTL: Ponorogo ,12 desember 2008\n• Gender: MALE\n• Email: fasa@gmail.com\n• No. HP: 088976546026\n• Alamat: jenanggan', NULL, 1, '2026-10-01 02:05:40'),
(15, 0, 'Akun fasa telah dihapus.', NULL, 1, '2026-10-01 02:08:40'),
(16, -1, 'deleted', 'fasa@gmail.com', 0, '2026-10-01 02:08:40'),
(18, 0, 'Pengguna baru mendaftar:\n• Nama: fasa\n• NISN: 12343543\n• TTL: Ponorogo ,12 desember 2008\n• Gender: MALE\n• Email: fasa9@gmail.com\n• No. HP: -\n• Alamat: jenanggan', NULL, 1, '2026-10-01 02:09:42'),
(20, 0, 'Data fasa diubah oleh pengguna sendiri:\n• No. HP: (kosong) → 088976546026', NULL, 1, '2026-10-01 02:10:11'),
(22, 0, 'Data fasa diubah oleh admin:\n• Email: fasa9@gmail.com → fazazzz@gmail.com\n• Kelas: (kosong) → XII RPL 2', NULL, 1, '2026-10-01 03:51:10'),
(24, 0, 'Data fasa diubah oleh pengguna sendiri:\n• Kelas: XII RPL 2 → XII RPL 1', NULL, 1, '2026-10-01 03:52:07'),
(25, 0, 'Akun fasa telah dihapus.', NULL, 1, '2026-10-01 03:52:34'),
(26, -1, 'deleted', 'fazazzz@gmail.com', 0, '2026-10-01 03:52:34'),
(27, 0, 'Akun User 25 (user25@mail.com) dihapus oleh pemilik akun sendiri.', NULL, 1, '2026-10-01 04:20:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `ttl` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'user',
  `kelas` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `nisn`, `ttl`, `gender`, `email`, `address`, `phone`, `password`, `role`, `kelas`) VALUES
(1, 'Leanne Graham', '123131444', 'PONOROGO, 12 Agustus 2010', 'MALE', 'Sincere@april.biz', 'Kulas Light, Apt. 556, Gwenborough, 92998-3874', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(2, 'Ervin Howell', '262363463', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'Shanna@melissa.tv', 'Victor Plains, Suite 879, Wisokyburgh, 90566-7771', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(3, 'Clementine Bauch', '632641362', 'PONOROGO, 12 Agustus 2010', 'MALE', 'Nathan@yesenia.net', 'Douglas Extension, Suite 847, McKenziehaven, 59590-4157', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(4, 'Patricia Lebsack', '111222333', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'Julianne.OConner@kory.org', 'Hoeger Mall, Apt. 692, South Elvis, 53919-4257', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(5, 'Chelsey Dietrich', '444555666', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'Lucio_Hettinger@annie.ca', 'Skiles Walks, Suite 351, Roscoeview, 33263', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(6, 'Mrs. Dennis Schulist', '777888999', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'Karley_Dach@jasper.info', 'Norberto Crossing, Apt. 950, South Christy, 23505-1337', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(7, 'Kurtis Weissnat', '121212121', 'PONOROGO, 12 Agustus 2010', 'MALE', 'Telly.Hoeger@billy.biz', 'Rex Trail, Suite 280, Howemouth, 58804-1099', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(8, 'Nicholas Runolfsdottir', '343434343', 'PONOROGO, 12 Agustus 2010', 'MALE', 'Sherwood@rosamond.me', 'Ellsworth Summit, Suite 729, Aliyaview, 45169', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(9, 'Glenna Reichert', '565656565', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'Chaim_McDermott@dana.io', 'Dayna Park, Suite 449, Bartholomebury, 76495-3109', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(11, 'faza', '09090909', 'Ponorogo ,12 desember 2008', 'MALE', 'faza9@gmail.com', 'jenanggan', '088976546026', '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(12, 'nasa', '00000', 'kauman,1945', 'male', 'nasa@gmail.com', 'jolajoli', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(13, 'User 11', '100000011', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user11@mail.com', 'Alamat 11', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(14, 'User 12', '100000012', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user12@mail.com', 'Alamat 12', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(15, 'User 13', '100000013', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user13@mail.com', 'Alamat 13', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(16, 'User 14', '100000014', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user14@mail.com', 'Alamat 14', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(17, 'User 15', '100000015', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user15@mail.com', 'Alamat 15', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(18, 'User 16', '100000016', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user16@mail.com', 'Alamat 16', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(19, 'User 17', '100000017', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user17@mail.com', 'Alamat 17', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(20, 'User 18', '100000018', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user18@mail.com', 'Alamat 18', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(21, 'User 19', '100000019', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user19@mail.com', 'Alamat 19', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(22, 'User 20', '100000020', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user20@mail.com', 'Alamat 20', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(23, 'User 21', '100000021', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user21@mail.com', 'Alamat 21', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(24, 'User 22', '100000022', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user22@mail.com', 'Alamat 22', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(25, 'User 23', '100000023', 'PONOROGO, 12 Agustus 2010', 'MALE', 'user23@mail.com', 'Alamat 23', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(26, 'User 24', '100000024', 'PONOROGO, 12 Agustus 2010', 'FEMALE', 'user24@mail.com', 'Alamat 24', NULL, '$2y$10$diSfhgDoZY.OMn6XhO3cPOfjsiM4N.BXXOR3tFanXflzPkjyd5M6S', 'user', NULL),
(28, 'Administrator', NULL, NULL, NULL, 'admin@mail.com', NULL, NULL, '$2y$10$ZpIJ.7sgJEnAH0jBIDLUVe0GwUiNlqxkTLOjqgXIEPJZcpyxx0Qhm', 'admin', NULL),
(31, 'neo gimang', '24324354', 'Ponorogo ,15 desember 2008', 'MALE', 'fazaa1@gmail.com', 'jenanggan', '085784691260', '$2y$10$UlP9ctg3PMkDYXytJnK9LOLCe.Jfb1Y.Wps6ICwYPHXbhe4SG203i', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
