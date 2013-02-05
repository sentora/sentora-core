<?php

/**
 * Language translation class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.1
 * @author Russell Skinner (rskinner@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_language {

    static $LangCol;

    /**
     * Used to translate a text string into the language preference of the user.
     * @author Pascal Peyremorte (p.peyremorte@wanadoo.fr)
     * @global db_driver $zdbh The ZPX database handle.
     * @param $message The string to translate.
     * @return string The transalated string.
     */
    static function translate($message) {
        global $zdbh;

        if (empty(self::$LangCol)) {
            $uid = ctrl_auth::CurrentUserID();
            $sql = $zdbh->prepare('SELECT ud_language_vc FROM x_profiles WHERE ud_user_fk=' . $uid);
            $sql->execute();
            $lang = $sql->fetch();
            self::$LangCol = 'tr_' . $lang['ud_language_vc'] . '_tx';
        }
        if (self::$LangCol == 'tr_en_tx')
            return $message; //no translation required, english used

        $SlashedMessage = addslashes($message); //protect special chars
        $sql = $zdbh->prepare('SELECT ' . self::$LangCol . ' FROM x_translations WHERE tr_en_tx =:message');
        $sql->bindParam(':message', $SlashedMessage);
        $sql->execute();
        $result = $sql->fetch();

        if ($result) {
            if (!fs_director::CheckForEmptyValue($result[self::$LangCol]))
                return $result[self::$LangCol]; //valid translation present
            else
                return $message; //translated message empty
        } else { //message not found in the table
            //add unfound message to the table with empties translations
            $sql = $zdbh->prepare('INSERT INTO x_translations SET tr_en_tx=:message');
            $sql->bindParam(':message', $SlashedMessage);
            $sql->execute();
            return $message;
        }
    }

    /**
     * Return array of column names for a table.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @param string $table The table name to return that column names from.
     * @return array List of column names.
     */
    static function GetColumnNames($table) {
        global $zdbh;
        $sql = "select column_name from information_schema.columns where lower(table_name)=lower(:table)";
        $stmt = $zdbh->prepare($sql);
        $stmt->bindParam(':table', $table);
        try {
            if ($stmt->execute()) {
                $raw_column_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($raw_column_data as $outer_key => $array) {
                    foreach ($array as $inner_key => $value) {
                        if (!(int) $inner_key) {
                            $column_names[] = $value;
                        }
                    }
                }
            }
            return $column_names;
        } catch (Exception $e) {
            
        }
    }

}

?>