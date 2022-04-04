<?php
/**
 * DragonFly System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI DragonFly OS class
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: class.DragonFly.inc.php 287 2009-06-26 12:11:59Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * DragonFly sysinfo class
 * get all the required information from DragonFly system
 *
 * @category  PHP
 * @package   PSI DragonFly OS class
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class DragonFly extends BSDCommon
{
    /**
     * define the regexp for log parser
     */
    public function __construct($blockname = false)
    {
        parent::__construct($blockname);
        $this->setCPURegExp1("/^cpu(.*)\, (.*) MHz/\n/^CPU: (.*) \((.*)-MHz (.*)\)/"); // multiple regexp separated by \n
        $this->setCPURegExp2("/^(.*) at scsibus.*: <(.*)> .*/");
        $this->setSCSIRegExp2("/^(da[0-9]+): (.*)MB /");
        $this->setPCIRegExp1("/(.*): <(.*)>(.*) (pci|legacypci)[0-9]+$/");
        $this->setPCIRegExp2("/(.*): <(.*)>.* at [0-9\.]+$/");
    }

    /**
     * get network information
     *
     * @return void
     */
    private function _network()
    {
        CommonFunctions::executeProgram('netstat', '-nbdi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"', $netstat_b);
        CommonFunctions::executeProgram('netstat', '-ndi | cut -c1-25,44- | grep "^[a-z]*[0-9][ \t].*Link"', $netstat_n);
        $lines_b = preg_split("/\n/", $netstat_b, -1, PREG_SPLIT_NO_EMPTY);
        $lines_n = preg_split("/\n/", $netstat_n, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0, $max = sizeof($lines_b); $i < $max; $i++) {
            $ar_buf_b = preg_split("/\s+/", $lines_b[$i]);
            $ar_buf_n = preg_split("/\s+/", $lines_n[$i]);
            if (!empty($ar_buf_b[0]) && (!empty($ar_buf_n[5]) || ($ar_buf_n[5] === "0"))) {
                $dev = new NetDevice();
                $dev->setName($ar_buf_b[0]);
                $dev->setTxBytes($ar_buf_b[8]);
                $dev->setRxBytes($ar_buf_b[5]);
                $dev->setErrors($ar_buf_n[4] + $ar_buf_n[6]);
                $dev->setDrops($ar_buf_n[8]);
                $this->sys->setNetDevices($dev);
            }
        }
    }

    /**
     * get the ide information
     *
     * @return void
     */
    protected function ide()
    {
        foreach ($this->readdmesg() as $line) {
            if (preg_match('/^(.*): (.*) <(.*)> at (ata[0-9]+\-(.*)) (.*)/', $line, $ar_buf)) {
                $dev = new HWDevice();
                $dev->setName($ar_buf[1]);
                if (defined('PSI_SHOW_DEVICES_INFOS') && PSI_SHOW_DEVICES_INFOS && !preg_match("/^acd[0-9]+(.*)/", $ar_buf[1])) {
                    $dev->setCapacity($ar_buf[2] * 1024 * 1024);
                }
                $this->sys->setIdeDevices($dev);
            }
        }
    }

    /**
     * get icon name
     *
     * @return void
     */
    private function _distroicon()
    {
        $this->sys->setDistributionIcon('DragonFly.png');
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
