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
        if (isset($this->vars_get[0]['debug'])) {
            
        }

        if (!isset($this->vars_get[0]['module'])) {
            
        }
        return;
    }

    /**
     * Returns a vlaue from one of the requested type.
     */
    public function GetControllerRequest($type="URL", $name) {
        if ($type == 'FORM') {
            return $this->vars_post[0][$name];
        } elseif ($type == 'URL') {
            return $this->vars_get[0][$name];
        } elseif ($type == 'USER') {
            return $this->vars_session[0][$name];
        } else {
            return $this->vars_cookie[0][$name];
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

    public function OutputControllerDebug() {
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
            return "<strong>URL Variables set:</strong><br><pre>" . $set_urls . "</pre><strong>POST Variables set:</strong><br><pre>" . $set_forms . "</pre><strong>SESSION Variables set:</strong><br><pre>" . $set_sessions . "</pre><strong>COOKIE Variables set:</strong><br><pre>" . $set_cookies . "</pre>";
        } else {
            return false;
        }
    }

}

?>
