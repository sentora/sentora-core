<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_moduleloader {
    /**
     * ui_moduleloader class is used to display the modules and module categories.
     */

    /**
     * Gets all modules in categories, or if parameter given can select a single category.
     * @param string $name
     */
    static function GetModuleCats($category="") {
        global $zdbh;

        if ($category == "") {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats");
        } else {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats WHERE mc_name_vc = '$category'");
        }
        $sql->execute();
        $line = "";

        while ($categories = $sql->fetch()) {


			$modsql = "SELECT COUNT(*) FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'";
			if ($nummodsql = $zdbh->query($modsql)) {
 				if ($nummodsql->fetchColumn() > 0) {
					$line .= "<table class=\"zcat\"><tr><th align=\"left\"><a name=\"" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\"></a>" . $categories['mc_name_vc'] . "<a href=\"#\" class=\"zcat\" id=\"zcat_" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "_a\"></a></th></tr>";

            		$line .= "<tr><td align=\"left\"><div class=\"zcat_" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\" id=\"zcat_" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\"><table class=\"zcatcontent\" align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>";

            		$line .= "<table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
					
            		$modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'");
            		$modsql->execute();
            		$icons_per_row = ctrl_options::GetOption('module_icons_pr');
            		$num_icons = 0;

            			while ($modules = $modsql->fetch()) {
                			$cleanname = str_replace(" ", "<br />", $modules['mo_name_vc']);
                			if ($num_icons == $icons_per_row) {
                    			$line .= "</tr><tr>";
                    			$num_icons = 0;
                			}
                			$line .= "<td style=\"text-align:center;\" align=\"left\"><a href=\"?module=".$modules['mo_folder_vc']."\" title=\"" . $modules['mo_desc_tx'] . "\"><img src=\"modules/" . $modules['mo_folder_vc'] . "/assets/icon.png\" border=\"0\" /></a><br /><a href=\"?module=".$modules['mo_folder_vc']."\">" . $cleanname . "</a></td>";
                			$num_icons++;
            			}
						$line .= "</tr></table></td></tr></table></div></td></tr></table><br>";
				}
			}
        }
        return $line;
    }
	
    /**
     * Gets all modules in categories in unordered list format, or if parameter given can select a single category.
     * @param string $name
     */
    static function GetModuleCatsUL($category="") {
        global $zdbh;

        if ($category == "") {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats");
        } else {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats WHERE mc_name_vc = '$category'");
        }
        $sql->execute();
        $line = "";
		$show = 0;
        while ($categories = $sql->fetch()) {

			$modsql = "SELECT COUNT(*) FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'";
			if ($nummodsql = $zdbh->query($modsql)) {
 				if ($nummodsql->fetchColumn() > 0) {
					if ($show == 0) { $class = "parent"; } else { $class = "parent"; }
					$line .= "<div id=\"id=\"zcat_" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "_list\" class=\"" . $class . "\">";
            		$line .= "<ul>";
            		$line .= "<lh>" . $categories['mc_name_vc'] . "</lh>";
					
            		$modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'");
            		$modsql->execute();

            			while ($modules = $modsql->fetch()) {
 
							$line .= "<li><a href=\"?module=".$modules['mo_folder_vc']."\">" . $modules['mo_name_vc'] . "</a></li>";
            			}
						$line .= "</ul></div>";
						$show ++;
				}
			}
        }
        return $line;
    }
	

    /**
     * Gets all modules in categories and returns preformatted navbar that can be custom styled in css
	 * or with JQueryUI Themeroller. If parameter given can select a single category.
     * @param string $name
     */
    static function GetModuleCatsZnavBar($category="") {
        global $zdbh;

        if ($category == "") {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats");
        } else {
            $sql = $zdbh->prepare("SELECT * FROM x_modcats WHERE mc_name_vc = '$category'");
        }
        $sql->execute();
        $line = "";
		$tabindex = 0;
        while ($categories = $sql->fetch()) {
		
		$cleanname = explode(" ", $categories['mc_name_vc']);
		$cleanname = $cleanname[0];

			$modsql = "SELECT COUNT(*) FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'";
			if ($nummodsql = $zdbh->query($modsql)) {
 				if ($nummodsql->fetchColumn() > 0) {
				
					$line .= "<script type=\"text/javascript\">";
					$line .= "$(function(){";
					$line .= "$('#" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "').menu({";
					$line .= "content: $('#" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "').next().html(),";
					$line .= "showSpeed: 400";
					$line .= "});";
					$line .= "});";
					$line .= "</script>";
					
					$line .= "<a tabindex=\"" . $tabindex . "\" href=\"" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\" class=\"fg-navbutton fg-button-icon-right ui-widget ui-state-default ui-corner-all\" id=\"" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\"><span class=\"ui-icon ui-icon-triangle-1-s\"></span>" . $cleanname . "</a>";
            		$line .= "<div id=\"" . str_replace(" ", "_", strtolower($categories['mc_name_vc'])) . "\" class=\"hidden\">";
            		$line .= "<ul>";
					
            		$modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_category_fk = '" . $categories['mc_id_pk'] . "' AND mo_type_en = 'user'");
            		$modsql->execute();

            			while ($modules = $modsql->fetch()) {
 
							$line .= "<li><a href=\"?module=".$modules['mo_folder_vc']."\"><img src=\"modules/" . $modules['mo_folder_vc'] . "/assets/icon.png\" width=\"20\" height=\"20\" border=\"0\" /> " . $modules['mo_name_vc'] . "</a></li>";
            			}
						$line .= "</ul></div>";
						$tabindex ++;
				}
			}
        }
        return $line;
    }

}

?>
