-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 20, 2012 at 03:23 PM
-- Server version: 5.1.52
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zpanel_proftpd`
--
CREATE DATABASE `zpanel_proftpd`;
USE `zpanel_proftpd`;
-- --------------------------------------------------------

--
-- Table structure for table `ftpgroup`
--

CREATE TABLE IF NOT EXISTS `ftpgroup` (
  `groupname` varchar(16) NOT NULL DEFAULT '',
  `gid` smallint(6) NOT NULL DEFAULT '5500',
  `members` varchar(16) NOT NULL DEFAULT '',
  KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ProFTP group table';

--
-- Dumping data for table `ftpgroup`
--

INSERT INTO `ftpgroup` (`groupname`, `gid`, `members`) VALUES
('ftpgroup', 2001, 'ftpuser');

-- --------------------------------------------------------

--
-- Table structure for table `ftpquotalimits`
--

CREATE TABLE IF NOT EXISTS `ftpquotalimits` (
  `name` varchar(30) DEFAULT NULL,
  `quota_type` enum('user','group','class','all') NOT NULL DEFAULT 'user',
  `per_session` enum('false','true') NOT NULL DEFAULT 'false',
  `limit_type` enum('soft','hard') NOT NULL DEFAULT 'soft',
  `bytes_in_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `bytes_out_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `bytes_xfer_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `files_in_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `files_out_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `files_xfer_avail` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ftpquotatallies`
--

CREATE TABLE IF NOT EXISTS `ftpquotatallies` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `quota_type` enum('user','group','class','all') NOT NULL DEFAULT 'user',
  `bytes_in_used` int(10) unsigned NOT NULL DEFAULT '0',
  `bytes_out_used` int(10) unsigned NOT NULL DEFAULT '0',
  `bytes_xfer_used` int(10) unsigned NOT NULL DEFAULT '0',
  `files_in_used` int(10) unsigned NOT NULL DEFAULT '0',
  `files_out_used` int(10) unsigned NOT NULL DEFAULT '0',
  `files_xfer_used` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ftpuser`
--

CREATE TABLE IF NOT EXISTS `ftpuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(32) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `uid` smallint(6) NOT NULL DEFAULT '33',
  `gid` smallint(6) NOT NULL DEFAULT '33',
  `homedir` varchar(255) NOT NULL DEFAULT '',
  `shell` varchar(16) NOT NULL DEFAULT '/sbin/nologin',
  `count` int(11) NOT NULL DEFAULT '0',
  `accessed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ProFTP user table' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
