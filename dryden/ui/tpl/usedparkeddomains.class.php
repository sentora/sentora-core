<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_usedparkeddomains {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$parkeddomains = fs_director::GetQuotaUsages('parkeddomains', $currentuser['userid']);
		if ($parkeddomains <> 0){
        	return (string) $parkeddomains;
		} 
		return (string) 0;
    }

}

?>
