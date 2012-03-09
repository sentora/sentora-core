/*
ZPanelX Database Schema
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `zpanel_core`;

/*Table structure for table `x_accounts` */

DROP TABLE IF EXISTS `x_accounts`;

CREATE TABLE `x_accounts` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_accounts` */

insert  into `x_accounts`(`ac_id_pk`,`ac_user_vc`,`ac_pass_vc`,`ac_email_vc`,`ac_reseller_fk`,`ac_package_fk`,`ac_group_fk`,`ac_usertheme_vc`,`ac_usercss_vc`,`ac_enabled_in`,`ac_lastlogon_ts`,`ac_notice_tx`,`ac_resethash_tx`,`ac_created_ts`,`ac_deleted_ts`) values (1,'zadmin','5f4dcc3b5aa765d61d8327deb882cf99','zadmin@localhost',1,1,1,'zpanelx','default',1,0,'Welcome to your new ZPanel installation! You can remove this message from the Client Notice Manager module. This module allows you to notify your clients of service outages upgrades and new features etc :-)','',NULL,NULL);

/*Table structure for table `x_aliases` */

DROP TABLE IF EXISTS `x_aliases`;

CREATE TABLE `x_aliases` (
  `al_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `al_acc_fk` int(6) DEFAULT NULL,
  `al_address_vc` varchar(255) DEFAULT NULL,
  `al_destination_vc` varchar(255) DEFAULT NULL,
  `al_created_ts` int(30) DEFAULT NULL,
  `al_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`al_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_aliases` */

/*Table structure for table `x_bandwidth` */

DROP TABLE IF EXISTS `x_bandwidth`;

CREATE TABLE `x_bandwidth` (
  `bd_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `bd_acc_fk` int(6) DEFAULT NULL,
  `bd_month_in` int(6) DEFAULT NULL,
  `bd_transamount_bi` bigint(20) DEFAULT NULL,
  `bd_diskamount_bi` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`bd_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_bandwidth` */

/*Table structure for table `x_cronjobs` */

DROP TABLE IF EXISTS `x_cronjobs`;

CREATE TABLE `x_cronjobs` (
  `ct_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ct_acc_fk` int(6) DEFAULT NULL,
  `ct_script_vc` varchar(255) DEFAULT NULL,
  `ct_timing_vc` varchar(255) DEFAULT NULL,
  `ct_fullpath_vc` varchar(255) DEFAULT NULL,
  `ct_description_tx` text,
  `ct_created_ts` int(30) DEFAULT NULL,
  `ct_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ct_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_cronjobs` */

/*Table structure for table `x_distlists` */

DROP TABLE IF EXISTS `x_distlists`;

CREATE TABLE `x_distlists` (
  `dl_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dl_acc_fk` int(6) DEFAULT NULL,
  `dl_address_vc` varchar(255) DEFAULT NULL,
  `dl_created_ts` int(30) DEFAULT NULL,
  `dl_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dl_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_distlists` */

/*Table structure for table `x_distlistusers` */

DROP TABLE IF EXISTS `x_distlistusers`;

CREATE TABLE `x_distlistusers` (
  `du_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `du_distlist_fk` int(6) DEFAULT NULL,
  `du_address_vc` varchar(255) DEFAULT NULL,
  `du_created_ts` int(30) DEFAULT NULL,
  `du_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`du_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_distlistusers` */

/*Table structure for table `x_dns` */

DROP TABLE IF EXISTS `x_dns`;

CREATE TABLE `x_dns` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_dns` */

/*Table structure for table `x_faqs` */

DROP TABLE IF EXISTS `x_faqs`;

CREATE TABLE `x_faqs` (
  `fq_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fq_acc_fk` int(6) DEFAULT NULL,
  `fq_question_tx` text,
  `fq_answer_tx` text,
  `fq_global_in` int(1) DEFAULT NULL,
  `fq_created_ts` int(30) DEFAULT NULL,
  `fq_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fq_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `x_faqs` */

insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (1,1,'How can I update my personal contact details?','From the control panel homepage please click on the \'My Account\' icon to enable you to update your personal details.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (2,1,'I need to change my password!','Your ZPanel and MySQL password can be easily changed using the \'Password assistant\' icon on the control panel.',1,NULL,NULL);

/*Table structure for table `x_forwarders` */

DROP TABLE IF EXISTS `x_forwarders`;

CREATE TABLE `x_forwarders` (
  `fw_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fw_acc_fk` int(6) DEFAULT NULL,
  `fw_address_vc` varchar(255) DEFAULT NULL,
  `fw_destination_vc` varchar(255) DEFAULT NULL,
  `fw_keepmessage_in` int(1) DEFAULT '1',
  `fw_created_ts` int(30) DEFAULT NULL,
  `fw_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fw_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_forwarders` */

/*Table structure for table `x_ftpaccounts` */

DROP TABLE IF EXISTS `x_ftpaccounts`;

CREATE TABLE `x_ftpaccounts` (
  `ft_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ft_acc_fk` int(6) DEFAULT NULL,
  `ft_user_vc` varchar(20) DEFAULT NULL,
  `ft_directory_vc` varchar(255) DEFAULT NULL,
  `ft_access_vc` varchar(20) DEFAULT NULL,
  `ft_password_vc` varchar(50) DEFAULT NULL,
  `ft_created_ts` int(6) DEFAULT NULL,
  `ft_deleted_ts` int(6) DEFAULT NULL,
  PRIMARY KEY (`ft_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_ftpaccounts` */

/*Table structure for table `x_groups` */

DROP TABLE IF EXISTS `x_groups`;

CREATE TABLE `x_groups` (
  `ug_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ug_name_vc` varchar(20) DEFAULT NULL,
  `ug_notes_tx` text,
  `ug_reseller_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`ug_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `x_groups` */

insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (1,'Administrators','The main administration group, this group allows access to all areas of ZPanel.',1);
insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (2,'Resellers','Resellers have the ability to manage, create and maintain user accounts within ZPanel.',1);
insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (3,'Users','Users have basic access to ZPanel.',1);

/*Table structure for table `x_htaccess` */

DROP TABLE IF EXISTS `x_htaccess`;

CREATE TABLE `x_htaccess` (
  `ht_id_pk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ht_acc_fk` int(6) DEFAULT NULL,
  `ht_user_vc` varchar(10) DEFAULT NULL,
  `ht_dir_vc` varchar(255) DEFAULT NULL,
  `ht_created_ts` int(30) DEFAULT NULL,
  `ht_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ht_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_htaccess` */

/*Table structure for table `x_logs` */

DROP TABLE IF EXISTS `x_logs`;

CREATE TABLE `x_logs` (
  `lg_id_pk` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lg_user_fk` int(6) NOT NULL DEFAULT '1',
  `lg_code_vc` varchar(10) DEFAULT NULL,
  `lg_module_vc` varchar(25) DEFAULT NULL,
  `lg_detail_tx` text,
  `lg_stack_tx` text,
  `lg_when_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lg_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_logs` */

/*Table structure for table `x_mailboxes` */

DROP TABLE IF EXISTS `x_mailboxes`;

CREATE TABLE `x_mailboxes` (
  `mb_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mb_acc_fk` int(6) DEFAULT NULL,
  `mb_address_vc` varchar(255) DEFAULT NULL,
  `mb_enabled_in` int(1) DEFAULT '1',
  `mb_created_ts` int(30) DEFAULT NULL,
  `mb_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mb_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_mailboxes` */

/*Table structure for table `x_modcats` */

DROP TABLE IF EXISTS `x_modcats`;

CREATE TABLE `x_modcats` (
  `mc_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mc_name_vc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mc_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `x_modcats` */

insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (1,'Account Information');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (2,'Server Admin');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (3,'Advanced');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (4,'Database Management');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (5,'Domain Management');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (6,'Mail');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (7,'Reseller');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (8,'File Management');

/*Table structure for table `x_modules` */

DROP TABLE IF EXISTS `x_modules`;

CREATE TABLE `x_modules` (
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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

/*Data for the table `x_modules` */

insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (1,2,'PHPInfo',100,'phpinfo','user','PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (3,2,'Shadowing',100,'shadowing','user','From here you can shadow any of your client\'s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (4,2,'ZPanel Config',100,'zpanelconfig','user','Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (5,2,'ZPanel News',100,'news','user','Find out all the latest news and information from the ZPanel project.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (6,2,'Updates',100,'updates','user','Check to see if there are any available updates to your version of the ZPanel software.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (8,4,'phpMyAdmin',100,'phpmyadmin','user','phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (9,1,'My Account',100,'my_account','user','Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (10,6,'WebMail',100,'webmail','user','Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (11,1,'Change Password',100,'password_assistant','user','Change your current control panel password.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (12,3,'Backup',100,'backupmgr','user','The backup manager module enables you to backup your entire hosting account including all your MySQL® databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (13,3,'Network Tools',100,'nettools','user','You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (14,3,'Service Status',100,'services','user','Here you can check the current status of our services and see what services are up and running and which are down and not.',0,'true','198','http://www.ballen.co.uk/dwonload.zip');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (15,5,'Domains',100,'domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (16,5,'Parked Domains',100,'parked_domains','user','Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (17,5,'Sub Domains',100,'sub_domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (18,2,'Module Admin',100,'moduleadmin','user','Administer or configure modules registered with module admin',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (19,7,'Manage Clients',100,'manage_clients','user','The account manager enables you to view, update and create client accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (20,7,'Package Manager',100,'packages','user','Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (22,3,'Cron Manager',100,'cron','user','Here you can configure PHP scripts to run automatically at different time intervals.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (23,2,'phpSysInfo',100,'phpsysinfo','user','phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (24,4,'MySQL Database',100,'mysql_databases','user','MySQL&reg databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (25,1,'Usage Viewer',100,'usage_viewer','user','The account usage screen enables you to see exactly what you are currently using on your hosting package.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (26,8,'FTP Accounts',100,'ftp_management','user','Using this module you can create FTP accounts which will enable you and any other accounts you create to have the ability to upload and manage files on your hosting space.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (27,3,'FAQ\'s',100,'faqs','user','Please find a list of the most common questons from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (28,0,'Apache Config',100,'apache_admin','modadmin','This module enables you to configure Apache Vhost settings for your hosting accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (29,5,'DNS Manager',100,'dns_manager','user',NULL,0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (30,0,'DNS Config',100,'dns_admin','modadmin','This module enables you to configure DNS settings for the DNS Manager',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (31,7,'Manage Groups',100,'manage_groups','user','Manage user groups to enable greater control over module permission.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (32,6,'Mailboxes',100,'mailboxes','user','Using this module you have the ability to create IMAP and POP3 Mailboxes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (33,6,'Forwards',100,'forwarders','user','Using this module you have the ability to create mail forwarders.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (34,6,'Distribution Lists',100,'distlists','user','This module enables you to create and manage email distribution groups.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (35,6,'Aliases',100,'aliases','user','Using this module you have the ability to create alias mailboxes to existing accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (36,0,'Mail Config',100,'mail_admin','modadmin','This module enables you to configure your mail options',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (39,4,'MySQL Users',100,'mysql_users','user','MySQL® Users allows you to add users and permissions to your MySQL® databases.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (40,0,'FTP Config',100,'ftp_admin','modadmin','This module enables you to configure FTP settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (41,0,'Backup Config',100,'backup_admin','modadmin','This module enables you to configure Backup settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (42,7,'Client Notice Manager',100,'client_notices','user','Enables resellers to set global notices for their clients.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (43,3,'Protect Directories',100,'htpasswd','user','This module enables you to configure .htaccess files and users to protect your web directories.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (46,7,'Theme Manager',100,'theme_manager','user','Enables the reseller to set themes configurations for their clients.',0,'true','','');

/*Table structure for table `x_mysql` */

DROP TABLE IF EXISTS `x_mysql`;

CREATE TABLE `x_mysql` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql` */

/*Table structure for table `x_mysql_databases` */

DROP TABLE IF EXISTS `x_mysql_databases`;

CREATE TABLE `x_mysql_databases` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_databases` */

/*Table structure for table `x_mysql_dbmap` */

DROP TABLE IF EXISTS `x_mysql_dbmap`;

CREATE TABLE `x_mysql_dbmap` (
  `mm_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mm_acc_fk` int(6) DEFAULT NULL,
  `mm_user_fk` int(6) DEFAULT NULL,
  `mm_database_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`mm_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_dbmap` */

/*Table structure for table `x_mysql_users` */

DROP TABLE IF EXISTS `x_mysql_users`;

CREATE TABLE `x_mysql_users` (
  `mu_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mu_acc_fk` int(6) DEFAULT NULL,
  `mu_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mu_database_fk` int(6) DEFAULT NULL,
  `mu_access_vc` varchar(40) DEFAULT NULL,
  `mu_pass_vc` varchar(40) DEFAULT NULL,
  `mu_created_ts` int(30) DEFAULT NULL,
  `mu_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mu_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_users` */

/*Table structure for table `x_packages` */

DROP TABLE IF EXISTS `x_packages`;

CREATE TABLE `x_packages` (
  `pk_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pk_name_vc` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `pk_reseller_fk` int(6) DEFAULT NULL,
  `pk_enablephp_in` int(1) DEFAULT '0',
  `pk_enablecgi_in` int(1) DEFAULT '0',
  `pk_created_ts` int(30) DEFAULT NULL,
  `pk_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`pk_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `x_packages` */

insert  into `x_packages`(`pk_id_pk`,`pk_name_vc`,`pk_reseller_fk`,`pk_enablephp_in`,`pk_enablecgi_in`,`pk_created_ts`,`pk_deleted_ts`) values (1,'Administration',1,1,1,NULL,NULL);
insert  into `x_packages`(`pk_id_pk`,`pk_name_vc`,`pk_reseller_fk`,`pk_enablephp_in`,`pk_enablecgi_in`,`pk_created_ts`,`pk_deleted_ts`) values (2,'Demo',1,0,0,NULL,NULL);

/*Table structure for table `x_permissions` */

DROP TABLE IF EXISTS `x_permissions`;

CREATE TABLE `x_permissions` (
  `pe_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pe_group_fk` int(6) DEFAULT NULL,
  `pe_module_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`pe_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=287 DEFAULT CHARSET=utf8;

/*Data for the table `x_permissions` */

insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (221,2,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (195,3,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (179,2,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (239,3,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (238,3,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (237,2,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (236,1,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (227,1,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (220,1,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (214,2,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (181,1,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (183,1,4);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (185,1,6);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (189,3,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (184,1,5);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (197,2,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (194,2,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (193,1,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (246,4,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (235,3,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (245,4,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (234,2,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (244,4,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (233,1,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (213,1,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (226,3,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (219,2,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (212,3,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (196,1,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (205,2,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (204,1,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (201,3,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (243,4,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (232,3,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (218,1,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (225,2,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (217,2,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (211,2,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (200,2,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (199,1,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (242,4,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (231,2,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (224,1,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (223,2,23);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (216,1,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (210,1,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (203,2,13);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (202,1,13);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (198,3,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (241,4,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (230,1,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (228,2,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (222,1,23);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (215,3,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (209,3,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (208,2,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (206,3,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (240,4,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (229,3,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (207,1,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (192,3,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (191,2,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (190,1,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (178,1,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (180,3,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (182,2,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (187,1,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (188,2,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (186,1,7);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (248,1,30);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (249,1,28);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (250,1,2);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (251,1,31);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (252,1,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (253,1,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (254,1,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (255,1,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (256,2,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (257,3,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (258,2,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (259,3,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (260,2,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (261,3,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (262,2,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (263,3,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (264,1,36);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (265,3,38);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (266,3,37);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (267,1,38);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (268,2,38);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (269,1,37);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (270,2,37);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (271,1,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (272,2,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (273,3,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (274,1,41);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (275,1,40);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (277,1,18);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (278,1,42);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (279,1,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (280,1,46);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (281,1,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (282,2,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (283,3,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (284,1,48);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (285,2,48);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (286,3,48);

/*Table structure for table `x_profiles` */

DROP TABLE IF EXISTS `x_profiles`;

CREATE TABLE `x_profiles` (
  `ud_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ud_user_fk` int(6) DEFAULT NULL,
  `ud_fullname_vc` varchar(100) DEFAULT NULL,
  `ud_language_vc` varchar(10) DEFAULT 'en',
  `ud_group_fk` int(6) DEFAULT NULL,
  `ud_package_fk` int(6) DEFAULT NULL,
  `ud_address_tx` text,
  `ud_postcode_vc` varchar(20) DEFAULT NULL,
  `ud_phone_vc` varchar(20) DEFAULT NULL,
  `ud_created_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ud_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_profiles` */

insert  into `x_profiles`(`ud_id_pk`,`ud_user_fk`,`ud_fullname_vc`,`ud_language_vc`,`ud_group_fk`,`ud_package_fk`,`ud_address_tx`,`ud_postcode_vc`,`ud_phone_vc`,`ud_created_ts`) values (1,1,'Default Zadmin','en',1,1,'1 Example Road, Ipswich, Suffolk','IP9 2HL','+44(1473) 000 000',0);

/*Table structure for table `x_quotas` */

DROP TABLE IF EXISTS `x_quotas`;

CREATE TABLE `x_quotas` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `x_quotas` */

insert  into `x_quotas`(`qt_id_pk`,`qt_package_fk`,`qt_domains_in`,`qt_subdomains_in`,`qt_parkeddomains_in`,`qt_mailboxes_in`,`qt_fowarders_in`,`qt_distlists_in`,`qt_ftpaccounts_in`,`qt_mysql_in`,`qt_diskspace_bi`,`qt_bandwidth_bi`,`qt_bwenabled_in`,`qt_dlenabled_in`,`qt_totalbw_fk`,`qt_minbw_fk`,`qt_maxcon_fk`,`qt_filesize_fk`,`qt_filespeed_fk`,`qt_filetype_vc`,`qt_modified_in`) values (1,1,5,10,5,10,100,5,10,10,2048000000,10240000000,0,0,NULL,NULL,NULL,NULL,NULL,'*',1);

/*Table structure for table `x_settings` */

DROP TABLE IF EXISTS `x_settings`;

CREATE TABLE `x_settings` (
  `so_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `so_name_vc` varchar(50) DEFAULT NULL,
  `so_cleanname_vc` varchar(50) DEFAULT NULL,
  `so_value_tx` text,
  `so_defvalues_tx` text,
  `so_desc_tx` text,
  `so_module_vc` varchar(50) DEFAULT NULL,
  `so_usereditable_en` enum('true','false') DEFAULT 'false',
  PRIMARY KEY (`so_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

/*Data for the table `x_settings` */

insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (6,'dbversion','ZPanel version','10.0.0',NULL,'Database Version','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (7,'zpanel_root','ZPanel root path','C:/zpanel/panel/',NULL,'Zpanel Web Root','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (8,'module_icons_pr','Icons per Row','8',NULL,'Set the number of icons to display before beginning a new line.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (10,'zpanel_df','Date Format','H:i jS M Y T',NULL,'Set the date format used by modules.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (13,'servicechk_to','Service Check Timeout','2',NULL,'Service Check Timeout','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (14,'root_drive','Root Drive','/',NULL,'The root drive where ZPanel is installed.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (16,'php_exer','PHP executable','php',NULL,'PHP Executable','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (17,'temp_dir','Temp Directory','C:/windows/temp/',NULL,'Global temp directory.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (18,'news_url','ZPanel News API URL','http://api.zpanelcp.com/latestnews.json',NULL,'Zpanel News URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (19,'update_url','ZPanel Update API URL','http://api.zpanelcp.com/latestversion.json',NULL,'Zpanel Update URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (21,'server_ip','Server IP Address','172.167.22.10',NULL,'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (22,'zip_exe','ZIP Exe','C:/ZPanel/bin/7zip/7za.exe',NULL,'Path to the ZIP Executable','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (24,'disable_hostsen','Disable auto HOSTS file entry','false','true|false','Disable Host Entries','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (25,'latestzpversion','Cached version of latest zpanel version','10.0.0',NULL,'This is used for caching the latest version of ZPanel.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (26,'logmode','Debug logging mode','db','db|file|email','The default mode to log all errors in.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (27,'logfile','ZPanel Log file','C:/zpanel/logs/log.txt',NULL,'If loggging is set to \'file\' mode this is the path to the log file that is to be used by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (28,'apikey','XMWS API Key','ee8795c8c53bfdb3b2cc595186b68912',NULL,'The secret API key for the server.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (29,'email_from_address','From Address','zpanel@localhost',NULL,'The email address to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (30,'email_from_name','From Name','Control Panel',NULL,'The name to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (31,'email_smtp','Use SMTP','false','true|false','Use SMTP server to send emails from. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (32,'smtp_auth','Use AUTH','false','true|false','SMTP requires authentication. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (33,'smtp_server','SMTP Server','smtp.gmail.com',NULL,'The address of the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (34,'smtp_port','SMTP Port','465',NULL,'The port address of the SMTP server (usually 25)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (35,'smtp_username','SMTP User','youremail@gmail.com',NULL,'Username for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (36,'smtp_password','SMTP Pass','',NULL,'Password for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (37,'smtp_secure','SMTP Auth method','ssl','false|ssl|tls','If specified will attempt to use encryption to connect to the server, if \'false\' this is disabled. Available options: false, ssl, tls','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (38,'daemon_lastrun',NULL,'1330544970',NULL,'Timestamp of when the daemon last ran.',NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (39,'daemon_dayrun',NULL,'1330542304',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (40,'daemon_weekrun',NULL,'1330102353',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (41,'daemon_monthrun',NULL,'1328908442',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (42,'purge_bu','Purge Backups','true','true|false','Delete client backups after alloted time has elapsed to help save diskspace (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (43,'purge_date','Purge Date','30',NULL,'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (44,'disk_bu','Disk Backups','true','true|false','Allow users to create and save backups of their home directories to disk. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (45,'schedule_bu','Daily Backups','true','true|false','Maks a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (46,'ftp_db','FTP Database','zpanel_filezilla',NULL,'The name of the ftp server database','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (47,'ftp_php','FTP PHP','filezilla.php',NULL,'Name of PHP to include when adding FTP data.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (48,'ftp_service','FTP Service Name','FileZilla server.exe',NULL,'The name of the FTP service','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (49,'ftp_service_root','FTP Service Root','C:/ZPanel/bin/filezilla/',NULL,'The path to the service executable if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (50,'ftp_config_file','FTP Config File','C:/ZPanel/bin/filezilla/FileZilla Server.xml',NULL,'The path to the configuration file if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (51,'mailserver_db','Mailserver Database','zpanel_hmail',NULL,'The name of the mail server database','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (52,'hmailserver_et','Hmail Encryption Type','2',NULL,'Type of encryption uses for hMailServer passwords','Mail Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (53,'max_mail_size','Max Mailbox Size','200',NULL,'Maximum size in megabytes allowed for mailboxes. Default = 200','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (54,'mailserver_php','Mailserver PHP','hmail.php',NULL,'Name of PHP to include when adding mailbox data.','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (55,'remove_orphan','Remove Orphans','true','true|false','When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (56,'named_dir','Named Directory','C:/zpanel/configs/bind/etc/',NULL,'Path to the directory where named.conf is stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (57,'named_conf','Named Config','named.conf',NULL,'Named configuration file','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (58,'zone_dir','Zone Directory','C:/zpanel/configs/bind/zones/',NULL,'Path to where DNS zone files are stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (59,'refresh_ttl','SOA Refesh TTL','21600',NULL,'Global refresh TTL.  Default = 21600 (6 hours)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (60,'retry_ttl','SOA Retry TTL','3600',NULL,'Global retry TTL. Default = 3600 (1 hour)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (61,'expire_ttl','SOA Expire TTL','604800',NULL,'Global expire TTL. Default = 604800 (1 week)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (62,'minimum_ttl','SOA Minimum TTL','86400',NULL,'Global minimum TTL. Default = 86400 (1 day)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (63,'custom_ip','Allow Custom IP','true','true|false','Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (64,'bind_dir','Path to BIND Root','C:/ZPanel/bin/bind/bin/',NULL,'Path to the root directory where BIND is installed.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (65,'bind_service','BIND Service Name','named',NULL,'Name of the BIND service','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (66,'allow_xfer','Allow Zone Transfers','any',NULL,'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (67,'allowed_types','Allowed Record Types','A AAAA CNAME MX TXT SRV SPF NS',NULL,'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (68,'bind_log','Bind Log','C:/ZPanel/logs/bind/bind.log',NULL,'Path and name of the Bind Log','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (69,'hosted_dir','Vhosts Directory','C:/ZPanel/hostdata/',NULL,'Virtual host directory','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (70,'disable_hostsen','Disable HOSTS file entries','false','true|false','Disable host entries','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (71,'apache_vhost','Apache VHOST Conf','C:/zpanel/configs/apache/httpd-vhosts.conf',NULL,'The full system patch and filename of the Apache VHOST configuration name.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (72,'php_handler','PHP Handler','AddType application/x-httpd-php .php3 .php',NULL,'The PHP Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (73,'cgi_handler','CGI Handler','ScriptAlias /cgi-bin/ \"/_cgi-bin/\"\r\n<location /cgi-bin>\r\nAddHandler cgi-script .cgi .pl\r\nOptions ExecCGI -Indexes\r\n</location>',NULL,'The CGI Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (74,'global_vhcustom','Global VHost Entry',NULL,NULL,'Extra directives for all apache vhost\'s.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (75,'static_dir','Static Pages Directory','C:/zpanel/panel/etc/static/',NULL,'The ZPanel static directory, used for storing welcome pages etc. etc.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (76,'parking_path','Vhost Parking Path','C:/zpanel/panel/etc/static/parking/',NULL,'The path to the parking website, this will be used by all clients.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (78,'shared_domains','Shared Domains','no-ip,dydns',NULL,'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dydns,test','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (79,'upload_temp_dir','Upload Temp Directory','C:/zpanel/panel/etc/tmp/',NULL,'The path to the Apache Upload directory (with trailing slash)','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (80,'apache_port','Apache Port','80',NULL,'Apache service port','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (81,'dir_index','Directory Indexes','DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm',NULL,'Directory Index','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (82,'suhosin_value','Suhosin Value','php_admin_value suhosin.executor.func.blacklist \"passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg\"',NULL,'Suhosin configuration for virtual host  blacklisting commands','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (83,'openbase_seperator','Open Base Seperator',';',NULL,'Seperator flag used in open_base_directory setting','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (84,'openbase_temp','Open Base Temp Directory','c:/windows/temp',NULL,'Temp directory used in open_base_directory setting','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (85,'access_log_format','Access Log Format','combined','combined|common','Log format for the Apache access log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (86,'bandwidth_log_format','Bandwidth Log Format','common','combined|common','Log format for the Apache bandwidth log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (87,'global_zpcustom','Global ZPanel Entry',NULL,NULL,'Extra directives for Zpanel default vhost.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (88,'use_openbase','Use Open Base Dir','true','true|false','Enable openbase directory for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (89,'use_suhosin','Use Suhosin','true','true|false','Enable Suhosin for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (90,'zpanel_domain','ZPanel Domain','zpanel.ztest.com',NULL,'Domain that the control panel is installed under.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (91,'log_dir','Log Directory','C:/zpanel/logs/',NULL,'Root path to directory log folders','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (92,'apache_changed','Apache Changed','1330542288','true|false','If set, Apache Config daemon hook will write the vhost config file changes.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (94,'apache_allow_disabled','Allow Disabled','true','true|false','Allow webhosts to remain active even if a user has been disabled.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (95,'apache_budir','VHost Backup Dir','C:/zpanel/backups/apachebackups/',NULL,'Directory that vhost.conf backups are stored.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (96,'apache_purgebu','Purge Backups','true','true|false','Old backups are deleted after the date set in Puge Date','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (97,'apache_purge_date','Purge Date','7',NULL,'Time in days that vhost backups are safe from deletion','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (98,'apache_backup','VHost Backup','true','true|false','Backup vhost file before a new one is written','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (99,'zsudo','zsudo path','C:/zpanel/panel/bin/zsudo',NULL,'Path to the zsudo binary used by Apache to run system commands.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (100,'apache_restart','Apache Restart Cmd','-k restart -n \"Apache\"',NULL,'Command line arguments used after the restart service request when reloading Apache.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (101,'httpd_exe','Apache Binary','C:/ZPanel/bin/apache/bin/httpd.exe',NULL,'Path to the Apache binary','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (102,'apache_sn','Apache Service Name','httpd',NULL,'Service name used to handle Apache service control','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (103,'daemon_exer',NULL,'C:/zpanel/panel/bin/daemon.php',NULL,'Path to the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (104,'daemon_timing',NULL,'0 * * * *',NULL,'Cron time for when to run the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (105,'cron_file','Cron File','c:/windows/system32/crontab',NULL,'Path to the user cr0n file','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (106,'htpasswd_exe','htpassword Exe','htpasswd',NULL,'Path to htpasswd.exe for protecting directories with .htaccess','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (107,'mysqldump_exe','MySQL Dump','C:/ZPanel/bin/mysql/bin/mysqldump.exe',NULL,'Path to MySQL dump','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (108,'named_checkconf','Named CheckConfig','C:/ZPanel/bin/bind/bin/named-checkconf.exe',NULL,'Path to named-checkconf bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (109,'named_checkzone','Named CheckZone','C:/ZPanel/bin/bind/bin/named-checkzone.exe',NULL,'Path to named-checkzone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (110,'named_compilezone','Named CompileZone','C:/ZPanel/bin/bind/bin/named-compilezone.exe',NULL,'	Path to named-compilezone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (111,'dns_hasupdates','DNS Updated',NULL,NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (112,'mailer_type','Mail method','mail','mail|smtp|sendmail','Method to use when sending emails out. (mail = PHP Mail())','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (113,'daemon_run_interval','Number of seconds between each daemon execution','300',NULL,'The total number of seconds between each daemon run (default 300 = 5 mins)','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (114,'debug_mode','ZPanel Debug Mode','dev','dev|prod','Whether or not to show PHP debug errors,warnings and notices','ZPanel Config','true');

/*Table structure for table `x_translations` */

DROP TABLE IF EXISTS `x_translations`;

CREATE TABLE `x_translations` (
  `tr_id_pk` int(11) NOT NULL AUTO_INCREMENT,
  `tr_en_tx` text,
  `tr_de_tx` text,
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;

/*Data for the table `x_translations` */

insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (44,'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.','Webmail ist ein bequemer Weg fÃ¼r Sie, Ihre E-Mail-Konten online zu Ã¼berprÃ¼fen, ohne dass eine E-Mail-Client zu konfigurieren.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (45,'Launch Webmail','Starten Sie WebMail');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (56,'PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.','PHPInfo bietet Ihnen Informationen über die PHP-Version auf dem System, sowie PHP installiert extentsions und Konfigurationsmöglichkeiten.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (67,'From here you can shadow any of your client\'s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!','Von hier aus können alle Ihre Kunden-Accounts können Schatten, ermöglicht Ihnen dies automatisch, wenn der Benutzer mit dem Sie remote helfen zu sehen, was sie sehen, anbieten zu können login!');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (68,'My Account','Meine Konto');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (69,'Change Password','Kennwort ändern');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (70,'Shadowing','Schatten');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (71,'ZPanel Config','Config ZPanel');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (72,'ZPanel News','ZPanel Aktuelles');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (73,'Updates','Aktualisierung');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (74,'Report Bug','Fehler melden');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (75,'Account','Konto');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (76,'Module Admin','Modul Admin');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (77,'Backup','Sicherungskopie');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (78,'Network Tools','Netzwerk-Tools');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (79,'Service Status','Service Status');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (80,'PHPInfo','PHPInfo');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (81,'phpMyAdmin','phpMyAdmin');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (82,'Domains','Domains');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (83,'Sub Domains','Sub Domains');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (84,'Parked Domains','geparkte Domains');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (85,'Manage Clients','Verwalten Kunden');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (86,'Package Manager','Paket Manager');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (87,'Server','Server');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (88,'Database','Datenbank');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (89,'Advanced','Fortgeschritten');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (90,'Mail','Post');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (91,'Reseller','Wiederverkäufer');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (92,'Account Information','Account Informationen');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (93,'Server Admin','Server Admin');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (94,'Database Management','Datenbank Verwalten');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (95,'Domain Management','Verwalten von Domains');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (96,'Find out all the latest news and information from the ZPanel project.','Finden Sie heraus, alle Neuigkeiten und Informationen aus dem ZPanel Projekt.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (97,'Check to see if there are any available updates to your version of the ZPanel software.','Prüfen Sie, ob es irgendwelche verfügbaren Aktualisierungen für Ihre Version des ZPanel Software.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (98,'If you have found a bug with ZPanel you can report it here.','Did you mean: If you have found a bug with CPanel you can report it here.\r\nWenn Sie einen Fehler mit ZPanel gefunden haben, können Sie ihn hier melden.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (99,'phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.','phpMyAdmin ist ein webbasiertes Tool, das Sie zu Ihrem ZPanel MySQL-Datenbanken via verwalten können. im Internet.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (100,'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.','Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (101,'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.','Webmail ist ein bequemer Weg für Sie, Ihre E-Mail-Konten online zu überprüfen, ohne dass eine E-Mail-Client zu konfigurieren.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (102,'Change your current control panel password.','Ändern Sie Ihre aktuelle Bedienfeld oder MySQL-Kennwort.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (103,'The backup manager module enables you to backup your entire hosting account including all your MySQL® databases.','Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL ® Datenbank-Backup.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (104,'You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.','Sie können die folgenden Tools verwenden, um Probleme zu diagnostizieren oder einfach testen Verbindung mit anderen Servern oder Websites rund um den Globus.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (105,'Here you can check the current status of our services and see what services are up and running and which are down and not.','Hier können Sie den aktuellen Status unserer Dienstleistungen und sehen, welche Dienste vorhanden sind und laufen, und die nach unten und es nicht sind.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (106,'This module enables you to add or configure domain web hosting on your account.','Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (107,'Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!','Domain-Parking bezieht sich auf die Registrierung von Internet Domain-Namen ohne diese Domäne verwendet, um Dienste wie E-Mail oder eine Webseite bereitzustellen. Wenn Sie alle Domains, die Sie nicht haben, dann einfach parken sie!');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (108,'This module enables you to add or configure domain web hosting on your account.','Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (109,'Administer or configure modules registered with module admin','Verwalten oder zu konfigurieren Module mit Modul admin registriert');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (110,'The account manager enables you to view, update and create client accounts.','Die Account-Manager ermöglicht es Ihnen, anzuzeigen, zu aktualisieren und erstellen Kundenkonten.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (111,'Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.','Willkommen auf der Paket-Manager, mit diesem Modul ermöglicht Ihnen die Erstellung und Verwaltung von bestehenden Reseller-Pakete auf Ihrem ZPanel Hosting-Account.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (112,'Gives you access to your files with drag-and-drop, multiple file uploading, text editing, zip support.','Ermöglicht den Zugriff auf Ihre Dateien mit Drag-and-drop, multiple Datei-Upload, Textbearbeitung, zip unterstützen.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (113,'Secure FTP Applet is a JAVA based FTP client component that runs within your web browser. It is designed to let non-technical users exchange data securely with an FTP server.','Secure FTP Applet ist eine Java-basierte FTP-Client-Komponente, die in Ihrem Web-Browser läuft. Es wurde entwickelt, um nicht-technische Anwender den Datenaustausch secureiy lassen mit einem FTP-Server.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (114,'Full name','Vollständiger Name');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (115,'Email Address','E-Mail Adresse');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (116,'Phone Number','Telefonnummer');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (117,'Choose Language','Sprache wählen');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (118,'Postal Address','Postanschrift');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (119,'Postal Code','Postleitzahl');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (120,'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.','Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (121,'Changes to your account settings have been saved successfully!','Änderungen an Ihrem Konto-Einstellungen wurden erfolgreich gespeichert!');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (122,'Update Account','Aktualisierung Konto');
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (123,'Enter your account details','Geben Sie Ihre Kontodaten');

/*Table structure for table `x_vhosts` */

DROP TABLE IF EXISTS `x_vhosts`;

CREATE TABLE `x_vhosts` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_vhosts` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
