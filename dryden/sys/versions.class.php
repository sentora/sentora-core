<?php

/**
 * This reports on core zpanel software versions.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_versions {

    /**
     * Returns the Apache HTTPd Server Version Number
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Apache Server version number.
     */
    static function ShowApacheVersion() {
        if (preg_match('|Apache\/(\d+)\.(\d+)\.(\d+)|', apache_get_version(), $apachever)) {
            $retval = str_replace("Apache/", "", $apachever[0]);
        } else {
            $retval = "Not found";
        }
        return $retval;
    }

    /**
     * Returns the PHP version number.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string PHP version number
     */
    static function ShowPHPVersion() {
        return phpversion();
    }

    /**
     * Returns the MySQL server version number.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string MySQL version number 
     */
    static function ShowMySQLVersion() {
        global $zdbh;
        $retval = $zdbh->query("SHOW VARIABLES LIKE \"version\"")->Fetch();
        return $retval['Value'];
    }

    /**
     * Returns a human readable copy of the Kernal version number running on the server.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $platform The OS Platform (eg. Linux or Windows)
     * @return string *NIX kernal version. - Will return 'N/A' for Microsoft Windows.
     */
    static function ShowOSKernalVersion($platform) {
        if ($platform == 'Linux') {
            $retval = exec('uname -r');
        } else {
            $retval = "N/A";
        }
        return $retval;
    }

    /**
     * Returns in human readable form the operating system platform (eg. Windows, Linux, FreeBSD, Other)
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Human readable OS Platform name.
     */
    static function ShowOSPlatformVersion() {
        $os_abbr = strtoupper(substr(PHP_OS, 0, 3));
        if ($os_abbr == "WIN") {
            $retval = "Windows";
        } elseif ($os_abbr == "LIN") {
            $retval = "Linux";
        } elseif ($os_abbr == "FRE") {
            $retval = "FreeBSD";
        } elseif ($os_abbr == "DAR") {
            $retval = "MacOSX";
        } else {
            $retval = "Other";
        }
        return $retval;
    }

    /**
     * Returns in human readable form the operating system name (eg. Windows, Ubuntu, CentOS, MacOSX, FreeBSD, Other)
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Human readable OS name.
     */
    static function ShowOSName() {
        preg_match_all("#(?<=\()(.*?)(?=\))#", $_SERVER['SERVER_SOFTWARE'], $osname);
        if (!empty($osname)) {
            if (strtoupper(substr($osname[0][0], 0, 3)) == "WIN") {
                $retval = "Windows";
            } else {
                $retval = $osname[0][0];
                if ($retval == "Unix") {
                    // Lets just make sure it isn't MacOSX before we give up!
                    if (sys_versions::ShowOSPlatformVersion() == "MacOSX") {
                        $retval = "MacOSX";
                    }
                }
            }

            //My testing shows Linux shows correct OS, WindowsXP=Win32, Windows2007/Server=WINNT -russ
            /*
              $uname = strtolower(php_uname());
              $retval = "";
              if (strpos($uname, "darwin") !== false) {
              $retval = "MacOSX";
              } else if (strpos($uname, "win") !== false) {
              $retval = "Windows";
              } else if (strpos($uname, "freebsd") !== false) {
              $retval = "FreeBSD";
              } else if (strpos($uname, "openbsd") !== false) {
              $retval = "OpenBSD";
              } else {
             */
            /**
             * @todo convert the bottom bit to read from a list of OS's.
             */
            /*
              $list = @parse_ini_file("lib/zpanel/os.ini", true);
              foreach ($list as $section => $distribution) {
              if (!isset($distribution["Files"])) {

              } else {
              $intBytes = 4096;
              $intLines = 0;
              $intCurLine = 0;
              $strFile = "";
              foreach (preg_split("/;/", $distribution["Files"], -1, PREG_SPLIT_NO_EMPTY) as $filename) {
              if (file_exists($filename)) {
              if (isset($distribution["Name"])) {
              $os = $distribution["Name"];
              }
              }
              }
              if ($os == null) {
              $os = "Unknown";
              }
              }
              }
             */
        } else {
            $retval = "Unknown";
        }
        return $retval;
    }

    /**
     * Returns in human readable form the version of perl installed.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Human readable Perl version number.
     */
    static function ShowPerlVersion() {
        ob_start();
        passthru("perl -v", $result);
        $content_grabbed = ob_get_contents();
        ob_end_clean();
        if (self::ShowOSPlatformVersion() == "Windows") {
            preg_match_all("#(?<=\()(.*?)(?=\))#", $content_grabbed, $perlversion);
        } else {
            preg_match_all("#(\d+).(\d+).(\d+)#", $content_grabbed, $perlversion);
        }
        if (!empty($perlversion[0]) && !empty($perlversion[0][0])) {
            $retval = str_replace("v", "", $perlversion[0][0]);
        } else {
            $retval = "Perl not available";
        }
        return $retval;
    }

    /**
     * Returns the ZPanel version (based on the DB version number.)
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string ZPanel DB Version
     */
    static function ShowZpanelVersion() {
        return ctrl_options::GetSystemOption('dbversion');
    }

}

?>
