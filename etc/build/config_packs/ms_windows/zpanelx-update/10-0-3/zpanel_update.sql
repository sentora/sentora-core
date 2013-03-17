/* Update SQL for Windows ZPanel 10.0.2 to 10.0.3 */
USE `zpanel_core`;

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.0.3' WHERE  `so_name_vc` = 'dbversion';

/* Drop the redunent x_mysql table */
DROP TABLE IF EXISTS `zpanel_core`.`x_mysql`;