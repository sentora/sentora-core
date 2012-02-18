<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_usedemail {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$email = ctrl_users::GetQuotaUsages('mailboxes', $currentuser['userid']);
		if ($email <> 0){
        	return (string) $email;
		} 
		return (string) 0;
    }

}

?>
