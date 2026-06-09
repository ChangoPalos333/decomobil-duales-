-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2026 a las 17:56:04
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
-- Estructura de tabla para la tabla `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `accent_color` varchar(20) DEFAULT '#0F52BA',
  `created_by` varchar(100) DEFAULT 'Sistema',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `accent_color`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'mañana no laboramos', 'mañana no se labora por que nomas no quiero y ya, sh', '#36a14b', 'Administrador General', '2026-05-20 14:19:14', '2026-05-20 14:19:14'),
(6, 'Aviso!!', 'A partir del 24 de Mayo del 2026 se establecerán nuevas reglas que se deberán seguir al pie de la letra para evitar conflicto con dicho reglamento.\n\nTal como la prohibición de armas, alcohol, fumar, etc.', '#ff0a0a', 'Administrador General', '2026-05-20 15:05:42', '2026-05-20 15:05:42'),
(7, 'Hola gei', 'adios gei', '#d5d737', 'Carlos Acosta', '2026-05-27 11:48:35', '2026-05-27 11:48:35'),
(8, 'jaksjaas', 'sadhsakjh', '#6c8ec1', 'Carlos Acosta', '2026-05-27 11:49:23', '2026-05-27 11:49:23'),
(9, 'edwin es putote', 'jaja k joto', '#3865a8', 'Carlos Acosta', '2026-05-27 11:49:57', '2026-05-27 11:49:57'),
(10, 'kjashdkjs', 's', '#323d4e', 'Carlos Acosta', '2026-05-27 11:51:17', '2026-05-27 11:51:17'),
(11, 'yi', 'a', '#2c5ca5', 'Carlos Acosta', '2026-05-27 11:56:26', '2026-05-27 11:56:26'),
(12, 'mañana no laboramos', 'sadasd', '#0f52ba', 'Carlos Acosta', '2026-05-27 12:56:11', '2026-05-27 12:56:11'),
(13, 'fasfsffasf', 'asdsadsa', '#42b80f', 'Carlos Acosta', '2026-05-27 12:56:39', '2026-05-27 12:56:39'),
(14, 'sadas', 'aea', '#1f314d', 'Carlos Acosta', '2026-05-27 13:52:57', '2026-05-27 13:52:57'),
(15, 'faf', 'sadasdf', '#1f314d', 'Carlos Acosta', '2026-05-27 13:53:14', '2026-05-27 13:53:14'),
(16, 'fsafsafa', 'fsaff', '#0f52ba', 'Carlos Acosta', '2026-05-27 14:01:23', '2026-05-27 14:01:23');

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
(41, 1, '6ca6b065a51fbc86e61ec642734f119c3ddaf4c3133ab7ff5eb3ae077979864a', '2026-04-29 23:22:48', '2026-04-28 21:22:48'),
(42, 1, '4879c106a6dab86d1b24c213ca2fba6cac337cef87153ec296ee30d6d9dee726', '2026-05-07 21:24:16', '2026-05-06 19:24:16'),
(43, 1, 'c3ae4e6fa488243693b3c19f20b2345982333e9b2021b8eb6dcfa695d50cbc86', '2026-05-07 21:44:41', '2026-05-06 19:44:41'),
(44, 1, '254249d0a5cab941cd84ca8e2137787bcb2d6d4f8ff7350a15c6f88275db0007', '2026-05-13 17:29:51', '2026-05-12 15:29:51'),
(45, 1, '5bc4df9754c803d9d3f1616795c385c09fc1fe4260424fd8c3dba1efa29fec75', '2026-05-13 23:05:01', '2026-05-12 21:05:01'),
(46, 1, 'd69d09f57d207639766bf284db9a66fbc4aab56e91972300c7fb15732f4c66d7', '2026-05-13 23:52:55', '2026-05-12 21:52:55'),
(47, 1, '708ffa2211a8207fc4f8795d1bda31e8e332cfd4e597ba946649267b3d237a8b', '2026-05-21 17:40:29', '2026-05-20 15:40:29'),
(48, 1, 'fc622b3b557d230314453549693bf4f6c84eec7377ed94ae5708a85183a5c542', '2026-05-21 17:43:24', '2026-05-20 15:43:24'),
(49, 10, '0c4957c19e1ba6d2451718ae02e0d41029862bd8bbce506a09185d397d4d4d89', '2026-05-21 17:44:44', '2026-05-20 15:44:44'),
(50, 1, '9cc4361e3a87eb52874f77b86971e0975aac2e326e760bb27ef6eb7c7d166eef', '2026-05-21 17:46:59', '2026-05-20 15:46:59'),
(51, 10, 'efb584a90cf34f0808c64a75c823156eb447a0a560f6d0a9101427145aa7a109', '2026-05-21 17:56:10', '2026-05-20 15:56:10'),
(53, 9, 'fc54b9c8edd84285f4897b0da708ff2c6588c54e7bc85a074e14457f8b409a8b', '2026-05-21 18:12:06', '2026-05-20 16:12:06'),
(54, 10, '6b9f715f2f53545f403d88fe57704bcb17239581317707f83a2231c535f5e531', '2026-05-21 18:13:04', '2026-05-20 16:13:04'),
(55, 10, '19dda08b49420410dec4bf2964e05b8d13fd7dd364648deda497876c11a12313', '2026-05-21 18:17:25', '2026-05-20 16:17:25'),
(56, 10, '3360dbe758d39d02aa69502b3fe849abde10292cc22d92691650ba1f0dd3e5da', '2026-05-21 18:25:15', '2026-05-20 16:25:15'),
(58, 1, '6533e50ae6a7bf6ffded921a97d115b534a030fac239cc022c6fa3c537bece35', '2026-05-21 18:30:30', '2026-05-20 16:30:30'),
(59, 1, '702e1580e61e847fe185b882fa78e35686657ac6be7a8d9d87843fb2f8e3e35a', '2026-05-21 18:34:59', '2026-05-20 16:34:59'),
(60, 9, '3535197ca392bc77d9d9c73933a59cec49d7fe65f5a2cc623cee6eac0d59ffe1', '2026-05-21 18:36:27', '2026-05-20 16:36:27'),
(61, 9, 'eb986a1a0a1510ac7c0002b4dc8db8cc607080d07d9e35dadd5781bbab78b3c6', '2026-05-21 18:37:56', '2026-05-20 16:37:56'),
(62, 10, '098491afc205f1ad667f8e09105a63d5cdb3b2a3dfae6a4cfa651ed31d27a667', '2026-05-21 19:03:47', '2026-05-20 17:03:47'),
(63, 9, 'c06e49171946bb596d5057d7b44c8fddb3bbe0ea6d465abdba584a06ff1d8638', '2026-05-21 19:05:18', '2026-05-20 17:05:18'),
(64, 9, '357d9b43f0be92b60992b745ac2197833b20dd07edcefdbbc802c227f9550389', '2026-05-21 19:19:18', '2026-05-20 17:19:18'),
(65, 10, '410bc7f7dfcc1bfac9eff7df66a0ae1fbf038d1a3a6f4d1f6714faaee46a994d', '2026-05-21 20:34:06', '2026-05-20 18:34:06'),
(66, 10, '23d8ac40ee52b1129009e180318033b1618cc2be0b871029367cf828270d5e5e', '2026-05-21 20:37:24', '2026-05-20 18:37:24'),
(67, 9, '94f0890616f60ae125d000257528a19b195970d8357e39a536797db037884015', '2026-05-21 20:37:51', '2026-05-20 18:37:51'),
(68, 10, 'ba0416af666732e64c1bf51f1204ab62c2db9aae7152102a777099a900c384c6', '2026-05-21 20:49:15', '2026-05-20 18:49:15'),
(69, 9, '495ebb0fdfbbb685e64e6d01cbcf2fceee0f6cc8652eb1404189bfe93761ae4d', '2026-05-21 20:49:33', '2026-05-20 18:49:33'),
(70, 1, '6518346ce03338302ee20fedd557c15b7abedfc5a6511dec5f8888e0ec338f5e', '2026-05-21 21:46:34', '2026-05-20 19:46:34'),
(71, 1, '8a79edab3ff3e586512d120fd5bf1272df9bee75d29f857ad05c113215cb7abd', '2026-05-21 22:09:43', '2026-05-20 20:09:43'),
(72, 10, '6a8c114a52a483f1b781e854e938586de05c191f0b213243830b366d8a27a602', '2026-05-21 22:19:41', '2026-05-20 20:19:41'),
(73, 1, '2f44168c73098b7889c2a3fa61ed9f86e64a7fb0a1c558003bc9c1fade718362', '2026-05-21 22:54:49', '2026-05-20 20:54:49'),
(74, 10, 'ed2018b7762b8a8eb7cfefbd23fcacf85e1f63d6bb2dd6e0f69d4bb49afe7405', '2026-05-21 22:55:39', '2026-05-20 20:55:39'),
(75, 1, '56cdd861a479901b0279b38489821690042f6f60359b77ffe950848f15d65543', '2026-05-21 22:56:52', '2026-05-20 20:56:52'),
(76, 10, '93edb2cdcd400f99bb58043e2c85c366dbfc6ac9a78ad749d5556c04f88bfc0f', '2026-05-21 22:57:19', '2026-05-20 20:57:19'),
(77, 1, '422b52c31212b43478040f4739196526af00b32a9d988e69806c8d61935e439e', '2026-05-21 22:57:39', '2026-05-20 20:57:39'),
(78, 10, '1c225f313c0234153c9d79e8f2073030c161e6661ea976cbd57f2097c09ea7db', '2026-05-21 22:58:02', '2026-05-20 20:58:02'),
(79, 1, '3e8109b6e784bd8b165283f044e0ef7f8784cfc7d9d878c5c174bca4ad6aedd5', '2026-05-21 22:58:28', '2026-05-20 20:58:28'),
(80, 10, 'd15db63c7ef57021b37a4fde607a44768a6262faf65a637b56b984abddd43286', '2026-05-21 23:00:00', '2026-05-20 21:00:00'),
(81, 1, '39f0675909476c9a3f5dfcc635c307247cf40f62fd6ff80cb8fb2fa726be5043', '2026-05-21 23:03:47', '2026-05-20 21:03:47'),
(82, 10, '9d4078f0f309971f25ae90ddf03508726c9aa42236af68a2af1b3e3de6e3454e', '2026-05-21 23:07:27', '2026-05-20 21:07:27'),
(83, 10, '5e675b14cfab2e3a4b78c756fc0200fdb2dcfb1ba14583c8ae9f198d762d5ed1', '2026-05-21 23:07:33', '2026-05-20 21:07:33'),
(84, 9, 'b5bd9c3037248a2d007a04d7efddae90f40a07d756dc02a22619eecb279d81da', '2026-05-21 23:07:39', '2026-05-20 21:07:39'),
(85, 10, 'a4121c2e848a581d28d8fc680f2eba0755522386401645ce2c8d833716dcd039', '2026-05-21 23:12:44', '2026-05-20 21:12:44'),
(86, 10, '5481d5daa1bcc7e999fe39c8336497ee0b851a75a5923d8f1c2688e8a180de09', '2026-05-28 17:31:49', '2026-05-27 15:31:49'),
(87, 1, '870f1c6eca32ae099f6e0b844fea146b01a2d3975bc14c938b008d485251efef', '2026-05-28 17:35:17', '2026-05-27 15:35:17'),
(88, 9, 'dc6517d826367fe7d1ec147fbde9250698b41c63ca36658bf61c5ba83df843d2', '2026-05-28 18:24:57', '2026-05-27 16:24:57'),
(89, 1, 'de0da975c45ec4f3721f47b06350fcde084b504b5d02f10022d2e57e54b09880', '2026-05-28 18:25:28', '2026-05-27 16:25:28'),
(90, 9, 'c13d46edd5dfbd495b33ea92e327d1565b852ac4adc131c0ade5f0f66c2f40b1', '2026-05-28 18:26:23', '2026-05-27 16:26:23'),
(91, 10, '2eb03d98e80d12df473ebe734a7f8cafa2d820425c3ce107cdbc9ef4996e9cc6', '2026-05-28 18:46:21', '2026-05-27 16:46:21'),
(92, 10, '9870a16273bac3c67cf6b671393525ea0dde9522c92a2e4295e61f87e3d6086d', '2026-05-28 18:50:07', '2026-05-27 16:50:07'),
(93, 10, 'b660646f56eebd51ec6d61fd0a347cbd44b7abcf60b8c2ccdcbcfcba7118751a', '2026-05-28 20:01:16', '2026-05-27 18:01:16'),
(94, 10, 'fc5d3f09aeb943d86ca1ed2c1657720b0429181f4e697db9ee3e835c1ff1e998', '2026-05-28 21:18:27', '2026-05-27 19:18:27'),
(95, 10, '43598054397f0431b149d1a3910f67fc24ef9ed435a788254185f31108d829b3', '2026-05-28 21:21:33', '2026-05-27 19:21:33'),
(96, 1, 'a07b6836d7cc5652061bcdc087321f6b305c627d88e75f50778fc77b6e126266', '2026-05-28 21:23:11', '2026-05-27 19:23:11'),
(97, 10, '8a2920376e3b7a15b8881d879b21b527bfdd67a5af3092a4a56af07fee2711b0', '2026-05-28 21:25:26', '2026-05-27 19:25:26'),
(100, 10, '701487d7c4c458aa5604e02e7112d4013fb8f2c0fe7e41e22e19ea38bb2c9886', '2026-05-28 22:05:10', '2026-05-27 20:05:10'),
(101, 10, '6ea5d9216207dd28685a16988c0932e1649bf08deddf2f8c564b08f996c0a3da', '2026-05-28 22:17:52', '2026-05-27 20:17:52'),
(102, 9, 'c5154491b822eef40715a49465b5c53c84d8948ec67307f7dd3ae0476aeaee7f', '2026-05-28 22:19:21', '2026-05-27 20:19:21'),
(103, 9, 'be1149b55e81bd9519ab62cc246236310bd41b3451cef2773094c312fa683a1f', '2026-05-28 22:45:50', '2026-05-27 20:45:50'),
(104, 10, '17d700b5db4fa5685954e4d1cb6d7b0fc8a5709a589b9f826fdcdd1a0bf1d8fc', '2026-05-28 22:50:02', '2026-05-27 20:50:02'),
(105, 1, '686f1e20c3c79da8e78567720b0720a2369b38df3ec8fe5039402c558ad1f84e', '2026-05-28 23:18:21', '2026-05-27 21:18:21'),
(106, 9, 'b010ce7954963f6d72710f93da3c429dcd30bee5077d2af7a7323de65982f754', '2026-05-28 23:18:35', '2026-05-27 21:18:35'),
(107, 9, 'c46eaf386af493f1afb146728fd3435f8a29491db752b1ef71ef54818a46fe01', '2026-06-10 16:47:27', '2026-06-09 14:47:27'),
(108, 9, '0e1b2b0bfce8e0928ec310ef42c10987676393626135cd5bd039771fcd7e92f4', '2026-06-10 17:03:06', '2026-06-09 15:03:06'),
(109, 10, 'b33fb05bd326459934128ed053b25161f96c434ef2f0c02f789003bf563939c0', '2026-06-10 17:04:07', '2026-06-09 15:04:07');

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
('TK-001', 'Se necesitan estanterias en el area de calidad', 'urgente', 'Media', 'Abierto', 'TI', 'Direccion', 9, 10, '2026-06-03', NULL, '2026-05-27 20:18:10', '2026-05-27 20:18:10'),
('TK-002', 'Se jodio edwin por chambear en aurrera', 'asdasd', 'Media', 'Abierto', 'TI', 'RRHH', 9, 10, '2026-06-03', NULL, '2026-05-27 20:28:32', '2026-05-27 20:28:32'),
('TK-003', 'sadas', 'sadsa', 'Media', 'Abierto', 'TI', 'RH', NULL, 9, '2026-06-03', NULL, '2026-05-27 20:40:44', '2026-05-27 20:40:44'),
('TK-005', 'Se necesitan estanterias en el area de calidad', 'jkjh', 'Media', 'Abierto', 'TI', '⚙️Mantenimiento', 9, 10, '2026-06-03', NULL, '2026-05-27 20:45:31', '2026-05-27 20:45:31'),
('TK-006', 'Edwin es putote', 'sakhdksajdhkajs', 'Media', 'Abierto', 'TI', '🧭Direccion', 9, 10, '2026-06-16', NULL, '2026-06-09 15:04:55', '2026-06-09 15:04:55');

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
(182, 'TK-001', 'Sistema', 'create', 'Ticket creado', '2026-05-27 20:18:10'),
(183, 'TK-001', 'Usuario Kaka', 'comment', 'huh', '2026-05-27 20:18:28'),
(184, 'TK-001', 'Usuario Kaka', 'comment', 'huhhh', '2026-05-27 20:18:39'),
(185, 'TK-001', 'Usuario Kaka', 'comment', 'ey', '2026-05-27 20:18:56'),
(186, 'TK-001', 'Carlos Acosta', 'comment', 'baboso', '2026-05-27 20:19:38'),
(187, 'TK-001', 'Usuario Kaka', 'comment', 'ju', '2026-05-27 20:22:51'),
(188, 'TK-001', 'Usuario Kaka', 'comment', 'i am conquered', '2026-05-27 20:24:58'),
(189, 'TK-001', 'Carlos Acosta', 'comment', 'iji', '2026-05-27 20:26:06'),
(190, 'TK-001', 'Carlos Acosta', 'comment', 'hasldha', '2026-05-27 20:26:46'),
(191, 'TK-002', 'Sistema', 'create', 'Ticket creado', '2026-05-27 20:28:32'),
(192, 'TK-002', 'Carlos Acosta', 'comment', 'asadasd', '2026-05-27 20:28:52'),
(193, 'TK-002', 'Usuario Kaka', 'comment', 'asjdas', '2026-05-27 20:35:10'),
(194, 'TK-003', 'Sistema', 'create', 'Ticket creado', '2026-05-27 20:40:44'),
(196, 'TK-005', 'Sistema', 'create', 'Ticket creado', '2026-05-27 20:45:31'),
(197, 'TK-005', 'Carlos Acosta', 'comment', 'asdasdf', '2026-05-27 20:46:39'),
(198, 'TK-005', 'Carlos Acosta', 'comment', 'asdas', '2026-05-27 20:47:05'),
(199, 'TK-005', 'Carlos Acosta', 'comment', 'huh', '2026-05-27 20:50:20'),
(200, 'TK-005', 'Carlos Acosta', 'comment', 'chas', '2026-05-27 20:53:20'),
(201, 'TK-005', 'Carlos Acosta', 'comment', 'hasda', '2026-05-27 21:04:49'),
(202, 'TK-005', 'Carlos Acosta', 'comment', 'afsafa', '2026-05-27 21:05:05'),
(203, 'TK-005', 'Carlos Acosta', 'comment', 'afsfsafasfsafsa', '2026-05-27 21:05:11'),
(204, 'TK-005', 'Carlos Acosta', 'comment', 'huh', '2026-05-27 21:05:16'),
(205, 'TK-005', 'Carlos Acosta', 'comment', 'asdaf', '2026-05-27 21:11:27'),
(206, 'TK-005', 'Carlos Acosta', 'comment', 'asfasfa', '2026-05-27 21:19:00'),
(207, 'TK-005', 'Carlos Acosta', 'comment', 'asfas', '2026-05-27 21:19:44'),
(208, 'TK-005', 'Carlos Acosta', 'comment', 'asasda', '2026-05-27 21:19:52'),
(209, 'TK-005', 'Carlos Acosta', 'comment', 'saasffsafsa', '2026-05-27 21:20:02'),
(210, 'TK-005', 'Carlos Acosta', 'comment', 'kjkjh', '2026-05-27 21:20:15'),
(211, 'TK-005', 'Usuario Kaka', 'comment', 'aaa', '2026-05-27 21:20:55'),
(212, 'TK-005', 'Usuario Kaka', 'comment', 'e', '2026-05-27 21:21:25'),
(213, 'TK-005', 'Usuario Kaka', 'comment', 'a', '2026-05-27 21:21:30'),
(214, 'TK-005', 'Usuario Kaka', 'comment', 'sd', '2026-05-27 21:21:33'),
(215, 'TK-005', 'Usuario Kaka', 'comment', 'f', '2026-05-27 21:21:35'),
(216, 'TK-005', 'Usuario Kaka', 'comment', 'asd', '2026-05-27 21:25:06'),
(217, 'TK-005', 'Usuario Kaka', 'comment', 'a', '2026-05-27 21:25:16'),
(218, 'TK-005', 'Usuario Kaka', 'comment', 'dd', '2026-05-27 21:54:04'),
(219, 'TK-005', 'Usuario Kaka', 'comment', 'gfff', '2026-05-27 21:54:13'),
(220, 'TK-005', 'Carlos Acosta', 'comment', 'hhhh', '2026-05-27 21:54:23'),
(221, 'TK-005', 'Usuario Kaka', 'comment', 'hhhhh', '2026-05-27 21:55:10'),
(222, 'TK-005', 'Usuario Kaka', 'comment', 'hhhhh', '2026-05-27 21:55:21'),
(223, 'TK-005', 'Usuario Kaka', 'comment', 'hola', '2026-05-27 21:56:15'),
(224, 'TK-006', 'Sistema', 'create', 'Ticket creado', '2026-06-09 15:04:55'),
(225, 'TK-006', 'Usuario Kaka', 'comment', 'e joto', '2026-06-09 15:05:30'),
(226, 'TK-006', 'Usuario Kaka', 'comment', 'joto', '2026-06-09 15:05:45'),
(227, 'TK-006', 'Usuario Kaka', 'comment', 'joto', '2026-06-09 15:06:16'),
(228, 'TK-006', 'Carlos Acosta', 'comment', 'gei', '2026-06-09 15:06:34'),
(229, 'TK-006', 'Carlos Acosta', 'comment', 'puto', '2026-06-09 15:22:43'),
(230, 'TK-006', 'Usuario Kaka', 'comment', 'joto', '2026-06-09 15:23:17'),
(231, 'TK-006', 'Usuario Kaka', 'comment', 'jotote', '2026-06-09 15:23:27'),
(232, 'TK-006', 'Usuario Kaka', 'comment', 'gei', '2026-06-09 15:23:30'),
(233, 'TK-006', 'Usuario Kaka', 'comment', 'ewe', '2026-06-09 15:23:52'),
(234, 'TK-006', 'Usuario Kaka', 'comment', 'gei', '2026-06-09 15:24:09'),
(235, 'TK-006', 'Carlos Acosta', 'comment', 'joto', '2026-06-09 15:25:43'),
(236, 'TK-006', 'Usuario Kaka', 'comment', 'joto', '2026-06-09 15:36:26'),
(237, 'TK-006', 'Usuario Kaka', 'comment', 'gei', '2026-06-09 15:36:37'),
(238, 'TK-006', 'Usuario Kaka', 'comment', 'fei', '2026-06-09 15:36:48'),
(239, 'TK-006', 'Usuario Kaka', 'comment', 'aksjhdajshdjsadkadaskdlalksdlsahdlahsdlkshkddsalklkdsakhlskhldsa', '2026-06-09 15:36:55'),
(240, 'TK-006', 'Usuario Kaka', 'comment', 'aaaa', '2026-06-09 15:37:05'),
(241, 'TK-006', 'Carlos Acosta', 'comment', 'asdad', '2026-06-09 15:39:26'),
(242, 'TK-006', 'Carlos Acosta', 'comment', 'no w', '2026-06-09 15:39:56'),
(243, 'TK-006', 'Carlos Acosta', 'comment', 'klsad', '2026-06-09 15:40:00'),
(244, 'TK-006', 'Carlos Acosta', 'comment', 'hola', '2026-06-09 15:40:09'),
(245, 'TK-006', 'Carlos Acosta', 'comment', 'joto', '2026-06-09 15:44:59'),
(246, 'TK-006', 'Carlos Acosta', 'comment', 'gei', '2026-06-09 15:45:36');

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
(1, 'Administrador General', 'admin@empresa.com', '$2y$10$KmEWjFr0QetylozBLR/edugdedbODntclD079cBjJdxe0/AEIcJXm', 'admin', 'TI', '#D93025', 1, '2025-01-01', '2026-05-20 16:11:15'),
(9, 'Carlos Acosta', 'carlos.acosta@empresa.com', '$2y$10$pHYRpNxaBSX/N6sluf4oxOhfWi3IJTIcN8dRaxhdsFN1751sXBI5y', 'admin', 'TI', '#D93025', 1, '2026-05-12', '2026-05-20 16:11:09'),
(10, 'Usuario Kaka', 'ejemplo_user@gmail.com', '$2y$10$vEVZTKzj.kdx8GfP/C9h5.QxLvZCb8A7FyS24yUE8Rqw85HzZ1qkm', 'user', 'TI', '#0F52BA', 1, '2026-05-20', '2026-05-20 15:47:39');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de la tabla `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT de la tabla `ticket_activity`
--
ALTER TABLE `ticket_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

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
