-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-01-2023 a las 18:12:44
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `backend_csi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agentes_lideres_por_zona`
--

CREATE TABLE `agentes_lideres_por_zona` (
  `id_agente_lider_zona` int(11) NOT NULL,
  `fk_id_agente_agente_lider_zona` int(11) DEFAULT NULL,
  `fk_id_zona_agente_lider_zona` int(11) DEFAULT NULL,
  `date_created_agente_lider_zona` datetime DEFAULT NULL,
  `date_update_agente_lider_zona` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id_alerta` int(11) NOT NULL,
  `latitud_alerta` float DEFAULT NULL,
  `longitud_alerta` float DEFAULT NULL,
  `tipo_evento_alerta` varchar(255) DEFAULT NULL,
  `fecha_alerta` datetime DEFAULT NULL,
  `comentario_alerta` varchar(255) DEFAULT NULL,
  `ruta_foto_alerta_alerta` varchar(255) DEFAULT NULL,
  `fk_id_usuario_cliente_alerta` int(11) NOT NULL,
  `fk_servicio_por_zona_alerta` int(11) NOT NULL,
  `date_created_alerta` datetime DEFAULT NULL,
  `date_update_alerta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id_contacto` int(11) NOT NULL,
  `nombre_contacto` varchar(255) DEFAULT NULL,
  `telefono_contacto` varchar(45) DEFAULT NULL,
  `descripcion_contacto` varchar(255) DEFAULT NULL,
  `fk_id_usuario_cliente_contacto` int(11) DEFAULT NULL,
  `date_created_contacto` datetime DEFAULT NULL,
  `date_update_contacto` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id_documento` int(11) NOT NULL,
  `ruta_documento` varchar(255) DEFAULT NULL,
  `fecha_carga_documento` datetime DEFAULT NULL,
  `fk_id_usuario_cliente_documento` int(11) DEFAULT NULL,
  `fk_id_usuario_agente_documento` int(11) DEFAULT NULL,
  `date_created_documento` datetime DEFAULT NULL,
  `date_update_documento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `establecimientos`
--

CREATE TABLE `establecimientos` (
  `id_establecimiento` int(11) NOT NULL,
  `nombre_establecimiento` varchar(255) DEFAULT NULL,
  `imagen_establecimiento` varchar(255) DEFAULT NULL,
  `fk_id_zona_establecimiento` int(11) DEFAULT NULL,
  `date_created_establecimiento` datetime DEFAULT NULL,
  `date_update_establecimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insignias`
--

CREATE TABLE `insignias` (
  `id_insignia` int(11) NOT NULL,
  `descripcion_insignia` varchar(255) DEFAULT NULL,
  `ruta_icono_insignia` varchar(255) DEFAULT NULL,
  `puntos_insignia` int(11) DEFAULT NULL,
  `date_created_insignia` datetime DEFAULT NULL,
  `date_update_insignia` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insignias_ganadas`
--

CREATE TABLE `insignias_ganadas` (
  `id_insignia_ganada` int(11) NOT NULL,
  `fk_id_usuario_cliente_insignia_ganada` int(11) DEFAULT NULL,
  `fk_id_insignia_por_zona_insignia_ganada` int(11) DEFAULT NULL,
  `date_created_insignia_ganada` datetime DEFAULT NULL,
  `date_update_insignia_ganada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insignias_por_zona`
--

CREATE TABLE `insignias_por_zona` (
  `id_insignia_por_zona` int(11) NOT NULL,
  `fk_id_zona_insignia_por_zona` int(11) DEFAULT NULL,
  `fk_id_insignia_insignia_por_zona` int(11) DEFAULT NULL,
  `date_created_insignia_por_zona` datetime DEFAULT NULL,
  `date_update_insignia_por_zona` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paradas`
--

CREATE TABLE `paradas` (
  `id_parada` int(11) NOT NULL,
  `descripcion_parada` varchar(255) DEFAULT NULL,
  `place_id_parada` varchar(255) DEFAULT NULL,
  `fk_id_viaje_parada` int(11) DEFAULT NULL,
  `date_created_parada` datetime DEFAULT NULL,
  `date_update_parada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

CREATE TABLE `planes` (
  `id_plan` int(11) NOT NULL,
  `descripcion_plan` varchar(255) DEFAULT NULL,
  `date_created_plan` datetime DEFAULT NULL,
  `date_update_plan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `planes`
--

INSERT INTO `planes` (`id_plan`, `descripcion_plan`, `date_created_plan`, `date_update_plan`) VALUES
(1, 'esto es una prueba', '2023-01-18 00:00:00', '2023-01-18 17:15:14'),
(2, 'esto es una prueba 2', '2023-01-18 00:00:00', '2023-01-18 17:15:43'),
(3, 'esto es una prueba 3', '2023-01-18 00:00:00', '2023-01-19 11:04:46'),
(5, 'esto es un plan creado con autorizacion JWT', '0000-00-00 00:00:00', '2023-01-23 16:05:14'),
(6, 'edicion con JWT', '2023-01-26 00:00:00', '2023-01-23 16:05:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes_comprados`
--

CREATE TABLE `planes_comprados` (
  `id_plan_comprado` int(11) NOT NULL,
  `fecha_compra_plan_comprado` datetime DEFAULT NULL,
  `activo_plan_comprado` int(11) DEFAULT NULL,
  `fk_id_plan_plan_comprado` int(11) DEFAULT NULL,
  `fk_id_usuario_cliente_plan_comprado` int(11) DEFAULT NULL,
  `date_created_plan_comprado` datetime DEFAULT NULL,
  `date_update_plan_comprado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posiciones_agentes`
--

CREATE TABLE `posiciones_agentes` (
  `id_posicion_agente` int(11) NOT NULL,
  `latitud_posicion_agente` float DEFAULT NULL,
  `longitud_posicion_agente` float DEFAULT NULL,
  `origen_posicion_agente` varchar(255) DEFAULT NULL,
  `fecha_posicion_agente` datetime DEFAULT NULL,
  `id_dispositivo_posicion_agente` varchar(255) DEFAULT NULL,
  `fk_id_usuario_agente` int(11) DEFAULT NULL,
  `date_created_posicion_agente` datetime DEFAULT NULL,
  `date_update_posicion_agente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posiciones_clientes`
--

CREATE TABLE `posiciones_clientes` (
  `id_posicion_cliente` int(11) NOT NULL,
  `latitud_posicion_cliente` float DEFAULT NULL,
  `longitud_posicion_cliente` float DEFAULT NULL,
  `origen_posicion_cliente` varchar(255) DEFAULT NULL,
  `fecha_posicion_cliente` datetime DEFAULT NULL,
  `id_dispositivo_posicion_cliente` varchar(255) DEFAULT NULL,
  `fk_id_usuario_cliente_posicion_cliente` int(11) DEFAULT NULL,
  `date_created__posicion_cliente` datetime DEFAULT NULL,
  `date_update__posicion_cliente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre_promocion` varchar(255) DEFAULT NULL,
  `descripcion_promocion` varchar(255) DEFAULT NULL,
  `puntos_promocion` int(11) DEFAULT NULL,
  `date_created_promocion` datetime DEFAULT NULL,
  `date_update_promocion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones_por_establecimiento`
--

CREATE TABLE `promociones_por_establecimiento` (
  `id_promocion_por_establecimiento` int(11) NOT NULL,
  `fk_id_establecimiento_promocion_por_establecimiento` int(11) DEFAULT NULL,
  `fk_id_promocion_promocion_por_establecimiento` int(11) DEFAULT NULL,
  `date_created_promocion_por_establecimiento` datetime DEFAULT NULL,
  `date_update_promocion_por_establecimiento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntos_ganados`
--

CREATE TABLE `puntos_ganados` (
  `id_punto_ganado` int(11) NOT NULL,
  `puntos_ganados_punto_ganado` int(11) DEFAULT NULL,
  `acumulado_puntos_punto_ganado` int(11) DEFAULT NULL,
  `fk_id_usuario_cliente_punto_ganado` int(11) DEFAULT NULL,
  `fk_id_servicio_por_zona_punto_ganado` int(11) DEFAULT NULL,
  `date_created_punto_ganado` datetime DEFAULT NULL,
  `date_update_punto_ganado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reacciones_agentes`
--

CREATE TABLE `reacciones_agentes` (
  `id_reaccion_agente` int(11) NOT NULL,
  `latitud_reaccion_agente` varchar(255) DEFAULT NULL,
  `longitud_reaccion_agente` varchar(255) DEFAULT NULL,
  `evidencia_reaccion_agente` varchar(255) DEFAULT NULL,
  `fecha_reaccion_agente` datetime DEFAULT NULL,
  `confirmacion_agente_reaccion_agente` int(11) DEFAULT NULL,
  `confirmacion_cliente_reaccion_agente` int(11) DEFAULT NULL,
  `notificacion_agente_reaccion_agente` int(11) DEFAULT NULL,
  `notificacion_cliente_reaccion_agente` int(11) DEFAULT NULL,
  `fk_id_usuario_agente_reaccion_agente` int(11) DEFAULT NULL,
  `fk_id_alerta_reaccion_agente` int(11) DEFAULT NULL,
  `date_created_reaccion_agente` datetime DEFAULT NULL,
  `date_update_reaccion_agente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reacciones_cliente_cliente`
--

CREATE TABLE `reacciones_cliente_cliente` (
  `id_reaccion_cliente_cliente` int(11) NOT NULL,
  `latitud_reaccion_cliente_cliente` varchar(255) DEFAULT NULL,
  `longitud_reaccion_cliente_cliente` varchar(255) DEFAULT NULL,
  `evidencia_reaccion_cliente_cliente` varchar(255) DEFAULT NULL,
  `fecha_reaccion_cliente_cliente` datetime DEFAULT NULL,
  `fk_id_alerta_reaccion_cliente_cliente` int(11) DEFAULT NULL,
  `fk_id_usuario_cliente_reaccion_cliente_cliente` int(11) DEFAULT NULL,
  `date_created_reaccion_cliente_cliente` datetime DEFAULT NULL,
  `date_update_reaccion_cliente_cliente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_fotograficos_viajes`
--

CREATE TABLE `registros_fotograficos_viajes` (
  `id_registro_fotografico_viaje` int(11) NOT NULL,
  `ruta_registro_fotografico_viaje` varchar(255) DEFAULT NULL,
  `fk_id_viaje_registro_fotografico_viaje` int(11) DEFAULT NULL,
  `date_created_registro_fotografico_viaje` datetime DEFAULT NULL,
  `date_update_registro_fotografico_viaje` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `descripcion_servicio` varchar(255) DEFAULT NULL,
  `imagen_servicio` varchar(255) DEFAULT NULL,
  `puntos_servicio` int(11) DEFAULT NULL,
  `date_created_servicio` datetime DEFAULT NULL,
  `date_update_servicio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_por_zona`
--

CREATE TABLE `servicios_por_zona` (
  `id_servicos_por_zona` int(11) NOT NULL,
  `fk_id_servicio_servicos_por_zona` int(11) DEFAULT NULL,
  `fk_id_zona_servicos_por_zona` int(11) DEFAULT NULL,
  `date_created_servicos_por_zona` datetime DEFAULT NULL,
  `date_update__servicos_por_zona` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_usuarios`
--

CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int(11) NOT NULL,
  `descricion_tipo_usuario` varchar(45) DEFAULT NULL,
  `activado_tipo_usuario` int(11) DEFAULT NULL,
  `date_created_tipo_usuario` datetime DEFAULT NULL,
  `date_update_tipo_usuario` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipos_usuarios`
--

INSERT INTO `tipos_usuarios` (`id_tipo_usuario`, `descricion_tipo_usuario`, `activado_tipo_usuario`, `date_created_tipo_usuario`, `date_update_tipo_usuario`) VALUES
(1, 'Agente de transito', 1, '2023-01-17 11:02:07', '2023-01-17 11:02:07'),
(2, 'Agente de policia', 1, '2023-01-17 11:02:07', '2023-01-17 11:02:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_agentes`
--

CREATE TABLE `usuarios_agentes` (
  `id_usuario_agente` int(11) NOT NULL,
  `cedula_usuario_agente` int(11) DEFAULT NULL,
  `nombre_usuario_agente` varchar(255) DEFAULT NULL,
  `apellido_usuario_agente` varchar(255) DEFAULT NULL,
  `telefono_usuario_agente` varchar(255) DEFAULT NULL,
  `direccion_usuario_agente` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `token_exp` varchar(255) DEFAULT NULL,
  `foto_perfil_usuario_agente` varchar(255) DEFAULT NULL,
  `activo_usuario_agente` int(11) DEFAULT NULL,
  `estado_usuario_agente` int(11) DEFAULT NULL,
  `eliminado_usuario_agente` int(11) DEFAULT NULL,
  `lastlogin_usuario_agente` datetime DEFAULT NULL,
  `fk_id_tipo_usuario_usuario_agente` int(11) DEFAULT NULL,
  `date_created_usuario_agente` datetime DEFAULT NULL,
  `date_update_usuario_agente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios_agentes`
--

INSERT INTO `usuarios_agentes` (`id_usuario_agente`, `cedula_usuario_agente`, `nombre_usuario_agente`, `apellido_usuario_agente`, `telefono_usuario_agente`, `direccion_usuario_agente`, `email`, `password`, `token`, `token_exp`, `foto_perfil_usuario_agente`, `activo_usuario_agente`, `estado_usuario_agente`, `eliminado_usuario_agente`, `lastlogin_usuario_agente`, `fk_id_tipo_usuario_usuario_agente`, `date_created_usuario_agente`, `date_update_usuario_agente`) VALUES
(22, 2147483647, 'Andres', 'Restrepo', '4156123', 'reservada', 'arz.950203@gmail.com', '$2a$07$azybxcags23425sdg23sde0y.SmidIpyazsKYTKJ1H5Xi9e/F.uCa', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzQ1NzkwNzUsImV4cCI6MTY3NDY2NTQ3NSwiZGF0YSI6eyJpZCI6MjIsImVtYWlsIjoiYXJ6Ljk1MDIwM0BnbWFpbC5jb20ifX0._Hte1meSL4qd4VpKuS0oFnHafxLINh69zZmsuf4cC2k', '1674665475', NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-24 00:00:00', '2023-01-24 09:50:15'),
(23, 2147483647, 'Andres', 'Restrepo', '4156123', 'reservada', 'andres@gmail.com', '$2a$07$azybxcags23425sdg23sde0y.SmidIpyazsKYTKJ1H5Xi9e/F.uCa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-24 00:00:00', '2023-01-24 11:08:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_clientes`
--

CREATE TABLE `usuarios_clientes` (
  `id_usuario_cliente` int(11) NOT NULL,
  `cedula_usuario_cliente` int(11) DEFAULT NULL,
  `nombre_usuario_cliente` varchar(255) DEFAULT NULL,
  `apellido_usuario_cliente` varchar(255) DEFAULT NULL,
  `telefono_usuario_cliente` varchar(255) DEFAULT NULL,
  `direccion_usuario_cliente` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `token_exp` varchar(255) DEFAULT NULL,
  `foto_perfil_usuario_cliente` varchar(255) DEFAULT NULL,
  `activo_usuario_cliente` int(11) DEFAULT NULL,
  `estado_usuario_cliente` int(11) DEFAULT NULL,
  `eliminado_usuario_cliente` int(11) DEFAULT NULL,
  `presentacion_inicial_popup_usuario_cliente` int(11) DEFAULT NULL,
  `anuncio_popup_usuario_cliente` int(11) DEFAULT NULL,
  `lastlogin_usuario_cliente` datetime DEFAULT NULL,
  `fk_id_tipo_usuario_usuario_cliente` int(11) DEFAULT NULL,
  `date_created_usuario_cliente` datetime DEFAULT NULL,
  `date_update_usuario_cliente` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios_clientes`
--

INSERT INTO `usuarios_clientes` (`id_usuario_cliente`, `cedula_usuario_cliente`, `nombre_usuario_cliente`, `apellido_usuario_cliente`, `telefono_usuario_cliente`, `direccion_usuario_cliente`, `email`, `password`, `token`, `token_exp`, `foto_perfil_usuario_cliente`, `activo_usuario_cliente`, `estado_usuario_cliente`, `eliminado_usuario_cliente`, `presentacion_inicial_popup_usuario_cliente`, `anuncio_popup_usuario_cliente`, `lastlogin_usuario_cliente`, `fk_id_tipo_usuario_usuario_cliente`, `date_created_usuario_cliente`, `date_update_usuario_cliente`) VALUES
(4, 2147483647, 'Andres', 'Restrepo', '4156123', 'reservada', 'arz.950203@gmail.com', '$2a$07$azybxcags23425sdg23sde0y.SmidIpyazsKYTKJ1H5Xi9e/F.uCa', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NzQ1NzkyMDgsImV4cCI6MTY3NDY2NTYwOCwiZGF0YSI6eyJpZCI6NCwiZW1haWwiOiJhcnouOTUwMjAzQGdtYWlsLmNvbSJ9fQ.0wrfdMcChnS9DbJNpuF7gToEiJeTd20ZXyeUf35JWQU', '1674665608', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-24 00:00:00', '2023-01-24 10:34:47'),
(5, 2147483647, 'Andres', 'Restrepo', '4156123', 'reservada', 'andres@gmail.com', '$2a$07$azybxcags23425sdg23sde0y.SmidIpyazsKYTKJ1H5Xi9e/F.uCa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-01-24 00:00:00', '2023-01-24 11:04:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `id_viaje` int(11) NOT NULL,
  `direccion_origen_viaje` varchar(255) DEFAULT NULL,
  `direccion_destino_viaje` varchar(255) DEFAULT NULL,
  `ciudad_viaje` varchar(255) DEFAULT NULL,
  `nombre_recorrido_viaje` varchar(255) DEFAULT NULL,
  `descripcion_mensaje_contactos_viaje` varchar(255) DEFAULT NULL,
  `tiempo_estimado_recorrido_viaje` int(11) DEFAULT NULL,
  `fecha_inicio_viaje` datetime DEFAULT NULL,
  `activo_viaje` int(11) DEFAULT NULL,
  `cancelado_viaje` int(11) DEFAULT NULL,
  `confirmacion_llegada_destino_viaje` int(11) DEFAULT NULL,
  `fk_id_usuario_cliente_viaje` int(11) DEFAULT NULL,
  `date_created_viaje` datetime DEFAULT NULL,
  `date_update_viaje` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `id_zona` int(11) NOT NULL,
  `latitud_zona` float DEFAULT NULL,
  `longitud_zona` float DEFAULT NULL,
  `radio_zona` float DEFAULT NULL,
  `patrocinada_zona` int(11) DEFAULT NULL,
  `date_created_zona` datetime DEFAULT NULL,
  `date_update_zona` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agentes_lideres_por_zona`
--
ALTER TABLE `agentes_lideres_por_zona`
  ADD PRIMARY KEY (`id_agente_lider_zona`),
  ADD KEY `fk_agentes_lideres_por_zona_usuarios_agentes_1` (`fk_id_agente_agente_lider_zona`),
  ADD KEY `fk_agentes_lideres_por_zona_zonas_1` (`fk_id_zona_agente_lider_zona`);

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `fk_alertas_usuarios_clientes_1` (`fk_id_usuario_cliente_alerta`),
  ADD KEY `fk_alertas_servicios_por_zona_1` (`fk_servicio_por_zona_alerta`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `fk_contactos_usuarios_clientes_1` (`fk_id_usuario_cliente_contacto`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `fk_documentos_usuarios_clientes_1` (`fk_id_usuario_cliente_documento`),
  ADD KEY `fk_documentos_usuarios_agentes_1` (`fk_id_usuario_agente_documento`);

--
-- Indices de la tabla `establecimientos`
--
ALTER TABLE `establecimientos`
  ADD PRIMARY KEY (`id_establecimiento`),
  ADD KEY `fk_establecimientos_zonas_1` (`fk_id_zona_establecimiento`);

--
-- Indices de la tabla `insignias`
--
ALTER TABLE `insignias`
  ADD PRIMARY KEY (`id_insignia`);

--
-- Indices de la tabla `insignias_ganadas`
--
ALTER TABLE `insignias_ganadas`
  ADD PRIMARY KEY (`id_insignia_ganada`),
  ADD KEY `fk_insignias_ganadas_usuarios_clientes_1` (`fk_id_usuario_cliente_insignia_ganada`),
  ADD KEY `fk_insignias_ganadas_insignias_por_zona_1` (`fk_id_insignia_por_zona_insignia_ganada`);

--
-- Indices de la tabla `insignias_por_zona`
--
ALTER TABLE `insignias_por_zona`
  ADD PRIMARY KEY (`id_insignia_por_zona`),
  ADD KEY `fk_insignias_por_zona_insignias_1` (`fk_id_insignia_insignia_por_zona`),
  ADD KEY `fk_insignias_por_zona_zonas_1` (`fk_id_zona_insignia_por_zona`);

--
-- Indices de la tabla `paradas`
--
ALTER TABLE `paradas`
  ADD PRIMARY KEY (`id_parada`),
  ADD KEY `fk_paradas_viajes_1` (`fk_id_viaje_parada`);

--
-- Indices de la tabla `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indices de la tabla `planes_comprados`
--
ALTER TABLE `planes_comprados`
  ADD PRIMARY KEY (`id_plan_comprado`),
  ADD KEY `fk_planes_comprados_usuarios_clientes_1` (`fk_id_usuario_cliente_plan_comprado`),
  ADD KEY `fk_planes_comprados_planes_1` (`fk_id_plan_plan_comprado`);

--
-- Indices de la tabla `posiciones_agentes`
--
ALTER TABLE `posiciones_agentes`
  ADD PRIMARY KEY (`id_posicion_agente`),
  ADD KEY `fk_posiciones_agentes_usuarios_agentes_1` (`fk_id_usuario_agente`);

--
-- Indices de la tabla `posiciones_clientes`
--
ALTER TABLE `posiciones_clientes`
  ADD PRIMARY KEY (`id_posicion_cliente`),
  ADD KEY `fk_posiciones_clientes_usuarios_clientes_1` (`fk_id_usuario_cliente_posicion_cliente`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `promociones_por_establecimiento`
--
ALTER TABLE `promociones_por_establecimiento`
  ADD PRIMARY KEY (`id_promocion_por_establecimiento`),
  ADD KEY `fk_promociones_por_establecimiento_establecimientos_1` (`fk_id_establecimiento_promocion_por_establecimiento`),
  ADD KEY `fk_promociones_por_establecimiento_promociones_1` (`fk_id_promocion_promocion_por_establecimiento`);

--
-- Indices de la tabla `puntos_ganados`
--
ALTER TABLE `puntos_ganados`
  ADD PRIMARY KEY (`id_punto_ganado`),
  ADD KEY `fk_puntos_ganados_usuarios_clientes_1` (`fk_id_usuario_cliente_punto_ganado`),
  ADD KEY `fk_puntos_ganados_servicios_por_zona_1` (`fk_id_servicio_por_zona_punto_ganado`);

--
-- Indices de la tabla `reacciones_agentes`
--
ALTER TABLE `reacciones_agentes`
  ADD PRIMARY KEY (`id_reaccion_agente`),
  ADD KEY `fk_reacciones_agentes_usuarios_agentes_1` (`fk_id_usuario_agente_reaccion_agente`),
  ADD KEY `fk_reacciones_agentes_alertas_1` (`fk_id_alerta_reaccion_agente`);

--
-- Indices de la tabla `reacciones_cliente_cliente`
--
ALTER TABLE `reacciones_cliente_cliente`
  ADD PRIMARY KEY (`id_reaccion_cliente_cliente`),
  ADD KEY `fk_reacciones_cliente_cliente_alertas_1` (`fk_id_alerta_reaccion_cliente_cliente`),
  ADD KEY `fk_reacciones_cliente_cliente_usuarios_clientes_1` (`fk_id_usuario_cliente_reaccion_cliente_cliente`);

--
-- Indices de la tabla `registros_fotograficos_viajes`
--
ALTER TABLE `registros_fotograficos_viajes`
  ADD PRIMARY KEY (`id_registro_fotografico_viaje`),
  ADD KEY `fk_registros_fotograficos_viajes_viajes_1` (`fk_id_viaje_registro_fotografico_viaje`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `servicios_por_zona`
--
ALTER TABLE `servicios_por_zona`
  ADD PRIMARY KEY (`id_servicos_por_zona`),
  ADD KEY `fk_servicios_por_zona_zonas_1` (`fk_id_zona_servicos_por_zona`),
  ADD KEY `fk_servicios_por_zona_servicios_1` (`fk_id_servicio_servicos_por_zona`);

--
-- Indices de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indices de la tabla `usuarios_agentes`
--
ALTER TABLE `usuarios_agentes`
  ADD PRIMARY KEY (`id_usuario_agente`),
  ADD KEY `fk_usuarios_agentes_tipos_usuarios_1` (`fk_id_tipo_usuario_usuario_agente`);

--
-- Indices de la tabla `usuarios_clientes`
--
ALTER TABLE `usuarios_clientes`
  ADD PRIMARY KEY (`id_usuario_cliente`),
  ADD KEY `fk_usuarios_clientes_tipos_usuarios_1` (`fk_id_tipo_usuario_usuario_cliente`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`id_viaje`),
  ADD KEY `fk_viajes_usuarios_clientes_1` (`fk_id_usuario_cliente_viaje`);

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`id_zona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agentes_lideres_por_zona`
--
ALTER TABLE `agentes_lideres_por_zona`
  MODIFY `id_agente_lider_zona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `establecimientos`
--
ALTER TABLE `establecimientos`
  MODIFY `id_establecimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insignias`
--
ALTER TABLE `insignias`
  MODIFY `id_insignia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insignias_ganadas`
--
ALTER TABLE `insignias_ganadas`
  MODIFY `id_insignia_ganada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `insignias_por_zona`
--
ALTER TABLE `insignias_por_zona`
  MODIFY `id_insignia_por_zona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paradas`
--
ALTER TABLE `paradas`
  MODIFY `id_parada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planes`
--
ALTER TABLE `planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `planes_comprados`
--
ALTER TABLE `planes_comprados`
  MODIFY `id_plan_comprado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posiciones_agentes`
--
ALTER TABLE `posiciones_agentes`
  MODIFY `id_posicion_agente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posiciones_clientes`
--
ALTER TABLE `posiciones_clientes`
  MODIFY `id_posicion_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promociones_por_establecimiento`
--
ALTER TABLE `promociones_por_establecimiento`
  MODIFY `id_promocion_por_establecimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `puntos_ganados`
--
ALTER TABLE `puntos_ganados`
  MODIFY `id_punto_ganado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reacciones_agentes`
--
ALTER TABLE `reacciones_agentes`
  MODIFY `id_reaccion_agente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reacciones_cliente_cliente`
--
ALTER TABLE `reacciones_cliente_cliente`
  MODIFY `id_reaccion_cliente_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros_fotograficos_viajes`
--
ALTER TABLE `registros_fotograficos_viajes`
  MODIFY `id_registro_fotografico_viaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios_por_zona`
--
ALTER TABLE `servicios_por_zona`
  MODIFY `id_servicos_por_zona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios_agentes`
--
ALTER TABLE `usuarios_agentes`
  MODIFY `id_usuario_agente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios_clientes`
--
ALTER TABLE `usuarios_clientes`
  MODIFY `id_usuario_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `id_viaje` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `agentes_lideres_por_zona`
--
ALTER TABLE `agentes_lideres_por_zona`
  ADD CONSTRAINT `fk_agentes_lideres_por_zona_usuarios_agentes_1` FOREIGN KEY (`fk_id_agente_agente_lider_zona`) REFERENCES `usuarios_agentes` (`id_usuario_agente`),
  ADD CONSTRAINT `fk_agentes_lideres_por_zona_zonas_1` FOREIGN KEY (`fk_id_zona_agente_lider_zona`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `fk_alertas_servicios_por_zona_1` FOREIGN KEY (`fk_servicio_por_zona_alerta`) REFERENCES `servicios_por_zona` (`id_servicos_por_zona`),
  ADD CONSTRAINT `fk_alertas_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_alerta`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `fk_contactos_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_contacto`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `fk_documentos_usuarios_agentes_1` FOREIGN KEY (`fk_id_usuario_agente_documento`) REFERENCES `usuarios_agentes` (`id_usuario_agente`),
  ADD CONSTRAINT `fk_documentos_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_documento`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `establecimientos`
--
ALTER TABLE `establecimientos`
  ADD CONSTRAINT `fk_establecimientos_zonas_1` FOREIGN KEY (`fk_id_zona_establecimiento`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `insignias_ganadas`
--
ALTER TABLE `insignias_ganadas`
  ADD CONSTRAINT `fk_insignias_ganadas_insignias_por_zona_1` FOREIGN KEY (`fk_id_insignia_por_zona_insignia_ganada`) REFERENCES `insignias_por_zona` (`id_insignia_por_zona`),
  ADD CONSTRAINT `fk_insignias_ganadas_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_insignia_ganada`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `insignias_por_zona`
--
ALTER TABLE `insignias_por_zona`
  ADD CONSTRAINT `fk_insignias_por_zona_insignias_1` FOREIGN KEY (`fk_id_insignia_insignia_por_zona`) REFERENCES `insignias` (`id_insignia`),
  ADD CONSTRAINT `fk_insignias_por_zona_zonas_1` FOREIGN KEY (`fk_id_zona_insignia_por_zona`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `paradas`
--
ALTER TABLE `paradas`
  ADD CONSTRAINT `fk_paradas_viajes_1` FOREIGN KEY (`fk_id_viaje_parada`) REFERENCES `viajes` (`id_viaje`);

--
-- Filtros para la tabla `planes_comprados`
--
ALTER TABLE `planes_comprados`
  ADD CONSTRAINT `fk_planes_comprados_planes_1` FOREIGN KEY (`fk_id_plan_plan_comprado`) REFERENCES `planes` (`id_plan`),
  ADD CONSTRAINT `fk_planes_comprados_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_plan_comprado`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `posiciones_agentes`
--
ALTER TABLE `posiciones_agentes`
  ADD CONSTRAINT `fk_posiciones_agentes_usuarios_agentes_1` FOREIGN KEY (`fk_id_usuario_agente`) REFERENCES `usuarios_agentes` (`id_usuario_agente`);

--
-- Filtros para la tabla `posiciones_clientes`
--
ALTER TABLE `posiciones_clientes`
  ADD CONSTRAINT `fk_posiciones_clientes_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_posicion_cliente`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `promociones_por_establecimiento`
--
ALTER TABLE `promociones_por_establecimiento`
  ADD CONSTRAINT `fk_promociones_por_establecimiento_establecimientos_1` FOREIGN KEY (`fk_id_establecimiento_promocion_por_establecimiento`) REFERENCES `establecimientos` (`id_establecimiento`),
  ADD CONSTRAINT `fk_promociones_por_establecimiento_promociones_1` FOREIGN KEY (`fk_id_promocion_promocion_por_establecimiento`) REFERENCES `promociones` (`id_promocion`);

--
-- Filtros para la tabla `puntos_ganados`
--
ALTER TABLE `puntos_ganados`
  ADD CONSTRAINT `fk_puntos_ganados_servicios_por_zona_1` FOREIGN KEY (`fk_id_servicio_por_zona_punto_ganado`) REFERENCES `servicios_por_zona` (`id_servicos_por_zona`),
  ADD CONSTRAINT `fk_puntos_ganados_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_punto_ganado`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `reacciones_agentes`
--
ALTER TABLE `reacciones_agentes`
  ADD CONSTRAINT `fk_reacciones_agentes_alertas_1` FOREIGN KEY (`fk_id_alerta_reaccion_agente`) REFERENCES `alertas` (`id_alerta`),
  ADD CONSTRAINT `fk_reacciones_agentes_usuarios_agentes_1` FOREIGN KEY (`fk_id_usuario_agente_reaccion_agente`) REFERENCES `usuarios_agentes` (`id_usuario_agente`);

--
-- Filtros para la tabla `reacciones_cliente_cliente`
--
ALTER TABLE `reacciones_cliente_cliente`
  ADD CONSTRAINT `fk_reacciones_cliente_cliente_alertas_1` FOREIGN KEY (`fk_id_alerta_reaccion_cliente_cliente`) REFERENCES `alertas` (`id_alerta`),
  ADD CONSTRAINT `fk_reacciones_cliente_cliente_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_reaccion_cliente_cliente`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);

--
-- Filtros para la tabla `registros_fotograficos_viajes`
--
ALTER TABLE `registros_fotograficos_viajes`
  ADD CONSTRAINT `fk_registros_fotograficos_viajes_viajes_1` FOREIGN KEY (`fk_id_viaje_registro_fotografico_viaje`) REFERENCES `viajes` (`id_viaje`);

--
-- Filtros para la tabla `servicios_por_zona`
--
ALTER TABLE `servicios_por_zona`
  ADD CONSTRAINT `fk_servicios_por_zona_servicios_1` FOREIGN KEY (`fk_id_servicio_servicos_por_zona`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `fk_servicios_por_zona_zonas_1` FOREIGN KEY (`fk_id_zona_servicos_por_zona`) REFERENCES `zonas` (`id_zona`);

--
-- Filtros para la tabla `usuarios_agentes`
--
ALTER TABLE `usuarios_agentes`
  ADD CONSTRAINT `fk_usuarios_agentes_tipos_usuarios_1` FOREIGN KEY (`fk_id_tipo_usuario_usuario_agente`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`);

--
-- Filtros para la tabla `usuarios_clientes`
--
ALTER TABLE `usuarios_clientes`
  ADD CONSTRAINT `fk_usuarios_clientes_tipos_usuarios_1` FOREIGN KEY (`fk_id_tipo_usuario_usuario_cliente`) REFERENCES `tipos_usuarios` (`id_tipo_usuario`);

--
-- Filtros para la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD CONSTRAINT `fk_viajes_usuarios_clientes_1` FOREIGN KEY (`fk_id_usuario_cliente_viaje`) REFERENCES `usuarios_clientes` (`id_usuario_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
