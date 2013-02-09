<?php

/**
 * A class to manage common file manipulation operations.
 * @package zpanelx
 * @subpackage dryden -> filesystem
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class fs_filehandler {

    /**
     * Clear all text in a file.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @param string $file The full path to the file.
     */
    static function ResetFile($file) {
        $new_file = "";
        if (!is_dir($file)) {
            $fp = @fopen($file, 'w');
            @fwrite($fp, $new_file);
            @fclose($fp);
        }
    }

    /**
     * Copies without overwritting an existing file, adding permissions for Linux.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @param string $src The full path to the source file (the file to copy)
     * @param string $desc The full path to the new file (where to copy the file too) 
     */
    static function CopyFileSafe($src, $dest) {
        if (!file_exists($dest)) {
            @copy($src, $dest);
            if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
                fs_director::SetFileSystemPermissions($dest, 0777);
            }
        }
    }

    /**
     * Copies and overwrites existing file, adding permissions for Linux.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @param string $src The full path to the source file (the file to copy)
     * @param string dest The full path to the new file (where to copy the file too) 
     */
    static function CopyFile($src, $dest) {
        @copy($src, $dest);
        if (sys_versions::ShowOSPlatformVersion() <> "Windows") {
            fs_director::SetFileSystemPermissions($dest, 0777);
        }
    }
	
	/**
	 * Copies a Directory's contents including all of its subfolders and files
	 * @author VJ Patel (meetthevj@gmail.com - VJftw @ ZPanel Forums)
	 * @param string $src The full path to the source directory (the directory who's contents to copy)
	 * @param string $dest The full path to the destination directory (the directory to put the subfolders and files in)
	 */
	 static function CopyDirectoryContents($src, $dest) { 
		if(is_dir($src)) {
			$dir_handle=opendir($src);
			while($file=readdir($dir_handle)){
				if($file!="." && $file!=".."){
					if(is_dir(fs_director::ConvertSlashes($src."/".$file))){
						mkdir(fs_director::ConvertSlashes($dest."/".$file));
						fs_filehandler::CopyDirectoryContents(fs_director::ConvertSlashes($src."/".$file), fs_director::ConvertSlashes($dest."/".$file));
					} else {
						copy(fs_director::ConvertSlashes($src."/".$file), fs_director::ConvertSlashes($dest."/".$file));
					}
				}
			}
			closedir($dir_handle);
		} else {
			copy($src, $dest);
		}
	 }
	 
	/**
	 * Removes a Directory's contents (without removing the directory itself)
	 * @author VJ Patel (meetthevj@gmail.com - VJftw @ZPanel Forums)
	 * @param string $dir The full path to the directory who's contents to remove
	*/
	static function RemoveDirectoryContents($dir) {
		$dir = fs_director::ConvertSlashes($dir);
		$files = dir($dir);
		while ($file = $files->read()) {
			if ($file != '.' && $file != '..') {			
				if (is_dir($dir.$file)) {
					fs_filehandler::RemoveDirectoryContents(fs_director::ConvertSlashes($dir.$file.'/'));
					rmdir($dir.$file);
				} else
				unlink($dir.$file);
			}
		}
		$files->close();
	}

    /**
     * Create blank or populated file with permissions, including the path.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @param string $path Path and filename to the file to create.
     * @param string $chmod Permissions mode to use for the new file. (eg. 0777)
     * @param string $string The contents to write into the new file.
     */
    static function CreateFile($path, $chmod = 0777, $string = "") {
        if (!is_file($path)) {
            preg_match('`^(.+)/([a-zA-Z0-9]+\.[a-z]+)$`i', $path, $matches);
            $directory = $matches[1];
            $file = $matches[2];
            if (!is_dir($directory)) {
                if (!mkdir($directory, $chmod, 1)) {
                    return FALSE;
                }
            }
            $fp = @fopen($path, 'w');
            @fwrite($fp, $string);
            @fclose($fp);
            fs_director::SetFileSystemPermissions($dest, $chmod);
        }
    }

    /**
     * Creates the correct line ending based on the server OS platform.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @return string The correct line ending charater.
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
     * @param string $file The path and file name to the file.
     * @return string The contents of the file. 
     */
    static function ReadFileContents($file) {
        $retval = @file_get_contents($file);
        if ($retval) {
            return $retval;
        } else {
            return false;
        }
    }

    /**
     * Updates an existing file and will chmod it too if required.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $path The path and file name to the file to update.
     * @param string $chmod Permissions mode to use for the new file. (eg. 0777)
     * @param string $sting The content to update the file with.
     * @return boolean 
     */
    static function UpdateFile($path, $chmod = 0777, $string = "") {
        if (!file_exists($path))
            fs_filehandler::ResetFile($path);
        $fp = @fopen($path, 'w');
        @fwrite($fp, $string);
        @fclose($fp);
        fs_director::SetFileSystemPermissions($path, $chmod);
        return true;
    }

    /**
     * This adds text data into a specified file. This can be before the start or at the end of the file.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $file The system path to the file.
     * @param string $content The content to add to the file.
     * @param int $pos Where to add the text. (0 = At the start, 1 = At the end of the file)
     */
    static function AddTextToFile($file, $content, $pos) {
        $current_version = @fs_filehandler::ReadFileContents($file);
        if ($pos == 0) {
            $new_version = $content . fs_filehandler::NewLine() . $current_version;
        } else {
            $new_version = $current_version . fs_filehandler::NewLine() . $content;
        }
        fs_filehandler::UpdateFile($file, 0777, $new_version);
    }

}

?>
