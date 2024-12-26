<?php

namespace Roundcube\Composer;

class PluginInstaller extends ExtensionInstaller
{
    protected $composer_type = 'roundcube-plugin';

    #[\Override]
    public function getVendorDir()
    {
        return $this->getRoundcubemailInstallPath() . \DIRECTORY_SEPARATOR . 'plugins';
    }

    #[\Override]
    protected function confirmInstall($package_name)
    {
        $config = $this->composer->getConfig()->get('roundcube');

        if (isset($config['enable-plugin'])) {
            $answer = $config['enable-plugin'];
        } else {
            $answer = $this->io->askConfirmation("Do you want to activate the plugin {$package_name}? [Y|n] ", true);
        }

        return $answer;
    }

    #[\Override]
    protected function getConfig($package_name, $config, $add)
    {
        $cur_config = !empty($config['plugins'])
            ? ((array) $config['plugins'])
            : [];
        $new_config = $cur_config;

        if ($add && !in_array($package_name, $new_config, true)) {
            $new_config[] = $package_name;
        } elseif (!$add && ($i = array_search($package_name, $new_config, true)) !== false) {
            unset($new_config[$i]);
        }

        if ($new_config !== $cur_config) {
            $config_val = count($new_config) > 0
                ? "[\n\t'" . implode("',\n\t'", $new_config) . "',\n];"
                : '[];';
            $result = ['plugins', $config_val];
        } else {
            $result = false;
        }

        return $result;
    }
}
