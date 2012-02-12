<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_serveripaddress {

    public function Template() {
		global $zdbh;
        $sql = $zdbh->prepare("SELECT so_value_tx FROM x_settings WHERE so_name_vc='server_ip'");
        $sql->execute();
        $serverip = $sql->fetch();
		if (!fs_director::CheckForEmptyValue($serverip['so_value_tx'])){
        	return $serverip['so_value_tx'];
		} else {
			return sys_monitoring::ServerIPAddress();
		}
		
    }

}

?>
