<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_usedftp {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		$ftp = fs_director::GetQuotaUsages('ftpaccounts', $currentuser['userid']);
		if ($ftp <> 0){
        	return (string) $ftp;
		} 
		return (string) 0;
    }

}

?>
