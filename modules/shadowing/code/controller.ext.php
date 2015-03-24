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
class module_controller extends ctrl_module
{

    static $shout;

    static function getShadowAccounts()
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['username'] == 'zadmin') {
            $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc";
            $numrows = $zdbh->prepare($sql);
            $numrows->execute();
        } else {
            $sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk = :userid AND ac_deleted_ts IS NULL ORDER BY ac_user_vc";
            $numrows = $zdbh->prepare($sql);
            $numrows->bindParam(':userid', $currentuser['userid']);
            $numrows->execute();
        }

        //$numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            if ($currentuser['username'] == 'zadmin') {
                //noi bind needed
            } else {
                //bind the username
                $sql->bindParam(':userid', $currentuser['userid']);
            }
            $res = array();
            $sql->execute();
            while ($rowclients = $sql->fetch()) {
                if ($rowclients['ac_id_pk'] != $currentuser['userid']) {
                    $clientdetail = ctrl_users::GetUserDetail($rowclients['ac_id_pk']);
                    array_push($res, array('clientusername' => $clientdetail['username'],
                        'clientid' => $rowclients['ac_id_pk'],
                        'packagename' => $clientdetail['packagename'],
                        'usergroup' => $clientdetail['usergroup'],
                        'currentdisk' => fs_director::ShowHumanFileSize(ctrl_users::GetQuotaUsages('diskspace', $rowclients['ac_id_pk'])),
                        'currentbandwidth' => fs_director::ShowHumanFileSize(ctrl_users::GetQuotaUsages('bandwidth', $rowclients['ac_id_pk']))));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function doShadowUser()
    {
        global $zdbh;
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['username'] == 'zadmin') {
            $sql = "SELECT * FROM x_accounts WHERE ac_deleted_ts IS NULL ORDER BY ac_user_vc";
            $numrows = $zdbh->prepare($sql);
        } else {
            $sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk = :userid AND ac_deleted_ts IS NULL";
            $numrows = $zdbh->prepare($sql);
            $numrows->bindParam(':userid', $currentuser['userid']);
        }
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare($sql);
                if ($currentuser['username'] == 'zadmin') {
                    //no bind needed
                } else {
                    //bind the username
                    $sql->bindParam(':userid', $currentuser['userid']);
                }
                $sql->execute();
                while ($rowclients = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inShadow_' . $rowclients['ac_id_pk']))) {
                        ctrl_auth::KillCookies();
                        ctrl_auth::SetSession('ruid', $currentuser['userid']);
                        ctrl_auth::SetUserSession($rowclients['ac_id_pk'], runtime_sessionsecurity::getSessionSecurityEnabled());
                        header("location: /");
                        exit;
                    }
                }
            }
        }
    }

}
