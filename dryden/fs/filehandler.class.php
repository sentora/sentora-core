<?php

class fs_filehandler {

    /**
     * Clear all text in a file.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function ResetFile($file) {
        $new_file = "";
        if (!is_dir($file)) {
            $fp = fopen($file, 'w');
            fwrite($fp, $new_file);
            fclose($fp);
        }
    }

    /**
     * Copies a without overwritting existing file, adding permissions for Linux.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function CopyFileSafe($src, $dest) {
        if (!file_exists($dest)) {
            @copy($src, $dest);
            if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
                @chmod($dest, 0777);
            }
        }
    }

    /**
     * Copies and overwrites existing file, adding permissions for Linux.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function CopyFile($src, $dest) {
        @copy($src, $dest);
        if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
            @chmod($dest, 0777);
        }
    }

    /**
     * Create blank or populated with permissions, including the path.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function CreateFile($path, $chmod="0777", $string="") {
        if (!is_file($path)) {
            preg_match('`^(.+)/([a-zA-Z0-9]+\.[a-z]+)$`i', $path, $matches);
            $directory = $matches[1];
            $file = $matches[2];
            if (!is_dir($directory)) {
                if (!mkdir($directory, $chmod, 1)) {
                    return FALSE;
                }
            }
            $fp = fopen($path, 'w');
            fwrite($fp, $string);
            fclose($fp);
            chmod($path, $chmod);
        }
    }

    /**
     * @todo - THIS NEEDS TO BE REMOVED!! - THIS ALREADY EXISTS IN director.class.php
     */
    static function CreateDirectory($directory) {
        if (!file_exists($directory)) {
            @mkdir($directory, 0777, TRUE);
            if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
                # Lets set some more permissions on it so it can be accessed correctly! (eg. 0777 permissions)
                @chmod($directory, 0777);
            }
        } else {
            # Folder already exist... Just ignore the request!
        }
        return;
    }

    /**
     * Create proper line ending based on server version.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function NewLine() {
        if (sys_versions::ShowOSPlatformVersion() == "Windows") {
            $retval = "\r\n";
        } else {
            $retval = "\n";
        }
        return $retval;
    }

    /**
     * Returns the contents of a file in a string.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $file
     * @return type 
     */
    static function ReadFileContents($file) {
        return file_get_contents($file);
    }

    /**
     * Updates an existing file and will chmod it too if required.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $file
     * @return type 
     */
    static function UpdateFile($path, $chmod="0777", $string="") {
        if (!file_exists($path))
            fs_filehandler::ResetFile($path);
        $fp = fopen($path, 'w');
        fwrite($fp, $string);
        fclose($fp);
        //chmod($path, $chmod);
        return true;
    }

    /**
     * This adds text data into a specified file. This can be before the start or at the end of the file.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @version 10.0.0
     * @param string $file The system path to the file.
     * @param string $content The content to add to the file.
     * @param int $pos Where to add the text. 0 = At the start, 1 = At the end of the file.
     */
    static

    function AddTextToFile($file, $content, $pos) {
        $current_version = @fs_filehandler::ReadFileContents($file);
        if ($pos == 0) {
            $new_version = $content . fs_filehandler::NewLine() . $current_version;
        } else {
            $new_version = $current_version . fs_filehandler::NewLine() . $content;
        }
        fs_filehandler::UpdateFile($file, '0777', $new_version);
    }

}

?>
