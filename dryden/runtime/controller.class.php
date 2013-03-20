<?php

/**
 * The controller class!
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_controller {

    /**
     * @var array All current request 'get' variables.
     */
    private $vars_get;

    /**
     * @var array All current request 'post' variables.
     */
    private $vars_post;

    /**
     * @var array All current request 'session' variables.
     */
    private $vars_session;

    /**
     * @var array All current request 'cookie' variables.
     */
    private $vars_cookie;

    /**
     * Get the latest requests and updates the values avaliable to the model/view.
     * @author Bobby Allen (ballen@zpanelcp.com)
     */
    public function Init() {

        //Set class varables
        $this->vars_get = array($_GET);
        $this->vars_post = array($_POST);
        $this->vars_session = array($_SESSION);
        $this->vars_cookie = array($_COOKIE);

        //Here we get the users information
        $user = ctrl_users::GetUserDetail();

        if (!isset($this->vars_session[0]['zpuid'])) {
            ui_module::GetLoginTemplate();
        }

        if (isset($this->vars_get[0]['module'])) {
            ui_module::getModule($this->GetCurrentModule());
        }
        if (isset($this->vars_get[0]['action'])) {
            if (ctrl_groups::CheckGroupModulePermissions($user['usergroupid'], ui_module::GetModuleID())) {
                if ((class_exists('module_controller', FALSE)) && (method_exists('module_controller', 'do' . $this->vars_get[0]['action']))) {
                    call_user_func(array('module_controller', 'do' . $this->vars_get[0]['action']));
                } else {
                    echo ui_sysmessage::shout("No 'do" . runtime_xss::xssClean($this->vars_get[0]['action']) . "' class exists - Please create it to enable controller actions and runtime placeholders within your module.");
                }
            }
        }
        return;
    }

    /**
     * Returns a vlaue from one of the requested type.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $type The type of request data to return.
     * @param string $name The named key of the array.
     * @retrun mixed Returns that array data if avaliable (is set) otherwise will return 'false'.
     */
    public function GetControllerRequest($type = "URL", $name) {
        if ($type == 'FORM') {
            if (isset($this->vars_post[0][$name])) {
                return $this->vars_post[0][$name];
            } else {
                return false;
            }
        } elseif ($type == 'URL') {
            if (isset($this->vars_get[0][$name])) {
                return $this->vars_get[0][$name];
            } else {
                return false;
            }
        } elseif ($type == 'USER') {
            if (isset($this->vars_session[0][$name])) {
                return $this->vars_session[0][$name];
            } else {
                return false;
            }
        } else {
            if (isset($this->vars_cookie[0][$name])) {
                return $this->vars_cookie[0][$name];
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Grabs the list of all controller requests for a given type.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $type What type of requests would you like to see? (URL, USER, FORM or COOKIE)
     * @return array List of all set variables for the requested type.
     */
    public function GetAllControllerRequests($type = "URL") {
        if ($type == 'FORM') {
            return $this->vars_post[0];
        } elseif ($type == 'URL') {
            return $this->vars_get[0];
        } elseif ($type == 'USER') {
            return $this->vars_session[0];
        } else {
            return $this->vars_cookie[0];
        }
        return false;
    }

    /**
     * Gets the current framework requested action.
     * @return boolean
     */
    public function GetAction() {
        if (isset($this->vars_get[0]['action']))
            return $this->vars_get[0]['action'];
        return false;
    }

    /**
     * Gets the current framework requested module 'options'.
     * @return boolean
     */
    public function GetOptions() {
        if (isset($this->vars_get[0]['options']))
            return $this->vars_get[0]['options'];
        return false;
    }

    /**
     * Gets and returns the name of the current module.
     * @return boolean
     */
    public function GetCurrentModule() {
        if (isset($this->vars_get[0]['module']))
            return $this->vars_get[0]['module'];
        return false;
    }

    /**
     * Displays Controller debug infomation (mainly for module development and debugging)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global string $script_memory The current amount of memory that the script it using.
     * @global int $starttime The microtime of when the script started executing.
     * @return string HTML output of the debug infomation.
     */
    public function OutputControllerDebug() {
        global $script_memory;
        global $starttime;
        if (isset($this->vars_get[0]['debug'])) {
            ob_start();
            var_dump($this->GetAllControllerRequests('URL'));
            $set_urls = ob_get_contents();
            ob_end_clean();
            ob_start();
            var_dump($this->GetAllControllerRequests('FORM'));
            $set_forms = ob_get_contents();
            ob_end_clean();
            ob_start();
            var_dump($this->GetAllControllerRequests('USER'));
            $set_sessions = ob_get_contents();
            ob_end_clean();
            ob_start();
            var_dump($this->GetAllControllerRequests('COOKIE'));
            $set_cookies = ob_get_contents();
            ob_end_clean();
            $classes_loaded = debug_execution::GetLoadedClasses();
            ob_start();
            print_r($classes_loaded);
            $classes_array = ob_get_contents();
            ob_end_clean();
            $sql_queries = debug_execution::GetSQLQueriesExecuted();
            ob_start();
            print_r($sql_queries);
            $sql_array = ob_get_contents();
            ob_end_clean();
            $mtime = microtime();
            $mtime = explode(" ", $mtime);
            $mtime = $mtime[1] + $mtime[0];
            $endtime = $mtime;
            $totaltime = ($endtime - $starttime);
            runtime_hook::Execute('OnDisplayRuntimeDebug');
            return "<h1>Controller Debug Mode</h1><strong>PHP Script Memory Usage:</strong> " . debug_execution::ScriptMemoryUsage($script_memory) . "<br><strong>Script Execution Time: </strong> " . $totaltime . "<br><br><strong>URL Variables set:</strong><br><pre>" . $set_urls . "</pre><strong>POST Variables set:</strong><br><pre>" . $set_forms . "</pre><strong>SESSION Variables set:</strong><br><pre>" . $set_sessions . "</pre><strong>COOKIE Variables set:</strong><br><pre>" . $set_cookies . "</pre><br><br><strong>Loaded classes (Total: " . count($classes_loaded) . "):</strong><pre>" . $classes_array . "</pre><br><br><strong>SQL queries executed (Total: " . count($sql_queries) . "):</strong><pre>" . $sql_array . "</pre>";
        } else {
            return false;
        }
    }

    /**
     * Checks if the current script is running in CLI mode (eg. as a cron job)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return boolean
     */
    static function IsCLI() {
        if (!@$_SERVER['HTTP_USER_AGENT'])
            return true;
        return false;
    }

    /**
     * Used in hooks to communicate with the modules controller.ext.php
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $module_path The full path to the module.
     */
    static function ModuleControllerCode($module_path) {
        $raw_path = str_replace("\\", "/", $module_path);
        $module_path = str_replace("/hooks", "/code/", $raw_path);
        $rawroot_path = str_replace("\\", "/", dirname(__FILE__));
        $root_path = str_replace("/dryden/runtime", "/", $rawroot_path);
        require_once $root_path . 'dryden/loader.inc.php';
        require_once $root_path . 'cnf/db.php';
        require_once $root_path . 'inc/dbc.inc.php';
        if (file_exists($module_path . 'controller.ext.php')) {
            require_once $module_path . 'controller.ext.php';
        } else {
            $hook_log = new debug_logger();
            $hook_log->method = ctrl_options::GetSystemOption('logmode');
            $hook_log->logcode = "611";
            $hook_log->detail = "No hook controller.ext.php avaliable to import in (" . $root_path . 'controller.ext.php' . ")";
            $hook_log->writeLog();
        }
    }

}

?>
