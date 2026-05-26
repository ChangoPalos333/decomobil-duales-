-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-05-2026 a las 22:14:04
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `decomobil_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_token`, `expires_at`, `created_at`) VALUES
(2, 1, '15ccc4805672a63eca1122dfe4b73a0dd8d74b320ebda3887318e508c84cb7f6', '2026-04-16 18:24:31', '2026-04-15 16:24:31'),
(11, 6, 'ef2fbf96862e15e1b16ff0a15ad9b33ba905fec7013c6e5393d123088ada4e7b', '2026-04-16 21:03:20', '2026-04-15 19:03:20'),
(12, 6, 'b8010ff03862672ff9277e701d789564e1ce9d9b6223b835d2c1e46753ac1cad', '2026-04-16 22:02:59', '2026-04-15 20:02:59'),
(13, 6, '9d551f590cdde1765971cab1fb04ee497c56d8c67d88ad326dc891fc3cb168cd', '2026-04-16 23:00:31', '2026-04-15 21:00:31'),
(15, 6, '194fad6a1af6a8a30b1372ae3988fea364e23a2734b006940f430ea08ecccf6d', '2026-04-16 23:20:05', '2026-04-15 21:20:05'),
(17, 6, '51ed071ac30fc51fe45f67a151eee56e08304ca5c571cfffea2a03c10c53afb1', '2026-04-16 23:20:56', '2026-04-15 21:20:56'),
(18, 6, '71125a83d33409d2d125477e4c359cebe8a694f3bbdef2b5e0bd4055b8255020', '2026-04-22 23:25:05', '2026-04-21 21:25:05'),
(19, 6, 'de0c092f7a827c398f5ad2d08e2eed2027dc7b66ff0f725ce71b7503af274d13', '2026-04-29 16:27:22', '2026-04-28 14:27:22'),
(20, 6, '0737e31ae101f95cae4c25dd894a66e3a231188aaef4da8b8f8b48c1fdf1b14f', '2026-04-29 16:30:11', '2026-04-28 14:30:11'),
(21, 6, '7ccf17c55068cc17a180954af2ef1959e2c25886d4cc3697f91388e807e4afef', '2026-04-29 16:33:54', '2026-04-28 14:33:54'),
(23, 6, '2f7163401d9b8e0abd859fcc58f2d6a1ee6165c3877ee87db8f1c39a30c01ee5', '2026-04-29 16:35:34', '2026-04-28 14:35:34'),
(25, 6, '2ad8f76bafcee99f6f28e04d0ae319dad3eaf665b62e91b45c4c48c937b6e29f', '2026-04-29 16:37:56', '2026-04-28 14:37:56'),
(26, 6, '5a6ee444e8d64b68467a462bff3a69d327ac91cdddb3ff6a3dbdba54fc4007d6', '2026-04-29 16:44:08', '2026-04-28 14:44:08'),
(27, 6, '5df9d57dd762cd43a00e7357946d653a4e58a350023f969f23c6f1dd58d478e7', '2026-04-29 16:44:59', '2026-04-28 14:44:59'),
(29, 6, '952ff6c96bed9945ea7ceb2ed3637d4e74c111b3d6f07f9b0fce33a40e0f5901', '2026-04-29 16:46:12', '2026-04-28 14:46:12'),
(31, 1, 'c347eb82ba9f27187810f9b9cfd0027dcac6c236882114f3a8edc21014b09c23', '2026-04-29 22:45:33', '2026-04-28 20:45:33'),
(32, 6, '7d4b4d150a22d20d039a45b0837f06fa82824f4dad6d00b6d758023f4b340751', '2026-05-06 16:04:51', '2026-05-05 14:04:51'),
(33, 1, 'e63ee7aaf2e3cf35a80be25fd31605b20290f96eccb2f2bdbea46de0b45a6cb0', '2026-05-06 16:09:06', '2026-05-05 14:09:06'),
(34, 6, '0d159d5487209e6e7185651b9aecf84459811f541430ac595c0aea2237f9f62e', '2026-05-06 16:10:14', '2026-05-05 14:10:14'),
(35, 1, '6af00ffa79c91b8a4d0d36e203621f3dc7c5ae425037b51279d80e62a64e6e92', '2026-05-06 16:11:16', '2026-05-05 14:11:16'),
(37, 6, 'ae83b2670ddd97247226d2ae75cdb5a91b2c64181cb708f1a080d03f8638b2b8', '2026-05-06 16:54:55', '2026-05-05 14:54:55'),
(38, 1, 'd1ca6fd001ca26aaa30cc89bbeb2a4b8987640d22dbe9300572320932e92fa36', '2026-05-06 16:55:29', '2026-05-05 14:55:29'),
(39, 8, '6a3177e512629b4b2440372b3965ff1df92236d6cf1f73faf7deba40b06336f0', '2026-05-06 16:57:43', '2026-05-05 14:57:43'),
(40, 1, '827b5f514210703ba634d0afe521ab118fcc8067bb9e390e40673ba8b9f8be2b', '2026-05-06 17:00:01', '2026-05-05 15:00:01'),
(41, 8, '91dc7360e979bd48822e22bbde2666fe29b05980d4e5b5f3e49a3a4f5dff8ee2', '2026-05-06 17:00:32', '2026-05-05 15:00:32'),
(43, 8, 'c0aae11f6e34ed0af24c09d16273ead346017eeac79641ed2ec1413cef65f036', '2026-05-06 17:06:42', '2026-05-05 15:06:42'),
(44, 8, '0d616e8d5cd260b6e7777563c1aaccc594e4fadadeb64dad9178cdcb90e1c720', '2026-05-06 17:09:42', '2026-05-05 15:09:42'),
(45, 1, '6ca16fc9821fc2aa07523f039f1d705e51c59afbd994f2da12838deaab834709', '2026-05-06 17:15:11', '2026-05-05 15:15:11'),
(46, 8, 'e15ca67629f4c0c5a206c1694f762f0f0fd2028423a11c1838797352c1c199a4', '2026-05-06 17:23:07', '2026-05-05 15:23:07'),
(47, 1, 'ba1d04403561ab1ccea9c8ff01811d7411238b173a11f19c3dc32becadce128a', '2026-05-06 17:38:45', '2026-05-05 15:38:45'),
(48, 1, 'a77d6818ebf1ec0bced4d8f163f3bbb59c5e4e11129ac9094d226dcc59168307', '2026-05-06 17:41:51', '2026-05-05 15:41:51'),
(49, 1, '0cfc0b9b1a41243b1f261594a490f447a67ecee2441bf63bc34d7be4dac8a96a', '2026-05-06 17:42:42', '2026-05-05 15:42:42'),
(50, 10, 'bfe5ba98e206289814d5ce2a27bbf18e6a32837249f6722f9bfe4bdbd9163014', '2026-05-06 17:44:06', '2026-05-05 15:44:06'),
(51, 1, 'd3b47d1fab4e603fdc5b08cbc03eeed2af3e5d44de34d8d7e611aef86320d075', '2026-05-06 17:44:27', '2026-05-05 15:44:27'),
(52, 8, '7c606c2b25102d48608a7b45815600ffe57c2bfcef0375dc43882cd6b2a6867a', '2026-05-06 17:52:49', '2026-05-05 15:52:49'),
(53, 6, '7ba9a08aca848fef6d5ef298c695b9ee84e3a87c567b8eaa55fdc80e15108529', '2026-05-06 17:53:16', '2026-05-05 15:53:16'),
(54, 1, '33143b2b04949a2ff7ca17b1f2d0e8436c73958c86c5d37fae3c6e869755dd66', '2026-05-06 17:53:59', '2026-05-05 15:53:59'),
(55, 6, '922cdc01ea16bc1699a46efc2d4abc81a29236a55e25c447cb01576af82a9cca', '2026-05-06 17:55:30', '2026-05-05 15:55:30'),
(56, 1, '613cbb23f90b49ab82848e0f0a0ae4568396436b318039a40701943fe5eab3da', '2026-05-06 17:56:04', '2026-05-05 15:56:04'),
(57, 10, '7d5177146503dfc6b9ae9fc973c6dc0b1568f00ba08cf2137c01536e1ed2285d', '2026-05-06 17:57:18', '2026-05-05 15:57:18'),
(58, 8, 'ac5fa5b79a0a8251008ae40856a5ec275759043a82684dffc13433e0701495c4', '2026-05-06 17:57:46', '2026-05-05 15:57:46'),
(59, 6, '8775a08c2d070f2d52788fdf55c61016d5778192b7708998399d7e65a793cd24', '2026-05-06 18:05:03', '2026-05-05 16:05:03'),
(60, 8, '05590a97a6445bb785db5d645e57a7a58ae561a8f4bf43808a439e623570b9ef', '2026-05-06 18:05:20', '2026-05-05 16:05:20'),
(61, 8, 'f746388a2afc2180c1e48a27fe20bddedcbec7bc77b3aa90a7503f430963380d', '2026-05-06 18:06:39', '2026-05-05 16:06:39'),
(62, 6, '895b802c3aae7a3e7d71b7ad2f4264652161d77809df7eb4fd2de7b404ec58af', '2026-05-06 18:07:13', '2026-05-05 16:07:13'),
(63, 1, '53dea9d8309252c246a7770ad8e874cbec4cc550dcee66101de0ef7c7a118372', '2026-05-06 18:08:27', '2026-05-05 16:08:27'),
(64, 1, '58cb9df018f42c8cec9db34ccb89c7fe20fb2154e46249bea1beeb6d6d205520', '2026-05-06 18:09:49', '2026-05-05 16:09:49'),
(65, 8, '808e2a2027ba34245c800c2d935ecca5c167a2d0cd0a9f1cb80ff8370d909e39', '2026-05-06 18:10:47', '2026-05-05 16:10:47'),
(66, 6, '4f07078d1a53ab19df2bedc4774ba21be4ce5e84efa35d548e7e1adddceebe3a', '2026-05-06 18:13:44', '2026-05-05 16:13:44'),
(67, 6, '3d41c680680ff8e2b908d2642e0110a7bed269dd08d2693b0084fed84fe493e0', '2026-05-06 18:25:27', '2026-05-05 16:25:27'),
(68, 1, '03eac6f91cbf787498304757462181d9fc9e47833b966045b4a06f859ba7bf59', '2026-05-06 18:25:51', '2026-05-05 16:25:51'),
(69, 6, '1f05c171905041cda89adc619a767e730a827d5edc15ddd9037c8876e6819c94', '2026-05-06 18:45:07', '2026-05-05 16:45:07'),
(70, 1, 'b25b7b24656e4caea7748e5278b6a860ff76cf5d53d5120e7faeb1dc423bc868', '2026-05-06 18:47:30', '2026-05-05 16:47:30'),
(71, 6, '4e58bc080a111647c2acb63648715f1cd99145c98d1b944bbdf69653e4b3e8af', '2026-05-06 19:55:31', '2026-05-05 17:55:31'),
(72, 1, '57588257874ad5653f07e9a659dc67866f601ac1298d22d1a16a896b8880ab38', '2026-05-06 20:06:45', '2026-05-05 18:06:45'),
(73, 6, 'cf4205a4724c4778254e40f378acbba4d9533467b191944dd0b891f779e538ef', '2026-05-06 20:38:28', '2026-05-05 18:38:28'),
(74, 1, '287a490f43486975064112bd05068307c5199c933022c893214f09f01bfd80e6', '2026-05-06 20:38:37', '2026-05-05 18:38:37'),
(75, 1, 'ddfe6dbe0befcf44e009a17b7e5049f824f02fefc999d4e4d538600034d59bb6', '2026-05-06 20:40:09', '2026-05-05 18:40:09'),
(76, 8, '796816ee2bed639218e941a58bcbdfb8b7ec3ad5477290d7ef85de46852f090a', '2026-05-06 20:47:29', '2026-05-05 18:47:29'),
(77, 1, '7222dce78e0d66658e3d2ae9cb95e1361292a1098fbc85366fd7afa6adec733b', '2026-05-06 20:55:52', '2026-05-05 18:55:52'),
(78, 1, '0c6032ac1ca2ab24ecabf3ca927360822690c746d6526a710a7d821f5ffc7e0b', '2026-05-06 21:32:06', '2026-05-05 19:32:06'),
(79, 6, '3462cb62f9fa1006b893e4dea2e9bf3bca99313d4074c563348ae18bd670e52d', '2026-05-06 21:35:25', '2026-05-05 19:35:25'),
(80, 1, 'f78749ad018dea6a7b307af2b8673622be97b4e67e4298a4f4d8357f7d02d9cf', '2026-05-06 21:35:37', '2026-05-05 19:35:37'),
(81, 6, '0305dea423fe08ebf354ddbcb4dc2550fa0516bdd10c4d3d14d9d051dfbd313c', '2026-05-06 21:37:55', '2026-05-05 19:37:55'),
(82, 6, '29a200c6a2f005c102813fa9b4713a989464637fa910b9861bfa25351ccacdac', '2026-05-06 21:54:41', '2026-05-05 19:54:41'),
(84, 8, 'd72b6b1552ae6b6cf52558b7fb15e1e4ba63c096f10f68f9cbb49abe7e823a3e', '2026-05-06 22:06:41', '2026-05-05 20:06:41'),
(85, 8, '2bdb6c9a585247a4961709f4e293543bbd795016e4807fb3e778c3089e5e88d8', '2026-05-06 22:07:03', '2026-05-05 20:07:03'),
(86, 6, '131d33fd7565dc9e137c6555ceb46227cdc0579052955fc566cc5b6708e0f7c2', '2026-05-06 22:12:20', '2026-05-05 20:12:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('Critica','Alta','Media','Baja') DEFAULT 'Media',
  `status` enum('Abierto','En Progreso','En Revision','Pendiente','Resuelto') DEFAULT 'Abierto',
  `category` varchar(50) DEFAULT 'TI',
  `dept` varchar(50) DEFAULT NULL,
  `assignee_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `title`, `description`, `priority`, `status`, `category`, `dept`, `assignee_id`, `created_by`, `due_date`, `resolved_at`, `created_at`, `updated_at`) VALUES
('TK-011', 'No funciona el creador de tickets', 'Sigue sin funcionar pije', 'Alta', 'Abierto', 'TI', 'Ventas', 6, 6, NULL, NULL, '2026-04-15 20:54:15', '2026-04-15 20:54:15'),
('TK-016', 'Se necesitan estanterias en el area de calidad', 'Se solicita la instalcion de estanterias en el area de calidad para el almacenamiento temporal de escombros de ovalines y de mas', 'Media', 'Abierto', 'TI', 'Administración', 6, 6, '2026-04-22', NULL, '2026-04-15 21:37:14', '2026-04-15 21:37:14'),
('TK-025', 'falla de impresora', 'la impresora no esta resiviendo la señal y no imprime nada', 'Alta', 'Abierto', 'TI', 'Ventas', 1, 8, '2026-05-12', '2026-05-05 09:54:27', '2026-05-05 15:34:32', '2026-05-05 15:54:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_activity`
--

CREATE TABLE `ticket_activity` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `activity_type` enum('create','comment','status','assign','resolve') DEFAULT 'comment',
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ticket_activity`
--

INSERT INTO `ticket_activity` (`id`, `ticket_id`, `user_name`, `activity_type`, `message`, `created_at`) VALUES
(17, 'TK-011', 'Sistema', 'create', 'Ticket creado', '2026-04-15 20:54:15'),
(27, 'TK-016', 'Sistema', 'create', 'Ticket creado', '2026-04-15 21:37:14'),
(51, 'TK-025', 'Sistema', 'create', 'Ticket creado', '2026-05-05 15:34:32'),
(52, 'TK-025', 'Edwin Perez', 'comment', 'a', '2026-05-05 15:34:49'),
(54, 'TK-025', 'Administrador General', 'comment', 'ok', '2026-05-05 15:39:23'),
(55, 'TK-025', 'Sistema', 'status', 'Estado cambiado a Resuelto', '2026-05-05 15:48:52'),
(57, 'TK-025', 'Administrador General', 'status', 'Estado cambiado a En Progreso', '2026-05-05 15:49:33'),
(60, 'TK-025', 'Administrador General', 'status', 'Estado cambiado a Resuelto', '2026-05-05 15:54:27'),
(62, 'TK-025', 'Administrador General', 'status', 'Estado cambiado a Abierto', '2026-05-05 15:54:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','agent','user') DEFAULT 'user',
  `dept` varchar(50) DEFAULT NULL,
  `avatar` varchar(20) DEFAULT '#0F52BA',
  `active` tinyint(1) DEFAULT 1,
  `created_at` date DEFAULT curdate(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `dept`, `avatar`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Administrador General', 'admin@decomobil.com', '$2y$10$kgqLq5e0Fiv6uC0Uwp//7uxFe1l2g.G8cEGicJ/GOk7ZySfnRqaTO', 'admin', 'TI', '#0F52BA', 1, '2025-01-01', '2026-05-05 16:09:02'),
(6, 'Francisco Uribe', 'franciscou@decomobil.com', '$2y$10$5PNu1eknPIar9kXbBpdG9eqClpJuhXJ2tEAc4ErA8VLBD2E3A2Cya', 'agent', 'Producción', '#D93025', 1, '2026-04-15', '2026-05-05 16:09:36'),
(8, 'Edwin Perez', 'edd@decomobil.com', '$2y$10$Dj3XMgcuVCTnFz.JBBVz5OTKxIrjsorVPNUhvUnm2PqnCVnSt.9aa', 'user', 'Ventas', '#0D9488', 1, '2026-05-05', '2026-05-05 14:57:10'),
(10, 'Carlos', 'carlos@decomobil.com', '$2y$10$6CCMOtlGpN12aVfqSFWl1eG/myBy4dBNnADUy5oi5ltb0ghS6cwWu', 'user', 'RRHH', '#EA580C', 1, '2026-05-05', '2026-05-05 15:44:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_priority` (`priority`),
  ADD KEY `idx_tickets_assignee` (`assignee_id`),
  ADD KEY `idx_tickets_created_by` (`created_by`);

--
-- Indices de la tabla `ticket_activity`
--
ALTER TABLE `ticket_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_ticket` (`ticket_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `ticket_activity`
--
ALTER TABLE `ticket_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ticket_activity`
--
ALTER TABLE `ticket_activity`
  ADD CONSTRAINT `ticket_activity_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;