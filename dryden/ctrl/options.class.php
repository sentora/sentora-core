<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * Options class communicates with the ZPanel database and can read and write system options.
 * @package zpanelx
 * @subpackage dryden -> controller
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ctrl_options {

    /**
     * The main 'getter' class used to retrieve the value from the system options table.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param string $name The name of the system option (eg. sentora_root)
     * @return string The system option value.
     */
    static function GetSystemOption($name) {
        global $zdbh;
        $sqlString = "SELECT so_value_tx FROM x_settings WHERE so_name_vc = :name";
        $bindArray = array(':name' => $name);
        $zdbh->bindQuery($sqlString, $bindArray);
        $result = $zdbh->returnRow();
        if ($result) {
            return $result['so_value_tx'];
        } else {
            return false;
        }
    }

    /**
     * An alias of 'GetSystemOption' to 'fix' the recent refactor of the sentora code, this was affecting third-party modules which relied on this old function name.
     * @author Bobby Allen <ballen@bobbyallen.me>
     * @param string $name The name of the system option.
     * @return string The system option value.
     * @deprecated since version 10.0.1
     */
    static function GetOption($name) {
        return self::GetSystemOption($name);
    }

    /**
     * The main 'setter' class used to write/update system options.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param string $name The name of the system option (eg. Sentora_root)
     * @param string $value The value to set.
     * @param bool $create Instead of update the system option, create it instead?
     * @return bool
     */
    static function SetSystemOption($name, $value, $create = false) {
        global $zdbh;
        if ($create == false) {
            $bindArray = array(
                ':name' => $name,
                ':value' => $value
            );
            if ($zdbh->bindQuery("UPDATE x_settings SET so_value_tx = :value WHERE so_name_vc = :name", $bindArray)) {
                return true;
            } else {
                return false;
            }
        } else {
            $bindArray = array(
                ':name' => $name,
                ':value' => $value
            );
            if ($zdbh->bindQuery("INSERT INTO x_settings (so_name_vc, so_value_tx) VALUES (:name, :value)", $bindArray)) {
                return true;
            } else {
                return false;
            }
        }
        runtime_hook::Execute('OnSetSystemOption');
    }

    /**
     * Gets user account information.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $id The user account ID. 
     * @return mixed If the user exists it will return an array containing the account details for the user otherwise if the user doesn't exist will return 'false'.
     */
    static function GetUserInfo($id) {
        global $zdbh;
        $bindArray = array(
            ':id' => $id,
        );
        $sqlStatment = $zdbh->bindQuery("SELECT * FROM x_accounts WHERE ac_id_pk = :id", $bindArray);
        $results = $zdbh->returnRow();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Gets user package information.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @global db_driver $zdbh The ZPX database handle.
     * @param int $id The user account ID.
     * @return mixed If the user and package details exist it will return an array containing the user's package details otherwise will return 'false'. 
     */
    static function GetPackageInfo($id) {
        global $zdbh;

        $bindArray = array(
            ':id' => $id,
        );
        $sqlStatment = $zdbh->bindQuery("SELECT * FROM x_accounts WHERE ac_id_pk = :id", $bindArray);
        $results = $zdbh->returnRow();

        if ($result) {
            $packageid = $result['ac_id_pk'];
            $result = $zdbh->query("SELECT * FROM x_packages WHERE pk_id_pk = '$packageid'")->Fetch();
            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Checks for predefined avaliable options to choose from.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $dval The list of predefined values (seperated with a '|' pipe charater!)
     * @return boolean 
     */
    public static function CheckForPredefinedOptions($dval) {
        if ($dval == "")
            return false;
        return true;
    }

    /**
     * Dynamically builds a standard text field and will populate with a value if one is set.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $name The name to use for the <input> tag.
     * @param string $cval Current value of the field.
     * @return string The HTML code for the generated text field.
     */
    public static function OutputSettingTextField($name, $cval = "") {
        if ($cval == "")
            return "<input type=\"text\" name=\"" . $name . "\" id=\"" . $name . "\">\n";
        return "<input type=\"text\" name=\"" . $name . "\" id=\"" . $name . "\" value=\"" . $cval . "\">\n";
    }

    /**
     * Dynamicaly builds a single line text area and will populate with a value if one is set.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $name The name to use for the <textarea> tag.
     * @param string $cval Current value of the field.
     * @return string The HTML code for the generated textarea field.
     */
    public static function OutputSettingTextArea($name, $cval = "") {
        return "<textarea cols=\"30\" rows=\"1\" name=\"" . $name . "\">" . $cval . "</textarea>";
    }

    /**
     * Dynamically builds a drop-down menu of avaliable options based on predfined list of values (seperated with a '|' pipe character!)
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $name The name to use for the <select> tag.
     * @param string $dval The list of predefined values (seperated with a '|' pipe charater!) 
     * @param string $cval Current value of the field.
     * @return string The HTML code for the generated drop-down menu.
     */
    public static function OuputSettingMenuField($name, $dval, $cval = "") {
        $values = explode("|", $dval);
        $field = "<select name=\"" . $name . "\" id=\"" . $name . "\">\n";
        foreach ($values as $option) {
            if ($cval != $option) {
                $field .= "\t<option value=\"" . $option . "\">" . $option . "</option>\n";
            } else {
                $field .= "\t<option value=\"" . $option . "\" selected=\"selected\">" . $option . "</option>\n";
            }
        }
        $field .= "</select>\n";
        return $field;
    }

}

?>
