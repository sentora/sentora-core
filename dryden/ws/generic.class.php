<?php

/**
 * Generic web services class.
 * @package zpanelx
 * @subpackage dryden -> webservices
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ws_generic {

    /**
     * Provides very basic way of retrieving a result as a string from a given URL (RAW) this does not need to be a 'true' web service.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $requestURL The URL to the resource.
     * @return mixed If the request was successful it will return the contents of the requested URL otherwise will return 'false'.
     */
    static function ReadURLRequestResult($requestURL) {
        ob_start();
        @readfile($requestURL);
        $reqcontent = ob_get_contents();
        ob_clean();
        if ($reqcontent)
            return $reqcontent;
        $ws_log = new debug_logger();
        $ws_log->logcode = "903";
        $ws_log->detail = "Unable to connect to webservice URL (" . $requestURL . ") as requested in ws_generic::ReadURLRequestResult()";
        $ws_log->writeLog();
        return false;
    }

    /**
     * Generic method to send POST data to a web service and then return its response (without the need to use cURL or another HTTP client).
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $url The URL of which to POST the data too.
     * @param string $data The data content of which to send.
     * @param string $optional_headers Option headers if you require to send them.
     * @return string The response recieved. 
     */
    static function DoPostRequest($url, $data, $optional_headers = null) {
        //$ws_log = new debug_logger();
        //$ws_log->logcode = "904";
        $params = array('http' => array(
                'method' => 'POST',
                'content' => $data
                ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            //$ws_log->detail = "Problem with " .$url. ", ".$php_errormsg."";
            //$ws_log->writeLog();
        }
        $response = @stream_get_contents($fp);
        if ($response == false) {
            //$ws_log->detail = "Problem reading data from ".$url. ", ".$php_errormsg."";
            //$ws_log->writeLog();
        }
        return $response;
    }

    /**
     * Captures the RAW POST data passed to this script.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return string The raw request data.
     */
    static function ProcessRawRequest() {
        $xml_raw_data = fs_filehandler::ReadFileContents('php://input');
        return $xml_raw_data;
    }

}

?>
