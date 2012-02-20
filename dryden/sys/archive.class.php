<?php

/**
 * Interface template selection class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_archive {

    /**
     * Uncompresses a ZIP archive to a given location.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $archive Full path and filename to the ZIP archive.
     * @param string $dest The full path to the folder to extract the archive into (with trailing slash!)
     * @return boolean 
     */
    static function Unzip($archive, $dest) {
        if (!class_exists('ZipArchive'))
            return false;
        $zip = new ZipArchive;
        $result = $zip->open($archive);
        if ($result == true) {
            $zip->extractTo($dest);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

}

?>
