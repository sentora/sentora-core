<?php

/**
 * General user infoamtion class.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_users {

    /**
     * Returns an array of infomation for the account details, package, groups and quota limits for a given UID.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $uid The ZPanel user account ID.
     * @return array
     */
    static function GetUserDetail($uid = "") {
        global $zdbh;
        $userdetail = new runtime_dataobject();
        if ($uid == "") {
            $uid = ctrl_auth::CurrentUserID();
        }
        $rows = $zdbh->prepare("
            SELECT * FROM x_accounts 
            LEFT JOIN x_profiles ON (x_accounts.ac_id_pk=x_profiles.ud_user_fk) 
            LEFT JOIN x_groups   ON (x_accounts.ac_group_fk=x_groups.ug_id_pk) 
            LEFT JOIN x_packages ON (x_accounts.ac_package_fk=x_packages.pk_id_pk) 
            LEFT JOIN x_quotas   ON (x_accounts.ac_package_fk=x_quotas.qt_package_fk) 
            WHERE x_accounts.ac_id_pk= :uid
          ");
		$rows->bindParam(':uid', $uid);
        $rows->execute();
        $dbvals = $rows->fetch();
        $userdetail->addItemValue('username', htmlspecialchars(strip_tags($dbvals['ac_user_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('userid', htmlspecialchars(strip_tags($dbvals['ac_id_pk']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
        $userdetail->addItemValue('email', htmlspecialchars(strip_tags($dbvals['ac_email_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('resellerid', htmlspecialchars(strip_tags($dbvals['ac_reseller_fk']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('packageid', htmlspecialchars(strip_tags($dbvals['ac_package_fk']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('enabled', htmlspecialchars(strip_tags($dbvals['ac_enabled_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('usertheme', htmlspecialchars(strip_tags($dbvals['ac_usertheme_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('usercss', htmlspecialchars(strip_tags($dbvals['ac_usercss_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('lastlogon', htmlspecialchars(strip_tags($dbvals['ac_lastlogon_ts']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('fullname', htmlspecialchars(strip_tags($dbvals['ud_fullname_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('packagename', htmlspecialchars(strip_tags($dbvals['pk_name_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('usergroup', htmlspecialchars(strip_tags($dbvals['ug_name_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('usergroupid', htmlspecialchars(strip_tags($dbvals['ac_group_fk']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('address', htmlspecialchars(strip_tags($dbvals['ud_address_tx']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('postcode', htmlspecialchars(strip_tags($dbvals['ud_postcode_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('phone', htmlspecialchars(strip_tags($dbvals['ud_phone_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('language', htmlspecialchars(strip_tags($dbvals['ud_language_vc']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('diskquota', htmlspecialchars(strip_tags($dbvals['qt_diskspace_bi']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('bandwidthquota', htmlspecialchars(strip_tags($dbvals['qt_bandwidth_bi']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('domainquota', htmlspecialchars(strip_tags($dbvals['qt_domains_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('subdomainquota', htmlspecialchars(strip_tags($dbvals['qt_subdomains_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('parkeddomainquota', htmlspecialchars(strip_tags($dbvals['qt_parkeddomains_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('ftpaccountsquota', htmlspecialchars(strip_tags($dbvals['qt_ftpaccounts_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('mysqlquota', htmlspecialchars(strip_tags($dbvals['qt_mysql_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('mailboxquota', htmlspecialchars(strip_tags($dbvals['qt_mailboxes_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('forwardersquota', htmlspecialchars(strip_tags($dbvals['qt_fowarders_in']), ENT_QUOTES, 'UTF-8'));
        $userdetail->addItemValue('distrobutionlistsquota', htmlspecialchars(strip_tags($dbvals['qt_distlists_in']), ENT_QUOTES, 'UTF-8'));
        return $userdetail->getDataObject();
    }

    /**
     * Returns the current usage of a particular resource.
     * @author Bobby Allen (ballen@zpanelcp.com) 
     * @param string $resource What time of quota should we be checking? (domains, subdomains, parkeddomains, mailboxes, distlists etc.)
     * @param int $acc_key The user ID of which to check the quota status for.
     * @return array Database table array of the quota infomation. 
     */
    static function GetQuotaUsages($resource, $acc_key = 0) {
        global $zdbh;
        if ($resource == 'domains') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk= :acc_key AND vh_type_in=1 AND vh_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'subdomains') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk= :acc_key AND vh_type_in=2 AND vh_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'parkeddomains') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk= :acc_key AND vh_type_in=3 AND vh_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'mailboxes') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_mailboxes WHERE mb_acc_fk= :acc_key AND mb_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'forwarders') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_forwarders WHERE fw_acc_fk= :acc_key AND fw_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'distlists') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_distlists WHERE dl_acc_fk= :acc_key AND dl_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'ftpaccounts') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_ftpaccounts WHERE ft_acc_fk= :acc_key AND ft_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'mysql') {
            $sql = $zdbh->prepare("SELECT COUNT(*) AS amount FROM x_mysql_databases WHERE my_acc_fk= :acc_key AND my_deleted_ts IS NULL");
            $sql->bindParam(':acc_key', $acc_key);
			$sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['amount'];
        }
        if ($resource == 'diskspace') {
            $sql = $zdbh->prepare("SELECT bd_diskamount_bi FROM x_bandwidth WHERE bd_acc_fk= :acc_key AND bd_month_in=" . date("Ym", time()) . "");
            $sql->bindParam(':acc_key', $acc_key);
		    $sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['bd_diskamount_bi'];
        }
        if ($resource == 'bandwidth') {
            $sql = $zdbh->prepare("SELECT bd_transamount_bi FROM x_bandwidth WHERE bd_acc_fk= :acc_key AND bd_month_in=" . date("Ym", time()) . "");
            $sql->bindParam(':acc_key', $acc_key);
		    $sql->execute();
            $retval = $sql->fetch();
            $retval = $retval['bd_transamount_bi'];
        }
        return $retval;
    }

    /**
     * @todo Does this still need to be here as this is now managed under a module and not seen as 'core' but template still relies on this at present! - Bobby Allen
     */
    static function GetUserDomains($userid, $type = "1") {
        global $zdbh;
        $domains = 0;
        $numrows = $zdbh->prepare("SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk= :userid AND vh_deleted_ts IS NULL AND vh_type_in= :type");
		$numrows->bindParam(':userid', $userid);
		$numrows->bindParam(':type', $type);
		$status = $sql->execute();
		if ($status) {
            if ($numrows->fetchColumn() <> 0) {
                $domains = count($numrows->fetchColumn());
                return $domains;
            }
        }
        return $domains;
    }

    /**
     * Checks that the specified user is active and therefore allowed to login to the panel.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $uid The ZPanel user account ID.
     * @return type 
     */
    static function CheckUserEnabled($uid) {
        global $zdbh;
        $domains = 0;
        $sql = $zdbh->prepare("SELECT COUNT(*) FROM x_accounts WHERE ac_id_pk= :uid AND ac_enabled_in=1 AND ac_deleted_ts IS NULL");
		$sql->bindParam(':uid', $uid);
		$status = $sql->execute();
		
		if ($status) {
            if ($sql->fetchColumn() <> 0) {
                return true;
            }
        }
        return false;
    }

}

?>
