<?php
/**
 * Session security class.
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.2
 * @author Sam Mottley (smottley@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_sessionsecurity {
    
    /*****The below are generic function used more than once*****/
    /**
     * Regenerate the PHPSID  
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function sessionRegen(){
         if(session_regenerate_id()){
             return true;
         }else{
             return false;
         }
    }
    
    /**
     * Get users ip address 
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The Clean IP.
     */
    static public function findIP() {
        //$ip = $_SERVER["REMOTE_ADDR"];
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $ip =  $_SERVER["REMOTE_ADDR"] . ' ';
        } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"] . ' ';
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"] . ' ';
        }
        return $ip;
    }
    
    /**
     * Distroys the current session
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The Clean IP.
     */
    static public function destroyCurrentSession(){
        $_SESSION['zpuid'] = null;
        unset($_COOKIE['zUserSaltCookie']);
        session_destroy();
        return true;
    }
    
    /**
     * Get users details that are spefic for the individual user only
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function userSpeficData(){
        $ip = self::findIP();
        $username = $_SESSION['zpuid'];
        return $ip.$username;
    }
    
    
    /*****Here we are are gathering infomration and storing securty*****/
    /**
     * This function will set the users agent in a secure session and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function setUserAgent(){
        $_SESSION['HTTP_USER_AGENT'] = sha1($_SERVER['HTTP_USER_AGENT'],self::userSpeficData());
    }
    
    /**
     * This function will set the users cookie login ID in a secure cookie and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function setCookie(){ 
        $random = runtime_randomstring::randomHash(100);
        $_SESSION['zUserSalt'] = $random;
        setcookie("zUserSaltCookie", $random, time() + 60 * 60 * 24 * 30, "/"); 
        return true;            
    }
    
    /**
     * This function will set the users IP in a secure session and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function setUserIP(){
        $_SESSION['ip'] = sha1(self::findIP(), self::userSpeficData());
    }
    
    /**
     * This set whether session security is enabled 
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function setSessionSecurityEnabled($option){
        if($option == true){
            $_SESSION['zSessionSecurityEnabled'] = 1;
            return true;
        }else{
            $_SESSION['zSessionSecurityEnabled'] = 0;
            return false;
        }
    }
    
    /*****The below is returning the secure information*****/
    /**
     * This will return the secure session set version of the users agent
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getSetUserAgent(){
        return $_SESSION['HTTP_USER_AGENT'];
    }
    
    /**
     * This will return the secure session set version of the users ip
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getSetIP(){
        return $_SESSION['ip'];
    }
    
    /**
     * This will return the secure session set version of the users cookie
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getSetCookie(){
        return $_SESSION['zUserSalt'];
    }
    
    /*****The below is retrieveing the current provided information*****/
    /**
     * This returns the current provied users agent via headers and THEN hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getProviedUserAgent(){
        return sha1($_SERVER['HTTP_USER_AGENT'], self::userSpeficData());
    }
    
    /**
     * This returns the current provied users agent via headers and THEN hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getProviedCookie(){
        return $_COOKIE["zUserSaltCookie"];
    }
    
    /**
     * This returns the current provied users IP and THEN hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    static public function getProviedIP(){
        return sha1(self::findIP(), self::userSpeficData());
    }
    
    /**
     * This returns whether the user set the session secuirty option on login
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function getSessionSecurityEnabled(){
        if($_SESSION['zSessionSecurityEnabled'] == 1){
            return true;
        }else{
            return false;
        }
    }
    
    
    /*****Below are function that check information and try to identy any tampering*****/
    /**
     * This checks wether the set user agent for the session is the same one as what is currently being provied
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function checkAgent(){
        $userSetAgent = self::getSetUserAgent();
        $currentUserAgent = self::getProviedUserAgent();
        
        if($userSetAgent == $currentUserAgent){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This checks wether the set user ip for the session is the same one as what is currently being provied
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function checkIP(){
        $userSetIP = self::getSetIP();
        $currentUserIP = self::getProviedIP();
        
        if($userSetIP == $currentUserIP){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This checks wether the set user ip for the session is the same one as what is currently being provied
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean.
     */
    static public function checkCookie(){
        $userSetCookie = self::getSetCookie();
        $currentUserCookie = self::getProviedCookie();
        
        if($userSetCookie == $currentUserCookie){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This checks wheather the user is behind a proxy
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean
     */
    static public function checkProxy(){
        if (@$_SERVER['HTTP_X_FORWARDED_FOR']|| @$_SERVER['HTTP_X_FORWARDED']|| @$_SERVER['HTTP_FORWARDED_FOR']|| @$_SERVER['HTTP_CLIENT_IP']|| @$_SERVER['HTTP_VIA']|| @in_array($_SERVER['REMOTE_PORT'], array(8080,80,6588,8000,3128,553,554))|| @fsockopen($_SERVER['REMOTE_ADDR'], 80, $errno, $errstr, 1)){
              return true;
        }else{
            return false;
        }
    }

    /**
     * Check if session secuirty enabled
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean
     */
    static public function checkSessionSecurityEnabled(){
        if(self::getSessionSecurityEnabled()){
            return true;
        }else{
            return false;
        }
    }
    
    /*****Below is the heart of the class*****/
    /**
     * This checks wheather the session has been stolen or not
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return boolean
     */
    static public function antiSessionHijacking(){
        $checkIP = self::checkIP();
        $checkUserAgent = self::checkAgent();
        
        if(($checkIP == true) && ($checkUserAgent == true)){
            if(isset($_GET['module'])){
                $checkUserCookie = self::checkCookie();
                if($checkUserCookie == true){
                    return true;
                }else{
                    self::destroyCurrentSession();
                    return false;
                }
            }else{
                return true;
            }
        }else{
            if(self::checkSessionSecurityEnabled() == false){
                //proxies can cause fluxuations in the user agent and IP headers so user can disable it on login
                if(isset($_GET['module'])){
                    $checkUserCookie = self::checkCookie();
                    if($checkUserCookie == true){
                        return true;
                    }else{
                        self::destroyCurrentSession();
                        return false;
                    }
                }else{
                    return true;
                }
            }else{
                self::destroyCurrentSession();
                return false;
            }
        }
    }
}
?>
