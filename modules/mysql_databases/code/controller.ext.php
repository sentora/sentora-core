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

	static $alreadyexists;
	static $blank;
	static $ok;
	 
    /**
     * The 'worker' methods.
     */

    static function ListDatabases($uid) {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_databases WHERE my_acc_fk=" . $uid . " AND my_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                array_push($res, array(	'mysqlid'   => $rowmysql['my_id_pk'],
									   	'mysqlname' => $rowmysql['my_name_vc'],
                    					'mysqlsize' => $rowmysql['my_usedspace_bi']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentDatabases($mysqlid) {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_databases WHERE my_id_pk=" . $mysqlid . " AND my_deleted_ts IS NULL";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                array_push($res, array(	'mysqlid'   => $rowmysql['my_id_pk'],
									   	'mysqlname' => $rowmysql['my_name_vc'],
                    					'mysqlsize' => $rowmysql['my_usedspace_bi']));
            }
            return $res;
        } else {
            return false;
        }
    }

	static function ExecuteCreateDatabase($uid, $databasename){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail($uid);
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($currentuser['username'], $databasename))) {
            return false;
        }
		runtime_hook::Execute('OnBeforeCreateDatabase');
		$sql = $zdbh->prepare("CREATE DATABASE `" . $currentuser['username'] . "_" . $databasename . "` DEFAULT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';");
		$sql->execute();
    	//$sql = $zdbh->prepare("GRANT ALL PRIVILEGES ON `" . $currentuser['username'] . "\_" . $databasename . "`.* TO '" . $currentuser['username'] . "'@'%'");
		//$sql->execute();
		$sql = $zdbh->prepare("INSERT INTO x_mysql_databases (
								my_acc_fk,
								my_name_vc,
								my_created_ts) VALUES (
								" . $currentuser['userid'] . ",
								'" . $currentuser['username'] . "_" . $databasename . "',
								" . time() . ")");		
		$sql->execute();
		runtime_hook::Execute('OnAfterCreateDatabase');
		self::$ok = true;
		return true;
	}

	static function CheckCreateForErrors($username, $databasename) {
		global $zdbh;
	    # Check to make sure the database name is not blank before we go any further...
	    if ($databasename == '') {
			self::$blank = true;
			return false;
	    }
	    # Check to make sure the database is not a duplicate...
		$sql = "SELECT COUNT(*) FROM x_mysql_databases WHERE my_name_vc='" . $username . "_".$databasename."' AND my_deleted_ts IS NULL";
		if ($numrows = $zdbh->query($sql)) {
 			if ($numrows->fetchColumn() <> 0) {	
				self::$alreadyexists = true;
				return false;
			}
		}

		return true;
   	}
	
	static function ExecuteDeleteDatabase($my_id_pk){
		global $zdbh;
		runtime_hook::Execute('OnBeforeDeleteDatabase');
		$rowmysql = $zdbh->query("SELECT my_name_vc FROM x_mysql_databases WHERE my_id_pk=" . $my_id_pk . "")->fetch(); 
		$sql = $zdbh->prepare("DROP DATABASE IF EXISTS `" . $rowmysql['my_name_vc'] . "`;");
		$sql->execute();
		$sql = $zdbh->prepare("
			UPDATE x_mysql_databases 
			SET my_deleted_ts = '" . time() . "' 
			WHERE my_id_pk = '".$my_id_pk."'");
		$sql->execute();
		runtime_hook::Execute('OnAfterDeleteDatabase');
		self::$ok = true;
		return true;
	}
	
    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */

    static function doCreateDatabase() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteCreateDatabase($currentuser['userid'], $formvars['inDatabase']))
            return true;
		return false;

    }
	
    static function doDeleteDatabase() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListDatabases($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['mysqlid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['mysqlid'] . "");
                exit;
            }
        }
        return;
    }

    static function doConfirmDeleteDatabase() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteDatabase($formvars['inDelete']))
            return true;
        return false;
    }

    static function getDatabaseList() {
		global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDatabases($currentuser['userid']);
    }
	
    static function getisDeleteDatabase() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisCreateDatabase() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getCurrentUserName() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
		return $currentuser['username'];
    }

    static function getEditCurrentDatabaseName() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDatabases($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mysqlname'];
        } else {
            return "";
        }
    }

    static function getEditCurrentDatabaseID() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDatabases($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mysqlid'];
        } else {
            return "";
        }
    }

    static function getMysqlUsagepChart() {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "";
		$mysqlquota = $currentuser['mysqlquota'];
		$mysql = fs_director::GetQuotaUsages('mysql', $currentuser['userid']);
		$total= $mysqlquota;
		$used = $mysql;
		$free = $total - $used;		
		$line .= "<img src=\"etc/lib/pChart2/zpanel/z3DPie.php?score=".$free."::".$used."&labels=Free: ".$free."::Used: ".$used."&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160\"/>";		
		return $line;
	}
		
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout(ui_language::translate("You need to specify a database name to create your database."), "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout(ui_language::translate("A database with that name already appears to exsist."), "zannounceerror");
		}	
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("Changes to your databases have been saved successfully!"), "zannounceok");
		}
        return;
    }

    static function getModuleDesc() {
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

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
     * Webinterface sudo methods.
     */
	 
}

?>