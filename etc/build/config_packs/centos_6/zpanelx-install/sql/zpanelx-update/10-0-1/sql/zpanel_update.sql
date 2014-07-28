/* Update SQL for CentOS 6 ZPanel 10.0.0 to 10.0.1 */
USE `zpanel_core`;
/* New Postfix User for new postfix configs */
GRANT ALL PRIVILEGES ON zpanel_postfix.*
TO 'postfix'@'localhost' IDENTIFIED BY 'postfix';
FLUSH PRIVILEGES;

USE `zpanel_proftpd`;
/* Change the Uid and Gid of all FTP users to enable editing of apache owned files */
ALTER TABLE `zpanel_proftpd`.`ftpuser`
CHANGE COLUMN `uid` `uid` SMALLINT(6) NOT NULL DEFAULT '48',
CHANGE COLUMN `gid` `gid` SMALLINT(6) NOT NULL DEFAULT '48';
UPDATE `zpanel_proftpd`.`ftpuser` SET `uid`='48', `gid`='48';

USE `zpanel_core`;
/* Update the ZPanel database version number */
UPDATE `zpanel_core`.`x_settings` SET `so_value_tx` = '10.0.1' WHERE `so_name_vc` = 'dbversion';
