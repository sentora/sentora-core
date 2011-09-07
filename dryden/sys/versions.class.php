<?php

class sys_versions {

    /**
     * Returns the Apache HTTPd Server Version Number
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
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
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @return string PHP version number
     */
    static function ShowPHPVersion() {
        return phpversion();
    }

    /**
     * Returns the MySQL server version number.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @return string MySQL version number 
     */
    static function ShowMySQLVersion() {
        /**
         * @todo Add removal of anything else except for version numbers (such as 'community')
         */
        return mysql_get_server_info();
    }

    /**
     * Returns a human readable copy of the Kernal version number running on the server.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param type $platform
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
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
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
        } else {
            $retval = "Other";
        }
        return $retval;
    }

}

?>
