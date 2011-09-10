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
    /**
     * ui_module class is used to load in the module contents into the controller.
     */

    /**
     * Lets declare some variables here!
     */
    function __construct() {
        /**
         * Lets grab the controller properties and then we can use the values to set the class variables which we will then
         * use to build the UI.
         */
    }

    /**
     * Checks that the module exists.
     * @param string $name
     * @return boolean 
     */
    static function CheckModuleExists($name) {
        if (file_exists("modules/" . $name . "/module.zpm"))
            return true;
        return false;
    }

    /**
     * Returns the module code.
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
     * @var $module Name of the module (folder name)
     * @return boolean
     */
    static function ModuleInfoToDB($module) {
        global $zdbh;
        global $zlo;
        $mod_xml = "modules/$module/module.xml";
        try {
            $mod_config = new xml_reader(fs_filehandler::ReadFileContents($mod_xml));
            $mod_config->Parse();
            $module_name = $mod_config->document->name[0]->tagData;
            $module_version = $mod_config->document->version[0]->tagData;
            $sql = $zdbh->prepare("INSERT INTO x_modules (mo_name_vc, mo_version_in, mo_folder_vc, mo_installed_ts) VALUES ('$module_name', $module_version, '$module_name', " . time() . ")");
            $sql->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

}

?>
