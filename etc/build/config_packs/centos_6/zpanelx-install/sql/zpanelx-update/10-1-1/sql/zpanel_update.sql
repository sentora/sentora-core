/* Update SQL for CentOS 6.3 ZPanel 10.1.0 to 10.1.1 */
USE `zpanel_core`;

/* VERSION SPECIFIC UPDATE SQL STATEMENTS */
ALTER TABLE x_vhosts ADD vh_soaserial_vc CHAR(10) DEFAULT "AAAAMMDDSS";

/* Update the ZPanel database version number */
UPDATE `zpanel_core`.`x_settings` SET `so_value_tx` = '10.1.1' WHERE `so_name_vc` = 'dbversion';

/* Removal of Password Protect directories module */
DELETE FROM `zpanel_core`.`x_modules` WHERE `x_modules`.`mo_name_vc` = 'Protect Directories';

/* Update module entries and their translations */
UPDATE `zpanel_core`.`x_modules` SET `mo_desc_tx` = 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.' WHERE `mo_id_pk` = '12';
UPDATE `zpanel_core`.`x_modules` SET `mo_desc_tx` = 'MySQL&reg; databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg; databases.' WHERE `mo_id_pk` = '24';
UPDATE `zpanel_core`.`x_modules` SET `mo_desc_tx` = 'MySQL&reg; Users allows you to add users and permissions to your MySQL&reg; databases.' WHERE `mo_id_pk` = '39';
UPDATE `zpanel_core`.`x_translations` SET `tr_en_tx` = 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.' WHERE `tr_id_pk` = '103';
UPDATE `zpanel_core`.`x_translations` SET `tr_de_tx` = 'Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL &reg; Datenbank-Backup.' WHERE `tr_id_pk` = '103';

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

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

/* Update the default zpanel debug mode from 'dev' to 'prod' */
UPDATE `zpanel_core`.`x_settings` SET `so_value_tx` = 'prod' WHERE `so_name_vc` = 'debug_mode';
