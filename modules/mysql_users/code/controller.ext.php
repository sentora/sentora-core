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
    static $dbalreadyadded;
    static $blank;
    static $badname;
    static $badpass;
    static $rootabuse;
    static $badIP;
    static $ok;

    /**
     * The 'worker' methods.
     */
    static function CleanOrphanDatabases($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_dbmap WHERE mm_user_fk=:userid";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $uid);
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                $rowdbSql = "SELECT * FROM x_mysql_databases WHERE my_id_pk=:id AND my_deleted_ts IS NULL";
                $find = $zdbh->prepare($rowdbSql);
                $find->bindParam(':id', $rowmysql['mm_database_fk']);
                $find->execute();
                $rowdb = $find->fetch();

                if (!$rowdb) {

                }
            }
            return true;
        } else {
            return false;
        }
    }

    static function ListUsers($uid)
    {
        global $zdbh;
        // Remove deleted databases from MySQL userlist...
        self::CleanOrphanDatabases($uid);
        $sql = "SELECT * FROM x_mysql_users WHERE mu_acc_fk=:userid AND mu_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $sql->bindParam(':userid', $uid);
            $res = array();
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                //$numrowdb = $zdbh->query("SELECT COUNT(*) FROM x_mysql_dbmap WHERE mm_user_fk=" . $rowmysql['mu_id_pk'] . "")->fetch();
                $numrows = $zdbh->prepare("SELECT COUNT(*) FROM x_mysql_dbmap WHERE mm_user_fk=:mysql");
                $numrows->bindParam(':mysql', $rowmysql['mu_id_pk']);
                $numrows->execute();
                $numrowdb = $numrows->fetch();

                if ($rowmysql['mu_access_vc'] == "%") {
                    $access = "ANY";
                } else {
                    $access = $rowmysql['mu_access_vc'];
                }
                array_push($res, array('userid' => $rowmysql['mu_id_pk'],
                    'username' => $rowmysql['mu_name_vc'],
                    'dbpassword' => $rowmysql['mu_pass_vc'],
                    'totaldb' => $numrowdb[0],
                    'accesshtml' => $access,
                    'access' => $rowmysql['mu_access_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListDatabases($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_databases WHERE my_acc_fk=:userid AND my_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':userid', $uid);
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                array_push($res, array('mysqlid' => $rowmysql['my_id_pk'],
                    'mysqlname' => $rowmysql['my_name_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListUserDatabases($uid)
    {
        global $zdbh;
        $sql = "SELECT * FROM x_mysql_dbmap WHERE mm_user_fk=:userid";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->bindParam(':userid', $uid);
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                $numrows = $zdbh->prepare("SELECT * FROM x_mysql_databases WHERE my_id_pk=:database AND my_deleted_ts IS NULL");
                $numrows->bindParam(':database', $rowmysql['mm_database_fk']);
                $numrows->execute();
                $rowdb = $numrows->fetch();
                if ($rowdb) {
                    array_push($res, array('mmid' => $rowmysql['mm_id_pk'],
                        'mmaccount' => $rowmysql['mm_acc_fk'],
                        'mmuserid' => $rowmysql['mm_user_fk'],
                        'mmdbid' => $rowmysql['mm_database_fk'],
                        'mmdbname' => $rowdb['my_name_vc']));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ListCurrentUser($mid)
    {
        global $zdbh;
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:mid AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':mid', $mid);
        $numrows->execute();

        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:mid AND mu_deleted_ts IS NULL");
            $res = array();
            $sql->bindParam(':mid', $mid);
            $sql->execute();
            while ($rowmysql = $sql->fetch()) {
                array_push($res, array('userid' => $rowmysql['mu_id_pk'],
                    'username' => $rowmysql['mu_name_vc']));
            }
            return $res;
        } else {
            return false;
        }
    }

    static function ExecuteCreateUser($uid, $username, $database, $access)
    {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail($uid);
        // Check for spaces and remove if found...
        $username = strtolower(str_replace(' ', '', $username));
        // If errors are found, then exit before creating user...
        if (fs_director::CheckForEmptyValue(self::CheckCreateForErrors($username, $database, $access))) {
            return false;
        }
        runtime_hook::Execute('OnBeforeCreateDatabaseUser');
        $password = fs_director::GenerateRandomPassword(9, 4);
        // Create user in MySQL
        $sql = $zdbh->prepare("CREATE USER :username@:access;");
        $sql->bindParam(':username', $username);
        $sql->bindParam(':access', $access);
        $sql->execute();
        // Set MySQL password for new user...
        $sql = $zdbh->prepare("SET PASSWORD FOR :username@:access=PASSWORD(:password)");
        $sql->bindParam(':username', $username);
        $sql->bindParam(':access', $access);
        $sql->bindParam(':password', $password);
        $sql->execute();
        // Get the database name from the ID...
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_databases WHERE my_id_pk=:database AND my_deleted_ts IS NULL");
        $numrows->bindParam(':database', $database);
        $numrows->execute();
        $rowdb = $numrows->fetch();
        // Remove all priveledges to all databases
        $sql = $zdbh->prepare("GRANT USAGE ON *.* TO :username@:access");
        $sql->bindParam(':username', $username);
        $sql->bindParam(':access', $access);
        $sql->execute();
        // Grant privileges for new user to the assigned database...
        $usernameClean = $zdbh->mysqlRealEscapeString($username);
        $accessClean = $zdbh->mysqlRealEscapeString($access);
        $my_name_vc = $zdbh->mysqlRealEscapeString($rowdb['my_name_vc']);
        $sql = $zdbh->prepare("GRANT ALL PRIVILEGES ON `$my_name_vc`.* TO `$usernameClean`@`$accessClean`");
        //$sql->bindParam(':username', $username, PDO::PARAM_STR);
        //$sql->bindParam(':access', $access, PDO::PARAM_STR);
        //$sql->bindParam(':name', $rowdb['my_name_vc'], PDO::PARAM_STR);
        $sql->execute();
        $sql = $zdbh->prepare("FLUSH PRIVILEGES");
        $sql->execute();
        // Add user to Sentora database...
        $sql = $zdbh->prepare("INSERT INTO x_mysql_users (
								mu_acc_fk,
								mu_name_vc,
								mu_database_fk,
								mu_pass_vc,
								mu_access_vc,
								mu_created_ts) VALUES (
								:userid,
								:username,
								:database,
								:password,
								:access,
								:time)");
        $sql->bindParam(':userid', $uid);
        $sql->bindParam(':username', $username);
        $sql->bindParam(':database', $database);
        $sql->bindParam(':password', $password);
        $sql->bindParam(':access', $access);
        $time = time();
        $sql->bindParam(':time', $time);
        $sql->execute();
        // Get the new users id...
        //$rowuser = $zdbh->query("SELECT * FROM x_mysql_users WHERE mu_name_vc='" . $username . "' AND mu_acc_fk=" . $uid . " AND mu_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_name_vc=:username AND mu_acc_fk=:userid AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':username', $username);
        $numrows->bindParam(':userid', $uid);
        $numrows->execute();
        $rowuser = $numrows->fetch();
        // Add database to Sentora user account...
        self::ExecuteAddDB($uid, $rowuser['mu_id_pk'], $database);
        runtime_hook::Execute('OnAfterCreateDatabaseUser');
        self::$ok = true;
        return true;
    }

    static function CheckCreateForErrors($username, $database, $access)
    {
        global $zdbh;
        // Check to make sure the user name is not blank before we go any further...
        if ($username == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure the user name is not blank before we go any further...
        if ($username == 'root') {
            self::$rootabuse = true;
            return false;
        }
        // Check to make sure the user name is not blank before we go any further...
        if ($database == '') {
            self::$blank = true;
            return false;
        }
        // Check to make sure the user name is not a duplicate...
        $sql = "SELECT COUNT(*) FROM x_mysql_users WHERE mu_name_vc=:username AND mu_deleted_ts IS NULL";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':username', $username);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                self::$alreadyexists = true;
                return false;
            }
        }
        // Check to make sure the user name is not a duplicate (checks actual mysql table)...
        $sql = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = :username)";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':username', $username);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                self::$alreadyexists = true;
                return false;
            }
        }
        // Check for invalid username
        if (!self::IsValidUserName($username)) {
            self::$badname = true;
            return false;
        }
        // Check for invalid IP address
        if ($access != "%" && strtolower($access) != "localhost") {
            if (!sys_monitoring::IsAnyValidIP($access)) {
                self::$badIP = true;
                return false;
            }
        }
        return true;
    }

    static function CheckAddForErrors($userid, $database)
    {
        global $zdbh;
        // Check to make sure the database isnt already added...
        //$result = $zdbh->query("SELECT * FROM x_mysql_dbmap WHERE mm_database_fk=" . $database . " AND mm_user_fk=" . $userid . "")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_dbmap WHERE mm_database_fk=:database AND mm_user_fk=:userid");
        $numrows->bindParam(':database', $database);
        $numrows->bindParam(':userid', $userid);
        $numrows->execute();
        $result = $numrows->fetch();
        if ($result) {
            self::$dbalreadyadded = true;
            return false;
        }
        return true;
    }

    static function ExecuteDeleteUser($mu_id_pk)
    {
        global $zdbh;
        runtime_hook::Execute('OnBeforeDeleteDatabaseUser');
        //$rowuser = $zdbh->query("SELECT * FROM x_mysql_users WHERE mu_id_pk=" . $mu_id_pk . " AND mu_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:mu_id_pk AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':mu_id_pk', $mu_id_pk);
        $numrows->execute();
        $rowuser = $numrows->fetch();

        $sql = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = :name)";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':name', $rowuser['mu_name_vc']);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                //drop user
                $sql = $zdbh->prepare("DROP USER :name@:access;");
                $sql->bindParam(':name', $rowuser['mu_name_vc']);
                $sql->bindParam(':access', $rowuser['mu_access_vc']);
                $sql->execute();
                //flush privileges
                $sql = $zdbh->prepare("FLUSH PRIVILEGES");
                $sql->execute();
            }
        }
        $sql = $zdbh->prepare("
			UPDATE x_mysql_users
			SET mu_deleted_ts = :time
			WHERE mu_id_pk = :mu_id_pk");
        $time = time();
        $sql->bindParam(':time', $time);
        $sql->bindParam(':mu_id_pk', $mu_id_pk);
        $sql->execute();
        $sql = $zdbh->prepare("
			DELETE FROM x_mysql_dbmap
			WHERE mm_user_fk = :mu_id_pk");
        $sql->bindParam(':mu_id_pk', $mu_id_pk);
        $sql->execute();
        runtime_hook::Execute('OnAfterDeleteDatabaseUser');
        self::$ok = true;
        return true;
    }

    static function ExecuteAddDB($uid, $myuserid, $dbid)
    {
        global $zdbh;
        if (fs_director::CheckForEmptyValue(self::CheckAddForErrors($myuserid, $dbid))) {
            return false;
        }
        if (!isset($uid) || $uid == NULL || $uid == '') {
            $currentuser = ctrl_users::GetUserDetail();
            $uid = $currentuser['userid'];
        }
        runtime_hook::Execute('OnBeforeAddDatabaseAccess');
        //$rowdb = $zdbh->query("SELECT * FROM x_mysql_databases WHERE my_id_pk=" . $dbid . " AND my_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_databases WHERE my_id_pk=:dbid AND my_deleted_ts IS NULL");
        $numrows->bindParam(':dbid', $dbid);
        $numrows->execute();
        $rowdb = $numrows->fetch();

        //$rowuser = $zdbh->query("SELECT * FROM x_mysql_users WHERE mu_id_pk=" . $myuserid . " AND mu_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:myuserid AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':myuserid', $myuserid);
        $numrows->execute();
        $rowuser = $numrows->fetch();

        $my_name_vc = $zdbh->mysqlRealEscapeString($rowdb['my_name_vc']);
        $mu_name_vc = $zdbh->mysqlRealEscapeString($rowuser['mu_name_vc']);
        $mu_access_vc = $zdbh->mysqlRealEscapeString($rowuser['mu_access_vc']);
        $sql = $zdbh->prepare("GRANT ALL PRIVILEGES ON `$my_name_vc`.* TO `$mu_name_vc`@`$mu_access_vc`");
        $sql->bindParam(':my_name_vc', $rowdb['my_name_vc'], PDO::PARAM_STR);
        $sql->bindParam(':mu_name_vc', $rowuser['mu_name_vc'], PDO::PARAM_STR);
        $sql->bindParam(':mu_access_vc', $rowuser['mu_access_vc'], PDO::PARAM_STR);
        $sql->execute();
        $sql = $zdbh->prepare("FLUSH PRIVILEGES");
        $sql->execute();
        $sql2 = $zdbh->prepare("
			INSERT INTO x_mysql_dbmap (
							mm_acc_fk,
							mm_user_fk,
							mm_database_fk) VALUES (
							:uid,
							:myuserid,
							:dbid
                                                        )");
        $sql2->bindParam(':uid', $uid);
        $sql2->bindParam(':myuserid', $myuserid);
        $sql2->bindParam(':dbid', $dbid);
        $sql2->execute();
        runtime_hook::Execute('OnAfterAddDatabaseAccess');
        self::$ok = true;
        return true;
    }

    static function ExecuteRemoveDB($myuserid, $mapid)
    { // <-- mmid = dbmaps
        global $zdbh;
        runtime_hook::Execute('OnBeforeRemoveDatabaseAccess');

        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_dbmap WHERE mm_id_pk=:mapid");
        $numrows->bindParam(':mapid', $mapid);
        $numrows->execute();
        $rowdbmap = $numrows->fetch();

        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_databases WHERE my_id_pk=:mm_database_fk AND my_deleted_ts IS NULL");
        $numrows->bindParam(':mm_database_fk', $rowdbmap['mm_database_fk']);
        $numrows->execute();
        $rowdb = $numrows->fetch();

        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:myuserid AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':myuserid', $myuserid);
        $numrows->execute();
        $rowuser = $numrows->fetch();

        $sql = $zdbh->prepare("REVOKE ALL PRIVILEGES ON `" . $rowdb['my_name_vc'] . "`.* FROM '" . $rowuser['mu_name_vc'] . "'@'" . $rowuser['mu_access_vc'] . "'");
        $sql->execute();

        $sql = $zdbh->prepare("FLUSH PRIVILEGES");
        $sql->execute();

        $sql = $zdbh->prepare("DELETE FROM x_mysql_dbmap WHERE mm_id_pk=:mapid AND mm_user_fk=:myuserid");
        $sql->bindParam(':mapid', $mapid);
        $sql->bindParam(':myuserid', $myuserid);
        $sql->execute();

        runtime_hook::Execute('OnAfterRemoveDatabaseAccess');
        self::$ok = true;
        return true;
    }

    static function ExecuteResetPassword($myuserid, $password)
    {
        global $zdbh;
        runtime_hook::Execute('OnBeforeResetDatabasePassword');
        //$rowuser = $zdbh->query("SELECT * FROM x_mysql_users WHERE mu_id_pk=" . $myuserid . " AND mu_deleted_ts IS NULL")->fetch();
        $numrows = $zdbh->prepare("SELECT * FROM x_mysql_users WHERE mu_id_pk=:myuserid AND mu_deleted_ts IS NULL");
        $numrows->bindParam(':myuserid', $myuserid);
        $numrows->execute();
        $rowuser = $numrows->fetch();

        // If errors are found, then exit before resetting password...
        if (fs_director::CheckForEmptyValue(self::CheckPasswordForErrors($password))) {
            return false;
        }
        $sql = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = :mu_name_vc)";
        $numrows = $zdbh->prepare($sql);
        $numrows->bindParam(':mu_name_vc', $rowuser['mu_name_vc']);
        if ($numrows->execute()) {
            if ($numrows->fetchColumn() <> 0) {
                // Set MySQL password for new user...
                $sql = $zdbh->prepare("SET PASSWORD FOR :mu_name_vc@:mu_access_vc=PASSWORD(:password)");
                $sql->bindParam(':mu_name_vc', $rowuser['mu_name_vc']);
                $sql->bindParam(':mu_access_vc', $rowuser['mu_access_vc']);
                $sql->bindParam(':password', $password);
                $sql->execute();
                $sql = $zdbh->prepare("FLUSH PRIVILEGES");
                $sql->execute();
                $sql = $zdbh->prepare("UPDATE x_mysql_users SET mu_pass_vc=:password WHERE mu_id_pk=:myuserid");
                $sql->bindParam(':password', $password);
                $sql->bindParam(':myuserid', $myuserid);
                $sql->execute();
            }
        }
        runtime_hook::Execute('OnAfterResetDatabasePassword');
        self::$ok = true;
        return true;
    }

    static function CheckPasswordForErrors($password)
    {
        if (!self::IsValidPassword($password)) {
            self::$badpass = true;
            return false;
        }
        return true;
    }

    static function IsValidUserName($username)
    {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $username) || preg_match('/-$/', $username)) {
            return false;
        } else {
            if (strlen($username) < 17) {
                // Enforce the MySQL username limit! (http://dev.mysql.com/doc/refman/4.1/en/user-names.html)
                return true;
            }
            return false;
        }
    }

    static function IsValidPassword($password)
    {
        if (!ctype_alnum($password)) {
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
    static function doCreateUser()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if ($formvars['inAccess'] == 1) {
            $access = "%";
        } else {
            $access = $formvars['inAccessIP'];
        }
        if (self::ExecuteCreateUser($currentuser['userid'], $formvars['inUserName'], $formvars['inDatabase'], $access))
            return true;
        return false;
    }

    static function doEditUser()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListUsers($currentuser['userid']) as $row) {
            if (isset($formvars['inDelete_' . $row['userid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $row['userid'] . "");
                exit;
            }
            if (isset($formvars['inEdit_' . $row['userid'] . ''])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Edit&other=" . $row['userid'] . "");
                exit;
            }
        }
        return;
    }

    static function doAddDB()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteAddDB($currentuser['userid'], $formvars['inUser'], $formvars['inDatabase']))
            return true;
        return false;
    }

    static function doRemoveDB()
    {
        global $controller;
        runtime_csfr::Protect();
        $currentuser = ctrl_users::GetUserDetail();
        $formvars = $controller->GetAllControllerRequests('FORM');
        foreach (self::ListUserDatabases($formvars['inUser']) as $row) {
            if (isset($formvars['inRemove_' . $row['mmid'] . ''])) {
                if (self::ExecuteRemoveDB($formvars['inUser'], $formvars['inRemove_' . $row['mmid'] . ''])) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    static function doConfirmDeleteUser()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteDeleteUser($formvars['inDelete']))
            return true;
        return false;
    }

    static function doResetPW()
    {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ExecuteResetPassword($formvars['inUser'], $formvars['inResetPW']))
            return true;
        return false;
    }

    static function getUserList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListUsers($currentuser['userid']);
    }

    static function getDatabaseList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListDatabases($currentuser['userid']);
    }

    static function getUserDatabaseList()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return self::ListUserDatabases($controller->GetControllerRequest('URL', 'other'));
    }

    static function getisDeleteUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getisEditUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Edit"))
            return true;
        return false;
    }

    static function getisCreateUser()
    {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if (!isset($urlvars['show']))
            return true;
        return false;
    }

    static function getCurrentUserName()
    {
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        return $currentuser['username'];
    }

    static function getEditCurrentUserName()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentUser($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['username'];
        } else {
            return "";
        }
    }

    static function getEditCurrentUserID()
    {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            $current = self::ListCurrentUser($controller->GetControllerRequest('URL', 'other'));
            return $current[0]['userid'];
        } else {
            return "";
        }
    }

    static function getMysqlUsagepChart()
    {
        return '<img src="' . ui_tpl_assetfolderpath::Template() . 'img/misc/unlimited.png" alt="' . ui_language::translate('Unlimited') . '"/>';
    }

    static function getResult()
    {
        if (!fs_director::CheckForEmptyValue(self::$blank)) {
            return ui_sysmessage::shout(ui_language::translate("You need to specify a user name and select a database to create your MySQL user."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$rootabuse)) {
            return ui_sysmessage::shout(ui_language::translate("You cannot create a user named 'root'! This attempt has been logged and the system administrator notified!."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$alreadyexists)) {
            return ui_sysmessage::shout(ui_language::translate("A MySQL username with that name already appears to exsist."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badname)) {
            return ui_sysmessage::shout(ui_language::translate("Your MySQL user name is not valid. Please enter a valid MySQL user name."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badpass)) {
            return ui_sysmessage::shout(ui_language::translate("Your MySQL password is not valid. Valid characters are A-Z, a-z, 0-9."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$badIP)) {
            return ui_sysmessage::shout(ui_language::translate("The IP address is not valid. Please enter a valid IP address."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$dbalreadyadded)) {
            return ui_sysmessage::shout(ui_language::translate("That database has already been added to this user."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Changes to your MySQL users have been saved successfully!"), "zannounceok");
        }
        return;
    }

    /**
     * Webinterface sudo methods.
     */
}
