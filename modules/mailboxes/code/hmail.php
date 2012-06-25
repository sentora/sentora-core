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
 
 		$mailserver_db = ctrl_options::GetSystemOption('mailserver_db');
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
  			$mail_db = new db_driver("mysql:host=localhost;dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {
	
		}

		// Deleting hMail Mailboxes
		if (!fs_director::CheckForEmptyValue(self::$delete)) {
	    	$result = $mail_db->query("SELECT accountid FROM hm_accounts WHERE accountaddress='" . $rowmailbox['mb_address_vc'] . "'")->Fetch();
			if ($result) {
				$sql = $mail_db->prepare("DELETE FROM hm_accounts WHERE accountaddress='" . $rowmailbox['mb_address_vc'] . "'");
				$sql->execute();
			}
		}
					
		//Saving hMail Mailboxes
		if (self::$update) {
			if (!fs_director::CheckForEmptyValue($password)) {
				$sql = $mail_db->prepare("UPDATE hm_accounts SET accountpassword='" . md5($password) . "' WHERE accountaddress='" . $rowmailbox['mb_address_vc'] . "'");
				$sql->execute();
			}
			$sql = $mail_db->prepare("UPDATE hm_accounts SET accountactive=" . $enabled . " WHERE accountaddress='" . $rowmailbox['mb_address_vc'] . "'");
			$sql->execute();
		}
		
		// Adding hMail Mailboxes
		if (!fs_director::CheckForEmptyValue(self::$create)) {
			// Lets add the domain if it does not exist for that mailbox...
            $result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'")->Fetch();
			if (!$result) {
   				 $sql = "INSERT INTO hm_domains(domainname,
												domainactive,
												domainpostmaster,
												domainmaxsize,
												domainaddomain,
												domainmaxmessagesize,
												domainuseplusaddressing,
												domainplusaddressingchar,
												domainantispamoptions,
												domainenablesignature,
												domainsignaturemethod,
												domainsignatureplaintext,
												domainsignaturehtml,
												domainaddsignaturestoreplies,
												domainaddsignaturestolocalemail,
												domainmaxnoofaccounts,
												domainmaxnoofaliases,
												domainmaxnoofdistributionlists,
												domainlimitationsenabled,
												domainmaxaccountsize,
												domaindkimselector,
												domaindkimprivatekeyfile) VALUES (
												'" . $domain . "',
												 1,
												 '',
												 0,
												 '',
												 0,
												 0,
												 '',
												 0,
												 0,
												 1,
												 '',
												 '',
												 0,
												 0,
												 0,
												 0,
												 0,
												 0,
												 " . ctrl_options::GetSystemOption('max_mail_size') . ",
												 '',
												 '')";
				$sql = $mail_db->prepare($sql);
				$sql->execute();			
			}
        	# Now lets get the hMailServer domain ID...
	        $result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'")->Fetch();
			if ($result) {
	           	$domain_id = $result['domainid'];
	            # Now we insert the mailbox data into the hMailServer database...
	            $sql = "INSERT INTO hm_accounts (accountdomainid,
											 	accountadminlevel,
											 	accountaddress,
											 	accountpassword,
											 	accountactive,
											 	accountisad,
											 	accountaddomain,
											 	accountadusername,
											 	accountmaxsize,
											 	accountvacationmessageon,
											 	accountvacationmessage,
											 	accountvacationsubject,
											 	accountpwencryption,
											 	accountforwardenabled,
											 	accountforwardaddress,
											 	accountforwardkeeporiginal,
											 	accountenablesignature,
											 	accountsignatureplaintext,
											 	accountsignaturehtml,
											 	accountlastlogontime,
											 	accountvacationexpires,
											 	accountvacationexpiredate,
											 	accountpersonfirstname,
											 	accountpersonlastname) VALUES (
											 	" . $domain_id . ",
											 	0,
											 	'" . $fulladdress . "',
											 	'" . md5($password) . "',
											 	1,
											 	0,
											 	'',
											 	'',
											 	" . ctrl_options::GetSystemOption('max_mail_size') . ",
											 	0,
											 	'',
											 	'',
											 	" . ctrl_options::GetSystemOption('hmailserver_et') . ",
											 	0,
											 	'',
											 	0,
											 	0,
											 	'',
											 	'',
											 	'',
											 	0,
											 	'',
											 	'',
											 	'')";
				$sql = $mail_db->prepare($sql);
				$sql->execute();
	            # Lets grab the accountid of the mailbox...
	            $result = $mail_db->query("SELECT accountid FROM hm_accounts WHERE accountaddress='" . $fulladdress . "'")->Fetch();
				if ($result) {
	            	# Now we create the hm_imapfolders row...
		            $sql = "INSERT INTO hm_imapfolders(folderaccountid,
												   	folderparentid,
												   	foldername,
												   	folderissubscribed,
												   	foldercreationtime,
												   	foldercurrentuid) VALUES (
												   	" . $result['accountid'] . ",
												   	-1,
												   	'INBOX',
												   	1,
												   	NOW(),
												   	1)";
					$sql = $mail_db->prepare($sql);
					$sql->execute();
				}
			}
		}			
?>