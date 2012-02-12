<?php
/**
 * Update Notifier Plugin Config File
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_UpdateNotifier
 * @author    Damien ROTH <iysaak@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: UpdateNotifier.config.php 525 2011-11-26 10:19:27Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */

/**
 * define the update info file format
 * - true: Ubuntu Landscape format (file: /var/lib/update-notifier/updates-available)
 * - false: universal format   (format: A;B)
 *          A: total packages to update
 *          B: security packages to update
 */
define('PSI_PLUGIN_UPDATE_NOTIFIER_UBUNTU_LANDSCAPE_FORMAT', true);

/**
 * define the update info file
 */
define('PSI_PLUGIN_UPDATE_NOTIFIER_FILE', '/var/lib/update-notifier/updates-available');
?>
