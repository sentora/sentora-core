-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 29, 2012 at 10:05 PM
-- Server version: 5.5.13
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zpanelx_core`
--

-- --------------------------------------------------------

--
-- Table structure for table `x_accounts`
--

CREATE TABLE IF NOT EXISTS `x_accounts` (
  `ac_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ac_user_vc` varchar(50) DEFAULT NULL,
  `ac_pass_vc` varchar(50) DEFAULT NULL,
  `ac_email_vc` varchar(250) DEFAULT NULL,
  `ac_reseller_fk` int(6) DEFAULT NULL,
  `ac_package_fk` int(6) DEFAULT NULL,
  `ac_group_fk` int(6) DEFAULT NULL,
  `ac_usertheme_vc` varchar(45) DEFAULT NULL,
  `ac_usercss_vc` varchar(45) DEFAULT NULL,
  `ac_enabled_in` int(1) DEFAULT '1',
  `ac_lastlogon_ts` int(30) DEFAULT NULL,
  `ac_notice_tx` text,
  `ac_resethash_tx` text,
  `ac_created_ts` int(30) DEFAULT NULL,
  `ac_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ac_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `x_accounts`
--

INSERT INTO `x_accounts` (`ac_id_pk`, `ac_user_vc`, `ac_pass_vc`, `ac_email_vc`, `ac_reseller_fk`, `ac_package_fk`, `ac_group_fk`, `ac_usertheme_vc`, `ac_usercss_vc`, `ac_enabled_in`, `ac_lastlogon_ts`, `ac_notice_tx`, `ac_resethash_tx`, `ac_created_ts`, `ac_deleted_ts`) VALUES
(1, 'zadmin', '5f4dcc3b5aa765d61d8327deb882cf99', 'bobbyallen.uk@gmail.com', 1, 1, 1, 'zpanelx', 'default', 1, 1330552864, 'Yo bitches!!!', 'ca583113e8ca1180e73ddf7dad64651a0a83ee9d', 1324511063, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `x_aliases`
--

CREATE TABLE IF NOT EXISTS `x_aliases` (
  `al_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `al_acc_fk` int(6) DEFAULT NULL,
  `al_address_vc` varchar(255) DEFAULT NULL,
  `al_destination_vc` varchar(255) DEFAULT NULL,
  `al_created_ts` int(30) DEFAULT NULL,
  `al_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`al_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_bandwidth`
--

CREATE TABLE IF NOT EXISTS `x_bandwidth` (
  `bd_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `bd_acc_fk` int(6) DEFAULT NULL,
  `bd_month_in` int(6) DEFAULT NULL,
  `bd_transamount_bi` bigint(20) DEFAULT NULL,
  `bd_diskamount_bi` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`bd_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_cronjobs`
--

CREATE TABLE IF NOT EXISTS `x_cronjobs` (
  `ct_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ct_acc_fk` int(6) DEFAULT NULL,
  `ct_script_vc` varchar(255) DEFAULT NULL,
  `ct_timing_vc` varchar(255) DEFAULT NULL,
  `ct_fullpath_vc` varchar(255) DEFAULT NULL,
  `ct_description_tx` text,
  `ct_created_ts` int(30) DEFAULT NULL,
  `ct_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ct_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_distlists`
--

CREATE TABLE IF NOT EXISTS `x_distlists` (
  `dl_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dl_acc_fk` int(6) DEFAULT NULL,
  `dl_address_vc` varchar(255) DEFAULT NULL,
  `dl_created_ts` int(30) DEFAULT NULL,
  `dl_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dl_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_distlistusers`
--

CREATE TABLE IF NOT EXISTS `x_distlistusers` (
  `du_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `du_distlist_fk` int(6) DEFAULT NULL,
  `du_address_vc` varchar(255) DEFAULT NULL,
  `du_created_ts` int(30) DEFAULT NULL,
  `du_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`du_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_dns`
--

CREATE TABLE IF NOT EXISTS `x_dns` (
  `dn_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dn_acc_fk` int(6) DEFAULT NULL,
  `dn_name_vc` varchar(255) DEFAULT NULL,
  `dn_vhost_fk` int(6) DEFAULT NULL,
  `dn_type_vc` varchar(50) DEFAULT NULL,
  `dn_host_vc` varchar(50) DEFAULT NULL,
  `dn_ttl_in` int(30) DEFAULT NULL,
  `dn_target_vc` varchar(50) DEFAULT NULL,
  `dn_texttarget_tx` text,
  `dn_priority_in` int(50) DEFAULT NULL,
  `dn_weight_in` int(50) DEFAULT NULL,
  `dn_port_in` int(50) DEFAULT NULL,
  `dn_created_ts` int(30) DEFAULT NULL,
  `dn_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dn_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_faqs`
--

CREATE TABLE IF NOT EXISTS `x_faqs` (
  `fq_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fq_acc_fk` int(6) DEFAULT NULL,
  `fq_question_tx` text,
  `fq_answer_tx` text,
  `fq_global_in` int(1) DEFAULT NULL,
  `fq_created_ts` int(30) DEFAULT NULL,
  `fq_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fq_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `x_faqs`
--

INSERT INTO `x_faqs` (`fq_id_pk`, `fq_acc_fk`, `fq_question_tx`, `fq_answer_tx`, `fq_global_in`, `fq_created_ts`, `fq_deleted_ts`) VALUES
(1, 1, 'How can I update my personal contact details?', 'From the control panel homepage please click on the ''My Account'' icon to enable you to update your personal details.', 1, NULL, NULL),
(2, 1, 'I need to change my password!', 'Your ZPanel and MySQL password can be easily changed using the ''Password assistant'' icon on the control panel.', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `x_forwarders`
--

CREATE TABLE IF NOT EXISTS `x_forwarders` (
  `fw_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fw_acc_fk` int(6) DEFAULT NULL,
  `fw_address_vc` varchar(255) DEFAULT NULL,
  `fw_destination_vc` varchar(255) DEFAULT NULL,
  `fw_keepmessage_in` int(1) DEFAULT '1',
  `fw_created_ts` int(30) DEFAULT NULL,
  `fw_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fw_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_ftpaccounts`
--

CREATE TABLE IF NOT EXISTS `x_ftpaccounts` (
  `ft_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ft_acc_fk` int(6) DEFAULT NULL,
  `ft_user_vc` varchar(20) DEFAULT NULL,
  `ft_directory_vc` varchar(255) DEFAULT NULL,
  `ft_access_vc` varchar(20) DEFAULT NULL,
  `ft_password_vc` varchar(50) DEFAULT NULL,
  `ft_created_ts` int(6) DEFAULT NULL,
  `ft_deleted_ts` int(6) DEFAULT NULL,
  PRIMARY KEY (`ft_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_groups`
--

CREATE TABLE IF NOT EXISTS `x_groups` (
  `ug_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ug_name_vc` varchar(20) DEFAULT NULL,
  `ug_notes_tx` text,
  `ug_reseller_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`ug_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `x_groups`
--

INSERT INTO `x_groups` (`ug_id_pk`, `ug_name_vc`, `ug_notes_tx`, `ug_reseller_fk`) VALUES
(1, 'Administrators', 'The main administration group, this group allows access to all areas of ZPanel.', 1),
(2, 'Resellers', 'Resellers have the ability to manage, create and maintain user accounts within ZPanel.', 1),
(3, 'Users', 'Users have basic access to ZPanel.', 1),
(10, 'Test Group1', 'Test desc', 1);

-- --------------------------------------------------------

--
-- Table structure for table `x_htaccess`
--

CREATE TABLE IF NOT EXISTS `x_htaccess` (
  `ht_id_pk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ht_acc_fk` int(6) DEFAULT NULL,
  `ht_user_vc` varchar(10) DEFAULT NULL,
  `ht_dir_vc` varchar(255) DEFAULT NULL,
  `ht_created_ts` int(30) DEFAULT NULL,
  `ht_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ht_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_logs`
--

CREATE TABLE IF NOT EXISTS `x_logs` (
  `lg_id_pk` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lg_user_fk` int(6) NOT NULL DEFAULT '1',
  `lg_code_vc` varchar(10) DEFAULT NULL,
  `lg_module_vc` varchar(25) DEFAULT NULL,
  `lg_detail_tx` text,
  `lg_stack_tx` text,
  `lg_when_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lg_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_mailboxes`
--

CREATE TABLE IF NOT EXISTS `x_mailboxes` (
  `mb_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mb_acc_fk` int(6) DEFAULT NULL,
  `mb_address_vc` varchar(255) DEFAULT NULL,
  `mb_enabled_in` int(1) DEFAULT '1',
  `mb_created_ts` int(30) DEFAULT NULL,
  `mb_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mb_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_modcats`
--

CREATE TABLE IF NOT EXISTS `x_modcats` (
  `mc_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mc_name_vc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mc_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `x_modcats`
--

INSERT INTO `x_modcats` (`mc_id_pk`, `mc_name_vc`) VALUES
(1, 'Account Information'),
(2, 'Server Admin'),
(3, 'Advanced'),
(4, 'Database Management'),
(5, 'Domain Management'),
(6, 'Mail'),
(7, 'Reseller'),
(8, 'File Management');

-- --------------------------------------------------------

--
-- Table structure for table `x_modules`
--

CREATE TABLE IF NOT EXISTS `x_modules` (
  `mo_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mo_category_fk` int(6) NOT NULL DEFAULT '1',
  `mo_name_vc` varchar(200) NOT NULL,
  `mo_version_in` int(10) DEFAULT NULL,
  `mo_folder_vc` varchar(255) DEFAULT NULL,
  `mo_type_en` enum('user','system','modadmin','lang') NOT NULL DEFAULT 'user',
  `mo_desc_tx` text,
  `mo_installed_ts` int(30) DEFAULT NULL,
  `mo_enabled_en` enum('true','false') NOT NULL DEFAULT 'true',
  `mo_updatever_vc` varchar(10) DEFAULT NULL,
  `mo_updateurl_tx` text,
  PRIMARY KEY (`mo_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `x_modules`
--

INSERT INTO `x_modules` (`mo_id_pk`, `mo_category_fk`, `mo_name_vc`, `mo_version_in`, `mo_folder_vc`, `mo_type_en`, `mo_desc_tx`, `mo_installed_ts`, `mo_enabled_en`, `mo_updatever_vc`, `mo_updateurl_tx`) VALUES
(1, 2, 'PHPInfo', 100, 'phpinfo', 'user', 'PHPInfo provides you with infomation regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.', 0, 'true', '', ''),
(3, 2, 'Shadowing', 100, 'shadowing', 'user', 'From here you can shadow any of your client''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', 0, 'true', '', ''),
(4, 2, 'ZPanel Config', 100, 'zpanelconfig', 'user', 'Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.', 0, 'true', '', ''),
(5, 2, 'ZPanel News', 100, 'news', 'user', 'Find out all the latest news and infomation from the ZPanel project.', 0, 'true', '', ''),
(6, 2, 'Updates', 100, 'updates', 'user', 'Check to see if there are any avaliable updates to your version of the ZPanel software.', 0, 'true', '', ''),
(8, 4, 'phpMyAdmin', 100, 'phpmyadmin', 'user', 'phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.', 0, 'true', '', ''),
(9, 1, 'My Account', 100, 'my_account', 'user', 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n', 0, 'true', '', ''),
(10, 6, 'WebMail', 100, 'webmail', 'user', 'Webmail is a convienient way for you to check your email accounts online without the need to configure an email client.', 0, 'true', '', ''),
(11, 1, 'Change Password', 100, 'password_assistant', 'user', 'Change your current control panel or MySQL password.', 0, 'true', '', ''),
(12, 3, 'Backup', 100, 'backupmgr', 'user', 'The backup manager module enables you to backup your entire hosting account including all your MySQL® databases.', 0, 'true', '', ''),
(13, 3, 'Network Tools', 100, 'nettools', 'user', 'You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.', 0, 'true', '', ''),
(14, 3, 'Service Status', 100, 'services', 'user', 'Here you can check the current status of our services and see what services are up and running and which are down and not.', 0, 'true', '198', 'http://www.ballen.co.uk/dwonload.zip'),
(15, 5, 'Domains', 100, 'domains', 'user', 'This module enables you to add or configure domain web hosting on your account.', 0, 'true', '', ''),
(16, 5, 'Parked Domains', 100, 'parked_domains', 'user', 'Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!', 0, 'true', '', ''),
(17, 5, 'Sub Domains', 100, 'sub_domains', 'user', 'This module enables you to add or configure domain web hosting on your account.', 0, 'true', '', ''),
(18, 2, 'Module Admin', 100, 'moduleadmin', 'user', 'Administer or configure modules registered with module admin', 0, 'true', '', ''),
(19, 7, 'Manage Clients', 100, 'manage_clients', 'user', 'The account manager enables you to view, update and create client accounts.', 0, 'true', '', ''),
(20, 7, 'Package Manager', 100, 'packages', 'user', 'Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.', 0, 'true', '', ''),
(22, 3, 'Cron Manager', 100, 'cron', 'user', 'Here you can configure PHP scripts to run automatically at different time intervals.', 0, 'true', '', ''),
(23, 2, 'phpSysInfo', 100, 'phpsysinfo', 'user', 'phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.', 0, 'true', '', ''),
(24, 4, 'MySQL Database', 100, 'mysql_databases', 'user', 'MySQL&reg databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg databases.', 0, 'true', '', ''),
(25, 1, 'Usage Viewer', 100, 'usage_viewer', 'user', 'The account usage screen enables you to see exactly what you are currently using on your hosting package.', 0, 'true', '', ''),
(26, 8, 'FTP Accounts', 100, 'ftp_management', 'user', 'Using this module you can create FTP accounts which will enable you and any other accounts you create have the ability to upload and manage files on your hosting space.', 0, 'true', '', ''),
(27, 3, 'FAQ''s', 100, 'faqs', 'user', 'Please find a list of the most common questons from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.', NULL, 'true', '', ''),
(28, 0, 'Apache Config', 100, 'apache_admin', 'modadmin', 'This module enables you to configure Apache Vhost settings for your hosting accounts.', 0, 'true', '', ''),
(29, 5, 'DNS Manager', 100, 'dns_manager', 'user', NULL, 0, 'true', '', ''),
(30, 0, 'DNS Config', 100, 'dns_admin', 'modadmin', 'This module enables you to configure DNS settings for the DNS Manager', NULL, 'true', '', ''),
(31, 7, 'Manage Groups', 100, 'manage_groups', 'user', 'Manage user groups to enable greater control over module permission.', 0, 'true', '', ''),
(32, 6, 'Mailboxes', 100, 'mailboxes', 'user', 'Using this module you have the ability to create IMAP and POP3 Mailboxes.', 0, 'true', '', ''),
(33, 6, 'Forwards', 100, 'forwarders', 'user', 'Using this module you have the ability to create mail forwarders.', 0, 'true', '', ''),
(34, 6, 'Distrubution Lists', 100, 'distlists', 'user', 'This module enables you to create and manage email distrubution groups.', 0, 'true', '', ''),
(35, 6, 'Aliases', 100, 'aliases', 'user', 'Using this module you have the ability to create alias mailboxes to existing accounts.', 0, 'true', '', ''),
(36, 0, 'Mail Config', 100, 'mail_admin', 'modadmin', 'This module enables you to configure your mail options', NULL, 'true', '', ''),
(39, 4, 'MySQL Users', 100, 'mysql_users', 'user', 'MySQL® Users allows you to add users and permissions to your MySQL® databases.', NULL, 'true', '', ''),
(40, 0, 'FTP Config', 100, 'ftp_admin', 'modadmin', 'This module enables you to configure FTP settings for your hosting accounts.', NULL, 'true', '', ''),
(41, 0, 'Backup Config', 100, 'backup_admin', 'modadmin', 'This module enables you to configure Backup settings for your hosting accounts.', NULL, 'true', '', ''),
(42, 7, 'Client Notice Manager', 100, 'client_notices', 'user', 'Enables resellers to set global notices for their clients.', NULL, 'true', '', ''),
(43, 3, 'Protect Directories', 100, 'htpasswd', 'user', 'This module enables you to configure .htaccess files and users to protect your web directories.', 0, 'true', '', ''),
(46, 7, 'Theme Manager', 100, 'theme_manager', 'user', 'Enables the reseller to set themes configurations for their clients.', 0, 'true', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `x_mysql`
--

CREATE TABLE IF NOT EXISTS `x_mysql` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `x_mysql_databases`
--

CREATE TABLE IF NOT EXISTS `x_mysql_databases` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `x_mysql_databases`
--

INSERT INTO `x_mysql_databases` (`my_id_pk`, `my_acc_fk`, `my_name_vc`, `my_usedspace_bi`, `my_created_ts`, `my_deleted_ts`) VALUES
(1, 1, 'zadmin_test', 0, 1330551600, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `x_mysql_dbmap`
--

CREATE TABLE IF NOT EXISTS `x_mysql_dbmap` (
  `mm_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mm_acc_fk` int(6) DEFAULT NULL,
  `mm_user_fk` int(6) DEFAULT NULL,
  `mm_database_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`mm_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `x_mysql_dbmap`
--

INSERT INTO `x_mysql_dbmap` (`mm_id_pk`, `mm_acc_fk`, `mm_user_fk`, `mm_database_fk`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `x_mysql_users`
--

CREATE TABLE IF NOT EXISTS `x_mysql_users` (
  `mu_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mu_acc_fk` int(6) DEFAULT NULL,
  `mu_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mu_database_fk` int(6) DEFAULT NULL,
  `mu_access_vc` varchar(40) DEFAULT NULL,
  `mu_pass_vc` varchar(40) DEFAULT NULL,
  `mu_created_ts` int(30) DEFAULT NULL,
  `mu_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mu_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `x_mysql_users`
--

INSERT INTO `x_mysql_users` (`mu_id_pk`, `mu_acc_fk`, `mu_name_vc`, `mu_database_fk`, `mu_access_vc`, `mu_pass_vc`, `mu_created_ts`, `mu_deleted_ts`) VALUES
(1, 1, 'kevin', 1, '%', 'zetypupu5', 1330551633, NULL),
(2, 1, 'bobby', 1, '81.77.65.190', 'bupasajyj', 1330551655, 1330551728);

-- --------------------------------------------------------

--
-- Table structure for table `x_packages`
--

CREATE TABLE IF NOT EXISTS `x_packages` (
  `pk_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pk_name_vc` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `pk_reseller_fk` int(6) DEFAULT NULL,
  `pk_enablephp_in` int(1) DEFAULT '0',
  `pk_enablecgi_in` int(1) DEFAULT '0',
  `pk_created_ts` int(30) DEFAULT NULL,
  `pk_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`pk_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `x_packages`
--

INSERT INTO `x_packages` (`pk_id_pk`, `pk_name_vc`, `pk_reseller_fk`, `pk_enablephp_in`, `pk_enablecgi_in`, `pk_created_ts`, `pk_deleted_ts`) VALUES
(1, 'Administration', 1, 1, 1, NULL, NULL),
(2, 'Demo', 1, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `x_permissions`
--

CREATE TABLE IF NOT EXISTS `x_permissions` (
  `pe_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pe_group_fk` int(6) DEFAULT NULL,
  `pe_module_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`pe_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=281 ;

--
-- Dumping data for table `x_permissions`
--

INSERT INTO `x_permissions` (`pe_id_pk`, `pe_group_fk`, `pe_module_fk`) VALUES
(221, 2, 22),
(195, 3, 10),
(179, 2, 1),
(239, 3, 22),
(238, 3, 29),
(237, 2, 29),
(236, 1, 29),
(227, 1, 25),
(220, 1, 22),
(214, 2, 17),
(181, 1, 3),
(183, 1, 4),
(185, 1, 6),
(189, 3, 8),
(184, 1, 5),
(197, 2, 11),
(194, 2, 10),
(193, 1, 10),
(246, 4, 27),
(235, 3, 27),
(245, 4, 25),
(234, 2, 27),
(244, 4, 17),
(233, 1, 27),
(213, 1, 17),
(226, 3, 24),
(219, 2, 20),
(212, 3, 16),
(196, 1, 11),
(205, 2, 14),
(204, 1, 14),
(201, 3, 12),
(243, 4, 16),
(232, 3, 26),
(218, 1, 20),
(225, 2, 24),
(217, 2, 19),
(211, 2, 16),
(200, 2, 12),
(199, 1, 12),
(242, 4, 15),
(231, 2, 26),
(224, 1, 24),
(223, 2, 23),
(216, 1, 19),
(210, 1, 16),
(203, 2, 13),
(202, 1, 13),
(198, 3, 11),
(241, 4, 11),
(230, 1, 26),
(228, 2, 25),
(222, 1, 23),
(215, 3, 17),
(209, 3, 15),
(208, 2, 15),
(206, 3, 14),
(240, 4, 9),
(229, 3, 25),
(207, 1, 15),
(192, 3, 9),
(191, 2, 9),
(190, 1, 9),
(178, 1, 1),
(180, 3, 1),
(182, 2, 3),
(187, 1, 8),
(188, 2, 8),
(186, 1, 7),
(248, 1, 30),
(249, 1, 28),
(250, 1, 2),
(251, 1, 31),
(252, 1, 32),
(253, 1, 33),
(254, 1, 34),
(255, 1, 35),
(256, 2, 35),
(257, 3, 35),
(258, 2, 34),
(259, 3, 34),
(260, 2, 33),
(261, 3, 33),
(262, 2, 32),
(263, 3, 32),
(264, 1, 36),
(265, 3, 38),
(266, 3, 37),
(267, 1, 38),
(268, 2, 38),
(269, 1, 37),
(270, 2, 37),
(271, 1, 39),
(272, 2, 39),
(273, 3, 39),
(274, 1, 41),
(275, 1, 40),
(277, 1, 18),
(278, 1, 42),
(279, 1, 43),
(280, 1, 46);

-- --------------------------------------------------------

--
-- Table structure for table `x_profiles`
--

CREATE TABLE IF NOT EXISTS `x_profiles` (
  `ud_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ud_user_fk` int(6) DEFAULT NULL,
  `ud_fullname_vc` varchar(100) DEFAULT NULL,
  `ud_email_vc` varchar(255) DEFAULT NULL,
  `ud_language_vc` varchar(10) DEFAULT 'en',
  `ud_group_fk` int(6) DEFAULT NULL,
  `ud_package_fk` int(6) DEFAULT NULL,
  `ud_address_tx` text,
  `ud_postcode_vc` varchar(20) DEFAULT NULL,
  `ud_phone_vc` varchar(20) DEFAULT NULL,
  `ud_created_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ud_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `x_profiles`
--

INSERT INTO `x_profiles` (`ud_id_pk`, `ud_user_fk`, `ud_fullname_vc`, `ud_email_vc`, `ud_language_vc`, `ud_group_fk`, `ud_package_fk`, `ud_address_tx`, `ud_postcode_vc`, `ud_phone_vc`, `ud_created_ts`) VALUES
(1, 1, 'Test user 1', NULL, 'en', 1, 1, '1 Example house,\r\nTest street,\r\nSimple town,\r\nTestshire', 'C011 7RT', '+44(1206) 457169', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `x_quotas`
--

CREATE TABLE IF NOT EXISTS `x_quotas` (
  `qt_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `qt_package_fk` int(6) DEFAULT NULL,
  `qt_domains_in` int(6) DEFAULT '0',
  `qt_subdomains_in` int(6) DEFAULT '0',
  `qt_parkeddomains_in` int(6) DEFAULT '0',
  `qt_mailboxes_in` int(6) DEFAULT '0',
  `qt_fowarders_in` int(6) DEFAULT '0',
  `qt_distlists_in` int(6) DEFAULT '0',
  `qt_ftpaccounts_in` int(6) DEFAULT '0',
  `qt_mysql_in` int(6) DEFAULT '0',
  `qt_diskspace_bi` bigint(20) DEFAULT '0',
  `qt_bandwidth_bi` bigint(20) DEFAULT '0',
  `qt_bwenabled_in` int(1) DEFAULT '0',
  `qt_dlenabled_in` int(1) DEFAULT '0',
  `qt_totalbw_fk` int(30) DEFAULT NULL,
  `qt_minbw_fk` int(30) DEFAULT NULL,
  `qt_maxcon_fk` int(30) DEFAULT NULL,
  `qt_filesize_fk` int(30) DEFAULT NULL,
  `qt_filespeed_fk` int(30) DEFAULT NULL,
  `qt_filetype_vc` varchar(30) NOT NULL DEFAULT '*',
  `qt_modified_in` int(1) DEFAULT '0',
  PRIMARY KEY (`qt_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `x_quotas`
--

INSERT INTO `x_quotas` (`qt_id_pk`, `qt_package_fk`, `qt_domains_in`, `qt_subdomains_in`, `qt_parkeddomains_in`, `qt_mailboxes_in`, `qt_fowarders_in`, `qt_distlists_in`, `qt_ftpaccounts_in`, `qt_mysql_in`, `qt_diskspace_bi`, `qt_bandwidth_bi`, `qt_bwenabled_in`, `qt_dlenabled_in`, `qt_totalbw_fk`, `qt_minbw_fk`, `qt_maxcon_fk`, `qt_filesize_fk`, `qt_filespeed_fk`, `qt_filetype_vc`, `qt_modified_in`) VALUES
(1, 1, 5, 10, 5, 10, 100, 5, 10, 10, 2048000000, 10240000000, 0, 0, NULL, NULL, NULL, NULL, NULL, '*', 1);

-- --------------------------------------------------------

--
-- Table structure for table `x_settings`
--

CREATE TABLE IF NOT EXISTS `x_settings` (
  `so_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `so_name_vc` varchar(50) DEFAULT NULL,
  `so_cleanname_vc` varchar(50) DEFAULT NULL,
  `so_value_tx` text,
  `so_defvalues_tx` text,
  `so_desc_tx` text,
  `so_module_vc` varchar(50) DEFAULT NULL,
  `so_usereditable_en` enum('true','false') DEFAULT 'false',
  PRIMARY KEY (`so_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=112 ;

--
-- Dumping data for table `x_settings`
--

INSERT INTO `x_settings` (`so_id_pk`, `so_name_vc`, `so_cleanname_vc`, `so_value_tx`, `so_defvalues_tx`, `so_desc_tx`, `so_module_vc`, `so_usereditable_en`) VALUES
(6, 'dbversion', NULL, '10.0.0.0', NULL, 'Database Version', 'ZPanel Config', 'false'),
(7, 'zpanel_root', 'ZPanel Root', 'C:/zpanel/panel/', NULL, 'Zpanel Web Root', 'ZPanel Config', 'true'),
(8, 'module_icons_pr', 'Icons per Row', '8', NULL, 'Set the number of icons to display before beginning a new line.', 'ZPanel Config', 'true'),
(9, 'zpanel_template', NULL, '', NULL, 'Template', 'ZPanel Config', 'false'),
(10, 'zpanel_df', 'Date Format', 'H:i jS M Y T', NULL, 'Set the date format used by modules.', 'ZPanel Config', 'true'),
(11, 'server_email', 'Server Email', '', NULL, 'Server Email', 'ZPanel Config', 'true'),
(13, 'servicechk_to', 'Service Check Timeout', '2', NULL, 'Service Check Timeout', 'ZPanel Config', 'true'),
(14, 'root_drive', 'Root Drive', '/', NULL, 'The root drive where ZPanel is installed.', 'ZPanel Config', 'true'),
(16, 'php_exer', NULL, 'php', NULL, 'PHP Executable', 'ZPanel Config', 'false'),
(17, 'temp_dir', 'Temp Directory', 'C:/windows/temp/', NULL, 'Global temp directory.', 'ZPanel Config', 'true'),
(18, 'news_url', '', 'http://api.zpanelcp.com/api/news.php', NULL, 'Zpanel News URL', 'ZPanel Config', 'false'),
(19, 'update_url', '', 'http://www.ballen.co.uk/zpxapi/latestversion/', NULL, 'Zpanel Update URL', 'ZPanel Config', 'false'),
(20, 'bugreport_url', '', 'http://api.zpanelcp.com/api/bugapi.php', NULL, 'Bug report URL', 'ZPanel Config', 'false'),
(21, 'server_ip', 'Server IP Address', '66.150.164.73', NULL, 'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.', 'ZPanel Config', 'true'),
(22, 'zip_exe', 'ZIP Exe', 'C:/zpanel/bin/7zip/7za.exe', NULL, 'Path to the ZIP Executable', 'ZPanel Config', 'true'),
(24, 'disable_hostsen', '', 'false', 'true|false', 'Disable Host Entries', 'ZPanel Config', 'false'),
(25, 'latestzpversion', '', '10.0.0.2', NULL, 'This is used for caching the latest version of ZPanel.', 'ZPanel Config', 'false'),
(26, 'logmode', NULL, 'file', 'db|file|email', 'The default mode to log all errors in.', 'ZPanel Config', 'false'),
(27, 'logfile', NULL, 'log.txt', NULL, 'If loggging is set to ''file'' mode this is the path to the log file that is to be used by ZPanel.', 'ZPanel Config', 'false'),
(28, 'apikey', NULL, 'ee8795c8c53bfdb3b2cc595186b68912', NULL, 'The secret API key for the server.', 'ZPanel Config', 'false'),
(29, 'email_from_address', 'From Address', 'dev@zpanelcp.com', NULL, 'The email address to appear in the From field of emails sent by ZPanel.', 'ZPanel Config', 'true'),
(30, 'email_from_name', 'From Name', 'Control Panel', NULL, 'The name to appear in the From field of emails sent by ZPanel.', 'ZPanel Config', 'true'),
(31, 'email_smtp', 'Use SMTP', 'true', 'true|false', 'Use SMTP server to end emails from. (true/false)', 'ZPanel Config', 'true'),
(32, 'smtp_auth', 'Use AUTH', 'true', 'true|false', 'SMTP requires authentication. (true/false)', 'ZPanel Config', 'true'),
(33, 'smtp_server', 'SMTP Server', 'smtp.gmail.com', NULL, 'The address of the SMTP server.', 'ZPanel Config', 'true'),
(34, 'smtp_port', 'SMTP Port', '465', NULL, 'The port address of the SMTP server (usually 25)', 'ZPanel Config', 'true'),
(35, 'smtp_username', 'SMTP User', 'bobbyallen.uk@gmail.com', NULL, 'Username for authentication on the SMTP server.', 'ZPanel Config', 'true'),
(36, 'smtp_password', 'SMTP Pass', '___', NULL, 'Password for authentication on the SMTP server.', 'ZPanel Config', 'true'),
(37, 'smtp_secure', 'SMTP Auth method', 'ssl', 'false|ssl|tls', 'If specified will attempt to use encryption to connect to the server, if ''false'' this is disabled. Avaliable options: false, ssl, tls', 'ZPanel Config', 'true'),
(38, 'daemon_lastrun', NULL, '1330551864', NULL, 'Timestamp of when the daemon last ran.', NULL, 'false'),
(39, 'daemon_dayrun', NULL, '1330551864', NULL, NULL, NULL, 'false'),
(40, 'daemon_weekrun', NULL, '1330551864', NULL, NULL, NULL, 'false'),
(41, 'daemon_monthrun', NULL, '1328908442', NULL, NULL, NULL, 'false'),
(42, 'purge_bu', 'Purge Backups', 'true', 'true|false', 'Delete client backups after alloted time has elapsed to help save diskspace (true/false)', 'Backup Config', 'true'),
(43, 'purge_date', 'Purge Date', '30', NULL, 'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run', 'Backup Config', 'true'),
(44, 'disk_bu', 'Disk Backups', 'true', 'true|false', 'Allow users to create and save backups of their home directories to disk. (true/false)', 'Backup Config', 'true'),
(45, 'schedule_bu', 'Daily Backups', 'true', 'true|false', 'Maks a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)', 'Backup Config', 'true'),
(46, 'ftp_db', 'FTP Database', 'zpanel_proftp', NULL, 'The name of the ftp server database', 'FTP Config', 'true'),
(47, 'ftp_php', 'FTP PHP', 'filezilla.phps', NULL, 'Name of PHP to include when adding FTP data.', 'FTP Config', 'true'),
(48, 'ftp_service', 'FTP Service Name', 'FileZilla server.exe', NULL, 'The name of the FTP service', 'FTP Config', 'true'),
(49, 'ftp_service_root', 'FTP Service Root', 'C:/zpanel/bin/filezilla/', NULL, 'The path to the service executable if applicable.', 'FTP Config', 'true'),
(50, 'ftp_config_file', 'FTP Config File', 'C:/ZPanel/bin/filezilla/FileZilla Server.xml', NULL, 'The path to the configuration file if applicable.', 'FTP Config', 'true'),
(51, 'mailserver_db', 'Mailserver Database', 'zpanel_hmail', NULL, 'The name of the mail server database', 'Mail Config', 'true'),
(52, 'hmailserver_et', 'Hmail Encryption Type', '2', NULL, 'Type of encryption uses for hMailServer passwords', 'Mail Config', 'false'),
(53, 'max_mail_size', 'Max Mailbox Size', '200', NULL, 'Maximum size in megabytes allowed for mailboxes. Default = 200', 'Mail Config', 'true'),
(54, 'mailserver_php', 'Mailserver PHP', 'hmail.php', NULL, 'Name of PHP to include when adding mailbox data.', 'Mail Config', 'true'),
(55, 'remove_orphan', 'Remove Orphans', 'true', 'true|false', 'When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)', 'Mail Config', 'true'),
(56, 'named_dir', 'Named Directory', 'C:/zpanel/configs/bind/etc/', NULL, 'Path to the directory where named.conf is stored', 'DNS Config', 'true'),
(57, 'named_conf', 'Named Config', 'named.conf', NULL, 'Named configuration file', 'DNS Config', 'true'),
(58, 'zone_dir', 'Zone Directory', 'C:/zpanel/configs/bind/zones/', NULL, 'Path to where DNS zone files are stored', 'DNS Config', 'true'),
(59, 'refresh_ttl', 'SOA Refesh TTL', '21600', NULL, 'Global refresh TTL.  Default = 21600 (6 hours)', 'DNS Config', 'true'),
(60, 'retry_ttl', 'SOA Retry TTL', '3600', NULL, 'Global retry TTL. Default = 3600 (1 hour)', 'DNS Config', 'true'),
(61, 'expire_ttl', 'SOA Expire TTL', '604800', NULL, 'Global expire TTL. Default = 604800 (1 week)', 'DNS Config', 'true'),
(62, 'minimum_ttl', 'SOA Minimum TTL', '86400', NULL, 'Global minimum TTL. Default = 86400 (1 day)', 'DNS Config', 'true'),
(63, 'custom_ip', 'Allow Custom IP', 'true', 'true|false', 'Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config', 'DNS Config', 'true'),
(64, 'bind_dir', 'Path to BIND Root', 'C:/zpanel/bin/bind/', NULL, 'Path to the root directory where BIND is installed.', 'DNS Config', 'true'),
(65, 'bind_service', 'BIND Service Name', 'named', NULL, 'Name of the BIND service', 'DNS Config', 'true'),
(66, 'allow_xfer', 'Allow Zone Transfers', 'all', NULL, 'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all', 'DNS Config', 'true'),
(67, 'allowed_types', 'Allowed Record Types', 'A AAAA CNAME MX TXT SRV SPF NS', NULL, 'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS', 'DNS Config', 'true'),
(68, 'bind_log', 'Bind Log', 'C:/zpanel/bin/bind/bind.log', NULL, 'Path and name of the Bind Log', 'DNS Config', 'true'),
(69, 'hosted_dir', 'Vhosts Directory', 'C:/zpanel/hostdata/', NULL, 'Virtual host directory', 'Apache Config', 'true'),
(70, 'disable_hostsen', 'Disable HOSTS file entries', 'false', 'true|false', 'Disable host entries', 'Apache Config', 'true'),
(71, 'apache_vhost', 'Apache VHOST Conf', 'C:/zpanel/configs/apache/httpd-vhosts.conf', NULL, 'The full system patch and filename of the Apache VHOST configuration name.', 'Apache Config', 'true'),
(72, 'php_handler', 'PHP Handler', 'AddType application/x-httpd-php .php3', NULL, 'The PHP Handler.', 'Apache Config', 'false'),
(73, 'cgi_handler', 'CGI Handler', 'ScriptAlias /cgi-bin/ "/_cgi-bin/"\r\n<location /cgi-bin>\r\nAddHandler cgi-script .cgi .pl\r\nOptions ExecCGI -Indexes\r\n</location>', NULL, 'The CGI Handler.', 'Apache Config', 'false'),
(74, 'global_vhcustom', 'Global VHost Entry', NULL, NULL, 'Extra directives for all apache vhost''s.', 'Apache Config', 'true'),
(75, 'static_dir', 'Static Pages Directory', 'C:/zpanel/panel/etc/static/', NULL, 'The ZPanel static directory, used for storing welcome pages etc. etc.', 'Apache Config', 'true'),
(76, 'parking_path', 'Vhost Parking Path', 'C:/zpanel/panel/etc/static/parking/', NULL, 'The path to the parking website, this will be used by all clients.', 'Apache Config', 'true'),
(78, 'shared_domains', 'Shared Domains', 'no-ip,dydns', NULL, 'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dydns,test', 'Apache Config', 'true'),
(79, 'upload_temp_dir', 'Upload Temp Directory', 'C:/zpanel/panel/etc/tmp/', NULL, 'The path to the Apache Upload directory (with trailing slash)', 'Apache Config', 'true'),
(80, 'apache_port', 'Apache Port', '80', NULL, 'Apache service port', 'Apache Config', 'true'),
(81, 'dir_index', 'Directory Indexes', 'DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm', NULL, 'Directory Index', 'Apache Config', 'true'),
(82, 'suhosin_value', 'Suhosin Value', 'php_admin_value suhosin.executor.func.blacklist "passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg"', NULL, 'Suhosin configuration for virtual host  blacklisting commands', 'Apache Config', 'true'),
(83, 'openbase_seperator', 'Open Base Seperator', ';', NULL, 'Seperator flag used in open_base_directory setting', 'Apache Config', 'false'),
(84, 'openbase_temp', 'Open Base Temp Directory', 'c:/windows/temp', NULL, 'Temp directory used in open_base_directory setting', 'Apache Config', 'true'),
(85, 'access_log_format', 'Access Log Format', 'combined', 'combined|common', 'Log format for the Apache access log', 'Apache Config', 'true'),
(86, 'bandwidth_log_format', 'Bandwidth Log Format', 'common', 'combined|common', 'Log format for the Apache bandwidth log', 'Apache Config', 'true'),
(87, 'global_zpcustom', 'Global ZPanel Entry', NULL, NULL, 'Extra directives for Zpanel default vhost.', 'Apache Config', 'true'),
(88, 'use_openbase', 'Use Open Base Dir', 'true', 'true|false', 'Enable openbase directory for all vhosts', 'Apache Config', 'true'),
(89, 'use_suhosin', 'Use Suhosin', 'true', 'true|false', 'Enable Suhosin for all vhosts', 'Apache Config', 'true'),
(90, 'zpanel_domain', 'ZPanel Domain', 'zpanel.ztest.com', NULL, 'Domain that the control panel is installed under.', 'ZPanel Config', 'false'),
(91, 'log_dir', 'Log Directory', 'C:/zpanel/logs/', NULL, 'Root path to directory log folders', 'ZPanel Config', 'true'),
(92, 'apache_changed', 'Apache Changed', '1330551855', 'true|false', 'If set, Apache Config daemon hook will write the vhost config file changes.', 'Apache Config', 'false'),
(94, 'apache_allow_disabled', 'Allow Disabled', 'true', 'true|false', 'Allow webhosts to remain active even if a user has been disabled.', 'Apache Config', 'true'),
(95, 'apache_budir', 'VHost Backup Dir', 'C:/zpanel/backups/apachebackups/', NULL, 'Directory that vhost.conf backups are stored.', 'Apache Config', 'true'),
(96, 'apache_purgebu', 'Purge Backups', 'true', 'true|false', 'Old backups are deleted after the date set in Puge Date', 'Apache Config', 'true'),
(97, 'apache_purge_date', 'Purge Date', '7', NULL, 'Time in days that vhost backups are safe from deletion', 'Apache Config', 'true'),
(98, 'apache_backup', 'VHost Backup', 'true', 'true|false', 'Backup vhost file before a new one is written', 'Apache Config', 'true'),
(99, 'zsudo', 'zsudo path', 'C:/zpanel/panel/bin/zsudo', NULL, 'Path to the zsudo binary used by Apache to run system commands.', 'ZPanel Config', 'true'),
(100, 'apache_restart', 'Apache Restart Cmd', '-k restart -n "Apache"', NULL, 'Command line arguements used after the restart service request when reloading Apache.', 'Apache Config', 'true'),
(101, 'httpd_exe', 'Apache Binary', 'C:/ZPanel/bin/apache/bin/httpd.exe', NULL, 'Path to the Apache binary', 'Apache Config', 'true'),
(102, 'apache_sn', 'Apache Service Name', 'httpd', NULL, 'Service name used to handle Apache service control', 'Apache Config', 'true'),
(103, 'daemon_exer', NULL, 'C:/zpanel/panel/bin/daemon.php', NULL, 'Path to the ZPanel daemon', 'ZPanel Config', 'false'),
(104, 'daemon_timing', NULL, '0 * * * *', NULL, 'Cron time for when to run the ZPanel daemon', 'ZPanel Config', 'false'),
(105, 'cron_file', 'Cron File', 'c:/windows/system32/crontab', NULL, 'Path to the user cr0n file', 'Cron Config', 'true'),
(106, 'htpasswd_exe', 'htpassword Exe', 'htpasswd', NULL, 'Path to htpasswd.exe for potecting directories with .htaccess', 'Apache Config', 'false'),
(107, 'mysqldump_exe', 'MySQL Dump', 'C:/ZPanel/bin/mysql/bin/mysqldump.exe', NULL, 'Path to MySQL dump', 'ZPanel Config', 'false'),
(108, 'named_checkconf', 'Named CheckConfig', 'C:/ZPanel/bin/bind/bin/named-checkconf.exe', NULL, 'Path to named-checkconf bind utility.', 'DNS Config', 'true'),
(109, 'named_checkzone', 'Named CheckZone', 'C:/ZPanel/bin/bind/bin/named-checkzone.exe', NULL, 'Path to named-checkzone bind utility.', 'DNS Config', 'true'),
(110, 'named_compilezone', 'Named CompileZone', 'C:/ZPanel/bin/bind/bin/named-compilezone.exe', NULL, '	Path to named-compilezone bind utility.', 'DNS Config', 'true'),
(111, 'dns_hasupdates', 'DNS Updated', NULL, NULL, NULL, NULL, 'false');

-- --------------------------------------------------------

--
-- Table structure for table `x_translations`
--

CREATE TABLE IF NOT EXISTS `x_translations` (
  `tr_id_pk` int(11) NOT NULL AUTO_INCREMENT,
  `tr_en_tx` text,
  `tr_de_tx` text,
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

--
-- Dumping data for table `x_translations`
--

INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_de_tx`) VALUES
(44, 'Webmail is a convienient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg fÃ¼r Sie, Ihre E-Mail-Konten online zu Ã¼berprÃ¼fen, ohne dass eine E-Mail-Client zu konfigurieren.'),
(45, 'Launch Webmail', 'Starten Sie WebMail'),
(56, 'PHPInfo provides you with infomation regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.', 'PHPInfo bietet Ihnen Informationen über die PHP-Version auf dem System, sowie PHP installiert extentsions und Konfigurationsmöglichkeiten.'),
(67, 'From here you can shadow any of your client''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', 'Von hier aus können alle Ihre Kunden-Accounts können Schatten, ermöglicht Ihnen dies automatisch, wenn der Benutzer mit dem Sie remote helfen zu sehen, was sie sehen, anbieten zu können login!'),
(68, 'My Account', 'Meine Konto'),
(69, 'Change Password', 'Kennwort ändern'),
(70, 'Shadowing', 'Schatten'),
(71, 'ZPanel Config', 'Config ZPanel'),
(72, 'ZPanel News', 'ZPanel Aktuelles'),
(73, 'Updates', 'Aktualisierung'),
(74, 'Report Bug', 'Fehler melden'),
(75, 'Account', 'Konto'),
(76, 'Module Admin', 'Modul Admin'),
(77, 'Backup', 'Sicherungskopie'),
(78, 'Network Tools', 'Netzwerk-Tools'),
(79, 'Service Status', 'Service Status'),
(80, 'PHPInfo', 'PHPInfo'),
(81, 'phpMyAdmin', 'phpMyAdmin'),
(82, 'Domains', 'Domains'),
(83, 'Sub Domains', 'Sub Domains'),
(84, 'Parked Domains', 'geparkte Domains'),
(85, 'Manage Clients', 'Verwalten Kunden'),
(86, 'Package Manager', 'Paket Manager'),
(87, 'Server', 'Server'),
(88, 'Database', 'Datenbank'),
(89, 'Advanced', 'Fortgeschritten'),
(90, 'Mail', 'Post'),
(91, 'Reseller', 'Wiederverkäufer'),
(92, 'Account Information', 'Account Informationen'),
(93, 'Server Admin', 'Server Admin'),
(94, 'Database Management', 'Datenbank Verwalten'),
(95, 'Domain Management', 'Verwalten von Domains'),
(96, 'Find out all the latest news and infomation from the ZPanel project.', 'Finden Sie heraus, alle Neuigkeiten und Informationen aus dem ZPanel Projekt.'),
(97, 'Check to see if there are any avaliable updates to your version of the ZPanel software.', 'Prüfen Sie, ob es irgendwelche verfügbaren Aktualisierungen für Ihre Version des ZPanel Software.'),
(98, 'If you have found a bug with ZPanel you can report it here.', 'Did you mean: If you have found a bug with CPanel you can report it here.\r\nWenn Sie einen Fehler mit ZPanel gefunden haben, können Sie ihn hier melden.'),
(99, 'phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.', 'phpMyAdmin ist ein webbasiertes Tool, das Sie zu Ihrem ZPanel MySQL-Datenbanken via verwalten können. im Internet.'),
(100, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.'),
(101, 'Webmail is a convienient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg für Sie, Ihre E-Mail-Konten online zu überprüfen, ohne dass eine E-Mail-Client zu konfigurieren.'),
(102, 'Change your current control panel or MySQL password.', 'Ändern Sie Ihre aktuelle Bedienfeld oder MySQL-Kennwort.'),
(103, 'The backup manager module enables you to backup your entire hosting account including all your MySQL® databases.', 'Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL ® Datenbank-Backup.'),
(104, 'You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.', 'Sie können die folgenden Tools verwenden, um Probleme zu diagnostizieren oder einfach testen Verbindung mit anderen Servern oder Websites rund um den Globus.'),
(105, 'Here you can check the current status of our services and see what services are up and running and which are down and not.', 'Hier können Sie den aktuellen Status unserer Dienstleistungen und sehen, welche Dienste vorhanden sind und laufen, und die nach unten und es nicht sind.'),
(106, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.'),
(107, 'Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!', 'Domain-Parking bezieht sich auf die Registrierung von Internet Domain-Namen ohne diese Domäne verwendet, um Dienste wie E-Mail oder eine Webseite bereitzustellen. Wenn Sie alle Domains, die Sie nicht haben, dann einfach parken sie!'),
(108, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.'),
(109, 'Administer or configure modules registered with module admin', 'Verwalten oder zu konfigurieren Module mit Modul admin registriert'),
(110, 'The account manager enables you to view, update and create client accounts.', 'Die Account-Manager ermöglicht es Ihnen, anzuzeigen, zu aktualisieren und erstellen Kundenkonten.'),
(111, 'Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.', 'Willkommen auf der Paket-Manager, mit diesem Modul ermöglicht Ihnen die Erstellung und Verwaltung von bestehenden Reseller-Pakete auf Ihrem ZPanel Hosting-Account.'),
(112, 'Gives you access to your files with drag-and-drop, multiple file uploading, text editing, zip support.', 'Ermöglicht den Zugriff auf Ihre Dateien mit Drag-and-drop, multiple Datei-Upload, Textbearbeitung, zip unterstützen.'),
(113, 'Secure FTP Applet is a JAVA based FTP client component that runs within your web browser. It is designed to let non-technical users exchange data secureiy with an FTP server.', 'Secure FTP Applet ist eine Java-basierte FTP-Client-Komponente, die in Ihrem Web-Browser läuft. Es wurde entwickelt, um nicht-technische Anwender den Datenaustausch secureiy lassen mit einem FTP-Server.'),
(114, 'Full name', 'Vollständiger Name'),
(115, 'Email Address', 'E-Mail Adresse'),
(116, 'Phone Number', 'Telefonnummer'),
(117, 'Choose Language', 'Sprache wählen'),
(118, 'Postal Address', 'Postanschrift'),
(119, 'Postal Code', 'Postleitzahl'),
(120, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.'),
(121, 'Changes to your account settings have been saved successfully!', 'Änderungen an Ihrem Konto-Einstellungen wurden erfolgreich gespeichert!'),
(122, 'Update Account', 'Aktualisierung Konto'),
(123, 'Enter your account details', 'Geben Sie Ihre Kontodaten');

-- --------------------------------------------------------

--
-- Table structure for table `x_vhosts`
--

CREATE TABLE IF NOT EXISTS `x_vhosts` (
  `vh_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `vh_acc_fk` int(6) DEFAULT NULL,
  `vh_name_vc` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vh_directory_vc` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vh_type_in` int(1) DEFAULT '1',
  `vh_active_in` int(1) DEFAULT '0',
  `vh_suhosin_in` int(1) DEFAULT '1',
  `vh_obasedir_in` int(1) DEFAULT '1',
  `vh_custom_tx` text,
  `vh_enabled_in` int(1) DEFAULT '1',
  `vh_created_ts` int(30) DEFAULT NULL,
  `vh_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`vh_id_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `x_vhosts`
--

INSERT INTO `x_vhosts` (`vh_id_pk`, `vh_acc_fk`, `vh_name_vc`, `vh_directory_vc`, `vh_type_in`, `vh_active_in`, `vh_suhosin_in`, `vh_obasedir_in`, `vh_custom_tx`, `vh_enabled_in`, `vh_created_ts`, `vh_deleted_ts`) VALUES
(1, 1, 'ipswerve.com', '/ipswerve_com', 1, 1, 1, 1, NULL, 1, 1330551785, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
