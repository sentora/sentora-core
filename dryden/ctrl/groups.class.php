<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Group permissions class, handles user group permissions.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_groups {

    /**
     * Checks permissions to a module for a given user group.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $groupid The usergroup ID.
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function CheckGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $sqlString = "SELECT pe_id_pk FROM 
                    x_permissions WHERE 
                    pe_group_fk = :groupid AND 
                    pe_module_fk = :moduleid";
        $bindArray = array(
            ':groupid' => $groupid,
            ':moduleid' => $moduleid,
        );
        $zdbh->bindQuery($sqlString, $bindArray);
        $result = $zdbh->returnRow();
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Adds permission to enable a module for a given user group.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $groupid The usergroup ID.
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function AddGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $sqlString = "SELECT COUNT(*) FROM 
                     x_permissions WHERE 
                     pe_group_fk = :groupid AND 
                     pe_module_fk = :moduleid";
        $bindArray = array(
            ':groupid' => $groupid,
            ':moduleid' => $moduleid,
        );
        $sqlPrepare = $zdbh->prepare($sqlString);
        $zdbh->bindParams($sqlPrepare, $bindArray);
        unset($sqlString);
        $rowCount = $sqlPrepare->rowCount();
        unset($sqlPrepare);

        if ($rowCount < 1) {
            $sqlString = "INSERT INTO x_permissions 
                         ( pe_group_fk , pe_module_fk ) VALUES 
                         ( :groupid , :moduleid )";
            $bindArray = array(
                ':groupid' => $groupid,
                ':moduleid' => $moduleid,
            );
            $sqlPrepare = $zdbh->prepare($sqlString);
            $zdbh->bindParams($sqlPrepare, $bindArray);
            $result = $sqlPrepare->execute();
            if ($result > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Deletes permission to disable a module for a given user group.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $groupid The usergroup ID. (If '0' will delete the permissions for ALL groups)
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function DeleteGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $sqlString = "DELETE FROM x_permissions WHERE pe_module_fk = :moduleid ";
        if ($groupid > 0) {
            $sqlString .= "AND pe_group_fk = :groupid";
            $sqlQuery = $zdbh->prepare($sqlString);
            $sqlQuery->bindParam(':groupid', $groupid);
        } else {
            $sqlQuery = $zdbh->prepare($sqlString);
        }
        $sqlQuery->bindParam(':moduleid', $moduleid);

        if ($sqlQuery->execute() > 0) {
            return true;
        } else {
            return false;
        }
    }

}

?>
