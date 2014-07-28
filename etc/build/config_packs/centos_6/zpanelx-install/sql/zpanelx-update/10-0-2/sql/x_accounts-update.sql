/* Update SQL for CentOS 6 ZPanel 10.0.1 to 10.0.2 */
/* Adds the new fields for account password changes */
DELIMITER $$

USE `zpanel_core`; $$

DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
CREATE PROCEDURE AddColumnUnlessExists()
BEGIN

-- add a column safely
IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='zpanel_core'
        AND COLUMN_NAME='ac_passsalt_vc' AND TABLE_NAME='x_accounts') ) THEN
    ALTER TABLE `x_accounts`
ADD COLUMN `ac_passsalt_vc` VARCHAR(22)
NULL DEFAULT NULL AFTER `ac_resethash_tx`;
END IF;

END $$

CALL AddColumnUnlessExists() $$
DROP PROCEDURE IF EXISTS AddColumnUnlessExists $$
DELIMITER ;
