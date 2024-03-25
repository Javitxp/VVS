-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-03-2024 a las 19:23:47
-- Versión del servidor: 10.5.20-MariaDB
-- Versión de PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `id21862115_twitch`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Datos`
--

CREATE TABLE `Datos` (
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `views` int(11) NOT NULL,
  `duracion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `Datos`
--

INSERT INTO `Datos` (`title`, `user`, `views`, `duracion`, `fecha_creacion`) VALUES
('WORLDS 22 FINALS COUNTDOWN', 'Riot Games', 11615221, '9h25m12s', '2022-11-05T21:00:23Z'),
('T1 vs DRX | 2022 월드 챔피언십 | FINALS', 'Riot_esports_Korea', 7863019, '7h38m46s', '2022-11-05T22:50:16Z'),
('WBG vs T1 | Worlds 2023 FINALS', 'Riot Games', 7407955, '6h9m25s', '2023-11-19T05:56:16Z'),
('DK vs. EDG | Finals | Worlds 2021 | DWG KIA vs. Edward Gaming', 'Riot Games', 7289257, '7h48m23s', '2021-11-06T09:59:31Z'),
('DWG vs. SN | Finals | 2020 World Championship | DAMWON Gaming vs. Suning', 'Riot Games', 6067352, '7h36m51s', '2020-10-31T07:59:11Z'),
('2021 월드 챔피언십 4강 | T1 vs. DK', 'Riot_esports_Korea', 5976880, '5h58m52s', '2021-10-30T11:10:35Z'),
('2021 월드 챔피언십 결승 | EDG vs. DK', 'Riot_esports_Korea', 5613980, '6h49m17s', '2021-11-06T10:50:21Z'),
('Groups Day 1 | Worlds 2021', 'Riot Games', 5364307, '9h7m40s', '2021-10-11T09:47:19Z'),
('2022 MSI 결승전 | RNG vs T1', 'Riot_esports_Korea', 4873172, '5h35m1s', '2022-05-29T07:20:28Z'),
('FunPlus Phoenix vs. DWG KIA | Groups Day 4 | Worlds 2021', 'Riot Games', 4869790, '9h29m13s', '2021-10-15T10:00:07Z'),
('Groups Day 2 | Worlds 2021', 'Riot Games', 4861870, '10h1m42s', '2021-10-12T09:53:25Z'),
('DK vs. T1 | Semifinals Day 1 | Worlds 2021', 'Riot Games', 4618367, '6h26m44s', '2021-10-30T10:45:05Z'),
('Play-In Groups Day 1 | Worlds 2021', 'Riot Games', 4407466, '9h46m53s', '2021-10-05T09:49:24Z'),
('WORLDS 22 | PLAY-INS - DAY 1', 'Riot Games', 4398023, '11h13m15s', '2022-09-29T19:01:11Z'),
('2021 월드 챔피언십 Group Stage Day 1', 'Riot_esports_Korea', 4365468, '8h20m4s', '2021-10-11T10:30:20Z'),
('WORLDS 22 | GROUPS - DAY 1', 'Riot Games', 4360492, '7h33m29s', '2022-10-07T20:00:50Z'),
('Groups Day 3 | Worlds 2021', 'Riot Games', 4248322, '9h22m20s', '2021-10-13T10:01:10Z'),
('2022 LCK 스프링 결승전 | T1 vs GEN', 'Riot_esports_Korea', 4230309, '5h46m49s', '2022-04-02T07:10:09Z'),
('TES vs. FNC | Quarterfinals | 2020 World Championship | Top Esports vs. Fnatic', 'Riot Games', 4138789, '7h17m49s', '2020-10-17T08:22:15Z'),
('2022 LCK 서머 결승전 | GEN vs T1', 'Riot_esports_Korea', 4121245, '6h0m14s', '2022-08-28T04:00:21Z'),
('Groups Day 1 | 2020 World Championship', 'Riot Games', 4111136, '8h28m17s', '2020-10-03T06:38:50Z'),
('DWG vs. G2 | Semifinals | 2020 World Championship | DAMWON Gaming vs. G2 Esports', 'Riot Games', 4064701, '6h12m22s', '2020-10-24T08:30:28Z'),
('Worlds 2020 Groups: Day 2', 'Riot Games', 3835547, '8h38m2s', '2020-10-04T06:32:04Z'),
('WORLDS 22 | GROUPS - DAY 2', 'Riot Games', 3774252, '7h31m44s', '2022-10-08T20:01:08Z'),
('Countdown | Groups Day 6 | Worlds 2021', 'Riot Games', 3735833, '8h55m48s', '2021-10-17T09:44:10Z'),
('[2020 LoL 월드 챔피언십] 결승전 - DWG vs. SN', 'Riot_esports_Korea', 3637804, '5h54m34s', '2020-10-31T09:10:22Z'),
('Play-In Groups Day 2 | Worlds 2021', 'Riot Games', 3636985, '8h58m44s', '2021-10-06T09:51:11Z'),
('T1 vs. JDG | WORLDS 22 | SEMIFINALS - DAY 1', 'Riot Games', 3623365, '5h12m10s', '2022-10-29T20:00:16Z'),
('2021 월드 챔피언십 Group Stage Day 5', 'Riot_esports_Korea', 3568311, '6h44m11s', '2021-10-16T10:30:23Z'),
('2021 월드 챔피언십 Group Stage Day 4', 'Riot_esports_Korea', 3565107, '8h46m26s', '2021-10-15T10:31:01Z'),
('Worlds 2020 Groups: Day 5', 'Riot Games', 3528988, '9h3m34s', '2020-10-08T06:30:40Z'),
('2021 월드 챔피언십 Group Stage Day 7', 'Riot_esports_Korea', 3526183, '10h8m54s', '2021-10-18T10:30:25Z'),
('T1 vs. TL | Worlds Swiss Stage Day 1', 'Riot Games', 3480821, '9h54m24s', '2023-10-19T03:56:20Z'),
('2021 월드 챔피언십 Group Stage Day 6', 'Riot_esports_Korea', 3476880, '7h56m55s', '2021-10-17T10:30:23Z'),
('WORLDS 22 | GROUPS - DAY 5', 'Riot Games', 3461267, '6h38m58s', '2022-10-13T18:00:20Z'),
('WORLDS 22 | GROUPS - DAY 7', 'Riot Games', 3444780, '8h5m23s', '2022-10-15T18:00:34Z'),
('Warm Up Finals Preshow | LEC Summer (2020) |League of Legends', 'LEC', 3420937, '11h23m24s', '2020-09-06T08:00:39Z'),
('2021 월드 챔피언십 Group Stage Day 2', 'Riot_esports_Korea', 3392806, '9h6m53s', '2021-10-12T10:30:37Z'),
('WORLDS 22 | GROUPS - DAY 6', 'Riot Games', 3376905, '7h57m43s', '2022-10-14T18:00:29Z'),
('LCS Playoffs Grand Finals: FLY vs. TSM', 'LCS', 3318003, '9h5m30s', '2020-09-06T18:00:52Z');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Presentar`
--

CREATE TABLE `Presentar` (
  `ID` int(11) NOT NULL,
  `Datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Datos`)),
  `Tiempo` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `Presentar`
--

INSERT INTO `Presentar` (`ID`, `Datos`, `Tiempo`) VALUES
(0, '{\"game_id\":\"000\",\"game_name\":\"League of Legends\",\"user_name\":\"Riot Games\",\"total_videos\":\"25\",\"total_views\":\"117721417\",\"most_viewed_title\":\"WORLDS 22 FINALS COUNTDOWN\",\"most_viewed_views\":\"11615166\",\"most_viewed_duration\":\"9h25m12s\",\"most_viewed_created_at\":\"2022-11-05T21:00:23Z\"}', '2024-03-04 11:38:36'),
(21779, '{\"game_id\":\"21779\",\"game_name\":\"League of Legends\",\"user_name\":\"Riot Games\",\"total_videos\":\"25\",\"total_views\":\"117721791\",\"most_viewed_title\":\"WORLDS 22 FINALS COUNTDOWN\",\"most_viewed_views\":\"11615221\",\"most_viewed_duration\":\"9h25m12s\",\"most_viewed_created_at\":\"2022-11-05T21:00:23Z\"}', '2024-03-06 11:27:42'),
(29595, '{\"game_id\":\"29595\",\"game_name\":\"Dota 2\",\"user_name\":\"dota2ti_ru\",\"total_videos\":\"16\",\"total_views\":\"112591704\",\"most_viewed_title\":\"[RU] Team Secret vs Team Spirit | \\u041e\\u0441\\u043d\\u043e\\u0432\\u043d\\u043e\\u0439 \\u044d\\u0442\\u0430\\u043f | The International 10 | \\u0414\\u0435\\u043d\\u044c 6\",\"most_viewed_views\":\"17070711\",\"most_viewed_duration\":\"12h45m1s\",\"most_viewed_created_at\":\"2021-10-17T05:57:42Z\"}', '2024-03-06 11:27:42'),
(509658, '{\"game_id\":\"509658\",\"game_name\":\"Just Chatting\",\"user_name\":\"QTCinderella\",\"total_videos\":\"1\",\"total_views\":\"6546182\",\"most_viewed_title\":\"STREAMER AWARDS TODAY ON THIS STREAM, PRE-SHOW STARTING AT 12PM PST !TICKETS !MERCH !WHEN\",\"most_viewed_views\":\"6546182\",\"most_viewed_duration\":\"45h2m29s\",\"most_viewed_created_at\":\"2024-02-17T20:00:09Z\"}', '2024-03-06 11:27:42');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Presentar`
--
ALTER TABLE `Presentar`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Presentar`
--
ALTER TABLE `Presentar`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509659;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
