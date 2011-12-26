<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_usagebandwidth {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		$bandwidth = fs_director::ShowHumanFileSize(fs_director::GetQuotaUsages('bandwidth', $currentuser['userid']));
					
		return $bandwidth;
    }

}

?>
