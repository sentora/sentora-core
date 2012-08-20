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
class ui_tpl_progbardisk {

    public function Template() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $diskquota = $currentuser['diskquota'];
        $diskspace = ctrl_users::GetQuotaUsages('diskspace', $currentuser['userid']);
        if (!fs_director::CheckForEmptyValue($diskspace)) {
            $per = ($diskspace / $diskquota) * 100;
            $percent = round($per, 0);
            $line = "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=" . $percent . "\"/>";
        } else {
            $line = "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=0\"/>";
        }
        if ($diskspace == $diskquota) {
            $line = "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=100\"/>";
        }
        return $line;
    }

}

?>
