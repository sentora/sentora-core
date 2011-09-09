<?php

class fs_filehandler {

    /**
    * Create blank or preformatted file with permissions.
    * @author Bobby Allen (ballen@zpanel.co.uk) 
    * @version 10.0.0
	*/
	static function ResetFile($value) {
		$new_file = "";
		if (is_dir($value)){
			$fp = fopen($value,'w');
			fwrite($fp, $new_file);
			fclose($fp);
		}
	}
	
    /**
    * Create proper line ending based on server version.
    * @author Bobby Allen (ballen@zpanel.co.uk) 
    * @version 10.0.0
	*/	
	static function NewLine() {
		if (sys_versions::ShowOSPlatformVersion() == "Windows"){
			$retval = "\r\n";
		}else{
			$retval = "\n";
		}
	return $retval;
	}
        
        /**
         * Returns the contents of a file in a string.
         * @param string $file
         * @return type 
         */
        static function ReadFileContents($file){
            return file_get_contents($file);
        }
	
}

?>
