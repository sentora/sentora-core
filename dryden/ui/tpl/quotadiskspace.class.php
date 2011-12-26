<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_quotadiskspace {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		$diskspacequota = fs_director::ShowHumanFileSize($currentuser['diskquota']);	
	
		return $diskspacequota;
    }

}

?>
