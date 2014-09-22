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

/** separate port setting for panel */
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('sentora_port','Sentora Apache Port','80',NULL,'Sentora Apache panel port','ZPanel Config','true');

/* Fix for changed translated text */
/* '' = ' (escaped) */
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'The A record contains an IPv4 address. Its target is an IPv4 address, e.g. ''192.168.1.1''.' WHERE `tr_en_tx` = 'The A record contains an IPv4 address. It''s target is an IPv4 address, e.g. ''192.168.1.1''.';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'The AAAA record contains an IPv6 address. Its target is an IPv6 address, e.g. ''2607:fe90:2::1''.' WHERE `tr_en_tx` = 'The AAAA record contains an IPv6 address. It''s target is an IPv6 address, e.g. ''2607:fe90:2::1''.';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'The CNAME record specifies the canonical name of a record. Its target is a fully qualified domain name, e.g. ''webserver-01.example.com''.' WHERE `tr_en_tx` = 'The CNAME record specifies the canonical name of a record. It''s target is a fully qualified domain name, e.g. ''webserver-01.example.com''.';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535. Its target is a fully qualified domain name, e.g. ''mail.example.com''.' WHERE `tr_en_tx` = 'The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535. It''s target is a fully qualified domain name, e.g. ''mail.example.com''.';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'SRV records can be used to encode the location and port of services on a domain name. Its target is a fully qualified domain name, e.g. ''host.example.com''.' WHERE `tr_en_tx` = 'SRV records can be used to encode the location and port of services on a domain name. It''s target is a fully qualified domain name, e.g. ''host.example.com''.';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'SPF records is used to store Sender Policy Framework details. Its target is a text string, e.g.<br>''v=spf1 a:192.168.1.1 include:example.com mx ptr -all'' (Click <a href="http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/" target="_blank">HERE</a> for the Microsoft SPF Wizard.)' WHERE `tr_en_tx` = 'SPF records is used to store Sender Policy Framework details. It''s target is a text string, e.g.<br>''v=spf1 a:192.168.1.1 include:example.com mx ptr -all'' (Click <a href="http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/" target="_blank">HERE</a> for the Microsoft SPF Wizard.)';
UPDATE  `zpanel_core`.`x_translations` SET `tr_en_tx` = 'Nameserver record. Specifies nameservers for a domain. Its target is a fully qualified domain name, e.g. ''ns1.example.com''. The records should match what the domain name has registered with the internet root servers.' WHERE `tr_en_tx` = 'Nameserver record. Specifies nameservers for a domain. It''s target is a fully qualified domain name, e.g. ''ns1.example.com''. The records should match what the domain name has registered with the internet root servers.';

