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
    
    static function GetUserTemplate(){
        $user = ctrl_users::GetUserDetail();
        if(fs_director::CheckForEmptyValue($user['usertheme'])){
            # Lets use the reseller's theme they have setup!
            $reseller = ctrl_users::GetUserDetail($user['resellerid']);
            return $reseller['usertheme'];
        } else {
            return $user['usertheme'];
        }
    }
    
}

?>
