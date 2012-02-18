<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_useddistlists {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$distlists = ctrl_users::GetQuotaUsages('distlists', $currentuser['userid']);
		if ($distlists <> 0){
        	return (string) $distlists;
		} 
		return (string) 0;
    }

}

?>
