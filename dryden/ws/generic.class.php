<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ws
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ws_generic {

    /**
     * This function provides very basic way of retrieving a result as a string from a given URL (RAW) this does not need to be a 'true' web service.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param str $requestURL 
     */
    static function ReadURLRequestResult($requestURL) {
        ob_start();
        readfile($requestURL);
        $reqcontent = ob_get_contents();
        ob_clean();
        return $reqcontent;
    }

}

?>
