<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Main module loader class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.1.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_moduleloader {

    /**
     * Gets all categories, or if parameter given can select a single category.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param string $catname The name of the module category to get the list of modules from.
     */
    static function GetModuleCats($catname = "") {
        global $zdbh;
        $user = ctrl_users::GetUserDetail();

        if (isset($user['catorder']) && $user['catorder'] != '') {
            $order = trim($user['catorder'], '[]');
            $sql = 'SELECT * FROM `x_modcats`ORDER BY FIELD(`mc_id_pk`, '.$order.')';
        } else if($catname != ''){
            $sql = 'SELECT * FROM x_modcats WHERE mc_name_vc = :catname';
        }else{
            $sql = 'SELECT * FROM x_modcats';
        }

        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':catname', $catname);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            $has_icons = false;
            while ($row = $sql->fetch()) {
                $checksql = "SELECT * FROM x_modules WHERE mo_category_fk = :cid AND mo_type_en = 'user' AND mo_enabled_en = 'true'";
                $checksql = $zdbh->prepare($checksql);
                $checksql->bindParam(':cid', $row['mc_id_pk']);
                $checksql->execute();
                while ($rowcheck = $checksql->fetch()) {
                    if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], $rowcheck['mo_id_pk'])) {
                        $has_icons = true;
                    }
                }
                if ($has_icons) {
                    array_push($res, array('mc_id_pk' => $row['mc_id_pk'],
                        'mc_name_vc' => $row['mc_name_vc']));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    /**
     * Gets the module list as an array from a given category ID.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $catid The name of the module category to get the list of modules from.
     * @return array Array containing the list of modules for the category ID supplied.
     */
    static function GetModuleList($catid = "") {
        global $zdbh;
        $user = ctrl_users::GetUserDetail();
        if ($catid == "") {
            $sql = "SELECT * FROM x_modules";
        } else {
            $sql = "SELECT * FROM x_modules WHERE mo_category_fk = :catid AND mo_type_en = 'user' AND mo_enabled_en = 'true' ORDER BY mo_name_vc";
        }

        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':catid', $catid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':catid', $catid);
            $res = array();
            $sql->execute();
            while ($row = $sql->fetch()) {
                if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], $row['mo_id_pk'])) {
                    array_push($res, array('mo_id_pk' => $row['mo_id_pk'],
                        'mo_category_fk' => $row['mo_category_fk'],
                        'mo_name_vc' => $row['mo_name_vc'],
                        'mo_version_in' => $row['mo_version_in'],
                        'mo_folder_vc' => $row['mo_folder_vc'],
                        'mo_type_en' => $row['mo_type_en'],
                        'mo_desc_tx' => $row['mo_desc_tx'],
                        'mo_installed_ts' => $row['mo_installed_ts'],
                        'mo_enabled_en' => $row['mo_enabled_en'],
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