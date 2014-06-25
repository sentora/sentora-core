/*
* ZPanelX Database Schema
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE `zpanel_core` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `zpanel_core`;

/*Table structure for table `x_accounts` */

DROP TABLE IF EXISTS `x_accounts`;

CREATE TABLE `x_accounts` (
  `ac_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ac_user_vc` varchar(50) DEFAULT NULL,
  `ac_pass_vc` varchar(200) DEFAULT NULL,
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
  `ac_passsalt_vc` varchar(22) DEFAULT NULL,
  `ac_catorder_vc` varchar(255) DEFAULT NULL,
  `ac_created_ts` int(30) DEFAULT NULL,
  `ac_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ac_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_accounts` */

INSERT  INTO `x_accounts` (
    `ac_id_pk`,
    `ac_user_vc`,
    `ac_pass_vc`,
    `ac_passsalt_vc`,
    `ac_email_vc`,
    `ac_reseller_fk`,
    `ac_package_fk`,
    `ac_group_fk`,
    `ac_usertheme_vc`,
    `ac_usercss_vc`,
    `ac_enabled_in`,
    `ac_lastlogon_ts`,
    `ac_notice_tx`,
    `ac_resethash_tx`,
    `ac_created_ts`,
    `ac_deleted_ts`
    )
VALUES
    (
    1,
    'zadmin',
    'v.eCCwjd4xAGWagHafqod6SMASr25Na',
    '/L8ewHozMz0EqAmmILPFN2',
    'zadmin@localhost',
    1,
    1,
    1,
    'zpanelx',
    'default',
    1,
    0,
    'Welcome to your new ZPanel installation! You can remove this message from the Client Notice Manager module. This module allows you to notify your clients of service outages, upgrades and new features etc :-)',
    NULL,
    0,
    NULL
    );

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
  `bd_diskover_in` int(6) DEFAULT NULL,
  `bd_diskcheck_in` int(6) DEFAULT NULL,
  `bd_transover_in` int(6) DEFAULT NULL,
  `bd_transcheck_in` int(6) DEFAULT NULL,
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
  `dn_host_vc` varchar(100) DEFAULT NULL,
  `dn_ttl_in` int(30) DEFAULT NULL,
  `dn_target_vc` varchar(100) DEFAULT NULL,
  `dn_texttarget_tx` text,
  `dn_priority_in` int(50) DEFAULT NULL,
  `dn_weight_in` int(50) DEFAULT NULL,
  `dn_port_in` int(50) DEFAULT NULL,
  `dn_created_ts` int(30) DEFAULT NULL,
  `dn_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dn_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_dns` */

/* Table structure for table `x_dns_create` */
CREATE TABLE IF NOT EXISTS `zpanel_core`.`x_dns_create` (
  `dc_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dc_acc_fk` int(6) DEFAULT NULL,
  `dc_type_vc` varchar(50) DEFAULT NULL,
  `dc_host_vc` varchar(100) DEFAULT NULL,
  `dc_ttl_in` int(30) DEFAULT NULL,
  `dc_target_vc` varchar(100) DEFAULT NULL,
  `dc_priority_in` int(50) DEFAULT NULL,
  `dc_weight_in` int(50) DEFAULT NULL,
  `dc_port_in` int(50) DEFAULT NULL,
  PRIMARY KEY (`dc_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

/* Data for the table `x_dns_create` */
INSERT INTO `zpanel_core`.`x_dns_create` (`dc_id_pk`, `dc_acc_fk`, `dc_type_vc`, `dc_host_vc`, `dc_ttl_in`, `dc_target_vc`, `dc_priority_in`, `dc_weight_in`, `dc_port_in`) VALUES
(1, 0, 'A', '@', 3600, ':IP:', NULL, NULL, NULL),
(2, 0, 'CNAME', 'www', 3600, '@', NULL, NULL, NULL),
(3, 0, 'CNAME', 'ftp', 3600, '@', NULL, NULL, NULL),
(4, 0, 'A', 'mail', 86400, ':IP:', NULL, NULL, NULL),
(5, 0, 'MX', '@', 86400, 'mail.:DOMAIN:', 10, NULL, NULL),
(6, 0, 'A', 'ns1', 172800, ':IP:', NULL, NULL, NULL),
(7, 0, 'A', 'ns2', 172800, ':IP:', NULL, NULL, NULL),
(8, 0, 'NS', '@', 172800, 'ns1.:DOMAIN:', NULL, NULL, NULL),
(9, 0, 'NS', '@', 172800, 'ns2.:DOMAIN:', NULL, NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `x_faqs` */

insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (1,1,'How can I update my personal contact details?','From the control panel homepage please click on the &quot;My Account&quot; icon to enable you to update your personal details.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (2,1,'How do I change my password?','Your ZPanel and MySQL password can be easily changed using the &quot;Change Password&quot; icon on the control panel.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (3,1,'I don&#39;t think one of the services(Apache, MySQL, FTP, etc) are running. Is there any easy way to check?','ZPanel comes with a service monitoring system that checks to make sure all the services are up and running, Simply go to your Control Panel Home and select the module called &quot;Service Status&quot;. From there you will be able to see if any of the services are down or up.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (4,1,'How can I set my domain to work with my ZPanel Account?','To setup up a domain with ZPanel first thing you need to do is go &quot;Domains&quot; and add your to the list. Next you need to set the Name Server on your Domain Registrar to match that of your host. This information can be obtained by contacting your host.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (7,1,'How can I create a MySQL Database?','To create a MySQL database simply go to the section of the panel called &quot;Database Management&quot; and select the module called &quot;MySQL Databases&quot; from here you will easily be able to add and manage databases on your account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (8,1,'What is phpMyAdmin?','phpMyAdmin is an open source tool intended to handle the administration of MySQL databases. It can perform various tasks such as creating, modifying or deleting databases, tables, fields or rows or executing SQL statements',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (9,1,'How do I create FTP Accounts?','You can create FTP accounts by going to &quot;FTP Accounts&quot; from their you can add accounts and manage quotas and directories. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (10,1,'How to Password Protect Directories?','Go to Advanced and select the module &quot;Password Protect Directories&quot; From here you can generate .htaccess files to lock down directories on your site to only people with a login and password.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (11,1,'How do I create an E-Mail Account?','Go to the Mail section of ZPanel and select the module called &quot;Mailboxes&quot;, from here you can create E-Mail account for each domain setup on your account. You can also reset passwords to previously created accounts.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (12,1,'How do I create a Mail Alias?','Go to the Mail section of ZPanel and select the module called &quot;Aliases&quot;, from here you can create Alias E-Mail accounts for each previously created E-Mail account. All mail sent to the alias will be delivered to the master e-mail account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (13,1,'How can I create a Mailing List?','Mailing lists can be setup by going to the Mail section of ZPanel and select the module called &quot;Distribution Lists&quot;, from here you can create Mailing lists by creating an E-mail Account. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (14,1,'How do I use Mail Forwards?','Go to the Mail section of ZPanel and select the module called &quot;Forwards&quot;, from here you can create E-Mail address on your domains that will forward to other E-Mail addresses that are on different servers like &quot;G-Mail, Yahoo, and MSN&quot;. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (15,1,'What are Subdomains?','A subdomain combines a unique identifier with a domain name to become essentially a &quot;domain within a domain.&quot; The unique identifier simply replaces the www in the web address. Yahoo!, for example, uses subdomains such as mail.yahoo.com and music.yahoo.com to reference its mail and music services, under the umbrella of www.yahoo.com. They can be created by using the Subdomain module in the Domains section. You can assign directories for each sub domain from the module.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (16,1,'How can I view how much Data I have used?','You can view how much data you have used by accessing the &quot;Usage Viewer&quot; under the Account Information section of ZPanel. It displays account information in different formats. It displays Data Usage, Domain Usage, Bandwidth Usage, MySQL Usage, and much more.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (17,1,'How can I access Webmail?','Go to the Mail section of ZPanel and select the module called &quot;Webmail&quot;, from here you can login to your E-Mail account and view and create messages. ',1,NULL,NULL);
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

insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (1,2,'PHPInfo',100,'phpinfo','user','PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extensions and configuration details.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (3,2,'Shadowing',100,'shadowing','user','From here you can shadow any of your client\'s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (4,2,'ZPanel Config',100,'zpanelconfig','user','Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (5,2,'ZPanel News',100,'news','user','Find out all the latest news and information from the ZPanel project.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (6,2,'Updates',100,'updates','user','Check to see if there are any available updates to your version of the ZPanel software.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (8,4,'phpMyAdmin',100,'phpmyadmin','user','phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (9,1,'My Account',100,'my_account','user','Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (10,6,'WebMail',100,'webmail','user','Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (11,1,'Change Password',100,'password_assistant','user','Change your current control panel password.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (12,3,'Backup',100,'backupmgr','user','The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (14,3,'Service Status',100,'services','user','Here you can check the current status of our services and see what services are up and running and which are down and not.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (15,5,'Domains',100,'domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (16,5,'Parked Domains',100,'parked_domains','user','Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (17,5,'Sub Domains',100,'sub_domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (18,2,'Module Admin',100,'moduleadmin','user','Administer or configure modules registered with module admin',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (19,7,'Manage Clients',100,'manage_clients','user','The account manager enables you to view, update and create client accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (20,7,'Package Manager',100,'packages','user','Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (22,3,'Cron Manager',100,'cron','user','Here you can configure PHP scripts to run automatically at different time intervals.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (23,2,'phpSysInfo',100,'phpsysinfo','user','phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (24,4,'MySQL Database',100,'mysql_databases','user','MySQL&reg; databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg; databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (25,1,'Usage Viewer',100,'usage_viewer','user','The account usage screen enables you to see exactly what you are currently using on your hosting package.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (26,8,'FTP Accounts',100,'ftp_management','user','Using this module you can create FTP accounts which will enable you and any other accounts you create to have the ability to upload and manage files on your hosting space.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (27,3,'FAQ\'s',100,'faqs','user','Please find a list of the most common questions from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (28,0,'Apache Config',100,'apache_admin','modadmin','This module enables you to configure Apache Vhost settings for your hosting accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (29,5,'DNS Manager',100,'dns_manager','user',NULL,0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (30,0,'DNS Config',100,'dns_admin','modadmin','This module enables you to configure DNS settings for the DNS Manager',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (31,7,'Manage Groups',100,'manage_groups','user','Manage user groups to enable greater control over module permission.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (32,6,'Mailboxes',100,'mailboxes','user','Using this module you have the ability to create IMAP and POP3 Mailboxes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (33,6,'Forwards',100,'forwarders','user','Using this module you have the ability to create mail forwarders.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (34,6,'Distribution Lists',100,'distlists','user','This module enables you to create and manage email distribution groups.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (35,6,'Aliases',100,'aliases','user','Using this module you have the ability to create alias mailboxes to existing accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (36,0,'Mail Config',100,'mail_admin','modadmin','This module enables you to configure your mail options',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (39,4,'MySQL Users',100,'mysql_users','user','MySQL&reg; Users allows you to add users and permissions to your MySQL&reg; databases.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (40,0,'FTP Config',100,'ftp_admin','modadmin','This module enables you to configure FTP settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (41,0,'Backup Config',100,'backup_admin','modadmin','This module enables you to configure Backup settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (42,7,'Client Notice Manager',100,'client_notices','user','Enables resellers to set global notices for their clients.',NULL,'true',NULL,NULL);
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (46,7,'Theme Manager',100,'theme_manager','user','Enables the reseller to set themes configurations for their clients.',0,'true','',NULL);
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (47,3,'Webalizer Stats',100,'webalizer_stats','user','You can view many statistics such as visitor infomation, bandwidth used, referal infomation and most viewed pages etc. Web stats are based on Domains and sub-domains so to view web stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view web stats for.',0,'true','',NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

/*Data for the table `x_permissions` */

insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (1,1,18);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (2,1,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (3,2,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (4,3,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (5,1,28);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (6,1,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (7,2,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (8,3,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (9,1,41);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (10,1,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (11,2,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (12,3,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (13,1,42);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (14,2,42);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (15,1,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (16,2,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (17,3,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (18,1,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (19,2,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (20,3,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (21,1,30);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (22,1,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (23,2,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (24,3,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (25,1,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (26,2,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (27,3,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (28,1,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (29,2,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (30,3,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (31,1,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (32,2,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (33,3,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (34,1,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (35,2,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (36,3,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (37,1,40);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (38,1,36);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (39,1,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (40,2,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (41,3,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (42,1,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (43,2,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (44,1,31);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (45,2,31);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (46,1,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (47,2,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (48,3,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (49,1,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (50,2,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (51,3,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (52,1,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (53,2,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (54,3,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (55,1,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (56,2,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (57,1,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (58,2,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (59,3,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (60,1,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (61,2,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (62,3,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (63,1,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (64,2,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (65,3,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (66,1,23);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (67,1,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (68,2,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (69,3,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (70,1,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (71,2,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (72,3,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (73,1,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (74,2,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (75,1,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (76,2,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (77,3,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (78,1,46);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (79,2,46);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (80,1,6);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (81,1,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (82,2,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (83,3,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (84,1,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (85,2,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (86,3,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (87,1,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (88,2,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (89,3,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (90,1,4);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (91,1,5);

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

insert  into `x_profiles`(`ud_id_pk`,`ud_user_fk`,`ud_fullname_vc`,`ud_language_vc`,`ud_group_fk`,`ud_package_fk`,`ud_address_tx`,`ud_postcode_vc`,`ud_phone_vc`,`ud_created_ts`) values (1,1,'Default Zadmin','en',1,1,'1 Example Road,\r\nIpswich,\r\nSuffolk','IP9 2HL','+44(1473) 000 000',0);

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

insert  into `x_quotas`(`qt_id_pk`,`qt_package_fk`,`qt_domains_in`,`qt_subdomains_in`,`qt_parkeddomains_in`,`qt_mailboxes_in`,`qt_fowarders_in`,`qt_distlists_in`,`qt_ftpaccounts_in`,`qt_mysql_in`,`qt_diskspace_bi`,`qt_bandwidth_bi`,`qt_bwenabled_in`,`qt_dlenabled_in`,`qt_totalbw_fk`,`qt_minbw_fk`,`qt_maxcon_fk`,`qt_filesize_fk`,`qt_filespeed_fk`,`qt_filetype_vc`,`qt_modified_in`) values (1,1,-1,-1,-1,-1,-1,-1,-1,-1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'*',1);

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

insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (6,'dbversion','ZPanel version','10.1.1',NULL,'Database Version','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (7,'zpanel_root','ZPanel root path','/etc/zpanel/panel/',NULL,'Zpanel Web Root','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (8,'module_icons_pr','Icons per Row','10',NULL,'Set the number of icons to display before beginning a new line.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (10,'zpanel_df','Date Format','H:i jS M Y T',NULL,'Set the date format used by modules.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (13,'servicechk_to','Service Check Timeout','10',NULL,'Service Check Timeout','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (14,'root_drive','Root Drive','/',NULL,'The root drive where ZPanel is installed.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (16,'php_exer','PHP executable','php',NULL,'PHP Executable','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (17,'temp_dir','Temp Directory','/var/zpanel/temp/',NULL,'Global temp directory.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (18,'news_url','ZPanel News API URL','http://api.zpanelcp.com/latestnews.json',NULL,'Zpanel News URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (19,'update_url','ZPanel Update API URL','http://api.zpanelcp.com/latestversion.json',NULL,'Zpanel Update URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (21,'server_ip','Server IP Address','',NULL,'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (22,'zip_exe','ZIP Exe','zip',NULL,'Path to the ZIP Executable','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (24,'disable_hostsen','Disable auto HOSTS file entry','false','true|false','Disable Host Entries','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (25,'latestzpversion','Cached version of latest zpanel version','10.0.0',NULL,'This is used for caching the latest version of ZPanel.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (26,'logmode','Debug logging mode','db','db|file|email','The default mode to log all errors in.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (27,'logfile','ZPanel Log file','/var/zpanel/logs/zpanel.log',NULL,'If logging is set to \'file\' mode this is the path to the log file that is to be used by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (28,'apikey','XMWS API Key','ee8795c8c53bfdb3b2cc595186b68912',NULL,'The secret API key for the server.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (29,'email_from_address','From Address','zpanel@localhost',NULL,'The email address to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (30,'email_from_name','From Name','ZPanel Server',NULL,'The name to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (31,'email_smtp','Use SMTP','false','true|false','Use SMTP server to send emails from. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (32,'smtp_auth','Use AUTH','false','true|false','SMTP requires authentication. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (33,'smtp_server','SMTP Server','',NULL,'The address of the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (34,'smtp_port','SMTP Port','465',NULL,'The port address of the SMTP server (usually 25)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (35,'smtp_username','SMTP User','',NULL,'Username for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (36,'smtp_password','SMTP Pass','',NULL,'Password for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (37,'smtp_secure','SMTP Auth method','false','false|ssl|tls','If specified will attempt to use encryption to connect to the server, if \'false\' this is disabled. Available options: false, ssl, tls','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (38,'daemon_lastrun','Daemon timeing cache','0',NULL,'Timestamp of when the daemon last ran.',NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (39,'daemon_dayrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (40,'daemon_weekrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (41,'daemon_monthrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (42,'purge_bu','Purge Backups','true','true|false','Delete client backups after allotted time has elapsed to help save diskspace (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (43,'purge_date','Purge Date','30',NULL,'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (44,'disk_bu','Disk Backups','true','true|false','Allow users to create and save backups of their home directories to disk. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (45,'schedule_bu','Daily Backups','false','true|false','Make a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (46,'ftp_db','FTP Database','zpanel_proftpd',NULL,'The name of the ftp server database','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (47,'ftp_php','FTP PHP','proftpd.php',NULL,'Name of PHP to include when adding FTP data.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (48,'ftp_service','FTP Service Name','proftpd',NULL,'The name of the FTP service','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (49,'ftp_service_root','FTP Service Root','/etc/init.d/',NULL,'The path to the service executable if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (50,'ftp_config_file','FTP Config File','',NULL,'The path to the configuration file if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (51,'mailserver_db','Mailserver Database','zpanel_postfix',NULL,'The name of the mail server database','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (52,'hmailserver_et','Hmail Encryption Type','2',NULL,'Type of encryption uses for hMailServer passwords','Mail Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (53,'max_mail_size','Max Mailbox Size','200',NULL,'Maximum size in megabytes allowed for mailboxes. Default = 200','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (54,'mailserver_php','Mailserver PHP','postfix.php',NULL,'Name of PHP to include when adding mailbox data.','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (55,'remove_orphan','Remove Orphans','true','true|false','When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (56,'named_dir','Named Directory','/etc/zpanel/configs/bind/etc/',NULL,'Path to the directory where named.conf is stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (57,'named_conf','Named Config','named.conf',NULL,'Named configuration file','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (58,'zone_dir','Zone Directory','/etc/zpanel/configs/bind/zones/',NULL,'Path to where DNS zone files are stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (59,'refresh_ttl','SOA Refesh TTL','21600',NULL,'Global refresh TTL.  Default = 21600 (6 hours)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (60,'retry_ttl','SOA Retry TTL','3600',NULL,'Global retry TTL. Default = 3600 (1 hour)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (61,'expire_ttl','SOA Expire TTL','604800',NULL,'Global expire TTL. Default = 604800 (1 week)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (62,'minimum_ttl','SOA Minimum TTL','86400',NULL,'Global minimum TTL. Default = 86400 (1 day)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (63,'custom_ip','Allow Custom IP','true','true|false','Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (64,'bind_dir','Path to BIND Root','/etc/named/',NULL,'Path to the root directory where BIND is installed.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (65,'bind_service','BIND Service Name','named',NULL,'Name of the BIND service','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (66,'allow_xfer','Allow Zone Transfers','any',NULL,'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (67,'allowed_types','Allowed Record Types','A AAAA CNAME MX TXT SRV SPF NS',NULL,'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (68,'bind_log','Bind Log','/var/named/data/named.run',NULL,'Path and name of the Bind Log','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (69,'hosted_dir','Vhosts Directory','/var/zpanel/hostdata/',NULL,'Virtual host directory','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (70,'disable_hostsen','Disable HOSTS file entries','false','true|false','Disable host entries','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (71,'apache_vhost','Apache VHOST Conf','/etc/zpanel/configs/apache/httpd-vhosts.conf',NULL,'The full system path and filename of the Apache VHOST configuration name.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (72,'php_handler','PHP Handler','AddType application/x-httpd-php .php3 .php',NULL,'The PHP Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (73,'cgi_handler','CGI Handler','ScriptAlias /cgi-bin/ \"/_cgi-bin/\"\r\n<location /cgi-bin>\r\nAddHandler cgi-script .cgi .pl\r\nOptions ExecCGI -Indexes\r\n</location>',NULL,'The CGI Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (74,'global_vhcustom','Global VHost Entry',NULL,NULL,'Extra directives for all apache vhost\'s.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (75,'static_dir','Static Pages Directory','/etc/zpanel/panel/etc/static/',NULL,'The ZPanel static directory, used for storing welcome pages etc. etc.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (76,'parking_path','Vhost Parking Path','/etc/zpanel/panel/etc/static/parking/',NULL,'The path to the parking website, this will be used by all clients.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (78,'shared_domains','Shared Domains','no-ip,dyndns',NULL,'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dyndns','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (79,'upload_temp_dir','Upload Temp Directory','/var/zpanel/temp/',NULL,'The path to the Apache Upload directory (with trailing slash)','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (80,'apache_port','Apache Port','80',NULL,'Apache service port','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (81,'dir_index','Directory Indexes','DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm',NULL,'Directory Index','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (82,'suhosin_value','Suhosin Value','php_admin_value suhosin.executor.func.blacklist \"passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg, exec\"',NULL,'Suhosin configuration for virtual host  blacklisting commands','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (83,'openbase_seperator','Open Base Seperator',':',NULL,'Seperator flag used in open_base_directory setting','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (84,'openbase_temp','Open Base Temp Directory','/var/zpanel/temp/',NULL,'Temp directory used in open_base_directory setting','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (85,'access_log_format','Access Log Format','combined','combined|common','Log format for the Apache access log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (86,'bandwidth_log_format','Bandwidth Log Format','common','combined|common','Log format for the Apache bandwidth log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (87,'global_zpcustom','Global ZPanel Entry',NULL,NULL,'Extra directives for Zpanel default vhost.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (88,'use_openbase','Use Open Base Dir','true','true|false','Enable openbase directory for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (89,'use_suhosin','Use Suhosin','true','true|false','Enable Suhosin for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (90,'zpanel_domain','ZPanel Domain','zpanel.ztest.com',NULL,'Domain that the control panel is installed under.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (91,'log_dir','Log Directory','/var/zpanel/logs/',NULL,'Root path to directory log folders','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (92,'apache_changed','Apache Changed','true','true|false','If set, Apache Config daemon hook will write the vhost config file changes.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (94,'apache_allow_disabled','Allow Disabled','true','true|false','Allow webhosts to remain active even if a user has been disabled.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (95,'apache_budir','VHost Backup Dir','/var/zpanel/backups/',NULL,'Directory that vhost.conf backups are stored.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (96,'apache_purgebu','Purge Backups','true','true|false','Old backups are deleted after the date set in Puge Date','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (97,'apache_purge_date','Purge Date','7',NULL,'Time in days that vhost backups are safe from deletion','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (98,'apache_backup','VHost Backup','true','true|false','Backup vhost file before a new one is written','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (99,'zsudo','zsudo path','/etc/zpanel/panel/bin/zsudo',NULL,'Path to the zsudo binary used by Apache to run system commands.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (100,'apache_restart','Apache Restart Cmd','reload',NULL,'Command line arguments used after the restart service request when reloading Apache.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (101,'httpd_exe','Apache Binary','httpd24',NULL,'Path to the Apache binary','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (102,'apache_sn','Apache Service Name','httpd24-httpd',NULL,'Service name used to handle Apache service control','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (103,'daemon_exer',NULL,'/etc/zpanel/panel/bin/daemon.php',NULL,'Path to the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (104,'daemon_timing',NULL,'0 * * * *',NULL,'Cron time for when to run the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (105,'cron_file','Cron File','/var/spool/cron/apache',NULL,'Path to the user cron file','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (106,'htpasswd_exe','htpassword Exe','htpasswd',NULL,'Path to htpasswd.exe for protecting directories with .htaccess','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (107,'mysqldump_exe','MySQL Dump','mysqldump',NULL,'Path to MySQL dump','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (108,'dns_hasupdates','DNS Updated',NULL,NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (109,'named_checkconf','Named CheckConfig','named-checkconf',NULL,'Path to named-checkconf bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (110,'named_checkzone','Named CheckZone','named-checkzone',NULL,'Path to named-checkzone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (111,'named_compilezone','Named CompileZone','named-compilezone',NULL,'	Path to named-compilezone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (112,'mailer_type','Mail method','mail','mail|smtp|sendmail','Method to use when sending emails out. (mail = PHP Mail())','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (113,'daemon_run_interval','Number of seconds between each daemon execution','300',NULL,'The total number of seconds between each daemon run (default 300 = 5 mins)','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (114,'debug_mode','ZPanel Debug Mode','prod','dev|prod','Whether or not to show PHP debug errors,warnings and notices','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (115,'password_minlength','Min Password Length','6',NULL,'Minimum length required for new passwords','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (116,'cron_reload_command','Cron Reload Command','crontab',NULL,'Crontab binary in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (117,'cron_reload_path','Cron Reload Path','/var/spool/cron/apache',NULL,'Cron reload path in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (118,'cron_reload_flag','Cron Reload Flags','-u',NULL,'Cron reload command flags in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (119,'cron_reload_user','Cron Reload User','apache',NULL,'Cron reload apache user in Linux','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (120,'login_csfr','Remote Login Forms','false','false|true','Disables CSFR protection on the login form to enable remote login forms.','ZPanel Config','true');

/*Table structure for table `x_translations` */

DROP TABLE IF EXISTS `x_translations`;

CREATE TABLE `x_translations` (
  `tr_id_pk` int(11) NOT NULL AUTO_INCREMENT,
  `tr_en_tx` text,
  `tr_de_tx` text,
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=latin1;

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
insert  into `x_translations`(`tr_id_pk`,`tr_en_tx`,`tr_de_tx`) values (103,'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.','Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL &reg; Datenbank-Backup.');
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
  `vh_custom_port_in` int(6) DEFAULT NULL,
  `vh_custom_ip_vc` varchar(45) DEFAULT NULL,
  `vh_portforward_in` int(1) DEFAULT NULL,
  `vh_soaserial_vc` CHAR(10) DEFAULT 'AAAAMMDDSS',
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
