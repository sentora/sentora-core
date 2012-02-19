<?php

/**
 * Group permissions class, handles user group permissions.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_groups {

    /**
     * Checks permissions to a module for a given user group.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param int $groupid The usergroup ID.
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function CheckGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $rows = $zdbh->query("SELECT pe_id_pk FROM x_permissions WHERE pe_group_fk=$groupid AND pe_module_fk=$moduleid")->fetch();
        if ($rows)
            return true;
        return false;
    }

    /**
     * Adds permission to enable a module for a given user group.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param int $groupid The usergroup ID.
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function AddGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $sql = "SELECT COUNT(*) FROM x_permissions WHERE pe_group_fk=$groupid AND pe_module_fk=$moduleid";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() < 1) {
            $statement = "INSERT INTO x_permissions (pe_group_fk, pe_module_fk) VALUES ($groupid, $moduleid)";
            if ($zdbh->exec($statement) > 0)
                return true;
            return false;
        }
    }

    /**
     * Deletes permission to disable a module for a given user group.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param int $groupid The usergroup ID.
     * @param int $moduleid The module ID.
     * @return bool
     */
    static function DeleteGroupModulePermissions($groupid, $moduleid) {
        global $zdbh;
        $statement = "DELETE FROM x_permissions WHERE pe_group_fk=$groupid AND pe_module_fk=$moduleid";
        if ($zdbh->exec($statement) > 0)
            return true;
        return false;
    }

}

?>
