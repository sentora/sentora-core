<?php

namespace Roundcube\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\Version\VersionParser;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\ProcessExecutor;
use Composer\Util\Filesystem;
use React\Promise\PromiseInterface;

/**
 * @category Plugins
 * @package  PluginInstaller
 * @author   Till Klampaeckel <till@php.net>
 * @author   Thomas Bruederli <thomas@roundcube.net>
 * @author   Philip Weir <roundcube@tehinterweb.co.uk>
 * @license  GPL-3.0+
 * @version  GIT: <git_id>
 * @link     http://github.com/roundcube/plugin-installer
 */
class ExtensionInstaller extends LibraryInstaller
{
    protected $composer_type;

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        static $vendorDir;
        if ($vendorDir === null) {
            $vendorDir = $this->getVendorDir();
        }

        return sprintf('%s/%s', $vendorDir, $this->getPackageName($package));
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        // initialize Roundcube environment
        define('INSTALL_PATH', getcwd() . '/');
        include_once(INSTALL_PATH . 'program/include/clisetup.php');

        $this->rcubeVersionCheck($package);

        $self = $this;
        $postInstall = function() use ($self, $package) {
            $config_file  = $self->rcubeConfigFile();
            $package_name = $self->getPackageName($package);
            $package_dir  = $self->getVendorDir() . DIRECTORY_SEPARATOR . $package_name;
            $extra        = $package->getExtra();

            if (is_writeable($config_file) && php_sapi_name() == 'cli' && $this->confirmInstall($package_name)) {
                $self->rcubeAlterConfig($package_name);
            }

            // copy config.inc.php.dist -> config.inc.php
            if (is_file($package_dir . DIRECTORY_SEPARATOR . 'config.inc.php.dist')) {
                $config_exists   = false;
                $alt_config_file = $self->rcubeConfigFile($package_name . '.inc.php');

                if (is_file($package_dir . DIRECTORY_SEPARATOR . 'config.inc.php')) {
                    $config_exists = true;
                }
                elseif (is_file($alt_config_file)) {
                    $config_exists = true;
                }

                if (!$config_exists && is_writeable($package_dir)) {
                    $self->io->write("<info>Creating package config file</info>");
                    copy($package_dir . DIRECTORY_SEPARATOR . 'config.inc.php.dist', $package_dir . DIRECTORY_SEPARATOR . 'config.inc.php');
                }
            }

            // initialize database schema
            if (!empty($extra['roundcube']['sql-dir'])) {
                if ($sqldir = realpath($package_dir . DIRECTORY_SEPARATOR . $extra['roundcube']['sql-dir'])) {
                    $self->io->write("<info>Running database initialization script for $package_name</info>");

                    $roundcube_version = self::versionNormalize(RCMAIL_VERSION);
                    if (self::versionCompare($roundcube_version, '1.2.0', '>=')) {
                        \rcmail_utils::db_init($sqldir);
                    }
                    else {
                        throw new \Exception("Database initialization failed. Roundcube 1.2.0 or above required.");
                    }
                }
            }

            // run post-install script
            if (!empty($extra['roundcube']['post-install-script'])) {
                $self->rcubeRunScript($extra['roundcube']['post-install-script'], $package);
            }
        };

        $promise = parent::install($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postInstall);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postInstall();
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        // initialize Roundcube environment
        define('INSTALL_PATH', getcwd() . '/');
        include_once(INSTALL_PATH . 'program/include/clisetup.php');

        $this->rcubeVersionCheck($target);

        $self  = $this;
        $extra = $target->getExtra();
        $fs    = new Filesystem();

        // backup persistent files e.g. config.inc.php
        $package_name     = $self->getPackageName($initial);
        $package_dir      = $self->getVendorDir() . DIRECTORY_SEPARATOR . $package_name;
        $temp_dir         = $package_dir . '-' . sprintf('%010d%010d', mt_rand(), mt_rand());

        // make a backup of existing files (for restoring persistent files)
        $fs->copy($package_dir, $temp_dir);

        $postUpdate = function() use ($self, $target, $extra, $fs, $temp_dir) {
            $package_name = $self->getPackageName($target);
            $package_dir  = $self->getVendorDir() . DIRECTORY_SEPARATOR . $package_name;

            // restore persistent files
            $persistent_files = !empty($extra['roundcube']['persistent-files']) ? $extra['roundcube']['persistent-files'] : ['config.inc.php'];
            foreach ($persistent_files as $file) {
                $path = $temp_dir . DIRECTORY_SEPARATOR . $file;
                if (is_readable($path)) {
                    if ($fs->copy($path, $package_dir . DIRECTORY_SEPARATOR . $file)) {
                        $self->io->write("<info>Restored $package_name/$file</info>");
                    }
                    else {
                        throw new \Exception("Restoring " . $file . " failed.");
                    }
                }
            }
            // remove backup folder
            $fs->remove($temp_dir);

            // update database schema
            if (!empty($extra['roundcube']['sql-dir'])) {
                if ($sqldir = realpath($package_dir . DIRECTORY_SEPARATOR . $extra['roundcube']['sql-dir'])) {
                    $self->io->write("<info>Updating database schema for $package_name</info>");

                    $roundcube_version = self::versionNormalize(RCMAIL_VERSION);
                    if (self::versionCompare($roundcube_version, '1.2.0', '>=')) {
                        \rcmail_utils::db_update($sqldir, $package_name);
                    }
                    else {
                        throw new \Exception("Database update failed. Roundcube 1.2.0 or above required.");
                    }
                }
            }

            // run post-update script
            if (!empty($extra['roundcube']['post-update-script'])) {
                $self->rcubeRunScript($extra['roundcube']['post-update-script'], $target);
            }
        };

        $promise = parent::update($repo, $initial, $target);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postUpdate);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postUpdate();
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        // initialize Roundcube environment
        define('INSTALL_PATH', getcwd() . '/');
        include_once(INSTALL_PATH . 'program/include/clisetup.php');

        $self   = $this;
        $config = $self->composer->getConfig()->get('roundcube');

        $postUninstall = function() use ($self, $package, $config) {
            // post-uninstall: deactivate package
            $package_name = $self->getPackageName($package);
            $package_dir  = $self->getVendorDir() . DIRECTORY_SEPARATOR . $package_name;

            $self->rcubeAlterConfig($package_name, false);

            // run post-uninstall script
            $extra = $package->getExtra();
            if (!empty($extra['roundcube']['post-uninstall-script'])) {
                $self->rcubeRunScript($extra['roundcube']['post-uninstall-script'], $package);
            }

            // remove package folder
            if (!empty($config['uninstall-remove-folder'])) {
                $fs = new Filesystem();
                $fs->remove($package_dir);
                $self->io->write("<info>Removed $package_name files</info>");
            }
        };

        $promise = parent::uninstall($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postUninstall);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postUninstall();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return $packageType === $this->composer_type;
    }

    /**
     * Setup vendor directory to one of these two:
     *
     * @return string
     */
    public function getVendorDir()
    {
        return getcwd();
    }

    /**
     * Extract the (valid) package name from the package object
     */
    protected function getPackageName(PackageInterface $package)
    {
        @list($vendor, $packageName) = explode('/', $package->getPrettyName());

        return strtr($packageName, '-', '_');
    }

    /**
     * Check version requirements from the "extra" block of a package
     * against the local Roundcube version
     */
    private function rcubeVersionCheck($package)
    {
        // read rcube version from iniset
        $rcubeVersion = self::versionNormalize(RCMAIL_VERSION);

        if (empty($rcubeVersion)) {
            throw new \Exception("Unable to find a Roundcube installation in $rootdir");
        }

        $extra = $package->getExtra();

        if (!empty($extra['roundcube'])) {
            foreach (['min-version' => '>=', 'max-version' => '<='] as $key => $operator) {
                if (!empty($extra['roundcube'][$key])) {
                    $version = self::versionNormalize($extra['roundcube'][$key]);
                    if (!self::versionCompare($rcubeVersion, $version, $operator)) {
                        throw new \Exception("Version check failed! " . $package->getName() . " requires Roundcube version $operator $version, $rcubeVersion was detected.");
                    }
                }
            }
        }
    }

    /**
     * Add or remove the given package to the Roundcube config.
     */
    private function rcubeAlterConfig($package_name, $add = true)
    {
        $config_file = $this->rcubeConfigFile();
        @include($config_file);
        $success = false;
        $varname = '$config';

        if (empty($config) && !empty($rcmail_config)) {
            $config  = $rcmail_config;
            $varname = '$rcmail_config';
        }

        if (!empty($config) && is_writeable($config_file)) {
            $config_template = @file_get_contents($config_file) ?: '';

            if ($config = $this->getConfig($package_name, $config, $add)) {
                list($config_name, $config_val) = $config;
                $count = 0;

                if (empty($config_val)) {
                    $new_config = preg_replace(
                        "/(\\$varname\['$config_name'\])\s+=\s+(.+);/Uims",
                        "",
                        $config_template, -1, $count);
                }
                else {
                    $new_config = preg_replace(
                        "/(\\$varname\['$config_name'\])\s+=\s+(.+);/Uims",
                        "\\1 = " . $config_val,
                        $config_template, -1, $count);
                }

                // config option does not exist yet, add it...
                if (!$count) {
                    $var_txt    = "\n{$varname}['$config_name'] = $config_val\n";
                    $new_config = str_replace('?>', $var_txt . '?>', $config_template, $count);

                    if (!$count) {
                        $new_config = $config_template . $var_txt;
                    }
                }

                $success = file_put_contents($config_file, $new_config);
            }
        }

        if ($success && php_sapi_name() == 'cli') {
            $this->io->write("<info>Updated local config at $config_file</info>");
        }

        return $success;
    }

    /**
    * Ask the user to confirm installation
    */
    protected function confirmInstall($package_name)
    {
        return false;
    }

    /**
    * Generate Roundcube config entry
    */
    protected function getConfig($package_name, $cur_config, $add)
    {
        return false;
    }

    /**
     * Helper method to get an absolute path to the local Roundcube config file
     */
    private function rcubeConfigFile($file = 'config.inc.php')
    {
        $config = new \rcube_config();
        $paths  = $config->resolve_paths($file);
        $path   = getcwd() . '/config/' . $file;

        foreach ($paths as $fpath) {
            if ($fpath && is_file($fpath) && is_readable($fpath)) {
                $path = $fpath;
                break;
            }
        }

        return realpath($path);
    }

    /**
     * Run the given script file
     */
    private function rcubeRunScript($script, PackageInterface $package)
    {
        $package_name = $this->getPackageName($package);
        $package_type = $package->getType();
        $package_dir  = $this->getVendorDir() . DIRECTORY_SEPARATOR . $package_name;

        // check for executable shell script
        if (($scriptfile = realpath($package_dir . DIRECTORY_SEPARATOR . $script)) && is_executable($scriptfile)) {
            $script = $scriptfile;
        }

        // run PHP script in Roundcube context
        if ($scriptfile && preg_match('/\.php$/', $scriptfile)) {
            $incdir = realpath(getcwd() . '/program/include');
            include_once($incdir . '/iniset.php');
            include($scriptfile);
        }
        // attempt to execute the given string as shell commands
        else {
            $process = new ProcessExecutor($this->io);
            $exitCode = $process->execute($script, $output, $package_dir);
            if ($exitCode !== 0) {
                throw new \RuntimeException('Error executing script: '. $process->getErrorOutput(), $exitCode);
            }
        }
    }

    /**
     * normalize Roundcube version string
     */
    private static function versionNormalize($version)
    {
        $parser = new VersionParser;

        return $parser->normalize(str_replace('-git', '.999', $version));
    }

    /**
     * version_compare() wrapper, originally from composer/semver
     */
    private static function versionCompare($a, $b, $operator, $compareBranches = false)
    {
        $aIsBranch = 'dev-' === substr($a, 0, 4);
        $bIsBranch = 'dev-' === substr($b, 0, 4);

        if ($aIsBranch && $bIsBranch) {
            return $operator === '==' && $a === $b;
        }

        // when branches are not comparable, we make sure dev branches never match anything
        if (!$compareBranches && ($aIsBranch || $bIsBranch)) {
            return false;
        }

        return version_compare($a, $b, $operator);
    }
}
