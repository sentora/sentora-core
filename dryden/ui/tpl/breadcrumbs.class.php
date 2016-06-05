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
class ui_tpl_breadcrumbs {

    public static function Template() {
        global $zdbh, $controller;
        $line = '';

        if($controller->GetControllerRequest('URL', 'module')){

            $module = $controller->GetControllerRequest('URL', 'module');
            $moduleRow = ui_module::GetModuleCategoryName();
            $catUrl = strtolower(str_replace(' ', '-', $moduleRow['mc_name_vc']));

            if($moduleRow){
                $line .= '<ul class="breadcrumb">';
                $line .= '  <li><a href=".">Home</a></li>';
                $line .= '  <li><a href="./#' .$catUrl. '">' .$moduleRow['mc_name_vc']. '</a></li>';
                $line .= '  <li class="active">' .$moduleRow['mo_name_vc']. '</li>';
                $line .= '</ul>';
            }
        }

        return $line;
    }
}
?>