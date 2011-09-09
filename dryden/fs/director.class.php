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
            @mkdir($path, 0777);
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
                SureRemoveDir($path . '/' . $obj, true);
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
            @chmod($path, $mode);
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
	
}

?>
