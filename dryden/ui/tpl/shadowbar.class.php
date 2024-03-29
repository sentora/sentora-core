<?php

/**
 * @copyright 2014-2023 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.1
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @Added shadowed account username - TGates 2024-01-20
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_shadowbar {

    public static function Template() {
		$shadoweduser = ctrl_users::GetUserDetail();
		$shadowedusername = $shadoweduser['username'];
        if (isset($_SESSION['ruid'])) {
            return '<div class="zshadowbar" id="zshadowbar"><a href="./?returnsession=true"><button type="button" class="shadow-btn btn btn-danger"><: End shadowing :> <b>' . $shadowedusername . '</b> <: and return to your session. :></button></a></div>';
        } else {
            return false;
        }
    }

}

?>