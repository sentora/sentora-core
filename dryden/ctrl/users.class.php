<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_users {

    static function GetUserDetail($user="") {
        global $zdbh;

        $userdetail = new runtime_dataobject();

        if ($user == "") {
            $user = ctrl_auth::CurrentUserID();
        }
        $rows = $zdbh->prepare("SELECT * FROM x_accounts 
								LEFT JOIN x_profiles ON (x_accounts.ac_id_pk=x_profiles.ud_user_fk) 
								LEFT JOIN x_groups   ON (x_accounts.ac_group_fk=x_groups.ug_id_pk) 
								LEFT JOIN x_packages ON (x_accounts.ac_package_fk=x_packages.pk_id_pk) 
								LEFT JOIN x_quotas   ON (x_accounts.ac_package_fk=x_quotas.qt_package_fk) 
								WHERE x_accounts.ac_id_pk= " . $user . "");
        $rows->execute();
        $dbvals = $rows->fetch();
        $userdetail->addItemValue('username', $dbvals['ac_user_vc']);
        $userdetail->addItemValue('userid', $dbvals['ac_id_pk']);
        $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
        $userdetail->addItemValue('email', $dbvals['ac_email_vc']);
        $userdetail->addItemValue('resellerid', $dbvals['ac_reseller_fk']);
        $userdetail->addItemValue('packageid', $dbvals['ac_package_fk']);
        $userdetail->addItemValue('enabled', $dbvals['ac_enabled_in']);
        $userdetail->addItemValue('fullname', $dbvals['ud_fullname_vc']);
        $userdetail->addItemValue('packagename', $dbvals['pk_name_vc']);
        $userdetail->addItemValue('usergroup', $dbvals['ug_name_vc']);
        $userdetail->addItemValue('usergroupid', $dbvals['ac_group_fk']);
        $userdetail->addItemValue('address', $dbvals['ud_address_tx']);
        $userdetail->addItemValue('postcode', $dbvals['ud_postcode_vc']);
        $userdetail->addItemValue('phone', $dbvals['ud_phone_vc']);
        $userdetail->addItemValue('language', $dbvals['ud_language_vc']);
        $userdetail->addItemValue('diskquota', $dbvals['qt_diskspace_bi']);
        $userdetail->addItemValue('bandwidthquota', $dbvals['qt_bandwidth_bi']);
        $userdetail->addItemValue('domainquota', $dbvals['qt_domains_in']);
        $userdetail->addItemValue('subdomainquota', $dbvals['qt_subdomains_in']);
        $userdetail->addItemValue('parkeddomainquota', $dbvals['qt_parkeddomains_in']);
        $userdetail->addItemValue('ftpaccountsquota', $dbvals['qt_ftpaccounts_in']);
        $userdetail->addItemValue('mysqlquota', $dbvals['qt_mysql_in']);
        $userdetail->addItemValue('mailboxquota', $dbvals['qt_mailboxes_in']);
        $userdetail->addItemValue('forwardersquota', $dbvals['qt_fowarders_in']);
        $userdetail->addItemValue('distrobutionlistsquota', $dbvals['qt_distlists_in']);


        /*
          # Display the current logged in your details!
          $rows = $zdbh->prepare("SELECT * FROM x_accounts LEFT JOIN x_profiles ON (x_accounts.ac_id_pk=x_profiles.ud_user_fk) LEFT JOIN x_groups ON (x_profiles.ud_group_fk=x_groups.ug_id_pk) LEFT JOIN x_packages ON (x_profiles.ud_package_fk=x_packages.pk_id_pk) LEFT JOIN x_quotas ON (x_profiles.ud_package_fk=x_quotas.qt_package_fk) WHERE x_accounts.ac_id_pk = " . ctrl_auth::CurrentUserID() . "");
          $rows->execute();
          $dbvals = $rows->fetch();
          $userdetail->addItemValue('username', $dbvals['ac_user_vc']);
          $userdetail->addItemValue('userid', $dbvals['ac_id_pk']);
          $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
          $userdetail->addItemValue('email', $dbvals['ac_email_vc']);
          $userdetail->addItemValue('fullname', $dbvals['ud_fullname_vc']);
          $userdetail->addItemValue('packagename', $dbvals['pk_name_vc']);
          $userdetail->addItemValue('usergroup', $dbvals['ug_name_vc']);
          $userdetail->addItemValue('usergroupid', $dbvals['ac_group_fk']);
          $userdetail->addItemValue('address', $dbvals['ud_address_tx']);
          $userdetail->addItemValue('postcode', $dbvals['ud_postcode_vc']);
          $userdetail->addItemValue('phone', $dbvals['ud_phone_vc']);
          $userdetail->addItemValue('language', $dbvals['ud_language_vc']);
          $userdetail->addItemValue('diskquota', $dbvals['qt_diskspace_bi']);
          $userdetail->addItemValue('bandwidthquota', $dbvals['qt_bandwidth_bi']);
          $userdetail->addItemValue('domainquota', $dbvals['qt_domains_in']);
          $userdetail->addItemValue('subdomainquota', $dbvals['qt_subdomains_in']);
          $userdetail->addItemValue('parkeddomainquota', $dbvals['qt_parkeddomains_in']);
          $userdetail->addItemValue('ftpaccountsquota', $dbvals['qt_ftpaccounts_in']);
          $userdetail->addItemValue('mysqlquota', $dbvals['qt_mysql_in']);
          $userdetail->addItemValue('mailboxquota', $dbvals['qt_mailboxes_in']);
          $userdetail->addItemValue('forwardersquota', $dbvals['qt_fowarders_in']);
          $userdetail->addItemValue('distrobutionlistsquota', $dbvals['qt_distlists_in']);
          } else {
          $rows = $zdbh->query("SELECT * FROM x_accounts JOIN x_profiles ON x_accounts.ac_id_pk=x_profiles.ud_user_fk WHERE x_accounts.ac_id_pk = " . $user . "");
          $rows->execute();
          $dbvals = $rows->fetch();
          $userdetail->addItemValue('username', $dbvals['ac_user_vc']);
          $userdetail->addItemValue('userid', $dbvals['ac_id_pk']);
          $userdetail->addItemValue('password', $dbvals['ac_pass_vc']);
          $userdetail->addItemValue('email', $dbvals['ac_email_vc']);
          $userdetail->addItemValue('fullname', $dbvals['ud_fullname_vc']);
          $userdetail->addItemValue('packagename', $dbvals['pk_name_vc']);
          $userdetail->addItemValue('usergroup', $dbvals['ug_name_vc']);
          $userdetail->addItemValue('usergroupid', $dbvals['ac_group_fk']);
          $userdetail->addItemValue('address', $dbvals['ud_address_tx']);
          $userdetail->addItemValue('postcode', $dbvals['ud_postcode_vc']);
          $userdetail->addItemValue('phone', $dbvals['ud_phone_vc']);
          $userdetail->addItemValue('language', $dbvals['ud_language_vc']);

          }
         */
        return $userdetail->getDataObject();
    }

    #Get all active domains on user account

    static function GetUserDomains($userid, $type="1") {
        global $zdbh;
        $domains = 0;
        $sql = "SELECT COUNT(*) FROM x_vhosts WHERE vh_acc_fk=" . $userid . " AND vh_deleted_ts IS NULL AND vh_type_in=" . $type . "";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $domains = count($numrows->fetchColumn());
                return $domains;
            }
        }
        return $domains;
    }

    static function CheckUserEnabled($userid) {
        global $zdbh;
        $row = $zdbh->prepare("SELECT ac_enabled_in FROM x_accounts WHERE ac_id_pk=" . $userid . " AND ac_deleted_ts IS NULL");
        $row->execute();
		if ($row['ac_enabled_in'] <> 0){
			return true;
        }
        return false;
    }

}

?>
