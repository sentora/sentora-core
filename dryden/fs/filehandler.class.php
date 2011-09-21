<?php

class fs_filehandler {

    /**
     * Clear all text in a file.
     * @author RusTus (rustus@zpanel.co.uk) 
     * @version 10.0.0
     */
    static function ResetFile($file) {
        $new_file = "";
        if (is_dir($file)) {
            $fp = fopen($file, 'w');
            fwrite($fp, $new_file);
            fclose($fp);
        }
    }

    /**
     * Create blank or populated with permissions.
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
            runtime_hook::Execute('OnBeforeFileCreate');
            $fp = fopen($path, 'w');
            fwrite($fp, $string);
            fclose($fp);
            chmod($path, $chmod);
            runtime_hook::Execute('OnAfterFileCreate');
        }
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
     * @param string $file
     * @return type 
     */
    static function ReadFileContents($file) {
        return file_get_contents($file);
    }

}

?>
