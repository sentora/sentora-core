<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 *
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@bobbyallen.me
 * @copyright (c) 2008-2014 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class module_controller extends ctrl_module
{

    static $error;
    static $alreadyexists;
    static $badname;
    static $blank;
    static $ok;
    static $edit;
    static $samepackage;

    /**
     * The 'worker' methods.
     */
    static function ListPackages($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_packages WHERE pk_reseller_fk=:uid AND pk_deleted_ts IS NULL";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            while ($rowpackages = $sql->fetch()) {
                //$numrows = $zdbh->query("SELECT COUNT(*) FROM x_accounts WHERE ac_package_fk=" . $rowpackages['pk_id_pk'] . " AND ac_deleted_ts IS NULL")->fetchColumn();
                $numrows = $zdbh->prepare("SELECT COUNT(*) FROM x_accounts WHERE ac_package_fk=:pk_id_pk AND ac_deleted_ts IS NULL");
                $numrows->bindParam(':pk_id_pk', $rowpackages['pk_id_pk']);
                $numrows->execute();
                $Column = $numrows->fetchColumn();
                array_push($res, array('packageid' => $rowpackages['pk_id_pk'],
                    'created' => date(ctrl_options::GetSystemOption('sentora_df'), $rowpackages['pk_created_ts']),
                    'clients' => $Column[0],
                    'packagename' => ui_language::translate($rowpackages['pk_name_vc'])));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentPackage($id)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_packages
				LEFT JOIN x_quotas  ON (x_packages.pk_id_pk=x_quotas.qt_package_fk)
				WHERE pk_id_pk=:id AND pk_deleted_ts IS NULL";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':id', $id);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':id', $id);
            $res = array();
            $sql->execute();
            while ($rowpackages = $sql->fetch()) {
                $PHPChecked = "";
                if ($rowpackages['pk_enablephp_in'] <> 0) {
                    $PHPChecked = "CHECKED";
                }
                array_push($res, array('packageid' => $rowpackages['pk_id_pk'],
                    'enablePHP' => $rowpackages['pk_enablephp_in'],
                    'PHPChecked' => $PHPChecked,
                    'domains' => $rowpackages['qt_domains_in'],
                    'subdomains' => $rowpackages['qt_subdomains_in'],
                    'parkeddomains' => $rowpackages['qt_parkeddomains_in'],
                    'fowarders' => $rowpackages['qt_fowarders_in'],
                    'distlists' => $rowpackages['qt_distlists_in'],
                    'ftpaccounts' => $rowpackages['qt_ftpaccounts_in'],
                    'mysql' => $rowpackages['qt_mysql_in'],
                    'diskquota' => ($rowpackages['qt_diskspace_bi'] / 1024000),
                    'bandquota' => ($rowpackages['qt_bandwidth_bi'] / 1024000),
                    'mailboxes' => $rowpackages['qt_mailboxes_in'],
                    'packagename' => stripslashes($rowpackages['pk_name_vc'])));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteDeletePackage($pk_id_pk, $mpk_id_pk)
    {
        global $zdbh;

        $sql = $zdbh->prepare("SELECT COUNT(*) FROM x_accounts WHERE ac_package_fk=:packageid AND ac_deleted_ts IS NULL");
        $sql->bindParam(':packageid', $pk_id_pk);
        $sql->execute();
        $numrows = $sql->fetchAll();
        if ($numrows[0] <> 0) {
            if ($pk_id_pk == $mpk_id_pk) {
                self::$samepackage = true;
                return false;
            }
        }
        runtime_hook::Execute('OnBeforeDeletePackage');
        $sql = $zdbh->prepare("
            UPDATE x_accounts
            SET ac_package_fk = :mpk_id_pk
            WHERE ac_package_fk =:pk_id_pk");
        $sql->bindParam(':mpk_id_pk', $mpk_id_pk);
        $sql->bindParam(':pk_id_pk', $pk_id_pk);
        $sql->execute();
        $sql = $zdbh->prepare("
            UPDATE x_profiles
            SET ud_package_fk = :mpk_id_pk
            WHERE ud_package_fk = :pk_id_pk");
        $sql->bindParam(':mpk_id_pk', $mpk_id_pk);
        $sql->bindParam(':pk_id_pk', $pk_id_pk);
        $sql->execute();
        $sql = $zdbh->prepare("
			UPDATE x_packages
			SET pk_deleted_ts = :time
			WHERE pk_id_pk = :pk_id_pk");
        $time = time();
        $sql->bindParam(':time', $time);
        $sql->bindParam(':pk_id_pk', $pk_id_pk);
        $sql->execute();
        runtime_hook::Execute('OnAfterDeletePackage');
        self::$ok = true;
        return true;
    }

    static function ExecuteCreatePackage($uid, $packagename, $EnablePHP, $Domains, $SubDomains, $ParkedDomains, $Mailboxes, $Fowarders, $DistLists, $FTPAccounts, $MySQL, $DiskQuota, $BandQuota)
    {
        global $zdbh;
        if (fs_director::CheckForEmptyValue(self::CheckNumeric($EnablePHP, $Domains, $SubDomains, $ParkedDomains, $Mailboxes, $Fowarders, $DistLists, $FTPAccounts, $MySQL, $DiskQuota, $BandQuota))) {
            return false;
        }
        $packagename = str_replace(' ', '', $packagename);
        // Check for errors before we continue...
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($packagename, $uid))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreatePackage');
        # If the user submitted a 'new' request then we will simply add the package to the database...
        $sql = $zdbh->prepare("INSERT INTO x_packages (pk_reseller_fk,
										pk_name_vc,
										pk_enablephp_in,
										pk_created_ts) VALUES (
										:uid,
										:packagename,
										:php,
										:time);");
        $php = fs_director::GetCheckboxValue($EnablePHP);
        $sql->bindParam(':php', $php);
        $sql->bindParam(':uid', $uid);
        $time = time();
        $sql->bindParam(':time', $time);
        $pack = addslashes($packagename);
        $sql->bindParam(':packagename', $pack);
        $sql->execute();
        # Now lets pull back the package ID so we can use it in the other tables we are about to manipulate.
        //$package = $zdbh->query("SELECT * FROM x_packages WHERE pk_reseller_fk=" . $uid . " AND pk_name_vc='" . $packagename . "' AND pk_deleted_ts IS NULL")->Fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_packages WHERE pk_reseller_fk=:uid AND pk_name_vc=:packagename AND pk_deleted_ts IS NULL");
        $numrows->bindParam(':uid', $uid);
        $numrows->bindParam(':packagename', $packagename);
        $numrows->execute();
        $package = $numrows->fetch();


        $sql = $zdbh->prepare("INSERT INTO x_quotas (qt_package_fk,
										qt_domains_in,
										qt_subdomains_in,
										qt_parkeddomains_in,
										qt_mailboxes_in,
										qt_fowarders_in,
										qt_distlists_in,
										qt_ftpaccounts_in,
										qt_mysql_in,
										qt_diskspace_bi,
										qt_bandwidth_bi) VALUES (
										:pk_id_pk,
										:Domains,
										:SubDomains,
										:ParkedDomains,
										:Mailboxes,
										:Fowarders,
										:DistLists,
										:FTPAccounts,
										:MySQL,
										:DiskQuotaFinal,
										:BandQuotaFinal)");
        $DiskQuotaFinal = $DiskQuota * 1024000;
        $BandQuotaFinal = $BandQuota * 1024000;
        $sql->bindParam(':DiskQuotaFinal', $DiskQuotaFinal);
        $sql->bindParam(':BandQuotaFinal', $BandQuotaFinal);
        $sql->bindParam(':MySQL', $MySQL);
        $sql->bindParam(':DistLists', $DistLists);
        $sql->bindParam(':Fowarders', $Fowarders);
        $sql->bindParam(':Mailboxes', $Mailboxes);
        $sql->bindParam(':SubDomains', $SubDomains);
        $sql->bindParam(':FTPAccounts', $FTPAccounts);
        $sql->bindParam(':ParkedDomains', $ParkedDomains);
        $sql->bindParam(':Domains', $Domains);
        $sql->bindParam(':pk_id_pk', $package['pk_id_pk']);
        $sql->execute();
        runtime_hook::Execute('OnAfterCreatePackage');
        self::$ok = true;
        return true;
    }

    static function ExecuteUpdatePackage($uid, $pid, $packagename, $EnablePHP, $Domains, $SubDomains, $ParkedDomains, $Mailboxes, $Fowarders, $DistLists, $FTPAccounts, $MySQL, $DiskQuota, $BandQuota)
    {
        global $zdbh;
        if (fs_director::CheckForEmptyValue(self::CheckNumeric($EnablePHP, $Domains, $SubDomains, $ParkedDomains, $Mailboxes, $Fowarders, $DistLists, $FTPAccounts, $MySQL, $DiskQuota, $BandQuota))) {
            return false;
        }
        $packagename = str_replace(' ', '', $packagename);
        // Check for errors before we continue...
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($packagename, $uid, $pid))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeUpdatePackage');
        $sql = $zdbh->prepare("UPDATE x_packages SET pk_name_vc=:packagename,
								pk_enablephp_in = :php
								WHERE pk_id_pk  = :pid");

        $php = fs_director::GetCheckboxValue($EnablePHP);
        $sql->bindParam(':php', $php);
        $sql->bindParam(':pid', $pid);
        $sql->bindParam(':packagename', $packagename);
        $sql->execute();
        $sql = $zdbh->prepare("UPDATE x_quotas SET qt_domains_in = :Domains,
								qt_parkeddomains_in = :ParkedDomains,
								qt_ftpaccounts_in   = :FTPAccounts,
								qt_subdomains_in    = :SubDomains,
								qt_mailboxes_in     = :Mailboxes,
								qt_fowarders_in     = :Fowarders,
								qt_distlists_in     = :DistLists,
								qt_diskspace_bi     = :DiskQuotaFinal,
								qt_bandwidth_bi     = :BandQuotaFinal,
								qt_mysql_in         = :MySQL
                                                                WHERE qt_package_fk = :pid");
        $DiskQuotaFinal = $DiskQuota * 1024000;
        $BandQuotaFinal = $BandQuota * 1024000;
        $sql->bindParam(':DiskQuotaFinal', $DiskQuotaFinal);
        $sql->bindParam(':BandQuotaFinal', $BandQuotaFinal);
        $sql->bindParam(':MySQL', $MySQL);
        $sql->bindParam(':DistLists', $DistLists);
        $sql->bindParam(':Fowarders', $Fowarders);
        $sql->bindParam(':Mailboxes', $Mailboxes);
        $sql->bindParam(':SubDomains', $SubDomains);
        $sql->bindParam(':FTPAccounts', $FTPAccounts);
        $sql->bindParam(':ParkedDomains', $ParkedDomains);
        $sql->bindParam(':Domains', $Domains);
        $sql->bindParam(':pid', $pid);
        $sql->execute();
        runtime_hook::Execute('OnAfterUpdatePackage');
        self::$ok = true;
        return true;
    }

    static function CheckCreateForErrors($packagename, $uid, $pid = 0)
    {
        global $zdbh;
        $packagename = str_replace(' ', '', $packagename);
        # Check to make sure the packagename is not blank or exists for reseller before we go any further...
        if (!fs_director::CheckForEmptyValue($packagename)) {
            $sql = "SELECT COUNT(*) FROM x_packages WHERE UPPER(pk_name_vc)=:packageNameSlashes AND pk_reseller_fk=:uid AND pk_id_pk !=:pid AND pk_deleted_ts IS NULL";
            $packageNameSlashes = addslashes(strtoupper($packagename));

            $numrows = $zdbh->prepare($sql);
            $numrows->bindParam(':packageNameSlashes', $packageNameSlashes);
            $numrows->bindParam(':uid', $uid);
            $numrows->bindParam(':pid', $pid);

            if ($numrows->execute()) {
                if ($numrows->fetchColumn() <> 0) {
                    self::$alreadyexists = true;
                    return false;
                }
            }
        } else {
            self::$blank = true;
            return false;
        }
        // Check packagename format.
        if (!self::IsValidPackageName($packagename)) {
            self::$badname = true;
            return false;
        }
        return true;
    }

    static function IsValidPackageName($packagename)
    {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $packagename) || preg_match('/-$/', $packagename)) {
            return false;
        }
        return true;
    }

    static function CheckNumeric($EnablePHP, $Domains, $SubDomains, $ParkedDomains, $Mailboxes, $Fowarders, $DistLists, $FTPAccounts, $MySQL, $DiskQuota, $BandQuota)
    {
        if (!is_numeric($EnablePHP) ||
                !is_numeric($Domains) ||
                !is_numeric($SubDomains) ||
                !is_numeric($ParkedDomains) ||
                !is_numeric($Mailboxes) ||
                !is_numeric($Fowarders) ||
                !is_numeric($DistLists) ||
                !is_numeric($FTPAccounts) ||
                !is_numeric($MySQL) ||
                !is_numeric($DiskQuota) ||
                !is_numeric($BandQuota)) {
            self::$error = true;
            return false;
        } else {
            return true;
        }
    }

    static function AddDefaultPackageTime($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_packages WHERE pk_reseller_fk=:uid AND pk_deleted_ts IS NULL";
        //$numrows = $zdbh->query($sql);
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $sql->execute();
            while ($rowpackages = $sql->fetch()) {
                if ($rowpackages['pk_created_ts'] == "") {
                    $add = $zdbh->prepare("UPDATE x_packages SET pk_created_ts=:time
									WHERE pk_id_pk  =:pk_id_pk");
                    $time = time();
                    $add->bindParam(':time', $time);
                    $add->bindParam(':pk_id_pk', $rowpackages['pk_id_pk']);
                    $add->execute();
                }
            }
        }
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doCreatePackage()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inEnablePHP'])) {
            $EnablePHP = fs_director::GetCheckboxValue($formvars['inEnablePHP']);
        } else {
            $EnablePHP = 0;
        }
        if (self::ExecuteCreatePackage($currentuser['userid'], $formvars['inPackageName'], $EnablePHP, $formvars['inNoDomains'], $formvars['inNoSubDomains'], $formvars['inNoParkedDomains'], $formvars['inNoMailboxes'], $formvars['inNoFowarders'], $formvars['inNoDistLists'], $formvars['inNoFTPAccounts'], $formvars['inNoMySQL'], $formvars['inDiskQuota'], $formvars['inBandQuota']))
            return true;
        return false;
    }

    static function doUpdatePackage()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inEnablePHP'])) {
            $EnablePHP = fs_director::GetCheckboxValue($formvars['inEnablePHP']);
        } else {
            $EnablePHP = 0;
        }
        if (self::ExecuteUpdatePackage($currentuser['userid'], $formvars['inPackageID'], $formvars['inPackageName'], $EnablePHP, $formvars['inNoDomains'], $formvars['inNoSubDomains'], $formvars['inNoParkedDomains'], $formvars['inNoMailboxes'], $formvars['inNoFowarders'], $formvars['inNoDistLists'], $formvars['inNoFTPAccounts'], $formvars['inNoMySQL'], $formvars['inDiskQuota'], $formvars['inBandQuota']))
            return true;
        return false;
    }

    static function doEditPackage()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListPackages($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['packageid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['packageid'] . "");
                exit;
            }
            if (isset($formvars['inEdit_' . $row['packageid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $row['packageid'] . "");
                exit;
            }
        }
        return;
    }

    static function doDeletePackage()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeletePackage($formvars['inPackageID'], $formvars['inMovePackage']))
            return true;
        return false;
    }

    static function getPackageList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $packages = self::ListPackages($currentuser['userid']);
        if ($packages)
            return $packages;
        return false;
    }

    static function getPackageListDropdown()
    {
        $currentuser = ctrl_users::GetUserDetail();
        $packages = self::ListPackages($currentuser['userid']);
        $available = array();
        foreach ($packages as $package) {
            if ($package['packageid'] != $_GET['other']) $available[] = $package;
        }
        if (count($available) > 0)
            return $available;
        return false;
    }

    static function getisCreatePackage()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getisDeletePackage()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisEditPackage()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit")) {
            return true;
        } else {
            return false;
        }
    }

    static function getEditCurrentPackageName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['packagename'];
        } else {
            return "";
        }
    }

    static function getEditCurrentPackageID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['packageid'];
        } else {
            return "";
        }
    }

    static function getEditCurrentDomains()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['domains'];
        } else {
            return "";
        }
    }

    static function getEditCurrentSubDomains()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['subdomains'];
        } else {
            return "";
        }
    }

    static function getEditCurrentParkedDomains()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['parkeddomains'];
        } else {
            return "";
        }
    }

    static function getEditCurrentMailboxes()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mailboxes'];
        } else {
            return "";
        }
    }

    static function getEditCurrentForwarders()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['fowarders'];
        } else {
            return "";
        }
    }

    static function getEditCurrentDistLists()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['distlists'];
        } else {
            return "";
        }
    }

    static function getEditCurrentFTP()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['ftpaccounts'];
        } else {
            return "";
        }
    }

    static function getEditCurrentMySQL()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mysql'];
        } else {
            return "";
        }
    }

    static function getEditCurrentDisk()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['diskquota'];
        } else {
            return "";
        }
    }

    static function getEditCurrentBandWidth()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['bandquota'];
        } else {
            return "";
        }
    }

    static function getAddDefaultPackageTime()
    {
        $currentuser = ctrl_users::GetUserDetail();
        self::AddDefaultPackageTime($currentuser['userid']);
    }

    static function getPHPChecked()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentPackage($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['PHPChecked'];
        } else {
            return "";
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You need to specify a package name to create your package."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badname)) {
            return ui_sysmessage::shout(ui_language::translate("Your package name is not valid. Please enter a valid package name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A package with that name already appears to exsist."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$error)) {
            return ui_sysmessage::shout(ui_language::translate("There was an error updating your packages"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$samepackage)) {
            return ui_sysmessage::shout(ui_language::translate("You cant move clients to the same package you are deleting!"), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your packages have been saved successfully!"), "zannounceok");
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
