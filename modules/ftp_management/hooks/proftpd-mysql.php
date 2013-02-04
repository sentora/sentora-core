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
$result = $zdbh->query("SELECT * FROM x_settings WHERE so_name_vc='ftp_db'")->Fetch();
$ftp_db = $result['so_value_tx'];
include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;
try {
    $ftp_db = new db_driver("mysql:host=" . $host . ";dbname=$ftp_db", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

foreach ($deletedclients as $deletedclient) {
    $sql = "SELECT COUNT(*) FROM x_ftpaccounts WHERE ft_acc_fk=:deletedclient AND ft_deleted_ts IS NULL";
    $numrows = $zdbh->prepare($sql);
    $numrows->bindParam(':deletedclient', $deletedclient);
    if ($numrows->execute()) {
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_ftpaccounts WHERE ft_acc_fk=:deletedclient AND ft_deleted_ts IS NULL");
            $sql->bindParam(':deletedclient', $deletedclient);
            $sql->execute();
            while ($rowclient = $sql->fetch()) {
                $fsql = $ftp_db->prepare("DELETE FROM ftpquotalimits 
												 WHERE
												 name=:ft_user_vc");
                $fsql->bindParam(':ft_user_vc', $rowclient['ft_user_vc']);
                $fsql->execute();
                $fsql = $ftp_db->prepare("DELETE FROM ftpuser 
												 WHERE
												 userid=:ft_user_vc");
                $fsql->bindParam(':ft_user_vc', $rowclient['ft_user_vc']);
                $fsql->execute();
            }
            $sql = $zdbh->prepare("UPDATE x_ftpaccounts SET ft_deleted_ts=:time WHERE ft_acc_fk=:deletedclient");
            $sql->bindParam(':deletedclient', $deletedclient);
            $sql->bindParam(':time', time());
            $sql->execute();
        }
    }
}
?>