/* Update SQL for Windows ZPanel 10.0.0 to 10.0.1 */

/* Adds the new fields for customising vhost configs */
ALTER TABLE `zpanel_core`.`x_vhosts` 
ADD COLUMN `vh_custom_port_in` INT(6) NULL DEFAULT NULL  AFTER `vh_custom_tx` , 
ADD COLUMN `vh_portforward_in` INT(1) NULL DEFAULT '1'  AFTER `vh_custom_port_in` , 
ADD COLUMN `vh_custom_ip_vc` VARCHAR(45) NULL DEFAULT NULL  AFTER `vh_portforward_in` ;

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.0.1' WHERE  `so_name_vc` = 'dbversion';







