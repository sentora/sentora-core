<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 		$mailserver_db = self::GetMailOption('mailserver_db');
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
  			$mail_db = new db_driver("mysql:host=localhost;dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {
	
		}

		// Adding hMail Mailboxes
		if (!fs_director::CheckForEmptyValue(self::$create)) {
			$result = $mail_db->query("SELECT username FROM mailbox WHERE username='" . $fulladdress . "'")->Fetch();
			if (!$result) {
            $sql = $mail_db->prepare("INSERT INTO mailbox (username,
								 							password,
														 	name,
															maildir,
														 	local_part,
														 	quota,
														 	domain,
														 	created,
														 	modified,
														 	active) VALUES (
														 	'" . $fulladdress . "',
														 	'{PLAIN-MD5}" . md5($password) . "',
														 	'" . $address . "',
														 	'" . $domain . "/" . $address . "/',
														 	'" . $address . "',
														 	'" . self::GetMailOption('max_mail_size') . "',
														 	'" . $domain . "',
														 	NOW(),
														 	NOW(),
														 	'1')");
            $sql->execute();
            $sql = $mail_db->prepare("INSERT INTO alias  (address,
														 	goto,
														 	domain,
															created,
														 	modified,
														 	active) VALUES (
														 	'" . $fulladdress . "',
														 	'" . $fulladdress . "',
														 	'" . $domain . "',
														 	NOW(),
														 	NOW(),
														 	'1')");
			$sql->execute();
			}
		}
		
		// Deleting hMail Mailboxes
		if (!fs_director::CheckForEmptyValue(self::$delete)) {
        	$sql = $mail_db->prepare("DELETE FROM mailbox WHERE username='" . $rowmailbox['mb_address_vc'] . "'");
            $sql->execute();
            $sql = $mail_db->prepare("DELETE FROM alias WHERE address='" . $rowmailbox['mb_address_vc'] . "'");
			$sql->execute();
		}
				
		//Saving hMail Mailboxes
		if (!fs_director::CheckForEmptyValue(self::$update)) {
			if (!fs_director::CheckForEmptyValue($password)) {
            	$sql = $mail_db->prepare("UPDATE mailbox SET password='{PLAIN-MD5}" . md5($password) . "' WHERE username='" . $rowmailbox['mb_address_vc'] . "'");
				$sql->execute();		
			}
            $sql = $mail_db->prepare("UPDATE mailbox SET active=".$enabled." WHERE username='" . $rowmailbox['mb_address_vc'] . "'");
			$sql->execute();
		}			
?>