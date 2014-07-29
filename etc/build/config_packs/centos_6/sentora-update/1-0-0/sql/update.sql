/* Update Sentora Version */
USE `zpanel_core`;
/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET `so_value_tx` = '1.0.0' WHERE `so_name_vc` = 'dbversion';

/* Add new Protected Directories module */
INSERT INTO `x_modules` (`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) VALUES (3,'Protected Directories',200,'protected_directories','user','Password protect your web applications and directories.',NULL,'true','','');


INSERT INTO `x_permissions`(`pe_group_fk`,`pe_module_fk`) VALUES (1, (SELECT `mo_id_pk` FROM `x_modules` WHERE `mo_folder_vc` = 'protected_directories' LIMIT 1));
INSERT INTO `x_permissions`(`pe_group_fk`,`pe_module_fk`) VALUES (2, (SELECT `mo_id_pk` FROM `x_modules` WHERE `mo_folder_vc` = 'protected_directories' LIMIT 1));
INSERT INTO `x_permissions`(`pe_group_fk`,`pe_module_fk`) VALUES (3, (SELECT `mo_id_pk` FROM `x_modules` WHERE `mo_folder_vc` = 'protected_directories' LIMIT 1));


CREATE TABLE `x_zvps_htpasswd_file` (
  `x_zvps_htpasswd_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `x_zvps_htpasswd_file_target` varchar(255) NOT NULL,
  `x_zvps_htpasswd_file_message` varchar(255) NOT NULL,
  `x_zvps_htpasswd_file_created` int(11) NOT NULL,
  `x_zvps_htpasswd_file_deleted` int(11) DEFAULT NULL,
  `x_zvps_htpasswd_zpanel_user_id` int(11) NOT NULL,
  PRIMARY KEY (`x_zvps_htpasswd_file_id`),
  UNIQUE KEY `x_zvps_htpasswd_file_target` (`x_zvps_htpasswd_file_target`),
  KEY `x_zvps_htpasswd_file_x_zvps_htpasswd_zpanel_user_id_idx` (`x_zvps_htpasswd_zpanel_user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `x_zvps_htpasswd_mapper` (
  `x_zvps_htpasswd_mapper_id` int(11) NOT NULL AUTO_INCREMENT,
  `x_zvps_htpasswd_file_id` int(11) NOT NULL,
  `x_zvps_htpasswd_user_id` int(11) NOT NULL,
  PRIMARY KEY (`x_zvps_htpasswd_mapper_id`),
  KEY `x_zvps_htpasswd_mapper_x_zvps_htpasswd_file_id_idx` (`x_zvps_htpasswd_file_id`),
  KEY `x_zvps_htpasswd_mapper_x_zvps_htpasswd_user_id_idx` (`x_zvps_htpasswd_user_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE `x_zvps_htpasswd_user` (
  `x_zvps_htpasswd_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `x_zvps_htpasswd_user_username` varchar(255) NOT NULL,
  `x_zvps_htpasswd_user_password` varchar(255) NOT NULL,
  `x_zvps_htpasswd_user_created` int(11) NOT NULL,
  `x_zvps_htpasswd_user_deleted` int(11) DEFAULT NULL,
  `x_zvps_htpasswd_zpanel_user_id` int(11) NOT NULL,
  PRIMARY KEY (`x_zvps_htpasswd_user_id`),
  UNIQUE KEY `x_zvps_htpasswd_user_username` (`x_zvps_htpasswd_user_username`),
  UNIQUE KEY `x_zvps_htpasswd_user_password` (`x_zvps_htpasswd_user_password`)
) DEFAULT CHARSET=utf8;




