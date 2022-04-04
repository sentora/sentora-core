<?php
/**
 * IBM AIX System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI AIX OS class
 * @author    Krzysztof Paz (kpaz@gazeta.pl) based on HPUX of Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2011 Krzysztof Paz
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: class.AIX.inc.php 287 2009-06-26 12:11:59Z Krzysztof Paz, IBM POLSKA
 * @link      http://phpsysinfo.sourceforge.net
 */
/**
* IBM AIX sysinfo class
* get all the required information from IBM AIX system
*
* @category  PHP
* @package   PSI AIX OS class
* @author    Krzysztof Paz (kpaz@gazeta.pl) based on Michael Cramer <BigMichi1@users.sourceforge.net>
* @copyright 2011 Krzysztof Paz
* @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
* @version   Release: 3.0
* @link      http://phpsysinfo.sourceforge.net
*/
class AIX extends OS
{
    /**
     * uptime command result.
     */
    private $_uptime = null;

    /**
     * prtconf command result.
     */
    private $_aixdata = array();

    /**
     * Virtual Host Name
     * @return void
     */
    private function _hostname()
    {
        /*   if (PSI_USE_VHOST) {
               if (CommonFunctions::readenv('SERVER_NAME', $hnm)) $this->sys->setHostname($hnm);
           } else {
               if (CommonFunctions::executeProgram('hostname', '', $ret)) {
                   $this->sys->setHostname($ret);
               }
           } */
        if (CommonFunctions::readenv('SERVER_NAME', $hnm)) $this->sys->setHostname($hnm);

    }

    /**
     * IBM AIX Version
     * @return void
     */
    private function _kernel()
    {
        if (CommonFunctions::executeProgram('oslevel', '', $ret1) && CommonFunctions::executeProgram('oslevel', '-s', $ret2)) {
            $this->sys->setKernel($ret1 . '   (' . $ret2 . ')');
        }
    }

    /**
     * UpTime
     * time the system is running
     * @return void
     */
    private function _uptime()
    {
        if (($this->_uptime !== null) || CommonFunctions::executeProgram('uptime', '', $this->_uptime)) {
            if (preg_match("/up (\d+) day[s]?,\s*(\d+):(\d+),/", $this->_uptime, $ar_buf)) {
                $min = $ar_buf[3];
                $hours = $ar_buf[2];
                $days = $ar_buf[1];
                $this->sys->setUptime($days * 86400 + $hours * 3600 + $min * 60);
            }
        }
    }

    /**
     * Processor Load
     * optionally create a loadbar
     * @return void
     */
    private function _loadavg()
    {
        if (($this->_uptime !== null) || CommonFunctions::executeProgram('uptime', '', $this->_uptime)) {
            if (preg_match("/average: (.*), (.*), (.*)$/", $this->_uptime, $ar_buf)) {
                $this->sys->setLoad($ar_buf[1].' '.$ar_buf[2].' '.$ar_buf[3]);
            }
        }
    }

    /**
     * CPU information
     * All of the tags here are highly architecture dependant
     * @return void
     */
    private function _cpuinfo()
    {
        $ncpu = 0;
        $tcpu = "";
        $vcpu = "";
        $ccpu = "";
        $scpu = "";
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^Number Of Processors:\s+(\d+)/", $line, $ar_buf)) {
                $ncpu = $ar_buf[1];
            }
            if (preg_match("/^Processor Type:\s+(.+)/", $line, $ar_buf)) {
                $tcpu = $ar_buf[1];
            }
            if (preg_match("/^Processor Version:\s+(.+)/", $line, $ar_buf)) {
                $vcpu = $ar_buf[1];
            }
            if (preg_match("/^CPU Type:\s+(.+)/", $line, $ar_buf)) {
                $ccpu = $ar_buf[1];
            }
            if (preg_match("/^Processor Clock Speed:\s+(\d+)\s/", $line, $ar_buf)) {
                $scpu = $ar_buf[1];
            }
        }
        for ($i = 0; $i < $ncpu; $i++) {
            $dev = new CpuDevice();
            if (trim($tcpu) != "") {
                $cpu = trim($tcpu);
                if (trim($vcpu) != "") $cpu .= " ".trim($vcpu);
                if (trim($ccpu) != "") $cpu .= " ".trim($ccpu);
                $dev->setModel($cpu);
            }
            if (trim($scpu) != "") {
                $dev->setCpuSpeed(trim($scpu));
            }
            $this->sys->setCpus($dev);
        }
    }

    /**
     * PCI devices
     * @return void
     */
    private function _pci()
    {
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^[\*\+]\s\S+\s+\S+\s+(.*PCI.*)/", $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName(trim($ar_buf[1]));
                $this->sys->setPciDevices($dev);
            }
        }
    }

    /**
     * IDE devices
     * @return void
     */
    private function _ide()
    {
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^[\*\+]\s\S+\s+\S+\s+(.*IDE.*)/", $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName(trim($ar_buf[1]));
                $this->sys->setIdeDevices($dev);
            }
        }
    }

    /**
     * SCSI devices
     * @return void
     */
    private function _scsi()
    {
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^[\*\+]\s\S+\s+\S+\s+(.*SCSI.*)/", $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName(trim($ar_buf[1]));
                $this->sys->setScsiDevices($dev);
            }
        }
    }

    /**
     * USB devices
     * @return void
     */
    private function _usb()
    {
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^[\*\+]\s\S+\s+\S+\s+(.*USB.*)/", $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName(trim($ar_buf[1]));
                $this->sys->setUsbDevices($dev);
            }
        }
    }

    /**
     * Network devices
     * includes also rx/tx bytes
     * @return void
     */
    private function _network()
    {
        if (CommonFunctions::executeProgram('netstat', '-ni | tail -n +2', $netstat)) {
            $lines = preg_split("/\n/", $netstat, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                $ar_buf = preg_split("/\s+/", $line);
                if (! empty($ar_buf[0]) && ! empty($ar_buf[3])) {
                    $dev = new NetDevice();
                    $dev->setName($ar_buf[0]);
                    $dev->setRxBytes($ar_buf[4]);
                    $dev->setTxBytes($ar_buf[6]);
                    $dev->setErrors($ar_buf[5] + $ar_buf[7]);
                    //$dev->setDrops($ar_buf[8]);
                    $this->sys->setNetDevices($dev);
                }
            }
        }
    }

    /**
     * Physical memory information and Swap Space information
     * @return void
     */
    private function _memory()
    {
        $mems = "";
        $tswap = "";
        $pswap = "";
        foreach ($this->readaixdata() as $line) {
            if (preg_match("/^Good Memory Size:\s+(\d+)\s+MB/", $line, $ar_buf)) {
                $mems = $ar_buf[1];
            }
            if (preg_match("/^\s*Total Paging Space:\s+(\d+)MB/", $line, $ar_buf)) {
                $tswap = $ar_buf[1];
            }
            if (preg_match("/^\s*Percent Used:\s+(\d+)%/", $line, $ar_buf)) {
                $pswap = $ar_buf[1];
            }
        }
        if (trim($mems) != "") {
            $mems = $mems*1024*1024;
            $this->sys->setMemTotal($mems);
            $memu = 0;
            $memf = 0;
            if (CommonFunctions::executeProgram('svmon', '-G', $buf)) {
                if (preg_match("/^memory\s+\d+\s+(\d+)\s+/", $buf, $ar_buf)) {
                    $memu = $ar_buf[1]*1024*4;
                    $memf = $mems - $memu;
                }
            }
            $this->sys->setMemUsed($memu);
            $this->sys->setMemFree($memf);
//            $this->sys->setMemApplication($mems);
//            $this->sys->setMemBuffer($mems);
//            $this->sys->setMemCache($mems);
        }
        if (trim($tswap) != "") {
            $dev = new DiskDevice();
            $dev->setName("SWAP");
            $dev->setFsType('swap');
            $dev->setTotal($tswap * 1024 * 1024);
            if (trim($pswap) != "") {
                $dev->setUsed($dev->getTotal() * $pswap / 100);
            }
            $dev->setFree($dev->getTotal() - $dev->getUsed());
            $this->sys->setSwapDevices($dev);
        }
    }

    /**
     * filesystem information
     *
     * @return void
     */
    private function _filesystems()
    {
        if (CommonFunctions::executeProgram('df', '-kP', $df, PSI_DEBUG)) {
            $mounts = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);
            if (CommonFunctions::executeProgram('mount', '-v', $s, PSI_DEBUG)) {
                $lines = preg_split("/\n/", $s, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($lines as $line) {
                    $a = preg_split('/ /', $line, -1, PREG_SPLIT_NO_EMPTY);
                    $fsdev[$a[0]] = $a[4];
                }
            }
            foreach ($mounts as $mount) {
                $ar_buf = preg_split("/\s+/", $mount, 6);
                $dev = new DiskDevice();
                $dev->setName($ar_buf[0]);
                $dev->setTotal($ar_buf[1] * 1024);
                $dev->setUsed($ar_buf[2] * 1024);
                $dev->setFree($ar_buf[3] * 1024);
                $dev->setMountPoint($ar_buf[5]);
                if (isset($fsdev[$ar_buf[0]])) {
                    $dev->setFsType($fsdev[$ar_buf[0]]);
                }
                $this->sys->setDiskDevices($dev);
            }
        }
    }

    /**
     * Distribution
     *
     * @return void
     */
    private function _distro()
    {
        $this->sys->setDistribution('IBM AIX');
        $this->sys->setDistributionIcon('AIX.png');
    }

    /**
     * IBM AIX informations by K.PAZ
     * @return array
     */
    private function readaixdata()
    {
        if (count($this->_aixdata) === 0) {
            if (CommonFunctions::executeProgram('prtconf', '', $bufr)) {
                $this->_aixdata = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            }
        }

        return $this->_aixdata;
    }

    /**
     * get the information
     *
     * @see PSI_Interface_OS::build()
     *
     * @return void
     */
    public function build()
    {
        $this->error->addWarning("The AIX version of phpSysInfo is a work in progress, some things currently don't work");
        if (!$this->blockname || $this->blockname==='vitals') {
            $this->_distro();
            $this->_hostname();
            $this->_kernel();
            $this->_uptime();
            $this->_users();
            $this->_loadavg();
        }
        if (!$this->blockname || $this->blockname==='hardware') {
            $this->_cpuinfo();
            $this->_pci();
            $this->_ide();
            $this->_scsi();
            $this->_usb();
        }
        if (!$this->blockname || $this->blockname==='memory') {
            $this->_memory();
        }
        if (!$this->blockname || $this->blockname==='filesystem') {
            $this->_filesystems();
        }
        if (!$this->blockname || $this->blockname==='network') {
            $this->_network();
        }
    }
}
