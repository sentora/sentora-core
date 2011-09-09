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
			
$tables = new xml_reader($mod_db_file);
$tables->Parse();

foreach($tables->document->table_structure as $table){
	$table_name = $table->table_name[0]->tagData;
	
	#Check if table exists, if not then create it.
	$sql="SELECT * FROM `".$database."`.`".$table_name."`";
	$result=@mysql_query($sql);
	if (!$result){
	mysql_query("CREATE TABLE `".$database."`.`".$table_name."`(create_temp INT)");
	}
	
	//Get a list of all fields that exist in the database for each table
	$fields = mysql_list_fields($database, $table_name);
	$num_fields = mysql_num_fields($fields);
	for ($i = 0; $i < $num_fields; $i++) {
   		$field_array[] = mysql_field_name($fields, $i);
	}
	
	
	//Loop through columnns for selected table
	foreach($table->column as $data){
		$column_name = $data->column_name[0]->tagData;
		$column_structure = $data->column_structure[0]->tagData;
		//Check if field exists for each table
		//Field does not exist...we add it
		if (!in_array($column_name, $field_array)) {
    		mysql_query("ALTER TABLE `".$database."`.`".$table_name."` ADD `".$column_name."` ".$column_structure."");
		//Field exists... we are good
		}else{
		//Do nothing
		}	
	}
	
	//Loop through inserts for selected table
	foreach($table->data as $data){
		$tagName = "";
		$insert = $data->insert[0]->tagData;
		$update = $data->update[0]->tagData;
		if (!empty($insert)){
		mysql_query("INSERT INTO `".$database."`.`".$table_name."` ".$insert."");
		}
		if (!empty($update)){
		mysql_query("UPDATE `".$database."`.`".$table_name."` SET ".$update."");
		}
	}
	
	//Reset the field array for the next table in the main loop
	unset($field_array);
	#Delete temp table if created.
	$sql="SELECT `create_temp` FROM `".$database."`.`".$table_name."`";
	$result=@mysql_query($sql);
	if ($result){
	mysql_query("ALTER TABLE `".$database."`.`".$table_name."` DROP `create_temp`");
	}
}			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			}
		}
		catch (Exception $e){
		// handle error
		}
	}
 
	
}

?>