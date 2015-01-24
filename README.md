#The Rhythm Randomizer
The Rhythm Randomizer is a web app that automatically generates random musically-notated rhythm examples to aid in sight-reaing practice.

See it in action at [www.rhythmrandomizer.com](http://www.rhythmrandomizer.com)

After cloning, you will need to create a database, and edit the database connection parameters in `sqlconn.php`. Then, you will need to import the following into the database. This will give you the necessary table structure, as well as the note objects matching the images in the `notes` folder.

```sql
-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 24, 2015 at 10:30 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
/*!40101 SET NAMES utf8 */;

--
-- Database: `rhythmrandomizer`
--

-- --------------------------------------------------------

--
-- Table structure for table `noteCats`
--

CREATE TABLE IF NOT EXISTS `noteCats` (
`noteCatID` int(11) NOT NULL,
`noteCatName` varchar(32) NOT NULL,
PRIMARY KEY (`noteCatID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `noteCats`
--

INSERT INTO `noteCats` (`noteCatID`, `noteCatName`) VALUES
(1, 'Basic Notes'),
(2, 'Basic Rests'),
(3, '16th Note Subdivisions'),
(4, 'Dotted Notes'),
(5, 'Triplets'),
(6, '8th Rest Rhythms');

-- --------------------------------------------------------

--
-- Table structure for table `noteGroups`
--

CREATE TABLE IF NOT EXISTS `noteGroups` (
`noteGroupID` int(11) NOT NULL,
`noteGroupName` varchar(64) NOT NULL,
`noteGroupGraphic` varchar(1) NOT NULL,
`noteGroupNoteCatID` int(11) NOT NULL,
`noteGroupDefault` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`noteGroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `noteGroups`
--

INSERT INTO `noteGroups` (`noteGroupID`, `noteGroupName`, `noteGroupGraphic`, `noteGroupNoteCatID`, `noteGroupDefault`) VALUES
(1, 'whole Note', 'w', 1, 0),
(2, 'half Note', 'h', 1, 0),
(3, 'quarter Note', 'q', 1, 1),
(4, 'quarter rest', 'z', 2, 1),
(5, 'half rest', 'x', 2, 0),
(6, 'whole rest', 'c', 2, 0),
(7, '2 beamed 8th notes', 'n', 1, 1),
(8, '4 beamed 16th notes', 'm', 3, 0),
(9, '1 8th note & 2 16th notes, beamed', 'y', 3, 0),
(10, '2 16th notes & 1 8th note, beamed', 'u', 3, 0),
(11, '1 16th note, 1 8th note, & 1 16th note, beamed', 'i', 3, 0),
(12, 'dotted half note', 'd', 4, 0),
(13, 'dotted quarter note', 'f', 4, 0),
(14, 'dotted 8th note', 'g', 4, 0),
(15, 'quarter note triplet', 'r', 5, 0),
(16, '8th note truplet', 't', 5, 0),
(17, '8th note with 8th rest', 'e', 6, 0),
(18, '16th note with 8th rest', 's', 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
`noteID` int(11) NOT NULL,
`noteName` varchar(64) NOT NULL,
`noteValue` int(11) NOT NULL,
`noteGraphic` varchar(1) NOT NULL,
`noteGroupID` int(11) NOT NULL,
PRIMARY KEY (`noteID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`noteID`, `noteName`, `noteValue`, `noteGraphic`, `noteGroupID`) VALUES
(1, 'whole note', 4, 'w', 1),
(2, 'half note', 2, 'h', 2),
(3, 'quarter note', 1, 'q', 3),
(4, 'quarter rest', 1, 'z', 4),
(5, 'half rest', 2, 'x', 5),
(6, 'whole rest', 4, 'c', 6),
(7, '2 beamed 8th notes', 1, 'n', 7),
(8, '4 beamed 16th notes', 1, 'm', 8),
(9, '1 8th note & 2 16th notes, beamed', 1, 'y', 9),
(10, '2 16th notes and 1 8th note, beamed', 1, 'u', 11),
(11, '1 16th note, 1 8th note & 1 16th note, beamed', 1, 'i', 11),
(12, 'dotted half note', 3, 'd', 12),
(13, 'dotted quarter note, 8th note', 2, 'k', 13),
(14, '8th note, dotted quarter note', 2, 'l', 13),
(15, 'dotted 8th note & 16th note, beamed', 1, 'o', 14),
(16, '16th note and dotted 8th note, beamed', 1, 'p', 14),
(17, 'quarter note triplet', 2, 'r', 15),
(18, '8th note triplet', 1, 't', 16),
(19, '8th note, 8th rest', 1, 'e', 17),
(20, '8th rest, 8th note', 1, 'v', 17),
(21, '2 16th notes, beamed, 8th rest', 1, 's', 18),
(22, '8th rest, 2 16th notes, beamed', 1, 'a', 18);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
```
