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

    /**
     * The 'worker' methods.
     */
    static function GroupInfo($gid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_groups WHERE ug_id_pk=:gid";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':gid', $gid);
        $numrows->execute();
        //$numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':gid', $gid);
            $res = array();
            $sql->execute();
            while ($rowgroups = $sql->fetch()) {
                array_push($res, array('groupid' => $rowgroups['ug_id_pk'], 'groupname' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_name_vc'])), 'groupdesc' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_notes_tx']))));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListGroups($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_groups WHERE ug_reseller_fk=:uid";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            while ($rowgroups = $sql->fetch()) {
                if ($rowgroups['ug_name_vc'] != "Administrators" &&
                        $rowgroups['ug_name_vc'] != "Resellers" &&
                        $rowgroups['ug_name_vc'] != "Users") {
                    $noaccs = "SELECT COUNT(*) AS total FROM x_accounts WHERE ac_group_fk=" . $rowgroups['ug_id_pk'] . "";
                    $totalnoaccs = $zdbh->query($noaccs)->fetch();
                    array_push($res, array('groupid' => $rowgroups['ug_id_pk'], 'groupname' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_name_vc'])), 'groupdesc' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_notes_tx'])), 'usersingroup' => runtime_xss::xssClean($totalnoaccs['total'])));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListDefaultGroups($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_groups WHERE ug_reseller_fk=:uid";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        //$numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            while ($rowgroups = $sql->fetch()) {
                if ($rowgroups['ug_name_vc'] == "Administrators" ||
                        $rowgroups['ug_name_vc'] == "Resellers" ||
                        $rowgroups['ug_name_vc'] == "Users") {
                    $noaccs = "SELECT COUNT(*) AS total FROM x_accounts WHERE ac_group_fk=" . $rowgroups['ug_id_pk'] . "";
                    $totalnoaccs = $zdbh->query($noaccs)->fetch();
                    array_push($res, array('groupid' => $rowgroups['ug_id_pk'], 'groupname' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_name_vc'])), 'groupdesc' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_notes_tx'])), 'usersingroup' => runtime_xss::xssClean($totalnoaccs['total'])));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function GroupMoveTo($uid, $gid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_groups WHERE ug_reseller_fk=:uid AND ug_id_pk <> :gid";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->bindParam(':gid', $gid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $sql->bindParam(':gid', $gid);
            $res = array();
            $sql->execute();
            while ($rowgroups = $sql->fetch()) {
                array_push($res, array('groupid' => $rowgroups['ug_id_pk'], 'groupname' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_name_vc'])), 'groupdesc' => ui_language::translate(runtime_xss::xssClean($rowgroups['ug_notes_tx']))));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExectuteCreateGroup($name, $desc, $uid)
    {
        global $zdbh;
        if (!fs_director::CheckForEmptyValue($name)) {
            $sql = $zdbh->prepare("INSERT INTO x_groups (ug_name_vc, ug_notes_tx, ug_reseller_fk) VALUES (:name, :desc, :uid)");
            $sql->bindParam(':name', $name);
            $sql->bindParam(':desc', $desc);
            $sql->bindParam(':uid', $uid);
            $sql->execute();
        }
        return true;
    }

    static function ExectuteUpdateGroup($gid, $name, $desc)
    {
        global $zdbh;
        $sql = $zdbh->prepare("UPDATE x_groups SET ug_name_vc = :name, ug_notes_tx = :desc WHERE ug_id_pk = :groupid");
        $sql->bindParam(':name', $name);
        $sql->bindParam(':desc', $desc);
        $sql->bindParam(':groupid', $gid);
        $sql->execute();
        return true;
    }

    static function ExecuteDeleteGroup($gid, $mgid = "")
    {
        global $zdbh;
        if ($mgid != "") {
            $sql = $zdbh->prepare("
            UPDATE x_accounts
            SET ac_group_fk = :mgid
            WHERE ac_group_fk = :gid");
            $sql->bindParam(':mgid', $mgid);
            $sql->bindParam(':gid', $gid);
            $sql->execute();
            $sql = $zdbh->prepare("
            DELETE FROM x_groups
            WHERE ug_id_pk = :gid");
            $sql->bindParam(':gid', $gid);
            $sql->execute();
            return true;
        } else {
            $sql = $zdbh->prepare("
            DELETE FROM x_groups
            WHERE ug_id_pk = :gid");
            $sql->bindParam(':gid', $gid);
            $sql->execute();
            return true;
        }
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function getGroupList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListGroups($currentuser['userid']);
    }

    static function getDefaultGroupList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDefaultGroups($currentuser['userid']);
    }

    static function getGroupMoveToList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $urlvars = $controller->GetAllControllerRequests('URL');
        return self::GroupMoveTo($currentuser['userid'], $urlvars['other']);
    }

    static function doCreateGroup()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExectuteCreateGroup($formvars['inGroupName'], $formvars['inDesc'], $currentuser['userid'])) {
            return true;
        } else {
            return false;
        }
        return;
    }

    static function doEditGroup()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListGroups($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['groupid'] . ''])) {
                header("location: ./?module=" . runtime_xss::xssClean($controller->GetCurrentModule()) . "&show=Delete&other=" . runtime_xss::xssClean($row['groupid']) . "");
                exit;
            }
            if (isset($formvars['inEdit_' . $row['groupid'] . ''])) {
                header("location: ./?module=" . runtime_xss::xssClean($controller->GetCurrentModule()) . "&show=Edit&other=" . runtime_xss::xssClean($row['groupid']) . "");
                exit;
            }
        }
        return;
    }

    static function doDeleteGroup()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inMoveGroup'])) {
            $inMoveGroup = $formvars['inMoveGroup'];
        } else {
            $inMoveGroup = "";
        }
        if (self::ExecuteDeleteGroup($formvars['inGroupID'], $inMoveGroup))
            return true;
        return false;
    }

    static function doUpdateGroup()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExectuteUpdateGroup($formvars['inGroupID'], $formvars['inGroupName'], $formvars['inDesc']))
            return true;
        return false;
    }

    static function getisCreateGroup()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getisDeleteGroup()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisEditGroup()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit"))
            return true;
        return false;
    }

    static function getCurrentID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::GroupInfo($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['groupid'];
        } else {
            return "";
        }
    }

    static function getEditCurrentName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::GroupInfo($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['groupname'];
        } else {
            return "";
        }
    }

    static function getEditCurrentDesc()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::GroupInfo($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['groupdesc'];
        } else {
            return "";
        }
    }

    /**
     * Webinterface sudo methods.
     */
}
