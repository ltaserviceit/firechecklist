-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2026 at 08:32 AM
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
-- Database: `maintenance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `dry_riser_system`
--

CREATE TABLE `dry_riser_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dry_riser_system`
--

INSERT INTO `dry_riser_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(6, 6, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', 'bye'),
(7, 6, 'EQUIPMENT', '2', 'LANDING VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(8, 6, 'EQUIPMENT', '3', 'CANVAS HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(9, 6, 'EQUIPMENT', '4', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(10, 6, 'EQUIPMENT', '5', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(11, 6, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(12, 6, 'EQUIPMENT', '2', 'LANDING VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', 'hello'),
(13, 6, 'EQUIPMENT', '3', 'CANVAS HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(14, 6, 'EQUIPMENT', '4', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(15, 6, 'EQUIPMENT', '5', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(16, 6, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(17, 6, 'EQUIPMENT', '2', 'LANDING VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(18, 6, 'EQUIPMENT', '3', 'CANVAS HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(19, 6, 'EQUIPMENT', '4', 'HOSE CRADLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(20, 6, 'EQUIPMENT', '5', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(21, 6, 'EQUIPMENT', '6', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fireman_intercom_system`
--

CREATE TABLE `fireman_intercom_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fireman_intercom_system`
--

INSERT INTO `fireman_intercom_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 7, 'PANEL PROFILE', '1', 'Main Panel', 'Semi Addressable', 'pony', '', '2', 'bawah katil', NULL, NULL, NULL, NULL),
(2, 7, 'CONTROL PANEL', '1', 'FIREMAN INTERCOM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(3, 7, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(4, 7, 'CONTROL PANEL', '3', 'FIREMAN INTERCOM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(5, 7, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(6, 7, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(7, 7, 'CONTROL PANEL', '6', 'FUSES, FACIAL LED DISPLAY, KEYPAD/BUTTON', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(8, 7, 'CONTROL PANEL', '7', 'INDICATION OF ALL FUNCTIONS AND STATUS IS CORRECTLY DISPLAYED ON THE LED SCREEN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(9, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(10, 7, 'CONTROL PANEL', '9', 'LED INDICATION AND INDICATES CORRECT ZONE LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(11, 7, 'DEVICES', '1-I', 'FIREMAN INTERCOM HANDSET', NULL, NULL, NULL, NULL, 'floor 1', '', 'Done', 'Normal', ''),
(13, 7, 'DEVICES', '2', 'CLEAR COMMUNICATION BETWEEN MASTER INTERCOM PANEL AND REMOTE INTERCOM STATION', NULL, NULL, NULL, NULL, '', '', 'Done', 'Faulty', ''),
(14, 7, 'DEVICES', '1-II', 'FIREMAN INTERCOM HANDSET', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_alarm_system_add`
--

CREATE TABLE `fire_alarm_system_add` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_alarm_system_add`
--

INSERT INTO `fire_alarm_system_add` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 4, 'PANEL PROFILE', '1', '', '', 'Apple', '', '2', 'Floor 2', NULL, NULL, NULL, NULL),
(2, 4, 'CONTROL PANEL', '1', 'MAIN FIRE ALARM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(3, 4, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(4, 4, 'CONTROL PANEL', '3', 'MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(5, 4, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(6, 4, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(7, 4, 'CONTROL PANEL', '6', 'AVR STABILISER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(8, 4, 'CONTROL PANEL', '7', 'FUSES, FACIAL LED DISPLAY, KEYPAD/BUTTON AND PANEL PROCESSOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(9, 4, 'CONTROL PANEL', '8', 'PRINTER AND PAPER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(10, 4, 'CONTROL PANEL', '9', 'INDICATION OF ALL FUNCTIONS AND STATUS IS CORRECTLY DISPLAYED ON THE LED SCREEN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(11, 4, 'CONTROL PANEL', '10', 'FIELD DEVICES, MODULES AND RELAYS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(12, 4, 'CONTROL PANEL', '11', 'AC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(13, 4, 'CONTROL PANEL', '12', 'DC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(14, 4, 'CONTROL PANEL', '13', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(15, 4, 'CONTROL PANEL', '14', 'MIMIC DIAGRAM DRAWING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(16, 4, 'CONTROL PANEL', '15', 'SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(17, 4, 'DEVICES', '1-I', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(18, 4, 'DEVICES', '1-II', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'Done', 'Normal', ''),
(19, 4, 'DEVICES', '1-III', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(20, 4, 'DEVICES', '1-IV', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(21, 4, 'DEVICES', '1-V', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', '', ''),
(22, 4, 'DEVICES', '1-VI', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(23, 4, 'DEVICES', '1-VII', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(24, 4, 'DEVICES', '1-VIII', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(25, 4, 'DEVICES', '1-IX', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(26, 4, 'DEVICES', '1-X', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(27, 4, 'DEVICES', '2-I', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(28, 4, 'DEVICES', '2-II', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'Done', 'Normal', ''),
(29, 4, 'DEVICES', '2-III', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(30, 4, 'DEVICES', '2-IV', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(31, 4, 'DEVICES', '2-V', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(32, 4, 'DEVICES', '2-VI', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(33, 4, 'DEVICES', '2-VII', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(34, 4, 'DEVICES', '2-VIII', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(35, 4, 'DEVICES', '2-IX', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(36, 4, 'DEVICES', '2-X', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(37, 4, 'DEVICES', '3-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(38, 4, 'DEVICES', '3-II', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'Done', 'Normal', ''),
(39, 4, 'DEVICES', '3-III', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(40, 4, 'DEVICES', '3-IV', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(41, 4, 'DEVICES', '3-V', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(42, 4, 'DEVICES', '3-VI', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(43, 4, 'DEVICES', '3-VII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(44, 4, 'DEVICES', '3-VIII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(45, 4, 'DEVICES', '3-IX', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(46, 4, 'DEVICES', '3-X', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(47, 4, 'DEVICES', '4-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(48, 4, 'DEVICES', '4-II', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(49, 4, 'DEVICES', '4-III', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(50, 4, 'DEVICES', '4-IV', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(51, 4, 'DEVICES', '4-V', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(52, 4, 'DEVICES', '4-VI', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(53, 4, 'DEVICES', '4-VII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(54, 4, 'DEVICES', '4-VIII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(55, 4, 'DEVICES', '4-IX', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(56, 4, 'DEVICES', '4-X', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(57, 4, 'DEVICES', '5-I', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'Done', 'Normal', ''),
(58, 4, 'DEVICES', '5-II', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(59, 4, 'DEVICES', '5-III', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(60, 4, 'DEVICES', '5-IV', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(61, 4, 'DEVICES', '5-V', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(62, 4, 'DEVICES', '5-VI', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(63, 4, 'DEVICES', '5-VII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(64, 4, 'DEVICES', '5-VIII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(65, 4, 'DEVICES', '5-IX', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(66, 4, 'DEVICES', '5-X', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(67, 4, 'DEVICES', '6-I', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(68, 4, 'DEVICES', '6-II', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(69, 4, 'DEVICES', '6-III', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(70, 4, 'DEVICES', '6-IV', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(71, 4, 'DEVICES', '6-V', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(72, 4, 'DEVICES', '6-VI', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(73, 4, 'DEVICES', '6-VII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(74, 4, 'DEVICES', '6-VIII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(75, 4, 'DEVICES', '6-IX', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(76, 4, 'DEVICES', '6-X', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(77, 4, 'DEVICES', '7-I', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(78, 4, 'DEVICES', '7-II', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(79, 4, 'DEVICES', '7-III', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(80, 4, 'DEVICES', '7-IV', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(81, 4, 'DEVICES', '7-V', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'Done', 'Normal', ''),
(82, 4, 'DEVICES', '7-VI', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(83, 4, 'DEVICES', '7-VII', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(84, 4, 'DEVICES', '7-VIII', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(85, 4, 'DEVICES', '7-IX', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(86, 4, 'DEVICES', '7-X', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(87, 4, 'DEVICES', '8-I', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(88, 4, 'DEVICES', '8-II', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(89, 4, 'DEVICES', '8-III', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(90, 4, 'DEVICES', '8-IV', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(91, 4, 'DEVICES', '8-V', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(92, 4, 'DEVICES', '8-VI', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(93, 4, 'DEVICES', '8-VII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(94, 4, 'DEVICES', '8-VIII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(95, 4, 'DEVICES', '8-IX', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(96, 4, 'DEVICES', '8-X', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, '', 'N/A', 'Not Applicable', ''),
(97, 4, 'SIGNAL TEST', 'I-A', 'FROM HOSE REEL PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(98, 4, 'SIGNAL TEST', 'I-B', 'FROM HOSE REEL PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(99, 4, 'SIGNAL TEST', 'I-C', 'FROM HOSE REEL PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(100, 4, 'SIGNAL TEST', 'I-D', 'FROM HOSE REEL PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(101, 4, 'SIGNAL TEST', 'I-E', 'FROM HOSE REEL PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(102, 4, 'SIGNAL TEST', 'I-F', 'FROM HOSE REEL PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(103, 4, 'SIGNAL TEST', 'II-A', 'FROM FIRE SPRINKLER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(104, 4, 'SIGNAL TEST', 'II-B', 'FROM FIRE SPRINKLER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(105, 4, 'SIGNAL TEST', 'II-C', 'FROM FIRE SPRINKLER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(106, 4, 'SIGNAL TEST', 'II-D', 'FROM FIRE SPRINKLER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(107, 4, 'SIGNAL TEST', 'II-E', 'FROM FIRE SPRINKLER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(108, 4, 'SIGNAL TEST', 'II-F', 'FROM FIRE SPRINKLER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(109, 4, 'SIGNAL TEST', 'II-G', 'FROM FIRE SPRINKLER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(110, 4, 'SIGNAL TEST', 'II-H', 'FROM FIRE SPRINKLER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(111, 4, 'SIGNAL TEST', 'III-A', 'FROM WET RISER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(112, 4, 'SIGNAL TEST', 'III-B', 'FROM WET RISER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(113, 4, 'SIGNAL TEST', 'III-C', 'FROM WET RISER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(114, 4, 'SIGNAL TEST', 'III-D', 'FROM WET RISER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(115, 4, 'SIGNAL TEST', 'III-E', 'FROM WET RISER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(116, 4, 'SIGNAL TEST', 'III-F', 'FROM WET RISER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(117, 4, 'SIGNAL TEST', 'III-G', 'FROM WET RISER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(118, 4, 'SIGNAL TEST', 'III-H', 'FROM WET RISER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(119, 4, 'SIGNAL TEST', 'IV-A', 'PRESSURISED HYDRANT PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(120, 4, 'SIGNAL TEST', 'IV-B', 'PRESSURISED HYDRANT PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(121, 4, 'SIGNAL TEST', 'IV-C', 'PRESSURISED HYDRANT PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(122, 4, 'SIGNAL TEST', 'IV-D', 'PRESSURISED HYDRANT PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(123, 4, 'SIGNAL TEST', 'IV-E', 'PRESSURISED HYDRANT PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(124, 4, 'SIGNAL TEST', 'IV-F', 'PRESSURISED HYDRANT PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(125, 4, 'SIGNAL TEST', 'IV-G', 'PRESSURISED HYDRANT PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(126, 4, 'SIGNAL TEST', 'IV-H', 'PRESSURISED HYDRANT PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(127, 4, 'SIGNAL TEST', 'V', 'FROM CO2 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(128, 4, 'SIGNAL TEST', 'VI', 'FROM FE-13 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(129, 4, 'SIGNAL TEST', 'VII', 'FROM FM200 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(130, 4, 'SIGNAL TEST', 'VIII', 'FROM INERT GAS FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(131, 4, 'SIGNAL TEST', 'IX', 'FROM WET CHEMICAL SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(132, 4, 'SIGNAL TEST', 'X', 'FROM P.A SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(133, 4, 'SIGNAL TEST', 'XI', 'FROM SMOKE SPILLED FAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(134, 4, 'SIGNAL TEST', 'XII', 'FROM SMOKE PRESSURISED SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(135, 4, 'SIGNAL TEST', 'XIII', 'FROM LIFT HOMING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(136, 7, 'PANEL PROFILE', '1', 'Main Panel', 'Addressable', 'GST', '', '2', 'katil', NULL, NULL, NULL, NULL),
(137, 7, 'PANEL PROFILE', '2', 'Sub Panel', 'Conventional', 'COOPER', '', '2', 'bawah', NULL, NULL, NULL, NULL),
(138, 7, 'CONTROL PANEL', '1', 'MAIN FIRE ALARM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(139, 7, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(140, 7, 'CONTROL PANEL', '3', 'MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(141, 7, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(142, 7, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(143, 7, 'CONTROL PANEL', '6', 'AVR STABILISER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(144, 7, 'CONTROL PANEL', '7', 'FUSES, FACIAL LED DISPLAY, KEYPAD/BUTTON AND PANEL PROCESSOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(145, 7, 'CONTROL PANEL', '8', 'PRINTER AND PAPER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(146, 7, 'CONTROL PANEL', '9', 'INDICATION OF ALL FUNCTIONS AND STATUS IS CORRECTLY DISPLAYED ON THE LED SCREEN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(147, 7, 'CONTROL PANEL', '10', 'FIELD DEVICES, MODULES AND RELAYS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(148, 7, 'CONTROL PANEL', '11', 'AC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(149, 7, 'CONTROL PANEL', '12', 'DC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(150, 7, 'CONTROL PANEL', '13', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(151, 7, 'CONTROL PANEL', '14', 'MIMIC DIAGRAM DRAWING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(152, 7, 'CONTROL PANEL', '15', 'SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(153, 7, 'DEVICES', '1-I', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, 'floor 1', '', 'Done', 'Normal', ''),
(154, 7, 'DEVICES', '1-II', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, 'floor 3', '', 'Done', 'Faulty', ''),
(155, 7, 'DEVICES', '2-I', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, 'floor 1', '2', 'Done', 'Faulty', ''),
(156, 7, 'DEVICES', '2-II', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, 'floor 2', '2', 'Done', 'Faulty', ''),
(157, 7, 'DEVICES', '3-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(158, 7, 'DEVICES', '4-I', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(159, 7, 'DEVICES', '5-I', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(160, 7, 'DEVICES', '6-I', 'ACTIVATE BEAM DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(161, 7, 'DEVICES', '7-I', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(162, 7, 'SIGNAL TEST', 'I-A', 'FROM HOSE REEL PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(163, 7, 'SIGNAL TEST', 'I-B', 'FROM HOSE REEL PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(164, 7, 'SIGNAL TEST', 'I-C', 'FROM HOSE REEL PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(165, 7, 'SIGNAL TEST', 'I-D', 'FROM HOSE REEL PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(166, 7, 'SIGNAL TEST', 'I-E', 'FROM HOSE REEL PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(167, 7, 'SIGNAL TEST', 'I-F', 'FROM HOSE REEL PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(168, 7, 'SIGNAL TEST', 'II-A', 'FROM FIRE SPRINKLER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(169, 7, 'SIGNAL TEST', 'II-B', 'FROM FIRE SPRINKLER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(170, 7, 'SIGNAL TEST', 'II-C', 'FROM FIRE SPRINKLER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(171, 7, 'SIGNAL TEST', 'II-D', 'FROM FIRE SPRINKLER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(172, 7, 'SIGNAL TEST', 'II-E', 'FROM FIRE SPRINKLER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(173, 7, 'SIGNAL TEST', 'II-F', 'FROM FIRE SPRINKLER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(174, 7, 'SIGNAL TEST', 'II-G', 'FROM FIRE SPRINKLER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(175, 7, 'SIGNAL TEST', 'II-H', 'FROM FIRE SPRINKLER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(176, 7, 'SIGNAL TEST', 'III-A', 'FROM WET RISER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(177, 7, 'SIGNAL TEST', 'III-B', 'FROM WET RISER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(178, 7, 'SIGNAL TEST', 'III-C', 'FROM WET RISER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(179, 7, 'SIGNAL TEST', 'III-D', 'FROM WET RISER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(180, 7, 'SIGNAL TEST', 'III-E', 'FROM WET RISER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(181, 7, 'SIGNAL TEST', 'III-F', 'FROM WET RISER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(182, 7, 'SIGNAL TEST', 'III-G', 'FROM WET RISER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(183, 7, 'SIGNAL TEST', 'III-H', 'FROM WET RISER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(184, 7, 'SIGNAL TEST', 'IV-A', 'PRESSURISED HYDRANT PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(185, 7, 'SIGNAL TEST', 'IV-B', 'PRESSURISED HYDRANT PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(186, 7, 'SIGNAL TEST', 'IV-C', 'PRESSURISED HYDRANT PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(187, 7, 'SIGNAL TEST', 'IV-D', 'PRESSURISED HYDRANT PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(188, 7, 'SIGNAL TEST', 'IV-E', 'PRESSURISED HYDRANT PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(189, 7, 'SIGNAL TEST', 'IV-F', 'PRESSURISED HYDRANT PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(190, 7, 'SIGNAL TEST', 'IV-G', 'PRESSURISED HYDRANT PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(191, 7, 'SIGNAL TEST', 'IV-H', 'PRESSURISED HYDRANT PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(192, 7, 'SIGNAL TEST', 'V', 'FROM CO2 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(193, 7, 'SIGNAL TEST', 'VI', 'FROM FE-13 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(194, 7, 'SIGNAL TEST', 'VII', 'FROM FM200 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(195, 7, 'SIGNAL TEST', 'VIII', 'FROM INERT GAS FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(196, 7, 'SIGNAL TEST', 'IX', 'FROM WET CHEMICAL SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(197, 7, 'SIGNAL TEST', 'X', 'FROM P.A SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(198, 7, 'SIGNAL TEST', 'XI', 'FROM SMOKE SPILLED FAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(199, 7, 'SIGNAL TEST', 'XII', 'FROM SMOKE PRESSURISED SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(200, 7, 'SIGNAL TEST', 'XIII', 'FROM LIFT HOMING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_alarm_system_con`
--

CREATE TABLE `fire_alarm_system_con` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_alarm_system_con`
--

INSERT INTO `fire_alarm_system_con` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(125, 4, 'PANEL PROFILE', '1', '', '', 'Samsung', '', '2', 'Floor 1', NULL, NULL, NULL, NULL),
(126, 4, 'CONTROL PANEL', '1', 'MAIN FIRE ALARM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(127, 4, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Faulty', ''),
(128, 4, 'CONTROL PANEL', '3', 'MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(129, 4, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(130, 4, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(131, 4, 'CONTROL PANEL', '6', 'AVR STABILISER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(132, 4, 'CONTROL PANEL', '7', 'TEST FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(133, 4, 'CONTROL PANEL', '8', 'ZONE CARD, CHARGER CARD, FAULT CARD, OTHER CARDS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(134, 4, 'CONTROL PANEL', '9', 'AC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(135, 4, 'CONTROL PANEL', '10', 'DC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(136, 4, 'CONTROL PANEL', '11', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(137, 4, 'CONTROL PANEL', '12', 'MIMIC DIAGRAM DRAWING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(138, 4, 'CONTROL PANEL', '13', 'MASTER ALARM BELL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(139, 4, 'CONTROL PANEL', '14', 'SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(140, 4, 'DEVICES', '1-I', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(141, 4, 'DEVICES', '1-II', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(142, 4, 'DEVICES', '1-III', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(143, 4, 'DEVICES', '1-IV', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(144, 4, 'DEVICES', '1-V', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '23', 'Done', 'Normal', ''),
(145, 4, 'DEVICES', '1-VI', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'Done', 'Normal', ''),
(146, 4, 'DEVICES', '1-VII', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(147, 4, 'DEVICES', '1-VIII', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(148, 4, 'DEVICES', '1-IX', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(150, 4, 'DEVICES', '2-I', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(151, 4, 'DEVICES', '2-II', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(152, 4, 'DEVICES', '2-III', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(153, 4, 'DEVICES', '2-IV', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(154, 4, 'DEVICES', '2-V', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(155, 4, 'DEVICES', '2-VI', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(156, 4, 'DEVICES', '2-VII', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(157, 4, 'DEVICES', '2-VIII', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(158, 4, 'DEVICES', '2-IX', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(159, 4, 'DEVICES', '2-X', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(160, 4, 'DEVICES', '3-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(161, 4, 'DEVICES', '3-II', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(162, 4, 'DEVICES', '3-III', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(163, 4, 'DEVICES', '3-IV', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(164, 4, 'DEVICES', '3-V', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(165, 4, 'DEVICES', '3-VI', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(166, 4, 'DEVICES', '3-VII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(167, 4, 'DEVICES', '3-VIII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(168, 4, 'DEVICES', '3-IX', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(169, 4, 'DEVICES', '3-X', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(170, 4, 'DEVICES', '4-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(171, 4, 'DEVICES', '4-II', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(172, 4, 'DEVICES', '4-III', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(173, 4, 'DEVICES', '4-IV', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(174, 4, 'DEVICES', '4-V', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(175, 4, 'DEVICES', '4-VI', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(176, 4, 'DEVICES', '4-VII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(177, 4, 'DEVICES', '4-VIII', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(178, 4, 'DEVICES', '4-IX', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(179, 4, 'DEVICES', '4-X', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(180, 4, 'DEVICES', '5-I', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(181, 4, 'DEVICES', '5-II', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(182, 4, 'DEVICES', '5-III', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(183, 4, 'DEVICES', '5-IV', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(184, 4, 'DEVICES', '5-V', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(185, 4, 'DEVICES', '5-VI', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(186, 4, 'DEVICES', '5-VII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(187, 4, 'DEVICES', '5-VIII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(188, 4, 'DEVICES', '5-IX', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(189, 4, 'DEVICES', '5-X', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(190, 4, 'DEVICES', '6-I', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(191, 4, 'DEVICES', '6-II', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(192, 4, 'DEVICES', '6-III', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(193, 4, 'DEVICES', '6-IV', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(194, 4, 'DEVICES', '6-V', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(195, 4, 'DEVICES', '6-VI', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(196, 4, 'DEVICES', '6-VII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(197, 4, 'DEVICES', '6-VIII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(198, 4, 'DEVICES', '6-IX', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(199, 4, 'DEVICES', '6-X', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(200, 4, 'DEVICES', '7-I', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(201, 4, 'DEVICES', '7-II', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(202, 4, 'DEVICES', '7-III', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(203, 4, 'DEVICES', '7-IV', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(204, 4, 'DEVICES', '7-V', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(205, 4, 'DEVICES', '7-VI', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(206, 4, 'DEVICES', '7-VII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(207, 4, 'DEVICES', '7-VIII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(208, 4, 'DEVICES', '7-IX', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(209, 4, 'DEVICES', '7-X', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Normal', ''),
(210, 4, 'SIGNAL TEST', 'I-A', 'FROM HOSE REEL PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(211, 4, 'SIGNAL TEST', 'I-B', 'FROM HOSE REEL PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(212, 4, 'SIGNAL TEST', 'I-C', 'FROM HOSE REEL PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(213, 4, 'SIGNAL TEST', 'I-D', 'FROM HOSE REEL PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(214, 4, 'SIGNAL TEST', 'I-E', 'FROM HOSE REEL PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(215, 4, 'SIGNAL TEST', 'I-F', 'FROM HOSE REEL PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(216, 4, 'SIGNAL TEST', 'II-A', 'FROM FIRE SPRINKLER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(217, 4, 'SIGNAL TEST', 'II-B', 'FROM FIRE SPRINKLER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(218, 4, 'SIGNAL TEST', 'II-C', 'FROM FIRE SPRINKLER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(219, 4, 'SIGNAL TEST', 'II-D', 'FROM FIRE SPRINKLER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(220, 4, 'SIGNAL TEST', 'II-E', 'FROM FIRE SPRINKLER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(221, 4, 'SIGNAL TEST', 'II-F', 'FROM FIRE SPRINKLER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(222, 4, 'SIGNAL TEST', 'II-G', 'FROM FIRE SPRINKLER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(223, 4, 'SIGNAL TEST', 'II-H', 'FROM FIRE SPRINKLER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(224, 4, 'SIGNAL TEST', 'III-A', 'FROM WET RISER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(225, 4, 'SIGNAL TEST', 'III-B', 'FROM WET RISER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(226, 4, 'SIGNAL TEST', 'III-C', 'FROM WET RISER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(227, 4, 'SIGNAL TEST', 'III-D', 'FROM WET RISER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(228, 4, 'SIGNAL TEST', 'III-E', 'FROM WET RISER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(229, 4, 'SIGNAL TEST', 'III-F', 'FROM WET RISER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(230, 4, 'SIGNAL TEST', 'III-G', 'FROM WET RISER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(231, 4, 'SIGNAL TEST', 'III-H', 'FROM WET RISER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(232, 4, 'SIGNAL TEST', 'IV-A', 'PRESSURISED HYDRANT PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(233, 4, 'SIGNAL TEST', 'IV-B', 'PRESSURISED HYDRANT PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(234, 4, 'SIGNAL TEST', 'IV-C', 'PRESSURISED HYDRANT PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(235, 4, 'SIGNAL TEST', 'IV-D', 'PRESSURISED HYDRANT PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(236, 4, 'SIGNAL TEST', 'IV-E', 'PRESSURISED HYDRANT PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(237, 4, 'SIGNAL TEST', 'IV-F', 'PRESSURISED HYDRANT PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(238, 4, 'SIGNAL TEST', 'IV-G', 'PRESSURISED HYDRANT PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(239, 4, 'SIGNAL TEST', 'IV-H', 'PRESSURISED HYDRANT PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(240, 4, 'SIGNAL TEST', 'V', 'FROM CO2 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(241, 4, 'SIGNAL TEST', 'VI', 'FROM FE-13 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(242, 4, 'SIGNAL TEST', 'VII', 'FROM FM200 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(243, 4, 'SIGNAL TEST', 'VIII', 'FROM INERT GAS FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(244, 4, 'SIGNAL TEST', 'IX', 'FROM WET CHEMICAL SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(245, 4, 'SIGNAL TEST', 'X', 'FROM P.A SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(246, 4, 'SIGNAL TEST', 'XI', 'FROM SMOKE SPILLED FAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(247, 4, 'SIGNAL TEST', 'XII', 'FROM SMOKE PRESSURISED SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(248, 4, 'SIGNAL TEST', 'XIII', 'FROM LIFT HOMING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Normal', ''),
(249, 6, 'PANEL PROFILE', '1', 'Main Panel', 'Conventional', 'Sony', '', '4', 'Toilet', NULL, NULL, NULL, NULL),
(250, 6, 'CONTROL PANEL', '1', 'MAIN FIRE ALARM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(251, 6, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Faulty', ''),
(252, 6, 'CONTROL PANEL', '3', 'MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(253, 6, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(254, 6, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(255, 6, 'CONTROL PANEL', '6', 'AVR STABILISER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(256, 6, 'CONTROL PANEL', '7', 'TEST FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(257, 6, 'CONTROL PANEL', '8', 'ZONE CARD, CHARGER CARD, FAULT CARD, OTHER CARDS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(258, 6, 'CONTROL PANEL', '9', 'AC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(259, 6, 'CONTROL PANEL', '10', 'DC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(260, 6, 'CONTROL PANEL', '11', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(261, 6, 'CONTROL PANEL', '12', 'MIMIC DIAGRAM DRAWING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(262, 6, 'CONTROL PANEL', '13', 'MASTER ALARM BELL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(263, 6, 'CONTROL PANEL', '14', 'SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(264, 6, 'DEVICES', '1-I', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, 'Floor 1', '1', 'Done', 'Normal', ''),
(284, 6, 'DEVICES', '3-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, 'floor 2', '', 'Done', 'Not Applicable', ''),
(304, 6, 'DEVICES', '5-I', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(305, 6, 'DEVICES', '5-II', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(306, 6, 'DEVICES', '5-III', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(307, 6, 'DEVICES', '5-IV', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(308, 6, 'DEVICES', '5-V', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(309, 6, 'DEVICES', '5-VI', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(310, 6, 'DEVICES', '5-VII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(311, 6, 'DEVICES', '5-VIII', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(312, 6, 'DEVICES', '5-IX', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(313, 6, 'DEVICES', '5-X', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(314, 6, 'DEVICES', '6-I', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(315, 6, 'DEVICES', '6-II', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(316, 6, 'DEVICES', '6-III', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(317, 6, 'DEVICES', '6-IV', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(318, 6, 'DEVICES', '6-V', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(319, 6, 'DEVICES', '6-VI', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(320, 6, 'DEVICES', '6-VII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(321, 6, 'DEVICES', '6-VIII', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(322, 6, 'DEVICES', '6-IX', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(323, 6, 'DEVICES', '6-X', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(324, 6, 'DEVICES', '7-I', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(325, 6, 'DEVICES', '7-II', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(326, 6, 'DEVICES', '7-III', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(327, 6, 'DEVICES', '7-IV', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(328, 6, 'DEVICES', '7-V', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(329, 6, 'DEVICES', '7-VI', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(330, 6, 'DEVICES', '7-VII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(331, 6, 'DEVICES', '7-VIII', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(332, 6, 'DEVICES', '7-IX', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(333, 6, 'DEVICES', '7-X', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(334, 6, 'SIGNAL TEST', 'I-A', 'FROM HOSE REEL PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(335, 6, 'SIGNAL TEST', 'I-B', 'FROM HOSE REEL PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(336, 6, 'SIGNAL TEST', 'I-C', 'FROM HOSE REEL PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Faulty', ''),
(337, 6, 'SIGNAL TEST', 'I-D', 'FROM HOSE REEL PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(338, 6, 'SIGNAL TEST', 'I-E', 'FROM HOSE REEL PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(339, 6, 'SIGNAL TEST', 'I-F', 'FROM HOSE REEL PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(340, 6, 'SIGNAL TEST', 'II-A', 'FROM FIRE SPRINKLER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(341, 6, 'SIGNAL TEST', 'II-B', 'FROM FIRE SPRINKLER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(342, 6, 'SIGNAL TEST', 'II-C', 'FROM FIRE SPRINKLER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(343, 6, 'SIGNAL TEST', 'II-D', 'FROM FIRE SPRINKLER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(344, 6, 'SIGNAL TEST', 'II-E', 'FROM FIRE SPRINKLER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(345, 6, 'SIGNAL TEST', 'II-F', 'FROM FIRE SPRINKLER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(346, 6, 'SIGNAL TEST', 'II-G', 'FROM FIRE SPRINKLER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(347, 6, 'SIGNAL TEST', 'II-H', 'FROM FIRE SPRINKLER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(348, 6, 'SIGNAL TEST', 'III-A', 'FROM WET RISER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(349, 6, 'SIGNAL TEST', 'III-B', 'FROM WET RISER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(350, 6, 'SIGNAL TEST', 'III-C', 'FROM WET RISER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(351, 6, 'SIGNAL TEST', 'III-D', 'FROM WET RISER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(352, 6, 'SIGNAL TEST', 'III-E', 'FROM WET RISER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(353, 6, 'SIGNAL TEST', 'III-F', 'FROM WET RISER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(354, 6, 'SIGNAL TEST', 'III-G', 'FROM WET RISER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(355, 6, 'SIGNAL TEST', 'III-H', 'FROM WET RISER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(356, 6, 'SIGNAL TEST', 'IV-A', 'PRESSURISED HYDRANT PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(357, 6, 'SIGNAL TEST', 'IV-B', 'PRESSURISED HYDRANT PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(358, 6, 'SIGNAL TEST', 'IV-C', 'PRESSURISED HYDRANT PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(359, 6, 'SIGNAL TEST', 'IV-D', 'PRESSURISED HYDRANT PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(360, 6, 'SIGNAL TEST', 'IV-E', 'PRESSURISED HYDRANT PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(361, 6, 'SIGNAL TEST', 'IV-F', 'PRESSURISED HYDRANT PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(362, 6, 'SIGNAL TEST', 'IV-G', 'PRESSURISED HYDRANT PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(363, 6, 'SIGNAL TEST', 'IV-H', 'PRESSURISED HYDRANT PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(364, 6, 'SIGNAL TEST', 'V', 'FROM CO2 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(365, 6, 'SIGNAL TEST', 'VI', 'FROM FE-13 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(366, 6, 'SIGNAL TEST', 'VII', 'FROM FM200 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(367, 6, 'SIGNAL TEST', 'VIII', 'FROM INERT GAS FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(368, 6, 'SIGNAL TEST', 'IX', 'FROM WET CHEMICAL SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(369, 6, 'SIGNAL TEST', 'X', 'FROM P.A SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(370, 6, 'SIGNAL TEST', 'XI', 'FROM SMOKE SPILLED FAN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(371, 6, 'SIGNAL TEST', 'XII', 'FROM SMOKE PRESSURISED SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(372, 6, 'SIGNAL TEST', 'XIII', 'FROM LIFT HOMING', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(373, 7, 'PANEL PROFILE', '1', 'Sub Panel', 'Conventional', 'ASENWARE', '', '2', 'atas', NULL, NULL, NULL, NULL),
(374, 7, 'PANEL PROFILE', '2', 'Main Panel', 'Conventional', 'ASENWARE', '', '2', 'd', NULL, NULL, NULL, NULL),
(375, 7, 'CONTROL PANEL', '1', 'MAIN FIRE ALARM PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(376, 7, 'CONTROL PANEL', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(377, 7, 'CONTROL PANEL', '3', 'MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(378, 7, 'CONTROL PANEL', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(379, 7, 'CONTROL PANEL', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(380, 7, 'CONTROL PANEL', '6', 'AVR STABILISER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(381, 7, 'CONTROL PANEL', '7', 'TEST FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(382, 7, 'CONTROL PANEL', '8', 'ZONE CARD, CHARGER CARD, FAULT CARD, OTHER CARDS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(383, 7, 'CONTROL PANEL', '9', 'AC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(384, 7, 'CONTROL PANEL', '10', 'DC SURGE ARRESTOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(385, 7, 'CONTROL PANEL', '11', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(386, 7, 'CONTROL PANEL', '12', 'MIMIC DIAGRAM DRAWING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(387, 7, 'CONTROL PANEL', '13', 'MASTER ALARM BELL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(388, 7, 'CONTROL PANEL', '14', 'SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(389, 7, 'DEVICES', '1-I', 'ACTIVATE ALARM BELL', NULL, NULL, NULL, NULL, 'floor 1', '2', 'Done', 'Faulty', ''),
(390, 7, 'DEVICES', '2-I', 'ACTIVATE MANUAL CALL POINT', NULL, NULL, NULL, NULL, 'floor 2', '3', 'Done', 'Normal', ''),
(391, 7, 'DEVICES', '3-I', 'ACTIVATE SMOKE DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(392, 7, 'DEVICES', '4-I', 'ACTIVATE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(393, 7, 'DEVICES', '5-I', 'ACTIVATE SMOKE HEAT DETECTOR', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(394, 7, 'DEVICES', '6-I', 'ACTIVATE FLOW SWITCH', NULL, NULL, NULL, NULL, '', '', 'N/A', 'Not Applicable', ''),
(395, 7, 'SIGNAL TEST', 'I-A', 'FROM HOSE REEL PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(396, 7, 'SIGNAL TEST', 'I-B', 'FROM HOSE REEL PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(397, 7, 'SIGNAL TEST', 'I-C', 'FROM HOSE REEL PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(398, 7, 'SIGNAL TEST', 'I-D', 'FROM HOSE REEL PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(399, 7, 'SIGNAL TEST', 'I-E', 'FROM HOSE REEL PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(400, 7, 'SIGNAL TEST', 'I-F', 'FROM HOSE REEL PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(401, 7, 'SIGNAL TEST', 'II-A', 'FROM FIRE SPRINKLER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(402, 7, 'SIGNAL TEST', 'II-B', 'FROM FIRE SPRINKLER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(403, 7, 'SIGNAL TEST', 'II-C', 'FROM FIRE SPRINKLER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(404, 7, 'SIGNAL TEST', 'II-D', 'FROM FIRE SPRINKLER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(405, 7, 'SIGNAL TEST', 'II-E', 'FROM FIRE SPRINKLER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(406, 7, 'SIGNAL TEST', 'II-F', 'FROM FIRE SPRINKLER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(407, 7, 'SIGNAL TEST', 'II-G', 'FROM FIRE SPRINKLER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(408, 7, 'SIGNAL TEST', 'II-H', 'FROM FIRE SPRINKLER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(409, 7, 'SIGNAL TEST', 'III-A', 'FROM WET RISER PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(410, 7, 'SIGNAL TEST', 'III-B', 'FROM WET RISER PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(411, 7, 'SIGNAL TEST', 'III-C', 'FROM WET RISER PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(412, 7, 'SIGNAL TEST', 'III-D', 'FROM WET RISER PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(413, 7, 'SIGNAL TEST', 'III-E', 'FROM WET RISER PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(414, 7, 'SIGNAL TEST', 'III-F', 'FROM WET RISER PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(415, 7, 'SIGNAL TEST', 'III-G', 'FROM WET RISER PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(416, 7, 'SIGNAL TEST', 'III-H', 'FROM WET RISER PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(417, 7, 'SIGNAL TEST', 'IV-A', 'PRESSURISED HYDRANT PUMP - AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(418, 7, 'SIGNAL TEST', 'IV-B', 'PRESSURISED HYDRANT PUMP - JOCKEY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(419, 7, 'SIGNAL TEST', 'IV-C', 'PRESSURISED HYDRANT PUMP - JOCKEY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(420, 7, 'SIGNAL TEST', 'IV-D', 'PRESSURISED HYDRANT PUMP - DUTY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(421, 7, 'SIGNAL TEST', 'IV-E', 'PRESSURISED HYDRANT PUMP - DUTY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(422, 7, 'SIGNAL TEST', 'IV-F', 'PRESSURISED HYDRANT PUMP - STANDBY RUN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(423, 7, 'SIGNAL TEST', 'IV-G', 'PRESSURISED HYDRANT PUMP - STANDBY TRIP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(424, 7, 'SIGNAL TEST', 'IV-H', 'PRESSURISED HYDRANT PUMP - WATER TANK LOW', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(425, 7, 'SIGNAL TEST', 'V', 'FROM CO2 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(426, 7, 'SIGNAL TEST', 'VI', 'FROM FE-13 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(427, 7, 'SIGNAL TEST', 'VII', 'FROM FM200 FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(428, 7, 'SIGNAL TEST', 'VIII', 'FROM INERT GAS FIRE SUPPRESSION SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(429, 7, 'SIGNAL TEST', 'IX', 'FROM WET CHEMICAL SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(430, 7, 'SIGNAL TEST', 'X', 'FROM P.A SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(431, 7, 'SIGNAL TEST', 'XI', 'FROM SMOKE SPILLED FAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(432, 7, 'SIGNAL TEST', 'XII', 'FROM SMOKE PRESSURISED SYSTEM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(433, 7, 'SIGNAL TEST', 'XIII', 'FROM LIFT HOMING', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_hose_reel_system`
--

CREATE TABLE `fire_hose_reel_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(100) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_hose_reel_system`
--

INSERT INTO `fire_hose_reel_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 6, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'There', NULL, ''),
(2, 6, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(3, 6, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(4, 6, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(5, 6, 'PUMP INFO', '5', 'PRESSURE REDUCING VALVE SIZE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(6, 6, 'PUMP INFO', '6', 'PRESSURE REDUCING VALVE PRESSURE (BEFORE)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(7, 6, 'PUMP INFO', '7', 'PRESSURE REDUCING VALVE PRESSURE (AFTER)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(8, 6, 'PUMP INFO', '8', 'TAPPING', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(9, 6, 'PUMP INFO', '8 a)', 'CUT IN (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 90.5 | STANDBY: 20.5', NULL, ''),
(10, 6, 'PUMP INFO', '8 b)', 'CUT OUT (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 21.5 | STANDBY: 21.5', NULL, ''),
(11, 6, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(12, 6, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(13, 6, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY (12V 40AH)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(14, 6, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(15, 6, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(16, 6, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(17, 6, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(18, 6, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(19, 6, 'EQUIPMENT', '1', 'THE HOSE REEL DRUM, HOSE, CLIPS AND STOP VALVE IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(20, 6, 'EQUIPMENT', '2', 'HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(21, 6, 'EQUIPMENT', '3', 'HOSE REEL CABINET', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(22, 6, 'EQUIPMENT', '4', 'HOSE REEL DRUM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(23, 6, 'EQUIPMENT', '5', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(24, 6, 'EQUIPMENT', '6', 'NOZZLE BOX', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(25, 6, 'EQUIPMENT', '7', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(26, 6, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(27, 6, 'PUMP TEST - MANUAL', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(28, 6, 'PUMP TEST - MANUAL', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(29, 6, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(30, 6, 'PUMP TEST - AUTO', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(31, 6, 'PUMP TEST - AUTO', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(32, 6, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'there', NULL, ''),
(33, 6, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(34, 6, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, 'yes', NULL, ''),
(35, 6, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '90', NULL, ''),
(36, 6, 'PUMP INFO', '5', 'PRESSURE REDUCING VALVE SIZE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(37, 6, 'PUMP INFO', '6', 'PRESSURE REDUCING VALVE PRESSURE (BEFORE)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(38, 6, 'PUMP INFO', '7', 'PRESSURE REDUCING VALVE PRESSURE (AFTER)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(39, 6, 'PUMP INFO', '8', 'TAPPING', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(40, 6, 'PUMP INFO', '8 a)', 'CUT IN (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 202 | STANDBY: 22', NULL, ' [IMG: uploads/1784099764_celestialbeing.webp]'),
(41, 6, 'PUMP INFO', '8 b)', 'CUT OUT (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 12 | STANDBY: 42', NULL, ''),
(42, 6, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ' [IMG: uploads/1784099764_city.jpg]'),
(43, 6, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(44, 6, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY (12V 40AH)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(45, 6, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(46, 6, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(47, 6, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(48, 6, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(49, 6, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(50, 6, 'EQUIPMENT', '1', 'THE HOSE REEL DRUM, HOSE, CLIPS AND STOP VALVE IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(51, 6, 'EQUIPMENT', '2', 'HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(52, 6, 'EQUIPMENT', '3', 'HOSE REEL CABINET', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(53, 6, 'EQUIPMENT', '4', 'HOSE REEL DRUM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(54, 6, 'EQUIPMENT', '5', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(55, 6, 'EQUIPMENT', '6', 'NOZZLE BOX', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(56, 6, 'EQUIPMENT', '7', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(57, 6, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(58, 6, 'PUMP TEST - MANUAL', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(59, 6, 'PUMP TEST - MANUAL', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(60, 6, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(61, 6, 'PUMP TEST - AUTO', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(62, 6, 'PUMP TEST - AUTO', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(63, 7, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'ad', NULL, ''),
(64, 7, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, 'ad', NULL, ''),
(65, 7, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, 'afd', NULL, ''),
(66, 7, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(67, 7, 'PUMP INFO', '5', 'PRESSURE REDUCING VALVE SIZE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(68, 7, 'PUMP INFO', '6', 'PRESSURE REDUCING VALVE PRESSURE (BEFORE)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(69, 7, 'PUMP INFO', '7', 'PRESSURE REDUCING VALVE PRESSURE (AFTER)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(70, 7, 'PUMP INFO', '8', 'TAPPING', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(71, 7, 'PUMP INFO', '8 a)', 'CUT IN (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 123 | STANDBY: 23', NULL, ''),
(72, 7, 'PUMP INFO', '8 b)', 'CUT OUT (PSI)', NULL, NULL, NULL, NULL, NULL, NULL, 'DUTY: 23 | STANDBY: 23', NULL, ''),
(73, 7, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(74, 7, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(75, 7, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY (12V 40AH)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(76, 7, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(77, 7, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(78, 7, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(79, 7, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(80, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(81, 7, 'EQUIPMENT', '1', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(82, 7, 'EQUIPMENT', '2', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(83, 7, 'EQUIPMENT', '3', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(84, 7, 'EQUIPMENT', '4', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(85, 7, 'EQUIPMENT', '5', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(86, 7, 'EQUIPMENT', '6', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(87, 7, 'EQUIPMENT', '7', '', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(88, 7, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(89, 7, 'PUMP TEST - MANUAL', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(90, 7, 'PUMP TEST - MANUAL', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(91, 7, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(92, 7, 'PUMP TEST - AUTO', 'II', 'HOSE REEL DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(93, 7, 'PUMP TEST - AUTO', 'III', 'HOSE REEL STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_sprinkler_system`
--

CREATE TABLE `fire_sprinkler_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_sprinkler_system`
--

INSERT INTO `fire_sprinkler_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(89, 7, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'air', NULL, ''),
(90, 7, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, 'tanah', NULL, ''),
(91, 7, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(92, 7, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '21', NULL, ''),
(93, 7, 'PUMP PRESSURE', '1-JOCKEY', 'CUT IN (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '31', NULL, ''),
(94, 7, 'PUMP PRESSURE', '1-DUTY', 'CUT IN (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '43', NULL, ''),
(95, 7, 'PUMP PRESSURE', '1-STANDBY', 'CUT IN (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '12', NULL, ''),
(96, 7, 'PUMP PRESSURE', '2-JOCKEY', 'CUT OUT (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '32', NULL, ''),
(97, 7, 'PUMP PRESSURE', '2-DUTY', 'CUT OUT (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '21', NULL, ''),
(98, 7, 'PUMP PRESSURE', '2-STANDBY', 'CUT OUT (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(99, 7, 'PUMP PRESSURE', '3-JOCKEY', 'SUCTION VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '32', NULL, ''),
(100, 7, 'PUMP PRESSURE', '3-DUTY', 'SUCTION VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '123', NULL, ''),
(101, 7, 'PUMP PRESSURE', '3-STANDBY', 'SUCTION VALVE (STATUS) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '223', NULL, ''),
(102, 7, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(103, 7, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(104, 7, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(105, 7, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(106, 7, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(107, 7, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(108, 7, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(109, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(110, 7, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(111, 7, 'EQUIPMENT', '2', 'CONDITION OF SPRINKLER HEAD', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(112, 7, 'EQUIPMENT', '3', 'ALARM VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(113, 7, 'EQUIPMENT', '4', 'ALARM GONG', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(114, 7, 'EQUIPMENT', '5', 'PRESSURE GAUGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(115, 7, 'EQUIPMENT', '6', 'BUTTERFLY VALVE', NULL, NULL, NULL, NULL, NULL, NULL, '', '', ''),
(116, 7, 'EQUIPMENT', '6-I', '1', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(117, 7, 'EQUIPMENT', '6-II', '2', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(118, 7, 'EQUIPMENT', '7', 'FLOW SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, '', '', ''),
(119, 7, 'EQUIPMENT', '7-I', '1', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(120, 7, 'EQUIPMENT', '8', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(121, 7, 'EQUIPMENT', '9', 'ACTIVATE LIVE SPRINKLER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(122, 7, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(123, 7, 'PUMP TEST - MANUAL', 'II', 'SPRINKLER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(124, 7, 'PUMP TEST - MANUAL', 'III', 'SPRINKLER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(125, 7, 'PUMP TEST - MANUAL', 'IV', 'SPRINKLER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(126, 7, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(127, 7, 'PUMP TEST - AUTO', 'II', 'SPRINKLER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(128, 7, 'PUMP TEST - AUTO', 'III', 'SPRINKLER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(129, 7, 'PUMP TEST - AUTO', 'IV', 'SPRINKLER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(143, 7, 'PUMP PRESSURE', '4-JOCKEY', 'DISCHARGE VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, ''),
(144, 7, 'PUMP PRESSURE', '4-DUTY', 'DISCHARGE VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_suppression_system_1`
--

CREATE TABLE `fire_suppression_system_1` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_suppression_system_1`
--

INSERT INTO `fire_suppression_system_1` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 4, 'SYSTEM INFO', '1', 'AGENT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(2, 4, 'SYSTEM INFO', '2', 'LOCATION/ROOM', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(3, 4, 'SYSTEM INFO', '3', 'TYPE OF PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(4, 4, 'SYSTEM INFO', '4', 'BRAND', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(5, 4, 'SYSTEM INFO', '5', 'QUANTITY OF CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(6, 4, 'SYSTEM INFO', '6', 'CYLINDER CAPACITY (KG)', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(7, 4, 'SYSTEM INFO', '7', 'QUANTITY OF PILOT CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(8, 4, 'SYSTEM INFO', '8', 'MANUAL KEY SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(9, 4, 'SYSTEM INFO', '9', 'MANUAL PULL BOX', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(10, 4, 'SYSTEM INFO', '10', 'MANUAL ABORT SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(11, 4, 'SYSTEM INFO', '11', 'QUANTITY OF NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(12, 4, 'SYSTEM INFO', '12', 'QUANTITY OF HEAT DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(13, 4, 'SYSTEM INFO', '13', 'QUANTITY OF SMOKE DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(14, 4, 'MAINTENANCE', '1', 'FIRE SUPPRESSION CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(15, 4, 'MAINTENANCE', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(16, 4, 'MAINTENANCE', '3', 'FIRE SUPPRESSION CONTROL PANEL INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(17, 4, 'MAINTENANCE', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(18, 4, 'MAINTENANCE', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(19, 4, 'MAINTENANCE', '6', 'FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(20, 4, 'MAINTENANCE', '7', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(21, 4, 'MAINTENANCE', '8', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(22, 4, 'MAINTENANCE', '9', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(23, 4, 'MAINTENANCE', '10', 'EACH ZONES OPERATES CORRECTLY BETWEEN ALARM, FAULT AND ISOLATE INDICATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(24, 4, 'MAINTENANCE', '11', 'ALARM BELL INTERMITTENT, GENERAL ALARM, TRIPPING SIGNALS, AND INDICATIONS SOUND PERFECTLY ACCORDING TO SEQUENCE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(25, 4, 'MAINTENANCE', '12', 'HEAT & SMOKE DETECTOR OPERATIONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(26, 4, 'MAINTENANCE', '13', 'PILOT CYLINDER, 24V DC SIGNAL, TESTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(27, 4, 'MAINTENANCE', '14', 'FIRE SUPPRESSION DISCHARGE WITHIN 30 SECONDS FROM \"DOUBLE KNOCKING\" DETECTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(28, 4, 'MAINTENANCE', '15', 'TRIPPING OF MECHANICAL FAN AND ASBESTOS CURTAIN ON ALARM MODE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(29, 4, 'MAINTENANCE', '16', 'TRIPPING AND CYLINDER BRACKET RIGIDLY MOUNTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(30, 4, 'MAINTENANCE', '17', 'ALL TUBING CONNECTIONS TO CO2 SYSTEM CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(31, 4, 'MAINTENANCE', '18', 'PRESSURE GAUGE IN CYLINDER IN OPERATE RANGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(32, 4, 'MAINTENANCE', '19', 'TESTING OF FIRE CURTAIN RELEASE SOLENOID', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(33, 4, 'MAINTENANCE', '20', 'FLASHING LIGHT INDICATOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(34, 4, 'MAINTENANCE', '21', 'EVACUATE SIGN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(35, 4, 'MAINTENANCE', '22', 'VERIFICATION FIRE SUPPRESSION GAS CYLINDER OPERATION LIFESPAN (NOT EXCEEDING TEN YEARS).', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(36, 4, 'MAINTENANCE', '23', 'CONDITION OF MANIFOLD, CONNECTING HOSE AND DISCHARGE HOSE (FREE OF CRACKING, KINKING AND FOLDING)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(37, 7, 'PANEL PROFILE', '1', 'CO2 CYLINDER', '23', '', '', '1', 'bilik meeting', NULL, NULL, NULL, NULL),
(38, 7, 'SYSTEM INFO', '1', 'AGENT', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(39, 7, 'SYSTEM INFO', '2', 'LOCATION/ROOM', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(40, 7, 'SYSTEM INFO', '3', 'TYPE OF PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(41, 7, 'SYSTEM INFO', '4', 'NO. OF ZONE', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(42, 7, 'SYSTEM INFO', '5', 'BRAND', NULL, NULL, NULL, NULL, NULL, NULL, 'pony', NULL, ''),
(43, 7, 'SYSTEM INFO', '6', 'QUANTITY OF CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(44, 7, 'SYSTEM INFO', '7', 'CYLINDER CAPACITY (KG)', NULL, NULL, NULL, NULL, NULL, NULL, '12', NULL, ''),
(45, 7, 'SYSTEM INFO', '8', 'QUANTITY OF PILOT CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(46, 7, 'SYSTEM INFO', '9', 'MANUAL KEY SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, '21', NULL, ''),
(47, 7, 'SYSTEM INFO', '10', 'MANUAL PULL BOX', NULL, NULL, NULL, NULL, NULL, NULL, '21', NULL, ''),
(48, 7, 'SYSTEM INFO', '11', 'MANUAL ABORT SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(49, 7, 'SYSTEM INFO', '12', 'QUANTITY OF NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(50, 7, 'SYSTEM INFO', '13', 'QUANTITY OF HEAT DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(51, 7, 'SYSTEM INFO', '14', 'QUANTITY OF SMOKE DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(52, 7, 'MAINTENANCE', '1', 'FIRE SUPPRESSION CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(53, 7, 'MAINTENANCE', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(54, 7, 'MAINTENANCE', '3', 'FIRE SUPPRESSION CONTROL PANEL INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(55, 7, 'MAINTENANCE', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(56, 7, 'MAINTENANCE', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(57, 7, 'MAINTENANCE', '6', 'FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(58, 7, 'MAINTENANCE', '7', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(59, 7, 'MAINTENANCE', '8', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(60, 7, 'MAINTENANCE', '9', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(61, 7, 'MAINTENANCE', '10', 'EACH ZONES OPERATES CORRECTLY BETWEEN ALARM, FAULT AND ISOLATE INDICATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(62, 7, 'MAINTENANCE', '11', 'ALARM BELL INTERMITTENT, GENERAL ALARM, TRIPPING SIGNALS, AND INDICATIONS SOUND PERFECTLY ACCORDING TO SEQUENCE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(63, 7, 'MAINTENANCE', '12', 'HEAT & SMOKE DETECTOR OPERATIONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(64, 7, 'MAINTENANCE', '13', 'PILOT CYLINDER, 24V DC SIGNAL, TESTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(65, 7, 'MAINTENANCE', '14', 'FIRE SUPPRESSION DISCHARGE WITHIN 30 SECONDS FROM \"DOUBLE KNOCKING\" DETECTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(66, 7, 'MAINTENANCE', '15', 'TRIPPING OF MECHANICAL FAN AND ASBESTOS CURTAIN ON ALARM MODE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(67, 7, 'MAINTENANCE', '16', 'TRIPPING AND CYLINDER BRACKET RIGIDLY MOUNTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(68, 7, 'MAINTENANCE', '17', 'ALL TUBING CONNECTIONS TO CO2 SYSTEM CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(69, 7, 'MAINTENANCE', '18', 'PRESSURE GAUGE IN CYLINDER IN OPERATE RANGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(70, 7, 'MAINTENANCE', '19', 'TESTING OF FIRE CURTAIN RELEASE SOLENOID', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(71, 7, 'MAINTENANCE', '20', 'FLASHING LIGHT INDICATOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(72, 7, 'MAINTENANCE', '21', 'EVACUATE SIGN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(73, 7, 'MAINTENANCE', '22', 'VERIFICATION FIRE SUPPRESSION GAS CYLINDER OPERATION LIFESPAN (NOT EXCEEDING TEN YEARS).', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(74, 7, 'MAINTENANCE', '23', 'CONDITION OF MANIFOLD, CONNECTING HOSE AND DISCHARGE HOSE (FREE OF CRACKING, KINKING AND FOLDING)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_suppression_system_2`
--

CREATE TABLE `fire_suppression_system_2` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_suppression_system_2`
--

INSERT INTO `fire_suppression_system_2` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 6, 'PANEL PROFILE', '1', 'FM200 PANEL', 'sony', '', '', '2', 'bilik tido 1', NULL, NULL, NULL, NULL),
(2, 6, 'SYSTEM INFO', '1', 'AGENT', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(3, 6, 'SYSTEM INFO', '2', 'LOCATION/ROOM', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(4, 6, 'SYSTEM INFO', '3', 'TYPE OF PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(5, 6, 'SYSTEM INFO', '4', 'BRAND', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(6, 6, 'SYSTEM INFO', '5', 'QUANTITY OF CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(7, 6, 'SYSTEM INFO', '6', 'CYLINDER CAPACITY (KG)', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(8, 6, 'SYSTEM INFO', '7', 'QUANTITY OF PILOT CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(9, 6, 'SYSTEM INFO', '8', 'MANUAL KEY SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(10, 6, 'SYSTEM INFO', '9', 'MANUAL PULL BOX', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(11, 6, 'SYSTEM INFO', '10', 'MANUAL ABORT SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(12, 6, 'SYSTEM INFO', '11', 'QUANTITY OF NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(13, 6, 'SYSTEM INFO', '12', 'QUANTITY OF HEAT DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(14, 6, 'SYSTEM INFO', '13', 'QUANTITY OF SMOKE DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', NULL, ''),
(15, 6, 'MAINTENANCE', '1', 'FIRE SUPPRESSION CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(16, 6, 'MAINTENANCE', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(17, 6, 'MAINTENANCE', '3', 'FIRE SUPPRESSION CONTROL PANEL INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(18, 6, 'MAINTENANCE', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(19, 6, 'MAINTENANCE', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(20, 6, 'MAINTENANCE', '6', 'FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(21, 6, 'MAINTENANCE', '7', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(22, 6, 'MAINTENANCE', '8', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(23, 6, 'MAINTENANCE', '9', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(24, 6, 'MAINTENANCE', '10', 'EACH ZONES OPERATES CORRECTLY BETWEEN ALARM, FAULT AND ISOLATE INDICATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(25, 6, 'MAINTENANCE', '11', 'ALARM BELL INTERMITTENT, GENERAL ALARM, TRIPPING SIGNALS, AND INDICATIONS SOUND PERFECTLY ACCORDING TO SEQUENCE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(26, 6, 'MAINTENANCE', '12', 'HEAT & SMOKE DETECTOR OPERATIONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(27, 6, 'MAINTENANCE', '13', 'PILOT CYLINDER, 24V DC SIGNAL, TESTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(28, 6, 'MAINTENANCE', '14', 'FIRE SUPPRESSION DISCHARGE WITHIN 30 SECONDS FROM \"DOUBLE KNOCKING\" DETECTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(29, 6, 'MAINTENANCE', '15', 'TRIPPING OF MECHANICAL FAN AND ASBESTOS CURTAIN ON ALARM MODE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(30, 6, 'MAINTENANCE', '16', 'TRIPPING AND CYLINDER BRACKET RIGIDLY MOUNTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(31, 6, 'MAINTENANCE', '17', 'ALL TUBING CONNECTIONS TO CO2 SYSTEM CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(32, 6, 'MAINTENANCE', '18', 'PRESSURE GAUGE IN CYLINDER IN OPERATE RANGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(33, 6, 'MAINTENANCE', '19', 'TESTING OF FIRE CURTAIN RELEASE SOLENOID', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(34, 6, 'MAINTENANCE', '20', 'FLASHING LIGHT INDICATOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(35, 6, 'MAINTENANCE', '21', 'EVACUATE SIGN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(36, 6, 'MAINTENANCE', '22', 'VERIFICATION FIRE SUPPRESSION GAS CYLINDER OPERATION LIFESPAN (NOT EXCEEDING TEN YEARS).', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(37, 6, 'MAINTENANCE', '23', 'CONDITION OF MANIFOLD, CONNECTING HOSE AND DISCHARGE HOSE (FREE OF CRACKING, KINKING AND FOLDING)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(38, 6, 'PANEL PROFILE', '1', 'FM200 PANEL', 'samsung', '', '', '3', 'bilik tido', NULL, NULL, NULL, NULL),
(39, 6, 'SYSTEM INFO', '1', 'AGENT', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(40, 6, 'SYSTEM INFO', '2', 'LOCATION/ROOM', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(41, 6, 'SYSTEM INFO', '3', 'TYPE OF PANEL', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(42, 6, 'SYSTEM INFO', '4', 'BRAND', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(43, 6, 'SYSTEM INFO', '5', 'QUANTITY OF CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(44, 6, 'SYSTEM INFO', '6', 'CYLINDER CAPACITY (KG)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(45, 6, 'SYSTEM INFO', '7', 'QUANTITY OF PILOT CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(46, 6, 'SYSTEM INFO', '8', 'MANUAL KEY SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(47, 6, 'SYSTEM INFO', '9', 'MANUAL PULL BOX', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(48, 6, 'SYSTEM INFO', '10', 'MANUAL ABORT SWITCH', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(49, 6, 'SYSTEM INFO', '11', 'QUANTITY OF NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(50, 6, 'SYSTEM INFO', '12', 'QUANTITY OF HEAT DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(51, 6, 'SYSTEM INFO', '13', 'QUANTITY OF SMOKE DETECTORS', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(52, 6, 'MAINTENANCE', '1', 'FIRE SUPPRESSION CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(53, 6, 'MAINTENANCE', '2', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(54, 6, 'MAINTENANCE', '3', 'FIRE SUPPRESSION CONTROL PANEL INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(55, 6, 'MAINTENANCE', '4', 'DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(56, 6, 'MAINTENANCE', '5', 'POWER SUPPLY UNIT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(57, 6, 'MAINTENANCE', '6', 'FUSES, LED LIGHT BULB, SWITCHES & BUZZER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(58, 6, 'MAINTENANCE', '7', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(59, 6, 'MAINTENANCE', '8', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(60, 6, 'MAINTENANCE', '9', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(61, 6, 'MAINTENANCE', '10', 'EACH ZONES OPERATES CORRECTLY BETWEEN ALARM, FAULT AND ISOLATE INDICATION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(62, 6, 'MAINTENANCE', '11', 'ALARM BELL INTERMITTENT, GENERAL ALARM, TRIPPING SIGNALS, AND INDICATIONS SOUND PERFECTLY ACCORDING TO SEQUENCE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(63, 6, 'MAINTENANCE', '12', 'HEAT & SMOKE DETECTOR OPERATIONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(64, 6, 'MAINTENANCE', '13', 'PILOT CYLINDER, 24V DC SIGNAL, TESTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(65, 6, 'MAINTENANCE', '14', 'FIRE SUPPRESSION DISCHARGE WITHIN 30 SECONDS FROM \"DOUBLE KNOCKING\" DETECTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(66, 6, 'MAINTENANCE', '15', 'TRIPPING OF MECHANICAL FAN AND ASBESTOS CURTAIN ON ALARM MODE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(67, 6, 'MAINTENANCE', '16', 'TRIPPING AND CYLINDER BRACKET RIGIDLY MOUNTED', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(68, 6, 'MAINTENANCE', '17', 'ALL TUBING CONNECTIONS TO CO2 SYSTEM CYLINDER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(69, 6, 'MAINTENANCE', '18', 'PRESSURE GAUGE IN CYLINDER IN OPERATE RANGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(70, 6, 'MAINTENANCE', '19', 'TESTING OF FIRE CURTAIN RELEASE SOLENOID', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(71, 6, 'MAINTENANCE', '20', 'FLASHING LIGHT INDICATOR', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(72, 6, 'MAINTENANCE', '21', 'EVACUATE SIGN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(73, 6, 'MAINTENANCE', '22', 'VERIFICATION FIRE SUPPRESSION GAS CYLINDER OPERATION LIFESPAN (NOT EXCEEDING TEN YEARS).', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(74, 6, 'MAINTENANCE', '23', 'CONDITION OF MANIFOLD, CONNECTING HOSE AND DISCHARGE HOSE (FREE OF CRACKING, KINKING AND FOLDING)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `fire_suppression_system_3`
--

CREATE TABLE `fire_suppression_system_3` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fire_suppression_system_4`
--

CREATE TABLE `fire_suppression_system_4` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pressurised_hydrant_system`
--

CREATE TABLE `pressurised_hydrant_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pressurised_hydrant_system`
--

INSERT INTO `pressurised_hydrant_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(1, 7, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'ada', NULL, ''),
(2, 7, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(3, 7, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(4, 7, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(5, 7, 'PUMP PRESSURE', '1', 'CUT IN (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '323', NULL, ''),
(8, 7, 'PUMP PRESSURE', '2', 'CUT OUT (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '43', NULL, ''),
(11, 7, 'PUMP PRESSURE', '3', 'SUCTION VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '43', NULL, ''),
(14, 7, 'PUMP PRESSURE', '4', 'DISCHARGE VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '4', NULL, ''),
(17, 7, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(18, 7, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(19, 7, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY (12V 40AH)', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(20, 7, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(21, 7, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(22, 7, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(23, 7, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(24, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(25, 7, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(26, 7, 'EQUIPMENT', '2', 'HYDRANT', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(27, 7, 'EQUIPMENT', '3', 'HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(28, 7, 'EQUIPMENT', '4', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(29, 7, 'EQUIPMENT', '5', 'PRESSURE GAUGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(30, 7, 'EQUIPMENT', '6', 'BUTTERFLY VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(31, 7, 'EQUIPMENT', '7', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(32, 7, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(33, 7, 'PUMP TEST - MANUAL', 'II', 'PRESSURISED HYDRANT DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(34, 7, 'PUMP TEST - MANUAL', 'III', 'PRESSURISED HYDRANT STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(35, 7, 'PUMP TEST - MANUAL', 'IV', 'PRESSURISED HYDRANT JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(36, 7, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(37, 7, 'PUMP TEST - AUTO', 'II', 'PRESSURISED HYDRANT DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(38, 7, 'PUMP TEST - AUTO', 'III', 'PRESSURISED HYDRANT STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(39, 7, 'PUMP TEST - AUTO', 'IV', 'PRESSURISED HYDRANT JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `building_name` varchar(255) NOT NULL,
  `tech_id` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tech_sign_name` varchar(255) DEFAULT NULL,
  `client_sign_name` varchar(255) DEFAULT NULL,
  `tech_signature` longtext DEFAULT NULL,
  `client_signature` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `company_name`, `building_name`, `tech_id`, `status`, `created_at`, `tech_sign_name`, `client_sign_name`, `tech_signature`, `client_signature`) VALUES
(4, 'hq ', 'test', '', 'Completed', '2026-05-06 03:13:43', NULL, NULL, NULL, NULL),
(6, 'lta', 'hq', '', 'Completed', '2026-05-08 03:19:42', 'VER', 'BERUANG', 'data:image/png;base64,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', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAbwAAACgCAYAAAB309mLAAAXgklEQVR4Xu2dB5QEWVlGP4wo5oBxVYKgYkbFiGDEiEhQUZKIgIKuCxhRkGBAggkEUcRVQRDMigHJKmYJyqJijpjFnPDc3feaR1kdp3umqt6959Tpnp6Z7qoO9fUfvv9dLSIiIh1wtQ6OUURERMETEZE+MMITEZEuUPBERKQLFDwREekCBU9ERLpAwRMRkS5Q8EREpAsUPBER6QIFT0REukDBExGRLlDwRESkCxQ8ERHpAgVPRES6QMETEZEuUPBERKQLFDwREekCBU9ERLpAwRMRkS5Q8EREpAsUPBER6QIFT0REukDBExGRLlDwRESkCxQ8ERHpAgVPRES6QMETEZEuUPBERKQLFDwREekCBU9ERLpAwRMRkS5Q8EREpAsUPBER6QIFT0REukDBExGRLlDwRESkCxQ8ERHpAgVPRES6QMETEZEuUPBERKQLFDwREekCBU9ERLpAwRMRkS5Q8EREpAsUPBER6QIFT0REukDBExGRLlDwpFfeJcm1kzw3ySt6fRJEekLBk95A6C5LcpfmwP88ye8128uTPCvJy1Z/ISKzR8GTXhgTur9Nco0kV1/d8ipemeTGSZ63ukVEZo2CJ0tnTOgem+ThSa4oP79jkus22+2TXDPJPyf5OEVPZBkoeLJU3ifJPZPcqTnAodCt47WSPDHJrRQ9keWg4MkSuVGSX0xW7+9dha5F0RNZGAqeLA1Sks9IckmS30ly8z2FrkXRE1kQCp4siTctYvfeSX48yScd4eAUPZGFoODJknh6ko8s6cyPSPLvRzo4RU9kASh4shSelOQ2JY2J2P3ZkQ9sKHo81tNWvxWRyaPgyRJ4VJK7F18dYvfCEx1UK3r/m+Stk/z16rciMmkUPJk7D0xy33IQiN0zT3xAiB5NMNdJ8j3FsyciM0DBkzlzaZJHlAO4dZKnnNPBvFUZQfYGSW6S5Nnn9LgicgYUPJkrt0tyedn5uyV5zDkfCClUUql/kOQGSf7tnB9fRPZEwZM5ctsk31d2/CuSfM0FHASfnV9I8oFJHpbk3hewDyKyBwqezA3Siaxu8Bplesq9LvAArpfkt8rn6AOS/PoF7ouIbEHBk7nxnUk+u9gPrj+Bnb9/kvsV4XuvJP8zgX0SkREUPJkTrFzwk2WH3/UMI8OOCV2biB3R3n2SPHT1GxGZFAqezInfKGPDsCE8eEI7Th2P6S40rtDAQiOLiEwMBU/mAs0pD0rym8mVS/9MjUcnuWuxKGBVkMNgMV7sHm9YNtYqfO0kf1d+/7pJ/rRE1SJ7oeDJHGAR15eUHf34iY704iSNN4+mmouwSUyFj03yZkn+qxGturVCtu42UsTbYMrNa65+EtkRBU/mwA8muUWSxyW584R3+FOS/FBpXGHs2N9MeF+PAZH2Dcvl+5ftrPx3kleUwd+cn94kyeuUrtzKVKN8mTgKnkyd6rn7x9KV+VcT32EsE29TolCi0aXwvsmVGwLHJTaMMRCrl5d5plxnY9h2vT52G9ffIcn7JfnQJDdOrlzqqeU/kvxhkjsmef7qVpE9UPBkyvDNHvsBdZx7JHnklHe2cK0kL0pyjQnuM1ES6Ua2dy71sFobq5Aq5BgQnbdI8ubFbsHfDqGW9mvN9is7DtNmP96j1DrZeCz2qYX9ek6SZ5WN55RUpsjBKHgyZWjxx1jOQGgGQ88FfIL4BUnLIRaI9qn4mCRvWUQEcaqCxtb+zPU3Xv3X/hwqbqDAySRQ8GSqfFAZ3QUfXNr+5wQrrn9CkheXNOB/HmnnES/8iCx0y4ruRGH7gHWChpLXK92P2+AY9hG3ys1KTY/0pxGcTAIFT6bKM5LcdMZzKqlB/XZpXvnGJF904BNNipG6FpHcR480hiBeRF/UtUgDsiYgotZGeixl9J6r/3h1WCj3V0vKkHrptZP8Zdlv2OUc8W6NuN2obEPaFCXWDWp8pijlXNnlzSxy3nxlkgck+aMyweRY0dF5Q2TDCZ7PGWL19B13gAYOGl5o8SeV+0ar3ySvLDM7iXh/t4gbIsVG7Y3aHJ2NYyBuNSWJyNXIjUiPCTYfVYSPVCwCNtb+f93SlYnZnsiVv3v91W9fBc0of5LkseU5UODkwlHwZGrwnqQ1nbrPZyZ5wtR2cE9YyeHLikWBcWhjVgUEA2FD4NgQrRbEg+5PhAjfGhHbNnicf0ry3QNxG+P7k3xa+YKBgf5ry+0scIvNgu5JhA2BI2Icwv3yGPVxfql0aopMCgVPpgYnXk7ARCNvP7WdOwCM1L9coiIiPCI94JJaHFEg49KGkdQ2EJnfT/KystXrXCKORIK7UAX5H5J8Q5Kv3mL+xh5Sm1aqwCGUIpNHwZOp8dQkn5rksmY187lCqvCdStMNC9TS2o9QkXLc1jBClIuQtEJWxY0N79qhkCIlYvv8YuhHHMfOBf9a0qdV2LgkjbqrmIpMirE3uchFQbqMpgsguiPKmwN0Mr5daf0n3UgtjVoXtbhNIBwIIMJG7QxRYTwZw6cRtWNAupRUZE1Lcsm+DT/7TIchyqReiv/xZ0unpY0lshiGb3qRi4QZlN9WGigQkanxtsVXh2maNCTX6VA8BggLfrqhEXwfECr2qwobl9QN27FcgLhxG59/ptgwqot0JgKMFeRDknxO8RKKLAYFT6ZEtSLcKcnjL3jHiIoQtipw/Lyu+5HUHw0iNJVcMqjH1YhpCFaCGsUhUpjC97Ev4L/DBoDdgE7WOvZrDOwRLK1EBEkNjlQxx0JzCqvGM64NAeQ6j895AXH/i9U9iCwABU+mAqlA0nn4yjgZIyLnAXMvETWEo16yOsNY48a/FCP5C4pgsTJC9Z61URT1NYzdVfiInH4+yfeWVn1M6Xz2GDb9I8UOQAqxtQEgaNT/1m2MLhuDeZNtQwlbrfcRQZImpn54eZI7lCiOyTDsF12ZX17ug7SsyKJQ8GQq0ClIxyApts86wU5xkn/3gbBxuW5SCSd9vGOIW71EBDCAM+WEqKwFGwDL27QzJ4msOB6EBEGpsHgtwkJq8S6l9kdKEVjnbZOgVRBfPHhElt9VhI1u0L9f/cWrw/Fj+GaCDWKNb4+UJ0LM/ZACxU6A7YFjRIBFFoWCJ1OhrmZ+8yQ/esadOjRqq8JGTYuoiG5G7AM0b2AEJ81XIRoj5YdAtDMqsQRgqyCKQ4yGkVnd+L9N8Pg0syC8Y9uYn28TpIiJ6BBenme8fTTKsALFlxaxZpFdRA9TucjiUPBkChBpEJ1gViZNuA/U/BA0RGRbrQ0BqcKGl4wIbDjYmfvCH8dG80YrkqRbEQrEro0MWbqGehwCh5DtImgILWlPUqFEWTwW8FwcImibwH7wrSWSY1kfBP5LknxdkpeWx+Yxje5k0Sh4MgUekuQ+5aR8zy07tMvwZOp/NWojRYiXjAhyzLtGFEcNrYoc9oIWojgaT3icbWnGll0iNDxwLG6L564K67E/k8zhZLQXtUEe74dLYw1Ch+AidkSvRneyeI794RI5BASANe+YOvLc1a1XwYl62/BkhOWJRdjGorYhRIKIG7VCbAVtVyXRG00m7W1jEKHxuHjmWmGrP68b4zXk20sdr3LMzyTCRnqWLwlEc9RJAdsHx0/tj65ManpGd7J4jvnhEjkEhOynS8RBnQ12GZ78U+X/8I3R/LEJTOx3LrU4UpZjw47XgQAiYAxrZhXzK5oI7RhcvaRY6/zMY30mid5IE9Oow37ja+S5ozOU+ZiMEuMxLzW6k1441odL5FBo1WdINI0eiMjY8GSWq/mZInAI3SZzNp40ojZmcmIZoJ425oMDUpVEatTR2sYT/HQ81g8U28BYKvSY3KCkYOFYhm9EDXHjiwQdmBwDIsjPRH48zlOM7qQnFDy5KDjJ3yrJ/Ud2gJoWqU1Eh/Z4IrohtP/TgUnHIRvXGdDMSX0MRIw5kNT0iAjxzjFiq3LeIjeE6AvYD6JQ9vVQqMc9qJjMEX3mcALWh3s301Tqz3ZmShcoeHIM6mKhRGK7wHBooov2/YfpnNoSIkeTRWs8rzaDVtyI5IYjs1pIRdL1yXpsnPARQ9KkrchR/yNiJLrECnHqSG4dHEeblkWUicoQv32hNvcTRUBJF/9cuR37AfcLfNkgSub14rH3WatPZLYoeHIM8HZxgt02LJnUIuOraJMHTNKsDE4KjxQbJ1/qeK2wcXnN8vdDeMz2PUyjCC3+7A9Ga0SDaKlNaWIhqOlKOhYvSuRa2E+M4zwfpFipOTJT9PNWf7EbiBoTVmhAwVv39avfXPW8sGrDQ4sl4ZmlSWgpyzCJbEXBk7PSRieb3k+c1B9XOiTxstFEwsrm/Ex9jk5CGixo4hiCSLGKAiLGeKwWjN5Ecogatb+xZXfwx9GJyOzIqYhcyxeWOZos1vqoIk7YFIiEqcXtAjVIUr9MUHlSkk9f/eYq4Xxk+SKAKJJG/uIyQ5MvFVyKLJ5NJyiRXSCCwu9GvWid4ZvGEYzZiCMRDB2CTC0Ze/8hhggU0RvdmW2HZoWoDGEcm5yCsGEqrxuR064WgYviyUluneRzSwqW6Axx5rmgAYdj2gTPK18a8CbiN2R8GF8SgHQz/4/Ngik2PK+kb0n5Yveg+1SkC8ZOOCL7wMoGRG5EIrdM8uElaiDSIHojsmNs1aEM05YtpONI4bWLlE5d3MbgOPgCQG0NHyHHS7MOAoa1AAFDoNZRm084dp77usoBRvnnlbQwdUw6YBFEUp4Y/UlvinTDuhOJyCaIGqpnjiHIND1guGYI8lhEtkm0dgVRqFHbnMVtCNEvzx3RXPvcMdnlJeWSodp0Xo6B/YKmGxpwiNgQSCC1SxMQU2SYmYmf8TllfbwfS/LJ5e9EuuGsJyHpDyIRPGM0m+wDnjeiDNrt6chkygn1OCITbkdEuW82milIWZLeW5K4jYEHES8inZWfuLr1Kojw6qoFCBQDqVv4ooF4YdG4Y6kBVhBBxBDTPBYMxrZhA+G5p2N1Cs06IueKgifbIGojOmDjBDw0hVeYPkKrO7MoiVYwbCNkjPmifseJt6bliDJI3dEWj+WgZ2hSuXuJlKnbDaHTkgYTvIlMiCGSA9KcNLfwGR52dBIRMkaMrk+GRWNV+OZi9SDFTOQo0h0KngyhvkMdDnFD5GhKad8nRGoYlVmdHI8XaTLY5710jyTfcsK17+YEDT88x2NzRIHGHLpQiaipkTJsmjodtTleK75o4C2snbI0vjymrIzA64dIYsPgfmiMwf8o0iX7nKRkmZAOY+pGjeCIANruR6IymkIQODZOyq0pnPoc7PNeqsOLWWmbAcZLhWYd7BJYKsbg93Ss8hxzfZ3R/AuSfFNJb96tdFbiTWRiCq9Z/T+M9aQ4gZUkqHfSocnrie2BQdEi3bLPSUqWAe3prDhQIzjMyEPvG+nGGsFhUMZysI5DBI8TPH/PCgl/vLp1OXBspBgf2CwCOwZdk0x6eX5JUa4Djx1eOcSTDkzqnExN4X9IHwOLtvKaMVrtrmWVdQSSLzP8L2Z20qIi3bLPSUrmC14uPFic/G4ysq4bXYIIGydITpqk0HZlX8FDYKk9Ue+jPrg0iJBZ8ofGEBiawFseUMz3DyvNO5uoTShAbRSxq68Tj8Xrhg+SOiD1wOrto4ZK9+Yc7RoiR2XXk5TMF9rSMSW3rzXf+DlBMrOSSzr3DmVfwaum6keXZo2lgHiz5hwTZHguaMjB60YH5jpIDyNG2yaqUIdjXTsahoiOqdnxJQVYIb6OUrs8yR2a55jokmh+2/qAIl2w60lK5gmrhz+iTNkgdUgkQZqyDhE+BvsKHu331JpuWxZtnTscNynEB5fxaKQNsQAQuW1q/aeuRgMQ/489Y936eqQxaTohMkfsmKpSm08QOYY+0/BCqpMUMUsCEQ0CzzP/KyJ7nKRkXnCSZPoJHi+692hWoCvyFOwreNSc6C6kpoSZfM7Q8o8lgCV4gCYSVi+nBroNvHHU7mhowVw+Bl9UmP2JP49GIR7rXiX1zEoIRMmILV9miOSIFokoqePxmtOoIiKFXU9SMh9IrTErkVoZKS1a2U+59Ms+glfrd0SYDIqeKx9WhIYolePeJX05hBULSIFiIaDzcownJPmMEgnWsWC1eaWOBsOX95DS9VqFlxoi+yciDbucpGQ+ICikCVmmB38WJ8ErTrz7+wjenOt32ACYG8pEkzpWjRQjkfN9t6Qvx3hakpsVQaspyBaiM1ZRIEInTVmnrDBN5fbldiJAhkRjLQFeC15vfHrVoC4ihV1OUjIPqO0QCXASZPQXNZ91/q9jso/g0SBz0xnV77BrMNKLtfpo/qnHyDHTNMLvDqmHcj9E36R2mTQzXDi3rlgOw1pnjZJbWCKJSJF1BeeeJhY5GbucpGQ+UDtibBf1HhZZffw57HoVPJow2lW7h3By5yTPe27K9Tv2j5Ql3Y7MnmwHOmPkJsJiQgy2ikOp9buXNtFihfQmtTq4tBjOW4jeGdMGXOL3w8tHtCkiG1DwlgWiQzTAiRrwaVVvHSmxffx1u8IkD+Znkj7lxLsOTNik/miwoJtwalyrpCxvNzCKEzFRm6MJ6Fjt/evqd9RbWYmdz+W6FRKqWZ3mn9oVKiI7oOAtD0SPdeFYW224+vcLSwMLvrxnbxhltQ90DdI8QTfhLVa3/n+I/ki7MuH/lE00+8A4L1KG1MRIFVaIkJlZia8NG8exo6ex+h3PHWZxXj8ic8R3jMuKvYTaISPHRGRHFLzlQi2PcVO0ryMypNEQnAr1PkSPQdCk15i00s7I3BXSkxifgRQb0dBTS0dhyz61vlPC80I0ishRg2sbPjDiI3JEWazMfgo4/mH9jqWRiCR5fRBA7CTrRPY7irmdyJAIUUR25KJPPnJ+EM3QeIH44eu6ZOShMS/T5ceadaTvWEaGKSws77OpPof/bCio/B9DokmnsigpkQnQTn/e4EsjokLoGLFGx2WF42TuJEJ3HnM9h/W7oXmcGt2mNCVfUPD/rVtdQUTWoOD1CydbBkgjRAwnZiOdNgYn4Lq2XRXBKoycpIGIhfZ5poBQZ6qRU4X7IIpBVLik9kdkSDs/3aQ03DBthDXcDoH3MmLB+C1mh14/yfVKEw81xhYiLEQOnxtifZ4M63dD8/i6OiuRKaPYWNeOY6V+d+hzJdIlCp5UOKFep8xpRCQQC8QDYdy0SCtpUFrzEUEEEWM0qysgPpzQia6IYnZdIZ1IEgFkY+BxvY4YsnGdSJL5kkwoYV+ruPFYY3CfpCi5P7yAGPNJ6V4E1ARZpYL6HbYHlkfCM0f6mWWYxiAqp1uTYwUibl4nEdkDBU92AXFB+BCVKi71klTpLhDJIY40yhDtUTNDuBAtBBEDNQIwjAz3gciNqJFoCWHEK0f6kGYdfmZR1IuECJrj5LiprdLow3NL5EakN4QvHAgdqVggymZVhU2DpkVkDQqenBWivxoJEnXQWo93jWiMSzbSnVMAsWHB1V02PhsI81mX1SH1CPj2MN1/VblOVMdKByzY+vDyNxWWbyI9zFQXRBIhZzg1g8CdoCJyIAqenAe8z6gRVgGsW3sbkSLrudWfmQtJJMSItEMgPcv/E1lyv2zMoJwTRMHMxcS/uG41BRHZEQVPeoIaXxW/bRsLuRJd1XXnDgVDOxAp3rBZ4odok8ktYxEbkTLG8tskedHqVhE5EwqeyPlQhz7DtiYVETkBCp7I6aGOR/dqtX2sa1IRkROi4ImcHjot6xgw16oTuSAUPJHTU+eIYhSnM3OsbiciJ0bBEzk9dY7olAZni3SHgidyejC/Y4nAaiEiF4SCJyIiXaDgiYhIFyh4IiLSBQqeiIh0gYInIiJdoOCJiEgXKHgiItIFCp6IiHSBgiciIl2g4ImISBcoeCIi0gUKnoiIdIGCJyIiXaDgiYhIFyh4IiLSBQqeiIh0gYInIiJdoOCJiEgXKHgiItIFCp6IiHSBgiciIl2g4ImISBcoeCIi0gUKnoiIdIGCJyIiXaDgiYhIFyh4IiLSBQqeiIh0gYInIiJdoOCJiEgXKHgiItIFCp6IiHSBgiciIl2g4ImISBf8H5cns91u7/drAAAAEGRlQkc1QkRDMDQ4QzY5OUJCNjc0hy8LMwAAAABJRU5ErkJggg=='),
(7, 'LTA', 'blockA', '', 'Pending', '2026-07-16 00:51:29', NULL, NULL, NULL, NULL),
(8, 'verti', 'masjid', NULL, 'Pending', '2026-08-03 06:08:35', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wet_chemical_system`
--

CREATE TABLE `wet_chemical_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wet_riser_system`
--

CREATE TABLE `wet_riser_system` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `item_bil` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `panel_type` varchar(150) DEFAULT NULL,
  `panel_brand` varchar(150) DEFAULT NULL,
  `panel_model` varchar(150) DEFAULT NULL,
  `panel_qty` varchar(50) DEFAULT NULL,
  `location_floor` varchar(255) DEFAULT NULL,
  `zone_loop` varchar(100) DEFAULT NULL,
  `checklist` varchar(50) DEFAULT NULL,
  `item_condition` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wet_riser_system`
--

INSERT INTO `wet_riser_system` (`id`, `task_id`, `section_name`, `item_bil`, `description`, `panel_type`, `panel_brand`, `panel_model`, `panel_qty`, `location_floor`, `zone_loop`, `checklist`, `item_condition`, `remarks`) VALUES
(41, 7, 'PANEL PROFILE', '1', 'WET RISER', 'ada', NULL, NULL, '2', 'bawah', NULL, NULL, NULL, NULL),
(42, 7, 'PANEL PROFILE', '2', 'WET RISER', 'takde', NULL, NULL, '3', 'atas', NULL, NULL, NULL, NULL),
(43, 7, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'tandas', NULL, ''),
(44, 7, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, 'api', NULL, ''),
(45, 7, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(46, 7, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, ''),
(47, 7, 'PUMP PRESSURE', '1-JOCKEY', 'CUT IN (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '445', NULL, ''),
(48, 7, 'PUMP PRESSURE', '1-DUTY', 'CUT IN (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '56', NULL, ''),
(49, 7, 'PUMP PRESSURE', '1-STANDBY', 'CUT IN (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(50, 7, 'PUMP PRESSURE', '2-JOCKEY', 'CUT OUT (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '43', NULL, ''),
(51, 7, 'PUMP PRESSURE', '2-DUTY', 'CUT OUT (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '34', NULL, ''),
(52, 7, 'PUMP PRESSURE', '2-STANDBY', 'CUT OUT (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(53, 7, 'PUMP PRESSURE', '3-JOCKEY', 'SUCTION VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '34', NULL, ''),
(54, 7, 'PUMP PRESSURE', '3-DUTY', 'SUCTION VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '234', NULL, ''),
(55, 7, 'PUMP PRESSURE', '3-STANDBY', 'SUCTION VALVE (STATUS) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, ''),
(56, 7, 'PUMP PRESSURE', '4-JOCKEY', 'DISCHARGE VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '4', NULL, ''),
(57, 7, 'PUMP PRESSURE', '4-DUTY', 'DISCHARGE VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '4', NULL, ''),
(58, 7, 'PUMP PRESSURE', '4-STANDBY', 'DISCHARGE VALVE (STATUS) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(59, 7, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(60, 7, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(61, 7, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(62, 7, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(63, 7, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(64, 7, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(65, 7, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(66, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(67, 7, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(68, 7, 'EQUIPMENT', '2', 'LANDING VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Not Applicable', ''),
(69, 7, 'EQUIPMENT', '3', 'CANVAS HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(70, 7, 'EQUIPMENT', '4', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(71, 7, 'EQUIPMENT', '5', 'PRESSURE GAUGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(72, 7, 'EQUIPMENT', '6', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(73, 7, 'EQUIPMENT', '7', 'LIVE TEST', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(74, 7, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(75, 7, 'PUMP TEST - MANUAL', 'II', 'WET RISER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(76, 7, 'PUMP TEST - MANUAL', 'III', 'WET RISER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(77, 7, 'PUMP TEST - MANUAL', 'IV', 'WET RISER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(78, 7, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(79, 7, 'PUMP TEST - AUTO', 'II', 'WET RISER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(80, 7, 'PUMP TEST - AUTO', 'III', 'WET RISER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(81, 7, 'PUMP TEST - AUTO', 'IV', 'WET RISER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(82, 7, 'PANEL PROFILE', '1', 'WET RISER', 'ada', NULL, NULL, '2', 'tilam', NULL, NULL, NULL, NULL),
(83, 7, 'PANEL PROFILE', '2', 'WET RISER', 'tilam', NULL, NULL, '3', 'ada', NULL, NULL, NULL, NULL),
(84, 7, 'PUMP INFO', '1', 'FIRE PUMP LOCATION', NULL, NULL, NULL, NULL, NULL, NULL, 'hehe', NULL, ''),
(85, 7, 'PUMP INFO', '2', 'FIRE PUMP TYPE', NULL, NULL, NULL, NULL, NULL, NULL, 'ba', NULL, ''),
(86, 7, 'PUMP INFO', '3', 'WORKING PRESSURE', NULL, NULL, NULL, NULL, NULL, NULL, 'as', NULL, ''),
(87, 7, 'PUMP INFO', '4', 'WATER TANK SIZE AND CAPACITY (GALLON)', NULL, NULL, NULL, NULL, NULL, NULL, 'wa', NULL, ''),
(88, 7, 'PUMP PRESSURE', '1-JOCKEY', 'CUT IN (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, ''),
(89, 7, 'PUMP PRESSURE', '1-DUTY', 'CUT IN (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '32', NULL, ''),
(90, 7, 'PUMP PRESSURE', '1-STANDBY', 'CUT IN (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, ''),
(91, 7, 'PUMP PRESSURE', '2-JOCKEY', 'CUT OUT (PSI) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(92, 7, 'PUMP PRESSURE', '2-DUTY', 'CUT OUT (PSI) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '32', NULL, ''),
(93, 7, 'PUMP PRESSURE', '2-STANDBY', 'CUT OUT (PSI) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '23', NULL, ''),
(94, 7, 'PUMP PRESSURE', '3-JOCKEY', 'SUCTION VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(95, 7, 'PUMP PRESSURE', '3-DUTY', 'SUCTION VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(96, 7, 'PUMP PRESSURE', '3-STANDBY', 'SUCTION VALVE (STATUS) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '3', NULL, ''),
(97, 7, 'PUMP PRESSURE', '4-JOCKEY', 'DISCHARGE VALVE (STATUS) (JOCKEY)', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(98, 7, 'PUMP PRESSURE', '4-DUTY', 'DISCHARGE VALVE (STATUS) (DUTY)', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, ''),
(99, 7, 'PUMP PRESSURE', '4-STANDBY', 'DISCHARGE VALVE (STATUS) (STANDBY)', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, ''),
(100, 7, 'CONTROL PANEL', '1', 'CONTROL PANEL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(101, 7, 'CONTROL PANEL', '2', 'DUTY AND STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(102, 7, 'CONTROL PANEL', '3', 'SEALED LEAD ACID BATTERY', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(103, 7, 'CONTROL PANEL', '4', 'CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(104, 7, 'CONTROL PANEL', '5', 'FUSES, LED LIGHT BULB, SWITCHES & BUTTONS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(105, 7, 'CONTROL PANEL', '6', 'VOLT METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(106, 7, 'CONTROL PANEL', '7', 'AMPERE METER', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(107, 7, 'CONTROL PANEL', '8', 'WIRINGS AND CABLINGS', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(108, 7, 'EQUIPMENT', '1', 'BREACHING INLET IS FREE FROM OBSTRUCTION', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(109, 7, 'EQUIPMENT', '2', 'LANDING VALVE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(110, 7, 'EQUIPMENT', '3', 'CANVAS HOSE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(111, 7, 'EQUIPMENT', '4', 'DIFFUSE NOZZLE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(112, 7, 'EQUIPMENT', '5', 'PRESSURE GAUGE', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(113, 7, 'EQUIPMENT', '6', 'AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(114, 7, 'EQUIPMENT', '7', 'LIVE TEST', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(115, 7, 'PUMP TEST - MANUAL', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(116, 7, 'PUMP TEST - MANUAL', 'II', 'WET RISER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(117, 7, 'PUMP TEST - MANUAL', 'III', 'WET RISER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(118, 7, 'PUMP TEST - MANUAL', 'IV', 'WET RISER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(119, 7, 'PUMP TEST - AUTO', 'I', 'AC FAIL', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Normal', ''),
(120, 7, 'PUMP TEST - AUTO', 'II', 'WET RISER DUTY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'Done', 'Faulty', ''),
(121, 7, 'PUMP TEST - AUTO', 'III', 'WET RISER STANDBY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', ''),
(122, 7, 'PUMP TEST - AUTO', 'IV', 'WET RISER JOCKEY PUMP', NULL, NULL, NULL, NULL, NULL, NULL, 'N/A', 'Not Applicable', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dry_riser_system`
--
ALTER TABLE `dry_riser_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fireman_intercom_system`
--
ALTER TABLE `fireman_intercom_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_alarm_system_add`
--
ALTER TABLE `fire_alarm_system_add`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_alarm_system_con`
--
ALTER TABLE `fire_alarm_system_con`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_hose_reel_system`
--
ALTER TABLE `fire_hose_reel_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_sprinkler_system`
--
ALTER TABLE `fire_sprinkler_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_suppression_system_1`
--
ALTER TABLE `fire_suppression_system_1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_suppression_system_2`
--
ALTER TABLE `fire_suppression_system_2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_suppression_system_3`
--
ALTER TABLE `fire_suppression_system_3`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `fire_suppression_system_4`
--
ALTER TABLE `fire_suppression_system_4`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `pressurised_hydrant_system`
--
ALTER TABLE `pressurised_hydrant_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `wet_chemical_system`
--
ALTER TABLE `wet_chemical_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `wet_riser_system`
--
ALTER TABLE `wet_riser_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dry_riser_system`
--
ALTER TABLE `dry_riser_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `fireman_intercom_system`
--
ALTER TABLE `fireman_intercom_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `fire_alarm_system_add`
--
ALTER TABLE `fire_alarm_system_add`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `fire_alarm_system_con`
--
ALTER TABLE `fire_alarm_system_con`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=494;

--
-- AUTO_INCREMENT for table `fire_hose_reel_system`
--
ALTER TABLE `fire_hose_reel_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `fire_sprinkler_system`
--
ALTER TABLE `fire_sprinkler_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `fire_suppression_system_1`
--
ALTER TABLE `fire_suppression_system_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `fire_suppression_system_2`
--
ALTER TABLE `fire_suppression_system_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `fire_suppression_system_3`
--
ALTER TABLE `fire_suppression_system_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fire_suppression_system_4`
--
ALTER TABLE `fire_suppression_system_4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pressurised_hydrant_system`
--
ALTER TABLE `pressurised_hydrant_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wet_chemical_system`
--
ALTER TABLE `wet_chemical_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wet_riser_system`
--
ALTER TABLE `wet_riser_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dry_riser_system`
--
ALTER TABLE `dry_riser_system`
  ADD CONSTRAINT `dry_riser_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fireman_intercom_system`
--
ALTER TABLE `fireman_intercom_system`
  ADD CONSTRAINT `int_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_alarm_system_add`
--
ALTER TABLE `fire_alarm_system_add`
  ADD CONSTRAINT `fas_add_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_alarm_system_con`
--
ALTER TABLE `fire_alarm_system_con`
  ADD CONSTRAINT `fas_con_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_hose_reel_system`
--
ALTER TABLE `fire_hose_reel_system`
  ADD CONSTRAINT `hose_reel_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_sprinkler_system`
--
ALTER TABLE `fire_sprinkler_system`
  ADD CONSTRAINT `sprinkler_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_suppression_system_1`
--
ALTER TABLE `fire_suppression_system_1`
  ADD CONSTRAINT `sup1_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_suppression_system_2`
--
ALTER TABLE `fire_suppression_system_2`
  ADD CONSTRAINT `sup2_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_suppression_system_3`
--
ALTER TABLE `fire_suppression_system_3`
  ADD CONSTRAINT `sup3_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `fire_suppression_system_4`
--
ALTER TABLE `fire_suppression_system_4`
  ADD CONSTRAINT `sup4_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `pressurised_hydrant_system`
--
ALTER TABLE `pressurised_hydrant_system`
  ADD CONSTRAINT `hydrant_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `wet_chemical_system`
--
ALTER TABLE `wet_chemical_system`
  ADD CONSTRAINT `wet_chem_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;

--
-- Constraints for table `wet_riser_system`
--
ALTER TABLE `wet_riser_system`
  ADD CONSTRAINT `wet_riser_fk` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
