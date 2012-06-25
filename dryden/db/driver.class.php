<?php

/**
 * Database access class, enables PDO database access.
 * @package zpanelx
 * @subpackage dryden -> db
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class db_driver extends PDO {

    /**
     * 
     * @param String $dsn
     * @param String $username
     * @param String $password
     * @param $driver_options [optional]
     */
    public function __construct($dsn, $username = null, $password = null, $driver_options = null) {
        parent::__construct($dsn, $username, $password, $driver_options);
    }

    var $css = "<style type=\"text/css\"><!--
            .dbwarning {
                    font-family: Verdana, Geneva, sans-serif;
                    font-size: 14px;
                    color: #C00;
                    background-color: #FCC;
                    padding: 30px;
                    border: 1px solid #C00;
            }
            p {
                    font-size: 12px;
                    color: #000;
                    white-space: pre-wrap;
            }
            pre {
                color: #666;
            }
            </style>";

    /**
     * 
     * @param String $exception
     * @return String
     */
    
    private function cleanexpmessage($exception) {
        $res = strstr($exception, "]: ", false);
        $res1 = str_replace(']: ', '', $res);
        $res2 = strstr($res1, 'Stack', true);
        $stack = strstr($exception, 'Stack trace:', false);
        $stack1 = strstr($stack, '}', true);
        $stack2 = str_replace("Stack trace:", "", $stack1);
        return $res2 . $stack2 . "}";
    }
    
    /**
     * 
     * @param String $query
     * @return type
     */

    public function query($query) {
        try {
            $result = parent::query($query);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            if (!runtime_controller::IsCLI()) {
                $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'query' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            } else {
                $error_html = "SQL Error: " . $errormessage[2] . "\n";
            }
            die($error_html);
        }
    }
    
    /**
     * 
     * @param String $query
     * @return type
     */

    function exec($query) {
        try {
            $result = parent::exec($query);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            if (!runtime_controller::IsCLI()) {
                $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'exec' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            } else {
                $error_html = "SQL Error: " . $errormessage[2] . "\n";
            }
            die($error_html);
        }
    }
    
    /**
     * 
     * @param String $query
     * @param Array $driver_options
     * @return type
     */

    function prepare($query, $driver_options = array()) {
        try {
            $result = parent::prepare($query, $driver_options);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            if (!runtime_controller::IsCLI()) {
                $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'prepare' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            } else {
                $error_html = "SQL Error: " . $errormessage[2] . "\n";
            }
            die($error_html);
        }
    }
    
    /**
     * The main query function using bind variables for SQL injection protection.
     * Returns an array of results.
     * @author Kevin Andrews (kandrews@zpanelcp.com)
     * @param String $sqlString
     * @param Array $bindArray
     * @param Array $driver_options [optional]
     * @return Array
     */
    function bindQuery( $sqlString, $bindArray, $driver_options = array() ) {
        
        $safeQuery = $this->prepare( $sqlString , $driver_options );
        
        foreach ( $bindArray as $bindKey => &$bindValue ) {
            $safeQuery->bindParam($bindKey, $bindValue);
        }
        
        $safeQuery->execute();
        $resultRows = $safeQuery->fetchAll();
        
        return $resultRows;
    }
}

?>