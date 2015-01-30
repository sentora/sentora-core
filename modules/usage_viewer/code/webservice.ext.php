<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws {

    /**
     * Get usage stats for the entire server.
     * @global type $zdbh
     * @return type 
     */
    function GetServerUsageStats() {
        global $zdbh;
        $response_xml = "\n";

        // Total Sentora user accounts
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_accounts")->Fetch();
        $total_accounts = $sql['total'];

        // Total Active Sentora user accounts
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_accounts WHERE ac_enabled_in = 1")->Fetch();
        $total_activeaccounts = $sql['total'];

        // Total Disabled Sentora user accounts
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_accounts WHERE ac_enabled_in = 0")->Fetch();
        $total_disabledaccounts = $sql['total'];

        // Total Disk space in use.
        $total_disk = "TODO";

        // Total Bandwidth used this month
        $total_band = "TODO";

        // Total CRON Jobs
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_cronjobs WHERE ct_deleted_ts IS NULL")->Fetch();
        $total_crons = $sql['total'];

        // Total FTP accounts
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_ftpaccounts WHERE ft_deleted_ts IS NULL")->Fetch();
        $total_ftpaccounts = $sql['total'];

        // Total FTP accounts
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_mysql_databases WHERE my_deleted_ts IS NULL")->Fetch();
        $total_mysql = $sql['total'];

        // Total hosting packages
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_packages WHERE pk_deleted_ts IS NULL")->Fetch();
        $total_packages = $sql['total'];

        // Total VHOSTS
        $sql = $zdbh->query("SELECT COUNT(*) AS total FROM x_vhosts WHERE vh_deleted_ts IS NULL")->Fetch();
        $total_vhosts = $sql['total'];


        $response_xml = ws_xmws::NewXMLContentSection('stats', array(
                    'sentorausers' => $total_accounts,
                    'activesentorausers' => $total_activeaccounts,
                    'disabledsentorausers' => $total_disabledaccounts,
                    'diskspaceused' => $total_disk,
                    'bandwidthused' => $total_band,
                    'cronjobs' => $total_crons,
                    'ftpaccounts' => $total_ftpaccounts,
                    'mysqldatabases' => $total_mysql,
                    'hostingpackages' => $total_packages,
                    'vhosts' => $total_vhosts,
                ));

        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);

        return $dataobject->getDataObject();
    }

}

?>
