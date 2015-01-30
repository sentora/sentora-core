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

    static $alreadyexists;
    static $blank;
    static $badname;
    static $ok;

    /**
     * The 'worker' methods.
     */
    static function ListDatabases($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_databases WHERE my_acc_fk=:uid AND my_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':uid', $uid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':uid', $uid);
            $res = array();
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                $numrowdb = $zdbh->query("SELECT COUNT(*) FROM x_mysql_dbmap WHERE mm_acc_fk=" . $rowmysql['my_acc_fk'] . " AND mm_database_fk=" . $rowmysql['my_id_pk'] . "")->fetch();
                $res[] = array('mysqlid' => $rowmysql['my_id_pk'],
                    'totaldb' => $numrowdb[0],
                    'mysqlname' => $rowmysql['my_name_vc'],
                    'mysqlsize' => $rowmysql['my_usedspace_bi'],
                    'mysqlfriendlysize' => fs_director::ShowHumanFileSize($rowmysql['my_usedspace_bi']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentDatabases($mysqlid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_databases WHERE my_id_pk=:mysqlid AND my_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':mysqlid', $mysqlid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':mysqlid', $mysqlid);
            $res = array();
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                $res[] = array('mysqlid' => $rowmysql['my_id_pk'],
                    'mysqlname' => $rowmysql['my_name_vc'],
                    'mysqlsize' => $rowmysql['my_usedspace_bi'],
                    'mysqlfriendlysize' => fs_director::ShowHumanFileSize($rowmysql['my_usedspace_bi']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteCreateDatabase($uid, $databasename)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        $databasename = strtolower(str_replace(' ', '', $databasename));
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($currentuser['username'], $databasename))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreateDatabase');
        try {
            $db = $zdbh->mysqlRealEscapeString($currentuser['username'] . "_" . $databasename);
            $sql = $zdbh->prepare("CREATE DATABASE `$db` DEFAULT CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';");
            $sql->execute();
            $sql = $zdbh->prepare("FLUSH PRIVILEGES");
            $sql->execute();
            $sql = $zdbh->prepare("INSERT INTO x_mysql_databases (
									my_acc_fk,
									my_name_vc,
									my_created_ts) VALUES (
									:userid,
									:name,
									:time)");
            $time = time();
            $name = $currentuser['username'] . "_" . $databasename;

            $sql->bindParam(':userid', $currentuser['userid']);
            $sql->bindParam(':time', $time);
            $sql->bindParam(':name', $name);
            $sql->execute();
        } catch (PDOException $e) {
            return false;
        }
        runtime_hook::Execute('OnAfterCreateDatabase');
        self::$ok = true;
        return true;
    }

    static function CheckCreateForErrors($username, $databasename)
    {
        global $zdbh;
        # Check to make sure the database name is not blank before we go any further...
        if ($databasename == '') {
            self::$blank = true;
            return false;
        }
        // Check for invalid username
        if (!self::IsValidUserName($databasename)) {
            self::$badname = true;
            return false;
        }
        # Check to make sure the database is not a duplicate...
        $sql = "SELECT COUNT(*) FROM x_mysql_databases WHERE my_name_vc=:dbName AND my_deleted_ts IS NULL";
        $dbName = $username . "_" . $databasename;
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':dbName', $dbName);

        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                self::$alreadyexists = true;
                return false;
            }
        }

        return true;
    }

    static function ExecuteDeleteDatabase($my_id_pk)
    {
        global $zdbh;
        runtime_hook::Execute('OnBeforeDeleteDatabase');
        $numrows = $zdbh->prepare("SELECT my_name_vc FROM x_mysql_databases WHERE my_id_pk=:my_id_pk");
        $numrows->bindParam(':my_id_pk', $my_id_pk);
        $numrows->execute();
        $rowmysql = $numrows->fetch();
        try {
            $my_name_vc = $zdbh->mysqlRealEscapeString($rowmysql['my_name_vc']);
            $sql = $zdbh->prepare("DROP DATABASE IF EXISTS `$my_name_vc`;");
            //$sql->bindParam(':my_name_vc', $rowmysql['my_name_vc'], PDO::PARAM_STR);
            $sql->execute();

            $sql = $zdbh->prepare("FLUSH PRIVILEGES");
            $sql->execute();

            $sql = $zdbh->prepare("UPDATE x_mysql_databases SET my_deleted_ts = :time WHERE my_id_pk = :my_id_pk");
            $sql->bindParam(':time', time());
            $sql->bindParam(':my_id_pk', $my_id_pk);
            $sql->execute();

            $sql = $zdbh->prepare("DELETE FROM x_mysql_dbmap WHERE mm_database_fk=:my_id_pk");
            $sql->bindParam(':my_id_pk', $my_id_pk);
            $sql->execute();
        } catch (PDOException $e) {
            return false;
        }
        runtime_hook::Execute('OnAfterDeleteDatabase');
        self::$ok = true;
        return true;
    }

    static function IsValidUserName($username)
    {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $username) || preg_match('/-$/', $username)) {
            return false;
        }
        return true;
    }

    /**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */
    static function doCreateDatabase()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteCreateDatabase($currentuser['userid'], $formvars['inDatabase']);
    }

    static function doDeleteDatabase()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListDatabases($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['mysqlid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['mysqlid'] . "");
                exit;
            }
        }
        return true;
    }

    static function doConfirmDeleteDatabase()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        return self::ExecuteDeleteDatabase($formvars['inDelete']);
    }

    static function getDatabaseList()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDatabases($currentuser['userid']);
    }

    static function getisDeleteDatabase()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return (isset($urlvars['show'])) && ($urlvars['show'] == "Delete");
    }

    static function getisCreateDatabase()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        return !isset($urlvars['show']);
    }

    static function getCurrentUserName()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser['username'];
    }

    static function getEditCurrentDatabaseName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDatabases($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mysqlname'];
        } else {
            return '';
        }
    }

    static function getEditCurrentDatabaseID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentDatabases($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['mysqlid'];
        } else {
            return '';
        }
    }

    static function getQuotaLimit()
    {
        $currentuser = ctrl_users::GetUserDetail();
        return ($currentuser['mysqlquota'] < 0 ) or //-1 = unlimited
                ($currentuser['mysqlquota'] > ctrl_users::GetQuotaUsages('mysql', $currentuser['userid']));
    }

    static function getMysqlUsagepChart()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $maximum = $currentuser['mysqlquota'];
        if ($maximum < 0) { //-1 = unlimited
            return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
        } else {
            $used = ctrl_users::GetQuotaUsages('mysql', $currentuser['userid']);
            $free = max($maximum - $used, 0);
            return '<img src="etc/lib/pChart2/sentora/z3DPie.php?score=' . $free . '::' . $used
                    . '&labels=Free: ' . $free . '::Used: ' . $used
                    . '&legendfont=verdana&legendfontsize=8&imagesize=240::190&chartsize=120::90&radius=100&legendsize=150::160"'
                    . ' alt="' . ui_language::translate('Pie chart') . '"/>';
        }
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You need to specify a database name to create your database."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badname)) {
            return ui_sysmessage::shout(ui_language::translate("Your MySQL database name is not valid. Please enter a valid MySQL database name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A database with that name already appears to exsist."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your databases have been saved successfully!"), "zannounceok");
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
