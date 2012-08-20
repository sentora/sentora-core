<?php

/**
 * Darwin System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Darwin.inc.php 453 2011-04-04 18:07:43Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */

/**
 * Darwin sysinfo class
 * get all the required information from Darwin system
 * information may be incomplete
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Darwin extends BSDCommon {

    /**
     * define the regexp for log parser
     */
    public function __construct() {
        parent::__construct();
        $this->error->addWarning("The Darwin version of phpSysInfo is work in progress, some things currently don't work!");
        $this->setCPURegExp1("CPU: (.*) \((.*)-MHz (.*)\)");
        $this->setCPURegExp2("/(.*) ([0-9]+) ([0-9]+) ([0-9]+) ([0-9]+)/");
        $this->setSCSIRegExp1("^(.*): <(.*)> .*SCSI.*device");
    }

    /**
     * get a value from sysctl command
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    protected function grabkey($key) {
        if (CommonFunctions::executeProgram('sysctl', $key, $s, PSI_DEBUG)) {
            $s = preg_replace('/' . $key . ': /', '', $s);
            $s = preg_replace('/' . $key . ' = /', '', $s);
            return $s;
        } else {
            return '';
        }
    }

    /**
     * get a value from ioreg command
     *
     * @param string $key key of the value to get
     *
     * @return string
     */
    private function _grabioreg($key) {
        if (CommonFunctions::executeProgram('ioreg', '-cls "' . $key . '" | grep "' . $key . '"', $s, PSI_DEBUG)) {
            $s = preg_replace('/\|/', '', $s);
            $s = preg_replace('/\+\-\o/', '', $s);
            $s = preg_replace('/[ ]+/', '', $s);
            $s = preg_replace('/<[^>]+>/', '', $s);
            return $s;
        } else {
            return '';
        }
    }

    /**
     * UpTime
     * time the system is running
     *
     * @return void
     */
    private function _uptime() {
        if (CommonFunctions::executeProgram('sysctl', '-n kern.boottime', $a, PSI_DEBUG)) {
            $tmp = explode(" ", $a);
            if ($tmp[0] == "{") { /* kern.boottime= { sec = 1096732600, usec = 885425 } Sat Oct 2 10:56:40 2004 */
                $data = trim($tmp[3], ",");
                $this->sys->setUptime(time() - $data);
            } else { /* kern.boottime= 1096732600 */
                $this->sys->setUptime(time() - $a);
            }
        }
    }

    /**
     * get CPU information
     *
     * @return void
     */
    protected function cpuinfo() {
        $dev = new CpuDevice();
        if (CommonFunctions::executeProgram('hostinfo', '| grep "Processor type"', $buf, PSI_DEBUG)) {
            $dev->setModel(preg_replace('/Processor type: /', '', $buf));
            $buf = $this->grabkey('hw.model');
            if (CommonFunctions::rfts(APP_ROOT . '/data/ModelTranslation.txt', $buffer)) {
                $buffer = preg_split("/\n/", $buffer, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($buffer as $line) {
                    $ar_buf = preg_split("/:/", $line, 2);
                    if (trim($buf) === trim($ar_buf[0])) {
                        $dev->setModel(trim($ar_buf[1]));
                    }
                }
            }
        }
        $dev->setCpuSpeed(round($this->grabkey('hw.cpufrequency') / 1000000));
        $dev->setBusSpeed(round($this->grabkey('hw.busfrequency') / 1000000));
        $dev->setCache(round($this->grabkey('hw.l2cachesize')));
        for ($i = $this->grabkey('hw.ncpu'); $i > 0; $i--) {
            $this->sys->setCpus($dev);
        }
    }

    /**
     * get the pci device information out of ioreg
     *
     * @return void
     */
    protected function pci() {
        $s = $this->_grabioreg('IOPCIDevice');
        $lines = preg_split("/\n/", $s, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($lines as $line) {
            $ar_buf = preg_split("/\s+/", $line, 19);
            $dev = new HWDevice();
            $dev->setName($ar_buf[0]);
            $this->sys->setIdeDevices($dev);
        }
    }

    /**
     * get the ide device information out of ioreg
     *
     * @return void
     */
    protected function ide() {
        $s = $this->_grabioreg('IOATABlockStorageDevice');
        $lines = preg_split("/\n/", $s, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($lines as $line) {
            $ar_buf = preg_split("/\/\//", $line, 19);
            if (isset($ar_buf[1]) && $ar_buf[1] == 'class IOMedia' && preg_match('/Media/', $ar_buf[0])) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[0]);
                $this->sys->setIdeDevices($dev);
            }
        }
    }

    /**
     * get memory and swap information
     *
     * @return void
     */
    protected function memory() {
        $s = $this->grabkey('hw.memsize');
        if (CommonFunctions::executeProgram('vm_stat', '', $pstat, PSI_DEBUG)) {
            $lines = preg_split("/\n/", $pstat, -1, PREG_SPLIT_NO_EMPTY);
            $ar_buf = preg_split("/\s+/", $lines[1], 19);
            // calculate free memory from page sizes (each page = 4MB)
            $this->sys->setMemTotal($s);
            $this->sys->setMemFree($ar_buf[2] * 4 * 1024);
            $this->sys->setMemUsed($this->sys->getMemTotal() - $this->sys->getMemFree());

            if (CommonFunctions::executeProgram('sysctl', 'vm.swapusage | colrm 1 22', $swapBuff, PSI_DEBUG)) {
                $swap1 = preg_split('/M/', $swapBuff);
                $swap2 = preg_split('/=/', $swap1[1]);
                $swap3 = preg_split('/=/', $swap1[2]);
                $dev = new DiskDevice();
                $dev->setName('SWAP');
                $dev->setMountPoint('SWAP');
                $dev->setFsType('swap');
                $dev->setTotal($swap1[0] * 1024 * 1024);
                $dev->setUsed($swap2[1] * 1024 * 1024);
                $dev->setFree($swap3[1] * 1024 * 1024);
                $this->sys->setSwapDevices($dev);
            }
        }
    }

    /**
     * get network information
     *
     * @return void
     */
    private function _network() {
        if (CommonFunctions::executeProgram('netstat', '-nbdi | cut -c1-24,42- | grep Link', $netstat, PSI_DEBUG)) {
            $lines = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                $ar_buf = preg_split("/\s+/", $line, 10);
                if (!empty($ar_buf[0])) {
                    $dev = new NetDevice();
                    $dev->setName($ar_buf[0]);
                    $dev->setTxBytes($ar_buf[8]);
                    $dev->setRxBytes($ar_buf[5]);
                    $dev->setErrors($ar_buf[4] + $ar_buf[7]);
                    if (isset($ar_buf[10])) {
                        $dev->setDrops($ar_buf[10]);
                    }
                    $this->sys->setNetDevices($dev);
                }
            }
        }
    }

    /**
     * get icon name
     *
     * @return void
     */
    protected function distro() {
        $this->sys->setDistributionIcon('Darwin.png');
        if (!CommonFunctions::executeProgram('system_profiler', 'SPSoftwareDataType', $buffer, PSI_DEBUG)) {
            parent::distro();
        } else {
            $arrBuff = preg_split("/\n/", $buffer, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($arrBuff as $line) {
                $arrLine = preg_split("/:/", $line, -1, PREG_SPLIT_NO_EMPTY);
                if (trim($arrLine[0]) === "System Version") {
                    $distro = trim($arrLine[1]);

                    if (preg_match('/^Mac OS/', $distro)) {
                        $this->sys->setDistributionIcon('Apple.png');
                    }

                    $this->sys->setDistribution($distro);
                    return;
                }
            }
        }
    }

    /**
     * get the information
     *
     * @see PSI_Interface_OS::build()
     *
     * @return Void
     */
    function build() {
        parent::build();
        $this->_uptime();
        $this->_network();
    }

}

?>
