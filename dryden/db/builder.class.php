<?php

/**
 * Database builder class build database schema based on XML files.
 *
 * @package zpanelx
 * @subpackage dryden -> db
 * @version 1.0.0
 * @author ballen (ballen@zpanelcp.com)
 */

class db_builder {

	function moduledb_commit() {
    	$mod_db_dir = ctrl_options::GetOption('zpanel_root')."modules/*/*/{dbs.xml}";  
    	try
		{
			foreach(glob($mod_db_dir, GLOB_BRACE) as $mod_db_file){
			echo "Filename: " . $mod_db_file . "<br />"; 
			}
		}
		catch (Exception $e){
		// handle error
		}
	}
 
	
}

?>