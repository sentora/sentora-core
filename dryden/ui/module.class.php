<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_module {

    function __construct() {
        
    }

    /**
     * Checks that the module exists.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $name
     * @return boolean 
     */
    static function CheckModuleExists($name) {
        $user = ctrl_users::GetUserDetail();
        if (file_exists("modules/" . $name . "/module.zpm"))
            if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], self::GetModuleID()))
                return true;
        return false;
    }

    /**
     * Returns the module code.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $name
     * @return string 
     */
    static function GetModuleContent($name) {
        if (self::CheckModuleExists($name)) {
            return fs_filehandler::ReadFileContents("modules/" . $name . "/module.zpm");
        }
    }

    /**
     * Handles the GetModule control, if unable to load the module will handle the error too!
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $module
     * @return string 
     */
    static function GetModule($module) {
        /**
         * This is where it outputs the module code to the view.
         * @var $module (string) is the folder path to the requested module.
         */
        if (self::CheckModuleExists($module)) {
            $retval = self::GetModuleContent($module);
        } else {
            $retval = "Unable to find requested module!";
        }
        return $retval;
    }

    /**
     * Gathers module infomation from the FS and adds the detail to the DB.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @var $module Name of the module (folder name)
     * @return boolean
     */
    static function ModuleInfoToDB($module) {
        global $zdbh;
        global $zlo;
        runtime_hook::Execute('OnBeforeModuleInfoToDB');
        $mod_xml = "modules/$module/module.xml";
        try {
            $mod_config = new xml_reader(fs_filehandler::ReadFileContents($mod_xml));
            $mod_config->Parse();
            $module_name = $mod_config->document->name[0]->tagData;
            $module_version = $mod_config->document->version[0]->tagData;
            $module_description = $mod_config->document->desc[0]->tagData;
            $sql = $zdbh->prepare("INSERT INTO x_modules (mo_name_vc, mo_version_in, mo_folder_vc, mo_installed_ts, mo_desc_tx) VALUES ('$module_name', $module_version, '$module_name', " . time() . ", '$module_description')");
            $sql->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
        runtime_hook::Execute('OnAfterModuleInfoToDB');
    }

    /**
     * This class scans the module directory and will return an array of new modules that are not yet in the database.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return array 
     */
    static function ScanForNewModules() {
        $new_module_list = array();

        return $new_module_list;
    }

    /**
     * This class returns the name of the current module.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $controller
     * @global type $zdbh
     * @return type 
     */
    static function GetModuleName() {
        global $controller;
        global $zdbh;
        $retval = $zdbh->query("SELECT mo_name_vc FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'module') . "'")->Fetch();
        $retval = $retval['mo_name_vc'];
        return $retval;
    }

    /**
     * This class returns the database ID of the current module.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $controller
     * @global type $zdbh
     * @return type 
     */
    static function GetModuleID() {
        global $controller;
        global $zdbh;
        $retval = $zdbh->query("SELECT mo_id_pk FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'module') . "'")->Fetch();
        $retval = $retval['mo_id_pk'];
        return $retval;
    }

    /**
     * This class returns the description of the current module.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $controller
     * @global type $zdbh
     * @return type 
     */
    static function GetModuleDescription() {
        global $controller;
        global $zdbh;
        $retval = $zdbh->query("SELECT mo_desc_tx FROM x_modules WHERE mo_folder_vc = '" . $controller->GetControllerRequest('URL', 'module') . "'")->Fetch();
        $retval = $retval['mo_desc_tx'];
        return $retval;
    }

    /**
     * Checks to see if the specified module has updates.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global type $zdbh
     * @param type $modulefolder
     * @return boolean 
     */
    static function GetModuleHasUpdates($modulefolder) {
        global $zdbh;
        $retval = $zdbh->query("SELECT mo_updateurl_tx, mo_updatever_vc FROM x_modules WHERE mo_folder_vc = '" . $modulefolder . "'")->Fetch();
        if ($retval['mo_updatever_vc'] <> "") {
            $retval = array($retval['mo_updatever_vc'], $retval['mo_updateurl_tx']);
        } else {
            $retval = false;
        }
        return $retval;
    }

}

?>
