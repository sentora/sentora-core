<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_notice {

    public function Template() {
        $user_array = ctrl_users::GetUserDetail();
        global $zdbh;
        $result = $zdbh->query("SELECT ac_notice_tx FROM x_accounts WHERE ac_id_pk = " . $user_array['resellerid'] . "")->Fetch();
        if ($result) {
            return $result['ac_notice_tx'];
        } else {
            return false;
        }
    }

}

?>
