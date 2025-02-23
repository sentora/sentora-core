<?php

/**
 * @copyright 2014-2025 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 2.1.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @BBCode modification TGates (tgates@sentora.org)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_notice {

    public static function Template() {
        $user_array = ctrl_users::GetUserDetail();
        global $zdbh;
        $result = $zdbh->query("SELECT ac_notice_tx FROM x_accounts WHERE ac_id_pk = " . $user_array['resellerid'] . "")->Fetch();
        if ($result) {
            if ($result['ac_notice_tx'] <> "")
				// Convert BBcode to HTML - TGates
				require_once('modules/client_notices/code/bbcode.php');
				$ac_notice_tx = bbcode_to_html($result['ac_notice_tx']);
                return ui_sysmessage::shout($ac_notice_tx,
                    'notice',
                    'Notice:',
                    true
                );
                /* xssClean() disables BBCode conversion - TGates
                return ui_sysmessage::shout(
                    runtime_xss::xssClean($result['ac_notice_tx']),
                    'notice',
                    'Notice:',
                    true
                );
				*/
            return false;
        } else {
            return false;
        }
    }
}
?>