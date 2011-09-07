<?php

class sys_monitoring {

    /**
     * Reports on whether a TCP or UDP port is listening for connections.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param int $port
     * @param boolean $udp
     * @return boolean 
     */
    static function PortStatus($port, $udp=FALSE) {
        $timeout = 30;
        if ($udp) {
            $ip = 'udp://' . $_SERVER['SERVER_ADDR'];
        } else {
            $ip = $_SERVER['SERVER_ADDR'];
        }
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            $retval = false;
        } else {
            $retval = true;
        }
        return $retval;
    }

    /**
     * Returns a nice human readable copy of the server uptime.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @return string Human readable server uptime.
     * @todo Remove and replace old ZP6 functions with the new eqivilents.
     */
    static function ServerUptime() {
        if (system_versions::ShowOSPlatformVersion() == "Linux") {
            $uptime = trim(exec("cat /proc/uptime"));
            $uptime = explode(" ", $uptime);
            $uptime = $uptime[0];
            $day = 86400;
            $days = floor($uptime / $day);
            $utdelta = $uptime - ($days * $day);
            $hour = 3600;
            $hours = floor($utdelta / $hour);
            $utdelta-=$hours * $hour;
            $minute = 60;
            $minutes = floor($utdelta / $minute);
            $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } elseif (system_versions::ShowOSPlatformVersion() == "Windows") {
            $pagefile = "" . GetSystemOption('windows_drive') . ":\pagefile.sys";
            $upsince = filemtime($pagefile);
            $gettime = (time() - filemtime($pagefile));
            $days = floor($gettime / (24 * 3600));
            $gettime = $gettime - ($days * (24 * 3600));
            $hours = floor($gettime / (3600));
            $gettime = $gettime - ($hours * (3600));
            $minutes = floor($gettime / (60));
            $gettime = $gettime - ($minutes * 60);
            $seconds = $gettime;
            $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } elseif (system_versions::ShowOSPlatformVersion() == "FreeBSD") {
            $uptime = explode(" ", exec("/sbin/sysctl -n kern.boottime"));
            $uptime = str_replace(",", "", $uptime[3]);
            $uptime = time() - $uptime;
            $min = $uptime / 60;
            $hours = $min / 60;
            $days = floor($hours / 24);
            $hours = floor($hours - ($days * 24));
            $minutes = floor($min - ($days * 60 * 24) - ($hours * 60));
            $days = CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } else {
            $retval = "Unsupported O/S";
        }
        return $retval;
    }

}

?>
