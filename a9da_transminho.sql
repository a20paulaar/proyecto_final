-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: vl23934.dinaserver.com:3306
-- Generation Time: Mar 13, 2022 at 11:21 PM
-- Server version: 10.1.48-MariaDB-0+deb9u2
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a9da_transminho`
--

-- --------------------------------------------------------

--
-- Table structure for table `asignacion_asiento`
--

CREATE TABLE `asignacion_asiento` (
  `dni` varchar(9) DEFAULT NULL,
  `fecha_viaje` date NOT NULL,
  `id_expedicion` int(11) NOT NULL,
  `id_parada_origen` int(11) DEFAULT NULL,
  `id_parada_destino` int(11) DEFAULT NULL,
  `num_asiento` int(11) NOT NULL,
  `estado_reserva` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `horarios`
--

CREATE TABLE `horarios` (
  `id_expedicion` int(11) NOT NULL,
  `id_parada` int(11) NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `horarios`
--

INSERT INTO `horarios` (`id_expedicion`, `id_parada`, `hora`) VALUES
(1, 1, '12:10:00'),
(1, 2, '12:35:00'),
(1, 3, '12:45:00'),
(1, 4, '12:50:00'),
(1, 5, '13:00:00'),
(1, 6, '13:10:00'),
(1, 7, '13:55:00'),
(1, 8, '14:30:00'),
(1, 9, '14:50:00'),
(1, 10, '15:05:00'),
(2, 1, '15:10:00'),
(2, 2, '15:35:00'),
(2, 3, '15:45:00'),
(2, 4, '15:50:00'),
(2, 5, '16:00:00'),
(2, 6, '16:10:00'),
(2, 7, '16:55:00'),
(2, 8, '17:30:00'),
(2, 9, '17:50:00'),
(2, 10, '18:05:00'),
(3, 1, '15:50:00'),
(3, 2, '15:35:00'),
(3, 3, '15:25:00'),
(3, 4, '15:20:00'),
(3, 5, '15:10:00'),
(3, 6, '15:00:00'),
(3, 7, '14:15:00'),
(3, 8, '13:40:00'),
(3, 9, '13:20:00'),
(3, 10, '13:05:00'),
(4, 1, '18:50:00'),
(4, 2, '18:35:00'),
(4, 3, '18:25:00'),
(4, 4, '18:20:00'),
(4, 5, '18:10:00'),
(4, 6, '18:00:00'),
(4, 7, '17:15:00'),
(4, 8, '16:40:00'),
(4, 9, '16:20:00'),
(4, 10, '16:05:00'),
(5, 1, '20:10:00'),
(5, 2, '20:35:00'),
(5, 3, '20:45:00'),
(5, 4, '20:50:00'),
(5, 6, '21:10:00'),
(5, 5, '21:00:00'),
(5, 7, '21:55:00'),
(5, 8, '22:30:00'),
(5, 9, '22:50:00'),
(5, 10, '23:05:00'),
(6, 1, '22:50:00'),
(6, 2, '22:35:00'),
(6, 3, '22:25:00'),
(6, 4, '22:20:00'),
(6, 5, '22:10:00'),
(6, 6, '22:00:00'),
(6, 7, '21:15:00'),
(6, 8, '20:40:00'),
(6, 9, '20:20:00'),
(6, 10, '20:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `noticia` text NOT NULL,
  `imagen` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `titulo`, `noticia`, `imagen`) VALUES
(1, 'Adquisición de nuevos vehículos', 'Recientemente hemos llevado a cabo la adquisición de dos nuevos vehículos híbridos para nuestra flota, porque no hay nada que nos parezca más importante que el cuidado del medio ambiente y la comodidad de nuestros viajeros. Muchas gracias por su confianza.', 1),
(2, 'Mantenimiento de la web', 'El día de hoy entre las 7:00 y las 8:00 se llevará a cabo el mantenimiento de la web, por lo que no se podrá efectuar la compra de billetes. Disculpen las molestias.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `paradas`
--

CREATE TABLE `paradas` (
  `id_parada` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `paradas`
--

INSERT INTO `paradas` (`id_parada`, `nombre`) VALUES
(1, 'Vigo-Estación Autobuses'),
(2, 'Vigo-Hospital Meixoeiro'),
(3, 'Vigo-Aeroporto'),
(4, 'O Porriño'),
(5, 'Tui'),
(6, 'Valença do Minho'),
(7, 'Viana do Castelo'),
(8, 'Póvoa de Varzim'),
(9, 'Oporto-Aeroporto'),
(10, 'Oporto-Casa da Música');

-- --------------------------------------------------------

--
-- Table structure for table `registro`
--

CREATE TABLE `registro` (
  `id` int(11) NOT NULL,
  `usuario` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tarifas`
--

CREATE TABLE `tarifas` (
  `id_parada_origen` int(11) NOT NULL,
  `id_parada_destino` int(11) NOT NULL,
  `precio` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tarifas`
--

INSERT INTO `tarifas` (`id_parada_origen`, `id_parada_destino`, `precio`) VALUES
(1, 2, '0.00'),
(2, 3, '0.00'),
(3, 4, '3.50'),
(4, 5, '4.20'),
(5, 6, '2.50'),
(6, 7, '5.00'),
(7, 8, '4.00'),
(8, 9, '3.00'),
(9, 10, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `transacciones`
--

CREATE TABLE `transacciones` (
  `email` varchar(64) NOT NULL,
  `cantidad` decimal(5,2) NOT NULL,
  `metodo` varchar(1) NOT NULL,
  `fecha_hora` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `email` varchar(64) NOT NULL,
  `contrasenha` longtext NOT NULL,
  `perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`email`, `contrasenha`, `perfil`) VALUES
('admin@transminho.es', 'e10adc3949ba59abbe56e057f20f883e', 1),
('pepe@mail.com', 'e10adc3949ba59abbe56e057f20f883e', 2);

-- --------------------------------------------------------

--
-- Table structure for table `viajeros`
--

CREATE TABLE `viajeros` (
  `dni` varchar(9) NOT NULL,
  `email` varchar(64) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `apellidos` varchar(128) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `principal` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `viajeros`
--

INSERT INTO `viajeros` (`dni`, `email`, `nombre`, `apellidos`, `telefono`, `direccion`, `fecha_nacimiento`, `principal`) VALUES
('11111111A', 'admin@transminho.es', 'Paula', 'Álvarez Rocha', '666666666', 'Avda. Galicia 112', '1996-11-19', 1),
('55555555A', 'pepe@mail.com', 'Pepe', 'Perez', '600555444', 'Avda. Gran Vía 100', '2000-03-01', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asignacion_asiento`
--
ALTER TABLE `asignacion_asiento`
  ADD UNIQUE KEY `fecha_viaje` (`fecha_viaje`,`id_expedicion`,`id_parada_origen`,`id_parada_destino`,`num_asiento`);

--
-- Indexes for table `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`);

--
-- Indexes for table `paradas`
--
ALTER TABLE `paradas`
  ADD PRIMARY KEY (`id_parada`);

--
-- Indexes for table `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `viajeros`
--
ALTER TABLE `viajeros`
  ADD PRIMARY KEY (`dni`,`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registro`
--
ALTER TABLE `registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
