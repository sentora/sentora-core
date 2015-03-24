<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelistjson {

    public static function Template() {
        global $zdbh;

        /* Get Modules */
        $sql = 'SELECT mo_name_vc, mo_folder_vc FROM `x_modules` WHERE `mo_enabled_en` = "true" AND `mo_type_en` = "user"';
        $dbh = $zdbh->prepare($sql);
        $dbh->execute();
        $line = '';

        // Build Module list Array
        if ($dbh->fetch() !== 0) {

            $result = array();
            while ($row = $dbh->fetch()) {
                $result[] = array(
                    'name' => htmlentities($row['mo_name_vc'], ENT_QUOTES),
                    'url' => $row['mo_folder_vc']
                );
            }

        } else {
            $line .= 'No Modules Exist!';
        }

        // Build and Retun a JSON Object
        $line .= json_encode($result);

        return $line;
    }

}
?>