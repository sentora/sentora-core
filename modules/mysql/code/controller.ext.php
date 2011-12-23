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
	
    static function getCurrentDatabases() {
		$display = self::DisplayCurrentDatabases();
		return $display;		
    }

	static function getCreateDatabase(){
		$display = self::DisplayCreateDatabase();
		return $display;
	}

	#Begin Display Methods
    static function DisplayCurrentDatabases() {
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "";
		$line .= "<h2>".ui_language::translate("Current MySQL&reg Databases")."</h2>";
		$sql = "SELECT COUNT(*) FROM x_mysql WHERE my_acc_fk=" . $currentuser['userid'] . " AND my_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
		
					$sql = $zdbh->prepare("SELECT * FROM x_mysql WHERE my_acc_fk=" . $currentuser['userid'] . " AND my_deleted_ts IS NULL");
					$sql->execute();
				
	    			$line .= "<form action=\"./?module=mysql&action=EditDatabase\" method=\"post\">";
					$line .= "<table class=\"zgrid\">";
					$line .= "<tr>";
					$line .= "<th>Database name</th>";
					$line .= "<th>Size</th>";
					$line .= "<th></th>";
					$line .= "</tr>";
           		 	while ($rowmysql = $sql->fetch()) {
						$line .= "<tr>";
						$line .= "<td>".$rowmysql['my_name_vc']."</td>";
						$line .= "<td>".fs_director::ShowHumanFileSize($rowmysql['my_usedspace_bi'])."</td>";
						$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inDelete_".$rowmysql['my_id_pk']."\" id=\"inDelete_".$rowmysql['my_id_pk']."\" value=\"inDelete_".$rowmysql['my_id_pk']."\">".ui_language::translate("Delete")."</button></td>";
						$line .= "</tr>";
            		}
					$line .= "</table>";
					$line .= "<input type=\"hidden\" name=\"inAction\" value=\"delete\" />";
					$line .= "</form>";
				} else {
				$line .= "You have no packages at this time";
				}
			}
		return $line;	
    }


    static function DisplayCreateDatabase() {
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$line  = "<h2>".ui_language::translate("Create a new MySQL&reg database")."</h2>";
    	$line .= "<form action=\"./?module=mysql&action=CreateDatabase\" method=\"post\">";
        $line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Database name").":</th>";
        $line .= "<td>".$currentuser['username']."_<input name=\"inDatabase\" type=\"text\" id=\"inDatabase\" size=\"30\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th colspan=\"2\" align=\"right\">";
        $line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" >".ui_language::translate("Create")."</button></th>";
        $line .= "</tr>";
        $line .= "</table>";
    	$line .= "</form>";
		return $line;
    }		
	
	static function doCreateDatabase(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($currentuser['username']))){
			if (!fs_director::CheckForEmptyValue(self::Add_Database_To_MySQL($currentuser['username'], $controller->GetControllerRequest('FORM', 'inDatabase'), 'utf8', 'utf8_general_ci'))){
		
				$sql = $zdbh->prepare("INSERT INTO x_mysql (my_acc_fk,
										my_name_vc,
										my_created_ts) VALUES (
										" . $currentuser['userid'] . ",
										'" . $currentuser['username'] . "_" . $controller->GetControllerRequest('FORM', 'inDatabase') . "',
										" . time() . ")");		
				$sql->execute();
				self::$ok = TRUE;
				return;
			}
		}
	}

	static function CheckCreateForErrors($username) {
		global $zdbh;
		global $controller;
		$retval = FALSE;
	    # Check to make sure the database name is not blank before we go any further...
	    if ($controller->GetControllerRequest('FORM', 'inDatabase') == '') {
			self::$blank = TRUE;
			$retval = TRUE;
	    }
	    # Check to make sure the database is not a duplicate...
			$sql = "SELECT * FROM x_mysql WHERE my_name_vc='" . $username . "_".$controller->GetControllerRequest('FORM', 'inDatabase')."' AND my_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {	
					self::$alreadyexists = TRUE;
					$retval = TRUE;
				}
			}

		return $retval;
   	}

	static function doEditDatabase(){
		global $zdbh;
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = "SELECT COUNT(*) FROM x_mysql WHERE my_acc_fk=" . $currentuser['userid'] . " AND my_deleted_ts IS NULL";
		if ($numrows = $zdbh->query($sql)) {
	 		if ($numrows->fetchColumn() <> 0) {						
				$sql = $zdbh->prepare("SELECT * FROM x_mysql WHERE my_acc_fk=" . $currentuser['userid'] . " AND my_deleted_ts IS NULL");
				$sql->execute();
				while ($rowdatabase = $sql->fetch()) {
					if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$rowdatabase['my_id_pk'].''))){
						self::DeleteDatabase($rowdatabase['my_id_pk'], $rowdatabase['my_name_vc']);
					}
				}				
			}
		}
	}
	
	static function DeleteDatabase($my_id_pk, $my_name_vc){
		global $zdbh;
		if (!fs_director::CheckForEmptyValue(self::Delete_Database_From_MySQL($my_name_vc))){
		$sql = $zdbh->prepare("UPDATE x_mysql SET my_deleted_ts = '" . time() . "' WHERE my_id_pk = '".$my_id_pk."'");
		$sql->execute();
		self::$ok = TRUE;
		}
		return;
	}

	static function Add_Database_To_MySQL($username, $databasename, $charset, $collate) {
		global $zdbh;
    	$sql = $zdbh->prepare("CREATE DATABASE `" . $username . "_" . $databasename . "` DEFAULT CHARACTER SET " . $charset . " COLLATE " . $collate . ";");
		$sql->execute();
    	$sql = $zdbh->prepare("GRANT ALL PRIVILEGES ON `" . $username . "\_" . $databasename . "`.* TO '" . $username . "'@'%'");
		$sql->execute();
    	return TRUE;
	}

	static function Delete_Database_From_MySQL($databasename) {
		global $zdbh;
    	$sql = $zdbh->prepare("DROP DATABASE IF EXISTS `" . $databasename . "`;");
		$sql->execute();
    	return TRUE;
	}
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout(ui_language::translate("You need to specify a database name to create your database."));
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
		return ui_sysmessage::shout(ui_language::translate("A database with that name already appears to exsist."));
		}	
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("Changes to your databases have been saved successfully!"));
		}else{
			return ui_language::translate(ui_module::GetModuleDescription());
		}
        return;
    }

	static function getModuleName() {
		$module_name = ui_language::translate(ui_module::GetModuleName());
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "/modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

}

?>