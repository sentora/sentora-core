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
		global $zdbh;
    	$mod_db_dir = ctrl_options::GetOption('zpanel_root')."modules/*/*/{dbs.xml}";  
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
					echo $table_name;
					#Check if table exists, if not then create it.
					$sql = $zdbh->prepare("SHOW TABLES FROM $database LIKE '$table_name'");
					$sql->execute();
					$table_exists = $sql->fetch();
					if (!$table_exists){
        				$sql = $zdbh->prepare("CREATE TABLE $database.$table_name (create_temp INT)");
						$sql->execute();
					}
					
	
					//Loop through columnns for selected table
					foreach($table->column as $data){
						$column_name = $data->column_name[0]->tagData;
						$column_structure = $data->column_structure[0]->tagData;
						$sql = $zdbh->prepare("ALTER TABLE $database.$table_name ADD $column_name $column_structure");
						$sql->execute();
					}
		
		
					//Loop through inserts for selected table
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
				
				
				echo "END\n";
				
				
			
			
			
			
			
			
			
			
			
			}
		}
		catch (Exception $e){
		echo $e;
		}
	}
 
	
}

?>