<?php
/**
 * QNX System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI QNX OS class
 * @author    Mieczyslaw Nalewaj <namiltd@users.sourceforge.net>
 * @copyright 2012 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: class.QNX.inc.php 687 2012-09-06 20:54:49Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * QNX sysinfo class
 * get all the required information from QNX system
 *
 * @category  PHP
 * @package   PSI QNX OS class
 * @author    Mieczyslaw Nalewaj <namiltd@users.sourceforge.net>
 * @copyright 2012 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class QNX extends OS
{
    /**
     * get the cpu information
     *
     * @return void
     */
    protected function _cpuinfo()
    {
        if (CommonFunctions::executeProgram('pidin', 'info', $buf)
           && preg_match('/^Processor\d+: (.*)/m', $buf)) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^Processor\d+: (.+)/', $line, $proc)) {
                    $dev = new CpuDevice();
                    $dev->SetModel(trim($proc[1]));
                    if (preg_match('/(\d+)MHz/', $proc[1], $mhz)) {
                        $dev->setCpuSpeed($mhz[1]);
                    }
                    $this->sys->setCpus($dev);
                }
            }
        }
    }

    /**
     * QNX Version
     *
     * @return void
     */
    private function _kernel()
    {
        if (CommonFunctions::executeProgram('uname', '-rvm', $ret)) {
            $this->sys->setKernel($ret);
        }
    }

    /**
     * Distribution
     *
     * @return void
     */
    protected function _distro()
    {
        if (CommonFunctions::executeProgram('uname', '-sr', $ret))
            $this->sys->setDistribution($ret);
        else
            $this->sys->setDistribution('QNX');

        $this->sys->setDistributionIcon('QNX.png');
    }

    /**
     * UpTime
     * time the system is running
     *
     * @return void
     */
    private function _uptime()
    {

        if (CommonFunctions::executeProgram('pidin', 'info', $buf)
           && preg_match('/^.* BootTime:(.*)/', $buf, $bstart)
           && CommonFunctions::executeProgram('date', '', $bstop)) {
            date_default_timezone_set('UTC');
            $uptime = strtotime($bstop)-strtotime($bstart[1]);
            if ($uptime > 0) $this->sys->setUptime($uptime);
        }
    }

    /**
     * Number of Users
     *
     * @return void
     */
    protected function _users()
    {
        $this->sys->setUsers(1);
    }

    /**
     * Virtual Host Name
     *
     * @return void
     */
    private function _hostname()
    {
        if (PSI_USE_VHOST) {
            if (CommonFunctions::readenv('SERVER_NAME', $hnm)) $this->sys->setHostname($hnm);
        } else {
            if (CommonFunctions::executeProgram('uname', '-n', $result, PSI_DEBUG)) {
                $ip = gethostbyname($result);
                if ($ip != $result) {
                    $this->sys->setHostname(gethostbyaddr($ip));
                }
            }
        }
    }

    /**
     *  Physical memory information and Swap Space information
     *
     *  @return void
     */
    private function _memory()
    {
        if (CommonFunctions::executeProgram('pidin', 'info', $buf)
           && preg_match('/^.* FreeMem:(\S+)Mb\/(\S+)Mb/', $buf, $memm)) {
            $this->sys->setMemTotal(1024*1024*$memm[2]);
            $this->sys->setMemFree(1024*1024*$memm[1]);
            $this->sys->setMemUsed(1024*1024*($memm[2]-$memm[1]));
        }
    }

    /**
     * filesystem information
     *
     * @return void
     */
    private function _filesystems()
    {
        $arrResult = Parser::df("-P 2>/dev/null");
        foreach ($arrResult as $dev) {
            $this->sys->setDiskDevices($dev);
        }
    }

    /**
     * network information
     *
     * @return void
     */
    private function _network()
    {
        if (CommonFunctions::executeProgram('ifconfig', '', $bufr, PSI_DEBUG)) {
            $lines = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            $was = false;
            $dev = null;
            foreach ($lines as $line) {
                if (preg_match("/^([^\s:]+)/", $line, $ar_buf)) {
                    if ($was) {
                        $this->sys->setNetDevices($dev);
                    }
                    $dev = new NetDevice();
                    $dev->setName($ar_buf[1]);
                    $was = true;
                } else {
                    if ($was) {
                        if (defined('PSI_SHOW_NETWORK_INFOS') && (PSI_SHOW_NETWORK_INFOS)) {
                            if (preg_match('/^\s+address:\s*(\S+)/i', $line, $ar_buf2)) {
                                if (!defined('PSI_HIDE_NETWORK_MACADDR') || !PSI_HIDE_NETWORK_MACADDR) $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').preg_replace('/:/', '-', strtoupper($ar_buf2[1])));
                            } elseif (preg_match('/^\s+inet\s+(\S+)\s+netmask/i', $line, $ar_buf2))
                                $dev->setInfo(($dev->getInfo()?$dev->getInfo().';':'').$ar_buf2[1]);

                        }
                    }
                }
            }
            if ($was) {
                $this->sys->setNetDevices($dev);
            }
        }
    }

    /**
     * get the information
     *
     * @return void
     */
    public function build()
    {
        $this->error->addWarning("The QNX version of phpSysInfo is a work in progress, some things currently don't work");
        if (!$this->blockname || $this->blockname==='vitals') {
            $this->_distro();
            $this->_hostname();
            $this->_kernel();
            $this->_uptime();
            $this->_users();
        }
        if (!$this->blockname || $this->blockname==='hardware') {
            $this->_cpuinfo();
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
