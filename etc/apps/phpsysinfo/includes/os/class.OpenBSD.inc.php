<?php
/**
 * OpenBSD System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI OpenBSD OS class
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: class.OpenBSD.inc.php 621 2012-07-29 18:49:04Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * OpenBSD sysinfo class
 * get all the required information from OpenBSD systems
 *
 * @category  PHP
 * @package   PSI OpenBSD OS class
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class OpenBSD extends BSDCommon
{
    /**
     * define the regexp for log parser
     */
    public function __construct($blockname = false)
    {
        parent::__construct($blockname);
//        $this->setCPURegExp1("/^cpu(.*) (.*) MHz/");
        $this->setCPURegExp2("/(.*),(.*),(.*),(.*),(.*)/");
        $this->setSCSIRegExp1("/^(.*) at scsibus.*: <(.*)> .*/");
        $this->setSCSIRegExp2("/^(sd[0-9]+): (.*)MB,/");
        $this->setPCIRegExp1("/(.*) at pci[0-9]+ .* \"(.*)\"/");
        $this->setPCIRegExp2("/\"(.*)\" (.*).* at [.0-9]+ irq/");
    }

    /**
     * get network information
     *
     * @return void
     */
    private function _network()
    {
        CommonFunctions::executeProgram('netstat', '-nbdi | cut -c1-25,44- | grep Link | grep -v \'* \'', $netstat_b, PSI_DEBUG);
        CommonFunctions::executeProgram('netstat', '-ndi | cut -c1-25,44- | grep Link | grep -v \'* \'', $netstat_n, PSI_DEBUG);
        $lines_b = preg_split("/\n/", $netstat_b, -1, PREG_SPLIT_NO_EMPTY);
        $lines_n = preg_split("/\n/", $netstat_n, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0, $max = sizeof($lines_b); $i < $max; $i++) {
            $ar_buf_b = preg_split("/\s+/", $lines_b[$i]);
            $ar_buf_n = preg_split("/\s+/", $lines_n[$i]);
            if (!empty($ar_buf_b[0]) && (!empty($ar_buf_n[3]) || ($ar_buf_n[3] === "0"))) {
                $dev = new NetDevice();
                $dev->setName($ar_buf_b[0]);
                $dev->setTxBytes($ar_buf_b[4]);
                $dev->setRxBytes($ar_buf_b[3]);
                if (sizeof($ar_buf_n) == 9) {
                    $dev->setErrors($ar_buf_n[4] + $ar_buf_n[6]);
                    $dev->setDrops($ar_buf_n[8]);
                } elseif (sizeof($ar_buf_n) == 8) {
                    $dev->setDrops($ar_buf_n[4] + $ar_buf_n[6]);
                }
                if (defined('PSI_SHOW_NETWORK_INFOS') && (PSI_SHOW_NETWORK_INFOS) && (CommonFunctions::executeProgram('ifconfig', $ar_buf_b[0].' 2>/dev/null', $bufr2, PSI_DEBUG))) {
                    $speedinfo = "";
                    $bufe2 = preg_split("/\n/", $bufr2, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($bufe2 as $buf2) {
                        if (preg_match('/^\s+lladdr\s+(\S+)/i', $buf2, $ar_buf2)) {
                            if (!defined('PSI_HIDE_NETWORK_MACADDR') || !PSI_HIDE_NETWORK_MACADDR) $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').preg_replace('/:/', '-', strtoupper($ar_buf2[1])));
                        } elseif (preg_match('/^\s+inet\s+(\S+)\s+netmask/i', $buf2, $ar_buf2)) {
                            $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').$ar_buf2[1]);
                        } elseif ((preg_match('/^\s+inet6\s+([^\s%]+)\s+prefixlen/i', $buf2, $ar_buf2)
                              || preg_match('/^\s+inet6\s+([^\s%]+)%\S+\s+prefixlen/i', $buf2, $ar_buf2))
                              && ($ar_buf2[1]!="::") && !preg_match('/^fe80::/i', $ar_buf2[1])) {
                            $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').strtolower($ar_buf2[1]));
                        } elseif (preg_match('/^\s+media:\s+/i', $buf2) && preg_match('/[\(\s](\d+)(G*)base/i', $buf2, $ar_buf2)) {
                            if (isset($ar_buf2[2]) && strtoupper($ar_buf2[2])=="G") {
                                $unit = "G";
                            } else {
                                $unit = "M";
                            }
                            if (preg_match('/\s(\S+)-duplex/i', $buf2, $ar_buf3))
                                $speedinfo = $ar_buf2[1].$unit.'b/s '.strtolower($ar_buf3[1]);
                            else
                                $speedinfo = $ar_buf2[1].$unit.'b/s';
                        }
                    }
                    if ($speedinfo != "") $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').$speedinfo);
                }
                $this->sys->setNetDevices($dev);
            }
        }
    }

    /**
     * IDE information
     *
     * @return void
     */
    protected function ide()
    {
        foreach ($this->readdmesg() as $line) {
            if (preg_match('/^(.*) at pciide[0-9]+ (.*): <(.*)>/', $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[3]);
                if (defined('PSI_SHOW_DEVICES_INFOS') && PSI_SHOW_DEVICES_INFOS) {
                    // now loop again and find the capacity
                    foreach ($this->readdmesg() as $line2) {
                        if (preg_match("/^(".$ar_buf[1]."): (.*), (.*), (.*)MB, .*$/", $line2, $ar_buf_n)) {
                            $dev->setCapacity($ar_buf_n[4] * 1024 * 1024);
                            break;
                        }
                    }
                }
                $this->sys->setIdeDevices($dev);
            }
        }
    }

    /**
     * get CPU information
     *
     * @return void
     */
    protected function cpuinfo()
    {
        $was = false;
        $cpuarray = array();
        foreach ($this->readdmesg() as $line) {
            if (preg_match("/^cpu([0-9])+: (.*)/", $line, $ar_buf)) {
                $was = true;
                $ar_buf[2] = trim($ar_buf[2]);
                if (preg_match("/^(.+), ([\d\.]+) MHz/", $ar_buf[2], $ar_buf2)) {
                    if (($model = trim($ar_buf2[1])) !== "") {
                        $cpuarray[$ar_buf[1]]['model'] = $model;
                    }
                    if (($speed = trim($ar_buf2[2])) > 0) {
                        $cpuarray[$ar_buf[1]]['speed'] = $speed;
                    }
                } elseif (preg_match("/(\d+)([KM])B \S+ \S+ L[23] cache$/", $ar_buf[2], $ar_buf2)) {
                    if ($ar_buf2[2]=="M") {
                        $cpuarray[$ar_buf[1]]['cache'] = $ar_buf2[1]*1024*1024;
                    } elseif ($ar_buf2[2]=="K") {
                        $cpuarray[$ar_buf[1]]['cache'] = $ar_buf2[1]*1024;
                    }
                } else {
                    $feats = preg_split("/,/", strtolower($ar_buf[2]), -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($feats as $feat) {
                        if (($feat=="vmx") || ($feat=="svm")) {
                            $cpuarray[$ar_buf[1]]['virt'] = $feat;
                        } elseif ($feat=="hv") {
                            if (!isset($cpuarray[$ar_buf[1]]['virt'])) {
                                $cpuarray[$ar_buf[1]]['virt'] = 'hypervisor';
                            }
                            if (defined('PSI_SHOW_VIRTUALIZER_INFO') && PSI_SHOW_VIRTUALIZER_INFO) {
                                $this->sys->setVirtualizer("hypervisor", false);
                            }
                        }
                    }
                }
            } elseif (!preg_match("/^cpu[0-9]+|^mtrr: |^acpitimer[0-9]+: /", $line) && $was) {
                break;
            }
        }

        $ncpu = $this->grabkey('hw.ncpu');
        if (($ncpu === "") || !($ncpu >= 1)) {
            $ncpu = 1;
        }
        $ncpu = max($ncpu, count($cpuarray));

        $model = $this->grabkey('hw.model');
        $speed = $this->grabkey('hw.cpuspeed');
        $vendor = $this->grabkey('machdep.cpuvendor');

        for ($cpu = 0 ; $cpu < $ncpu ; $cpu++) {
            $dev = new CpuDevice();

            if (isset($cpuarray[$cpu]['model'])) {
                $dev->setModel($cpuarray[$cpu]['model']);
            } elseif ($model !== "") {
                $dev->setModel($model);
            }
            if (isset($cpuarray[$cpu]['speed'])) {
                $dev->setCpuSpeed($cpuarray[$cpu]['speed']);
            } elseif ($speed !== "") {
                $dev->setCpuSpeed($speed);
            }
            if (isset($cpuarray[$cpu]['cache'])) {
                $dev->setCache($cpuarray[$cpu]['cache']);
            }
            if (isset($cpuarray[$cpu]['virt'])) {
                $dev->setVirt($cpuarray[$cpu]['virt']);
            }
            if ($vendor !== "") {
                $dev->setVendorId($vendor);
            }
            if (($ncpu == 1) && PSI_LOAD_BAR) {
                $dev->setLoad($this->cpuusage());
            }

            $this->sys->setCpus($dev);
        }
    }

    /**
     * get icon name
     *
     * @return void
     */
    private function _distroicon()
    {
        $this->sys->setDistributionIcon('OpenBSD.png');
    }

    /**
     * Processes
     *
     * @return void
     */
    protected function _processes()
    {
        if (CommonFunctions::executeProgram('ps', 'aux', $bufr, PSI_DEBUG)) {
            $lines = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            $processes['*'] = 0;
            foreach ($lines as $line) {
                if (preg_match("/^\S+\s+\d+\s+\S+\s+\S+\s+\d+\s+\d+\s+\S+\s+(\w)/", $line, $ar_buf)) {
                    $processes['*']++;
                    $state = $ar_buf[1];
                    if ($state == 'I') $state = 'S'; //linux format
                    if (isset($processes[$state])) {
                        $processes[$state]++;
                    } else {
                        $processes[$state] = 1;
                    }
                }
            }
            if ($processes['*'] > 0) {
                $this->sys->setProcesses($processes);
            }
        }
    }

    /**
     * get the information
     *
     * @see BSDCommon::build()
     *
     * @return void
     */
    public function build()
    {
        parent::build();
        if (!$this->blockname || $this->blockname==='vitals') {
            $this->_distroicon();
            $this->_processes();
        }
        if (!$this->blockname || $this->blockname==='network') {
            $this->_network();
        }
    }
}
