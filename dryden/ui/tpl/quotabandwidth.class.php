<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_quotabandwidth {

    public static function Template() {
        $currentuser = ctrl_users::GetUserDetail();
        if ($currentuser['bandwidthquota'] == 0)
            $bandwidthquota = ui_language::translate('Unlimited');
        else
            $bandwidthquota = fs_director::ShowHumanFileSize($currentuser['bandwidthquota']);
        return $bandwidthquota;
    }

}

?>
