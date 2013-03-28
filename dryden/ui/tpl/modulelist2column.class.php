<?php
/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Bobby Allen (ballen@zpanelcp.com)
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


                    //$line .= '<ul class="connected grid no2">';
                    $line .= '<li id="'.$modcat['mc_id_pk'].'" class="col-span-6 module-box">';
                    $line .= '    <a name="'.$modcat['mc_name_vc'].'"></a><div class="module-box-title">';
                    $line .= '        <h4>' .ui_language::translate($modcat['mc_name_vc']). '</h4>';
                    $line .= '        <div class="tools">';
                    $line .= '            <span class="handle">::</span> <a href="#" class="collapse">-</a>';
                    $line .= '        </div>';
                    $line .= '    </div>';
                    $line .= '    <div class="module-box-body" style="display: block;">';
                    $line .= '        <ul>';

                    foreach ($mods as $mod) {
                        $translatename = ui_language::translate($mod['mo_name_vc']);
                        $cleanname = str_replace(" ", "<br />", $translatename);

                        // Check is User Style Module Icon Exist
                        if (file_exists('etc/styles/' . ui_template::GetUserTemplate() . '/images/'.$mod['mo_folder_vc'].'/assets/icon.png')) {
                            $icon = 'etc/styles/' . ui_template::GetUserTemplate() . '/images/'.$mod['mo_folder_vc'].'/assets/icon.png';
                        } else {
                            $icon = 'modules/' . $mod['mo_folder_vc'] . '/assets/icon.png';
                        }

                        $line .= '              <li>';
                        $line .= '                      <a href="?module=' . $mod['mo_folder_vc'] . '" title="' . ui_language::translate($mod['mo_desc_tx']) . '">';
                        $line .= '<img src="' .$icon. '" border="0">';
                        $line .= '                      </a>';
                        $line .= '                      <br />';
                        $line .= '                      <a href="?module=' . $mod['mo_folder_vc'] . '">' . $cleanname . '</a>';
                        $line .= '              </li>';
                    }

                    $line .= '        </ul>';
                    $line .= '    </div><!-- end module-box-body-->';
                    $line .= '</li><!-- end module-box-->';
                    //$line .= '</ul><!-- end connected-->';

                    // Clear our Floated Divs ever 2 boxes
                    $mod_box_count++;
                    if($mod_box_count % 2 == 0){
                     // $line .= '</div><!-- end row-fluid--><div class="row-fluid">';
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






/*

$line .= '<div class="module-box">';

$line .= '    <div class="module-box-title">';
$line .= '        <h4><i class="icon-cogs"></i>Admin Settings</h4>';
$line .= '        <div class="tools">';
$line .= '            <a href="#" class="collapse">-</a>';
$line .= '        </div>';
$line .= '    </div>';

$line .= '    <div class="module-box-body" style="display: block;">';
$line .= '        <ul>';
$line .= '              <li>Module link and Icon</li>';
$line .= '              <li>Module link and Icon</li>';
$line .= '              <li>Module link and Icon</li>';
$line .= '        </ul>';
$line .= '    </div>';

$line .= '</div>';


*/
?>
