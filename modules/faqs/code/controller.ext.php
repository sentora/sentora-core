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

	function getFAQS() {
	    global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT COUNT(*) FROM x_faqs WHERE fq_queston_tx IS NOT NULL";
        if ($numrows = $zdbh->query($sql)) {
			$line = NULL;
            if ($numrows->fetchColumn() <> 0) {
			    $sql = $zdbh->prepare("SELECT * FROM x_faqs WHERE fq_queston_tx IS NOT NULL");
                $sql->execute();
		 		while ($rowfaqs = $sql->fetch()) {
            	$line .= "<tr valign=\"top\">";
                $line .= "<td><img src=\"modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/item.png\" width=\"16\" height=\"16\"></td>";
                $line .= "<td><a href=\"#\" onclick=\"toggle_visibility('".$rowfaqs['fq_id_pk']."')\"><strong>".$rowfaqs['fq_queston_tx']."</strong></a>";
                $line .= "<div id=\"".$rowfaqs['fq_id_pk']."\" style=\"display:none;\">".$rowfaqs['fq_answer_tx']."<br><br></div></td>";
            	$line .= "</tr>";
         		} 
			}
		}
    return $line;
	}
	

	static function getModuleName() {
		$module_name = ui_module::GetModuleName();
        return $module_name;
    }

	static function getModuleIcon() {
		global $controller;
		$module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }
		
}

?>