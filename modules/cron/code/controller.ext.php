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

	static $error;
	static $noexists;
	static $alreadyexists;
	static $blank;
	static $ok;

	static function getCrons(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();

		$sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=" . $currentuser['userid'] . " AND ct_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {
							
				$sql = $zdbh->prepare("SELECT * FROM x_cronjobs WHERE ct_acc_fk=" . $currentuser['userid'] . " AND ct_deleted_ts IS NULL");
				$sql->execute();
		
    			$line  = "<form action=\"./?module=cron&action=DeleteCron\" method=\"post\">";
        		$line .= "<table class=\"zgrid\">";
            	$line .= "<tr>";
                $line .= "<th>".ui_language::translate("Script")."</th>";
                $line .= "<th>".ui_language::translate("Description")."</th>";
                $line .= "<th></th>";
            	$line .= "</tr>";
            	while ($rowcrons = $sql->fetch()) {
                	$line .= "<tr>";
                	$line .= "<td>" . $rowcrons['ct_script_vc'] . "</td>";
                	$line .= "<td>" . $rowcrons['ct_description_tx'] . "</td>";
                	$line .= "<td><button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inDelete_" . $rowcrons['ct_id_pk'] . "\" id=\"button\" value=\"inDelete_" . $rowcrons['ct_id_pk'] . "\">".ui_language::translate("Delete")."</button></td>";
                	$line .= "</tr>";
             	}
        		$line .= "</table>";
    			$line .= "</form>";

				} else {
    			$line = "<h2>".ui_language::translate("You currently do no have any tasks setup.")."</h2>";
				}
				return $line;
			}

	}
	
	static function getCreateCron(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();

		$line  = "<h2>Create a new task</h2>";
		$line .= "<form action=\"./?module=cron&action=CreateCron\" method=\"post\">";
    	$line .= "<table class=\"zform\">";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Script").":</th>";
        $line .= "<td><input name=\"inScript\" type=\"text\" id=\"inScript\" size=\"50\" /><br />".ui_language::translate("example").": /folder/task.php</td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Comment").":</th>";
        $line .= "<td><input name=\"inDescription\" type=\"text\" id=\"inDescription\" size=\"50\" maxlength=\"50\" /></td>";
        $line .= "</tr>";
        $line .= "<tr>";
        $line .= "<th>".ui_language::translate("Executed").":</th>";
        $line .= "<td><select name=\"inTiming\" id=\"inTiming\">";
        $line .= "<option value=\"* * * * *\">".ui_language::translate("Every 1 minute")."</option>";
        $line .= "<option value=\"0,5,10,15,20,25,30,35,40,45,50,55 * * * *\">".ui_language::translate("Every 5 minutes")."</option>";
		$line .= "<option value=\"0,10,20,30,40,50 * * * *\">".ui_language::translate("Every 10 minutes")."</option>";
		$line .= "<option value=\"0,30 * * * *\">".ui_language::translate("Every 30 minutes")."</option>";
		$line .= "<option value=\"0 * * * *\">".ui_language::translate("Every 1 hour")."</option>";
		$line .= "<option value=\"0 0,2,4,6,8,10,12,14,16,18,20,22 * * *\">".ui_language::translate("Every 2 hours")."</option>";
		$line .= "<option value=\"0 0,8,16 * * *\">".ui_language::translate("Every 8 hours")."</option>";
		$line .= "<option value=\"0 0,12 * * *\">".ui_language::translate("Every 12 hours")."</option>";
		$line .= "<option value=\"0 0 * * *\">".ui_language::translate("Every 1 day")."</option>";
		$line .= "<option value=\"0 0 * * 0\">".ui_language::translate("Every week")."</option>";
		$line .= "</select></td>";
		$line .= "</tr>";
		$line .= "<tr>";
		$line .= "<th colspan=\"2\" align=\"right\"><input type=\"hidden\" name=\"inReturn\" value=\"GetFullURL\" />";
		$line .= "<input type=\"hidden\" name=\"inUserID\" value=\"".$currentuser['userid']."\" />";
		$line .= "<button class=\"fg-button ui-state-default ui-corner-all\" type=\"submit\" id=\"button\">".ui_language::translate("Create")."</button></th>";
		$line .= "</tr>";
		$line .= "</table>";
		$line .= "</form>";
		
		return $line;
	}
	
	static function doCreateCron(){
		global $zdbh;
		global $controller;
		if (fs_director::CheckForEmptyValue(self::CheckCronForErrors())){
		
    		# If the user submitted a 'new' request then we will simply add the cron task to the database...
	    	$sql = $zdbh->prepare("INSERT INTO x_cronjobs (ct_acc_fk,
											ct_script_vc,
											ct_description_tx,
											ct_created_ts) VALUES (
											" . $controller->GetControllerRequest('FORM', 'inUserID') . ",
											'" . $controller->GetControllerRequest('FORM', 'inScript') . "',
											'" . $controller->GetControllerRequest('FORM', 'inDescription') . "',
											" . time() . ")");
			$sql->execute();
			self::$ok = TRUE;
			return;
		}
		self::$error = TRUE;
		return;
	}
	
	static function doDeleteCron(){
		global $zdbh;
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=" . $currentuser['userid'] . " AND ct_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {	
					$sql = $zdbh->prepare("SELECT * FROM x_cronjobs WHERE ct_acc_fk=" . $currentuser['userid'] . " AND ct_deleted_ts IS NULL");
					$sql->execute();
					while ($rowcrons = $sql->fetch()) {
						if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inDelete_'.$rowcrons['ct_id_pk'].''))){
        					$sql2 = $zdbh->prepare("UPDATE x_cronjobs SET ct_deleted_ts=" . time() . " WHERE ct_id_pk=" . $rowcrons['ct_id_pk'] . "");
							$sql2->execute();
							self::$ok = TRUE;
							return;
						}
					}
				}
			}
		self::$error = TRUE;
		return;
	}
	
	static function CheckCronForErrors() {
		global $zdbh;
		global $controller;
		$retval = FALSE;
		$currentuser = ctrl_users::GetUserDetail();
	    # Check to make sure the cron is not blank before we go any further...
	    if ($controller->GetControllerRequest('FORM', 'inScript') == '') {
			self::$blank = TRUE;
			$retval = TRUE;
	    }
	    # Check to make sure the cron script exists before we go any further...
	    if (!is_file(fs_director::RemoveDoubleSlash(fs_director::ConvertSlashes(ctrl_options::GetOption('hosted_dir') . $currentuser['username'] . '/' . $controller->GetControllerRequest('FORM', 'inScript'))))) {
			self::$noexists = TRUE;
			$retval = TRUE;
	    }
	    # Check to make sure the cron is not a duplicate...
			$sql = "SELECT COUNT(*) FROM x_cronjobs WHERE ct_acc_fk=" . $currentuser['userid'] . " AND ct_script_vc='".$controller->GetControllerRequest('FORM', 'inScript')."' AND ct_deleted_ts IS NULL";
			if ($numrows = $zdbh->query($sql)) {
 				if ($numrows->fetchColumn() <> 0) {	
					self::$alreadyexists = TRUE;
					$retval = TRUE;
				}
			}

		return $retval;
   	}
	
	static function getResult() {
		if (!fs_director::CheckForEmptyValue(self::$blank)){
			return ui_sysmessage::shout(ui_language::translate("You need to specify a valid location for your script."));
		}
		if (!fs_director::CheckForEmptyValue(self::$noexists)){
			return ui_sysmessage::shout(ui_language::translate("Your script does not appear to exist at that location. Your root folder is: "));
		}
		if (!fs_director::CheckForEmptyValue(self::$alreadyexists)){
			return ui_sysmessage::shout(ui_language::translate("You can not add the same cron task more than once."));
		}
		if (!fs_director::CheckForEmptyValue(self::$error)){
			return ui_sysmessage::shout(ui_language::translate("There was an error updating the cron job."));
		}
		if (!fs_director::CheckForEmptyValue(self::$ok)){
			return ui_sysmessage::shout(ui_language::translate("Cron updated successfully."));
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