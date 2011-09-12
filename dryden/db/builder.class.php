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


    /**
     * Builds database from XML
     * @author RusTus (rustus@zpanelcp.com)
     * @version 10.0.0
     */
	static function moduledb_commit() {
		global $zdbh;
    	$mod_db_dir = ctrl_options::GetOption('zpanel_root')."modules/*/{dbs.xml}";  
    	try
		{
			foreach(glob($mod_db_dir, GLOB_BRACE) as $mod_db_file){
			
				$db_config = new xml_reader(fs_filehandler::ReadFileContents($mod_db_file));
				$db_config->Parse();
				
				$database = $db_config->document->database[0]->tagData;
				$sql = $zdbh->prepare("CREATE DATABASE IF NOT EXISTS $database");
				$sql->execute();

				foreach($db_config->document->table_structure as $table){
					$table_name = $table->table_name[0]->tagData;

					#Check if table exists, if not then create it.
					$sql = $zdbh->prepare("SHOW TABLES FROM $database LIKE '$table_name'");
					$sql->execute();
					$table_exists = $sql->fetch();
					if (!$table_exists){
        				$sql = $zdbh->prepare("CREATE TABLE $database.$table_name (create_temp INT)");
						$sql->execute();
					}
					
					#Loop through columnns for selected table
					foreach($table->column as $data){
						$column_name = $data->column_name[0]->tagData;
						$column_structure = $data->column_structure[0]->tagData;
						$sql = $zdbh->prepare("SHOW COLUMNS FROM $database.$table_name LIKE '$column_name'");
						$sql->execute();
						$column_exists = $sql->fetch();
						if (!$column_exists){
							$sql = $zdbh->prepare("ALTER TABLE $database.$table_name ADD $column_name $column_structure");
							$sql->execute();
						}
					}					
		
					#Populate tables with data from xml
					$sql = $zdbh->prepare("SELECT COUNT(*) FROM $database.$table_name");
					$sql->execute();
					$empty = $sql->fetch();
					if ($empty[0] == 0){
						#Loop through inserts/updates for selected table
						foreach($table->data as $data){
							if (!empty($data->insert[0])){
								$insert = $data->insert[0]->tagData;
								$sql = $zdbh->prepare("INSERT INTO $database.$table_name $insert");
								$sql->execute();
							}
							if (!empty($data->update[0])){
								$update = $data->update[0]->tagData;
								$sql = $zdbh->prepare("UPDATE $database.$table_name SET $update");
								$sql->execute();		
							}
						}				
					}
					
					#Remove temp table if created.
					$sql = $zdbh->prepare("SHOW COLUMNS FROM $database.$table_name LIKE 'create_temp'");
					$sql->execute();
					$table_exists = $sql->fetch();
					if ($table_exists){
        				$sql = $zdbh->prepare("ALTER TABLE $database.$table_name DROP create_temp");
						$sql->execute();
					}
				}
			}
		}
		catch (Exception $e){
		// I know this will change, just testing for now
		echo ui_sysmessage::shout($e, 'zdebug', 'zdebug');
		}
	}

    /**
     * Drops database if not zpanel core
     * @author RusTus (rustus@zpanelcp.com)
     * @version 10.0.0
     */
	static function moduledb_drop($database) {
		global $zdbh;
		if ($database != 'zpanelx'){ //Change to systemoption
			$sql = $zdbh->prepare("DROP DATABASE IF EXISTS $database");
			$sql->execute();
		}	
	}
	
}

?>