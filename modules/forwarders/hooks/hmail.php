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
    $mail_db = new db_driver("mysql:host=" . $host . ";dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

foreach ($deletedclients as $deletedclient) {
    //$sql = "SELECT * FROM x_forwarders WHERE fw_acc_fk=" . $deletedclient . " AND fw_deleted_ts IS NULL";
    //$numrows = $zdbh->query($sql);   
    $numrows = $zdbh->prepare("SELECT * FROM x_forwarders WHERE fw_acc_fk=:deletedclient AND fw_deleted_ts IS NULL");
    $numrows->bindParam(':deletedclient', $deletedclient);
    $numrows->execute();

    if ($numrows->fetchColumn() <> 0) {
        $sql = $zdbh->prepare("SELECT * FROM x_forwarders WHERE fw_acc_fk=:deletedclient AND fw_deleted_ts IS NULL");
        $sql->bindParam(':deletedclient', $deletedclient);
        $sql->execute();
        while ($rowforwarder = $sql->fetch()) {
            //$result = $mail_db->query("SELECT accountaddress FROM hm_accounts WHERE accountaddress='" . $rowforwarder['fw_address_vc'] . "'")->Fetch();            
            $numrows = $mail_db->prepare("SELECT accountaddress FROM hm_accounts WHERE accountaddress=:fw_address_vc");
            $numrows->bindParam(':fw_address_vc', $rowforwarder['fw_address_vc']);
            $numrows->execute();
            $result = $numrows->fetch();

            if ($result) {
                $sql = "DELETE * FROM hm_accounts WHERE accountaddress=:fw_address_vc";
                $sql = $mail_db->prepare($sql);
                $sql->bindParam(':fw_address_vc', $rowforwarder['fw_address_vc']);
                $sql->execute();
            }
        }
    }
}
?>