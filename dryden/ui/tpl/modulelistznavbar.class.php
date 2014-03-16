<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.1.0
 * @author Jason Davis (jason.davis.fl@gmail.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_modulelistznavbar
{

    public static function Template()
    {

        $active = isset($_REQUEST['module']) ? '' : 'class="active"';
        $line = '<li ' . $active . '><a href="."><: Home :></a></li>';

        $modcats = ui_moduleloader::GetModuleCats();
        rsort($modcats);

        foreach ($modcats as $modcat) {
            $shortName = $modcat['mc_name_vc'];

            switch ($shortName) {
                case 'Account Information':
                    $shortName = 'Account';
                    break;
                case 'Server Admin':
                    $shortName = 'Admin';
                    break;
                case 'Database Management':
                    $shortName = 'Database';
                    break;
                case 'Domain Management':
                    $shortName = 'Domain';
                    break;
                case 'File Management':
                    $shortName = 'File';
                    break;
                case 'Server Admin':
                    $shortName = 'Server';
                    break;
            }

            $shortName = '<: ' . $shortName . ' :>';
            $mods = ui_moduleloader::GetModuleList($modcat['mc_id_pk']);

            if (count($mods) > 0) {
                $line .= '<li class="dropdown">';
// IF Account, show Gravatar Image
                if ($shortName == '<: Account :>') {

                    $currentuser = ctrl_users::GetUserDetail();
                    $image = self::get_gravatar($currentuser['email'], 22, 'mm', 'g', true);
                    $line .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $image . ' ' . $shortName . ' <b class="caret"></b></a>';
                } else {
                    $line .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $shortName . ' <b class="caret"></b></a>';
                }

                $line .= '<ul class="dropdown-menu">';
                foreach ($mods as $mod) {

                    $class_name = str_replace(array(' ', '_'), '-', strtolower($mod['mo_folder_vc']));

                    if (isset($_GET['module']) && $_GET['module'] == $mod['mo_folder_vc']) {
                        $line .= '<li class="active">';
                    } else {
                        $line .= '<li>';
                    }
                    if ($mod['mo_installed_ts'] != 0) {
                        $line .= '<a href="?module=' . $mod['mo_folder_vc'] . '"><i class="icon-' . $class_name . ' greyscale"><img src="/modules/' . $mod['mo_folder_vc'] . '/assets/icon.png" height="16px" width="16px"></i> <: ' . $mod['mo_name_vc'] . ' :></a></li>';
                    } else {
                        $line .= '<a href="?module=' . $mod['mo_folder_vc'] . '"><i class="icon-' . $class_name . '"></i> <: ' . $mod['mo_name_vc'] . ' :></a></li>';
                    }
                    
                }
// If Account tab, show Logout Menu Item
                if ($shortName == '<: Account :>') {
                    $line .= '<li><a href="?logout"><i class="icon-phpinfo"></i> Logout</a></li>';
                }

                $line .= '</ul></li>';
            }
        }

        return $line;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = self::getCurrentProtocol() . 'www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    /**
     * Detects the correct protocol to use when building the Gravatar image URL, this prevents SSL errors if the panel is being hosted over SSL.
     * @return string The protocol prefix.
     */
    private static function getCurrentProtocol()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

}

?>
