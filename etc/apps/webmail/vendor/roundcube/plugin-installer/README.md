# Plugin Installer for Roundcube

This installer ensures that plugins and skins end up in the correct directory:

 * Plugins - `<roundcube-root>/plugins/plugin-name`
 * Skins - `<roundcube-root>/skins/skin-name`

## Minimum setup

 * create a `composer.json` file in your plugin's repository
 * add the following contents

### sample composer.json for plugins

    {
        "name": "<your-vendor-name>/<plugin-name>",
        "type": "roundcube-plugin",
        "license": "GPL-3.0+",
        "require": {
            "roundcube/plugin-installer": ">=0.3.0"
        }
    }

### sample composer.json for skins

    {
        "name": "<your-vendor-name>/<skin-name>",
        "type": "roundcube-skin",
        "license": "GPL-3.0+",
        "require": {
            "roundcube/plugin-installer": ">=0.3.0"
        }
    }

## Roundcube specific composer.json params

For both plugins and skins you can, optionally, add the following section to your `composer.json` file. All properties are optional and provided below with example values.
`persistent-files` defines a list of files which should be maintained across updates. By default only `config.inc.php` is maintained. The array should contain paths relative to the root of your plugin.

    "extra": {
        "roundcube": {
            "min-version": "1.4.0",
            "sql-dir": "./SQL",
            "post-install-script": "./bin/install.sh",
            "post-update-script": "./bin/update.sh",
            "persistent-files": ["config.inc.php", "skins/elastic/_custom.less"]
        }
    }

## Configuration

This installer will ask if you want to enable each plugin or skin as it is installed. To always enable all plugins or skins add `enable-plugin`/`enable-skin` to the `config` section in the `composer.json` in the root of your Roundcube directory.
When uninstalling packages Composer will not remove the folder. To remove the folder set `uninstall-remove-folder` in your config.

    "config": {
        "roundcube": {
            "enable-plugin": true,
            "enable-skin": true,
            "uninstall-remove-folder": true
        }
    }

## Repository

Submit your plugin or skin to [Packagist](https://packagist.org/).

## Installation

 * clone Roundcube
 * `cp composer.json-dist composer.json`
 * add your plugin in the `require` section of composer.json
 * `composer.phar install`

Read the whole story at [plugins.roundcube.net](http://plugins.roundcube.net/about).
