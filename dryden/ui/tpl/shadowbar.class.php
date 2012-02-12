<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_shadowbar {

    public function Template() {
        if (isset($_SESSION['ruid'])) {
                return "<div class=\"zshadowbar\" id=\"zshadowbar\"><a href=\"./?returnsession=true\">" . ui_language::translate("End shadow session and return to your session.") ."</a></div>";
        } else {
            return false;
        }
    }

}

?>
