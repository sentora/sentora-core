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
        $domain = ctrl_users::GetUserDomains($currentuser['userid']);
        if ($domain <> 0) {
            return (string) $domain;
        }
        return (string) 0;
    }

}

?>
