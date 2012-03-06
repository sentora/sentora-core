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

    private function cleanexpmessage($exception) {
        $res = strstr($exception, "]: ", false);
        $res = str_replace(']: ', '', $res);
        $res = strstr($res, 'Stack', true);
        $stack = strstr($exception, 'Stack trace:', false);
        $stack = strstr($stack, '}', true);
        $stack = str_replace("Stack trace:", "", $stack);
        return $res . $stack . "}";
    }

    public function query($query) {
        try {
            $result = parent::query($query);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'query' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            die($error_html);
        }
    }

    function exec($query) {
        try {
            $result = parent::exec($query);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'exec' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            die($error_html);
        }
    }

    function prepare($query, $driver_options = array()) {
        try {
            $result = parent::prepare($query, $driver_options);
            return($result);
        } catch (PDOException $e) {
            $errormessage = $this->errorInfo();
            $clean = $this->cleanexpmessage($e);
            $error_html = $this->css . "<div class=\"dbwarning\"><strong>Critical Error:</strong> [0144] - ZPanel database 'prepare' error (" . $this->errorCode() . ").<p>A database query has caused an error, the details of which can be found below.</p><p><strong>Error message:</strong></p><pre> " . $errormessage[2] . "</pre><p><strong>SQL Executed:</strong></p><pre>" . $query . "</pre><p><strong>Stack trace: </strong></p><pre>" . $clean . "</pre></div>";
            die($error_html);
        }
    }

}

?>