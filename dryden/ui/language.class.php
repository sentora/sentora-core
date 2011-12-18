<?php

/**
 * @package zpanelx
 * @subpackage dryden -> ui
 * @author RusTus (rustus@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_language {

    /**
     * ui_language class is used to translate a text string into the language preference of the user.
	 * usage: ui_language::translate("message to be translated").
     */

    static function translate($message) {
		global $controller;
		global $zdbh;
		$message = addslashes($message);
		$currentuser = ctrl_users::GetUserDetail();
		$column_names = self::GetColumnNames('x_translations');
		foreach ($column_names as $column_name){
			$result = $zdbh->query("SELECT * FROM x_translations WHERE ".$column_name." LIKE '".$message."'")->Fetch();
        	if ($result) {
			$lang = $currentuser['language'];
				if (!fs_director::CheckForEmptyValue($result['tr_'.$lang.'_tx'])){
        			return $result['tr_'.$lang.'_tx'];
				} else {
					return stripslashes($message);
				}
        	}
		}	
		return stripslashes($message);
    }
	
	# return array of column names for a table
     static function GetColumnNames($table){
            global $zdbh;
        	$sql = "select column_name from information_schema.columns where lower(table_name)=lower('$table')";
	        $stmt = $zdbh->prepare($sql);
	        try {    
	            if($stmt->execute()){
	                $raw_column_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	                foreach($raw_column_data as $outer_key => $array){
	                    foreach($array as $inner_key => $value){
	                                if (!(int)$inner_key){
	                                    $column_names[] = $value;
	                                }
	                    }
	                }
	            }
	                return $column_names;
	         } catch (Exception $e){
	                
	         }        
	    } 
}

?>
