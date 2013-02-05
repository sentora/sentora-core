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
class ui_tpl_progbarbandwidth {

    public static function Template() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $bandwidthquota = $currentuser['bandwidthquota'];
        $bandwidth = ctrl_users::GetQuotaUsages('bandwidth', $currentuser['userid']);
        if ($bandwidthquota == 0) {
            return "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=0\"/>";
        } else {
            if (fs_director::CheckForEmptyValue($bandwidth))
                $bandwidth = 0;
            $percent = round(($bandwidth / $bandwidthquota) * 100, 0);
            return "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=" . $percent . "\"/>";
        }
    }

}

?>
