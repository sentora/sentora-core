/* Update SQL for CentOS 6 ZPanel 10.0.2 to 10.0.3 */
USE `zpanel_core`;

UPDATE `zpanel_core`.`x_settings` SET `so_value_tx`='/var/zpanel/logs/zpanel.log' WHERE `so_name_vc`='logfile';
ALTER TABLE `x_accounts` ADD `ac_catorder_vc` VARCHAR(255) DEFAULT NULL AFTER `ac_passsalt_vc`;

/* split crontab into seperate storage */
DELETE FROM `x_settings` WHERE `so_name_vc` = 'cron_reload';
INSERT INTO `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_command','Cron Reload Command','crontab',NULL,'Crontab binary in Linux Only','Cron Config','true');
INSERT INTO `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_path','Cron Reload Path','/var/spool/cron/apache',NULL,'Cron reload path in Linux Only','Cron Config','true');
INSERT INTO `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_flag','Cron Reload Flags','-u',NULL,'Cron reload command flags in Linux Only','Cron Config','true');
INSERT INTO `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_user','Cron Reload User','apache',NULL,'Cron reload apache user in Linux','Cron Config','true');

/* Reset theme for all users due to new theme which breaks older themes. */
UPDATE `x_accounts` SET `ac_usertheme_vc` = 'zpanelx';

/* Drop the redunent x_mysql table */
DROP TABLE IF EXISTS `zpanel_core`.`x_mysql`;

/* Adds additonal shared domains, 'autono.de' and 'zphub.com' of which users can register at bindhub.com */
UPDATE `x_settings` SET `so_value_tx` = IFNULL(CONCAT(`so_value_tx`, ',autono,zphub'), ",autono,zphub") WHERE `so_name_vc` = 'shared_domains';

/* Update the ZPanel database version number */
UPDATE `zpanel_core`.`x_settings` SET `so_value_tx` = '10.1.0' WHERE `so_name_vc` = 'dbversion';
