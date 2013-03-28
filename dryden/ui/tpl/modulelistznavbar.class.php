<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelistznavbar {

    public static function Template() {

        $active = isset($_REQUEST['module']) ? '' : 'class="active"';
        $line = '<li '.$active.'><a href=".">'.ui_language::translate('Home').'</a></li>';

        $modcats = ui_moduleloader::GetModuleCats();
        foreach ($modcats as $modcat) {
            $shortName = $modcat['mc_name_vc'];

            switch ($shortName) {
                case 'Account Information':
                    $shortName = 'Account';
                    break;
                case 'Server Admin':
                    $shortName = 'Admin';
                    break;
                case 'Database Management':
                    $shortName = 'Database';
                    break;
                case 'Domain Management':
                    $shortName = 'Domain';
                    break;
                case 'File Management':
                    $shortName = 'File';
                    break;
                case 'Server Admin':
                    $shortName = 'Server';
                    break;
            }

            $shortName = ui_language::translate($shortName);
            $mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk']);

            $line .= '<li class="dropdown">';
            $line .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$shortName.' <b class="caret"></b></a>';
            $line .= '<ul class="dropdown-menu">';
            foreach ($mods as $mod) {

                $class_name = str_replace(array(' ', '_'), '-', strtolower($mod['mo_folder_vc']));

                if(isset($_GET['module']) && $_GET['module'] == $mod['mo_folder_vc']){
                    $line .= '<li class="active">';
                }else{
                    $line .= '<li>';
                }
                $line .= '<a href="?module=' . $mod['mo_folder_vc'] . '"><i class="icon-'.$class_name.'"></i> ' . ui_language::translate($mod['mo_name_vc']) . '</a></li>';
            }
            $line .= "</ul></li>";
        }

        return $line;
    }
}

?>