<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_template {

    static function GetUserTemplate() {
        $user = ctrl_users::GetUserDetail();
        if (fs_director::CheckForEmptyValue($user['usertheme'])) {
            # Lets use the reseller's theme they have setup!
            $reseller = ctrl_users::GetUserDetail($user['resellerid']);
            return $reseller['usertheme'];
        } else {
            return $user['usertheme'];
        }
    }

    static function ListAvaliableTemeplates() {
        $allstyles = array();
        $handle = @opendir(ctrl_options::GetOption('zpanel_root') . "etc/styles");
        $chkdir = ctrl_options::GetOption('zpanel_root') . "etc/styles/";
        if ($handle) {
            while ($file = readdir($handle)) {
                if ($file != "." && $file != "..") {
                    if (is_dir($chkdir . $file)) {
                        array_push($allstyles, array('name' => $file));
                    }
                }
            }
            closedir($handle);
        }
        return $allstyles;
    }

}

?>
