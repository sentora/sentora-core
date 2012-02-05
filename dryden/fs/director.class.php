<?php

class fs_director {

    /**
     * Corrects standard UNIX/PHP file slashes '/' to Windows slashes '\'.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $string
     * @return string 
     */
    static function SlashesToWin($string) {
        return str_replace("/", "\\", $string);
    }

    /**
     * Converts Windows slashes '\' to UNIX/PHP path slashes '/'.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $string
     * @return string
     */
    static function SlashesToNIX($string) {
        return str_replace("\\", "/", $string);
    }
	
    /**
     * Converts to proper slashes based on OS platform.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $string
     * @return string
     */
    static function ConvertSlashes($string) {
	    if (sys_versions::ShowOSPlatformVersion() <> "Windows"){
        	$retval = self::SlashesToNIX($string);
    	} else {
			$retval = self::SlashesToWin($string);
    	}
        return $retval;
    }
	
    /**
     * Remove the last character from a string.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $string
     * @return string
     */
	static function RemoveDoubleSlash($var) {
    	$retval = str_replace("\\\\", "\\", $var);
    	return $retval;
	}

    /**
     * Takes a raw file size value (bytes) and converts it to human readable size with an abbreavation.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param int $size
     * @return string 
     */
    static function ShowHumanFileSize($size) {
        if ($size / 1024000000 > 1) {
            $retval = round($size / 1024000000, 1) . ' GB';
        } elseif ($size / 1024000 > 1) {
            $retval = round($size / 1024000, 1) . ' MB';
        } elseif ($size / 1024 > 1) {
            $retval = round($size / 1024, 1) . ' KB';
        } else {
            $retval = round($size, 1) . ' bytes';
        }
        return $retval;
    }

    /**
     * Creates a directory (if it doesn't already exist!)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param type $path 
     * @return boolean
     */
    static function CreateDirectory($path) {
        if (!file_exists($path)) {
            runtime_hook::Execute('OnBeforeDirectoryCreate');
            @mkdir($path, 0777);
            runtime_hook::Execute('OnAfterDirectoryCreate');
            $retval = true;
        } else {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Removes (Deletes) a directory and all folders/file within it.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $path
     * @return boolean
     */
    static function RemoveDirectory($path) {
        if (!$dh = @opendir($path))
            $retval = false;
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..')
                continue;
            if (!@unlink($path . '/' . $obj))
                runtime_hook::Execute('OnBeforeDirectoryDelete');
                SureRemoveDir($path . '/' . $obj, true);
                runtime_hook::Execute('OnAfterDirectoryDelete');
        }

        closedir($dh);
        @rmdir($path);
        $retval = true;
        return $retval;
    }

    /**
     * Sets file/directory permissions on a given path.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $path
     * @param int $mode
     * @return boolean 
     */
    static function SetDirectoryPermissions($path, $mode) {
        if (file_exists($path)) {
            runtime_hook::Execute('OnBeforeSetDirectoryPerms');
            @chmod($path, $mode);
            runtime_hook::Execute('OnAfterSetDirectoryPerms');
            $retval = true;
        } else {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Checks and converts a given value.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value, string $true, string $false
     * @return boolean 
     */
    static function CheckForNullValue($value, $true, $false) {
        if ($value == 0) {
            return $false;
        } else {
            return $true;
        }
    }

    /**
     * Check for an empty value.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value, string $true, string $false
     * @return boolean 
     */
    static function CheckForEmptyValue($value) {
        if (!empty($value)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks the value of a checkbox and returns if 0 if not ticked or 1 if it is ticked.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value
     * @return boolean 
     */	
	static function GetCheckboxValue($value) {
    	$checkbox_status = $value;
    	if ($checkbox_status == 1) {
       	 $retval = 1;
   	 	} else {
        	$retval = 0;
    	}
    	return $retval;
	}

    /**
     * Checks the value of a checkbox and returns string "CHECKED" if ticked and NULL if not ticked.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value
     * @return boolean 
     */	
	static function IsChecked($value){
 		if ($value == 1){
			$retval = "CHECKED";
		} else {
			$retval =  NULL;
		}
		return $retval;
 	}
	
    /**
     * Returns the current usage of a particular resource.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value
     * @return boolean 
     */	
	static function GetQuotaUsages($resource, $acc_key=0) {
		global $zdbh;
	    if ($resource == 'domains') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=1 AND vh_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'subdomains') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=2 AND vh_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'parkeddomains') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_vhosts WHERE vh_acc_fk=" . $acc_key . " AND vh_type_in=3 AND vh_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'mailboxes') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_mailboxes WHERE mb_acc_fk=" . $acc_key . " AND mb_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'forwarders') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_forwarders WHERE fw_acc_fk=" . $acc_key . " AND fw_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'distlists') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_distlists WHERE dl_acc_fk=" . $acc_key . " AND dl_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'ftpaccounts') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_ftpaccounts WHERE ft_acc_fk=" . $acc_key . " AND ft_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'mysql') {
	        $sql = $zdbh->query("SELECT COUNT(*) AS amount FROM x_mysql_databases WHERE my_acc_fk=" . $acc_key . " AND my_deleted_ts IS NULL");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['amount'];
	    }
	    if ($resource == 'diskspace') {
	        $sql = $zdbh->query("SELECT bd_diskamount_bi FROM x_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['bd_diskamount_bi'];
	    }
	    if ($resource == 'bandwidth') {
	        $sql = $zdbh->query("SELECT bd_transamount_bi FROM x_bandwidth WHERE bd_acc_fk=" . $acc_key . " AND bd_month_in=" . date("Ym", time()) . "");
			$sql->execute();
			$retval = $sql->fetch();
	        $retval = $retval['bd_transamount_bi'];
	    }
    	return $retval;
	}

    /**
     * Returns a random password.
     * @author Bobby Allen (ballen@zpanel.co.uk) 
     * @version 10.0.0
     * @param string $value
     * @return boolean 
     */		
	static function GenerateRandomPassword($a=9, $b=0) {
	    $length = $a;
	    $strength = $b;
	    $vowels = 'aeuy';
	    $consonants = 'bdghjmnpqrstvz';
	    if ($strength & 1) {
	        $consonants .= 'BDGHJLMNPQRSTVWXZ';
	    }
	    if ($strength & 2) {
	        $vowels .= "AEUY";
	    }
	    if ($strength & 4) {
	        $consonants .= '23456789';
	    }
	    if ($strength & 8) {
	        $consonants .= '@#$%';
	    }
	    $fretval = '';
	    $alt = time() % 2;
	    for ($i = 0; $i < $length; $i++) {
	        if ($alt == 1) {
	            $fretval .= $consonants[(rand() % strlen($consonants))];
	            $alt = 0;
	        } else {
	            $fretval .= $vowels[(rand() % strlen($vowels))];
	            $alt = 1;
	        }
	    }
	    return $fretval;
	}

}

?>
