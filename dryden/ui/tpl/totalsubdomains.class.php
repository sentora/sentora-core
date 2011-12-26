<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_totalsubdomains {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$subdomainsquota = $currentuser['subdomainquota'];
		if ($subdomainsquota <> 0){
        	return (string) $subdomainsquota;
		} 
		return (string) 0;
    }

}

?>
