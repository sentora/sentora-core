<?php

/**
 * Logger class logs infomation passed to it and can record and report debug infomation
 * in a number of ways.
 *
 * @package zpanelx
 * @subpackage dryden -> debug
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */
class debug_logger {

    var $method;
    var $mextra;
    var $detail;
    var $logcode;

    function __construct() {

        /**
         * @var string $method How the debug log will be reported options html, text, email, database,file
         */
        $this->method = "text"; // html, text, email, datatabe, file.
        $this->mextra = null;
        $this->detail = null;
        $this->logcode = 0;
    }

    /**
     * Writes an error log/debug entry into the configued logging medium.
     * @return boolean 
     */
    function writeLog() {
        global $zdbh;
        if ($this->method == "screen") {
            /**
             *  Output is to the screen, generate a nice error message.
             *  @todo Add HTML file compatible box for displaying a raw system error message.
             */
        } elseif ($this->method == "file") {
            fs_filehandler::AddTextToFile(ctrl_options::GetOption('logfile'), date('c'). ' - ' .$this->logcode. ' - '.$this->detail, 1);
        } elseif ($this->method == "email") {
            /**
             * Use this option to email a debug log to an email account!
             * @todo Link with the email class once it has been developed.
             */
        } elseif ($this->method == "database") {
            $zdbh = new db_driver("mysql");
                $statment = "INSERT INTO x_logs (lg_user_fk, lg_code_vc, lg_module_vc, lg_detail_tx, lg_stack_tx, lg_when_ts) VALUES (0, '" .$this->logcode. "', 'no yet supported', '" .$this->detail. "', '" .$this->mextra. "','" .time(). "')";
                if ($zdbh->exec($statement) > 0) {
                    $retval = true;
                } else {
                    $retval = false;
                }
            try {
                $zdbh = new db_driver("mysql");
                $statment = "INSERT INTO x_logs (lg_user_fk, lg_code_vc, lg_module_vc, lg_detail_tx, lg_stack_tx, lg_when_ts) VALUES (0, '" .$this->logcode. "', 'no yet supported', '" .$this->detail. "', '" .$this->mextra. "','" .time(). "')";
                if ($zdbh->exec($statement) > 0) {
                    $retval = true;
                } else {
                    $retval = false;
                }
            } catch (Exception $e) {
                /**
                 *  Error accessing database, need to plain write out to the screen as database logging cannot be completed!
                */
                $temp_log_obj->method = "text";
                $temp_log_obj->logcode = "012";
                $temp_log_obj->detail = "Unable to log infomation to the required place (in the database)";
                $temp_log_obj->mextra = $e;
                $temp_log_obj->writeLog();
            }
            return true;
        } else {
            echo $this->logcode . " - " . $this->detail . " - " .$this->mextra;
        }
        return;
    }

    /**
     * Resets debug infomation - be careful to not use before writeLog() as it will clear the log!
     * @todo Maybe  handle this differently?
     * @return type 
     */
    function reset() {
        $this->mextra = null;
        $this->detail = null;
        $this->logcode = 0;
        return;
    }

    /**
     * Checks and returns true if there is infomation in the object to be reported on (If some debug/error message is pending).
     * @return boolean 
     */
    function hasInfo() {
        if ($this->detail != null)
            return true;
        return false;
    }

}

?>
