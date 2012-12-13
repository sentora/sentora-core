<?php
/* THIS CLASS IS STILL IN DEVELOPMENT AND IS UNTESTED WILL BE FININISHED OFF BY THE 15TH DECEMBER 2012*/


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
    
    // on login 
        //set 
            //ip
            //user agant
   
    /*****The below are generic function used more than once*****/
    public function sessionRegen(){
         session_regenerate_id();
    }
    
    /**
     * Get users ip address 
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The Clean IP.
     */
    public function findIP() {
        $ip = $_SERVER["REMOTE_ADDR"];
        return $ip;
    }
    /**
     * Get users details that are spefic for the individual user only
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function userSpeficData(){
        $ip = $_SESSION['ip'];
        $username = $_SESSION['zUser'];
        return $ip.$username;
    }
    
    
    /*****Here we are are gathering infomration and storing securty*****/
    /**
     * This function will set the users agent in a secure session and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function setUserAgent(){
        
        if($_SESSION['HTTP_USER_AGENT'] = sha1($_SERVER['HTTP_USER_AGENT'] . $this->userSpeficData())){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This function will set the users IP in a secure session and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function setUserIP(){
        
        if($_SESSION['ip'] = sha1($this->findIP() . $this->userSpeficData())){
            return true;
        }else{
            return false;
        }
    }
    
    /*****The below is returning the secure information*****/
    /**
     * This will return the secure session set version of the users agent
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function getSetUserAgent(){
        return $_SESSION['HTTP_USER_AGENT'];
    }
    
    /**
     * This will return the secure session set version of the users ip
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function getSetIP(){
        return $_SESSION['ip'];
    }
    
    
    /*****The below is retrieveing the current provided information*****/
    /**
     * This returns the current provied users agent via headers  and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function getProviedUserAgent(){
        return sha1($_SERVER['HTTP_USER_AGENT'] . $this->userSpeficData());
    }
    
    /**
     * This returns the current provied users IP and hashes
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function getProviedIP(){
        return sha1($this->findIP() . $this->userSpeficData());
    }
    
    
    
    /*****Below are function that check information and try to identy any tampering*****/
    /**
     * This checks wether the set user agent for the session is the same one as what is currently being provied
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function checkAgent(){
        $userSetAgent = $this->getSetUserAgent();
        $currentUserAgent = $this->getProviedUserAgent();
        
        if($userSetAgent == $currentUserAgent){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This checks wether the set user ip for the session is the same one as what is currently being provied
     * @author Sam Mottley (smottley@zpanelcp.com)
     * @return string The data.
     */
    public function checkIP(){
        $userSetIP = $this->getSetIP();
        $currentUserIP = $this->getProviedIP();
        
        if($userSetIP == $currentUserIP){
            return true;
        }else{
            return false;
        }
    }
    
    /*****Below is the heart of the class*****/
    public function antiSessionHijacking(){
        $checkIP = $this->checkIP();
        $checkUserAgent = $this->checkAgent();
        if(($checkIP == true) && ($checkUserAgent == true)){
            //Regenerate session id the more it changes the less likly to get hacked!
            $this->sessionRegen();
            return true;
        }else{
            $_SESSION['zpuid'] = null;
            session_destroy();
            return false;
        }
    }
}
?>
