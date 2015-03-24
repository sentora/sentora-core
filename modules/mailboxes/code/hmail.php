<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
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
    $mail_db = new db_driver("mysql:host=" . $host . ";dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

// Deleting hMail Mailboxes
if (!fs_director::CheckForEmptyValue(self::$delete)) {
//  $result = $mail_db->query("SELECT accountid FROM hm_accounts WHERE accountaddress=:mb_address_vc")->Fetch();
    $numrows = $mail_db->prepare("SELECT accountid FROM hm_accounts WHERE accountaddress=:mb_address_vc");
    $numrows->bindParam(':mb_address_vc', $rowmailbox['mb_address_vc']);
    $numrows->execute();
    $result = $numrows->fetch();
    if ($result) {
        $sql = $mail_db->prepare("DELETE FROM hm_accounts WHERE accountaddress=:mb_address_vc");
        $sql->bindParam(':mb_address_vc', $rowmailbox['mb_address_vc']);
        $sql->execute();
    }
}

//Saving hMail Mailboxes
if (self::$update) {
    if (!fs_director::CheckForEmptyValue($password)) {
        $sql = $mail_db->prepare("UPDATE hm_accounts SET accountpassword=:password WHERE accountaddress=:mb_address_vc");
        $password = md5($password);
        $sql->bindParam(':password', $password);
        $sql->bindParam(':mb_address_vc', $rowmailbox['mb_address_vc']);
        $sql->execute();
    }
    $sql = $mail_db->prepare("UPDATE hm_accounts SET accountactive=:enabled WHERE accountaddress=:mb_address_vc");
    $sql->bindParam(':enabled', $enabled);
    $sql->bindParam(':mb_address_vc', $rowmailbox['mb_address_vc']);
    $sql->execute();
}

// Adding hMail Mailboxes
if (!fs_director::CheckForEmptyValue(self::$create)) {
    // Lets add the domain if it does not exist for that mailbox...
    //$result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'")->Fetch();
    $numrows = $mail_db->prepare("SELECT domainid FROM hm_domains WHERE domainname=:domain");
    $numrows->bindParam(':domain', $domain);
    $numrows->execute();
    $result = $numrows->fetch();
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
												:domain,
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
												 :maxMail,
												 '',
												 '')";
        $sql = $mail_db->prepare($sql);
        $sql->bindParam(':domain', $domain);
        $maxMail = ctrl_options::GetSystemOption('max_mail_size');
        $sql->bindParam(':maxMail', $maxMail);
        $sql->execute();
    }
    # Now lets get the hMailServer domain ID...
    //$result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'")->Fetch();
    $numrows = $mail_db->prepare("SELECT domainid FROM hm_domains WHERE domainname=:domain");
    $numrows->bindParam(':domain', $domain);
    $numrows->execute();
    $result = $numrows->fetch();
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
											 	:domain_id,
											 	0,
											 	:fulladdress,
											 	:password,
											 	1,
											 	0,
											 	'',
											 	'',
											 	:maxMail,
											 	0,
											 	'',
											 	'',
											 	:hmail,
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
        $password = md5($password);
        $sql->bindParam(':password', $password);
        $sql->bindParam(':domain_id', $domain_id);
        $sql->bindParam(':fulladdress', $fulladdress);
        $maxMail = ctrl_options::GetSystemOption('max_mail_size');
        $sql->bindParam(':maxMail', $maxMail);
        $hmail = ctrl_options::GetSystemOption('hmailserver_et');
        $sql->bindParam(':hmail', $hmail);
        $sql->execute();
        # Lets grab the accountid of the mailbox...
        //$result = $mail_db->query("SELECT accountid FROM hm_accounts WHERE accountaddress='" . $fulladdress . "'")->Fetch();
        $numrows = $mail_db->prepare("SELECT accountid FROM hm_accounts WHERE accountaddress=:fulladdress");
        $numrows->bindParam(':fulladdress', $fulladdress);
        $numrows->execute();
        $result = $numrows->fetch();
        if ($result) {
            # Now we create the hm_imapfolders row...
            $sql = "INSERT INTO hm_imapfolders(folderaccountid,
												   	folderparentid,
												   	foldername,
												   	folderissubscribed,
												   	foldercreationtime,
												   	foldercurrentuid) VALUES (
												   	:accountid,
												   	-1,
												   	'INBOX',
												   	1,
												   	NOW(),
												   	1)";
            $sql = $mail_db->prepare($sql);
            $sql->bindParam(':accountid', $result['accountid']);
            $sql->execute();
        }
    }
}
?>