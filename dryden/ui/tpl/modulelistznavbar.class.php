<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    /**
     * Gets all modules in categories and returns preformatted navbar that can be custom styled in css
     * or with JQueryUI Themeroller. If parameter given can select a single category.
     * @param string $name
     */
class ui_tpl_modulelistznavbar {

    function Template() {
		global $controller;
			$line = "";
			$tabindex = 0;
			$modcats = ui_moduleloader::GetModuleCats();
			foreach ($modcats as $modcat){
				$cleanname = explode(" ", $modcat['mc_name_vc']);
            	$cleanname = ui_language::translate($cleanname[0]);
				$mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk'], "modadmin");
                $line .= "<script type=\"text/javascript\">";
                $line .= "$(function(){";
                $line .= "$('#" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "').menu({";
                $line .= "content: $('#" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "').next().html(),";
                $line .= "showSpeed: 400";
                $line .= "});";
                $line .= "});";
                $line .= "</script>";
				
                $line .= "<a tabindex=\"" . $tabindex . "\" href=\"" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "\" class=\"fg-navbutton fg-button-icon-right ui-widget ui-state-default ui-corner-all\" id=\"" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "\"><span class=\"ui-icon ui-icon-triangle-1-s\"></span>" . $cleanname . "</a>";
                $line .= "<div id=\"" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "\" class=\"hidden\">";
                $line .= "<ul>";					
				foreach ($mods as $mod){
                	$line .= "<li>";
					$line .= "<a href=\"?module=" . $mod['mo_folder_vc'] . "\">";
					$line .= "<img src=\"modules/" . $mod['mo_folder_vc'] . "/assets/icon.png\" width=\"20\" height=\"20\" border=\"0\" /> ";
					$line .= "" . ui_language::translate($mod['mo_name_vc']) . "";
					$line .= "</a>";
					$line .= "</li>";
				}
				$line .= "</ul></div>";
                $tabindex++;
			}
            return $line;
    }

}

?>
