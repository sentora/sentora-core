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
class module_controller {

    static function getModuleName() {
        $module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    /**
     * The 'worker' methods.
     */
    static function ListGroups($uid) {
        global $zdbh;
        $sql = "SELECT * FROM x_groups WHERE ug_reseller_fk=" . $uid . "";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowgroups = $sql->fetch()) {
                array_push($res, array('groupid' => $rowgroups['ug_id_pk'], 'groupname' => ui_language::translate($rowgroups['ug_name_vc']), 'groupdesc' => ui_language::translate($rowgroups['ug_notes_tx'])));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExectuteCreateGroup($name, $desc, $uid) {
        global $zdbh;
        $sql = $zdbh->prepare("
            INSERT INTO x_groups (
            ug_name_vc,
            ug_notes_tx,
            ug_reseller_fk
            ) VALUES (
            '" . $name . "',
            '" . $desc . "',
            " . $uid . ")
            ");
        $sql->execute();
        return true;
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function getGroupList() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListGroups($currentuser['userid']);
    }

    static function doCreateGroup() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExectuteCreateGroup($formvars['inGroupName'], $formvars['inDesc'], $currentuser['userid'])) {
            
        } else {
            return false;
        }
        return;
    }

    static function doEditGroup() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        /* if (self::ExectuteCreateGroup($formvars['inGroupName'], $formvars['inDesc'], $currentuser['userid'])) {

          } else {
          return false;
          } */
        return;
    }

    static function getisCreateGroup() {
        global $controller;
        if ($controller->GetAction() == "EditGroup")
            return false;
        return true;
    }

    static function getisDeleteGroup() {
        global $controller;
        if ($controller->GetAction()) {
            if ($controller->GetAction() == "EditGroup")
                return true;
            return false;
        }
    }

    /**
     * Webinterface sudo methods.
     */
}

?>