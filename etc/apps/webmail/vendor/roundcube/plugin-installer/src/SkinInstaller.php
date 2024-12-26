<?php

namespace Roundcube\Composer;

class SkinInstaller extends ExtensionInstaller
{
    protected $composer_type = 'roundcube-skin';

    #[\Override]
    public function getVendorDir()
    {
        return $this->getRoundcubemailInstallPath() . \DIRECTORY_SEPARATOR . 'skins';
    }

    #[\Override]
    protected function confirmInstall($package_name)
    {
        $config = $this->composer->getConfig()->get('roundcube');

        if (isset($config['enable-skin'])) {
            $answer = $config['enable-skin'];
        } else {
            $answer = $this->io->askConfirmation("Do you want to activate the skin {$package_name}? [Y|n] ", true);
        }

        return $answer;
    }

    #[\Override]
    protected function getConfig($package_name, $config, $add)
    {
        $cur_config = !empty($config['skin'])
            ? $config['skin']
            : null;
        $new_config = $cur_config;

        if ($add && $new_config !== $package_name) {
            $new_config = $package_name;
        } elseif (!$add && $new_config === $package_name) {
            $new_config = null;
        }

        if ($new_config !== $cur_config) {
            $config_val = !empty($new_config)
                ? "'{$new_config}';"
                : null;
            $result = ['skin', $config_val];
        } else {
            $result = false;
        }

        return $result;
    }
}
