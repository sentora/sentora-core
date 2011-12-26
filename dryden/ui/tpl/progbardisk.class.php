<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_progbardisk {

    public function Template() {
		global $controller;
		$currentuser = ctrl_users::GetUserDetail();
		
		$diskquota = $currentuser['diskquota'];
		$diskspace = fs_director::GetQuotaUsages('diskspace', $currentuser['userid']);
		$per = ($diskspace / $diskquota) * 100;
		$percent = round($per, 0);
		
		$line = "<img src=\"etc/lib/pChart2/zpanel/zProgress.php?percent=".$percent."\"/>";		
		return $line;
    }

}

?>
