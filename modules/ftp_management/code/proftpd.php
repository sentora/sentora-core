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
$ftp_db = ctrl_options::GetSystemOption('ftp_db');
include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;
try {
    $ftp_db = new db_driver("mysql:host=" . $host . ";dbname=$ftp_db", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

// Included after acount has been created
if (!fs_director::CheckForEmptyValue(self::$create)) {
    $homedir = ctrl_options::GetSystemOption('hosted_dir') . $currentuser['username'] . $homedirectoy_to_use . "";
    $sql = $ftp_db->prepare("INSERT INTO ftpquotalimits (name, quota_type, per_session, limit_type, bytes_in_avail, bytes_out_avail, bytes_xfer_avail, files_in_avail, files_out_avail, files_xfer_avail) VALUES (:username, 'user', 'true', 'hard', 0, 0, 0, 0, 0, 0);");
    $sql->bindParam(':username', $username);
    $sql->execute();
    $sql = $ftp_db->prepare("INSERT INTO ftpuser (id, userid, passwd, homedir, shell, count, accessed, modified) VALUES ('', :username, :password, :homedir, '/sbin/nologin', 0, '', '');");
    $sql->bindParam(':username', $username);
    $sql->bindParam(':password', $password);
    $sql->bindParam(':homedir', $homedir);
    $sql->execute();
}
// Included after account is created
if (!fs_director::CheckForEmptyValue(self::$delete)) {
    $sql = $ftp_db->prepare("DELETE FROM ftpuser  WHERE userid=:userid");
    $sql->bindParam(':userid', $rowftp['ft_user_vc']);
    $sql->execute();
    $sql = $ftp_db->prepare("DELETE FROM ftpquotalimits WHERE name=:username");
    $sql->bindParam(':username', $rowftp['ft_user_vc']);
    $sql->execute();
}
// Included after account password has been reset
if (!fs_director::CheckForEmptyValue(self::$reset)) {
    $sql = $ftp_db->prepare("UPDATE ftpuser SET passwd=:password WHERE userid=:username");
    $sql->bindParam(':username', $rowftp['ft_user_vc']);
    $sql->bindParam(':password', $password);
    $sql->execute();
}
?>