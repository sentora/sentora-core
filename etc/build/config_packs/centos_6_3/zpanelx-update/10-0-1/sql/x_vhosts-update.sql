/* Update SQL for CentOS 6 ZPanel 10.0.0 to 10.0.1 */
USE `zpanel_core`;
/* Adds the new fields for customising vhost configs */

delimiter '$$'
create procedure AddColumnUnlessExists(
        IN dbName tinytext,
        IN tableName tinytext,
        IN fieldName tinytext,
        IN fieldDef text)
begin
        IF NOT EXISTS (
                SELECT * FROM information_schema.COLUMNS
                WHERE column_name=vh_custom_port_in
                and table_name=x_vhosts
                and table_schema=zpanel_core
                )
        THEN
                ALTER TABLE `zpanel_core`.`x_vhosts`
                ADD COLUMN `vh_custom_port_in` INT(6) NULL DEFAULT NULL  AFTER `vh_custom_tx`;
        END IF;
end;
$$

delimiter '$$'
drop procedure AddColumnUnlessExists;
$$

delimiter '$$'
create procedure AddColumnUnlessExists(
        IN dbName tinytext,
        IN tableName tinytext,
        IN fieldName tinytext,
        IN fieldDef text)
begin
        IF NOT EXISTS (
                SELECT * FROM information_schema.COLUMNS
                WHERE column_name=vh_portforward_in
                and table_name=x_vhosts
                and table_schema=zpanel_core
                )
        THEN
                ALTER TABLE `zpanel_core`.`x_vhosts`
                ADD COLUMN `vh_portforward_in` INT(1) NULL DEFAULT '1'  AFTER `vh_custom_port_in`;
        END IF;
end;
$$

delimiter '$$'
drop procedure AddColumnUnlessExists;
$$

delimiter '$$'
create procedure AddColumnUnlessExists(
        IN dbName tinytext,
        IN tableName tinytext,
        IN fieldName tinytext,
        IN fieldDef text)
begin
        IF NOT EXISTS (
                SELECT * FROM information_schema.COLUMNS
                WHERE column_name=vh_custom_ip_vc
                and table_name=x_vhosts
                and table_schema=zpanel_core
                )
        THEN
                ALTER TABLE `zpanel_core`.`x_vhosts`
                ADD COLUMN `vh_custom_ip_vc` VARCHAR(45) NULL DEFAULT NULL  AFTER `vh_portforward_in`;
        END IF;
end;
$$

delimiter '$$'
drop procedure AddColumnUnlessExists;
$$