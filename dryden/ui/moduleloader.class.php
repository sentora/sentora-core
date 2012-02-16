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
     * Gets all categories, or if parameter given can select a single category.
     * @param string $name
     */
    static function GetModuleCats($catname = "") {
        global $zdbh;
		$user = ctrl_users::GetUserDetail();
        if ($catname == "") {
			$sql = "SELECT * FROM x_modcats";
        } else {
			$sql = "SELECT * FROM x_modcats WHERE mc_name_vc = '" . $catname . "'";
        }
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
			$has_icons = false;            	
            while ($row = $sql->fetch()) {
				$checksql = "SELECT * FROM x_modules WHERE mo_category_fk = '" . $row['mc_id_pk'] . "' AND mo_type_en = 'user' AND mo_enabled_en = 'true'";
				$checksql = $zdbh->prepare($checksql);
            	$checksql->execute();
            	while ($rowcheck = $checksql->fetch()) {
            		if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], $rowcheck['mo_id_pk'])) {
                		$has_icons = true;
                	}
            	}
	            if ($has_icons){
					array_push($res, array('mc_id_pk'   => $row['mc_id_pk'],
	                					   'mc_name_vc' => $row['mc_name_vc']));
				}
	        }
            return $res;
        } else {
            return false;
        }
    }

    /**
     * Gets all modules in categories, or if parameter given can select a single category.
     * @param string $name
     */
    static function  GetModuleList($catid = "") {
        global $zdbh;
		$user = ctrl_users::GetUserDetail();
        if ($catid == "") {
			$sql = "SELECT * FROM x_modules";
        } else {
			$sql = "SELECT * FROM x_modules WHERE mo_category_fk = '" . $catid . "' AND mo_type_en = 'user' AND mo_enabled_en = 'true'";
        }
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($row = $sql->fetch()) {
				if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], $row['mo_id_pk'])) {
            	array_push($res, array('mo_id_pk'   	 => $row['mo_id_pk'],
                					   'mo_category_fk'  => $row['mo_category_fk'],
									   'mo_name_vc' 	 => $row['mo_name_vc'],
									   'mo_version_in' 	 => $row['mo_version_in'],
									   'mo_folder_vc' 	 => $row['mo_folder_vc'],
									   'mo_type_en' 	 => $row['mo_type_en'],
									   'mo_desc_tx' 	 => $row['mo_desc_tx'],
									   'mo_installed_ts' => $row['mo_installed_ts'],
									   'mo_enabled_en' 	 => $row['mo_enabled_en'],
									   'mo_updatever_vc' => $row['mo_updatever_vc'],
									   'mo_updateurl_tx' => $row['mo_updateurl_tx']));
            	}
			}
            return $res;
        } else {
            return false;
        }
    }
	
	
}

?>