<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelistznavbar {

    public static function Template() {
        $line = "";
        $tabindex = 0;
        $modcats = ui_moduleloader::GetModuleCats();
        foreach ($modcats as $modcat) {
            $cleanname = $modcat['mc_name_vc'];
            if ($cleanname == "Account Information") {
                $cleanname = "Account";
            }
            if ($cleanname == "Server Admin") {
                $cleanname = "Admin";
            }
            if ($cleanname == "Database Management") {
                $cleanname = "Database";
            }
            if ($cleanname == "Domain Management") {
                $cleanname = "Domain";
            }
            if ($cleanname == "File Management") {
                $cleanname = "File";
            }
            if ($cleanname == "Server Admin") {
                $cleanname = "Server";
            }
            if ($cleanname == "Server Admin") {
                $cleanname = "Server";
            }
            if ($cleanname == "Server Admin") {
                $cleanname = "Server";
            }
            $cleanname = ui_language::translate($cleanname);
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
            foreach ($mods as $mod) {
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
