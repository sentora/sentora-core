<?php
/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Home Dashboard with draggable & collapsible category sections.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelist2column {

    public static function Template() {
        global $controller;
        if (!$controller->GetControllerRequest('URL', 'module')) {
            $line = '';
            $modcats = ui_moduleloader::GetModuleCats();
            $mod_box_count = 0;

            $line .= '<ul id="sortable-with-handles" class="sortable grid ">';

            foreach ($modcats as $modcat) {
                $mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk'], "modadmin");
                if ($mods) {

                    $catUrl = strtolower(str_replace(' ', '-', $modcat['mc_name_vc']));

                    $line .= '<li data-catid="'.$modcat['mc_id_pk'].'" id="'.$catUrl.'" class="col-span-6 module-box">';
                    $line .= '    <div class="module-box-title">';
                    $line .= '        <h4><: ' .$modcat['mc_name_vc']. ' :></h4>';
                    $line .= '        <div class="tools">';
                    $line .= '            <span class="collapse"><i class="icon-up-open"></i></span> <span class="handle"></span>';
                    $line .= '        </div>';
                    $line .= '    </div>';
                    $line .= '    <div class="module-box-body" style="display: block;">';
                    $line .= '        <ul>';

                    foreach ($mods as $mod) {
                        $translatename = $mod['mo_name_vc'];
                        $cleanname = str_replace(" ", "ZP(br)", $translatename);

                        // Check is User Style Module Icon Exist
                 if (file_exists('etc/styles/' . ui_template::GetUserTemplate() . '/img/modules/'.$mod['mo_folder_vc'].'/assets/icon.png')) {
                            $icon = 'etc/styles/' . ui_template::GetUserTemplate() . '/img/modules/'.$mod['mo_folder_vc'].'/assets/icon.png';
                        } else {
                            $icon = 'modules/' . $mod['mo_folder_vc'] . '/assets/icon.png';
                        }

                        $line .= '              <li>';
                        $line .= '                      <a href="?module=' . $mod['mo_folder_vc'] . '" title="<: ' . $mod['mo_desc_tx'] . ' :>">';
                        $line .= '<img src="' .$icon. '" border="0">';
                        $line .= '                      </a>';
                        $line .= '                      <br />';
                        $line .= '                      <a href="?module=' . $mod['mo_folder_vc'] . '"><: ' . $cleanname . ' :></a>';
                        $line .= '              </li>';
                    }

                    $line .= '        </ul>';
                    $line .= '    </div><!-- end module-box-body-->';
                    $line .= '</li><!-- end module-box-->';

                    // Clear our Floated Divs every 2 boxes
                    $mod_box_count++;
                    if($mod_box_count % 2 == 0){
                      $css_class = '';
                    }else{
                        $css_class = 'last';
                    }

                }
            }

            $line .= '</ul>'; //end sortable;

            return $line;
        }
    }

}

?>
