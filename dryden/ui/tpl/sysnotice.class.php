<?php

/**
 * @copyright 2014-2023 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_sysnotice {

    public static function Template() {
		$installed = ctrl_options::GetSystemOption('dbversion');
        $lastest = ctrl_options::GetSystemOption('latestzpversion');
		$lastest_tagged = ' (<strong>' . $lastest . '</strong>)';
		$user = ctrl_users::GetUserDetail();
		
		# Check if admin
		if ( $user['usergroup'] == 'Administrators') {
			# Check version and load message here
			# If NEW version
			if ($installed < $lastest) {
				# Set message
				$msg = ('There are currently new updates for your Sentora installation, please download the latest release')
				. $lastest_tagged . ' from <a href="http://www.sentora.org/">http://www.sentora.org/</a>.';
				# Return mesagge
				return ui_sysmessage::shout(
					$msg,
					'Notice',
					'Sentora System Notice:',
					true
				);
			# If BETA version
			} elseif ($installed > $lastest) {
				# Set message
				$msg = 'You are running a <b>BETA</b> release (<strong>' . $installed . '</strong>). Thank you and report what you observed.<br>'
				.'<b>Do not use it for production.</b>';
				# Return mesagge
				return ui_sysmessage::shout(
					$msg,
					'zannounceerror',
					'Sentora <b>BETA</b> System Notice:',
					false
				);
			} else {
				# Do/show nothing
			}	
		# If not admin do nothing
		}
    }
}
?>
