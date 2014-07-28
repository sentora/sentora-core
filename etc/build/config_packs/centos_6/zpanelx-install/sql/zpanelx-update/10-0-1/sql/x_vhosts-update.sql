/* Update SQL for CentOS 6 ZPanel 10.0.0 to 10.0.1 */
USE `zpanel_core`;
/* Adds the new fields for customising vhost configs */

DELIMITER $$

USE `zpanel_core`; $$

DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
CREATE PROCEDURE AddColumnUnlessExists()
BEGIN

-- add a column safely
IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='zpanel_core'
        AND COLUMN_NAME='vh_custom_port_in' AND TABLE_NAME='x_vhosts') ) THEN
    ALTER TABLE `x_vhosts`
ADD COLUMN `vh_custom_port_in` INT(6)
    NULL DEFAULT NULL AFTER `vh_custom_tx`;
END IF;

END $$

CALL AddColumnUnlessExists() $$
DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
DELIMITER ;



DELIMITER $$

USE `zpanel_core`; $$

DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
CREATE PROCEDURE AddColumnUnlessExists()
BEGIN

-- add a column safely
IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='zpanel_core'
        AND COLUMN_NAME='vh_portforward_in' AND TABLE_NAME='x_vhosts') ) THEN
    ALTER TABLE `x_vhosts`
ADD COLUMN `vh_portforward_in` VARCHAR(22)
NULL DEFAULT '1' AFTER `vh_custom_port_in`;
END IF;

END $$

CALL AddColumnUnlessExists() $$
DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
DELIMITER ;




DELIMITER $$

USE `zpanel_core`; $$

DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
CREATE PROCEDURE AddColumnUnlessExists()
BEGIN

-- add a column safely
IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='zpanel_core'
        AND COLUMN_NAME='vh_custom_ip_vc' AND TABLE_NAME='x_vhosts') ) THEN
    ALTER TABLE `x_vhosts`
ADD COLUMN `vh_custom_ip_vc` VARCHAR(22)
NULL DEFAULT NULL AFTER `vh_portforward_in`;
END IF;

END $$

CALL AddColumnUnlessExists() $$
DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
DELIMITER ;
