<?php

namespace Roundcube\Composer;

/**
 * @category Plugins
 * @package  PluginInstaller
 * @author   Philip Weir <roundcube@tehinterweb.co.uk>
 * @license  GPL-3.0+
 * @version  GIT: <git_id>
 * @link     http://github.com/roundcube/plugin-installer
 */
class PluginInstaller extends ExtensionInstaller
{
    protected $composer_type = 'roundcube-plugin';

    public function getVendorDir()
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'plugins';
    }

    protected function confirmInstall($package_name)
    {
        $config = $this->composer->getConfig()->get('roundcube');

        if (!empty($config['enable-plugin'])) {
            $answer = $config['enable-plugin'];
        }
        else {
            $answer = $this->io->askConfirmation("Do you want to activate the plugin $package_name? [Y|n] ", true);
        }

        return $answer;
    }

    protected function getConfig($package_name, $config, $add )
    {
        $cur_config = !empty($config['plugins']) ? ((array) $config['plugins']) : [];
        $new_config = $cur_config;

        if ($add && !in_array($package_name, $new_config)) {
            $new_config[] = $package_name;
        }
        elseif (!$add && ($i = array_search($package_name, $new_config)) !== false) {
            unset($new_config[$i]);
        }

        if ($new_config != $cur_config) {
            $config_val = count($new_config) > 0 ? "[\n\t'" . join("',\n\t'", $new_config) . "',\n];" : "[];";
            $result = ['plugins', $config_val];
        }
        else {
            $result = false;
        }

        return $result;
    }
}
