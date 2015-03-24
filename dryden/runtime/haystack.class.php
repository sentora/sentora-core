<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * A search and retrieve/replace class.
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_haystack {

    /**
     * Get a value between two given strings.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $string The complete string on which to compute.
     * @param string $start The starting character or seqence of characters.
     * @param string $end The ending character or seqence of characters.
     * @return string The value of the string between the starting and ending character(s).
     */
    static function GetValueBetween($string, $start, $end) {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0)
            return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}

?>
