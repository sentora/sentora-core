<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelistul {

    public static function Template() {
        $line = "";
        $show = 0;
        $modcats = ui_moduleloader::GetModuleCats();
        foreach ($modcats as $modcat) {
            $mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk'], "modadmin");
            if ($show == 0) {
                $class = "parent";
            } else {
                $class = "parent";
            }
            $line .= "<div id=\"id=\"zcat_" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "_list\" class=\"" . $class . "\">";
            $line .= "<ul>";
            $line .= "<lh>" . $modcat['mc_name_vc'] . "</lh>";
            foreach ($mods as $mod) {
                $line .= "<li><a href=\"?module=" . $mod['mo_folder_vc'] . "\"><: " . $mod['mo_name_vc'] . " :></a></li>";
            }
            $line .= "</ul></div>";
            $show++;
        }
        return $line;
    }

}

?>