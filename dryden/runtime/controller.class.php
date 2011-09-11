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

        if (isset($this->vars_get['module'])) {
            ui_module::getModule($this->GetCurrentModule());
        }
        if (isset($this->vars_get['debug'])) {
            
        }

        if (!isset($this->vars_get['module'])) {
            
        }
        return;
    }

    /**
     * Returns a vlaue from one of the requested type.
     */
    public function GetControllerRequest($type="URL", $name) {
        if ($type == 'FORM') {
            return $this->vars_post[$name];
        } elseif ($type == 'URL') {
            return $this->vars_get[$name];
        } elseif ($type == 'USER') {
            return $this->vars_session[$name];
        } else {
            return $this->vars_cookie[$name];
        }
        return false;
    }

    public function GetAllControllerRequests($type="URL") {
        if ($type == 'FORM') {
            return $this->vars_post;
        } elseif ($type == 'URL') {
            return $this->vars_get;
        } elseif ($type == 'USER') {
            return $this->vars_session;
        } else {
            return $this->vars_cookie;
        }
        return false;
    }

    public function GetAction() {
        return $this->vars_get['action'];
    }

    public function GetOptions() {
        return $this->vars_get['options'];
    }

    public function GetCurrentModule() {
        if (isset($this->vars_get['module']))
            return $this->vars_get['module'];
        return false;
    }

    public function OutputControllerDebug() {
        if (!isset($this->vars_get['debug'])) {
            $set_urls = var_dump($this->GetAllControllerRequests('URL'));
            $set_forms = var_dump($this->GetAllControllerRequests('FORM'));
            $set_sessions = var_dump($this->GetAllControllerRequests('USER'));
            $set_cookies = var_dump($this->GetAllControllerRequests('COOKIE'));
            return "<strong>URL Variables set:</strong><br><pre>" . $set_urls . "</pre><strong>POST Variables set:</strong><br><pre>" . $set_forms . "</pre><strong>SESSION Variables set:</strong><br><pre>" . $set_sessions . "</pre><strong>COOKIE Variables set:</strong><br><pre>" . $set_cookies . "</pre>";
        }
    }

}

?>
