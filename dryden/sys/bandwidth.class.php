<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
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
     * @change 09/07/2013 Pascal Peyremorte (ppeyremorte@sentora.org) : Full rewrite of bandwidth extraction
     * @change 12/02/2015 Pascal Peyremorte : replace readfile/foreach by fopen/fgets/fclose to remove memory requirement
     */
    static function CalculateFromApacheLog($logfile)
    {
        $file = fopen($logfile, 'r');
        if ($file === false)
            return 0;

        $total = 0;
        while (!feof($file)) {
            $line = fgets($file);
            preg_match('>.+ .+\[.+\] ".+ .* HTTP/.*" [0-9]{3} ([0-9]+\b)>', $line, $match);
            if (isset($match[1])) {
                $total += $match[1];
            }
        }
        fclose($file);
        return $total;
    }

}

?>
