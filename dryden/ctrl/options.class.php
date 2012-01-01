<?php

/**
 * Options class communicates with the ZPanel database and can read and write system options.
 *
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */
class ctrl_options {

    /**
     * The main 'getter' class used to retrieve the value from the system options table.
     * 
     * @global array $zdbh
     * @param string $name
     * @return array 
     */
    static function GetOption($name) {
        global $zdbh;
        $result = $zdbh->query("SELECT so_value_tx FROM x_settings WHERE so_name_vc = '$name'")->Fetch();
        if ($result) {
            return $result['so_value_tx'];
        } else {
            return false;
        }
    }

    /**
     * The main 'setter' class used to write/update system options.
     * 
     * @global array $zdbh
     * @param string $name
     * @param string $value
     * @return int (0 = record updated, 1 = record inserted)
     */
    static function SetSystemOption($name, $value, $create = false) {
        global $zdbh;
        if ($create == false) {
            if ($zdbh->exec("UPDATE x_settings SET so_value_tx = '$value' WHERE so_name_vc = '$name'")) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($zdbh->exec("INSERT INTO x_settings (so_name_vc, so_value_tx) VALUES ('$name', '$value')")) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Gets user account information.
     * 
     * @global array $zdbh
     * @param string $name
     * @return array 
     */
    static function GetUserInfo($id) {
        global $zdbh;
        $result = $zdbh->query("SELECT * FROM x_accounts WHERE ac_id_pk = '$id'")->Fetch();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Gets user package information.
     * 
     * @global array $zdbh
     * @param string $name
     * @return array 
     */
    static function GetPackageInfo($id) {
        global $zdbh;
        $result = $zdbh->query("SELECT * FROM x_accounts WHERE ac_id_pk = '$id'")->Fetch();
        if ($result) {
            $packageid = $result['ac_id_pk']; //change this to package id
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

}

?>
