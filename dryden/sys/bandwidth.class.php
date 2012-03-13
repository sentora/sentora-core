<?php

/**
 * Bandwidth generation class.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_bandwidth {

    /**
     * Generate the toal amount of bandwidth based on an Apache Access Log (common format).
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $logfile The path to the log file of which to parse.
     * @return int Total amount of bandwidth used (bytes)
     */
    static function CalculateFromApacheLog($logfile) {
        $bandwidthfile = file($logfile);
        $logdata = implode("", $bandwidthfile);
        $logdata = preg_replace("/(\n|\r|\t)/", "\n", $logdata);
        $records = preg_split("/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $logdata, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sizerecs = sizeof($records);
        $j = 0;
        $i = 1;
        $arb = array();
        $recordnum = 0;
        while ($i < $sizerecs) {
            $ip = $records[$i];
            $all = @$records[$i + 1];
            preg_match("/\[(.+)\]/", $all, $match);
            $access_time = @$match[1];
            $all = @str_replace($match[1], "", $all);
            preg_match("/\"GET (.[^\"]+)/", $all, $match);
            $http = @$match[1];
            $link = explode(" ", $http);
            $all = @str_replace("\"GET $match[1]\"", "", $all);
            preg_match("/([0-9]{3})/", $all, $match);
            $success_code = @$match[1];
            $all = @str_replace($match[1], "", $all);
            preg_match("/\"(.[^\"]+)/", $all, $match);
            $ref = @$match[1];
            $all = @str_replace("\"$match[1]\"", "", $all);
            preg_match("/\"(.[^\"]+)/", $all, $match);
            $browser = @$match[1];
            $all = @str_replace("\"$match[1]\"", "", $all);
            preg_match("/([0-9]+\b)/", $all, $match);
            $bytes = @$match[1];
            $all = @str_replace($match[1], "", $all);
            $arb[$j] = @$arb[$j] + $bytes;
            $i++;
            $recordnum++;
        }
        return @$arb[$j];
    }

}

?>
