/* Update SQL for CentOS 6.3 ZPanel 10.1.0 to 10.1.1 */
USE `zpanel_core`;

/* VERSION SPECIFIC UPDATE SQL STATEMENTS */
ALTER TABLE x_vhosts ADD vh_soaserial_vc CHAR(10) DEFAULT "AAAAMMDDSS";

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.1.1' WHERE  `so_name_vc` = 'dbversion';

/* Removal of Password Protect directories module */
DELETE FROM `zpanel_core`.`x_modules` WHERE `x_modules`.`mo_name_vc` = 'Protect Directories';

/* Update module entries and their translations */
UPDATE  `zpanel_core`.`x_modules` SET  `mo_desc_tx` = 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.' WHERE  `mo_id_pk` = '12';
UPDATE  `zpanel_core`.`x_modules` SET  `mo_desc_tx` = 'MySQL&reg; databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg; databases.' WHERE  `mo_id_pk` = '24';
UPDATE  `zpanel_core`.`x_modules` SET  `mo_desc_tx` = 'MySQL&reg; Users allows you to add users and permissions to your MySQL&reg; databases.' WHERE  `mo_id_pk` = '39';
UPDATE  `zpanel_core`.`x_translations` SET  `tr_en_tx` = 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg; databases.' WHERE  `tr_id_pk` = '103';
UPDATE  `zpanel_core`.`x_translations` SET  `tr_de_tx` = 'Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL &reg; Datenbank-Backup.' WHERE  `tr_id_pk` = '103';

/* Update the default zpanel debug mode from 'dev' to 'prod' */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  'prod' WHERE  `so_name_vc` = 'debug_mode';

