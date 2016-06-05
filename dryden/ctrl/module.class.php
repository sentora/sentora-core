<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Default module methods, module_controller classes should extend this class.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_module
{

    /**
     * Returns the name of the module.
     * @return string
     */
    public static function getModuleName()
    {
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    /**
     * Returns the modules description, this is pretty standard and by default is taken from the module description in the module table but
     * is designed to be overwritten in the module_controller class if a different alternative is required.
     * @return type
     */
    public static function getModuleDesc()
    {
        $module_desc = ui_language::translate(ui_module::GetModuleDescription());
        return $module_desc;
    }

    /**
     * Provides module icon functionality.
     * @global type $controller
     * @return type
     */
    public static function getModuleIcon()
    {
        global $controller;
        $mod_dir = $controller->GetControllerRequest('URL', 'module');
        // Check if the current userland theme has a module icon override
        if (file_exists('etc/styles/' . ui_template::GetUserTemplate() . '/img/modules/' . $mod_dir . '/assets/icon.png'))
            return './etc/styles/' . ui_template::GetUserTemplate() . '/img/modules/' . $mod_dir . '/assets/icon.png';
        return './modules/' . $mod_dir . '/assets/icon.png';
    }

    /**
     * Provides a simple method to access the current path to the module.
     * @global type $controller
     * @return string Directory path to the module root.
     */
    static function getModulePath()
    {
        global $controller;
        return 'modules/' . $controller->GetControllerRequest('URL', 'module') . '/';
    }

    /**
     * Returns the CSFR tag of which the module should use when attempting to post FORM data.
     * @return string
     */
    public static function getCSFR_Tag()
    {
        return runtime_csfr::Token();
    }

}
