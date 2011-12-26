<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_usedsubdomains {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$subdomains = fs_director::GetQuotaUsages('subdomains', $currentuser['userid']);
		if ($subdomains <> 0){
        	return (string) $subdomains;
		} 
		return (string) 0;
    }

}

?>
