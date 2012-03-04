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
class ui_tpl_useddomains {

    public function Template() {
        $currentuser = ctrl_users::GetUserDetail();
        $subdomains = ctrl_users::GetQuotaUsages('domains', $currentuser['userid']);
        if ($subdomains <> 0) {
            return (string) $subdomains;
        }
        return (string) 0;
    }

}

?>
