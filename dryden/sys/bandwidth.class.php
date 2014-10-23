<?php

/**
 * @copyright 2014 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Bandwidth generation class.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_bandwidth
{

    /**
     * Generate the toal amount of bandwidth based on an Apache Access Log (common format).
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $logfile The path to the log file of which to parse.
     * @return int Total amount of bandwidth used (bytes)
     */
    static function CalculateFromApacheLog($logfile)
    {
        $lines = file($logfile);
        $total = 0;
        foreach ($lines as $line) {
            preg_match('>.+ .+\[.+\] ".+ .* HTTP/.*" [0-9]{3} ([0-9]+\b)>', $line, $match);
            if (isset($match[1])) {
                $total += $match[1];
            }
        }
        return $total;
    }

}

?>
