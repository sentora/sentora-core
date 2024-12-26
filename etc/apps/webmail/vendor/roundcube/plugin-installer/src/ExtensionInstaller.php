<?php

namespace Roundcube\Composer;

use Composer\Installer\InstallationManager;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\InstalledRepository;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RootPackageRepository;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;
use React\Promise\PromiseInterface;

abstract class ExtensionInstaller extends LibraryInstaller
{
    /** @var string|null */
    private $roundcubemailInstallPath;

    /** @var string */
    protected $composer_type;

    protected function setRoundcubemailInstallPath(InstalledRepositoryInterface $installedRepo): void
    {
        // https://github.com/composer/composer/discussions/11927#discussioncomment-9116893
        $rootPackage = clone $this->composer->getPackage();
        $installedRepo = new InstalledRepository([
            $installedRepo,
            new RootPackageRepository($rootPackage),
        ]);

        $roundcubemailPackages = $installedRepo->findPackagesWithReplacersAndProviders('roundcube/roundcubemail');
        assert(count($roundcubemailPackages) === 1);
        $roundcubemailPackage = $roundcubemailPackages[0];

        if ($roundcubemailPackage === $rootPackage) { // $this->getInstallPath($package) does not work for root package
            $this->initializeVendorDir();
            $installPath = dirname($this->vendorDir);
        } else {
            $installPath = $this->getInstallPath($roundcubemailPackage);
        }

        if ($this->roundcubemailInstallPath === null) {
            $this->roundcubemailInstallPath = $installPath;
        } elseif ($this->roundcubemailInstallPath !== $installPath) {
            throw new \Exception('Install path of "roundcube/roundcubemail" package has unexpectedly changed');
        }
    }

    protected function getRoundcubemailInstallPath(): string
    {
        // install path is not set at composer download phase
        // never assume any path, but for this known composer behaviour get it from backtrace instead
        if ($this->roundcubemailInstallPath === null) {
            $backtrace = debug_backtrace();
            foreach ($backtrace as $frame) {
                // relies on https://github.com/composer/composer/blob/2.7.4/src/Composer/Installer/InstallationManager.php#L243
                if (($frame['object'] ?? null) instanceof InstallationManager
                    && $frame['function'] === 'downloadAndExecuteBatch'
                ) {
                    $this->setRoundcubemailInstallPath($frame['args'][0]);
                }
            }
        }

        return $this->roundcubemailInstallPath;
    }

    #[\Override]
    public function getInstallPath(PackageInterface $package)
    {
        if (!$this->supports($package->getType())) {
            return parent::getInstallPath($package);
        }

        $vendorDir = $this->getVendorDir();

        return $vendorDir . \DIRECTORY_SEPARATOR
            . str_replace('/', \DIRECTORY_SEPARATOR, $this->getPackageName($package));
    }

    private function initializeRoundcubemailEnvironment(): void
    {
        if (!defined('INSTALL_PATH')) {
            define('INSTALL_PATH', $this->getRoundcubemailInstallPath() . '/');
        }
        require_once INSTALL_PATH . 'program/include/iniset.php';
    }

    #[\Override]
    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->setRoundcubemailInstallPath($repo);

        return parent::isInstalled($repo, $package);
    }

    #[\Override]
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->setRoundcubemailInstallPath($repo);
        $this->initializeRoundcubemailEnvironment();
        $this->rcubeVersionCheck($package);

        $postInstall = function () use ($package) {
            $config_file = $this->rcubeConfigFile();
            $package_name = $this->getPackageName($package);
            $package_dir = $this->getInstallPath($package);
            $extra = $package->getExtra();

            if (is_writable($config_file) && \PHP_SAPI === 'cli' && $this->confirmInstall($package_name)) {
                $this->rcubeAlterConfig($package_name);
            }

            // copy config.inc.php.dist -> config.inc.php
            if (is_file($package_dir . \DIRECTORY_SEPARATOR . 'config.inc.php.dist')) {
                $config_exists = false;
                $alt_config_file = $this->rcubeConfigFile($package_name . '.inc.php');

                if (is_file($package_dir . \DIRECTORY_SEPARATOR . 'config.inc.php')) {
                    $config_exists = true;
                } elseif (is_file($alt_config_file)) {
                    $config_exists = true;
                }

                if (!$config_exists && is_writable($package_dir)) {
                    $this->io->write('<info>Creating package config file</info>');
                    copy($package_dir . \DIRECTORY_SEPARATOR . 'config.inc.php.dist', $package_dir . \DIRECTORY_SEPARATOR . 'config.inc.php');
                }
            }

            // initialize database schema
            if (!empty($extra['roundcube']['sql-dir'])) {
                if ($sqldir = realpath($package_dir . \DIRECTORY_SEPARATOR . $extra['roundcube']['sql-dir'])) {
                    $this->io->write("<info>Running database initialization script for {$package_name}</info>");

                    \rcmail_utils::db_init($sqldir);
                }
            }

            // run post-install script
            if (!empty($extra['roundcube']['post-install-script'])) {
                $this->rcubeRunScript($extra['roundcube']['post-install-script'], $package);
            }
        };

        $promise = parent::install($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postInstall);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postInstall();

        return null;
    }

    #[\Override]
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $this->setRoundcubemailInstallPath($repo);
        $this->initializeRoundcubemailEnvironment();
        $this->rcubeVersionCheck($target);

        $extra = $target->getExtra();
        $fs = new Filesystem();

        // backup persistent files e.g. config.inc.php
        $package_dir = $this->getInstallPath($initial);
        $temp_dir = $package_dir . '-' . sprintf('%010d%010d', mt_rand(), mt_rand());

        // make a backup of existing files (for restoring persistent files)
        $fs->copy($package_dir, $temp_dir);

        $postUpdate = function () use ($target, $extra, $fs, $temp_dir) {
            $package_name = $this->getPackageName($target);
            $package_dir = $this->getInstallPath($target);

            // restore persistent files
            $persistent_files = !empty($extra['roundcube']['persistent-files'])
                ? $extra['roundcube']['persistent-files']
                : ['config.inc.php'];
            foreach ($persistent_files as $file) {
                $path = $temp_dir . \DIRECTORY_SEPARATOR . $file;
                if (is_readable($path)) {
                    if ($fs->copy($path, $package_dir . \DIRECTORY_SEPARATOR . $file)) {
                        $this->io->write("<info>Restored {$package_name}/{$file}</info>");
                    } else {
                        throw new \Exception('Restoring ' . $file . ' failed.');
                    }
                }
            }
            // remove backup folder
            $fs->remove($temp_dir);

            // update database schema
            if (!empty($extra['roundcube']['sql-dir'])) {
                if ($sqldir = realpath($package_dir . \DIRECTORY_SEPARATOR . $extra['roundcube']['sql-dir'])) {
                    $this->io->write("<info>Updating database schema for {$package_name}</info>");

                    \rcmail_utils::db_update($sqldir, $package_name);
                }
            }

            // run post-update script
            if (!empty($extra['roundcube']['post-update-script'])) {
                $this->rcubeRunScript($extra['roundcube']['post-update-script'], $target);
            }
        };

        $promise = parent::update($repo, $initial, $target);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postUpdate);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postUpdate();

        return null;
    }

    #[\Override]
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $this->setRoundcubemailInstallPath($repo);
        $this->initializeRoundcubemailEnvironment();

        $config = $this->composer->getConfig()->get('roundcube');

        $postUninstall = function () use ($package, $config) {
            // post-uninstall: deactivate package
            $package_name = $this->getPackageName($package);
            $package_dir = $this->getInstallPath($package);

            $this->rcubeAlterConfig($package_name, false);

            // run post-uninstall script
            $extra = $package->getExtra();
            if (!empty($extra['roundcube']['post-uninstall-script'])) {
                $this->rcubeRunScript($extra['roundcube']['post-uninstall-script'], $package);
            }

            // remove package folder
            if (!empty($config['uninstall-remove-folder'])) {
                $fs = new Filesystem();
                $fs->remove($package_dir);
                $this->io->write("<info>Removed {$package_name} files</info>");
            }
        };

        $promise = parent::uninstall($repo, $package);

        // Composer v2 might return a promise here
        if ($promise instanceof PromiseInterface) {
            return $promise->then($postUninstall);
        }

        // If not, execute the code right away (composer v1, or v2 without async)
        $postUninstall();

        return null;
    }

    #[\Override]
    public function supports($packageType)
    {
        return $packageType === $this->composer_type;
    }

    /**
     * Setup vendor directory to one of these two:
     *
     * @return string
     */
    abstract public function getVendorDir();

    /**
     * Extract the (valid) package name from the package object.
     */
    protected function getPackageName(PackageInterface $package)
    {
        @[$vendor, $packageName] = explode('/', $package->getPrettyName());

        return strtr($packageName, '-', '_');
    }

    /**
     * Check version requirements from the "extra" block of a package
     * against the local Roundcube version.
     */
    private function rcubeVersionCheck($package)
    {
        // read rcube version from iniset
        $rcubeVersion = self::versionNormalize(RCMAIL_VERSION);

        $extra = $package->getExtra();

        if (!empty($extra['roundcube'])) {
            foreach (['min-version' => '>=', 'max-version' => '<='] as $key => $operator) {
                if (!empty($extra['roundcube'][$key])) {
                    $version = self::versionNormalize($extra['roundcube'][$key]);
                    if (!self::versionCompare($rcubeVersion, $version, $operator)) {
                        throw new \Exception('Version check failed! ' . $package->getName() . " requires Roundcube version {$operator} {$version}, {$rcubeVersion} was detected.");
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
        @include $config_file;
        $success = false;
        $varname = '$config';

        if (empty($config) && !empty($rcmail_config)) {
            $config = $rcmail_config;
            $varname = '$rcmail_config';
        }

        if (!empty($config) && is_writable($config_file)) {
            $config_template = @file_get_contents($config_file);
            if ($config_template === false) {
                $config_template = '';
            }

            if ($config = $this->getConfig($package_name, $config, $add)) {
                [$config_name, $config_val] = $config;
                $count = 0;

                if (empty($config_val)) {
                    $new_config = preg_replace(
                        "/(\\{$varname}\\['{$config_name}'\\])\\s+=\\s+(.+);/Uims",
                        '',
                        $config_template,
                        -1,
                        $count
                    );
                } else {
                    $new_config = preg_replace(
                        "/(\\{$varname}\\['{$config_name}'\\])\\s+=\\s+(.+);/Uims",
                        '\\1 = ' . $config_val,
                        $config_template,
                        -1,
                        $count
                    );
                }

                // config option does not exist yet, add it...
                if (!$count) {
                    $var_txt = "\n{$varname}['{$config_name}'] = {$config_val}\n";
                    $new_config = str_replace('?>', $var_txt . '?>', $config_template, $count);

                    if (!$count) {
                        $new_config = $config_template . $var_txt;
                    }
                }

                $success = file_put_contents($config_file, $new_config);
            }
        }

        if ($success && \PHP_SAPI === 'cli') {
            $this->io->write("<info>Updated local config at {$config_file}</info>");
        }

        return $success;
    }

    /**
     * Ask the user to confirm installation.
     */
    protected function confirmInstall($package_name)
    {
        return false;
    }

    /**
     * Generate Roundcube config entry.
     */
    protected function getConfig($package_name, $cur_config, $add)
    {
        return false;
    }

    /**
     * Helper method to get an absolute path to the local Roundcube config file.
     */
    private function rcubeConfigFile($file = 'config.inc.php')
    {
        $config = new \rcube_config();
        $paths = $config->resolve_paths($file);
        $path = $this->getRoundcubemailInstallPath() . '/config/' . $file;

        foreach ($paths as $fpath) {
            if ($fpath && is_file($fpath) && is_readable($fpath)) {
                $path = $fpath;

                break;
            }
        }

        return realpath($path);
    }

    /**
     * Run the given script file.
     */
    private function rcubeRunScript($script, PackageInterface $package)
    {
        $package_name = $this->getPackageName($package);
        $package_type = $package->getType();
        $package_dir = $this->getInstallPath($package);

        // check for executable shell script
        if (($scriptfile = realpath($package_dir . \DIRECTORY_SEPARATOR . $script)) && is_executable($scriptfile)) {
            $script = $scriptfile;
        }

        // run PHP script in Roundcube context
        if ($scriptfile && preg_match('/\.php$/', $scriptfile)) {
            $incdir = realpath($this->getRoundcubemailInstallPath() . '/program/include');
            include_once $incdir . '/iniset.php';
            include $scriptfile;
        }
        // attempt to execute the given string as shell commands
        else {
            $process = new ProcessExecutor($this->io);
            $exitCode = $process->execute($script, $output, $package_dir);
            if ($exitCode !== 0) {
                throw new \RuntimeException('Error executing script: ' . $process->getErrorOutput(), $exitCode);
            }
        }
    }

    /**
     * Normalize Roundcube version string.
     */
    private static function versionNormalize(string $version): string
    {
        $parser = new VersionParser();

        return $parser->normalize(str_replace('-git', '.999', $version));
    }

    /**
     * version_compare() wrapper, originally from composer/semver.
     */
    private static function versionCompare($a, $b, $operator, $compareBranches = false)
    {
        $aIsBranch = substr($a, 0, 4) === 'dev-';
        $bIsBranch = substr($b, 0, 4) === 'dev-';

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
