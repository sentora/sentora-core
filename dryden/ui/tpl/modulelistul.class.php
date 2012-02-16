<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    /**
     * Gets all modules in categories in unordered list format, or if parameter given can select a single category.
     * @param string $name
     */
class ui_tpl_modulelistul {

	global $controller;
     	$line = "";
       	$show = 0;
		$modcats = ui_moduleloader::GetModuleCats();
		foreach ($modcats as $modcat){
			$mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk'], "modadmin");
            if ($show == 0) {
    	        $class = "parent";
            } else {
                $class = "parent";
            }
            $line .= "<div id=\"id=\"zcat_" . str_replace(" ", "_", strtolower($modcat['mc_name_vc'])) . "_list\" class=\"" . $class . "\">";
            $line .= "<ul>";
            $line .= "<lh>" . $modcat['mc_name_vc'] . "</lh>";
			foreach ($mods as $mod){
				$translatename = ui_language::translate($mod['mo_name_vc']);
	            $line .= "<li><a href=\"?module=" . $mod['mo_folder_vc'] . "\">" . ui_language::translate($mod['mo_name_vc']) . "</a></li>";
			}
			$line .= "</ul></div>";
	        $show++;
		}
        return $line;
    }

}

?>