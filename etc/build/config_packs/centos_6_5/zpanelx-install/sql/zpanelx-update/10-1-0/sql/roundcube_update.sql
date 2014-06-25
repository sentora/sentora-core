/* Updates to RoundCube from v.0.8.1 to 0.9.2 */
USE `zpanel_roundcube`;
ALTER TABLE `cache` DROP COLUMN `cache_id`;
ALTER TABLE `users` DROP COLUMN `alias`;
ALTER TABLE `identities` ADD INDEX `email_identities_index` (`email`, `del`);

CREATE TABLE IF NOT EXISTS `system` (
 `name` varchar(64) NOT NULL,
 `value` mediumtext,
 PRIMARY KEY(`name`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

/*!40014 SET FOREIGN_KEY_CHECKS=1 */;

INSERT INTO system (name, value) VALUES ('roundcube-version', '2013011700');
