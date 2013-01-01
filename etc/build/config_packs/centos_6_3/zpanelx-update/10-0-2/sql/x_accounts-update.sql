/* Update SQL for CentOS 6 ZPanel 10.0.1 to 10.0.2 */
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
                WHERE column_name=ac_passsalt_vc
                and table_name=x_accounts
                and table_schema=zpanel_core
                )
        THEN
                ALTER TABLE `zpanel_core`.`x_accounts`
                ADD COLUMN `ac_passsalt_vc` VARCHAR(22) NULL DEFAULT NULL AFTER `ac_resethash_tx`;
        END IF;
end;
$$

delimiter '$$'
drop procedure AddColumnUnlessExists;
$$