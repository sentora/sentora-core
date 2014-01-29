/* Update SQL for Ubuntu 12.04 ZPanel 10.1.0 to 10.1.1 */
USE `zpanel_core`;

/* VERSION SPECIFIC UPDATE SQL STATEMENTS */
ALTER TABLE x_vhosts ADD vh_soaserial_vc CHAR(10) DEFAULT "AAAAMMDDSS";

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.1.1' WHERE  `so_name_vc` = 'dbversion';

/* Removal of Password Protect directories module */
DELETE FROM `zpanel_core`.`x_modules` WHERE `x_modules`.`mo_name_vc` = 'Protect Directories';