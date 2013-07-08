/* Update SQL for Ubuntu 12.04 ZPanel 10.0.2 to 10.0.3 */
USE `zpanel_core`;

UPDATE `zpanel_core`.`x_settings` SET `so_value_tx`='/var/zpanel/logs/zpanel.log' WHERE `so_name_vc`='logfile';

/* split crontab into seperate storage */
DELETE FROM `x_settings` WHERE `so_name_vc` = 'cron_reload';
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_command','Cron Reload Command','crontab',NULL,'Crontab binary in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_path','Cron Reload Path','/var/spool/cron/crontabs/www-data',NULL,'Cron reload path in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_flag','Cron Reload Flags','-u',NULL,'Cron reload command flags in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('cron_reload_user','Cron Reload User','www-data',NULL,'Cron reload apache user in Linux','Cron Config','true');

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.0.3' WHERE  `so_name_vc` = 'dbversion';
ALTER TABLE  `x_accounts` ADD  `ac_catorder_vc` VARCHAR(255) DEFAULT NULL AFTER  `ac_passsalt_vc`;

/* Drop the redunent x_mysql table */
DROP TABLE IF EXISTS `zpanel_core`.`x_mysql`;