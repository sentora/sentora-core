-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 07, 2011 at 02:02 AM
-- Server version: 5.1.37
-- PHP Version: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zpanel_roundcube`
--
CREATE DATABASE `zpanel_roundcube`;
USE `zpanel_roundcube`;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cache_key` varchar(128) CHARACTER SET ascii NOT NULL,
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `data` longtext NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cache_id`),
  KEY `created_index` (`created`),
  KEY `user_cache_index` (`user_id`,`cache_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cache`
--


-- --------------------------------------------------------

--
-- Table structure for table `contactgroupmembers`
--

CREATE TABLE IF NOT EXISTS `contactgroupmembers` (
  `contactgroup_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`contactgroup_id`,`contact_id`),
  KEY `contact_id_fk_contacts` (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contactgroupmembers`
--


-- --------------------------------------------------------

--
-- Table structure for table `contactgroups`
--

CREATE TABLE IF NOT EXISTS `contactgroups` (
  `contactgroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`contactgroup_id`),
  KEY `contactgroups_user_index` (`user_id`,`del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contactgroups`
--


-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `firstname` varchar(128) NOT NULL DEFAULT '',
  `surname` varchar(128) NOT NULL DEFAULT '',
  `vcard` text,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`contact_id`),
  KEY `user_contacts_index` (`user_id`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contacts`
--


-- --------------------------------------------------------

--
-- Table structure for table `identities`
--

CREATE TABLE IF NOT EXISTS `identities` (
  `identity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `standard` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `organization` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL,
  `reply-to` varchar(128) NOT NULL DEFAULT '',
  `bcc` varchar(128) NOT NULL DEFAULT '',
  `signature` text,
  `html_signature` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`identity_id`),
  KEY `user_identities_index` (`user_id`,`del`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `identities`
--

INSERT INTO `identities` (`identity_id`, `del`, `standard`, `name`, `organization`, `email`, `reply-to`, `bcc`, `signature`, `html_signature`, `user_id`, `changed`) VALUES
(1, 0, 1, '', '', 'zadmin@ztest.com', '', '', NULL, 0, 1, '2011-05-05 15:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `cache_key` varchar(128) CHARACTER SET ascii NOT NULL,
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `idx` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `size` int(11) unsigned NOT NULL DEFAULT '0',
  `headers` text NOT NULL,
  `structure` text,
  PRIMARY KEY (`message_id`),
  UNIQUE KEY `uniqueness` (`user_id`,`cache_key`,`uid`),
  KEY `created_index` (`created`),
  KEY `index_index` (`user_id`,`cache_key`,`idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `sess_id` varchar(40) NOT NULL,
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `ip` varchar(40) NOT NULL,
  `vars` mediumtext NOT NULL,
  PRIMARY KEY (`sess_id`),
  KEY `changed_index` (`changed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`sess_id`, `created`, `changed`, `ip`, `vars`) VALUES
('4cda1e6740f08dbab99fa408f8b99cf6', '2011-05-05 16:10:43', '2011-05-05 16:10:53', '10.1.1.100', 'bGFuZ3VhZ2V8czo1OiJlbl9VUyI7YXV0aF90aW1lfGk6MTMwNDYxMTg0MztpbWFwX25hbWVzcGFjZXxhOjM6e3M6ODoicGVyc29uYWwiO2E6MTp7aTowO2E6Mjp7aTowO3M6MDoiIjtpOjE7czoxOiIuIjt9fXM6NToib3RoZXIiO047czo2OiJzaGFyZWQiO2E6MTp7aTowO2E6Mjp7aTowO3M6NzoiI1B1YmxpYyI7aToxO3M6MToiLiI7fX19aW1hcF9kZWxpbWl0ZXJ8czoxOiIuIjt1c2VyX2lkfHM6MToiMSI7dXNlcm5hbWV8czoxNjoiemFkbWluQHp0ZXN0LmNvbSI7aW1hcF9ob3N0fHM6OToibG9jYWxob3N0IjtpbWFwX3BvcnR8aToxNDM7aW1hcF9zc2x8TjtwYXNzd29yZHxzOjI0OiJ2aGIxRjRmeHg3ZmNmOXBpb2Q0Nzd3PT0iO2xvZ2luX3RpbWV8aToxMzA0NjExODQzO3RpbWV6b25lfGQ6LTU7dGFza3xzOjQ6Im1haWwiO3JlcXVlc3RfdG9rZW5zfGE6MTp7czo0OiJtYWlsIjtzOjMyOiJiNjg0ZTQwNWQ4ZjZhYzFlYzNiNWZjODMxMTFkNjNjNiI7fW1ib3h8czo1OiJJTkJPWCI7c29ydF9jb2x8czo0OiJkYXRlIjtzb3J0X29yZGVyfHM6NDoiREVTQyI7bGlzdF9hdHRyaWJ8YToxMDp7czo0OiJuYW1lIjtzOjg6Im1lc3NhZ2VzIjtzOjI6ImlkIjtzOjExOiJtZXNzYWdlbGlzdCI7czoxMToiY2VsbHNwYWNpbmciO3M6MToiMSI7czo3OiJzdW1tYXJ5IjtzOjEyOiJNZXNzYWdlIGxpc3QiO3M6MTE6Im1lc3NhZ2VpY29uIjtzOjIxOiIvaW1hZ2VzL2ljb25zL2RvdC5wbmciO3M6MTA6InVucmVhZGljb24iO3M6MjQ6Ii9pbWFnZXMvaWNvbnMvdW5yZWFkLnBuZyI7czoxMToiZGVsZXRlZGljb24iO3M6MjU6Ii9pbWFnZXMvaWNvbnMvZGVsZXRlZC5wbmciO3M6MTE6InJlcGxpZWRpY29uIjtzOjI1OiIvaW1hZ2VzL2ljb25zL3JlcGxpZWQucG5nIjtzOjE0OiJhdHRhY2htZW50aWNvbiI7czoyODoiL2ltYWdlcy9pY29ucy9hdHRhY2htZW50LnBuZyI7czo3OiJjb2x1bW5zIjthOjc6e2k6MDtzOjc6InRocmVhZHMiO2k6MTtzOjc6InN1YmplY3QiO2k6MjtzOjQ6ImZyb20iO2k6MztzOjQ6ImRhdGUiO2k6NDtzOjQ6InNpemUiO2k6NTtzOjQ6ImZsYWciO2k6NjtzOjEwOiJhdHRhY2htZW50Ijt9fXNraW5fcGF0aHxzOjE3OiJza2lucy9tdmlzaW9uMl9lbiI7cXVvdGFfZGlzcGxheXxzOjU6ImltYWdlIjtmb2xkZXJzfGE6MTp7czo1OiJJTkJPWCI7YToyOntzOjM6ImNudCI7aToxO3M6NjoibWF4dWlkIjtpOjI7fX11bnNlZW5fY291bnR8YToxOntzOjU6IklOQk9YIjtpOjE7fWNvbXBvc2V8YTo0OntzOjI6ImlkIjtzOjIyOiI0MTUzMjAyMzk0ZGMyY2MwZDc4NjYwIjtzOjU6InBhcmFtIjthOjQ6e3M6NDoidGFzayI7czo0OiJtYWlsIjtzOjY6ImFjdGlvbiI7czo3OiJjb21wb3NlIjtzOjQ6Im1ib3giO3M6NToiSU5CT1giO3M6OToic2VudF9tYm94IjtzOjQ6IlNlbnQiO31zOjc6Im1haWxib3giO3M6NToiSU5CT1giO3M6MTA6ImRlbGV0ZWljb24iO3M6NTI6InNraW5zL212aXNpb24yX2VuL2ltYWdlcy9pY29ucy9yZW1vdmUtYXR0YWNobWVudC5wbmciO30='),
('a68b43b484ce79829b86b6095eaa0edc', '2011-05-05 15:59:47', '2011-05-05 16:23:25', '10.1.1.100', 'bGFuZ3VhZ2V8czo1OiJlbl9VUyI7YXV0aF90aW1lfGk6MTMwNDYxMTE4NztpbWFwX25hbWVzcGFjZXxhOjM6e3M6ODoicGVyc29uYWwiO2E6MTp7aTowO2E6Mjp7aTowO3M6MDoiIjtpOjE7czoxOiIuIjt9fXM6NToib3RoZXIiO047czo2OiJzaGFyZWQiO2E6MTp7aTowO2E6Mjp7aTowO3M6NzoiI1B1YmxpYyI7aToxO3M6MToiLiI7fX19aW1hcF9kZWxpbWl0ZXJ8czoxOiIuIjt1c2VyX2lkfHM6MToiMSI7dXNlcm5hbWV8czoxNjoiemFkbWluQHp0ZXN0LmNvbSI7aW1hcF9ob3N0fHM6OToibG9jYWxob3N0IjtpbWFwX3BvcnR8aToxNDM7aW1hcF9zc2x8TjtwYXNzd29yZHxzOjI0OiIzRFVsNE1rOTZHZElQVmJZUUdoYkNnPT0iO2xvZ2luX3RpbWV8aToxMzA0NjExMTg3O3RpbWV6b25lfGQ6LTU7dGFza3xzOjQ6Im1haWwiO3JlcXVlc3RfdG9rZW5zfGE6MTp7czo0OiJtYWlsIjtzOjMyOiI2Zjg0NzM3YjQ1ZGI2NTZjY2Y4NGE0NDBjZTRmYjVkYiI7fW1ib3h8czo1OiJJTkJPWCI7c29ydF9jb2x8czo0OiJkYXRlIjtzb3J0X29yZGVyfHM6NDoiREVTQyI7bGlzdF9hdHRyaWJ8YTo2OntzOjQ6Im5hbWUiO3M6ODoibWVzc2FnZXMiO3M6MjoiaWQiO3M6MTE6Im1lc3NhZ2VsaXN0IjtzOjExOiJjZWxsc3BhY2luZyI7czoxOiIwIjtzOjc6ImNvbHVtbnMiO2E6Nzp7aTowO3M6NzoidGhyZWFkcyI7aToxO3M6Nzoic3ViamVjdCI7aToyO3M6NDoiZnJvbSI7aTozO3M6NDoiZGF0ZSI7aTo0O3M6NDoic2l6ZSI7aTo1O3M6NDoiZmxhZyI7aTo2O3M6MTA6ImF0dGFjaG1lbnQiO31zOjc6InN1bW1hcnkiO3M6MTI6Ik1lc3NhZ2UgbGlzdCI7czoxNToib3B0aW9uc21lbnVpY29uIjtzOjQ6InRydWUiO31za2luX3BhdGh8czoxMzoic2tpbnMvZGVmYXVsdCI7cXVvdGFfZGlzcGxheXxzOjU6ImltYWdlIjt1bnNlZW5fY291bnR8YTo1OntzOjU6IklOQk9YIjtpOjA7czo2OiJEcmFmdHMiO2k6MDtzOjQ6IlNlbnQiO2k6MDtzOjQ6Ikp1bmsiO2k6MDtzOjU6IlRyYXNoIjtpOjA7fWZvbGRlcnN8YTo1OntzOjU6IklOQk9YIjthOjI6e3M6MzoiY250IjtpOjE7czo2OiJtYXh1aWQiO2k6Mjt9czo2OiJEcmFmdHMiO2E6Mjp7czozOiJjbnQiO2k6MDtzOjY6Im1heHVpZCI7aTowO31zOjQ6IlNlbnQiO2E6Mjp7czozOiJjbnQiO2k6MTtzOjY6Im1heHVpZCI7aToxO31zOjQ6Ikp1bmsiO2E6Mjp7czozOiJjbnQiO2k6MDtzOjY6Im1heHVpZCI7aTowO31zOjU6IlRyYXNoIjthOjI6e3M6MzoiY250IjtpOjA7czo2OiJtYXh1aWQiO2k6MDt9fXBhZ2V8aToxO3NhZmVfbWVzc2FnZXN8YToxOntpOjI7YjowO31jb21wb3NlfGE6NTp7czoyOiJpZCI7czoyMzoiMTc0MTE0OTg2NDRkYzJjYzQ1NzJjOTUiO3M6NToicGFyYW0iO2E6NDp7czo0OiJ0YXNrIjtzOjQ6Im1haWwiO3M6NjoiYWN0aW9uIjtzOjc6ImNvbXBvc2UiO3M6NDoibWJveCI7czo1OiJJTkJPWCI7czo5OiJzZW50X21ib3giO3M6NDoiU2VudCI7fXM6NzoibWFpbGJveCI7czo1OiJJTkJPWCI7czoxMDoiZGVsZXRlaWNvbiI7czozNzoic2tpbnMvZGVmYXVsdC9pbWFnZXMvaWNvbnMvZGVsZXRlLnBuZyI7czoxMToiYXR0YWNobWVudHMiO2E6MTp7czoyMDoiMTEzMDQ2MTE5MzIwODk4MzkwMDAiO2E6NTp7czo0OiJwYXRoIjtzOjQyOiJDOlxaUGFuZWxccGFuZWxcYXBwc1x3ZWJtYWlsXHRlbXBccmNtQy50bXAiO3M6NDoic2l6ZSI7aTo4MjY7czo0OiJuYW1lIjtzOjIwOiJiYWNrdXBfenBhbmVsX1NBLnppcCI7czo4OiJtaW1ldHlwZSI7czoxNToiYXBwbGljYXRpb24vemlwIjtzOjI6ImlkIjtzOjIwOiIxMTMwNDYxMTkzMjA4OTgzOTAwMCI7fX19cGx1Z2luc3xhOjE6e3M6MjI6ImZpbGVzeXN0ZW1fYXR0YWNobWVudHMiO2E6MTp7czo5OiJ0bXBfZmlsZXMiO2E6MTp7aTowO3M6NDI6IkM6XFpQYW5lbFxwYW5lbFxhcHBzXHdlYm1haWxcdGVtcFxyY21DLnRtcCI7fX19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `mail_host` varchar(128) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_login` datetime DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  `preferences` text,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`,`mail_host`),
  KEY `alias_index` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `mail_host`, `alias`, `created`, `last_login`, `language`, `preferences`) VALUES
(1, 'zadmin@ztest.com', 'localhost', '', '2011-05-05 15:59:47', '2011-05-05 16:10:43', 'en_US', 'a:1:{s:12:"preview_pane";s:1:"0";}');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cache`
--
ALTER TABLE `cache`
  ADD CONSTRAINT `user_id_fk_cache` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contactgroups`
--
ALTER TABLE `contactgroups`
  ADD CONSTRAINT `user_id_fk_contactgroups` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `user_id_fk_contacts` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `identities`
--
ALTER TABLE `identities`
  ADD CONSTRAINT `user_id_fk_identities` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `user_id_fk_messages` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
