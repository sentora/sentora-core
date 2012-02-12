<?php
/**
 * MDSTAT Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_MDStatus
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: MDStatus.config.php 313 2009-08-02 11:24:58Z jacky672 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * define how to access the mdstat statistic data
 * - 'file' /proc/mdstat is read
 * - 'data' (a file must be available in the data directory of the phpsysinfo installation with the filename "mdstat.txt"; content is the output from "cat /proc/mdstat")
 */
define('PSI_PLUGIN_MDSTAT_ACCESS', 'file');
?>
