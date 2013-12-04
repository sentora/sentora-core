/* Update SQL for Windows ZPanel 10.0.2 to 10.0.3 */
USE `zpanel_core`;

/* VERSION SPECIFIC UPDATE SQL STATEMENTS */
ALTER TABLE x_vhosts ADD vh_soaserial_vc CHAR(10) DEFAULT "AAAAMMDDSS";

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.1.1' WHERE  `so_name_vc` = 'dbversion';
