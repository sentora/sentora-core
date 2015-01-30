<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Archive and file compression class.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_archive {

    /**
     * Uncompresses a ZIP archive to a given location.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $archive Full path and filename to the ZIP archive.
     * @param string $dest The full path to the folder to extract the archive into (with trailing slash!)
     * @return boolean 
     */
    static function Unzip($archive, $dest) {
        global $zlo;
        if (!class_exists('ZipArchive'))
            return false;
        $zip = new ZipArchive;
        $result = $zip->open($archive);
        if ($result) {
            if (@$zip->extractTo($dest)) {
                $zip->close();
                return true;
            } else {
                $zlo->logcode = "623";
                $zlo->detail = "Unable to extract file '" . $archive . "' to '" . $dest . "'.";
                $zlo->writeLog();
                return false;
            }
        } else {
            $zlo->logcode = "621";
            $zlo->detail = "The archive file '" . $archive . "' appears to be invalid.";
            $zlo->writeLog();
            return false;
        }
    }

}

?>
