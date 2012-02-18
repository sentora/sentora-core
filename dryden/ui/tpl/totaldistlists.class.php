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
class ui_tpl_totaldistlists {

    public function Template() {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $distrobutionlistsquota = $currentuser['distrobutionlistsquota'];
        if ($distrobutionlistsquota <> 0) {
            return (string) $distrobutionlistsquota;
        }
        return (string) 0;
    }

}

?>
