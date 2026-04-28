-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-04-2026 a las 23:10:17
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
(1, 'Administrador General', 'admin@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Direccion', '#0F52BA', 1, '2025-01-01', '2026-04-14 18:40:31'),
(2, 'Ana Garcia', 'ana@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'Contabilidad', '#15803D', 1, '2025-01-15', '2026-04-28 21:09:42'),
(3, 'Carlos Lopez', 'carlos@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'Producción', '#B45309', 1, '2025-01-15', '2026-04-28 21:09:49'),
(4, 'Maria Torres', 'maria@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Contabilidad', '#6D28D9', 1, '2025-02-01', '2026-04-14 18:40:31'),
(5, 'Pedro Ruiz', 'pedro@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Ventas', '#D93025', 0, '2025-02-10', '2026-04-28 15:17:46'),
(6, 'Carlos Acosta', 'carlosa@empresa.com', '$2y$10$DUTWCufo2bApIu1SXb8.pOmS6x/TMaOjUr4/wKnKOWaJdbqRAa4e6', 'admin', 'TI', '#15803D', 1, '2026-04-15', '2026-04-22 19:45:16'),
(7, 'Edwin De Niz', 'edwine@empresa.com', '$2y$10$aBjKpsvp5SD9SemzZIA6Ye8R1ggqlEglXUIm7QZnnDJpju5csmSWu', 'admin', 'Administración', '#0891B2', 1, '2026-04-22', '2026-04-22 19:45:37'),
(8, 'Raul Juares', 'rael@empresa.com', '$2y$10$EOH9ZNYRZQG5L51W8C24auLvrHWkA9OHqaxVjWa1.NtTJAwNEh4Fy', 'user', 'Ventas', '#0D9488', 1, '2026-04-22', '2026-04-22 20:31:15');

--
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
