<?php

/**
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * A class to manage common file system operations.
 * @package zpanelx
 * @subpackage dryden -> filesystem
 * @version 1.0.0
 * @author Bobby Allen (ballen@bobbyallen.me)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class fs_director {

    /**
     * Corrects standard UNIX/PHP file slashes '/' to Windows slashes '\'.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $string The string to that of which to convert the slashes in.
     * @return string 
     */
    static function SlashesToWin($string) {
        return str_replace("/", "\\", $string);
    }

    /**
     * Converts Windows slashes '\' to UNIX/PHP path slashes '/'.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $string The string to that of which to convert the slashes in.
     * @return string
     */
    static function SlashesToNIX($string) {
        return str_replace("\\", "/", $string);
    }

    /**
     * Converts to proper slashes based on OS platform.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $string The string to that of which to convert the slashes in.
     * @return string
     */
    static function ConvertSlashes($string) {
        if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
            $retval = self::SlashesToNIX($string);
        } else {
            $retval = self::SlashesToWin($string);
        }
        return $retval;
    }

    /**
     * Remove double slashes.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $string The string to that of which to convert the slashes in.
     * @return string
     */
    static function RemoveDoubleSlash($var) {
        $retval = str_replace("\\\\", "\\", $var);
        return $retval;
    }

    /**
     * Takes a raw file size value (bytes) and converts it to human readable size with the correct abbreavation.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param int $size Number of bytes to convert to human readable format.
     * @return string Human readable version eg. 250 MB
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
     * Creates a directory if it doesn't already exist!
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $path The full path of the folder to create.
     * @return boolean
     */
    static function CreateDirectory($path) {
        if (!file_exists($path)) {
            runtime_hook::Execute('OnBeforeDirectoryCreate');
            @mkdir($path, 0777, true);
            fs_director::SetFileSystemPermissions($path, 0777);
            runtime_hook::Execute('OnAfterDirectoryCreate');
            $retval = true;
        } else {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Removes (Deletes) a directory and all folders/file within it.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $path The full path of the folder to delete.
     * @return boolean
     */
    static function RemoveDirectory($path) {
        if (!$dh = @opendir($path))
            return false;
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..')
                continue;
            if (!@unlink($path . '/' . $obj))
                runtime_hook::Execute('OnBeforeDirectoryDelete');
            self::RemoveDirectory($path . '/' . $obj, true);
            runtime_hook::Execute('OnAfterDirectoryDelete');
        }
        closedir($dh);
        @rmdir($path);
        return true;
    }

    /**
     * Sets file/directory permissions on a given path.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $path The full path of the file or folder on which to set the permissions on.
     * @param int $mode The UNIX permissions octal (eg. 0777 or 777)
     * @return boolean 
     */
    static function SetFileSystemPermissions($path, $mode) {
        if (file_exists($path)) {
            runtime_hook::Execute('OnBeforeSetFileSystemPerms');
            @chmod($path, $mode);
            runtime_hook::Execute('OnAfterSetFileSystemPerms');
            $retval = true;
        } else {
            $retval = false;
        }
        return $retval;
    }

    /**
     * Checks and converts a given value if the value is of a certain state. (designed to be used with HTML checkboxes).
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $value The value to check on which is null.
     * @param string $true If the result is 'true' return this.
     * @param string $false If the result is 'false' return this.
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
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $value The value of which to check if its empty.
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
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $value The value of which to check.
     * @return int Returns 1 if the checkbox is ticked and 0 if the text box is unticked.
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
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $value The value of which to check.
     * @return string  Returns 'CHECKED' if the checkbox is ticked and 'NULL' if the text box is unticked.
     */
    static function IsChecked($value) {
        if ($value == 1) {
            $retval = "CHECKED";
        } else {
            $retval = NULL;
        }
        return $retval;
    }

    /**
     * Returns a random password.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param int $length The total number of characters the password should be.
     * @param int $strength The strengh of the password generated. 
     * @return string The newly generated password. 
     */
    static function GenerateRandomPassword($length = 9, $strength = 0) {
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

    /**
     * Checks that an email address is of a valid format.
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $email The email address of which to check.
     * @return boolean 
     */
    static function IsValidEmail($email) {
        if (!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $email))
            return false;
        return true;
    }

    /**
     * Checks that a domain name is of a valid format.
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $domainname The domain name of which to check.
     * @return boolean 
     */
    static function IsValidDomainName($domainname) {
        if (stristr($domainname, '.')) {
            $part = explode(".", $domainname);
            foreach ($part as $check) {
                if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $check) || preg_match('/-$/', $check)) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * Checks that a user name is of a valid format.
     * @author Bobby Allen (ballen@bobbyallen.me) 
     * @param string $username The user name of which to check.
     * @return boolean 
     */
    static function IsValidUserName($username) {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $username) || preg_match('/-$/', $username))
            return false;
        return true;
    }

    /**
     * Checks if a file exists or not.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $path The path to the file of which to check.
     * @return boolean 
     */
    static function CheckFileExists($path) {
        if (file_exists($path)) {
            if (is_file($path))
                return true;
            return false;
        }
        return false;
    }

    /**
     * Checks that a folder exists or not.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $path The path to the folder of which to check.
     * @return boolean 
     */
    static function CheckFolderExists($path) {
        if (file_exists($path)) {
            if (is_dir($path))
                return true;
            return false;
        }
    }

    /**
     * Returns the file extentsion of a file.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $filename The full path to the file.
     * @return string The file extentsion (eg. .jpg) 
     */
    static function GetFileExtension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * Returns the file name of a file minus the file extention.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $filename The full path to the file.
     * @return string The file name.
     */
    static function GetFileNameNoExtentsion($filename) {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Returns the full size of a directory and all child objects.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $directory The filesystem path to the directory
     * @return int The directory size in bytes.
     */
    static function GetDirectorySize($directory) {
        $size = 0;
        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }
        if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory)) {
            return -1;
        }
        $handle = opendir($directory);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                $path = $directory . '/' . $file;
                if ($file != '.' && $file != '..') {
                    if (is_file($path)) {
                        $size += filesize($path);
                    } elseif (is_dir($path)) {
                        $handlesize = self::GetDirectorySize($path);
                        if ($handlesize >= 0) {
                            $size += $handlesize;
                        } else {
                            return -1;
                        }
                    }
                }
            }
            closedir($handle);
            return $size;
        } else {
            return false;
        }
    }

    /**
     * Renames a file or a folder.
     * @author Bobby Allen (ballen@bobbyallen.me)
     * @param string $source The full filesystem path of the file to rename.
     * @param string $target The full filesystem path of the new file (name).
     * @return boolean If the rename was successful or not. 
     */
    static function RenameFileFolder($source, $target) {
        if (rename($source, $target))
            return true;
        return false;
    }

}

?>
