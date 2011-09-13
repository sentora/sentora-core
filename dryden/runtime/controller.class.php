<?php

/**
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_controller {

    private $vars_get;
    private $vars_post;
    private $vars_session;
    private $vars_cookie;

    /**
     * Get the latest requests and updates the values avaliable to the model/view.
     */
    public function Init() {
        $this->vars_get = array($_GET);
        $this->vars_post = array($_POST);
        $this->vars_session = array($_SESSION);
        $this->vars_cookie = array($_COOKIE);

        if (isset($this->vars_get[0]['module'])) {
            ui_module::getModule($this->GetCurrentModule());
        }
        if (isset($this->vars_get[0]['action'])) {
            if ((class_exists('module_controller', FALSE)) && (method_exists('module_controller', 'do' . $this->vars_get[0]['action']))) {
                call_user_func(array('module_controller', 'do' . $this->vars_get[0]['action']));
            } else {
                echo ui_sysmessage::shout("No 'do" . $this->vars_get[0]['action'] . "' class exists - Please create it to enable controller actions and runtime placeholders within your module.");
            }
        }
        return;
    }

    /**
     * Returns a vlaue from one of the requested type.
     */
    public function GetControllerRequest($type="URL", $name) {
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

    public function GetAllControllerRequests($type="URL") {
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

    public function GetAction() {
        return $this->vars_get[0]['action'];
    }

    public function GetOptions() {
        return $this->vars_get[0]['options'];
    }

    public function GetCurrentModule() {
        if (isset($this->vars_get[0]['module']))
            return $this->vars_get[0]['module'];
        return false;
    }

    /**
     * Displays Controller debug infomation (mainly for module development and debugging)
     * @global type $script_memory
     * @return type 
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
            ob_start();
            debug_execution::GetLoadedClasses();
            $classes_loaded = ob_get_contents();
            ob_end_clean();
            $mtime = microtime();
            $mtime = explode(" ", $mtime);
            $mtime = $mtime[1] + $mtime[0];
            $endtime = $mtime;
            $totaltime = ($endtime - $starttime);
            return "<h1>Controller Debug Mode</h1><strong>PHP Script Memory Usage:</strong> " . debug_execution::ScriptMemoryUsage($script_memory) . "<br><strong>Script Execution Time: </strong> " . $totaltime . "<br><br><strong>URL Variables set:</strong><br><pre>" . $set_urls . "</pre><strong>POST Variables set:</strong><br><pre>" . $set_forms . "</pre><strong>SESSION Variables set:</strong><br><pre>" . $set_sessions . "</pre><strong>COOKIE Variables set:</strong><br><pre>" . $set_cookies . "</pre><br><br><strong>Loaded classes:</strong><pre>" . $classes_loaded . "</pre>";
        } else {
            return false;
        }
    }

}

?>
