<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Class provides server port monitoring and uptime reporting functionality.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_monitoring {

    /**
     * Reports on whether a TCP or UDP port is listening for connections.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param int $port The port number of which to check (eg. 25 for SMTP).
     * @param boolean $udp Port is a UDP port as opposed to a TCP port.
     * @return boolean 
     * @change P.Peyremorte
     * - added port close if open successes
     */
    static function PortStatus($port, $udp = false) {
        $timeout = ctrl_options::GetSystemOption('servicechk_to');
        $ip = ($udp) ? 'udp://' . $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_ADDR'];
        $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            runtime_hook::Execute('OnPortStatusDown');
            return false;
        }
        fclose($fp);
        runtime_hook::Execute('OnPortStatusUp');
        return true;
    }

    /**
     * Reports on whether a TCP port is listening for connections.
     * @author Pascal peyremorte
     * @param int $port The port number of which to check (eg. 25 for SMTP).
     * @return boolean
     */
    static function LocalPortStatus($port) {
        $timeout = ctrl_options::GetSystemOption('servicechk_to');
        $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, $timeout);
        if ($fp !== false) {
            fclose($fp); #do not leave the port open.
            return true;
        }
        return false; 
    }


    /**
     * Returns a nice human readable copy of the server uptime.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Human readable server uptime.
     */
    static function ServerUptime() {
        if (sys_versions::ShowOSPlatformVersion() == "Linux") {
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
            $days = fs_director::CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = fs_director::CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = fs_director::CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } elseif (sys_versions::ShowOSPlatformVersion() == "Windows") {
            $pagefile = "C:\pagefile.sys";
            $upsince = filemtime($pagefile);
            $gettime = (time() - filemtime($pagefile));
            $days = floor($gettime / (24 * 3600));
            $gettime = $gettime - ($days * (24 * 3600));
            $hours = floor($gettime / (3600));
            $gettime = $gettime - ($hours * (3600));
            $minutes = floor($gettime / (60));
            $gettime = $gettime - ($minutes * 60);
            $seconds = $gettime;
            $days = fs_director::CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = fs_director::CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = fs_director::CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } elseif (sys_versions::ShowOSPlatformVersion() == "MacOSX") {
            $uptime = explode(" ", exec("sysctl -n kern.boottime"));
            $uptime = str_replace(",", "", $uptime[3]);
            $uptime = time() - $uptime;
            $min = $uptime / 60;
            $hours = $min / 60;
            $days = floor($hours / 24);
            $hours = floor($hours - ($days * 24));
            $minutes = floor($min - ($days * 60 * 24) - ($hours * 60));
            $days = fs_director::CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = fs_director::CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = fs_director::CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } elseif (sys_versions::ShowOSPlatformVersion() == "FreeBSD") {
            $uptime = explode(" ", exec("/sbin/sysctl -n kern.boottime"));
            $uptime = str_replace(",", "", $uptime[3]);
            $uptime = time() - $uptime;
            $min = $uptime / 60;
            $hours = $min / 60;
            $days = floor($hours / 24);
            $hours = floor($hours - ($days * 24));
            $minutes = floor($min - ($days * 60 * 24) - ($hours * 60));
            $days = fs_director::CheckForNullValue($days != 1, $days . ' days', $days . ' day');
            $hours = fs_director::CheckForNullValue($hours != 1, $hours . ' hours', $hours . ' hour');
            $minutes = fs_director::CheckForNullValue($minutes != 1, $minutes . ' minutes', $minutes . ' minute');
            $retval = $days . ", " . $hours . ", " . $minutes . "";
        } else {
            $retval = "Unsupported OS";
        }
        return $retval;
    }

    /**
     * Returns the client's IP address.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string The IP address of the current client connection. 
     */
    static function ClientIPAddress() {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns the server's IP address.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @return string Returns the IP address of the server. 
     */
    static function ServerIPAddress() {
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * Checks that an IP address is valid, can be public or private subnet (IPv6 and IPv4).
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $ip The IP address of which to check.
     * @return boolean 
     */
    static function IsAnyValidIP($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks that an IP address is valid (IPv6 and IPv4).
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $ip The IP address of which to check.
     * @return boolean 
     */
    static function IsValidIP($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks that an IPv4 address is valid.
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $ip The IP address of which to check.
     * @return boolean 
     */
    static function IsValidIPv4($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks that an IPv6 address is valid.
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $ip The IP address of which to check.
     * @return boolean 
     */
    static function IsValidIPv6($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        } else {
            return false;
        }
    }

}

?>
