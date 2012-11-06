<?php

/**
 * Language translation class.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Russell Skinner (rskinner@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_language {

    /**
     * Used to translate a text string into the language preference of the user.
     * @author Russell Skinner (rskinner@zpanelcp.com)
     * @global db_driver $zdbh The ZPX database handle.
     * @param $message The string to translate.
     * @return string The transalated string.
     */
    static function translate($message) {
        global $zdbh;
        $message = addslashes($message);
        $currentuser = ctrl_users::GetUserDetail();
        $lang = $currentuser['language'];
        $column_names = self::GetColumnNames('x_translations');
        foreach ($column_names as $column_name) {
            $sql = $zdbh->prepare("SELECT * FROM x_translations WHERE :column_name LIKE :message");
            $sql->bindParam(':column_name', $column_name);
            $sql->bindParam(':message', $message);
            $sql->execute();
            $result = $sql->fetch();

            if ($result) {
                if (!fs_director::CheckForEmptyValue($result['tr_' . $lang . '_tx'])) {
                    return $result['tr_' . $lang . '_tx'];
                } else {
                    return stripslashes($message);
                }
            }
        }
        if (!fs_director::CheckForEmptyValue($message) && $lang == "en") {
            $sql = $zdbh->prepare("INSERT INTO x_translations (tr_en_tx) VALUES (:message)");
            $sql->bindParam(':message', $message);
            $sql->execute();
        }
        return stripslashes($message);
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