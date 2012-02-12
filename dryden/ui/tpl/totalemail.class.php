<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_totalemail {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$mailboxquota = $currentuser['mailboxquota'];
		if ($mailboxquota <> 0){
        	return (string) $mailboxquota;
		} 
		return (string) 0;
    }

}

?>
