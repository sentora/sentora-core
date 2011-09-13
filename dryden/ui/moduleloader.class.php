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
		
		if ($category == ""){
			$sql = $zdbh->prepare("SELECT * FROM x_modcats");
			$sql->execute();
		}else{
			$sql = $zdbh->prepare("SELECT * FROM x_modcats WHERE mc_name_vc = '$category'");
			$sql->execute();		
		}
		$line = "";
		
		while ($categories = $sql->fetch()){
			$line .= "<table class=\"zmodule\"><tr><th align=\"left\"><a name=\"" . strtolower($categories['mc_name_vc']) . "\"></a>" . $categories['mc_name_vc'] . "<a href=\"#\" class=\"zmodule\" id=\"zmodule_" . strtolower($categories['mc_name_vc']) . "_a\"></a></th>\n</tr>";
		
			$line .="<tr><td align=\"left\"><div class=\"zmodule_" . strtolower($categories['mc_name_vc']) . "\" id=\"zmodule_" . strtolower($categories['mc_name_vc']) . "\"><table class=\"zmodulecontent\" align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>";
		
			$line .= "<table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
		
			//echo "<b>".$categories['mc_name_vc']."</b>\n";
			$modsql = $zdbh->prepare("SELECT * FROM x_modules WHERE mo_category_fk = '".$categories['mc_id_pk']."'");
			$modsql->execute();
			$icons_per_row = ctrl_options::GetOption('module_icons_pr');
			$num_icons = 0;
			
			while ($modules = $modsql->fetch()){
				$cleanname = str_replace(" ", "<br />", $modules['mo_name_vc']);
				if ($num_icons == $icons_per_row){
					$line .= "</tr><tr>";
					$num_icons = 0;
				}
				$line .= "<td style=\"text-align:center;\" align=\"left\"><a href=\"\" title=\"" . $modules['mo_name_vc'] . "\"><img src=\"/zpanelx/etc/styles/zpanelx/images/home.png\" border=\"0\" /></a><br /><a href=\"\">" . $cleanname . "</a></td>";			
				$num_icons ++;
			}	
		$line .= "</tr></table></td></tr></table></div></td></tr></table><br>";
		}
echo $line;
	}

}

?>
