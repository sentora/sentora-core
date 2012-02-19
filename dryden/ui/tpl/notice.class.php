<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
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
            if ($result['ac_notice_tx'] <> "")
                return "<div class=\"znotice\" id=\"znotice\"><a href=\"#\" class=\"znotice\" id=\"znotice_a\" border=\"0\"></a>" . $result['ac_notice_tx'] . "</div>";
            return false;
        } else {
            return false;
        }
    }

}

?>
