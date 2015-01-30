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

// Deleting hMail Alias
if (!fs_director::CheckForEmptyValue(self::$delete)) {
    //$result = $mail_db->query("SELECT aliasname FROM hm_aliases WHERE aliasname='" . $rowalias['al_address_vc'] . "'")->Fetch();
    $bindArray = NULL;
    $bindArray = array(':aliasname' => $rowalias['al_address_vc']);
    $sqlStatment = $mail_db->bindQuery("SELECT aliasname FROM hm_aliases WHERE aliasname=:aliasname", $bindArray);
    $result = $mail_db->returnRow();

    if ($result) {
        $sqlStatment = "DELETE FROM hm_aliases WHERE aliasname=:aliasname";
        $sql = $mail_db->prepare($sqlStatment);
        $sql->bindParam(':aliasname', $rowalias['al_address_vc']);
        $sql->execute();
    }
}

// Adding hMail Alias
if (!fs_director::CheckForEmptyValue(self::$create)) {
    //$result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $domain . "'")->Fetch();
    $bindArray = NULL;
    $bindArray = array(':domain' => $domain);
    $sqlStatment = $mail_db->bindQuery("SELECT domainid FROM hm_domains WHERE domainname=:domain", $bindArray);
    $result = $mail_db->returnRow();

    if ($result) {
        $sqlStatment = "INSERT INTO hm_aliases (aliasdomainid,
										aliasname,
										aliasvalue,
										aliasactive) VALUES (
									 	:domainID,
									 	:fulladdress,
									 	:destination,
									 	'1')";
        $sql = $mail_db->prepare($sqlStatment);
        $sql->bindParam(':domainID', $result['domainid']);
        $sql->bindParam(':fulladdress', $fulladdress);
        $sql->bindParam(':destination', $destination);
        $sql->execute();
    }
}
?>